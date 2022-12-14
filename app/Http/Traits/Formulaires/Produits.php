<?php
namespace App\Http\Traits\Formulaires;

use App\Models\Produit;
/**
 * renvoie la liste des type de champs
 */
trait Produits
{
    public function listeProduits(){

        $listeProduits = Produit::orderBy('r_nom_produit','ASC')->get();
        return $listeProduits;
    }

    public function liste_produits_type_client($idtypeCLient){
        $listeProduits = Produit::orderBy('r_nom_produit','ASC')->where('r_type_client', $idtypeCLient)->get();
        return $listeProduits;
    }
}
