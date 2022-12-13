<?php

namespace App\Models\Formulaires;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeChamps extends Model
{
    use HasFactory;
    protected $table = 't_type_champs';
    protected $fillable  = [
        'r_libelle',
        'r_description',
        'r_creer_par',
        'r_modifier_par'
    ];

    public function t_champs(){
        return $this->hasMany(Champs::class, 'field_type');
    }

}
