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

class BillerController {

	public function index()
	{
		$data = array(
			'CHANNELCODE' 		=> '6021',
			'REQUESTDATETIME' 	=> '20190221220600' ,
			'LOGINNAME' 		=> 'agentkopastra' ,
			'PASSWORD' 			=> 'simpel' ,
			'WORDS'				=> '252138911f613c503457c5f1097b4dcb56ecc65b' 
			 );

		$res = RestCurl::exec('POST','http://103.10.129.109/DepositSystem-api/AgentLoginMIP?',$data);
		dd($res);
		// $send_otp_action = RestCurl::exec('POST',env('URL_NOTIF').'send-otp',$send_otp);
	}

}
