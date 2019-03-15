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

	public function request(Request $r)
	{

		if($this->__check_post()){
			$post = (array) $r->post();


			if($this->__check_var($post)){

				$header = '<INQUIRY_RESPONSE/>';
				$content = array();

				// echo $r->MALLID = env('DOKU_MALL_ID');
				if ($post['MALLID'] <> env('DOKU_MALL_ID')) { 
					return $this->__invalid_account_number($header);
				}

				$check_inquiry = DokuRepo::getByParam('transidmerchant' , $post['PAYMENTCODE']);
				if ($check_inquiry->count() == 0) {
					return $this->__invalid_account_number($header);
				}

				$get = $check_inquiry->first();
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
					'TRANSIDMERCHANT' => $get->id,
					'WORDS' =>  sha1(env('DOKU_MALL_ID') . env('DOKU_SHARED_KEY') . $get->transidmerchant),
					'REQUESTDATETIME' => date('YmdHis'),
					'CURRENCY' => 360,
					'PURCHASECURRENCY' => 360,
					'SESSIONID' => sha1(date('YmdHis')),
					'NAME' => $get_user->name,
					'EMAIL' => $get_user->email,
					'BASKET' => implode(',', $basket),
					'ADDITIONALDATA' => $get_user->name,
					'RESPONSECODE' => '0000',
				); 
				return XMLHelper::response($content, new \SimpleXMLElement($header))->asXML();

			}
			return response()->json(
				Api::response(
					false,Template::lang('Please check your POST data()')
				),400);
		}
		return response()->json(Api::response(false,Template::lang('Please POST method')),400);

	}

	

}
