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
use App\Models\Finance\LogModel;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\BlobStorage;
use App\Helpers\RestCurl;
use App\Helpers\XMLHelper;

class InquiryDokuController {

	public function __construct(){
		$this->now = date('YmdHis');
		$this->fields = array('MALLID', 'CHAINMERCHANT', 'PAYMENTCHANNEL', 'PAYMENTCODE','WORDS');
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

	private function __invalid_account_number($header){
		$content = array( 
			'RESPONSECODE' => '3000',
		); 
		return XMLHelper::response($content, new \SimpleXMLElement($header))->asXML();
		// die();
	}

	private function __bill_already_paid($header){
		$content = array( 
			'RESPONSECODE' => '3002',
		); 
		return XMLHelper::response($content, new \SimpleXMLElement($header))->asXML();
		// die();
	}


	// ======================================================================
	// This for inquiry to virtual account
  // ======================================================================
  /**
    * @SWG\Post(
    *     path="/doku/va/inquiry",
    *     consumes={"multipart/form-data"},
    *     description="VA Inquiry",
    *     operationId="doku_va",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="MALLID",
    *         in="formData",
    *         name="MALLID",
    *         required=true,
    *         type="number",
    *         default="1111"
    *     ),
    *     @SWG\Parameter(
    *         description="Chain Merchant",
    *         in="formData",
    *         name="CHAINMERCHANT",
    *         required=true,
    *         type="string",
    *         default="NA"
    *     ),
    *     @SWG\Parameter(
    *         description="PAYMENT CHANNEL",
    *         in="formData",
    *         name="PAYMENTCHANNEL",
    *         required=true,
    *         type="string",
    *         default="36"
    *     ),
    *     @SWG\Parameter(
    *         description="PAYMENT CODE",
    *         in="formData",
    *         name="PAYMENTCODE",
    *         required=true,
    *         type="number",
    *         default="8856095100000013"
    *     ),
    *     @SWG\Parameter(
    *         description="WORDS",
    *         in="formData",
    *         name="WORDS",
    *         required=true,
    *         type="number",
    *         default="e48b892015a850fb7090a899a76b18783c5d40d9"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="VA Request",
    *     tags={
    *         "Doku VA Permata"
    *     }
    * )
    * */

	public function request(Request $r)
	{

		// if($this->__check_post()){
			$post = (array) $r->post();


		// 	if($this->__check_var($post)){

				$header = '<INQUIRY_RESPONSE/>';
				$content = array();

				$check_paid = DokuRepo::getByParam('transidmerchant', $post['PAYMENTCODE'])->first();
				// dd($check_paid);
				if ($check_paid->trxstatus == 'SUCCESS') {
					return $this->__bill_already_paid($header);
				}


				$check_inquiry = DokuRepo::getTransID($post['PAYMENTCODE']);
				if (count($check_inquiry) == 0) {
					return $this->__invalid_account_number($header);
				}
				$i = $check_inquiry;
				$get = $i[0];
				// echo $r->MALLID = env('DOKU_MALL_ID');
				// 3002
				
				if ($post['MALLID'] <> env('DOKU_MALL_ID')) { 
					return $this->__invalid_account_number($header);
				}

				

				

				// get data user 
				$get_user = Profile::where('id_user',$get->id_user)->get()->first();
				
				$basket = array(
					'PENDAFTARAN MEMBER KOPERASI ASTRA',
					number_format($get->totalamount,2,".",""),
					'1',
					number_format($get->totalamount,2,".","").';'
				); 

				$content = array(
					'PAYMENTCODE' => $get->transidmerchant,
					'AMOUNT' => number_format($get->totalamount,2,".",""),
					'PURCHASEAMOUNT' => number_format($get->totalamount,2,".",""),
					'MINAMOUNT' => number_format($get->totalamount,2,".",""),
					'MAXAMOUNT' => number_format($get->totalamount,2,".",""),
					'TRANSIDMERCHANT' => $get->transidmerchant,
					'WORDS' =>  sha1(env('DOKU_MALL_ID') . env('DOKU_SHARED_KEY') . $get->transidmerchant),
					'REQUESTDATETIME' => date('YmdHis'),
					'CURRENCY' => 360,
					'PURCHASECURRENCY' => 360,
					'SESSIONID' => $get->session_id,
					'NAME' => $get_user->name,
					'EMAIL' => $get_user->email,
					'BASKET' => implode(',', $basket),
					'ADDITIONALDATA' => $get_user->name,
					'RESPONSECODE' => '0000',
				); 

				// insert log
				$insert = array('value' => json_encode($r->all()));
				LogModel::create($insert);
				return XMLHelper::response($content, new \SimpleXMLElement($header))->asXML();

		// 	}
		// 	return response()->json(
		// 		Api::response(
		// 			false,Template::lang('Please check your POST data()')
		// 		),400);
		// }
		// return response()->json(Api::response(false,Template::lang('Please POST method')),400);

	}

	

}
