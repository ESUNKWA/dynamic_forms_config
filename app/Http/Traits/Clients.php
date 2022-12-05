<?php
namespace App\Http\Traits;

use App\Models\Entreprise;
use App\Models\TypeClient;
/**
 * Cette fontion est un trait qui retour la liste des type de clients
 */
trait Clients
{
    public function list_type_clients(){
        try {

            $typeClients = TypeClient::orderBy('r_libelle', 'ASC')->where('r_status',1)->get();

            return $typeClients;

        } catch (\Throwable $e) {

            return $e->getMessage();

        }
    }

    public function list_clients_entp(){
        try {

            $clientEntp = Entreprise::orderBy('r_nom_entp', 'ASC')->get();

            return $clientEntp;

        } catch (\Throwable $e) {

            return $e->getMessage();

        }
    }
}
