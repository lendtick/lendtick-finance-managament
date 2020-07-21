<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\Api;
use App\Helpers\Template;
use App\Helpers\BlobStorage;
use App\Helpers\RestCurl;
use App\Models\Order\Order;
use DB;


class ExpiredOrderController extends Controller {
    
    // get list billers
    public function run(Request $request)
    {
        try {
            
            

            // die;

            $data = DB::select(DB::raw("SELECT * from(
                SELECT 
                a.id_order,
                a.billing_date,
                k.id_workflow_status,
                e.id_payment_type
                from [order].[order] a 
                inner join [user].[user] g on a.id_user=g.id_user
                inner join [user].[master_workflow_status] k on a.id_workflow_status = k.id_workflow_status
                left join [user].[user_profile] f on f.id_user = g.id_user
                LEFT JOIN [user].[user_company] h ON h.id_user_profile=f.id_user_profile
                INNER JOIN [user].[master_company] i ON i.id_company=h.id_company
                INNER JOIN [user].[master_grade] j ON j.id_grade=h.id_grade
                left join [order].[order_detail] b on a.id_order = b.id_order
                left join [order].[order_payment] c on b.id_order = c.id_order
                left join [order].[master_payment_type] e on c.id_payment_type = e.id_payment_type
                left join [order].[order_microloan] d on b.id_order = c.id_order
                where a.id_workflow_status = 'ODSTS01'
                -- and e.id_payment_type = 'PAY001'
                group by a.id_order, a.billing_date,k.id_workflow_status,
                e.id_payment_type
                ) oke
                where 1=1
                order by oke.billing_date asc"));
            // check 

            

            foreach($data as $d){
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s',strtotime($d->billing_date)));
                $date->modify('+1 day');
                $dateFinal = $date->format('Y-m-d H:i:s');
                if($dateFinal < date('Y-m-d H:i:s')){
                    // echo 'kadaluarsa';
                    Order::where('id_order',$d->id_order)->update([
                        'id_workflow_status' => 'ODSTS06'
                    ]);
                }
            }

            die;


            $httpcode 	= 200;
            $errorMsg 	= 'Sukses';
            $res = $data; //BillersMaster::where('billers_id',$request->billers_id)->update($update);
            
            // response
            $status   	= 1;
            // $data 		= '';
            
        } catch(\Exception $e) {
            $status   = 0;
            $httpcode = 400;
            $data     = null;
            $errorMsg = $e->getMessage();
        }
        
        return response()->json(Api::response($status,$errorMsg,$data),$httpcode);
        
    }
    
}
