<?php 

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class BillerLog extends Model {

    protected $table = 'finance.biller_log';
    // protected $primaryKey = 'SessionID';

    protected $fillable = [
        'biller_log_id',
        'log_biller_param',
        'log_biller_response'
    ];

    public $timestamps = true;
}
