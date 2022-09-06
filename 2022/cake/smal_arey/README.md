# smal arey

## Description

```
Small array only!
```

## Solution

It was a pwn challenge, and we may overwrite `GOT` again since it has no `PIE` and `RELRO` is partial. It's weird that it doesn't have stack canary, but I haven't utilized it anyway. (Or, I may have solved with unintended solution.)

```
    Arch:     amd64-64-little
    RELRO:    Partial RELRO
    Stack:    No canary found
    NX:       NX enabled
    PIE:      No PIE (0x400000)
```

At first sight, it may seem there is no vulnerability at all. The problem is in follwing code snippets. Since `C` macro works like substitution, `ARRAY_SIZE(n + 1)` is `(n + 1 * sizeof(long))`, which is far from the intention obviously. Author forgot to parenthesize the argument `n`.

```c
#define ARRAY_SIZE(n) (n * sizeof(long))
#define ARRAY_NEW(n) (long*)alloca(ARRAY_SIZE(n + 1))
```

I was about to be little tired and didn't want to calculate the exact offsets. Fortunately, the allocation `size` was highly restricted (i.e. [0, 5]), and I just used the max value since `alloca` allocates on the stack and I wanted to overwrite those stack variables.

```c
int main() {
  long size, index, *arr;

  printf("size: ");
  if (scanf("%ld", &size) != 1 || size < 0 || size > 5)
    exit(0);

  arr = ARRAY_NEW(size);
```

Since `index` should be smaller than `size`, the max value for `index` is `4`, and that is where `size` locates on the stack. Thus, we can access more than we are supposed to do by making `size` bigger.

```c
  while (1) {
    printf("index: ");
    if (scanf("%ld", &index) != 1 || index < 0 || index >= size)
      exit(0);

    printf("value: ");
    scanf("%ld", &arr[index]);
  }
```

Now, the problem is that we have no leak primitives. Fortunately, the binary has no `PIE`, and we can make `arr` point to `GOT`. Moreover, the binary knows where `libc` functions locate not like us. Since all `libc` functions' use cases were too simple, I needed to use one gadget in `libc`. So, I looked into all `GOT`s and search for the function which is near from one gadget. Candidates for one gadget were located at `0xe3000` range, while `alarm` function is located at `0xe2d90`.

```c
__attribute__((constructor))
void setup(void) {
  alarm(180);
  setbuf(stdin, NULL);
  setbuf(stdout, NULL);
}
```

Thus, if I make `arr` unaligned and overwrite only 2 least significant bytes of `alarm@got` with one gadget's offset, I can get a shell by bruteforcing 0.5 byte.

However, it is not that simple as it seems since we have to consider other `GOT`s below `alarm`'s, which are `setbuf` and `printf`.  It is ok to corrupt `setbuf` since `setup` will never be called again in normal flow.

However, `printf` was called too frequently. I had to find the `ret` gadget from the binary, which won't crash after temporarily corrupting `printf@got`. It will be recovered right at next step by making it completely point to the `ret` gadget.

Unfortunately, `libc` had `PIE`. So, I needed to bruteforce this too, but it just needed not to crash for once.

```
    Arch:     amd64-64-little
    RELRO:    Partial RELRO
    Stack:    Canary found
    NX:       NX enabled
    PIE:      PIE enabled
```

To make it more reliable, I tested the offsets on `ubuntu 20.04` via `docker`. After little tries, I was able to successfully get a shell.

## Flag

`CakeCTF{PRE01-C. Use parentheses within macros around parameter names}`
