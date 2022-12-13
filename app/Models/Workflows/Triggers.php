<?php

namespace App\Models\Workflows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Triggers extends Model
{
    use HasFactory;
    protected $table = 'triggers';
    protected $fillable = [
        'workflow_id',
        'name',
        'r_client',
        'r_formulaire_saisi',
        'r_status',
        'r_reference'
    ];
}
