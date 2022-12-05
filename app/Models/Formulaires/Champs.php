<?php

namespace App\Models\Formulaires;

use App\Models\Produit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Champs extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 't_champs';
    protected $fillable  = [
        'field_name',
        'field_label',
        'field_value',
        'field_placeholder',

        'field_options',
        'product',
        'r_creer_par',
        'r_modifier_par',
        'length',
        'value_min',

        'value_max',
        'description',
        'field_type',
        'r_status'
    ];

    //Relationsheep

    public function t_produits(){
        return $this->belongsTo(Produit::class,'r_product');
    }

    public function t_type_champs(){
        return $this->belongsTo(TypeChamps::class, 'field_type');
    }
}

