<?php
namespace App\Http\Traits\Formulaires;

use App\Models\Formulaires\TypeChamps;
/**
 * renvoie la liste des type de champs
 */
trait TypesChamps
{
    public function listeTypeChamps(){
        $listeTypeChps = TypeChamps::orderBy('r_libelle','ASC')->get();
        return $listeTypeChps;
    }
}
