<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEntreprise extends Model
{
    use HasFactory;
    protected $table = 't_client_entreprises';
    protected $fillable = [
        "r_entreprise",
        "r_client"
    ];
}
