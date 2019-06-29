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
use App\Models\Order\OrderPayment as OrderPayment;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\RestCurl;

// use App\Http\Controllers\DokuController as DC;

class OrderBillerController extends Controller { 

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
                'payment'               => 'required',
            ]);

            $params = (json_decode($request->getContent(), true));

            // print_r(DC::request()); die();

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
                    'category_id'        => $cart['category_id'],
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
                    'additional_data_3'  => $cart['additional_data_3'] ? $cart['additional_data_3'] : '',
                    'inquiry_id'         => $cart['inquiry_id'] ? $cart['inquiry_id'] : '',
                    'account_number'         => $cart['account_number'] ? $cart['account_number'] : '',
                );

            }

            $insert_detail = OrderDetail::insert($order_detail);

            $order_delivery = array(
                'id_order'              => $id_order->id_order,
                'id_delivery_type'      => $request->id_delivery_type ?  $request->id_delivery_type : '',
                'delivery_details'      => $request->name_delivery_type ? $request->name_delivery_type : '' 
            );

            $insert_delivery = OrderDelivery::insert($order_delivery);


            // insert payment order
            foreach ($request->payment as $payment) {

                $billertrx = NULL;
                if ($order_detail[0]['category_id'] == 'CATBILLER') { $billertrx = 1; }


                if ($payment['id_payment_type'] === 'PAY003') {



                    // $va_number = 88561083 . date('dHis');
                    $va_number = env('DOKU_VA_PERMATA') . date('dHis');
                    
                    // echo 'insert ke doku finance';

                    $order_payment[] = array(
                        'id_order'              => $id_order->id_order,
                        'id_payment_type'       => $payment['id_payment_type'],
                        'total_payment'         => $payment['total_payment'],
                        'identifier_number'     => $payment['identifier_number'],
                        'number_payment'        => $va_number

                    );

                    $amount = $payment['total_payment'];

                    // get profile user 
                    $token = $request->header('Authorization');
                    $profile = (object) RestCurl::exec('GET',env('LINK_USER').'/profile/get?id='.$request->id_user , '', $token);
                    $prof = $profile->data->data;
                    // end profile

                    $insert_to_doku = array(
                        'chain_merchant'    => 'NA',
                        'amount'            => number_format((is_string($amount)?(float)$amount:$amount),2,'.',''),
                        'invoice'           => $va_number,
                        'email'             => $prof->email,
                        'name'              => $prof->name,
                        'phone'             => $prof->phone_number,
                        'id_user'           => $request->id_user,
                        'billertrx'         => $billertrx // 1 = yes

                    );

                    $res_insert_to_doku = (object) RestCurl::hit(env('LINK_FINANCE').'/doku/va/request' , $insert_to_doku, 'POST');
                    $res_doku = json_decode($res_insert_to_doku->response);

                    if ($res_doku->status == true || $res_doku->status == 1) {
                        
                    } else {
                        throw New \Exception('Order gagal, silahkan coba kembali', 500);
                    }

                } else {

                    $order_payment[] = array(
                        'id_order'              => $id_order->id_order,
                        'id_payment_type'       => $payment['id_payment_type'],
                        'total_payment'         => $payment['total_payment'],
                        'identifier_number'     => $payment['identifier_number'],
                        'number_payment'        => $payment['number_payment'] ? $payment['number_payment'] : 0
                    );

                }

            }
            OrderPayment::insert($order_payment);  

            DB::commit();


            if ($insert_delivery) {
                $httpcode   = 200;
                $status     = 1;
                $data       = ['message_system' => '', 'va_number' => @$va_number ];
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

    //cari trans

    public function paymentBillerFromOrder(Request $request)
    {
        try {

            if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'number_payment'         => 'required'
            ]);
            
            $number_payment = $request->number_payment ? $request->number_payment : 0;
            if ($number_payment) {
                // proses pembayaran ke biller 
               $order_payment = OrderPayment::where('number_payment',$number_payment)->join('order.order_detail', 'order_payment.id_order', '=', 'order_detail.id_order')->first();

               $param_payment_biller = array(
                'billerid' => $order_payment->biller_id, 
                'accountnumber' => $order_payment->account_number,
                'inquiryid' => $order_payment->inquiry_id,
                'amount' => $order_payment->sell_price,
                'billid' => $order_payment->bill_id,
                'sessionid' => 'lorem',
                );

               $payment = (object) RestCurl::exec('POST',env('LINK_FINANCE','https://lentick-api-finance-dev.azurewebsites.net')."/biller/payment", $param_payment_biller);


               $update_status = OrderHeader::where('id_order', $order_payment->id_order)
               ->update([
                    'total_payment'         => $order_payment->sell_price,
                    'id_workflow_status'    => 'ODSTS05',
                    'repayment_date'        => date('Y-m-d H:i:s'),
                ]);

               $update_status = OrderPayment::where('id_order', $order_payment->id_order)
               ->update([
                    'payment_date'         => date('Y-m-d H:i:s')
                ]);
               

               $httpcode   = 200;
               $status     = 1;
               $data       = $payment->data->data;
               $errorMsg   = 'Sukses';
           } else {
            throw New \Exception('Order gagal, silahkan coba kembali', 500);
        }

    } catch(\Exception $e) {
        DB::rollback();
        $status   = 0;
        $httpcode = 400;
        $data     = $e->getMessage();
        $errorMsg = 'Gagal';

    }

    return response()->json(Api::response($status,$errorMsg,$data),$httpcode);
}


}
