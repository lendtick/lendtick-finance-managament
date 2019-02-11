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

class DokuController extends Controller
{
  public function __construct(){
    $this->now = date('YmdHis');
		$this->fields = array('chain_merchant', 'amount', 'invoice', 'email', 'name', 'phone', 'id_user');
  }

  public function __destruct(){
      //
  }

  // ======================================================================
	// This for request to virtual account
  // ======================================================================
  /**
    * @SWG\Post(
    *     path="/doku/va/request",
    *     consumes={"multipart/form-data"},
    *     description="VA Request",
    *     operationId="doku_va",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="Chain Merchant",
    *         in="formData",
    *         name="chain_merchant",
    *         required=true,
    *         type="string",
    *         default="NA"
    *     ),
    *     @SWG\Parameter(
    *         description="Amount",
    *         in="formData",
    *         name="amount",
    *         required=true,
    *         type="integer",
    *         default="10000"
    *     ),
    *     @SWG\Parameter(
    *         description="Va Number",
    *         in="formData",
    *         name="invoice",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Email user",
    *         in="formData",
    *         name="email",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Name",
    *         in="formData",
    *         name="name",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Name",
    *         in="formData",
    *         name="phone",
    *         required=false,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="id_user",
    *         in="formData",
    *         name="id_user",
    *         required=true,
    *         type="integer"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="VA Request",
    *     tags={
    *         "Doku"
    *     }
    * )
    * */
	public function request(Request $r, Doku $doku){
		if($this->__check_post()){
      $post = (array) $r->post();

			if($this->__check_var($post)){
				// set method to doku api
				$doku::setMethod('generateCodeUrl');
				// for staging/live method
        $doku::staging(true);


				// collect all data to push it
				$data = array(
					'req_chain_merchant' => $post['chain_merchant'],
					'req_amount' => number_format((is_string($post['amount'])?(float)$post['amount']:$post['amount']),2,'.',''),
					'req_purchase_amount' => number_format((is_string($post['amount'])?(float)$post['amount']:$post['amount']),2,'.',''),
					'req_trans_id_merchant' => $post['invoice'],
					'req_request_date_time' => $this->now,
					'req_expiry_time' => (24*60),
					'req_open_amount_status' => '',
					'req_mobile_phone' => $post['phone'],
					'req_session_id' => sha1($this->now),
					'req_email' => $post['email'],
					'req_name' => $post['name'],
					'req_currency' => '360'
				);
				$doku::data($data);
        $config = $doku::getConfig();

				// processing send data
				$doku::send();

				// get respose in json result
				// ...::json = true;

				// getting response from doku
				// notes (2 Method):
				// ...::response() / ...::getResponse()
        $resp = $doku::response();

				if(isset($resp->data->res_pay_code)){
					$resp->data->{'va_mandiri'} = $this->config->item('prefix')['mandiri'].$resp->data->res_pay_code;
					$resp->data->{'va_bca'} = $this->config->item('prefix')['bca'].$resp->data->res_pay_code;
				}

				if(isset($resp->data->res_pay_code)){
					// save to database
					DokuRepo::create(array(
						'transidmerchant' 	=> $config['req_trans_id_merchant'],
						'totalamount' 		=> $config['req_amount'],
						'words' 			=> $config['req_words'],
						'statustype' 		=> '',
						'response_code' 	=> '',
						'approvalcode' 		=> '',
						'trxstatus' 		=> 'Requested',
						'payment_channel' 	=> '',
						'paymentcode' 		=> '',
						'session_id' 		=> $config['req_session_id'],
						'bank_issuer' 		=> '',
						'creditcard' 		=> '',
						'payment_date_time' => '',
						'verifyid' 			=> '',
						'verifyscore' 		=> '',
						'verifystatus' 		=> '',
						'id_user' 		=> $post['id_user']
					));
				}

				response()->json(Api::response(false,Template::lang('success')),201);
			}
      return response()->json(Api::response(false,Template::lang('Please check your POST data(chain_merchant, amount, invoice, email, name)')),400);
		}
    return response()->json(Api::response(false,Template::lang('Please POST method')),400);
	}


	// ======================================================================
	// This function for feedback from doku service
  // ======================================================================
  /**
    * @SWG\Post(
    *     path="/doku/va/notify",
    *     consumes={"multipart/form-data"},
    *     description="VA Notify From Doku",
    *     operationId="doku_va_notify",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="TRANS ID / VA Number",
    *         in="formData",
    *         name="TRANSIDMERCHANT",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Amount",
    *         in="formData",
    *         name="AMOUNT",
    *         required=true,
    *         type="integer",
    *         default="10000"
    *     ),
    *     @SWG\Parameter(
    *         description="Hash WORDS From Doku",
    *         in="formData",
    *         name="WORDS",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Status Type From Doku",
    *         in="formData",
    *         name="STATUSTYPE",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Response Code From Doku",
    *         in="formData",
    *         name="RESPONSECODE",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Approval Code From Doku",
    *         in="formData",
    *         name="APPROVALCODE",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Status Message From Doku",
    *         in="formData",
    *         name="RESULTMSG",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Payment Channel From Doku",
    *         in="formData",
    *         name="PAYMENTCHANNEL",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Payment Code From Doku",
    *         in="formData",
    *         name="PAYMENTCODE",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Session ID From Doku",
    *         in="formData",
    *         name="SESSIONID",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Bank Issuer From Doku",
    *         in="formData",
    *         name="BANK",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Card Number From Doku",
    *         in="formData",
    *         name="MCN",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Payment Datetime From Doku",
    *         in="formData",
    *         name="PAYMENTDATETIME",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Verify ID From Doku",
    *         in="formData",
    *         name="VERIFYID",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Verify Score From Doku",
    *         in="formData",
    *         name="VERIFYSCORE",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Parameter(
    *         description="Verify Status From Doku",
    *         in="formData",
    *         name="VERIFYSTATUS",
    *         required=true,
    *         type="string",
    *         default="0"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="VA Notify",
    *     tags={
    *         "Doku"
    *     }
    * )
    * */
	public function notify(Request $r, Hash $h){
		if($this->__check_post()){
			// $ip_range = "103.10.129.16";
			// if ( $_SERVER['REMOTE_ADDR'] != '103.10.129.16' && (substr($_SERVER['REMOTE_ADDR'],0,strlen($ip_range)) !== $ip_range) ){
			// if(in_array($_SERVER['REMOTE_ADDR'], array('103.10.129.16','103.10.129.9'))){
        $post = (array) $r->post();

				// collecting data
				$order_number 			= isset($post['TRANSIDMERCHANT'])?$post['TRANSIDMERCHANT']:0;
				$totalamount 			= $post['AMOUNT'];
				$words 					= $post['WORDS'];
				$statustype 			= $post['STATUSTYPE'];
				$response_code 			= $post['RESPONSECODE'];
				$approvalcode 			= $post['APPROVALCODE'];
				$status 				= $post['RESULTMSG'];
				$paymentchannel 		= $post['PAYMENTCHANNEL'];
				$paymentcode 			= $post['PAYMENTCODE'];
				$session_id 			= $post['SESSIONID'];
				$bank_issuer 			= $post['BANK'];
				$cardnumber 			= $post['MCN'];
				$payment_date_time 		= $post['PAYMENTDATETIME'];
				$verifyid 				= $post['VERIFYID'];
				$verifyscore 			= $post['VERIFYSCORE'];
				$verifystatus 			= $post['VERIFYSTATUS'];

				$MALLID 				= env("DOKU_MALL_ID", "");
				$SHAREDKEY 				= env("DOKU_SHARED_KEY", "");


        $WORDS_GENERATED 		= sha1($totalamount.$MALLID.$SHAREDKEY.$order_number.$status.$verifystatus);
        $pass = Api::randomString(8,'alphanumeric');
    
        if(env("BYPASS_DOKU", 0) == 1){
          $doku_update = DokuRepo::update($order_number, array(
            "words" => $words, 
            "statustype" => $statustype, 
            "response_code" => $response_code, 
            "approvalcode" => $approvalcode, 
            "trxstatus" => $status, 
            "payment_channel" => $paymentchannel, 
            "paymentcode" => $paymentcode, 
            "session_id" => $session_id, 
            "bank_issuer" => $bank_issuer, 
            "creditcard" => htmlentities($cardnumber), 
            "payment_date_time" => date("Y-m-d H:i:s",strtotime($payment_date_time)), 
            "verifyid" => $verifyid, 
            "verifyscore" => $verifyscore, 
            "verifystatus" => $verifystatus
          ));
          $doku_data = DokuRepo::getByParam("transidmerchant", $order_number)->first();

          // update flag from not paid to paid
          $master_flow = MasterWorkflow::where('workflow_status_name', "like", "Active%")->where('workflow_status_desc', "like", "%user status%")->get()->first();
          ($member = User::where('id_user',$doku_data->id_user))->update(array('id_workflow_status' => $master_flow->id_workflow_status));
          $member = $member->get()->first();

          // get generate id_koperasi
          $nik = RestCurl::get(env('LINK_USER','https://lentick-api-user-dev.azurewebsites.net')."/profile/generate-nik",[]);
          $member->username = $nik["data"]->data->nomor_NIK;
          $member->save();

          $profile = Profile::where('id_user',$member->id_user)->get()->first();
          $profile->id_koperasi = $nik["data"]->data->nomor_NIK;
          $profile->save();

          // save password to user
          $user = User::where('id_user', $profile->id_user)->get()->first();
          $user->password = $h->make($pass);
          $user->save();

          echo "Continue";

          // notify to user to get a credential
          $email = [
            "phone_number" => $profile->phone,
            "anggota_id" => $profile->id_user,
            "password" => $pass
          ];
          $res_email = RestCurl::post(env('LINK_NOTIF','https://lentick-api-notification-dev.azurewebsites.net')."/send-sms-after-payment", $email);

        } else {
          if ( $words == $WORDS_GENERATED ) {
            $q = DokuRepo::getTransID($order_number);
            if(count($q) > 0){
              $dt = $q[0];
              $hasil = isset($dt['transidmerchant'])?$dt['transidmerchant']:false;
              $amount = isset($dt['totalamount'])?$dt['totalamount']:0;
  
              if (!$hasil) {
                echo 'Stop1';
                // $this->response(array('error' => 'No transidmerchant'));
              } else{
                if ($status=="SUCCESS") {
                  $doku_update = DokuRepo::update($order_number, array(
                    "words" => $words, 
                    "statustype" => $statustype, 
                    "response_code" => $response_code, 
                    "approvalcode" => $approvalcode, 
                    "trxstatus" => $status, 
                    "payment_channel" => $paymentchannel, 
                    "paymentcode" => $paymentcode, 
                    "session_id" => $session_id, 
                    "bank_issuer" => $bank_issuer, 
                    "creditcard" => htmlentities($cardnumber), 
                    "payment_date_time" => date("Y-m-d H:i:s",strtotime($payment_date_time)), 
                    "verifyid" => $verifyid, 
                    "verifyscore" => $verifyscore, 
                    "verifystatus" => $verifystatus
                  ));
                  if(!$doku_update)
                     die ("Stop2");
                  else{
                    $doku_data = DokuRepo::getByParam("transidmerchant", $order_number)->first();

                    // update flag from not paid to paid
                    $master_flow = MasterWorkflow::where('workflow_status_name', "like", "%active%")->where('workflow_status_desc', "like", "%user status%")->get()->first();
                    ($member = User::where('id_user',$doku_data->id_user))->update(array('id_workflow_status' => $master_flow->id_workflow_status));
                    $member = $member->get()->first();
          
                    // get generate id_koperasi
                    $nik = RestCurl::get(env('LINK_USER','https://lentick-api-user-dev.azurewebsites.net')."/profile/generate-nik",[]);
                    $member->username = $nik["data"]->data->nomor_NIK;
                    $member->save();
          
                    $profile = Profile::where('id_user',$member->id_user)->get()->first();
                    $profile->id_koperasi = $nik["data"]->data->nomor_NIK;
                    $profile->save();

                    // save password to user
                    $user = User::where('id_user', $profile->id_user)->get()->first();
                    $user->password = $h->make($pass);
                    $user->save();
          
                    echo "Continue";
          
                    // notify to user to get a credential
                    $email = [
                      "phone_number" => $profile->phone,
                      "anggota_id" => $profile->id_user,
                      "password" => $pass
                    ];
                    $res_email = RestCurl::post(env('LINK_NOTIF','https://lentick-api-notification-dev.azurewebsites.net')."/send-sms-after-payment", $email);

                  }
                  // // 	$this->response(array('error' => 'Can\'t update success data'));
                } else {
                  // $sql = "UPDATE doku set trxstatus='Failed' where transidmerchant='".$order_number."'";
                  // $this->db->query($sql);
                  // if(!$this->db->affected_rows())
                  // 	die ("Stop3");
                  // // 	$this->response(array('error' => 'Can\'t update failed data'));
                }
                echo 'Continue';
                // $this->response(array('status' => true, 'message' => 'Success update data'));
              }
            } else
              echo "No data";
            // else
            // 	$this->response(array('error' => 'Stop | No data on database'));
          // }
          // else
          // 	echo "Stop | Words not match";
          // 	// $this->response(array('error' => 'Stop | Words not match'));
        }
			}
		} 
		// else
		// 	$this->response(array('error' => 'You don\'t have access'));
	}

	private function __check_var($post){
		$ret = true;
		foreach ($post as $field => $val) {
			if(!in_array($field, $this->fields)){
				$ret = false;
				break;
			}
		}
		return (count($post) == count($this->fields) && $ret);
	}

	private function __check_post(){
		return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
	}
}
