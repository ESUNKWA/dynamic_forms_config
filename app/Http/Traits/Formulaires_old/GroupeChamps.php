<?php
namespace App\Http\Traits\Formulaires;

use App\Models\Formulaires\TypeChamps;
use App\Models\Formulaires\GrpeChamps;
/**
 * renvoie la liste des type de champs
 */
trait GroupeChamps
{
    public function listeGrpeChamps(){
        $grpChamps = GrpeChamps::orderBy('r_nom','ASC')->get();
        return $grpChamps;
    }
}
