<?php 

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model {

    protected $table = 'order.order_payment';
    
    // protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_order_payment',
        'id_order',
        'id_payment_type',
        'total_payment',
        'identifier_number',
        'payment_date',
        'number_payment'
    ];

    public $timestamps = false;

    public function numberPayment()
    {
        return $this->hasMany('App\Models\Order\OrderDetail','id_order','id_order');
    }
}
