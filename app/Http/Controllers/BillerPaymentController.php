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
use App\Models\Order\OrderDetail as OrderDetail;
use Carbon\Carbon;

use App\Helpers\Api;
use App\Helpers\SMS;
use App\Helpers\RestCurl;
use App\Helpers\SandiBiller AS Sandi;

class BillerPaymentController extends Controller {

    /**
    * @SWG\Post(
    *     path="/biller/payment",
    *     consumes={"multipart/form-data"},
    *     description="Biller Payment",
    *     operationId="billerpayment",
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
    *     @SWG\Parameter(
    *         description="15339",
    *         in="formData",
    *         name="inquiryid",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="50000",
    *         in="formData",
    *         name="amount",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="2",
    *         in="formData",
    *         name="billid",
    *         required=true,
    *         type="string"
    *     ),
     *     @SWG\Parameter(
    *         description="2",
    *         in="formData",
    *         name="request_date",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Biller Payment",
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
          'billerid'		  => 'required',
          'sessionid'         => 'required', // Please refer to BILLER ID LIST
          'accountnumber'     => 'required', // Meter Serial Number / Subscriber ID
          'inquiryid'         => 'required', // Inquiry ID from inquiry process
          'amount'            => 'required', // Total transaction amount
          'systrace'        => 'required',
          'billid'            => 'required',
          'request_date'            => 'required' // Diambil dari inquiry, Chosen bill ID or leave it empty for PLN Prepaid
      ]);

        // return $request->all();

        $channel_code = env('CHANNELCODE_BILLER');
        $request_date = date('YmdHis');

        $check = array(
        'CHANNELCODE'       => $channel_code, //Channel Identification Code
        'SESSIONID'         => $request->sessionid, // Session for each success login.
        'REQUESTDATETIME'   => $request->request_date, //yyyyMMddHHmmss
        'WORDS'             => sha1($channel_code . $request->sessionid . $request->request_date . env('SHARED_KEY_BILLER') . $request->billerid . $request->accountnumber),  // Hashed key combination encryption using SHA1 method. The hashed key generated from combining these parameters in order.
        // (CHANNELCODE + SESSIONID + REQUESTDATETIME + SHARED KEY + BILLERID + ACCOUNTNUMBER)
        'BILLERID'          => $request->billerid, // Please refer to BILLER ID LIST
        'ACCOUNT_NUMBER'    => $request->accountnumber,  // Meter Serial Number / Subscriber ID
        'INQUIRYID'         => $request->inquiryid, // Inquiry ID from inquiry process
        'AMOUNT'            => $request->amount, // Inquiry ID from inquiry process
        'BILL_ID'           => $request->billid, // Chosen bill ID or leave it empty for PLN Prepaid



        'SYSTRACE'          => $request->systrace, // System trace number
        'ADDITIONALDATA1'   => $channel_code,  //Additional information, please fill with channel code
        'ADDITIONALDATA2'   => 'biller', // Additional information 
        'ADDITIONALDATA3'   => 'biller', // Additional information, only BPJS Kesehatan fill this parameter with Phone number and month bill,o i.e "081319422963|2" 
        'LATITUDE'          => '',
        'LONGITUDE'         => '',
        'PASSWORD'          => Sandi::get()
    );

        // $res = (object) RestCurl::hit(env('LINK_DOKU_BILLER').'/DepositSystem-api/Inquiry?',$check,'POST');

        $res = (object) RestCurl::hit(env('LINK_DOKU_BILLER').'/DepositSystem-api/Payment?',$check,'POST'); 

        print_r([$check , $res]);
        die();

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


// get 
private function _curl($url='')
{
    $ch = curl_init();
            // set url
    curl_setopt($ch, CURLOPT_URL, $url);
            //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // $output contains the output string
    $output = curl_exec($ch);
        // close curl resource to free up system resources
    curl_close($ch);

    return $output;
}
}
