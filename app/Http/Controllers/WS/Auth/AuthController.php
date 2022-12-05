<?php

namespace App\Http\Controllers\WS\Auth;

use App\Http\Controllers\SendNotifyController;
use App\Http\Traits\Generics;
use App\Models\cr;
use App\Models\OTP;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Traits\cryptData;
use App\Http\Traits\SendResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Logout;
use App\Models\Client;

class AuthController extends Controller
{
    use SendResponses, cryptData, Logout, Generics;

    /**
     * Liste des utilisateurs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $listeUtilisateurs = User::orderBy('name','ASC')->get();

            //Cryptage des données avant retour au client
            $donneesCryptees = $this->crypt($this->responseSuccess('Liste desutilisateurs', $listeUtilisateurs));

            return $donneesCryptees;

        } catch (\Throwable $e) {

            return $this->crypt($this->responseCatchError('Une erreur est survenue',$e->getMessage()));

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

        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs = $this->decryptData($request->p_data);
        //return $inputs;


        try {

            //Validation des données avant le stockage dans la base de données

            $errors = [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed',
            ];
            $erreurs = [
                'name.required' => 'Le nom de l\'utilisateur est réquis',
                'email.required' => 'L\'adresse email de l\'utilisateur est réquis',
                'email.email' => 'Le format de l\'adresse email est invalide',
                'password.required' => 'Le mot de passe est réquis',
                'password.confirmed' => 'Les mots de passes ne correspondent pas',
            ];

            $inputsValidations = Validator::make($inputs, $errors, $erreurs);

            if ( !$inputsValidations->fails() ) {

                // Enregistrement des données dans la base de données
                $UserSave = User::create([
                    'name' => $inputs['name'],
                    'email' => $inputs['email'],
                    'lastname' => $inputs['lastname'],
                    'phone' => $inputs['phone'],
                    'r_canal' => 2,
                    'password' => Hash::make($inputs['phone']),
                    'r_creer_par' => $inputs['r_creer_par'],
                    'r_modifier_par' => $inputs['r_creer_par']
                ]);

                //Cryptage des données à renvoyer
                $response = $this->crypt($this->responseSuccess('Utilisateur enregistré avec succès',
                ['login'=>$UserSave->email, 'password'=>$inputs['phone']]));

                return $response;

            }else{
                return $this->crypt($this->responseCatchError('Erreur survenue lors de la création',$inputsValidations->errors()));
            }

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError('Une erreur est survenue',$e->getMessage()));
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

        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;
        //Décryptage des données récues
        $inputs = $this->decryptData($request->p_data);
        //return $inputs;


        try {

            //Validation des données avant le stockage dans la base de données

            $errors = [
                'name' => 'required',
                'lastname' => 'required|min:2',
                //'phone' => 'required|unique:users',
            ];
            $erreurs = [
                'name.required' => 'Le nom de l\'utilisateur est réquis',
                'lastname.required' => 'Le prenom de l\'utilisateur est réquis',
                //'phone.required' => 'Le numéro de téléphone est réquis',
                //'phone.unique' => 'Le numéro de téléphone exite dejà !',
            ];

            $inputsValidations = Validator::make($inputs, $errors, $erreurs);

            if( !$inputsValidations->fails() ){

                $update = User::find($id);

                $update->update($inputs);

                //Cryptage des données à renvoyer
                $response = $this->crypt($this->responseSuccess('Modification effectuée avec succès'));

                return $response;

            }else{
                return $this->crypt($this->responseValidation('Avertissement lié au paramètres',$inputsValidations->errors()));
            }

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
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

            $checkUser = User::where('id', $id)->first();

            $checkUser->delete();

            return $this->crypt($this->responseSuccess('Utiliateur suprimé'));

        } catch (\Throwable $e) {

            return $this->crypt($this->responseCatchError('Une erreur est sur lors de la supression', $e->getMessage()));

        }


    }

    public function login(Request $request)
    {
        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;


        //Décryptage des données récues
        $inputs = $this->decryptData($request->p_data);
        //return $inputs;

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
                return $this->crypt($this->responseValidation('Erreur de validation', $validateUser->errors()));
            }

            //recheche de l'utilisateur à la base de données
            $user = User::where('email', $inputs['email'])->first();

            // Si un utilisateur n'est pas trouvé
            if( !$user ){

                return $this->crypt($this->responseValidation('Email ou mot de passe incorrect.',null));

            }else{

                //Vérifie si le compte est actif
                if( $user->r_status == 0 ){
                    //return 'Votre compte est désactivé, veuillez contacter l\'administrateur.';
                    return $this->crypt($this->responseValidation('Votre compte est désactivé, veuillez contacter l\'administrateur.',null));
                }

                // Si un utilisateur est trouvé, on vérifie sont mot de passe
                if( Hash::check($inputs['password'], $user->password)){

                    unset($user->password); // Suppression du mot de passe dans le retour de l'API

                    // Identification du canal de connexion

                    switch ($user->r_canal_cnx) {

                        //Renseigner la historique connexion

                        case 1:
                            // Connexion utilisateur système centralisé
                                $result = [
                                    '_token' => $user->createToken("API TOKEN")->plainTextToken,
                                    '_user' => $user,
                                ];
                            break;

                        case 2:
                             // Connexion utilisateur backoffice web
                                $result = [
                                    '_token' => $user->createToken("API TOKEN")->plainTextToken,
                                    '_user' => $user,
                                ];
                            break;

                    }


                    if ($user->r_password_change == false) {

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
          return $this->crypt($this->responseCatchError($e->getMessage()));
        }
    }

    public function logout(Request $request){
        return $this->decnx($request);
    }

    //Changement de mot de passe
    /* public function passwordUpdate(Request $request){

        //Décryptage des données
        $inputs = $request->all();

        // Controlle des champs
        $errors = [
            'password' => 'required|min:4|confirmed'
        ];
        $erreurs = [
            'password.required' => 'Le mot de passe est réquis',
            'password.min' => 'Veuillez saisir un mot de passe fort',
            'password.confirmed' => 'Le mot de passe ne correspondent pas',
        ];
        $validation = Validator::make($inputs, $errors, $erreurs);

        if( !$validation->fails() ){

            $check = User::find($inputs['idutilisateur']);

            $updatePwd = $check->update([
                'password' => Hash::make($inputs['password']),
                'r_password_change' => true
            ]);

            if( $updatePwd){

                return "ok";

            }


        }else{
            return $validation->errors();
        }

    } */



    /**
     * Fonction d'envoie de OTP
     */
    public function sendOtp(Request $request, $mode){

        if ($mode == 3 || $mode == 2) {
            //Décryptage des données récues
            $inputs = $this->decryptData($request->p_data);

            //Vérification de l'existence de l'email
            if ($mode == 2) {
                $verifEmail = User::where('email', $inputs['email'])->first();
            }else{
                $verifEmail = Client::where('r_email', $inputs['email'])->first();
            }

           if (!$verifEmail) {
               return $this->crypt($this->responseValidation('Adresse email non existante'));
           }

        }else{
            $inputs = $request->all();
            //Vérification de l'existence de l'email
            $verifEmail = User::where('email', $inputs['email'])->first();

            if (!$verifEmail) {
                return $this->responseValidation('Adresse email non existante');
            }

        }

        //Génération OTP

        $otp = $this->otp();

        //Détermination de l'heure d'expiration de l'OTP
        $time = Carbon::now()->format('H:i');
        $otpexpiration = date('H:i', strtotime($time. ' +2 minutes'));

        //Sauvegarde de l'OTP
        $insertion = OTP::create([
            'r_email' => $inputs['email'],
            'r_otp' => $otp,
            'r_heure_expiration' =>  $otpexpiration
        ]);


        //Envoie de l'OTP à l'adresse email
        if( $insertion->id ){

            $data = [
                'email' => $inputs['email'],
                'message' => "Nous vous envoyons cet email à la suite de votre demande de réinitialisation de vos accès.
                Veuillez indiquer ce code unique valable 2 minutes: ".$insertion->r_otp,
                'titre' => 'Notification OTP'
            ];

            $sendMail = new SendNotifyController();
            $sendMail->sendMessageGoogle( new Request($data) );

            $retour = $this->responseSuccess('Un OTP à été envoyé sur l\'adresse email '.$inputs['email'].'. La durée d\'expiration est de 2 mn');

            if ($mode == 3 || $mode == 2) {
                //Décryptage des données récues
                return $this->crypt($retour);
            }else{
                return $retour;
            }

        }

    }

    public function verifotp(Request $request, $mode){

        if ($mode == 3 || $mode == 2) {
            //Décryptage des données récues
            $inputs = $this->decryptData($request->p_data);
            //return $inputs;
        }else{
            $inputs = $request->all();
        }

        //Vérification de l'OTP
        $checkotp = OTP::select('*')
                        ->where('r_email', $inputs['email'])
                        ->where('r_otp', $inputs['otp'])
                        ->first();

        if( !isset($checkotp) ){

            $retour = $this->responseValidation('OTP incorecte');

            if ($mode == 3 || $mode == 2) {
                return $this->crypt($retour);
            }else{
                return $retour;
            }

        }

        //Calcule du temps écoulé
        $start = strtotime(Carbon::now()->format('H:i'));
        $end = strtotime($checkotp->r_heure_expiration);
        $mins = ($end - $start) / 60;

        if ($mode == 3 || $mode == 2) {
            if ( $mins < 0) {
                return $this->crypt($this->responseValidation('OTP expiré'));
            }else{
                $check = OTP::where('r_otp', $inputs['otp'])->update(['r_otp_valide' => true]);
                return $this->crypt($this->responseSuccess('OTP vérifié avec succès'));
            }
            //return $inputs;
        }else{
            if ( $mins < 0) {
                return $this->responseValidation('OTP expiré');
            }else{
                $check = OTP::where('r_otp', $inputs['otp'])->update(['r_otp_valide' => true]);
                return $this->responseSuccess('OTP vérifié avec succès');
            }
        }

    }

    public function passwordchange(Request $request){

        //$datas                                                              = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs                                                               = $this->decryptData($request->p_data);
        //return $inputs;

        // Controlle des champs
        $errors = [
            'password' => 'required|min:4|confirmed'
        ];
        $erreurs = [
            'password.required' => 'Le mot de passe est réquis',
            'password.min' => 'Veuillez saisir un mot de passe fort',
            'password.confirmed' => 'Les mots de passes ne correspondent pas'
        ];
        $validation = Validator::make($inputs, $errors, $erreurs);

        if( !$validation->fails() ){

            try {
                $check = User::where('email',$inputs['email'])->first();

                if( isset($check) ){

                    $updatePwd = $check->update([
                        'password' => Hash::make($inputs['password']),
                        'r_password_change' => true
                    ]);

                    if( isset($updatePwd)){
                        return $this->crypt($this->responseSuccess('Mot de passe modifié avec succès'));
                    }

                }else{

                    $change_pwd = Client::where('r_email',$inputs['email'])->first();

                    if (isset($change_pwd)) {
                        $updatePwd = $change_pwd->update([
                            'r_password' => Hash::make($inputs['password']),
                            'r_password_change' => true
                        ]);

                        return $this->crypt($this->responseSuccess('Mot de passe modifié avec succès'));
                    }


                }



            } catch (\Throwable $e) {
                //return $e->getMessage();
                return $this->crypt($this->responseCatchError($e->getMessage()));
            }

        }else{
            return $this->crypt($this->responseCatchError($validation->errors()));
        }

    }

}
