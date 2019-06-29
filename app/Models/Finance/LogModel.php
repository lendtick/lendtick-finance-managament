<?php 

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class LogModel extends Model {

    protected $table = 'finance.logdokuva';
    protected $primaryKey = 'idlog';

    protected $fillable = [
        'idlog',
        'value'
    ];

    public $timestamps = true;
}


