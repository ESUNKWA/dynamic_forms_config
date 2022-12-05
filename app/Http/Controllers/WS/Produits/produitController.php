<?php

namespace App\Http\Controllers\WS\Produits;

use App\Models\cr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SendResponses;
use App\Http\Traits\cryptData;
use App\Http\Traits\Formulaires\Produits;
use App\Models\Produit;

class produitController extends Controller
{
    use SendResponses, cryptData, Produits;
    /**
     * Liste de tout les produits.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $listeProduits           = $this->listeProduits();
            $response                = $this->crypt($this->responseSuccess('Liste des produits',$listeProduits));
            return $response;

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$datas                     = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs                      = $this->decryptData($request->p_data);
        //return $inputs;

        //Validation des données avant le stockage dans la base de données

        $errors                      = [
            'r_nom_produit'          => 'required|min:2|unique:t_produits',
            'r_description'          => 'required',
        ];
        $erreurs                     = [
            'r_nom_produit.required' => 'Le nom du produits est réquis',
            'r_nom_produit.min'      => 'Veuillez saisir nom de produit valide',
            'r_nom_produit.unique'   => 'Le nom du produit existe déjà dans la base',
            'r_description.required' => 'Description obligatoire',
        ];

        $inputsValidations           = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                // Enregistrement des données dans la base de données
                $dataSave            = Produit::create([
                    'r_nom_produit'  => $inputs['r_nom_produit'],
                    'r_description'  => $inputs['r_description'],
                    'r_created_by'   => $inputs['r_creer_par'],
                    'r_status'       => 1
                ]);

                $response            = $this->crypt($this->responseSuccess('Enregistrement effectué avec succès',$dataSave));

                return $response;

            } catch (\Throwable $e) {
                //return $this->responseCatchError($e->getMessage());
                return $this->crypt($this->responseCatchError($e->getMessage()));
            }

        }else{
            //return $this->responseValidation('Erreur lié au paramètres, voir le détails',$inputsValidations->errors());
            return $this->crypt($this->responseValidation('Erreur lié au paramètres, voir le détails',$inputsValidations->errors()));

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function show(int $id_tyepe_client)
    {
        try {
            $listeProduits           = $this->liste_produits_type_client($id_tyepe_client);
            $response                = $this->crypt($this->responseSuccess('Liste des produits',$listeProduits));
            return $response;

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function edit(cr $cr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {

        //$datas                     = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs                      = $this->decryptData($request->p_data);
        //return $inputs;

        //Validation des données avant le stockage dans la base de données

        $errors                      = [
            'r_nom_produit'          => 'required|min:2|unique:t_produits',
            'r_description'          => 'required',
        ];
        $erreurs                     = [
            'r_nom_produit.required' => 'Le nom du produits est réquis',
            'r_nom_produit.min'      => 'Veuillez saisir nom de produit valide',
            'r_nom_produit.unique'   => 'Le nom du produit existe déjà dans la base',
            'r_description.required' => 'Description obligatoire',
        ];

        $inputsValidations           = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {
                //Recherche de la ligne
                $check               = Produit::find($id);

                // Modification des données dans la base de données
                $check->update([
                    'r_nom_produit'  => $inputs['r_nom_produit'],
                    'r_description'  => $inputs['r_description'],
                    'r_updated_by'   => $inputs['r_modifier_par']
                ]);

                if( $check->id ){

                    $response        = $this->crypt($this->responseSuccess('Modification effectué avec succès'));
                    return $response;

                }

            } catch (\Throwable $e) {
                return $this->crypt($this->responseCatchError($e->getMessage()));
            }

        }else{

            //return $this->responseValidation('Avertissement',$inputsValidations->errors());
            return $this->crypt($this->responseValidation('Avertissement',$inputsValidations->errors()));

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {

           $check                    = Produit::find($id);

           if( $check ){
               $check->delete();
                return $this->crypt($this->responseSuccess('Supression effectuée avec succès'));
           }else{
                return $this->crypt($this->responseValidation('Produit non inexistant'));
           }


        } catch (\Throwable $e) {
            //return $e->getMessage();
            return $this->crypt($e->getMessage());
        }

    }

    public function restore($id)
    {
        try {

            Produit::withTrashed()->find($id)->restore();
            return $this->crypt($this->responseSuccess('Restoration effectuée avec succès'));

        } catch (\Throwable $e) {
            return $this->crypt($e->getMessage());
        }
    }

    public function restoreAll()
    {
        try {

            Produit::onlyTrashed()->restore();
            return $this->crypt($this->responseSuccess('Les données ont bién étées restorées'));

        } catch (\Throwable $e) {
            return $this->crypt($e->getMessage());
        }


    }
}
