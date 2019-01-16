<?php 

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class Doku extends Model {

    protected $table = 'finance.doku';
    protected $primaryKey = 'id';

    protected $fillable = [
        'transidmerchant',
        'totalamount',
        'words',
        'statustype',
        'response_code',
        'approvalcode',
        'trxstatus',
        'payment_channel',
        'paymentcode',
        'session_id',
        'bank_issuer',
        'creditcard',
        'payment_date_time',
        'verifyid',
        'verifyscore',
        'verifystatus',
        'id_user'
    ];

    public $timestamps = false;
}