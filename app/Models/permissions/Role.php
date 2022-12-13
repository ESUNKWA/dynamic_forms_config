<?php

namespace App\Models\permissions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    public function permissions() {

        return $this->belongsToMany(Permission::class,'roles_permissions');

     }

     public function role() {

        return $this->belongsToMany(User::class,'users_roles');

     }
}
