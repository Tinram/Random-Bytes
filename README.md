
# Random Bytes

####  Generate cryptographically-strong random bytes.

## Purpose
- Create random bytes - as cryptographically-strong as possible - from available sources of entropy, and display in alternative output formats.

## Sources
- Linux / Unix: MCrypt, OpenSSL, */dev/urandom*
- Windows: MCrypt, OpenSSL

## Other
The random bytes are only as good as the underlying entropy generator of the OS.

Linux's */dev/urandom* entropy source is a non-blocking generator 'suitable for most cryptographic purposes'.  (*/dev/random*, being blocking, isn't suitable for this script.)

OpenBSD and FreeBSD have non-blocking */dev/random* implementations.

On Windows, the MCrypt implementation is known to have problems, which I confirmed as patterning in images (instead of non-patterned noise) I generated from MCrypt-generated data on PHP 5.4 / XAMPP.

### License

Random Bytes is released under the [GPL v.3](https://www.gnu.org/licenses/gpl-3.0.html).
