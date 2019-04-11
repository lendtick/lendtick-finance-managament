<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Validator;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Models\Order\Order as OrderModel;
use DB;

use App\Models\Order\Order as OrderHeader;
use App\Models\Order\OrderDetail as OrderDetail;
use App\Models\Order\OrderDelivery as OrderDelivery;

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
                'total_billing'         => 'required',
                'id_workflow_status'    => 'required',
                'id_user_company'       => 'required',
                'cart'                  => 'required',
                'id_delivery_type'      => 'required',
                'name_delivery_type'    => 'required',
            ]);

            $params = (json_decode($request->getContent(), true));

            $order_header = array(
                'id_user'              => $request->id_user,
                'billing_number'       => 'INV/'.date('Ymd').'/'.time(),
                'billing_date'         => date('Y-m-d H:i:s'),
                'total_billing'        => $request->total_billing ? $request->total_billing : '',
                'id_workflow_status'   => $request->id_workflow_status ? $request->id_workflow_status : '',
                'id_user_company'      => $request->id_user_company ? $request->id_user_company : '',
                'systrace'             => time(),
            );


            DB::beginTransaction();

            $insert_header = OrderHeader::insert($order_header); 
            $id_order = OrderHeader::where($order_header)->first(); 


            foreach ($request->cart as $cart) {

                $order_detail[] = array(
                    'id_order'           => $id_order->id_order,
                    'id_channel'         => $cart['id_channel'],
                    // 'category_id'        => $cart['category_id'],
                    // 'product_id'         => $cart['product_id'] ? $cart['product_id'] : '',
                    'product_name'       => $cart['product_name'] ? $cart['product_name'] : '',
                    'product_image_path' => $cart['product_image_path'] ? $cart['product_image_path'] : '',
                    'biller_id'          => $cart['biller_id'] ? $cart['biller_id'] : '',
                    // 'biller_name'        => $cart['biller_name'] ? $cart['biller_name'] : '',
                    'bill_id'            => $cart['bill_id'] ? $cart['bill_id'] : '',   
                    // 'bill_details'       => $cart['bill_details'] ? $cart['bill_details'] : '',
                    'quantity'           => $cart['quantity'] ? $cart['quantity'] : '',
                    // 'id_channel'         => $cart['id_channel'] ? $cart['id_channel'] : '',
                    'sell_price'         => $cart['sell_price'] ? $cart['sell_price'] : '',
                    'base_price'         => $cart['base_price'] ? $cart['base_price'] : '',
                    // 'product_details'    => $cart['product_details'] ? $cart['product_details'] : '',
                    'additional_data_1'  => $cart['additional_data_1'] ? $cart['additional_data_1'] : '',
                    'additional_data_2'  => $cart['additional_data_2'] ? $cart['additional_data_2'] : '',
                    'additional_data_3'  => $cart['additional_data_3'] ? $cart['additional_data_3'] : ''
                );

            }

            $insert_detail = OrderDetail::insert($order_detail);

            $order_delivery = array(
                'id_order'           => $id_order->id_order,
                'id_delivery_type'      => $request->id_delivery_type ?  $request->id_delivery_type : '',
                'delivery_details'    => $request->name_delivery_type ? $request->name_delivery_type : '' 
            );

            $insert_delivery = OrderDelivery::insert($order_delivery);


            // echo $id_order->id_order; 
            // print_r($insert_header);
            // print_r($order_detail);
            // print_r($order_delivery);
            DB::commit();


            if ($insert_delivery) {
                $httpcode   = 200;
                $status     = 1;
                $data       = '';
                $errorMsg   = 'Berhasil membuat order';
            } else {
                throw New \Exception('Order gagal, silahkan coba kembali', 500);
            }

        } catch(\Exception $e) {
            DB::rollback();
            $status   = 0;
            $httpcode = 400;
            $data     = ['message_system' => $e->getMessage()];
            $errorMsg = 'Terjadi Kesalahan Order, silahkan coba sekali lagi';

        }

        return response()->json(Api::response($status,$errorMsg,$data),$httpcode);
    }
}
