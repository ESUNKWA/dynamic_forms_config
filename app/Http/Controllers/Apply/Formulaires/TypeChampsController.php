<?php

namespace App\Http\Controllers\Apply\Formulaires;

use App\Http\Controllers\Controller;
use App\Models\c;
use App\Models\Formulaires\TypeChamps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SendResponses;
use App\Http\Traits\Formulaires\TypesChamps;

class TypeChampsController extends Controller
{
    use SendResponses, TypesChamps;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware('access')->only('index');
    }
    public function index()
    {
        $listeTypeChps = $this->listeTypeChamps();
        $permissions                                       = PermissionRole(Auth::user()->r_role);

        return view('pages.formulaires.typechamps', ['typeChamps' => $listeTypeChps, 'permissions'=> $permissions]);
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
     * Saisie des type de champs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;

        //Récupération des données postés par le client
        $inputs = $request->all();

        //Validation des données avant le stockage dans la base de données

        $errors = [
            'r_libelle' => 'required|min:2|unique:t_type_champs'
        ];
        $erreurs = [
            'r_libelle.required' => 'Le nom du type de champs est réquis',
            'r_libelle.min' => 'Veuillez saisir nom valide',
            'r_libelle.unique' => 'Le nom du type existe déjà dans la base',
        ];

        $inputsValidations = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                // Enregistrement des données dans la base de données
                $dataSave = TypeChamps::create([
                    'r_libelle' => $request->r_libelle,
                    'r_description' => $request->r_description,
                    'r_creer_par' => $userId,
                    'r_modifier_par' => $userId,
                ]);

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
    public function update(Request $request, int $id)
    {
        $userId = Auth::user()->id;

        //Récupération des données postés par le client
        $inputs = $request->all();

        //Validation des données avant le stockage dans la base de données

        $errors = [
            'r_libelle' => 'required|min:2'
        ];
        $erreurs = [
            'r_libelle.required' => 'Le nom du type de champs est réquis',
            'r_libelle.min' => 'Veuillez saisir nom valide'
        ];

        $inputsValidations = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                //Recherche de la ligne
                $check = TypeChamps::find($id);

                // Modification des données dans la base de données
                $dataSave = [
                    'r_libelle' => $request->r_libelle,
                    'r_description' => $request->r_description,
                    'r_modifier_par' => $userId,
                ];
                $check->update($dataSave);

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(c $c)
    {
        //
    }
}
