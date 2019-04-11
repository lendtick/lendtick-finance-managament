<?php 

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $table = 'order.order';
    
    // protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_order',
        'id_user',
        'billing_number',
        'billing_date',
        'total_billing',
        'total_payment',
        'id_workflow_status',
        'repayment_date',
        'id_user_company'
    ];

    public $timestamps = false;
}
