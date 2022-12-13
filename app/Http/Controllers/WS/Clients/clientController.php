<?php

namespace App\Http\Controllers\WS\Clients;

use App\Http\Controllers\SendNotifyController;
use App\Models\c;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Traits\Clients;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SendResponses;
use App\Http\Traits\cryptData;

use function GuzzleHttp\json_encode;
use App\Http\Traits\Logout;
use App\Models\Entreprise;

class clientController extends Controller
{
    use SendResponses, cryptData, Logout, Clients;
    /**
     * Cette fonction affiche la liste de tous les clients.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {

            $clients = Client::orderBy('r_nom', 'ASC')->get();
            //Cryptage des données avant retour au client
            $donneesCryptees = $this->crypt($this->responseSuccess('Liste des clients', $clients));

            return $donneesCryptees;

        } catch (\Throwable $e) {

            return $this->crypt($this->responseCatchError($e->getMessage()));;

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
     * Saise des clients.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerCpte(Request $request)
    {
        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs = $this->decryptData($request->p_data);
        //return $inputs;

         try {

             //DB::beginTransaction();

             //Validation des données avant le stockage dans la base de données

             $errors = [
                 'r_type_client' => 'required',
                 'r_nom' => 'required|min:2',
                 'r_prenoms' => 'required|min:2',
                 'r_telephone' => 'required|unique:t_clients',
                 'r_email' => 'required|email|unique:t_clients'
             ];
             $erreurs = [
                 'r_type_client.required' => 'Le type client est réquis',
                 'r_nom.required' => 'Le nom est réquis',
                 'r_nom.min' => 'Veuillez saisir un nom valide',
                 'r_prenoms.required' => 'Le prenoms et requis',
                 'r_prenoms.min' => 'Veuillez saisir un prenom valide',
                 'r_telephone.required' => 'Le numéro de téléphone est requis',
                 'r_telephone.unique' => 'Numéro de téléphone dejà existant',

                 'r_email.required' => 'L\'adresse email de l\'utilisateur est réquis',
                 'r_email.email' => 'Le format de l\'adresse email est invalide',
                 'r_email.unique' => 'L\'adresse email déjà existant'
             ];


             $inputsValidations = Validator::make($inputs, $errors, $erreurs);

             if ( !$inputsValidations->fails() ) {

                $domain_mail=explode('@', $inputs['r_email']);
                $inputs['domain_mail']= $domain_mail[count($domain_mail) -1] ;
                 $inputs['password'] = Hash::make($inputs['r_telephone']);
                 $str_json = \json_encode($inputs);
                 //return $str_json;

                $fncResponse = DB::select("SELECT sc_workflows.f_create_clients(?)", [$str_json]);

                //Envoi du mail au validateur concerné

                $data                                           = [
                    'titre'                                     => 'Création de compte utilisateur',
                    'message'                                   =>'Votre compte à bien été crée. Vos accès sont: Identifiant: '.$inputs['r_email'].' Mot de passe: '.$inputs['r_telephone'],
                    'email'                                     => $inputs['r_email']];

                    $sendMail                                   = new SendNotifyController();
                    $sendMail->sendMessageGoogle( new Request($data) );

                return $this->crypt(json_decode($fncResponse[0]->f_create_clients));
                //return $this->crypt($this->responseSuccess('Votre compte à bien été crée, veuillez vos connecter à votre email pour récupérer vos accès'));

             }else{

                 //return $this->responseValidation('Avertissement lié au paramètres',$inputsValidations->errors());
                 return $this->crypt($this->responseValidation('Erreur survenue lors de la création',$inputsValidations->errors()));
             }

         } catch (\Throwable $e) {
             //DB::rollBack();
             //return $this->responseCatchError($e->getMessage());
             return $this->crypt($this->responseCatchError($e->getMessage()));
         }
    }

    public function store(Request $request)
    {


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
     * Modification d'données d'un client
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $idClient)
    {
        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;
        //Décryptage des données récues
       $inputs = $this->decryptData($request->all());
       //return $inputs;

        try {

            //Validation des données avant le stockage dans la base de données

            $errors = [
                'r_type_client' => 'required',
                'r_nom' => 'required|min:2',
                'r_prenoms' => 'required|min:2',
                'r_telephone' => 'required|unique:t_clients',
                'r_modifier_par' => 'required',
                'r_email' => 'required|email|unique:t_clients',
                'password' => 'required|confirmed',
            ];
            $erreurs = [
                'r_type_client.required' => 'Le type client est réquis',
                'r_nom.required' => 'Le nom est réquis',
                'r_nom.min' => 'Veuillez saisir un nom valide',
                'r_prenoms.required' => 'Le prenoms et requis',
                'r_prenoms.min' => 'Veuillez saisir un prenom valide',
                'r_telephone.required' => 'Le numéro de téléphone est requis',
                'r_telephone.unique' => 'Numéro de téléphone dejà existant',
                'r_modifier_par.required' => 'Utilisateur non réconnu',
            ];


            $inputsValidations = Validator::make($inputs, $errors, $erreurs);

            if ( !$inputsValidations->fails() ) {

                //Recherche de la ligne
                $check = Client::find($idClient);

                //Enregistrement des données de l'utilisateurs
                $check->update($inputs);

                //Cryptage des données à renvoyer
                $response = $this->crypt($this->responseSuccess('Modification effectué avec succès',$check));

                return $response;

            }else{

                return $this->responseCatchError('Erreur survenue lors de la modification',$inputsValidations->errors());
            }

        } catch (\Throwable $e) {
            return $this->responseCatchError('Une erreur est survenue',$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $idClient)
    {
        try {
            //Recherche de la ligne
            $check = Client::find($idClient);

            $response = $check->delete();

            return $this->crypt($this->responseValidation('Supression effectuée avec succès'));

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }
    }


    public function login(Request $request)
    {
        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs = $this->decryptData($request->p_data);

        try {

            // Vérification des données postés
            $validateUser = Validator::make($inputs,
            [
                'email' => 'required|email',
                'password' => 'required'
            ],
            [
                'email.required' => 'l\'adresse email est réquis',
                'email.email' => 'l\'adresse email invalide',
                'password.required' => 'Mot de passe réquis'
            ]);

            if($validateUser->fails()){

                // renvoie un avertissement si un champs est mal renseigné
                //$this->responseValidation('Erreur de validation', $validateUser->errors());
                return $this->crypt($this->responseValidation('Erreur de validation', $validateUser->errors()));
            }

            //recheche de l'utilisateur dans la base de données
            $clients = Client::where('r_email', $inputs['email'])->first();


            // Si un utilisateur n'est pas trouvé
            if( !$clients ){

                //return $this->responseValidation('Email ou mot de passe incorrect.',null);
                return $this->crypt($this->responseValidation('Email ou mot de passe incorrect.',null));

            }else{

                // Si un utilisateur est trouvé, on vérifie sont mot de passe
                if( Hash::check($inputs['password'], $clients->r_password)){

                    unset($clients->r_password); //Suppression du mot de passe dans le retour

                    //Récupération des infos entreprise si client entreprise
                    if ($clients->r_type_client == 2) {

                        $societe = Client::select('t_entreprises.*')
                                            ->join('t_client_entreprises', 't_clients.id', 't_client_entreprises.r_client')
                                            ->join('t_entreprises', 't_entreprises.id', 't_client_entreprises.r_entreprise')
                                            ->where('t_client_entreprises.r_client', $clients->id)
                                            ->first();

                    }

                    // Données à retournées au client (Navigateur)
                    $result = [
                        '_token' => $clients->createToken("API TOKEN")->plainTextToken,
                        '_client' => $clients,
                    ];

                    if ($clients->r_type_client == 2) {
                        $result['_societe'] = $societe;
                    }

                    if ($clients->r_password_change == false) {

                        $response = $this->responseSuccess('Vous êtes maintenant connecté, veuillez changer votre mot de passe', $result);

                    }else{
                        $response = $this->responseSuccess('Vous êtes maintenant connecté', $result);
                    }
                     //Cryptage des données à renvoyer
                     $retour = $this->crypt($response);
                     return $retour;


                }else{

                    //return $this->responseValidation('Email ou mot de passe incorrect.',null);
                    return $this->crypt($this->responseValidation('Email ou mot de passe incorrect.',null));

                }
            }



        } catch (\Throwable $e) {

          return $this->responseCatchError($e->getMessage());
          return $this->crypt($this->responseCatchError($e->getMessage()));

        }
    }

    public function logout(Request $request){
        return $this->decnx($request);
    }

    public function liste_cli_entp()
    {

        try {

            $clients = $this->list_clients_entp();
            //Cryptage des données avant retour au client
            $donneesCryptees = $this->crypt($this->responseSuccess('Liste des clients entreprises', $clients));

            return $donneesCryptees;

        } catch (\Throwable $e) {
            //return $e->getMessage();
            return $this->crypt($this->responseCatchError($e->getMessage()));;

        }


    }
}
