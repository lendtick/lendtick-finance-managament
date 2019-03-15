<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Helpers\Doku AS Doku;
use App\Repositories\Finance\DokuRepo AS DokuRepo;
use App\Models\User\RegisterMemberFlowManagement AS MemberFlow;
use App\Models\User\UserManagement AS User;
use App\Models\User\ProfileManagement AS Profile;
use App\Models\Master\RegisterMemberFlowMaster AS MasterFlow;
use App\Models\Master\WorkflowMaster AS MasterWorkflow;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\BlobStorage;
use App\Helpers\RestCurl;

class BillerController {

	public function index()
	{
		$send = array(
			"CHANNELCODE" => 6021,
			"REQUESTDATETIME" => 20190315212347,
			"LOGINNAME" => "agentkopastra",
			"PASSWORD" => "126fa8e713620d964731eb242833aa91",
			"WORDS" => "db5e98c8f25e15ceb1c4604c451707d4e8117ee2"
		);

		$res = RestCurl::exec('POST','http://103.10.129.109/DepositSystem-api/AgentLoginMIP?',$send);
		dd($res);



		$msg = "simpel";
		$key = "shared123";
		// $iv_size = openssl_cipher_iv_length('AES-128-CBC');
		$iv = "fedcba9876543210";
		// if (!$iv) {
		// 	$iv = openssl_random_pseudo_bytes($iv_size);
		// }
		$encryptedMessage = openssl_encrypt($msg, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
		// return base64_encode($iv . $encryptedMessage);
		return bin2hex($encryptedMessage);
		die();
		// echo date('YmdHis');
		// echo "<br>";
		// echo sha1('6021' . date('YmdHis') .'shared123' .'agentkopastra');
		// die();
		$plaintext = "simpel";
		$cipher = "aes-128-ecb";
		$key = "shared123";
		$iv = "fedcba9876543210";
		if (in_array($cipher, openssl_get_cipher_methods()))
		{
			// $ivlen = openssl_cipher_iv_length($cipher);
			// $iv = openssl_random_pseudo_bytes($ivlen);
			echo $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv, $tag);
    //store $cipher, $iv, and $tag for decryption later
			// $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv, $tag);
			// $original_plaintext."\n";
		}

		die();

		// $data = array(
		// 	'CHANNELCODE' 		=> '6021',
		// 	'REQUESTDATETIME' 	=> '20190221220600' ,
		// 	'LOGINNAME' 		=> 'agentkopastra' ,
		// 	'PASSWORD' 			=> 'simpel' ,
		// 	'WORDS'				=> '252138911f613c503457c5f1097b4dcb56ecc65b' 
		// );

		// $res = RestCurl::exec('POST','http://103.10.129.109/DepositSystem-api/AgentLoginMIP?',$data);
		// dd($this->encrypt('simpel','oke'));
		// $send_otp_action = RestCurl::exec('POST',env('URL_NOTIF').'send-otp',$send_otp);
	}

	function encrypt($str,$key) 
	{

		$str = $this->AESKeyVerifier($str, null);
		$key = $this->AESKeyVerifier($key,'0');
		$iv = "fedcba9876543210";

		$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

		mcrypt_generic_init($td, $key, $iv);
		$encrypted = mcrypt_generic($td, $str);

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return bin2hex($encrypted);
	}



	private function AESKeyVerifier($key, $pad) 
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
