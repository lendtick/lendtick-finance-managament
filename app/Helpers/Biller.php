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

			if (BillerSession::get()->count()>0) {

				$get_data = BillerSession::get();
				return [ $get_data->SessionID , $get_data->RequestDate ];

			}

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
			$response = json_decode($res['response']);

			if ($response->responsecode == '0000' || $response->responsemsg == 'AGENT LOGIN IS SUCCESS') {
				// delete
				BillerSession::delete();
				// cek dulu ada gak ditable sessionid 
				// maka insert ke dalam sessionid table 
				BillerSession::create(['SessionID' =>  $response->sessionId , 'RequestDate' =>  $request_datetime]);
				// return 'berhasil ada sessionid';
				return [ $response->sessionId , $request_datetime ];
			}

		} else {
			return 'lanjut';
		}

	}
}
