# frozen cake 

## Description

```
oh your cake is frozen. please warm it up and get the first cake.
```

## Solution

It was a simple crypto challenge. We know the values listed below, and want to somehow extract the flag, `m`.

$$ n = p \times q $$
$$ a = m^p \bmod n $$
$$ b = m^q \bmod n $$
$$ c = m^n \bmod n $$

By `Euler's theorem`,

$$ m^{\varphi(n)} \equiv 1 \pmod n \tag{1} $$

where, 

$$ \varphi(n) = (p - 1) \times (q - 1) $$

Thus,

$$
m^{\varphi(n)} \\
= m^{(p - 1) \times (q - 1)} \\
= m^{n} \times (m^{p + q})^{-1} \times m \\
\equiv c \times (a \times b)^{-1} \times m \pmod n \tag{2} \\
$$

By `(1)` and `(2)`,

$$ m \equiv (c \times (a \times b)^{-1})^{-1} \pmod n $$

## Flag

`CakeCTF{oh_you_got_a_tepid_cake_sorry}`