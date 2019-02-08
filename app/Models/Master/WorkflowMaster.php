<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class WorkflowMaster extends Model {

    protected $table = 'user.master_workflow_status';
    // protected $primaryKey = 'id_workflow_status';

    protected $fillable = [
        'id_workflow_status',
        'workflow_status_name',
        'workflow_status_desc',
    ];

    public $timestamps = false;
}