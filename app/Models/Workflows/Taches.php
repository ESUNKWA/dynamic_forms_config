<?php

namespace App\Models\Workflows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taches extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $fillable = [
        'workflow_id',
        'name',
        'conditions',
        'r_creer_par',
        'r_formulaire',
        'r_rang'
    ];
}
