<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocProduit extends Model
{
    use HasFactory;

    protected $table = 't_produit_docments';
    protected $fillable  = [
        "r_libelle",
        "r_description",
        "r_produit",
        "document_path"
    ];
}
