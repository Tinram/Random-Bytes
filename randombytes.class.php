<?php


namespace CopySense\RandomBytes;


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
	*                $aData = RandBytes::generate(16, 'openssl');
	*                var_dump($aData);
	*
	* Linux / Unix: choose your preferred source - /dev/urandom is 'suitable for most cryptographic purposes'.  mcrypt_create_iv() is held by some in higher regard than OpenSSL. 
	* Windows: mcrypt_create_iv() can create visible patterns in images on Windows.  No /dev/urandom available.  CryptGenRandom accessibility?  OpenSSL is probably the best option.
	*
	* References:
	*                http://timoh6.github.io/2013/11/05/Secure-random-numbers-for-PHP-developers.html
	*                http://php.net/manual/en/function.openssl-random-pseudo-bytes.php
	*                http://php.net/manual/en/function.mcrypt-create-iv.php
	*
	*
	* @author        Martin Latter <copysense.co.uk>
	* @copyright     Martin Latter 13/04/2015
	* @version       0.10
	* @license       GNU GPL v3.0
	* @link          https://github.com/Tinram/Random-Bytes.git
	* @throws        RuntimeException
	*/


	const LINE_SEPARATOR = '<br>'; # <br> for server-based, "\n" for cmd-line usage


	public function __construct() {
		echo '<p style="#c00">Warning: this is a static class - do not invoke an instance of it.</p>';
	}


	/**
	* Initial checks, call generator method.
	*
	* @param    integer $iLength, length of string of bytes
	* @param    string $sByteGenMethod: 'mcrypt', 'openssl', 'urandom'
	*
	* @return   array, byte data and hashes of byte data
	*/

	public static function generate($iLength = 1, $sByteGenMethod = 'mcrypt') {

		try {

			if (version_compare(phpversion(), '5.3', '<')) {
				throw new \RuntimeException(__CLASS__ . '{} cannot run properly on PHP versions less than version 5.3');
			}

			if ( ! is_int($iLength) || $iLength < 8) { # minimum of 8 bytes to be generated
				throw new \RuntimeException(__METHOD__ . '() - $iLength argument must be 8 characters or more.');
			}
		}
		catch (\Exception $e) {
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
	* @param    string $sGenMethod: 'mcrypt', 'openssl', 'urandom'
	*
	* @return   array, byte data and hashes of byte data
	*/

	private static function generateRandomData($iLength, $sGenMethod) {

		$sRaw = '';
		$bStrong = FALSE;

		if ($sGenMethod === 'mcrypt') {

			try {

				$sRaw = mcrypt_create_iv($iLength, MCRYPT_DEV_URANDOM);

				if ( ! $sRaw) {
					throw new \RuntimeException(__METHOD__ . '() - could not create random bytes using mcrypt_create_iv().');
				}
			}
			catch (\Exception $e) {
				self::reportException($e);
			}
		}
		else if ($sGenMethod === 'openssl') {

			try {

				if ( ! function_exists('openssl_random_pseudo_bytes')) {
					throw new \RuntimeException(__METHOD__ . '() - OpenSSL bytes generation not possible using this PHP installation.');
				}

				$sRaw = openssl_random_pseudo_bytes($iLength, $bStrong);

				if ( ! $bStrong) {
					throw new \RuntimeException(__METHOD__ . '() - No \'secure\' generation of random bytes by OpenSSL.');
				}
			}
			catch (\Exception $e) {
				self::reportException($e);
			}
		}
		else if ($sGenMethod === 'urandom') {

			try {

				if (stripos(php_uname(), 'windows') === FALSE) {

					$sRaw = file_get_contents('/dev/urandom', FALSE, NULL, 0, $iLength);

					if ( ! $sRaw) {
						throw new \RuntimeException(__METHOD__ . '() - no creation of random bytes possible using /dev/urandom.');
					}
				}
				else {
					throw new \RuntimeException(__METHOD__ . '() - Windows does not have /dev/urandom available.');
				}
			}
			catch (\Exception $e) {
				self::reportException($e);
			}
		}
		else {

			try {

				throw new \RuntimeException(__METHOD__ . '() - unknown $sGenMethod argument passed.');
			}
			catch (\Exception $e) {
				self::reportException($e);
			}
		}


		## bytes processing

		# hex
		$sHex = strtoupper(bin2hex($sRaw));

		# SHA-256 hash
		$sSHA256 = hash('sha256', $sRaw);

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
	* Report Exceptions generated in this class.
	*
	* @param    Exception $e
	*/

	private static function reportException(\Exception $e) {

		echo $e->getMessage() . self::LINE_SEPARATOR . '(' . $e->getfile() . ', line ' . $e->getline() . ')' . self::LINE_SEPARATOR;

	} # end reportException()

} # end {}

?>