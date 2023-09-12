# import logging
# logging.getLogger('angr').setLevel('DEBUG')

from pwn import *
import re
import sys
import angr
import claripy
import hashlib

import sys
sys.setrecursionlimit(10**7)

context.log_level = "error"
context.arch = "i386"
context.bits = 32

vfmaddsub132ps = b"\x2E\xC4\xE2\x71\x96\x84\x9A\x0C\x80\x0E\x08"
mov_cl_ah = b"\x8A\xA1\x18\x80\x0E\x08"
mov_cl_al = b"\x8A\x81\x18\x80\x0E\x08"
with open("masterpiece", "rb") as f:
    bin = f.read()
with open("masterpiece2", "wb") as f:
    bin = bin.replace(vfmaddsub132ps, b"\x90" * len(vfmaddsub132ps))
    f.write(bin)

proj = angr.Project('masterpiece2')

BASE = 0x08048000

BYTES = {}
BYTE = bin[0xa0018:0xa0118]
for i in range(len(BYTE)):
    BYTES[BYTE[i]] = i

results = []
result = bin[0xa0118:0xa0118 + 400]#0xa02a8]
for i in range(len(result) // 4):
    results.append(u32(result[4 * i:4 * (i + 1)]))

paths = []
jpt = bin[0x1062:0x11f6]
for i in range(len(jpt) // 4):
    path = u32(jpt[4 * i:4 * (i + 1)]) - 0x08048000
    paths.append(path)

ckpts = [[] for _ in range(len(paths))]
for i in range(len(paths) - 1):
    _range = bin[paths[i]:paths[i+1]]
    # for pos in re.finditer(b"\x90" * len(vfmaddsub132ps), _range):
    #     assert _range[pos.start():pos.end()] == b"\x90" * len(vfmaddsub132ps)
    #     ckpts[i].append(paths[i] + pos.start())
    for pos in re.finditer(mov_cl_ah, _range):
        assert _range[pos.start():pos.end()] == mov_cl_ah
        ckpts[i].append(paths[i] + pos.start() + 6)
    for pos in re.finditer(mov_cl_al, _range):
        assert _range[pos.start():pos.end()] == mov_cl_al
        ckpts[i].append(paths[i] + pos.start() + 6)
    ckpts[i].sort()

with open("ans", "rb") as f:
    ans = f.read()

for i in range(len(ans) // 4, 100):
    # syms = claripy.Concat(*[claripy.BVS(f"x{i}", 8) for i in range(4)])
    # state = proj.factory.entry_state(stdin=syms, add_options={angr.options.LAZY_SOLVES})

    eax_next = results[i]
    find = BASE + paths[i+1] - 5
    for (j, ckpt) in enumerate(ckpts[i][::-1] + [paths[i]]):
        eip = BASE + ckpt
        print(i, j, hex(eip), "(???) -->", hex(find), hex(eax_next))

        eax = claripy.BVS("eax", 32)

        state = proj.factory.blank_state()
        state.regs.eip = eip
        state.regs.ecx = 0
        state.regs.eax = eax

        simul = proj.factory.simulation_manager(state)
        simul.explore(find=find)
        assert simul.found

        state = simul.found[0]

        state.solver.add(state.regs.eax == eax_next)
        eax_next = state.solver.eval(eax)
        print(hex(eax_next))

        if bin[eip - 8 - BASE:eip - 8 - BASE + 2] == b"\x88\xc1":
            al = BYTES[eax_next & 0xff]
            eax_next &= 0xffffff00
            eax_next |= al
        elif bin[eip - 8 - BASE:eip - 8 - BASE + 2] == b"\x88\xe1":
            ah = BYTES[(eax_next >> 8) & 0xff]
            eax_next &= 0xffff00ff
            eax_next |= (ah << 8)
        else:
            assert(j == len(ckpts[i]))
            ans += p32(eax_next)
            with open("ans", "wb") as f:
                f.write(ans)
            break

        print(hex(eax_next))

        find = eip - 8

    #input()

print("WACON2023{" + hashlib.sha256(ans).hexdigest() + "}")