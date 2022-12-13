<?php
namespace App\Http\Traits\Formulaires;

use App\Models\Formulaires\Formulaires as Forms;
/**
 * renvoie la liste des type de champs
 */
trait Formulaires
{
    public function formulaires(){
        $formulaires = Forms::orderBy('r_nom','ASC')->get();
        return $formulaires;
    }

    public function list_formulaire_by_product(int $idproduit){

        $forms = Forms::select('t_formulaires.*','t_niveaux_formulaires.r_nom_niveau')
                            ->where('r_produit', $idproduit)
                            ->join('t_niveaux_formulaires', 't_niveaux_formulaires.r_formulaire', '=', 't_formulaires.id')
                            ->orderBy('id', 'ASC')
                            ->get();
        return $forms;
    }
}
