<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convention extends Model
{
    use HasFactory;
    protected $table = 't_conventions';
    protected $fillable  = [
        'id',
        'r_mnt_min',
        'r_mnt_max',
        'r_taux',
        'r_produit',
        'r_entreprise',
        'r_description',
    ];
}
