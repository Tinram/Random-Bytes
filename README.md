
# Random Bytes

#### Generate cryptographically-strong random bytes.


## Purpose

Create random bytes &ndash; as cryptographically-strong as possible &ndash; from available sources of entropy, and display in different output formats.


## Crypto Sources

+ Linux/Unix: OpenSSL, `random_bytes()`, */dev/urandom*
+ Windows: OpenSSL, `random_bytes()`

**Random Bytes Definitions**:

+ openssl
+ random_bytes
+ urandom


## Usage

### Prototype

```php
    array RandomBytes::generate(int $length, string $source);
```

### Example

```php
    require('randombytes.class.php');
    use Tinram\RandomBytes\RandomBytes;
    $aData = RandomBytes::generate(32, 'openssl');
    var_dump($aData);
```


## Details

The random bytes generated are only as good as the underlying entropy generator of the OS.

Linux's */dev/urandom* entropy source is a non-blocking generator 'suitable for most cryptographic purposes'.  (*/dev/random*, being blocking, isn't suitable for this script.)

OpenBSD and FreeBSD have non-blocking */dev/random* implementations.

The `random_bytes()` function was added to PHP version 7.0


## License

Random Bytes is released under the [GPL v.3](https://www.gnu.org/licenses/gpl-3.0.html).
