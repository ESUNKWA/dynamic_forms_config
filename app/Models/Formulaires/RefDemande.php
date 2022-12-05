<?php

namespace App\Models\Formulaires;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefDemande extends Model
{
    use HasFactory;

    protected $table = 't_ref_demandes';
    protected $fillable  = [
        'r_reference',
        'r_client',
        'r_produit'
    ];
}
