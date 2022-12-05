<?php

namespace App\Http\Controllers\WS;

use App\Http\Controllers\Controller;
use App\Http\Traits\cryptData;
use App\Http\Traits\SendResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Dash extends Controller
{
    use SendResponses, cryptData;
    /**
    * Cette fonction retour les données pour la génération du tableau de bord au niveau de l'interface du système centralisé
    */
    public function dash_sc(){

        try{
            $dashquery = DB::select("SELECT sc_workflows.f_dashboard_sc()")[0]->f_dashboard_sc;
            $dash      = json_decode($dashquery)->_result[0];
            return $dash;
        }catch(\Exception $e){
            return $e->getMessage();
        }

    }

    public function dashbackofficeweb(){

        try{
            $dashquery = DB::select("SELECT sc_workflows.f_dashboard_back_web()")[0]->f_dashboard_back_web;
            $dash      = json_decode($dashquery);
            return $this->crypt($dash);
        }catch(\Exception $e){
            return $e->getMessage();
        }

    }
}
