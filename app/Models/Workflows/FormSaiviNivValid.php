<?php

namespace App\Models\Workflows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSaiviNivValid extends Model
{
    use HasFactory;

    protected $table = 't_forms_saivi_niv_val';

    protected $fillable = [
        'r_formulaire_saisir',
        'r_niveau_formulaire',
        'r_niveau_validation'
    ];
}
