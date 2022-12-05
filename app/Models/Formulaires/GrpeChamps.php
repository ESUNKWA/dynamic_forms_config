<?php

namespace App\Models\Formulaires;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrpeChamps extends Model
{
    use HasFactory;
    protected $table = 't_groupe_champs';
    protected $fillable  = [
        'r_nom',
        'r_description',
        'r_creer_par',
        'r_modifier_par'
    ];
}
