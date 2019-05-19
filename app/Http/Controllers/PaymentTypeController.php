<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Validator;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Models\Master\PaymentTypeModel as PaymentType;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\RestCurl;

class PaymentTypeController extends Controller {

	/**
    * @SWG\Get(
    *     path="/master/paymenttype",
    *     description="Master Payment Type",
    *     operationId="master-payment-type",
    *     produces={"application/json"},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Master Payment Type",
    *     tags={
    *         "Master Payment"
    *     }
    * )
    * */ 

	public function get(Request $request)
	{
		try {

			$res = PaymentType::get();

			$httpcode 	= 200;
			$status   	= 1;
			$data 		= $res;
			$errorMsg 	= '';

		} catch(\Exception $e) {
			$status   = 0;
			$httpcode = 400;
			$data     = null;
			$errorMsg = $e->getMessage();
		}

		return response()->json(Api::response($status,$errorMsg,$data),$httpcode);
	}
}
