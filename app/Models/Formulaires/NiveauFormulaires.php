<?php

namespace App\Models\Formulaires;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NiveauFormulaires extends Model
{
    use HasFactory;
    protected $table = 't_niveaux_formulaires';
    protected $fillable  = [
        'r_nom_niveau',
        'r_formulaire',
        'r_formulaire_saisi',
        'r_niveau_validation'
    ];
}
