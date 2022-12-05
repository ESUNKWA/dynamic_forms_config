<?php

namespace App\Http\Controllers\WS\Workflows;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendNotifyController;
use App\Http\Traits\Workflows\FormsWorkflows;
use App\Models\c;
use App\Models\Workflows\FormulaireSaisie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Workflows\FormulaireSaisieValeur;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\SendResponses;
use App\Http\Traits\cryptData;
use App\Models\Formulaires\NiveauFormulaires;
use App\Models\Formulaires\RefDemande;
use App\Models\Workflow;

use App\Models\Workflows\Triggers;
use App\Http\Controllers\WS\filesController;
use App\Models\Workflows\ValidationForms;

class FormulaireSaisieController extends Controller
{
    use SendResponses, cryptData, FormsWorkflows;

    public $refDmde                                                         = null;
    public $idref                                                           = null;

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        //
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        //
    }

    /**
    * Saisie de formulaire provenant d'un cleint
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {


        //$inputs                                                           = $this->decryptData($request->p_data);
        //return $inputs;
        $inputs                                                             = json_decode($request->data);

        //$rangValidateur                                                   = $this->get_validateur_tache($inputs['r_formulaire']);
        //return $inputs;

        /*  $errors                                                          = [
            'r_formulaire'                                                  => 'required',
            'r_produit'                                                     => 'required',
            'r_client'                                                      => 'required',
            'r_rang'                                                        => 'required'
        ];
        $erreurs                                                            = [
            'r_formulaire.required'                                         => 'Formulaire non définie',
            'r_produit.required'                                            => 'Produit non définie',
            'r_client.required'                                             => 'Client non reconnu',
            'r_client.r_rang'                                               => 'Rang non reconnu',
        ]; */
        // controlle des champs
        //$validation                                                       = Validator::make($inputs, $errors, $erreurs);

        //if( !$validation->fails() ){

            DB::beginTransaction();

            try {

                if ( $inputs->r_rang                                        == 1) {

                    //Génération de la référence de la damande
                    $ref                                                    = 'ref/'.date('Y').'/'.date('m').'/'.date('d').'/'.time();

                    //Enregistrement de la reférence de la demande: (Une demande <=> un ensemble de soumission de formulaire)
                    $this->refDmde                                          = RefDemande::create([
                        'r_reference'                                       => $ref,
                        'r_client'                                          => $inputs->r_client,
                        'r_produit'                                          => $inputs->r_produit
                    ]);

                    $this->idref                                            = $this->refDmde->id;

                    //Enregistrement dans le déclencheur
                    $workflow                                               = Workflow::where('r_produit', $inputs->r_produit)->first();

                    Triggers::create([
                        'workflow_id'                                       => $workflow->id,
                        'r_client'                                          => $inputs->r_client,
                        'name'                                              => 'Workflow déclenché',
                        'r_reference'                                       => $this->refDmde->id ,
                    ]);

                }else{

                    //Récupération de la référence si le formulaire dans la bd
                    $this->refDmde                                          =  Triggers::select('r_reference')
                    ->where('r_client', $inputs->r_client)
                    ->where('r_status', 0)
                    ->orderBy('id', 'DESC')
                    ->limit(1)
                    ->first();

                    $this->idref                                            = $this->refDmde->r_reference;
                }

                // Enregistrement de formulaire saisie
                $insertion                                                  = FormulaireSaisie::create([
                    'r_formulaire'                                          => $inputs->r_formulaire,
                    'r_produit'                                             => $inputs->r_produit,
                    'r_client'                                              => $inputs->r_client,
                    'r_rang'                                                => $inputs->r_rang,
                    'r_reference'                                           => $this->idref
                ]);


                if( $insertion->id ){
                    $tabvaleurs = [];
                    $tabFiles = [];

                    foreach ($inputs->valeur_champs as $value) {
                        // Récupération des clés de chaque objet
                        $keys                                               = array_keys(json_decode(json_encode($value), true));

                        if( in_array('r_fichier', $keys) ){
                            array_push($tabFiles, $value);
                        }else{
                            array_push($tabvaleurs, $value);
                        }

                    }

                    //Enregistrement des données saisie par le client
                    foreach ($tabvaleurs as $value) {
                        FormulaireSaisieValeur::create([
                            'r_formulaire_saisi'                        => $insertion->id,
                            'r_champs'                                  => $value->idchamps,
                            'r_valeur'                                  => $value->valeur,
                        ]);
                    }

                    //Enregistrement des fichiers
                    if( isset($request->r_fichier) ){
                        if ( count($request->r_fichier) !== 0 && ( count($request->r_fichier) == count($tabFiles) )) {

                            foreach ( $request->r_fichier as $keys => $fichier) {

                                FormulaireSaisieValeur::create([
                                    'r_formulaire_saisi'                    => $insertion->id,
                                    'r_champs'                              =>  $tabFiles[$keys]->idchamps, //$value->idchamps,
                                    'r_valeur'                              => url('/').'/storage/images/docs_clients/'.$fichier->getClientOriginalName()
                                ]);
                                $image                                                = $fichier->storeAs(
                                    'images/docs_clients',
                                    $fichier->getClientOriginalName(),
                                    'public'
                                );


                            }

                        }
                    }


                    //return $test;
                    DB::commit();

                    //Envoi de mail---------------------------------------------------------------------------------------------------
                    try {

                        if ( $inputs->r_rang                              == 1 ) {

                            //Récupération du mail du premier validateur
                            $rangValidateur                                 = $this->get_first_validateur_par_wkfl($inputs->r_produit);

                            //Envoi du mail au validateur concerné

                            $data                                           = [
                                'titre'                                     => 'Notification workflow',
                                'message'                                   =>'Vous avez une validation en attente, veuillez vous connecter à l\'application de gestion des workflows',
                                'email'                                     => $rangValidateur->email];

                                $sendMail                                   = new SendNotifyController();
                                $sendMail->sendMessageGoogle( new Request($data) );

                            //$sendMail->sendMessageGoogle(['message'=>'Vous avez une validation en attente, veuillez vous connecter à l\'application de gestion des workflows'], $rangValidateur->email);

                            }else{

                                //Récupération du rang du validateur
                                $validateurtache                                = $this->get_validateur_tache($inputs->r_formulaire);

                                //Envoi du mail au validateur concerné
                                $req                                            = DB::select('SELECT sc_workflows.f_validateur_par_wkf(?)', [json_encode(['idproduit'=>$inputs->r_produit])]);
                                $retourServeur                                  = json_decode($req[0]->f_validateur_par_wkf);
                                $validations                                    = $retourServeur->_result[0]->workflow_task;

                                foreach ($validations as $value) {

                                    if( $value->validateur_rang                 == $validateurtache->r_rang + 1 ){
                                        $value->validateur_email;

                                        $sendMail                               = new SendNotifyController();
                                        $data                                   = [
                                            'titre'                             => 'Notification workflow',
                                            'message'                           =>'Vous avez une validation en attente, veuillez vous connecter à l\'application de gestion des workflows',
                                            'email'                             => $value->validateur_email];
                                        }

                                    }
                                }

                            } catch (\Throwable $e) {
                                //return $e->getMessage();
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }
                            //Envoi de mail---------------------------------------------------------------------------------------------------

                            $response                                               = $this->crypt($this->responseSuccess('Enregistrement effectué avec succès'));
                            return  $response;

                        }else{
                            $response                                               = $this->crypt($this->responseValidation('Une erreur interne est survenue'));
                            return $response;
                        }


                    } catch (\Throwable $e) {
                        DB::rollBack();
                        //return $this->responseCatchError($e->getMessage());
                        return $this->crypt($this->responseCatchError($e->getMessage()));

                    }


                    //}else{
                        //return $this->responseValidation('Avertissement lié au paramètres, voir le détails',$validation->errors());
                        //return $this->crypt($this->responseValidation('Avertissement lié au paramètres, voir le détails',$validation->errors()));
                        //}

}

                    /**
                    * Liste des clients ayant faire une soumission
                    */
                    public function clients_submits_forms(Request $request){

                        //$datas                                                            = $this->crypt($request->all()); //Ceci est pour mes tests
                        //return $datas;

                        //Décryptage des données récues
                        $inputs                                                             = $this->decryptData($request->p_data);
                        //return $inputs;

                        $errors                                                             = [
                            'idproduit'                                                     => 'required'
                        ];
                        $erreurs                                                            = [
                            'idproduit.required'                                            => 'Produit non définie',
                        ];
                        // controlle des champs
                        $validation                                                         = Validator::make($inputs, $errors, $erreurs);

                        if( !$validation->fails() ){

                            try {
                                $req                                                        = DB::select('SELECT sc_workflows.f_get_clients_submits_forms(?)', [json_encode($inputs)]);
                                $retourServeur                                              = json_decode($req[0]->f_get_clients_submits_forms);
                                return $this->crypt($retourServeur);
                            } catch (\Throwable $e) {
                                return $e->getMessage();
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }

                        }else{
                            return $this->crypt($this->responseValidation('Erreur lié au paramètres d\'envoi', $validation->errors()));
                        }

                    }

                    /**
                    * Liste des clients ayant faire une soumission
                    */
                    public function get_data_submit(Request $request){

                        //$datas                                                            = $this->crypt($request->all()); //Ceci est pour mes tests
                        //return $datas;

                        //Décryptage des données récues
                        $inputs                                                             =  $this->decryptData($request->p_data);
                        //return $inputs;

                        $errors                                                             = [
                            //'r_formulaire'                                                => 'required',
                            'r_client'                                                      => 'required'
                        ];
                        $erreurs                                                            = [
                            //'r_formulaire.required'                                       => 'Formulaire non définie',
                            'r_client.required'                                             => 'Client non définie',
                        ];
                        // controlle des champs
                        $validation                                                         = Validator::make($inputs, $errors, $erreurs);

                        if( !$validation->fails() ){

                            try {

                                if( $inputs['r_canal_cnx'] !== 3 ){
                                    // cas backoffice
                                    $req                                                    = DB::select('SELECT sc_workflows.f_get_data_submit_by_client_ad(?)', [json_encode($inputs)]);
                                    $retourServeur                                          = json_decode($req[0]->f_get_data_submit_by_client_ad);
                                }else{

                                    // cas client
                                    $req                                                    = DB::select('SELECT sc_workflows.f_get_data_submit_by_client_cli(?)', [json_encode($inputs)]);
                                    $retourServeur                                          = json_decode($req[0]->f_get_data_submit_by_client_cli);
                                }

                                return $this->crypt($retourServeur);

                            } catch (\Throwable $e) {
                                //return $e->getMessage();
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }

                        }else{

                            return $this->crypt($this->responseValidation('Erreur lié au paramètres d\'envoi', $validation->errors()));
                        }

                    }


                    public function demande_en_cours(Request $request){

                        //$datas                                                            = $this->crypt($request->all()); //Ceci est pour mes tests
                        //return $datas;

                        //Décryptage des données récues
                        $inputs                                                             =  $this->decryptData($request->p_data);

                        //return $inputs;

                        $errors                                                             = [
                            //'r_formulaire'                                                => 'required',
                            'idproduit'                                                      => 'required'
                        ];
                        $erreurs                                                            = [
                            //'r_formulaire.required'                                       => 'Formulaire non définie',
                            'idproduit.required'                                             => 'Produit non définie',
                        ];
                        // controlle des champs
                        $validation                                                         = Validator::make($inputs, $errors, $erreurs);

                        if( !$validation->fails() ){

                            try {

                                $req                                                    = DB::select('SELECT sc_workflows.f_demande_en_cours_consult(?)', [json_encode($inputs)]);
                                $retourServeur                                          = json_decode($req[0]->f_demande_en_cours_consult);

                                return $this->crypt($retourServeur);

                            } catch (\Throwable $e) {
                                //return $e->getMessage();
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }

                        }else{

                            return $this->crypt($this->responseValidation('Erreur lié au paramètres d\'envoi', $validation->errors()));
                        }

                    }

                    public function list_soumission_precedent_par_demande(Request $request){

                        //$datas                                                            = $this->crypt($request->all()); //Ceci est pour mes tests
                        //return $datas;

                        //Décryptage des données récues
                        $inputs                                                             =  $this->decryptData($request->p_data);
                        //return $inputs;

                        $errors                                                             = [
                            'r_reference'                                                => 'required',
                            'r_client'                                                      => 'required',
                            'r_produit'                                                      => 'required'
                        ];
                        $erreurs                                                            = [
                            'r_reference.required'                                       => 'Référence non définie',
                            'r_client.required'                                             => 'Client non définie',
                            'r_produit.required'                                             => 'Produit non définie'
                        ];
                        // controlle des champs
                        $validation                                                         = Validator::make($inputs, $errors, $erreurs);

                        if( !$validation->fails() ){

                            try {

                                $req                                                    = DB::select('SELECT sc_workflows.f_list_soumission_precedent_par_demande(?)', [json_encode($inputs)]);
                                $retourServeur                                          = json_decode($req[0]->f_list_soumission_precedent_par_demande);

                                return $this->crypt($retourServeur);

                            } catch (\Throwable $e) {
                                //return $e->getMessage();
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }

                        }else{

                            return $this->crypt($this->responseValidation('Erreur lié au paramètres d\'envoi', $validation->errors()));
                        }

                    }

                    public function get_first_forms_client_by_product(Request $request){

                        //$datas                                                            = $this->crypt($request->all()); //Ceci est pour mes tests
                        //return $datas;

                        //Décryptage des données récues
                        $inputs                                                             = $this->decryptData($request->p_data);
                        //return $inputs;

                        $errors                                                             = [
                            'idproduit'                                                     => 'required',
                        ];
                        $erreurs                                                            = [
                            'idproduit.required'                                            => 'Produit non définie',
                        ];
                        // controlle des champs
                        $validation                                                         = Validator::make($inputs, $errors, $erreurs);

                        if( !$validation->fails() ){

                            try {
                                $req                                                        = DB::select('SELECT sc_workflows.f_get_first_forms_client_by_product(?)', [json_encode($inputs)]);
                                $retourServeur                                              = $req[0]->f_get_first_forms_client_by_product;
                                return $this->crypt($retourServeur);
                            } catch (\Throwable $e) {
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }



                        }else{

                            return $this->crypt($this->responseValidation('Erreur lié au paramètres d\'envoi', $validation->errors()));
                        }

                    }

                    public function f_produits_demandes(Request $request){

                        //$datas                                                            = $this->crypt($request->all()); //Ceci est pour mes tests
                        //return $datas;

                        //Décryptage des données récues
                        $inputs                                                             = $this->decryptData($request->p_data);
                        //return $inputs;

                        $errors                                                             = [
                            //'r_formulaire'                                                => 'required',
                            'r_client'                                                      => 'required'
                        ];
                        $erreurs                                                            = [
                            //'r_formulaire.required'                                       => 'Formulaire non définie',
                            'r_client.required'                                             => 'Client non définie',
                        ];
                        // controlle des champs
                        $validation                                                         = Validator::make($inputs, $errors, $erreurs);

                        if( !$validation->fails() ){

                            try {

                                // $req                                                        = DB::select('SELECT sc_workflows.f_produits_demandes(?)', [json_encode($inputs)]);
                                // $retourServeur                                              = json_decode($req[0]->f_produits_demandes);

                                $retourServeur = DB::select(
                                    "WITH cte_fmsi AS (
                                        select fmsi.*, prd.id as produit_id, fm.r_nom from sc_workflows.t_formulaire_saisi fmsi
                                        inner join sc_workflows.t_formulaires fm on fm.id = fmsi.r_formulaire
                                        inner join sc_workflows.t_produits prd on prd.id = fm.r_produit
                                        where fmsi.r_client = ? and fmsi.deleted_at is null
                                        )

                                        select
                                        pdt.id as product_id, pdt.r_nom_produit as product_name, pdt.r_description as product_description,
                                        pdt.path_name as product_image, rfd.r_reference, rfd.id as id_reference, tg.r_status,
                                        ( select array_to_json(array(
                                            select json_build_object('formulaire_id',r_formulaire,'formulaire_saisi_id',id, 'produit_id',
                                                                     produit_id, 'formulaire_nom', r_nom,'_form_saisi_status', r_validate
                                            )
                                            from cte_fmsi where r_reference = rfd.id )) as forms_saisi)
                                            from sc_workflows.t_produits pdt
                                            inner join sc_workflows.t_ref_demandes rfd on pdt.id = rfd.r_produit
                                            inner join sc_workflows.triggers tg on rfd.id = tg.r_reference
                                            where tg.r_client = ? and pdt.id in ( select produit_id from cte_fmsi )", [$inputs['r_client'],$inputs['r_client']]
                                        );

                                        return $this->crypt($this->responseSuccess('Liste des produits faisant l\'objet d\'une demande par client',$retourServeur));

                                    } catch (\Throwable $e) {
                                        return $e->getMessage();
                                        return $this->crypt($this->responseCatchError($e->getMessage()));
                                    }

                                }else{

                                    return $this->crypt($this->responseValidation('Erreur lié au paramètres d\'envoi', $validation->errors()));
                                }

                            }


                            /**
                            * Display the specified resource.
                            *
                            * @param  \App\Models\c  $c
                            * @return \Illuminate\Http\Response
                            */
                            public function show(c $c)
                            {
                                //
                            }

                            /**
                            * Show the form for editing the specified resource.
                            *
                            * @param  \App\Models\c  $c
                            * @return \Illuminate\Http\Response
                            */
                            public function edit(c $c)
                            {
                                //
                            }

                            /**
                            * Update the specified resource in storage.
                            *
                            * @param  \Illuminate\Http\Request  $request
                            * @param  \App\Models\c  $c
                            * @return \Illuminate\Http\Response
                            */
                            public function updatedemande(Request $request)
                            {
                                //$datas                                                            = $this->crypt($request->all()); //Ceci est pour mes tests
                                //return $datas;

                                //Décryptage des données récues

                                //$inputs                                                           = $this->decryptData($request->p_data);
                                $inputs                                                             = json_decode($request->dataModif);
                                $idformulaire_saisi = $inputs->idformulaire_saisi;
                                //$inputs                                                             = json_decode($inputs);

                                //$rangValidateur                                                   = $this->get_validateur_tache($inputs['r_formulaire']);
                                //return $rangValidateur;

                                /*  $errors                                                          = [
                                    'r_formulaire'                                                  => 'required',
                                    'r_produit'                                                     => 'required',
                                    'r_client'                                                      => 'required',
                                    'r_rang'                                                        => 'required'
                                ];
                                $erreurs                                                            = [
                                    'r_formulaire.required'                                         => 'Formulaire non définie',
                                    'r_produit.required'                                            => 'Produit non définie',
                                    'r_client.required'                                             => 'Client non reconnu',
                                    'r_client.r_rang'                                               => 'Rang non reconnu',
                                ]; */
                                // controlle des champs
                                //$validation                                                       = Validator::make($inputs, $errors, $erreurs);

                                //if( !$validation->fails() ){

                                    DB::beginTransaction();

                                    try {


                                        if( $idformulaire_saisi){
                                            $tabvaleurs = [];
                                            $tabFiles = [];

                                            foreach ($inputs->valeur_champs as $value) {
                                                // Récupération des clés de chaque objet
                                                $keys                                               = array_keys(json_decode(json_encode($value), true));

                                                if( in_array('r_fichier', $keys) ){
                                                    array_push($tabFiles, $value);
                                                }else{
                                                    array_push($tabvaleurs, $value);
                                                }

                                            }



                                            //Modification des données dans formulaire_saisi
                                            $checkfs = FormulaireSaisie::find($idformulaire_saisi);
                                            $checkfs->update([
                                                'r_status' => 1
                                            ]);

                                            $checktriggers = Triggers::where('r_reference',$checkfs->r_reference)->first();
                                            $checktriggers->update([
                                                'r_status' => 0
                                            ]);

                                            if( $checkfs->r_status == 1 && $checktriggers->r_status == 0 ){

                                                //Supression des valeurs
                                                $deleteValue = DB::select("DELETE FROM sc_workflows.t_formulaire_saisi_valeur WHERE r_formulaire_saisi = ?", [$idformulaire_saisi]);

                                                //Enregistrement des données saisie par le client
                                                foreach ($tabvaleurs as $value) {
                                                    FormulaireSaisieValeur::create([
                                                        'r_formulaire_saisi'                        => $idformulaire_saisi,
                                                        'r_champs'                                  => $value->idchamps,
                                                        'r_valeur'                                  => $value->valeur,
                                                    ]);
                                                }

                                            }

                                            //Enregistrement des fichiers

                                            if( isset($request->r_fichier) ){
                                                if ( count($request->r_fichier) !== 0 && ( count($request->r_fichier) == count($tabFiles) )) {

                                                    foreach ( $request->r_fichier as $keys => $fichier) {

                                                        FormulaireSaisieValeur::create([
                                                            'r_formulaire_saisi'                    => $idformulaire_saisi,
                                                            'r_champs'                              =>  $tabFiles[$keys]->idchamps, //$value->idchamps,
                                                            'r_valeur'                              => url('/').'/storage/images/docs_clients/'.$fichier->getClientOriginalName()
                                                        ]);
                                                        $image                                                = $fichier->storeAs(
                                                            'images/docs_clients',
                                                            $fichier->getClientOriginalName(),
                                                            'public'
                                                        );


                                                    }

                                                }
                                            }



                                            //return $test;
                                            DB::commit();

                                            //Envoi de mail---------------------------------------------------------------------------------------------------
                                            try {

                                                if ( $inputs->r_rang                              == 1 ) {

                                                    //Récupération du mail du premier validateur
                                                    $rangValidateur                                 = $this->get_first_validateur_par_wkfl($inputs->r_produit);

                                                    //Envoi du mail au validateur concerné

                                                    $data                                           = [
                                                        'titre'                                     => 'Notification workflow',
                                                        'message'                                   =>'Vous avez une validation en attente, veuillez vous connecter à l\'application de gestion des workflows',
                                                        'email'                                     => $rangValidateur->email];

                                                        $sendMail                                   = new SendNotifyController();
                                                        $sendMail->sendMessageGoogle( new Request($data) );

                                                        //$sendMail->sendMessageGoogle(['message'=>'Vous avez une validation en attente, veuillez vous connecter à l\'application de gestion des workflows'], $rangValidateur->email);

                                                    }else{

                                                        //Récupération du rang du validateur
                                                        $validateurtache                                = $this->get_validateur_tache($inputs->r_formulaire);

                                                        //Envoi du mail au validateur concerné
                                                        $req                                            = DB::select('SELECT sc_workflows.f_validateur_par_wkf(?)', [json_encode(['idproduit'=>$inputs->r_produit])]);
                                                        $retourServeur                                  = json_decode($req[0]->f_validateur_par_wkf);
                                                        $validations                                    = $retourServeur->_result[0]->workflow_task;

                                                        foreach ($validations as $value) {

                                                            if( $value->validateur_rang                 == $validateurtache->r_rang + 1 ){
                                                                $value->validateur_email;

                                                                $sendMail                               = new SendNotifyController();
                                                                $data                                   = [
                                                                    'titre'                             => 'Notification workflow',
                                                                    'message'                           =>'Vous avez une validation en attente, veuillez vous connecter à l\'application de gestion des workflows',
                                                                    'email'                             => $value->validateur_email];
                                                                }

                                                            }
                                                        }

                                                    } catch (\Throwable $e) {
                                                        //return $e->getMessage();
                                                        return $this->crypt($this->responseCatchError($e->getMessage()));
                                                    }
                                                    //Envoi de mail---------------------------------------------------------------------------------------------------

                                                    $response                                               = $this->crypt($this->responseSuccess('Modification effectué avec succès'));
                                                    return  $response;

                                                }else{
                                                    $response                                               = $this->crypt($this->responseValidation('Une erreur interne est survenue'));
                                                    return $response;
                                                }


                                            } catch (\Throwable $e) {
                                                DB::rollBack();
                                                //return $this->responseCatchError($e->getMessage());
                                                return $this->crypt($this->responseCatchError($e->getMessage()));

                                            }


                                        }


                                        /**
                                        * Remove the specified resource from storage.
                                        *
                                        * @param  \App\Models\c  $c
                                        * @return \Illuminate\Http\Response
                                        */
                                        public function destroy(int $idformulaire_saisi)
                                        {
                                            try {

                                                //ON vérifie si le processus de traitement de la demande à été enclenché
                                                $check_trt_dmd = ValidationForms::where('r_formulaire_saisi', $idformulaire_saisi)->first();

                                                if ( isset($check_trt_dmd)) {
                                                    return $this->crypt($this->responseValidation('Vous ne pouvez pas supprimer cette demande'));
                                                }

                                                $check                    = FormulaireSaisie::find($idformulaire_saisi);

                                                if( $check ){
                                                    $check->update(['r_status' => 0]);
                                                    $check->delete();
                                                     return $this->crypt($this->responseSuccess('Votre demande vient d\'être anullée'));
                                                }else{
                                                     return $this->crypt($this->responseValidation('Demande non inexistant'));
                                                }


                                             } catch (\Throwable $e) {
                                                 //return $e->getMessage();
                                                 return $this->crypt($e->getMessage());
                                             }
                                        }

                                        public function detail_forms_soumit(Request $request){

                                            //$datas                                     = $this->crypt($request->all()); //Ceci est pour mes tests
                                            //return $datas;

                                            //Décryptage des données récues
                                            $inputs                                      = $this->decryptData($request->p_data);

                                            //return $inputs;

                                            try {
                                                // Sélectionne la liste des formulaires << Il exécute une fonction PL pour la récupération >>
                                                $formsFields                         = DB::select('SELECT sc_workflows.f_formulaires_soumit(?)', [json_encode($inputs)]);
                                                $result                              = json_decode($formsFields[0]->f_formulaires_soumit)->_result[0];


                                                $response                                = $this->crypt($this->responseSuccess('Liste des formulaires',$result));
                                                return $response;


                                                //return $result;
                                                //return $this->decryptData(json_decode($response));

                                            } catch (\Throwable $e) {
                                                //return $e->getMessage();
                                                return $this->crypt($this->responseCatchError($e->getMessage()));
                                            }
                                        }

                                    }
