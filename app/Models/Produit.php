<?php

namespace App\Models;

use App\Models\Formulaires\Champs;
use App\Models\Formulaires\Formulaires;
use App\Models\Workflows\FormulaireSaisie;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 't_produits';
    protected $fillable  = [
        'id',
        'r_nom_produit',
        'r_description',
        'r_status',
        'r_created_by',
        'r_updated_by',
        'created_at',
        'path_name',
        'r_type_client'
    ];

    //Relationshep

    public function t_champs(){
        return $this->hasMany(Champs::class, 'product');
    }

    public function t_formulaires(){
        return $this->hasMany(Formulaires::class, 'r_produit');
    }

    public function t_formulaire_saisi(){
        return $this->hasMany(FormulaireSaisie::class, 'r_produit');
    }
}
