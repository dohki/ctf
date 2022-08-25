# BOF101

## Description
```
You might have heard about BOF.
It's the most common vulnerability in executable binaries.

Here is a vulnerable binary(Download).
The binary is running at: nc bof101.sstf.site 1337.

Can you smash it?
Just execute printflag() function and get the flag!

This is a tutorial challenge from Hacker's Playground 2020.
If you are not sure how to solve this,
please refer to the tutorial guide(Eng | Kor).
```

## Solution
It seems the binary has no stack canary.
```
$ checksec bof101
    Arch:     amd64-64-little
    RELRO:    Full RELRO
    Stack:    No canary found
    NX:       NX enabled
    PIE:      PIE enabled
```
However, it has custom stack canary, and we can easily bypass it since it is constant.
```c
	int check=0xdeadbeef;
    // ...
	if (check != 0xdeadbeef){
		printf("[Warning!] BOF detected!\n");
		exit(0);
	}
```
It has stack BOF. So, where to return?
```c
	printf("What is your name?\n: ");
	scanf("%s", name);	
```
We already have a magic function, so we just return to it.
```c
int printflag(){ 
	char buf[32];
	FILE* fp = fopen("/flag", "r"); 
	fread(buf, 1, 32, fp);
	fclose(fp);
	printf("%s", buf);
	return 0;
}
```

## Flag
`SCTF{n0w_U_R_B0F_3xpEr7}`