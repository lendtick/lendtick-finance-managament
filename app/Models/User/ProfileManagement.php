<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class ProfileManagement extends Model {

    protected $table = 'user.user_profile';
    protected $primaryKey = 'id_user_profile';

    protected $fillable = [
        'id_user',
        'id_koperasi',
        'name',
        'personal_identity_path',
        'personal_photo',
        'phone_number',
        'email',
        'npwp',
        'mother_name',
        'domicile_address',
        'id_domicile_address_status',
        'heirs_name',
        'id_heirs_relation_status',
        'id_mariage_status',
        'loan_plafond',
        'microloan_plafond'
    ];

    public $timestamps = false;
}