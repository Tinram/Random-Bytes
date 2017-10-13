
# Random Bytes

####  Generate cryptographically-strong random bytes.

## Purpose

Create random bytes - as cryptographically-strong as possible - from available sources of entropy, and display in different output formats.


## Sources

+ Linux and Unix: OpenSSL, */dev/urandom*, random\_bytes(), MCrypt
+ Windows: OpenSSL, random\_bytes(), MCrypt


## Example Usage

    require('randombytes.class.php');
    use CopySense\RandomBytes\RandomBytes;
    $aData = RandomBytes::generate(32, 'openssl');
    var_dump($aData);


## Other

The random bytes generated are only as good as the underlying entropy generator of the OS.

Linux's */dev/urandom* entropy source is a non-blocking generator 'suitable for most cryptographic purposes'.  (*/dev/random*, being blocking, isn't suitable for this script.)

OpenBSD and FreeBSD have non-blocking */dev/random* implementations.

random\_bytes() function was added to PHP version 7.0

On Windows, the implementation of MCrypt is known to have problems, which I confirmed as patterning present in images (instead of non-patterned noise) created from MCrypt-generated data (XAMPP, PHP 5.4).

The libmcrypt library behind MCrypt is unmaintained and contains unfixed bugs. MCrypt is now [deprecated in PHP 7.1](http://php.net/manual/en/migration71.deprecated.php).


## License

Random Bytes is released under the [GPL v.3](https://www.gnu.org/licenses/gpl-3.0.html).


## Miscellaneous

![alt](http://www.phpclasses.org/award/innovation/nominee.gif "PHP Classes Innovation Award")

Nominated for a [PHP Classes Innovation Award ](http://www.phpclasses.org/award/innovation/) ([May 2015](http://www.phpclasses.org/package/9146-PHP-Generate-cryptographically-strong-random-bytes.html)).
