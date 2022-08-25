# pppr

## Description
```
A simple x86 ROP exercise for tutorial graduates.

Server: nc pppr.sstf.site 1337

Download: pppr.zip
```

## Solution 
The binary has no stack canary and PIE.
```
    Arch:     i386-32-little
    RELRO:    Full RELRO
    Stack:    No canary found
    NX:       NX enabled
    PIE:      No PIE (0x8048000)
```
We have a function `r` which looks almost like `fgets`, and we have stack BOF. So, where to return?
```c
  char v4[4]; // [esp+0h] [ebp-8h] BYREF
  // ...
  r(v4, 64, 0);
```
We have magic `system`. So, where to put command?
```c
int __cdecl x(char *command)
{
  return system(command);
}
```
We have a global buffer. So, the goal is to call `r(buf_in_bss, ...)` with ROP and to prepare shell command.
```
char buf_in_bss[128];
```
Since the binary's architecture is `i386`, we need `pop foo; pop bar; pop baz; ret;` gadget to pop 3 arguments for `r` from the stack as the title suggests.
```
$ ROPgadget --binary pppr --only "pop|ret" --depth 4
Gadgets information
============================================================
(...)
0x080486a9 : pop esi ; pop edi ; pop ebp ; ret
(...)
```
Finally, we do `system("sh")` and read the flag.

## Flag
`SCTF{Anc13nt_x86_R0P_5kiLl}`