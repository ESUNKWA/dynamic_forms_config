<?php

namespace App\Models\Workflows;

use App\Models\Produit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormulaireSaisie extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 't_formulaire_saisi';

    protected $fillable = [
        'id',
        'r_formulaire',
        'r_produit',
        'r_client',
        'r_description',
        'r_rang',
        'r_status',
        'r_validate',
        'r_reference'
    ];

    //Relationsheep
    public function t_produit(){
        return $this->belongsTo(Produit::class, 'r_produit');
    }
}
