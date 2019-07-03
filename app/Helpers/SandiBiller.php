<?php 

namespace App\Helpers;

class SandiBiller
{
	
	public static function get()
	{
		$channel_code = env('CHANNELCODE_BILLER');
		$request_datetime = date('Ymdhis');
		$shared_key = env('SHARED_KEY_BILLER');
		$login_name = env('LOGIN_NAME_BILLER');

		$str = env('STRING_BILLER');
		$cipher = 'AES-128-CBC';
		$key = $shared_key;
		$opts = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;
		$iv_len = 16;
		$iv = "fedcba9876543210";

		$str = self::AESKeyVerifier($str, null);
		$key = self::AESKeyVerifier($key,'0');

		$encrypted = openssl_encrypt($str, $cipher, $key, $opts, $iv);

		return bin2hex($encrypted);
	}

	private static function AESKeyVerifier($key, $pad) 
	{
		if($pad == null)
		{
			$pad = " ";
		}

		$keyLength = strlen($key);

		$factor = ceil($keyLength / 16);

		if ($factor == 0) {
			$factor = 1;
		}

		for ($i = $keyLength; $i < ($factor * 16); $i++) {
			$key .= $pad;
		}

		return $key;
	} 
}
