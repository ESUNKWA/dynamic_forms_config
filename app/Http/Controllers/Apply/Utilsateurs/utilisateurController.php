<?php

namespace App\Http\Controllers\Apply\Utilsateurs;

use App\Http\Controllers\SendNotifyController;
use App\Models\c;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Traits\Utilisateurs;
use App\Http\Traits\SendResponses;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\Permissions\Roles;
use Illuminate\Support\Facades\Validator;

class utilisateurController extends Controller
{
    use SendResponses, Roles, Utilisateurs;


    public function __construct(){
        $this->middleware('access')->only(['index']);
    }

    public function index()
    {
        $listeUtilisateurs                                 = $this->listeUtilisateur();
        $listeRoles                                        = $this->listRoles();
        $permissions                                       = PermissionRole(Auth::user()->r_role);

        return view('pages.utilisateur', [
            'utilisateurs' => $listeUtilisateurs,
            'listeRoles'   => $listeRoles,
            'permissions'  => $permissions
        ]);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function validateurs()
    {
        $validateurs                                 = $this->listevalidateurs();
        $listeRoles                                        = $this->listRoles();
        $permissions                                       = PermissionRole(Auth::user()->r_role);

        return view('pages.validateurs', [
            'utilisateurs' => $validateurs,
            'listeRoles'   => $listeRoles,
            'permissions'  => $permissions
        ]);
    }

    public function utilisateurs()
    {
        $listeUtilisateurs                                 = $this->listeUtilisateur();
        return $listeUtilisateurs;
    }


    public function affect_validateur(Request $request){

        $inputs = $request->users;

        try {

            DB::beginTransaction();

            $update = DB::table('users')->update(['r_validateur_wkf' => false]);

            if ( !isset($inputs) ) {
                $res = ['_status' => 1, '_message' => 'Enregistrement effectu?? avec succ??s'];
            }else{
                foreach ($inputs as $user_id) {
                    $check = User::find($user_id)->update(['r_validateur_wkf' => true]);
                }
            }

            DB::commit();

            $res = ['_status' => 1, '_message' => 'Les validateurs ont bien ??t??s enregistr??s'];
            return $res;

        } catch (\Throwable $e) {
            DB::rollback();
            return $e->getMessage();
        }




    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {

        //Utilisateur connect?? qui mene l'action
        $userId                                            = Auth::user()->id;

        //R??cup??ration des champs
        $inputs                                            = $request->all();

        if( isset($inputs['photo']) ){
            $this->fileName                                                 = time().'.'.$request->photo->extension();
        }

        $errors                                            = [
            'name'                                         => 'required|string|max:35',
            'lastname'                                     => 'required|string|max:35',
            'phone'                                        => 'required|unique:users',
            'email'                                        => 'required|email|unique:users'
        ];
        $erreurs                                           = [
            'name.required'                                => 'Le nom est r??quis',
            'name.string'                                  => 'Format incorrect pour le nom',
            'name.max'                                     => 'Nom invalide',

            'lastname.required'                            => 'Le prenoms est r??quis',
            'lastname.string'                              => 'Format incorrect pour le prenoms',
            'lastname.max'                                 => 'Prenom invalide',

            'phone.required'                               => 'Le num??ro de t??l??phone est r??quis',
            'phone.unique'                                 => 'Le num??ro de t??l??phone existe dej??',

            'email.required'                               => 'Adresse email requis',
            'email.email'                                  => 'Adresse email invalide',
            'email.unique'                                 => 'Adresse email dej?? existante'

        ];

        $inputsValidations                                 = \Illuminate\Support\Facades\Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                DB::beginTransaction();

                if( !isset($inputs['photo']) ){
                    unset($inputs['path_name']);
                }else{
                    $inputs['path_name']                                    =  url('/').'/storage/images/utilisateurs/'.$this->fileName;
                }

                $user                                                = User::create([
                    'name'                                           => $request->name,
                    'lastname'                                       => $request->lastname,
                    'phone'                                          => $request->phone,
                    'email'                                          => $request->email,
                    'r_canal_cnx'                                    => $request->r_canal_cnx,
                    'r_es_super_admin'                               =>false,
                    'r_role'                                         =>$request->user_role,
                    'r_status'                                       => 0,
                    'r_creer_par'                                    => $userId,
                    'r_modifier_par'                                 => $userId,
                    'password'                                       => Hash::make($request->phone.time()),
                    'path_name'                                       => (isset($inputs['path_name'])? $inputs['path_name']: null)
                ]);

                // Stockage des images
                if( isset($inputs['photo']) && trim($this->fileName) && isset($user) ){
                    //$image                                                =$request->image->move(public_path('/images/produits'), $this->fileName);
                    $image                                                  = $request->photo->storeAs(
                        'images/utilisateurs',
                        $this->fileName,
                        'public'
                    );
                }

                // R??cup??ration des validateurs
                $groupeDiff = $this->listevalidateurs();
                $email = [];
                // R??cup??ration des emails
                foreach ($groupeDiff as  $value) {
                    array_push($email, $value->email);
                }

                // Envoie de mail au groupe de diffusion
                $data                                           = [
                    'titre'                                     => 'Cr??ation de compte',
                    'message'                                   =>'Le compte de '.$inputs['name'].' '.$inputs['lastname'].' viens d\'??tre cr??er par '.Auth::user()->name.' '.Auth::user()->lastname,
                    'email'                                     => $email
                ];

                    //return $data;

                    $sendMail                                   = new SendNotifyController();
                    $sendMail->sendMessageGoogle( new Request($data) );
                    // Envoie de mail ?? l'utilisateur

                    DB::commit();

                    return $this->responseSuccess('Le compte utilisateur ?? bien ??t?? cr??e', [
                        'login'   => $user->email
                    ]);

                } catch (\Throwable $e) {
                    DB::rollBack();
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

        /**
        * Update the specified resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @param  \App\Models\c  $c
        * @return \Illuminate\Http\Response
        */
        public function update(Request $request, int $id)
        {
            $userId                                            = Auth::user()->id;

            //R??cup??ration des donn??es post??s par le client
            $inputs                                            = $request->all();

            if( isset($inputs['photo']) ){
                $this->fileName                                                 = time().'.'.$request->photo->extension();
            }

            //Validation des donn??es avant le stockage dans la base de donn??es

            $errors                                            = [
                'name'                                         => 'required|string|max:35',
                'lastname'                                     => 'required|string|max:35',
                //'phone'                                      => 'required|unique:users'
            ];
            $erreurs                                           = [
                'name.required'                                => 'Le nom est r??quis',
                'name.string'                                  => 'Format incorrect pour le nom',
                'name.max'                                     => 'Nom invalide',

                'lastname.required'                            => 'Le prenoms est r??quis',
                'lastname.string'                              => 'Format incorrect pour le prenoms',
                'lastname.max'                                 => 'Prenom invalide',

                //'phone.required'                             => 'Le num??ro de t??l??phone est r??quis',
                //'phone.unique'                               => 'Le num??ro de t??l??phone existe dej??'

            ];

            $inputsValidations                                 = \Illuminate\Support\Facades\Validator::make($inputs, $errors, $erreurs);

            if ( !$inputsValidations->fails() ) {

                try {

                    // Modification des donn??es dans la base de donn??es
                    if( !isset($inputs['photo']) ){
                        unset($inputs['path_name']);
                    }else{
                        $inputs['path_name']                                    =  url('/').'/storage/images/utilisateurs/'.$this->fileName;
                    }

                    //Recherche de la ligne
                    $check                                     = User::find($id);

                    // Modification des donn??es dans la base de donn??es
                    $check->update($inputs);

                    // Stockage des images
                if( isset($inputs['photo']) && trim($this->fileName) && $check ){
                    $image                                                  = $request->photo->storeAs(
                        'images/utilisateurs',
                        $this->fileName,
                        'public'
                    );
                }

                    if( $check->id ){
                        return $this->responseSuccess('Modification effectu??e avec succ??s');
                    }

                } catch (\Throwable $e) {
                    return $this->responseCatchError('Erreur', $e->getMessage());
                }

            }else{

                $this->responseCatchError('Avertissement',$inputsValidations->errors());

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

        public function active_desactive(Request $request, int $id){
            //Recherche de la ligne
            $check                                             = User::find($id);

            $check->update($request->all());

            if( $check->id ){
                //return $this->responseSuccess('Activation effectu??e avec succ??s');

                //Envoi du mail au validateur concern??

                switch ($request->r_status) {
                    case 0:
                        $retour = [
                            '_status' => 0,
                            '_message' => 'D??sactivation du compte reussie'
                        ];
                        break;

                        default:
                        // Envoie de mail ?? l'utilisateur
                        $data                                           = [
                            'titre'                                     => 'Validation de compte',
                            'message'                                   =>'Votre compte a ??t?? valid??, veuillez vous connecter',
                            'email'                                     => $check->email];

                            $sendMail                                   = new SendNotifyController();
                            $sendMail->sendMessageGoogle( new Request($data) );
                            // Envoie de mail ?? l'utilisateur
                            $retour = [
                                '_status' => 1,
                                '_message' => 'Activation du compte reussie'
                            ];
                            break;
                        }

                        return $retour;
                    }

                }


                public function sendOtp(Request $request){

                    $otp = '12345';

                    $insertion = OTP::create([
                        'r_email' => $request->email,
                        'r_otp' => $otp,
                        'r_heure_expiration' => time()
                    ]);

                    if( $insertion->id ){

                    }

                }

                public function passwordchange(Request $request){

                    //D??cryptage des donn??es
                    $inputs = $request->all();

                    // Controlle des champs
                    $errors = [
                        'mdp' => 'required|min:4'
                    ];
                    $erreurs = [
                        'mdp.required' => 'Le mot de passe est r??quis',
                        'mdp.min' => 'Veuillez saisir un mot de passe fort'
                    ];
                    $validation = Validator::make($inputs, $errors, $erreurs);

                    if( !$validation->fails() ){

                        $check = User::where('email',$inputs['email']);

                        $updatePwd = $check->update([
                            'password' => Hash::make($inputs['mdp']),
                            'r_password_change' => true
                        ]);

                        if( $updatePwd){

                            return [
                                '_status' =>1,
                                '_message' => 'Mot de passe modifi?? avec succ??s'
                            ];

                        }


                    }else{
                        return $validation->errors();
                    }

                }
            }
