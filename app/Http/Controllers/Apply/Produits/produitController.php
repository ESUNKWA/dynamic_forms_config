<?php

namespace App\Http\Controllers\Apply\Produits;

use App\Models\cr;
use App\Http\Traits\SendResponses;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Http\Traits\Clients;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Formulaires\Produits;

class produitController extends Controller
{
    use SendResponses, Produits, Clients;

    public $fileName;


    public function __construct(){
        $this->middleware('access')->only('index');
    }

    public function index()
    {

        $listeProduits                                                      = $this->listeProduits();
        $listeTypeClients                                                   = $this->list_type_clients();
        $clientEntp                                                         = $this->list_clients_entp();
        $permissions                                                        = PermissionRole(Auth::user()->r_role);

        return view('pages.produit', [
            'produits'                                                      => $listeProduits,
            'listeTypeClients'                                              => $listeTypeClients,
            'clientEntp'                                                    => $clientEntp,
            'permissions'                                                   => $permissions,
            'type_client_id' => 0
        ]);
    }

    //Liste des produits par type de client
    public function produitParTypeClient(Request $request)
    {

        $listeProduits                                                      = $this->liste_produits_type_client($request->r_type_client);

        $listeTypeClients                                                   = $this->list_type_clients();
        $clientEntp                                                         = $this->list_clients_entp();
        $permissions                                                        = PermissionRole(Auth::user()->r_role);

        return view('pages.produit', [
            'produits'                                                      => $listeProduits,
            'listeTypeClients'                                              => $listeTypeClients,
            'clientEntp'                                                    => $clientEntp,
            'idtypeclient'                                                  => $request->r_type_client,
            'permissions'                                                   => $permissions,
            'type_client_id'                                                => $request->r_type_client
        ]);
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
    * Fonction permettant de saisir un nouveau produits.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $userId                                                             = Auth::user()->id;

        //Récupération des données postés par le client
        $inputs                                                             = $request->all();

        if( isset($inputs['image']) ){
            $this->fileName                                                 = time().'.'.$request->image->extension();
        }
        //Validation des données avant le stockage dans la base de données

        $errors                                                             = [
            'r_nom_produit'                                                 => 'required|min:2|unique:t_produits'
        ];
        $erreurs                                                            = [
            'r_nom_produit.required'                                        => 'Le nom du produits est réquis',
            'r_nom_produit.min'                                             => 'Veuillez saisir nom de produit valide',
            'r_nom_produit.unique'                                          => 'Le nom du produit existe déjà dans la base'
        ];

        $inputsValidations                                                  = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                // Enregistrement des données dans la base de données
                if( !isset($inputs['image']) ){
                    unset($inputs['path_name']);
                }else{
                    $inputs['path_name']                                    =  url('/').'/storage/images/produits/'.$this->fileName;
                }
                $inputs['r_created_by']                                     = $userId;
                $dataSave                                                   = Produit::create($inputs);

                // Stockage des images
                if( isset($inputs['image']) && trim($this->fileName) && $dataSave ){
                    //$image                                                =$request->image->move(public_path('/images/produits'), $this->fileName);
                    $image                                                  = $request->image->storeAs(
                        'images/produits',
                        $this->fileName,
                        'public'
                    );
                }

                if( $dataSave->id ){
                    return $this->responseSuccess('Enregistrement effectué avec succès');
                }

            } catch (\Throwable $e) {
                return $e->getMessage();
            }

        }else{

            return $inputsValidations->errors();

        }
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Models\cr  $cr
    * @return \Illuminate\Http\Response
    */
    public function show(cr $cr)
    {
        //
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
        $userId                                                             = Auth::user()->id;

        //Récupération des données postés par le client
        $inputs                                                             = $request->all();


        if( isset($inputs['image']) ){
            $this->fileName                                                 = time().'.'.$request->image->extension();
        }
        //Validation des données avant le stockage dans la base de données

        $errors                                                             = [
            'r_nom_produit'                                                 => 'required|min:2'
        ];
        $erreurs                                                            = [
            'r_nom_produit.required'                                        => 'Le nom du produits est réquis',
            'r_nom_produit.min'                                             => 'Veuillez saisir nom de produit valide'
        ];

        $inputsValidations                                                  = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                //Recherche de la ligne
                $check                                                      = Produit::find($id);

                // Modification des données dans la base de données
                if( !isset($inputs['image']) ){
                    unset($inputs['path_name']);
                }else{
                    $inputs['path_name']                                    =  url('/').'/storage/images/produits/'.$this->fileName;
                }

                //return $inputs;
                $inputs['r_updated_by']                                     = $userId;
                $check->update($inputs);

                // Stockage des images
                if( isset($inputs['image']) && trim($this->fileName) && $check ){
                    //$image                                                =$request->image->move(public_path('/images/produits'), $this->fileName);
                    $image                                                  = $request->image->storeAs(
                        'images/produits',
                        $this->fileName,
                        'public'
                    );
                }

                if( $check->id ){
                    return $this->responseSuccess('Modification effectué avec succès');
                }

            } catch (\Throwable $e) {
                return $e->getMessage();
            }

        }else{

            return $inputsValidations->errors(); //$this->responseCatchError('Avertissement',$inputsValidations->errors());

        }
    }

    public function active_desactive( Request $request){

        //Recherche de la ligne
        $check                                                              = Produit::find($request->idproduit);

        $check->update([
            'r_status'                                                      => $request->r_status
        ]);

        if( $check->id ){

            if ( $check->r_status == 0) {
                return $this->responseSuccess('Désactivation effectuée avec succès');
            }else{
                return $this->responseSuccess('Activation effectuée avec succès');

            }
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
            //Recherche de la ligne
            $check                                                          = Produit::find($id);

            $response                                                       = ( $check )? $check->delete() : $this->responseValidationForm('Ligne non trouvée');

            return $response;

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function restore($id)
    {
        try {

            Produit::withTrashed()->find($id)->restore();
            return $this->responseSuccess('Restoration effectuée avec succès');

        } catch (\Throwable $e) {
            return $e->getMessage();
        }


    }

    public function restoreAll()
    {
        try {

            Produit::onlyTrashed()->restore();
            return $this->responseSuccess('Les données ont bién étes restorées');

        } catch (\Throwable $e) {
            return $e->getMessage();
        }


    }
}
