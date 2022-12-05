<?php

namespace App\Models\permissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersRoles extends Model
{
    use HasFactory;

    protected $table = 'users_roles';
    protected $fillable = [
        'user_id',
        'role_id'
    ];

}
