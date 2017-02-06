<?php


namespace CopySense\RandomBytes;

use RuntimeException;


class RandomBytes {

	/**
		* Static class to generate cryptographically-strong random bytes from different available sources of entropy.
		*
		* Coded for PHP 5.4+
		* Not usable on Windows PHP < 5.3.0
		*
		* Example usage:
		*                require('randombytes.class.php');
		*                use CopySense\RandomBytes\RandomBytes;
		*                $aData = RandBytes::generate(32, 'openssl');
		*                var_dump($aData);
		*
		* Entropy sources:
		*                OpenSSL is regarded highly by some.
		*                /dev/urandom is 'suitable for most cryptographic purposes' (not available in Windows).
		*                random_bytes() was introduced in PHP 7.0
		*                MCrypt is deprecated from PHP 7.1
		*
		* References:
		*                http://timoh6.github.io/2013/11/05/Secure-random-numbers-for-PHP-developers.html
		*                http://php.net/manual/en/function.openssl-random-pseudo-bytes.php
		*                http://php.net/manual/en/function.mcrypt-create-iv.php
		*                http://php.net/manual/en/migration71.deprecated.php
		*
		*
		* @author        Martin Latter <copysense.co.uk>
		* @copyright     Martin Latter 13/04/2015
		* @version       0.11
		* @license       GNU GPL v3.0
		* @link          https://github.com/Tinram/Random-Bytes.git
		* @throws        RuntimeException
	*/


	private static $sLineBreak = '';


	public function __construct() {
		echo '<p style="#c00">Warning: this is a static class - do not invoke an instance of it.</p>';
	}


	/**
		* Initial checks, call generator method.
		*
		* @param    integer $iLength, length of string of bytes
		* @param    string $sByteGenMethod: 'openssl', 'urandom', 'random_bytes', 'mcrypt'
		*
		* @return   array, byte data and hashes of byte data
	*/

	public static function generate($iLength = 1, $sByteGenMethod = 'openssl') {

		# command-line or server line-break output
		self::$sLineBreak = (PHP_SAPI === 'cli') ? PHP_EOL : '<br>';

		try {

			if (version_compare(phpversion(), '5.3', '<')) {
				throw new RuntimeException(__CLASS__ . '{} cannot run properly on PHP versions less than version 5.3');
			}

			if ( ! is_int($iLength) || $iLength < 8) { # minimum of 8 bytes to be generated
				throw new RuntimeException(__METHOD__ . '() - $iLength argument must be 8 characters or more.');
			}
		}
		catch (RuntimeException $e) {
			self::reportException($e);
		}

		return self::generateRandomData($iLength, $sByteGenMethod);

	} # end generate()


	/**
		* Attempts to generate random byte data and process the data into different forms and hashes.
		*
		* Note that both mcrypt_create_iv() and openssl_random_pseudo_bytes() can generate multi-bytes (can check with mb_strlen()).
		*
		* @param    integer $iLength, length of string of bytes
		* @param    string $sGenMethod: 'openssl', 'urandom', 'random_bytes', 'mcrypt'
		*
		* @return   array, byte data and hashes of byte data
	*/

	private static function generateRandomData($iLength, $sGenMethod) {

		$sRaw = '';
		$bStrong = FALSE;

		if ($sGenMethod === 'openssl') {

			try {

				if ( ! function_exists('openssl_random_pseudo_bytes')) {
					throw new RuntimeException(__METHOD__ . '() - OpenSSL bytes generation not possible using this PHP installation.');
				}

				$sRaw = openssl_random_pseudo_bytes($iLength, $bStrong);

				if ( ! $bStrong) {
					throw new RuntimeException(__METHOD__ . '() - No \'secure\' generation of random bytes by OpenSSL.');
				}
			}
			catch (RuntimeException $e) {
				self::reportException($e);
			}
		}
		else if ($sGenMethod === 'urandom') {

			try {

				if (stripos(php_uname(), 'windows') === FALSE) {

					$sRaw = file_get_contents('/dev/urandom', FALSE, NULL, 0, $iLength);

					if ( ! $sRaw) {
						throw new RuntimeException(__METHOD__ . '() - creation of random bytes not possible using /dev/urandom.');
					}
				}
				else {
					throw new RuntimeException(__METHOD__ . '() - Windows does not have /dev/urandom available.');
				}
			}
			catch (RuntimeException $e) {
				self::reportException($e);
			}
		}
		else if ($sGenMethod === 'random_bytes') {

			try {

				if ( ! function_exists('random_bytes')) {
					throw new RuntimeException(__METHOD__ . '() - random_bytes() function not available in this PHP installation.');
				}
				else {
					$sRaw = random_bytes($iLength);
				}
			}
			catch (RuntimeException $e) {
				self::reportException($e);
			}
		}
		else if ($sGenMethod === 'mcrypt') {

			try {

				$sRaw = mcrypt_create_iv($iLength, MCRYPT_DEV_URANDOM);

				if ( ! $sRaw) {
					throw new RuntimeException(__METHOD__ . '() - could not create random bytes using mcrypt_create_iv().');
				}
			}
			catch (RuntimeException $e) {
				self::reportException($e);
			}
		}
		else {

			try {
				throw new RuntimeException(__METHOD__ . '() - unknown $sGenMethod argument passed.');
			}
			catch (RuntimeException $e) {
				self::reportException($e);
			}
		}


		## generated bytes processing

		# hex
		$sHex = strtoupper(bin2hex($sRaw));

		# SHA-256 hash
		$sSHA256 = hash('sha256', $sRaw);

		# SHA-256 hash to decimal bytes
		$bsHash = hash('sha256', $sRaw, TRUE);
		$aRaw = str_split($bsHash, 1);

		foreach ($aRaw as $sChar) {
			$aDecimals[] = ord($sChar);
		}

		$sHashDecimals = join(',', $aDecimals);

		# Whirlpool hash
		$sWhirlpool = hash('whirlpool', $sRaw);

		return [

			'raw' => $sRaw,
			'hex' => $sHex,
			'sha' => $sSHA256,
			'shabytes' => $sHashDecimals,
			'whirlpool' => $sWhirlpool
		];

	} # end generateRandomData()


	/**
		* Report exceptions generated in this class.
		*
		* @param    RuntimeException $e
	*/

	private static function reportException(RuntimeException $e) {

		echo $e->getMessage() . self::$sLineBreak . '(' . $e->getfile() . ', line ' . $e->getline() . ')' . self::$sLineBreak;

	} # end reportException()

} # end {}

?>