<?php

namespace App\Http\Controllers\Apply\Formulaires;

use App\Http\Controllers\Controller;
use App\Models\c;
use Illuminate\Http\Request;
use App\Models\Formulaires\Champs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\SendResponses;
use App\Http\Traits\Formulaires\Produits;
use App\Http\Traits\Formulaires\TypesChamps;
use App\Models\Formulaires\Formulaire_champs;

class ChampsController extends Controller
{
    use TypesChamps, Produits, SendResponses;

    public function __construct(){
        $this->middleware('access')->only('index');
    }

    public function index()
    {
        $listeChps = Champs::orderBy('id','ASC')->where('product',null)->get();

        $listeTypeChps = $this->listeTypeChamps();

        $listeProduits = $this->listeProduits();
        $permissions                                       = PermissionRole(Auth::user()->r_role);

        return view('pages.formulaires.champs', [
                                                'champs' => $listeChps,
                                                'typeChps' => $listeTypeChps,
                                                'produits' => $listeProduits,
                                                'produit' => $listeProduits,
                                                'permissions'  => $permissions,
                                                'produit_id' => 0
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;

        //Récupération des données postés par le client
        $inputs = $request->all();

        $fields = json_decode($inputs['fields']);

        $check_product_field_name = Champs::where('product', $fields[0]->product)->where('field_name',$fields[0]->field_name )->first();

        if( isset($check_product_field_name->id) ){
            return [
                '_status' => 0,
                '_message' => 'Le nom du champs existe dejà pour ce produit'
            ];
        }



        try {

            foreach ($fields as $field) {
                // Enregistrement des données dans la base de données
                $dataSave = Champs::create([
                    'product' => $field->product,
                    'field_label' => $field->field_label,
                    'field_name' => $field->field_name,
                    'field_options' => $field->field_options,
                    'field_placeholder' => $field->field_placeholder,
                    'field_type' => $field->field_type,
                    'field_value' => $field->field_value,
                    'length' => $field->length,
                    'value_max' => $field->value_max,
                    'value_min' => $field->value_min,
                    'r_creer_par' => $userId,
                ]);

            }

            if( $dataSave->id ){
                return $this->responseSuccess('Enregistrement effectué avec succès');
            }

        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {


        $listeChps = Champs::orderBy('id','ASC')
                            ->where('product', $id)
                            ->get();

                            $listeTypeChps = $this->listeTypeChamps();

                            $listeProduits = $this->listeProduits();

                            return view('pages.formulaires.champs', [
                                'champs' => $listeChps,
                                'typeChps' => $listeTypeChps,
                                'produits' => $listeProduits,
                                'produit' => $listeProduits,
                            ]);
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
    public function update(Request $request, int $id)
    {
        $userId = Auth::user()->id;

        try {

            // Recherche la ligne
            $check = Champs::find($id);

            if( $check ){

                $check->update([
                    'product' => $request->product,
                    'field_label' => $request->field_label,
                    'field_name' => $request->field_name,
                    'field_options' => $request->field_options,
                    'field_placeholder' => $request->field_placeholder,
                    'field_type' => $request->field_type,
                    'field_value' => $request->field_value,
                    'length' => $request->length,
                    'value_max' => $request->value_max,
                    'value_min' => $request->value_min,
                    'r_modifier_par' => $userId
                ]);

                if( $check->id ){
                    return $this->responseSuccess('Modification effectués avec succès');
                }

            }else{
                return $this->responseCatchError('Utilisateur non trouvé');
            }



        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public function destroy(int $id)
    {
        try {

            // Vérifions d'abords si le champs est utilisé dans un formulaire
            $checkChp = Champs::find($id);

            if( isset($checkChp)){

                $checkChp->delete();

                $response = [
                    '_status' => 1,
                    '_message' => 'Ce champs à bien été suprimé'
                ];

                return $response;

            }else{

                $check = Champs::find($id);
                $response = ( $check )? $check->delete() : $this->responseValidationForm('Ligne non trouvée');

                return $response;
            }
            //Recherche de la ligne


        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public function restore($id)
    {
        try {

            Champs::withTrashed()->find($id)->restore();
            return $this->responseSuccess('Restoration effectuée avec succès');

        } catch (\Throwable $e) {
            return $e->getMessage();
        }


    }

    public function restoreAll()
    {
        try {

            Champs::onlyTrashed()->restore();
            return $this->responseSuccess('Les données ont bién étes restorées');

        } catch (\Throwable $e) {
            return $e->getMessage();
        }


    }

     public function list_champs_by_product(int $id){

        $champs = Champs::select('t_champs.*','t_formulaire_champs.r_champs')
        ->leftJoin('t_formulaire_champs', 't_formulaire_champs.r_champs', 't_champs.id')
        ->where('t_champs.product', $id)
        ->where('t_champs.r_status',1)
        ->whereNull('t_formulaire_champs.r_champs')
        ->orderBy('t_champs.field_name','ASC')
        ->get();
        return $this->responseSuccess('Liste des champs par produit', $champs);

    }

    public function champs_by_product(Request $request){

        $listeChps = Champs::orderBy('id','ASC')
                            ->where('product', $request->id)
                            //->where('r_status',1)
                            ->get();

                            $listeTypeChps =  $this->listeTypeChamps();

                            $listeProduits = $this->listeProduits();
                            $permissions                                       = PermissionRole(Auth::user()->r_role);
                            return view('pages.formulaires.champs', [
                                'champs' => $listeChps,
                                'typeChps' => $listeTypeChps,
                                'produits' => $listeProduits,
                                'produit' => $listeProduits,
                                'permissions'  => $permissions,
                                'produit_id' => $request->id
                            ]);
    }

    public function active_desactive( Request $request){

        //Recherche de la ligne
        $check = Champs::find($request->idchamp);

        $check->update([
            'r_status' => $request->r_status
        ]);

        if( $check->id ){
            if ( $check->r_status == 0) {
                return $this->responseSuccess('Désactivation effectuée avec succès');
            }else{
                return $this->responseSuccess('Activation effectuée avec succès');

            }        }

    }
}
