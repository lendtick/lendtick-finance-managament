<?php 

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $table = 'order.order_billers';
    
    protected $primaryKey = 'order_billers_id';

    protected $fillable = [
        'billers_id',
        'account_number',
        'session_id',
        'additional_data_1',
        'additional_data_2',
        'additional_data_3',
        'status_order',
        'create_date_order',
        'type_payment',
        'id_user',
        'bill_id',
        'latitude',
        'longitude'
    ];

    public $timestamps = false;
}
