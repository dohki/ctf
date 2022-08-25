# BOF103

## Description
```
ROP is an attack technique which makes BOF vulnerabilities so critical.

Find the binary running at: nc bof103.sstf.site 1337.

Download: BOF103.zip

This is a tutorial challenge.
If you are not sure how to solve this,
please refer to the tutorial guide(Eng | Kor).
```

## Solution
The binary has no stack canary and PIE.
```
    Arch:     amd64-64-little
    RELRO:    Partial RELRO
    Stack:    No canary found
    NX:       NX enabled
    PIE:      No PIE (0x400000)
```
We have stack BOF.
```c
	char name[16];
    // ...
	printf("Name > ");
	fflush(stdout);
	scanf("%s", name);
```
We also have `system@plt`. So, where to put command?
```c
	system("echo 'Welcome to BOF 103!'");
```
We have 8-bytes long global variable.
```c
unsigned long long key;
```
Also, we have a magic function gadget for it.
```c
void useme(unsigned long long a, unsigned long long b)
{
	key = a * b;
}
```
To do ROP, we need to find gadgets for settings parameters of `useme()` (e.g. `a` and `b`), which is `pop rdi; ret;` and `pop rsi; ret;` for each.
```sh
$ ROPgadget --binary bof103 --only "pop|ret" | grep "rdi\|rsi"
0x00000000004007b3 : pop rdi ; ret
0x00000000004007b1 : pop rsi ; pop r15 ; ret
0x0000000000400747 : pop rsi ; ret
```
The last step is only to calculate appropriate `a` and `b` which makes `key` as `cat<flag`.

## Flag
`SCTF{S0_w3_c4ll_it_ROP_cha1n}`