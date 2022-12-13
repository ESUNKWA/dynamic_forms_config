<?php

namespace App\Http\Controllers\WS\Clients;

use App\Models\c;
use App\Models\Client;
use App\Models\TypeClient;
use Illuminate\Http\Request;
use App\Http\Traits\cryptData;
use App\Http\Traits\SendResponses;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TypeClientController extends Controller
{
    //Traits pour gérer le retour et le cryptage des données
    use SendResponses, cryptData;
    /**
     * Cette fonction renvoie la liste des type clients.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $clients = TypeClient::orderBy('r_libelle', 'ASC')->where('r_status', 1)->where('deleted_at', null)->get();

            //Cryptage des données avant retour au client
            $donneesCryptees = $this->crypt($this->responseSuccess('Liste des types de clients', $clients));
            return $donneesCryptees;
            //return $donneesCryptees;

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
     * Cette fonction permet de saisir un nouveau type client
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;
        //Décryptage des données récues
       $inputs = $this->decryptData($request->p_data);
       //return $inputs;

        try {

            DB::beginTransaction();

            //Validation des données avant le stockage dans la base de données

            $errors = [
                'r_libelle' => 'required|alpha|min:2|unique:t_type_clients',
                'r_creer_par' => 'required'
            ];
            $erreurs = [
                'r_libelle.required' => 'Le nom du type est réquis',
                'r_libelle.alpha' => 'Caractères invalide',
                'r_libelle.min' => 'Veuillez saisir un valide',
                'r_libelle.unique' => 'Libellé dejà existant',
                'r_creer_par.required' => 'Utilisateur non réconnu'
            ];


            $inputsValidations = Validator::make($inputs, $errors, $erreurs);

            if ( !$inputsValidations->fails() ) {

                //Enregistrement des données de l'utilisateurs
                $insertTypeClient = TypeClient::create($inputs);

                DB::commit();

                //Cryptage des données à renvoyer
                $response = $this->crypt($this->responseSuccess('Enregistrement effectué avec succès',$insertTypeClient));

                return $response;

            }else{

                return $this->responseValidation('Avertissement lié au paramètres',$inputsValidations->errors());
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->responseCatchError('Une erreur est survenue',$e->getMessage());
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
     * Cette fonction permet de modifier un type client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     */
    public function update(Request $request, int $id)
    {
        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;
        //Décryptage des données récues
       $inputs = $this->decryptData($request->all());
       //return $inputs;

        try {

            //Validation des données avant le stockage dans la base de données

            $errors = [
                'r_libelle' => 'required|min:2',
                'r_modifier_par' => 'required'
            ];
            $erreurs = [
                'r_libelle.required' => 'Le nom du type est réquis',
                'r_libelle.alpha' => 'Caractères invalide',
                'r_libelle.min' => 'Veuillez saisir un valide',
                'r_modifier_par.required' => 'Utilisateur non réconnu'
            ];


            $inputsValidations = Validator::make($inputs, $errors, $erreurs);

            if ( !$inputsValidations->fails() ) {

                //Recherche de la ligne
                $check = TypeClient::find($id);

                if( $check ){

                    $check->update($inputs);
                    //Cryptage des données à renvoyer
                    $response = $this->crypt($this->responseSuccess('Modification effectués avec succès',$check));

                    return $response;
                }

                return $response = $this->crypt($this->responseValidation('Ligne non trouvée'));


            }else{

                return $this->crypt($this->responseValidation('Avertissement lié au paramètres',$inputsValidations->errors()));
            }

        } catch (\Throwable $e) {

            return $this->crypt($this->responseCatchError('Une erreur est survenue',$e->getMessage()));
        }
    }

    /**
     * Ctte fonction sert à suprimer un type de client.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $idTypeClient)
    {
        //Recherche de la ligne
        $check = TypeClient::find($idTypeClient);

        $response = ( $check )? $check->delete() : $this->responseValidation('Ligne non trouvée');

        return $response;
    }
}
