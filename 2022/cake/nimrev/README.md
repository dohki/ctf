# nimrev

## Description

```
Have you ever analysed programs written in languages other than C/C++?
```

## Solution

It was the program written in [nim](https://nim-lang.org/) language, and it just prints whether input is the right flag or not. After little reversing with `IDA`, I found `NimMainModule`, which seems like `main` in `C`, and there was one initialized bytes sequence at the beginning.

```c
  v3 = readLine_systemZio_271(stdin);
  v4 = newSeq(&NTIseqLcharT__lBgZ7a89beZGYPl8PiANMTA_, 24LL);
  *(_BYTE *)(v4 + 16) = '\xBC';
  *(_BYTE *)(v4 + 17) = -98;
  *(_BYTE *)(v4 + 18) = -108;
  // (...)
```

After that, I see some `map`-like function opearting on the previous sequence.

```c
  v7 = (__int64 (__fastcall *)())colonanonymous__main_7;
  // (...)
  v5 = (__int64 *)map_main_11(v4 + 16, v0, (__int64 (__fastcall *)(_QWORD))v7, v8);
```

Mapping was just bitwise NOT.

```c
__int64 __fastcall colonanonymous__main_7(char a1)
{
  return (unsigned __int8)~a1;
}
```

After that, mapped array got joined and was compared with the input. Thus, we just extract the int array, flip and join to get the flag.

```c
  // (...)
  v6 = join_main_42((__int64)(v5 + 2), v1, 0LL);
  if ( (unsigned __int8)eqStrings(v3, v6) != 1 )
    v9 = copyString(&TM__V45tF8B8NBcxFcjfe7lhBw_5);
  else
    v9 = copyString(&TM__V45tF8B8NBcxFcjfe7lhBw_4);
  echoBinSafe(&v9, 1LL);
```

## Flag

`CakeCTF{s0m3t1m3s_n0t_C}`