# str.vs.cstr 

## Description

```
Which do you like, C string or C++ string?
```

## Solution

It was a `C++` pwn challenge, and the binary has no `PIE` and is `Partial RELRO`. It seems we would overwrite GOT soon.

```
    Arch:     amd64-64-little
    RELRO:    Partial RELRO
    Stack:    Canary found
    NX:       NX enabled
    PIE:      No PIE (0x400000)
```

The source file was given, and we could easily recognize buffer overflow in `Test._c_str`, and corrupt `_str`, which is `std::string`.

```c
struct Test {
  // (...)
  char* c_str() { return _c_str; }
  // (...)

  char _c_str[0x20];
  std::string _str;
};

int main() {
  Test test;
  // (...)
    std::cout << "choice: ";
    std::cin >> choice;

    switch (choice) {
      case 1: // set c_str
        std::cout << "c_str: ";
        std::cin >> test.c_str();
        break;
```

According the [author's blog](https://ptr-yudai.hatenablog.com/entry/2021/11/30/235732#stdstring), `std::string` has a pointer to the data buffer at offset 0. Since we can set and get data of `Test._str`, we can get arbitrary address read and write primitives by overwriting `Test._str`'s first poniter.

```c
struct Test {
  // (...)
  std::string& str() { return _str; }
  // (...)
  std::string _str;
};
// (...)
      case 3: // set str
        std::cout << "str: ";
        std::cin >> test.str();
        break;

      case 4: // get str
        std::cout << "str: " << test.str() << std::endl;
        break;
```

Apparently, we have a magic function. So, I tried to overwrite GOT with it, but somehow it didn't go well. `std::system@plt` just returned without resolving its address.

```c
struct Test {
  // (...)
private:
  __attribute__((used))
  void call_me() {
    std::system("/bin/sh");
  }
  // (...)
};
```

Since we already have arbitrary read and write primitives, we just need to leak libc address from GOT, find libc version, and use one gadget to get shell.

## Flag

`CakeCTF{HW1: Remove "call_me" and solve it / HW2: Set PIE+RELRO and solve it}`