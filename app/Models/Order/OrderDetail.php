<?php 

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model {

    protected $table = 'order.order_detail';
    
    // protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_order_detail',
        'id_order',
        'id_channel',
        'category_id',
        'product_id',
        'product_name',
        'product_image_path',
        'biller_id',
        'biller_name',
        'bill_id',
        'bill_details',
        'quantity',
        'id_channel',
        'sell_price',
        'base_price',
        'product_details',
        'additional_data_1',
        'additional_data_2',
        'additional_data_3',
        'inquiry_id',
        'account_number'
    ];

    public $timestamps = false;

    
}
