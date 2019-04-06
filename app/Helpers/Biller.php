<?php 

namespace App\Helpers;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\BlobStorage;
use App\Helpers\RestCurl;
use App\Helpers\SandiBiller AS Sandi;
use App\Models\Finance\BillerSession AS BillerSession;

class Biller
{
	
	public static function SessionID($response_code = '' , $response_message = '')
	{ 
		if ($response_code == '0203' || $response_message == 'INVALID SESSIONID') {
		 	// return 'session id salah';
			$channel_code = env('CHANNELCODE_BILLER');
			$request_datetime = date('YmdHis');
			$shared_key = env('SHARED_KEY_BILLER');
			$login_name = env('LOGIN_NAME_BILLER'); 

			$send = array(
				"CHANNELCODE"       => $channel_code,
				"REQUESTDATETIME"   => $request_datetime,
				"LOGINNAME"         => $login_name,
				"PASSWORD"          => Sandi::get(),
				"WORDS"             => sha1($channel_code . $request_datetime . $shared_key . $login_name)
			);

			$res = RestCurl::hit(env('LINK_DOKU_BILLER').'/DepositSystem-api/AgentLoginMIP?',$send,'POST');
			print_r($res);

		} else {
			return 'lanjut';
		}

	}
}
