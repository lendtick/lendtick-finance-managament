<?php 

namespace App\Helpers;
use App\Helpers\RestCurl;

class SMS
{
	public static function OTPNumber()
	{
		try {
			return rand (1000,9999);
		} catch (Exception $e) {
			return 1111;
		}
	}

	// 
	public static function OTPsend($phone_number= null , $message = null)
	{
		try {
		
		$user_awo           = env('AWO_USER');
		$password_user_awo  = env('AWO_PASSWORD');
		$sender             = env('AWO_SENDER');  

		$data = array(
			'user'		=> $user_awo,
			'pwd'		=> $password_user_awo,
			'sender'	=> $sender,
			'msisdn'	=> $phone_number,
			'message'	=> $message,
		);

		// return env('AWO_URL_SEND_OTP').'?user='.$user_awo.'&pwd='.$password_user_awo.'&sender='.$sender.'&msisdn='.$phone_number.'&message='.$message;
		$url = env('AWO_URL_SEND_OTP').'?user='.$user_awo.'&pwd='.$password_user_awo.'&sender='.$sender.'&msisdn='.$phone_number.'&message='.$message;

		$res = RestCurl::exec('GET',$url,array(),'');

		return $res;

		} catch (Exception $e) {
			return false;
		}

	}
}
