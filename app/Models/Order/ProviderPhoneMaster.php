<?php 

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class ProviderPhoneMaster extends Model {

    protected $table = 'order.master_provider_phone';
    
    protected $primaryKey = 'master_provider_phone_id';


    protected $fillable = [
        'provider_phone_name',
        'provider_phone_number',
        'billers_id_pulsa',
        'provider_phone_image',
        'provider_phone_code',
        'billers_id_paketdata',
        'status'
    ];

    public $timestamps = false;
}
