<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Validator;
use Illuminate\Hashing\BcryptHasher AS Hash;
use App\Models\Order\Order as OrderModel;
use DB;

use App\Models\Order\Order as Order;
use App\Models\Order\OrderDetail as OrderDetail;
use App\Models\Order\OrderDelivery as OrderDelivery;
use App\Models\Order\OrderPayment as OrderPayment;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\RestCurl;

// use App\Http\Controllers\DokuController as DC;

class OrderHistoryController extends Controller { 

	public function list(Request $request)
	{
		try {
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'offset'          		=> 'required|integer',
				'limit'					=> 'required|integer'
			]);

			$id_user = $request->id_user ? $request->id_user : 0;
			// $res = Order::where('id_user',$id_user)->get();
			//->where('id_user',$id_user)->skip($request->offset)->take($request->limit)
			// $res = Order::join('order.order_payment as a','order.id_order','=','a.id_order')->get();
			// $ress = ($res)->where('id_user',$id_user)->skip($request->offset)->take($request->limit)->get();
			$res = DB::table('order.order')
				// ->join('order_payment','id_order')
            	->join('order.order_payment', 'order.order_payment.id_order', '=', 'order.order.id_order')
                ->offset($request->offset)
                ->limit($request->limit)
                ->select('*')
                ->get();
			// dd($res);

			if($res) {
				$httpcode   = 200;
				$status     = 1;
				$data       = $res;
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
