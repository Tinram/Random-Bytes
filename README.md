
# Random Bytes

####  Generate cryptographically-strong random bytes.

## Purpose

- Create random bytes - as cryptographically-strong as possible - from available sources of entropy, and display in alternative output formats.


## Sources

- Linux and Unix: MCrypt, OpenSSL, */dev/urandom*
- Windows: MCrypt, OpenSSL


## Example Usage

    require('randombytes.class.php');
    use CopySense\RandomBytes\RandomBytes;
    $aData = RandomBytes::generate(16, 'openssl');
    var_dump($aData);


## Other

The random bytes generated are only as good as the underlying entropy generator of the OS.

Linux's */dev/urandom* entropy source is a non-blocking generator 'suitable for most cryptographic purposes'.  (*/dev/random*, being blocking, isn't suitable for this script.)

OpenBSD and FreeBSD have non-blocking */dev/random* implementations.

On Windows, the implementation of MCrypt is known to have problems, which I confirmed as patterning present in images (instead of non-patterned noise) created from MCrypt-generated data (XAMPP, PHP 5.4).


### License

Random Bytes is released under the [GPL v.3](https://www.gnu.org/licenses/gpl-3.0.html).


#### Miscellaneous

![alt](http://www.phpclasses.org/award/innovation/nominee.gif "PHP Classes Innovation Award")

Nominated for a [PHP Classes Innovation Award ](http://www.phpclasses.org/award/innovation/) ([May 2015] (http://www.phpclasses.org/package/9146-PHP-Generate-cryptographically-strong-random-bytes.html)).
