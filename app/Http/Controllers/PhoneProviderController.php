<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Validator;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Models\Order\ProviderPhoneMaster as ProviderPhone;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\RestCurl;

class PhoneProviderController extends Controller {

	/**
    * @SWG\Get(
    *     path="/order/check-phone",
    *     description="Check Phone Number for Information Provider, and Billerscode",
    *     operationId="check-phone",
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="0812",
    *         in="formData",
    *         name="phone_number",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Check Phone Number",
    *     tags={
    *         "Order"
    *     }
    * )
    * */ 

	public function check(Request $request)
	{
		try {

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'phone_number'	=> 'required',
			]);

			$res = ProviderPhone::where('provider_phone_number', $request->phone_number)->get();

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
