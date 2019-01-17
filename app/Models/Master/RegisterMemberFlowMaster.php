<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class RegisterMemberFlowMaster extends Model {

    protected $table = 'user.master_register_member_flow';
    protected $primaryKey = 'id_master_register_member_flow';

    protected $fillable = [
        'id_role_master',
        'level',
        'send_email_to_role',
        'send_email_to_member',
        'authorization_company',
        'set_workflow_status_code'
    ];

    public $timestamps = false;
}