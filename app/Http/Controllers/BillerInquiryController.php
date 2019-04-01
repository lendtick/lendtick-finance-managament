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

class BillerInquiryController extends Controller {

    /**
    * @SWG\Post(
    *     path="/biller/inquiry",
    *     consumes={"multipart/form-data"},
    *     description="Biller Inquiry, Bisa Isi Pulsa, Data, Bayar Tagihan, dll",
    *     operationId="billerinquiry",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="9900002",
    *         in="formData",
    *         name="billerid",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="akusayanglutfi",
    *         in="formData",
    *         name="sessionid",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="081311529594",
    *         in="formData",
    *         name="accountnumber",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Biller Inquiry",
    *     tags={
    *         "Biller"
    *     }
    * )
    * */

	// get list billers
    public function store(Request $request)
    {

      try {

       if(empty($request->json())) throw New \Exception('Params not found', 500);

       // bisa semua selain bayar BPJS Kesehatan

       $this->validate($request, [
          'billerid'		      => 'required',
          'sessionid'         => 'required', // Please refer to BILLER ID LIST
          'accountnumber'     => 'required', // PLN POSTPAID Subscriber ID PLN NONTAGLIS Registration Number TELKOM PSTN Area code (4 digit) + Phone number (9 digit, zero left padding) PDAM Customer ID MULTIFINANCE Subscriber ID 
       ]);

       $channel_code = env('CHANNELCODE_BILLER');
       $request_date = date('YmdHis');

       $check = array(
        'CHANNELCODE'       => $channel_code, //Channel Identification Code
        'SESSIONID'         => $request->sessionid, // Session for each success login.
        'REQUESTDATETIME'   => '20190401074352', //$request_date, //yyyyMMddHHmmss
        'WORDS'             => '186c12d92120c3ca91ef053a8717e72c08eb9129', // sha1($channel_code . $request->sessionid . $request_date . env('SHARED_KEY_BILLER') . $request->billerid . $request->accountnumber),  // Hashed key combination encryption using SHA1 method. The hashed key generated from combining these parameters in order.
        // (CHANNELCODE + SESSIONID + REQUESTDATETIME + SHARED KEY + BILLERID + ACCOUNTNUMBER)
        'BILLERID'          => $request->billerid, // Please refer to BILLER ID LIST
        'ACCOUNT_NUMBER'    => $request->accountnumber,  //PLN POSTPAID Subscriber ID PLN NONTAGLIS Registration Number TELKOM PSTN Area code (4 digit) + Phone number (9 digit, zero left padding) PDAM Customer ID MULTIFINANCE Subscriber ID 
        'SYSTRACE'          => '12345', // System trace number
        'ADDITIONALDATA1'   => $channel_code,  //Additional information, please fill with channel code
        'ADDITIONALDATA2'   => '', // Additional information 
        'ADDITIONALDATA3'   => '', // Additional information, only BPJS Kesehatan fill this parameter with Phone number and month bill,o i.e "081319422963|2" 
        );
       print_r($check);
       $res = (object) RestCurl::exec('POST',env('LINK_DOKU_BILLER').'/DepositSystem-api/Inquiry?',$check);

       if ($res->data->responsecode == '0000') {
            $errorMsg   = 'Sukses';
            $res = $res->data;
        } else {
            $errorMsg   = 'Gagal';
            $res = $res->data;
        }

        $httpcode 	= 200;
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

     /**
    * @SWG\Post(
    *     path="/biller/inquiry-electricity-postpaid",
    *     consumes={"multipart/form-data"},
    *     description="Biller Inquiry Listrik Postpaid",
    *     operationId="billerinquiryPostpaid",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="9950101",
    *         in="formData",
    *         name="billerid",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="akusayanglutfi",
    *         in="formData",
    *         name="sessionid",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="081311529594",
    *         in="formData",
    *         name="accountnumber",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Biller Inquiry Listrik Postpaid",
    *     tags={
    *         "Biller"
    *     }
    * )
    * */

    /**
    * @SWG\Post(
    *     path="/biller/inquiry-electricity-prepaid",
    *     consumes={"multipart/form-data"},
    *     description="Biller Inquiry Listrik prepaid",
    *     operationId="billerinquiryprepaid",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="9950102",
    *         in="formData",
    *         name="billerid",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="akusayanglutfi",
    *         in="formData",
    *         name="sessionid",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="081311529594",
    *         in="formData",
    *         name="accountnumber",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Biller Inquiry Listrik prepaid",
    *     tags={
    *         "Biller"
    *     }
    * )
    * */

    /**
    * @SWG\Post(
    *     path="/biller/inquiry-pulsa-data",
    *     consumes={"multipart/form-data"},
    *     description="Biller Inquiry Listrik Pulsa Data : 9910016, 9910011, 9910007, 9910001, 9900016, 9900015, 9900014, 9900013, 9900012, 9900011, 9900010, 9900009, 9900008, 9900007, 9900004, 9900003, 9900002, 9900001",
    *     operationId="billerinquirypulsa",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="9900007",
    *         in="formData",
    *         name="billerid",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="akusayanglutfi",
    *         in="formData",
    *         name="sessionid",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="081311529594",
    *         in="formData",
    *         name="accountnumber",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Biller Inquiry Listrik Pulsa Data ",
    *     tags={
    *         "Biller"
    *     }
    * )
    * */
     

     
}
