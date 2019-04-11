<?php 

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model {

    protected $table = 'order.order_delivery';
    
    // protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_order_delivery',
        'id_order',
        'id_delivery_type',
        'delivery_details'
    ];

    public $timestamps = false;
}
