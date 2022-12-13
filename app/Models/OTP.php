<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    use HasFactory;
    protected $table = 't_otp';
    protected $fillable  = [
        "r_email", "r_otp", "r_heure_expiration", "r_otp_valide", "created_at", "updated_at"
    ];
}
