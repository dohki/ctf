# welkerme

## Description

```
Introduction to Linux Kernel Exploit :)
```

## Solution

It was easy Linux kernel LPE challenge. The kernel loads the driver with following vulnerable ioctl, which offers arbitrary call primitive.

```c
static long module_ioctl(struct file *filp,
                         unsigned int cmd,
                         unsigned long arg) {
  long (*code)(void);
  // (...)
  switch (cmd) {
    // (...)
    case CMD_EXEC:
      printk("CMD_EXEC: arg=0x%016lx\n", arg);
      code = (long (*)(void))(arg);
      return code();
    // (...)
  }
}
```

The kernel didn't have `SMEP` or `PXN` like things, so nothing prevents executing user code from kerenl code. So, we can get root shell by just preparing one gadget function (i.e. `commit_creds(prepare_kernel_cred(NULL))`) in user code, and calling it from kernel via `CMD_EXEC` ioctl.

So, how do we know where those kerenl funtions are? Fortunately, the kernel had no `KASLR`.

```
     -append "console=ttyS0 loglevel=3 oops=panic panic=-1 nopti nokaslr" \
```

Since `bzImage` was given, I used [kallsyms_finder](https://github.com/marin-m/vmlinux-to-elf/blob/master/vmlinux_to_elf/kallsyms_finder.py), which extracts available kernel symbols and its offsets.

## Flag

`CakeCTF{b4s1cs_0f_pr1v1l3g3_3sc4l4t10n!!}`