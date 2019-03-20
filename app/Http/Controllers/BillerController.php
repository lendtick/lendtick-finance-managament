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
		$channel_code = 6021;
		$request_datetime = date('Ymdhis');
		$shared_key = 'shared123';
		$login_name = 'agentkopastra';

		$str = 'simpel';
		$cipher = 'AES-128-CBC';
		$key = $shared_key;
		$opts = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;
		$iv_len = 16;
		$iv = "fedcba9876543210";

		$str = $this->AESKeyVerifier($str, null);
		$key = $this->AESKeyVerifier($key,'0');

		$encrypted = openssl_encrypt($str, $cipher, $key, $opts, $iv);

		// echo env('LINK_DOKU_BILLER').'/DepositSystem-api/AgentLoginMIP?';

		// echo bin2hex($encrypted);
		// die();
		
		$send = array(
			"CHANNELCODE" 		=> $channel_code,
			"REQUESTDATETIME" 	=> $request_datetime,
			"LOGINNAME" 		=> $login_name,
			"PASSWORD" 			=> bin2hex($encrypted),
			"WORDS"				=> sha1($channel_code . $request_datetime . $shared_key . $login_name)
		);

		// print_r($send); die();

		$res = RestCurl::exec('POST',env('LINK_DOKU_BILLER').'/DepositSystem-api/AgentLoginMIP?',$send);
		dd($res);
	}

	function AESKeyVerifier($key, $pad) 
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
