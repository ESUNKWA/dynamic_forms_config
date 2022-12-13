<?php

namespace App\Http\Controllers\Apply\Config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendNotifyController;
use App\Http\Traits\SendResponses;
use App\Http\Traits\Utilisateurs;
use App\Models\cr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TypeClient;
use Illuminate\Support\Facades\Auth;

class TypeClientController extends Controller
{
    use SendResponses, Utilisateurs;

    public function __construct(){
        $this->middleware('access')->only('index');
    }

    public function index()
    {
        try {

            $typeClients                                                      = TypeClient::orderBy('r_libelle', 'ASC')->get();
            $permissions                                                      = PermissionRole(Auth::user()->r_role);

            return view('pages.typeclients', ['typeClients'                   => $typeClients, 'permissions'=> $permissions]);

        } catch (\Throwable $e) {

            return $e->getMessage();

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
        $userId                                                               = Auth::user()->id;

        $inputs                                                               = $request->all();
        //return $inputs;



        $errors                                                               = [
            'r_code'                                                          => 'required|min:3|unique:t_type_clients',
            'r_libelle'                                                       => 'required|min:2|unique:t_type_clients',
        ];
        $erreurs                                                              = [
            'r_libelle.required'                                              => 'Le nom du type est réquis',
            'r_libelle.min'                                                   => 'Veuillez saisir un libellé valide',
            'r_libelle.unique'                                                => 'Libellé dejà existant',

            'r_code.required'                                                 => 'Le nom est réquis',
            'r_code.min'                                                      => 'Veuillez saisir un code valide',
            'r_code.unique'                                                   => 'Code dejà existant'
        ];

        $inputsValidations                                                    = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {
                //Validation des données avant le stockage dans la base de données

                //Enregistrement des données de l'utilisateurs
                $insertTypeClient                                             = TypeClient::create([
                    'r_code'                                                  => $request->r_code,
                    'r_libelle'                                               => $request->r_libelle,
                    'r_description'                                           => $request->r_description,
                    'r_creer_par'                                             => $userId,
                ]);

                // Récupération des validateurs
                $groupeDiff                                                   = $this->listevalidateurs();
                $email                                                        = [];
                // Récupération des emails
                foreach ($groupeDiff as  $value) {
                    array_push($email, $value->email);
                }

                // Envoie de mail au groupe de diffusion
                $data                                                         = [
                    'titre'                                                   => 'Création de type de client',
                    'message'                                                 =>'Le type de client '.$request->r_libelle.' viens d\'être créer par '.Auth::user()->name.' '.Auth::user()->lastname,
                    'email'                                                   => $email];

                    //return $data;

                    $sendMail                                                 = new SendNotifyController();
                    $sendMail->sendMessageGoogle( new Request($data) );
                    // Envoie de mail à l'utilisateur

                    if( $insertTypeClient->id ){
                        //Cryptage des données à renvoyer
                        $response                                             = $this->responseSuccess('Enregistrement effectué avec succès');
                        return $response;
                    }

                } catch (\Throwable $e) {
                    return $this->responseCatchError($e->getMessage());
                }

            }else{

                return $this->responseValidation('Avertissement lié au paramètres',$inputsValidations->errors());
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
            $userId                                                           = Auth::user()->id;

            $inputs                                                           = $request->all();
            //return $inputs;

            try {
                //Validation des données avant le stockage dans la base de données

                $errors                                                       = [
                    'r_libelle'                                               => 'required|min:2'
                ];
                $erreurs                                                      = [
                    'r_libelle.required'                                      => 'Le nom du type est réquis',
                    'r_libelle.min'                                           => 'Veuillez saisir un valide'
                ];


                $inputsValidations                                            = Validator::make($inputs, $errors, $erreurs);

                if ( !$inputsValidations->fails() ) {

                    //Recherche de la ligne
                    $check                                                    = TypeClient::find($id);

                    if( $check ){

                        $check->update([
                            'r_libelle'                                       => $request->r_libelle,
                            'r_description'                                   => $request->r_description,
                            'r_modifier_par'                                  => $userId,
                        ]);
                        //Cryptage des données à renvoyer
                        $response                                             = $this->responseSuccess('Modification effectués avec succès');

                        return $response;
                    }

                    return $response                                          = $this->responseValidation('Ligne non trouvée');


                }else{

                    return $this->responseValidation('Avertissement lié au paramètres',$inputsValidations->errors());
                }

            } catch (\Throwable $e) {

                return $this->responseCatchError('Une erreur est survenue',$e->getMessage());
            }
        }


        /**
        * Remove the specified resource from storage.
        *
        * @param  \App\Models\cr  $cr
        * @return \Illuminate\Http\Response
        */
        public function destroy(int $idTypeClient)
        {
            //Recherche de la ligne
            $check                                                            = TypeClient::find($idTypeClient);

            $response                                                         = ( $check )? $check->delete() : $this->responseValidation('Ligne non trouvée');

            return $response;
        }

        public function restore($id)
        {
            try {

                TypeClient::withTrashed()->find($id)->restore();
                return $this->responseSuccess('Restoration effectuée avec succès');

            } catch (\Throwable $e) {
                return $e->getMessage();
            }


        }

        public function restoreAll()
        {
            try {

                TypeClient::onlyTrashed()->restore();
                return $this->responseSuccess('Les données ont bién étes restorées');

            } catch (\Throwable $e) {
                return $e->getMessage();
            }


        }

        public function active_desactive( Request $request){

            //Recherche de la ligne
            $check                                                            = TypeClient::find($request->id);

            $check->update([
                'r_status'                                                    => $request->r_status
            ]);

            if( $check->id ){

                if ( $check->r_status == 0) {
                    return $this->responseSuccess('Désactivation effectuée avec succès');
                }else{
                    return $this->responseSuccess('Activation effectuée avec succès');

                }

            }

        }
    }
