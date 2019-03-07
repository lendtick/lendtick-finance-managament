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

	public function request()
	{
		$header = '<INQUIRY_RESPONSE/>';
 		$content = array(
			'PAYMENTCODE' => '8975011200005642',
			'AMOUNT' => '100000.0',
			'PURCHASEAMOUNT' => '100000.00',
			'MINAMOUNT' => '10000.0',
			'MAXAMOUNT' => '550000.0',
			'TRANSIDMERCHANT' => '1396430482839',
			'WORDS' => 'b5a22f37ad0693ebac1bf03a89a8faeae9e7f390',
			'REQUESTDATETIME' => '20140402162122',
			'CURRENCY' => '360',
			'PURCHASECURRENCY' => '360',
			'SESSIONID' => 'dxgcmvcbywhu3t5mwye7ngqhpf8i6edu',
			'NAME' => 'Nama Lengkap',
			'EMAIL' => 'nama@xyx.com',
			'BASKET' => 'ITEM 1,10000.00,2,20000.00;ITEM 2,20000.00,4,80000.00',
			'ADDITIONALDATA' => 'BORNEO TOUR AND TRAVEL',
			'RESPONSECODE' => '0000',
		); 
		return XMLHelper::response($content, new \SimpleXMLElement($header))->asXML();
 
	}

	

}
