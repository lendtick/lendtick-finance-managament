<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserManagement extends Model {

    protected $table = 'user.user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'password',
        'id_role_master',
        'id_workflow_status',
        'android_device_token',
        'ios_device_token',
        'is_new_user',
        'last_login',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public $timestamps = true;
}