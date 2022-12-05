<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entreprise extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 't_entreprises';
    protected $fillable  = [
        "r_represantant",
        "r_nom_entp",
        "r_rccm",
        "r_sigle",
        "r_raison_social",
        "r_numero_contribuable",
        "r_numero_social",
        "r_email_entp",
        "r_contact_entp",
        "r_adresse_postale",
        "r_adresse_geo",
        "r_indicatif_pays",
        "r_procuration_url",
        "r_status",
        "r_description",
        "r_forme_juridique",
        "created_at",
        "updated_at",
        "r_domain_email"
    ];
}
