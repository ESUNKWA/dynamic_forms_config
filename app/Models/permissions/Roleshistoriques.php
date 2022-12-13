<?php

namespace App\Models\permissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roleshistoriques extends Model
{
    use HasFactory;
    protected $table = 't_historiques_users_roles';
    protected $fillable = [
        'r_utilisateur',
        'r_role',
        'r_date_debut',
        'r_date_fin',
        'r_description',
        'r_creer_par'
    ];
}
