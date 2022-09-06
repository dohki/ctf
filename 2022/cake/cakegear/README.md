# CakeGEAR

## Description

```
Can you crack the login portal of CakeGEAR router?
```

## Solution

There was magic mode called `godmode`, which makes user as admin without password. However, `username` gets changed if the request isn't sent from localhost.

```php
$req = @json_decode(file_get_contents("php://input"));
if (isset($req->username) && isset($req->password)) {
    if ($req->username === 'godmode'
        && !in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
        /* Debug mode is not allowed from outside the router */
        $req->username = 'nobody';
    }

    switch ($req->username) {
        case 'godmode':
            /* No password is required in god mode */
            $_SESSION['login'] = true;
            $_SESSION['admin'] = true;
            break;
    // (...)
```

It checks the `username` using `switch` statement.

```php
    switch ($req->username) {
        case 'godmode':
```

According to the `PHP` [manual](https://www.php.net/manual/en/control-structures.switch.php),

```
Note:

Note that switch/case does loose comparison.
```

Since the request is decoded as json, we can set `username` as other types than string.

```php
$req = @json_decode(file_get_contents("php://input"));
```

Again, according to the `PHP` [manual](https://www.php.net/manual/en/types.comparisons.php#types.comparisions-loose), `"godmode" == true` is `true`. Since we can now login as admin, we can read the flag from `admin.php`.

## Flag

`CakeCTF{y0u_mu5t_c4st_2_STRING_b3f0r3_us1ng_sw1tch_1n_PHP}`