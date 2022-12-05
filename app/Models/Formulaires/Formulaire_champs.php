<?php

namespace App\Models\Formulaires;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulaire_champs extends Model
{
    use HasFactory;
    protected $table = 't_formulaire_champs';
    protected $fillable  = [
        'r_formulaire',
        'r_champs',
        'r_rang',
        'r_description',
        'r_status',
        'r_creer_par',
        'r_modifier_par',
        'r_grp_champs',
        'r_es_obligatoire'
    ];
}
