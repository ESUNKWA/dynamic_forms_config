<?php

namespace App\Models\Formulaires;

use App\Models\Produit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formulaires extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 't_formulaires';
    protected $fillable  = [
        'r_nom',
        'r_produit',
        'r_description',
        'r_creer_par',
        'r_modifier_par',
        'r_status',
        'r_rang'
    ];

    public function t_produits(){
        return $this->belongsTo(Produit::class, 'r_produit');
    }
}
