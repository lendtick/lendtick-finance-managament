<?php 

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class DokuBiller extends Model {

    protected $table = 'finance.doku_biller';
    protected $primaryKey = 'doku_biller_id';

    protected $fillable = [
        'doku_biller_id',
        'session_id',
        'request_date_time',
        'words',
        'biller_id',
        'account_number',
        'systrace',
        'inquiry_id'
    ];

    public $timestamps = true;
}
