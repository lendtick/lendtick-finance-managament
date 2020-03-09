<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Finance\PaymentDokuRepo AS PaymentRepo;
use App\Helpers\Api;

class PaymentUsersRegisterController extends Controller
{

    public function list(Request $r){
        $this->validate($r, ['start' => 'required', 'length' => 'required', 'sort' => 'required']);
        
        try {
        
            $start = $r->start;
            $length = $r->length;
            $sort = $r->sort;
            $data = $r->all();
            $where = isset($data['filter'])?$r->filter:[];
            $manual_where = isset($data['manual_filter'])?json_decode($r->manual_filter):[];
            return response()->json(Api::response(true,'success',PaymentRepo::list($r, $where, $start, $length, $sort, $manual_where)),200);
        
        } catch(Exception $e){

            return response()->json(Api::response(true,$e->getMessage()),400);

            // return response()->json(Api::response(true,'parameter salah'),400);

        }
    }
}
