<?php 

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class BillerSession extends Model {

    protected $table = 'finance.biller_session';
    protected $primaryKey = 'SessionID';

    protected $fillable = [
        'SessionID',
        'RequestDate'
    ];

    public $timestamps = false;
}
