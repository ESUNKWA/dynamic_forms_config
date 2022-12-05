<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 't_clients';
    protected $fillable = [
        "r_type_client",
        "r_nom",
        "r_prenoms",
        "r_telephone",
        "r_email",
        "r_description",
        "r_creer_par",
        "r_modifier_par",
        "created_at",
        "updated_at",
        "r_password_change",
        "r_password",
    ];


    public function t_type_clients(){
        $this->belongsTo(TypeClient::class,'r_type_client');
    }

}
