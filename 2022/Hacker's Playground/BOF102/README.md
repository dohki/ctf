# BOF102

## Description
```
This is an advanced course in Buffer Overflow.

The binary is running at: nc bof102.sstf.site 1337.

Get the shell of the server!

Download: BOF102.zip

This is a tutorial challenge from Hacker's Playground 2021.
If you are not sure how to solve this,
please refer to the tutorial guide(Eng | Kor).
```

## Solution
The binary has no stack canary and PIE.
```
    Arch:     i386-32-little
    RELRO:    Partial RELRO
    Stack:    No canary found
    NX:       NX enabled
    PIE:      No PIE (0x8048000)
```
We have stack BOF. So, where to return?
```c
	char payload[16];
    // ...
	puts("Do you wanna build a snowman?");
	printf(" > ");
	scanf("%s", payload);
```
It turns out that we have `system@plt`. So, where to put command?
```c
	system("echo 'Welcome to BOF 102!'");
```
We have a global buffer `name`, which is fully controllable.
```c
char name[16];
// ...
	puts("What's your name?");
	printf("Name > ");
	scanf("%16s", name);
```
So, we just do `system("sh")` and read the flag.

## Flag 
`SCTF{5t4ck_c4n4ry_4nd_ASLR_4nd_PIE_4re_l3ft_a5_h0m3wOrk}`