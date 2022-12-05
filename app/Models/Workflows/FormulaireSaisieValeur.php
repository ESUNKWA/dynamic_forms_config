<?php

namespace App\Models\Workflows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulaireSaisieValeur extends Model
{
    use HasFactory;
    protected $table = 't_formulaire_saisi_valeur';
    protected $fillable = [
        'r_formulaire_saisi',
        'r_champs',
        'r_valeur'
    ];
}
