<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class PaymentTypeModel extends Model {

    protected $table = 'order.master_payment_type';
    // protected $primaryKey = 'id_master_register_member_flow';

    protected $fillable = [
        'id_payment_type',
        'name_payment_type'
    ];

    public $timestamps = false;
}
