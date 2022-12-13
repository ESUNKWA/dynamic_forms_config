<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeClient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_type_clients';
    protected $fillable  = [
        'r_code',
        'r_libelle',
        'r_description',
        'r_creer_par',
        'r_modifier_par',
        'r_status'
    ];

    /**
     * Obtenir les clients pour un type
     */
    public function t_clients(){
        $this->hasMany(Client::class);
    }
}
