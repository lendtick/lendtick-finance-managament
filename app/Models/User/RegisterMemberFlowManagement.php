<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class RegisterMemberFlowManagement extends Model {

    protected $table = 'user.register_member_flow';
    protected $primaryKey = 'id_register_member_flow';

    protected $fillable = [
        'id_user',
        'approve_by',
        'appprove_at',
        'level',
        'id_master_register_member_flow'
    ];

    public $timestamps = false;
}