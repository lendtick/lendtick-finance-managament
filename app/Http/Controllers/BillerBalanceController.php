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

class BillerBalanceController extends Controller {
    /**
    * @SWG\Post(
    *     path="/biller/balance",
    *     consumes={"multipart/form-data"},
    *     description="Biller Balance Information",
    *     operationId="billerbalance",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="akusayanglutfi",
    *         in="formData",
    *         name="sessionid",
    *         required=true,
    *         type="string"
    *     ),
     *     @SWG\Parameter(
    *         description="akusayanglutfi",
    *         in="formData",
    *         name="request_date",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Biller Balance",
    *     tags={
    *         "Biller"
    *     }
    * )
    * */

	// get list billers
    public function check(Request $request)
    {

      try {

       if(empty($request->json())) throw New \Exception('Params not found', 500);

       $this->validate($request, [
        'sessionid'		       => 'required'
       ]);

       $channel_code = env('CHANNELCODE_BILLER');
       $request_date = $request->request_date;//date('YmdHis');

       $check_balance = array(
        'CHANNELCODE'       => $channel_code, //Channel Identification Code
        'SESSIONID'         => $request->sessionid, // Session for each success login.
        'REQUESTDATETIME'   => $request->request_date, //yyyyMMddHHmmss
        'WORDS'             => sha1($channel_code . $request->sessionid . $request_date . env('SHARED_KEY_BILLER')),  // Hashed key combination encryption using SHA1 method. The hashed key generated from combining these parameters in order. (CHANNELCODE + SESSIONID + REQUESTDATETIME + SHARED KEY)
        );
       // hit($url, $dataArray = array(), $method='GET' ){
       $res = (object) RestCurl::hit(env('LINK_DOKU_BILLER').'/DepositSystem-api/CheckLastBalance?',$check_balance,'POST');
       dd($res);
       // $res = (object) RestCurl::exec('POST',env('LINK_DOKU_BILLER').'/DepositSystem-api/CheckLastBalance?',$check_balance);

       if ($res->data->responsecode == '0000') {
            $res = $res->data;
        } else {
            $res = $res->data;
        }

        $httpcode 	= 200;
        $errorMsg 	= 'Sukses'; 
        $status   	= 1;
        $data 		= $res;

    } catch(\Exception $e) {
       $status   = 0;
       $httpcode = 400;
       $data     = null;
       $errorMsg = $e->getMessage();
    }

    return response()->json(Api::response($status,$errorMsg,$data),$httpcode);

    }
}
