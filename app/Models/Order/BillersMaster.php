<?php 

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class BillersMaster extends Model {

    protected $table = 'order.master_billers';
    // protected $primaryKey = 'id_workflow_status';

    protected $fillable = [
        'billers_id',
        'billers_name',
        'status',
    ];

    public $timestamps = false;
}
