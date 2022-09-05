# Cairo Reverse

## Description

```
Simple cairo reverse

starknet-compile 0.9.1
```

## Solution

`cairo`? `starknet`? I did a quick search.

```
StarkNet is a permissionless decentralized ZK-Rollup operating as an L2 network over Ethereum (...)

Cairo is a programming language for writing provable programs (...)

StarkNet uses the Cairo programming language both for its infrastructure and for writing StarkNet contracts.
```

I was given `contract.cairo`, which is a source, and its compiled output `contract_compiled.json`. The source was very short so that I can even show the whole code here.

```cairo
# Declare this file as a StarkNet contract.
%lang starknet

from starkware.cairo.common.cairo_builtins import HashBuiltin

@view
func get_flag{
    syscall_ptr : felt*,
    pedersen_ptr : HashBuiltin*,
    range_check_ptr,
}(t:felt) -> (res : felt):
    if t == /* CENSORED */:
        return (res=0x42414c534e7b6f032fa620b5c520ff47733c3723ebc79890c26af4 + t*t)
    else:
        return(res=0)
    end
end
```

I could recognize some ASCII string, `BALSN{` from the return value, but it seems like we need to know `t` to restore it.

```cairo
    if t == /* CENSORED */:
        return (res=0x42414c534e7b6f032fa620b5c520ff47733c3723ebc79890c26af4 + t*t)
```

Then, I recognized its weird type `felt`, and quick search [said](https://www.cairo-lang.org/docs/hello_cairo/intro.html#the-primitive-type-field-element-felt) that it is an integer whose range is defined by some prime number.

```cairo
}(t:felt) -> (res : felt):
```

At first, I was trying to extract `t` somehow from debugging, but after fiddling with cairo environments, I gave up since it seems it's in little early stage. Rather, I started to dig the `contract_compiled.json` and found the program opcodes. After seeing that, I `felt` like (pun-intended) `t` must be one of those long numbers.

```json
    "program": {
        "attributes": [],
        "builtins": [
            "pedersen",
            "range_check"
        ],
        "data": [
            "0x482680017ffd8000",
            "0x800000000000010fffffffffffffffffffffffffffe2919e3d696087d12173e",
            "0x20680017fff7fff",
            "0x9",
            "0x484a7ffd7ffd8000",
            "0x480a7ffa7fff8000",
            "0x480a7ffb7fff8000",
            "0x480a7ffc7fff8000",
            "0x482480017ffc8000",
            "0x42414c534e7b6f032fa620b5c520ff47733c3723ebc79890c26af4",
            (...)
```

Another thing I found from `contract_compiled.json` is the prime number, which defines the range of the `felt` type. The only remaining step is just to calculate and restore the flag.

```json
        "prime": "0x800000000000011000000000000000000000000000000000000000000000001",
```

## Flag

`BALSN{read_data_from_cairo}`