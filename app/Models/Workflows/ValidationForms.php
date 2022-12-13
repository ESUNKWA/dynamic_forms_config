<?php

namespace App\Models\Workflows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationForms extends Model
{
    use HasFactory;
    protected $table = 't_validations_forms';
    protected $fillable = [
        'r_formulaire_saisi',
        'r_formulaire',
        'r_client',
        'r_niveau_validation',
        'r_status',
        'r_commentaire',
        'r_validateur',
        'r_reference'
    ];
}
