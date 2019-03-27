<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Validator;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Models\Order\Order as OrderModel;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\RestCurl;

class OrderBillerController extends Controller {

	/**
    * @SWG\Get(
    *     path="/order/biller",
    *     description="Check Phone Number for Information Provider, and Billerscode",
    *     operationId="check-phone",
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="0812",
    *         in="query",
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

	public function store(Request $request)
	{
		try {

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
                'billers_id' => 'required',
                'account_number' => 'required',
                'type_payment' => 'required',
                'bill_id' => 'required' 
            ]);

            $insert_order = array(
                'billers_id'        => trim($request->billers_id),
                'account_number'    => trim($request->account_number),
                'session_id'        => $request->session_id,
                'additional_data_1' => $request->additional_data_1,
                'additional_data_2' => $request->additional_data_2,
                'additional_data_3' => $request->additional_data_3,
                'status_order'      => 'active',
                'create_date_order' => date('Y-m-d H:i:s'),
                'type_payment'      => trim($request->type_payment),
                'id_user'           => $request->id_user,
                'bill_id'           => trim($request->bill_id),
                'latitude'          => $request->latitude,
                'longitude'         => $request->longitude
            );

            // dd($insert_order);

            $insert = OrderModel::insert($insert_order);
            if ($insert) {
                $httpcode   = 200;
                $status     = 1;
                $data       = '';
                $errorMsg   = 'Berhasil membuat order';
            } else {
                throw New \Exception('Order gagal, silahkan coba kembali', 500);
            }

        } catch(\Exception $e) {
           $status   = 0;
           $httpcode = 400;
           $data     = null;
           $errorMsg = $e->getMessage();
       }

       return response()->json(Api::response($status,$errorMsg,$data),$httpcode);
   }
}
