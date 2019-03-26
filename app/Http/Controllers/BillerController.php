<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Validator;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Helpers\Doku AS Doku;
use App\Repositories\Finance\DokuRepo AS DokuRepo;
use App\Models\User\RegisterMemberFlowManagement AS MemberFlow;
use App\Models\User\UserManagement AS User;
use App\Models\User\ProfileManagement AS Profile;
use App\Models\Master\RegisterMemberFlowMaster AS MasterFlow;
use App\Models\Master\WorkflowMaster AS MasterWorkflow;
use App\Models\Order\BillersMaster AS BillersMaster;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\BlobStorage;
use App\Helpers\RestCurl;

class BillerController extends Controller {

	public function index()
	{
		$channel_code = env('CHANNELCODE_BILLER');
		$request_datetime = date('Ymdhis');
		$shared_key = env('SHARED_KEY_BILLER');
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
 
		$send = array(
			"CHANNELCODE" 		=> $channel_code,
			"REQUESTDATETIME" 	=> $request_datetime,
			"LOGINNAME" 		=> $login_name,
			"PASSWORD" 			=> bin2hex($encrypted),
			"WORDS"				=> sha1($channel_code . $request_datetime . $shared_key . $login_name)
		);

		print_r($send); die();

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

	/**
    * @SWG\Get(
    *     path="/biller/list",
    *     consumes={"multipart/form-data"},
    *     description="VA Notify From Doku",
    *     operationId="billerlist",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Biller List Information",
    *     tags={
    *         "Biller"
    *     }
    * )
    * */

	// get list billers
	public function listBiller(Request $request)
	{
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);
			
			$res = BillersMaster::where('status' , 1)->orderBy('master_biller_id','ASC')->get();

			$status   = 1;
			$httpcode = 200;
			$errorMsg     = 'Sukses';  
			$data = $res;

		}catch(\Exception $e){
			$status   = 0;
			$httpcode = 400;
			$data     = null;
			$errorMsg = $e->getMessage();
		}

		return response()->json(Api::response($status,$errorMsg,$data),$httpcode);
		
	}

	/**
    * @SWG\Post(
    *     path="/biller/update",
    *     consumes={"multipart/form-data"},
    *     description="VA Notify From Doku",
    *     operationId="billerupdate",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="9950101",
    *         in="formData",
    *         name="billers_id",
    *         required=false,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="PLN Postpaid",
    *         in="formData",
    *         name="billers_name",
    *         required=false,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="1",
    *         in="formData",
    *         name="status",
    *         required=false,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Biller Update",
    *     tags={
    *         "Biller"
    *     }
    * )
    * */

	// get list billers
	public function updateBiller(Request $request)
	{
		try{
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'billers_id'		=> 'required|integer',
				'billers_name'		=> 'required',
				'status'			=> 'required'
			]);

			$update = array(
				'billers_name'	=> $request->billers_name,
				'status'		=> $request->status
			);

			// check
			$check = BillersMaster::where('billers_id',$request->billers_id);

			if ($check->count() > 0) {
				$httpcode 	= 200;
				$errorMsg 	= 'Sukses';
				$res = BillersMaster::where('billers_id',$request->billers_id)->update($update);
			} else {
				$httpcode 	= 400;
				$errorMsg 	= 'Tidak ditemukan';
			}

			// response
			$status   	= 1;
			$data 		= '';

		}catch(\Exception $e){
			$status   = 0;
			$httpcode = 400;
			$data     = null;
			$errorMsg = $e->getMessage();
		}

		return response()->json(Api::response($status,$errorMsg,$data),$httpcode);
		
	}

}
