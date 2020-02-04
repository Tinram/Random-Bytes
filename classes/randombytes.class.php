<?php

declare(strict_types=1);

namespace Tinram\RandomBytes;

use RuntimeException;


final class RandomBytes
{
    /**
        * Static class to generate cryptographically-strong random bytes from different available sources of entropy.
        *
        * Coded to PHP 7.2
        *
        * Description:
        *                 array RandomBytes::generate(int $length, string $source);
        *
        * Example usage:
        *                 require('classes/randombytes.class.php');
        *                 use Tinram\RandomBytes\RandomBytes;
        *                 $aData = RandomBytes::generate(32, 'openssl');
        *                 var_dump($aData);
        *
        * Entropy sources:
        *                 OpenSSL is regarded highly by some.
        *                 /dev/urandom is 'suitable for most cryptographic purposes' (not available in Windows).
        *                 random_bytes() was introduced in PHP 7.0
        *
        * References:
        *                 http://timoh6.github.io/2013/11/05/Secure-random-numbers-for-PHP-developers.html
        *                 https://php.net/manual/en/function.openssl-random-pseudo-bytes.php
        *
        * @author         Martin Latter
        * @copyright      Martin Latter 13/04/2015
        * @version        0.15
        * @license        GNU GPL v3.0
        * @link           https://github.com/Tinram/Random-Bytes.git
        * @throws         RuntimeException
    */


    /** @var string $sLineBreak, line break holder */
    private static $sLineBreak = '';


    public function __construct()
    {
        echo '<p style="#c00">Warning: this is a static class - do not invoke an instance of it.</p>';
    }


    /**
        * Initial checks, call generator method.
        *
        * @param    integer $iLength, number of bytes
        * @param    string $sByteGenMethod: 'openssl', 'urandom', 'random_bytes'
        *
        * @return   array<mixed>, byte data and hashes of byte data
    */

    public static function generate($iLength = 1, $sByteGenMethod = 'openssl')
    {
        # command-line or server line-break output
        self::$sLineBreak = (PHP_SAPI === 'cli') ? PHP_EOL : '<br>';

        $iLength = (int) $iLength; # in case of string number being passed

        try
        {
            if (version_compare(phpversion(), '7.2', '<'))
            {
                throw new RuntimeException(__CLASS__ . '{} coded to PHP 7.2; with slight modifications can work on PHP 5.3+');
            }

            if ($iLength < 8) # minimum of 8 bytes to be generated
            {
                throw new RuntimeException(__METHOD__ . '() - $iLength argument must be 8 characters or more.');
            }
        }
        catch (RuntimeException $e)
        {
            die(self::reportException($e));
        }

        return self::generateRandomData($iLength, $sByteGenMethod);
    }


    /**
        * Attempt to generate random byte data and process the data into different forms and hashes.
        *
        * Note that openssl_random_pseudo_bytes() can generate multi-bytes (can check with mb_strlen()).
        *
        * @param    integer $iLength, number of bytes
        * @param    string $sSource, cryptographic source: 'openssl', 'urandom', 'random_bytes'
        *
        * @return   array<string,string|false>, byte data and hashes of byte data
    */

    private static function generateRandomData($iLength, $sSource)
    {
        $sRaw = '';
        $bStrong = false;
        $aDecimals = [];

        if ($sSource === 'openssl')
        {
            try
            {
                if ( ! function_exists('openssl_random_pseudo_bytes'))
                {
                    throw new RuntimeException(__METHOD__ . '() - OpenSSL bytes generation not possible using this PHP installation.');
                }

                $sRaw = openssl_random_pseudo_bytes($iLength, $bStrong);

                if ( ! $bStrong)
                {
                    throw new RuntimeException(__METHOD__ . '() - No \'secure\' generation of random bytes by OpenSSL.');
                }
            }
            catch (RuntimeException $e)
            {
                die(self::reportException($e));
            }
        }
        else if ($sSource === 'urandom')
        {
            try
            {
                if (stripos(php_uname(), 'windows') === false)
                {
                    $sRaw = file_get_contents('/dev/urandom', false, null, 0, $iLength);

                    if ($sRaw === false)
                    {
                        throw new RuntimeException(__METHOD__ . '() - creation of random bytes not possible using /dev/urandom.');
                    }
                }
                else
                {
                    throw new RuntimeException(__METHOD__ . '() - Windows does not have /dev/urandom available.');
                }
            }
            catch (RuntimeException $e)
            {
                die(self::reportException($e));
            }
        }
        else if ($sSource === 'random_bytes')
        {
            try
            {
                if ( ! function_exists('random_bytes'))
                {
                    throw new RuntimeException(__METHOD__ . '() - random_bytes() function not available in this PHP installation.');
                }
                else
                {
                    $sRaw = random_bytes($iLength);
                }
            }
            catch (RuntimeException $e)
            {
                die(self::reportException($e));
            }
        }
        else
        {
            try
            {
                throw new RuntimeException(__METHOD__ . '() - unknown $sSource function argument passed.');
            }
            catch (RuntimeException $e)
            {
                die(self::reportException($e));
            }
        }


        ## generated bytes processing

        # hex
        $sHex = strtoupper(bin2hex($sRaw));

        # SHA-256 hash
        $sSHA256 = hash('sha256', $sRaw);

        # SHA-256 hash to decimal bytes
        $bsHash = hash('sha256', $sRaw, true);
        $aRaw = str_split($bsHash, 1);

        foreach ($aRaw as $sChar)
        {
            $aDecimals[] = ord($sChar);
        }

        $sHashDecimals = join(',', $aDecimals);

        # Whirlpool hash
        $sWhirlpool = hash('whirlpool', $sRaw);

        return
        [
            'raw' => $sRaw,
            'hex' => $sHex,
            'sha' => $sSHA256,
            'shabytes' => $sHashDecimals,
            'whirlpool' => $sWhirlpool
        ];
    }


    /**
        * Report exceptions generated in this class.
        *
        * @param    RuntimeException $e
    */

    private static function reportException(RuntimeException $e): string
    {
        return $e->getMessage() . self::$sLineBreak . '(' . $e->getFile() . ', line ' . $e->getLine() . ')' . self::$sLineBreak;
    }
}
