<?php

namespace App\Models\Workflows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau_validation extends Model
{
    use HasFactory;
    protected $table = 't_niveau_validations';
    protected $fillable = [
        'r_task',
        'r_utilisateur',
        'r_nom_niveau',
        'r_rang'
    ];
}
