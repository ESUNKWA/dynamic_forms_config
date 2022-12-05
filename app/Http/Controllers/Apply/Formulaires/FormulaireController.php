<?php

namespace App\Http\Controllers\Apply\Formulaires;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendNotifyController;
use App\Http\Traits\Utilisateurs;
use App\Models\c;
use Illuminate\Http\Request;
use App\Http\Traits\Formulaires\Produits;
use App\Http\Traits\Formulaires\Formulaires as forms;
use App\Http\Traits\Formulaires\GroupeChamps;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Formulaires\Formulaires;
use App\Http\Traits\SendResponses;
use App\Models\Formulaires\Formulaire_champs;
use App\Models\Formulaires\NiveauFormulaires;
use Illuminate\Support\Facades\DB;

class FormulaireController extends Controller
{
    use Produits, GroupeChamps, SendResponses, forms, Utilisateurs;

    public function __construct(){
        $this->middleware('access')->only('index');
    }

    public function index()
    {
        $idproduct = 0;
        $produits = $this->listeProduits();
        $grpChamps = $this->listeGrpeChamps();
        $permissions                                       = PermissionRole(Auth::user()->r_role);
        // Sélectionne la liste des formulaires << Il exécute une fonction PL pour la récupération >>
        $formsFields = DB::select('SELECT sc_workflows.f_get_formulaires_par_product_ad('.$idproduct.')');

        $forms = json_decode($formsFields[0]->f_get_formulaires_par_product_ad)->_result;

        return view('pages.formulaires.formulaire',
                                                [
                                                    'listeProduits' => $produits,
                                                    'grpChamps' => $grpChamps,
                                                    'formsList' => $forms,
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

        //return $fields[1];

        //Récupération des données postés par le client
        $inputs = $request->all();
        $fields = json_decode($request->r_champs);

        //Validation des données avant le stockage dans la base de données

        $errors = [
            'r_nom' => 'required|min:2',
            'r_produit' => 'required',
        ];
        $erreurs = [
            'r_nom.required' => 'Le nom du formulaire de champs est réquis',
            'r_nom.min' => 'Veuillez saisir nom de formulaire valide',
            'r_produit.required' => 'Veuillez selectionner le produit',
        ];

        $inputsValidations = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                DB::beginTransaction();

                //Recherche et incrémentation du niveau formulaire
                //$niveau = Formulaire_champs::where('r_formulaire','')->get();

                // Enregistrement des données dans la base de données
                $FormsSave = Formulaires::create([
                    'r_nom' => $request->r_nom,
                    'r_produit' => $request->r_produit,
                    'r_description' => $request->r_description,
                    'r_creer_par' => $userId,
                    'r_rang' => explode(" ",$request->r_niveau)[1]
                ]);


                // Enregistrement des données dans la base de données
                foreach ($fields as $field) {

                    Formulaire_champs::create([
                        'r_formulaire' => $FormsSave->id,
                        'r_champs' => $field->id,
                        'r_rang' => $field->r_rang,
                        'r_es_obligatoire' => $field->r_es_obligatoire,
                        'r_status' => 1,
                        'r_grp_champs' => $field->r_grp_champs,
                        'r_creer_par' => $userId
                    ]);
                }

                //Enregistrement du niveau de formualire
                NiveauFormulaires::create([
                    'r_nom_niveau' => $request->r_niveau,
                    'r_formulaire' => $FormsSave->id
                ]);


                DB::commit();

                // Récupération des validateurs
                $groupeDiff = $this->listevalidateurs();
                $email = [];
                // Récupération des emails
                foreach ($groupeDiff as  $value) {
                   array_push($email, $value->email);
                }

                // Envoie de mail au groupe de diffusion
                $data                                           = [
                    'titre'                                     => 'Création d\'un nouveau formulaire',
                    'message'                                   =>'Le formulaire '.$request->r_nom.' vient d\'être créer par '.Auth::user()->name.' '.Auth::user()->lastname,
                    'email'                                     => $email];

                //return $data;

                    $sendMail                                   = new SendNotifyController();
                    $sendMail->sendMessageGoogle( new Request($data) );
                // Envoie de mail à l'utilisateur

                return $this->responseSuccess('Enregistrement effectué avec succès');


            } catch (\Throwable $e) {
                DB::rollback();
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
    public function update(Request $request, int $idForm)
    {
        $userId = Auth::user()->id;
        $fields = json_decode($request->r_champs);

        //return $fields[1];

        //Récupération des données postés par le client
        $inputs = $request->all();

        //Validation des données avant le stockage dans la base de données

        $errors = [
            'r_nom' => 'required|min:2',
            'r_produit' => 'required'
        ];
        $erreurs = [
            'r_nom.required' => 'Le nom du formulaire de champs est réquis',
            'r_nom.min' => 'Veuillez saisir nom de formulaire valide',
            'r_produit.unique' => 'Veuillez selectionner le produit',
        ];

        $inputsValidations = Validator::make($inputs, $errors, $erreurs);


        if ( !$inputsValidations->fails() ) {

            try {

                DB::beginTransaction();

                // Modification du formulaire des données dans la base de données

                $checkForms = Formulaires::find($idForm);

                $checkForms->update([
                    'r_nom' => $request->r_nom,
                    'r_produit' => $request->r_produit,
                    'r_description' => $request->r_description,
                    'r_creer_par' => $userId
                ]);

                if( $checkForms->id ){

                    if( count($fields) !== 0 ){

                        $checkFields = Formulaire_champs::where('r_formulaire',$request->r_formulaire);

                        if( $checkFields ){
                            $checkFields->delete();

                            // Enregistrement des données dans la base de données

                            foreach ($fields as $field) {

                                Formulaire_champs::create([
                                    'r_formulaire' => $checkForms->id,
                                    'r_champs' => $field->id,
                                    'r_es_obligatoire' => $field->r_es_obligatoire,
                                    'r_rang' => $field->r_rang,
                                    'r_status' => 1,
                                    'r_creer_par' => $userId
                                ]);
                            }

                        }else{

                            return $this->responseCatchError('Une erreur est survenue !');

                        }


                    }


                }

                DB::commit();

                return $this->responseSuccess('Enregistrement effectué avec succès');


            } catch (\Throwable $e) {
                DB::rollback();
                return $e->getMessage();
            }

        }else{

            return $inputsValidations->errors();

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {

            $check                                   = Formulaires::find($id);

            if( $check ){
                $check->delete();
                 return $this->responseSuccess('Supression effectuée avec succès');
            }else{
                 return $this->responseValidation('Produit non inexistant');
            }


         } catch (\Throwable $e) {
            return $this->responseCatchError($e->getMessage());
         }
    }


    public function restore($id)
    {
        try {

            Formulaires::withTrashed()->find($id)->restore();
            return $this->responseSuccess('Restoration effectuée avec succès');

        } catch (\Throwable $e) {
            return $this->responseCatchError($e->getMessage());
        }


    }

    public function restoreAll()
    {
        try {

            Formulaires::onlyTrashed()->restore();
            return $this->responseSuccess('Les données ont bién étes restorées');

        } catch (\Throwable $e) {
            return $this->responseCatchError($e->getMessage());
        }


    }

    public function formulaire_by_product(Request $request){

        $produits = $this->listeProduits();
        $grpChamps = $this->listeGrpeChamps();
        $permissions                                       = PermissionRole(Auth::user()->r_role);
        // Sélectionne la liste des formulaires << Il exécute une fonction PL pour la récupération >>
        try {

            $formsFields = DB::select('SELECT sc_workflows.f_get_formulaires_par_product_ad('.$request->idproduct.')');

            $forms = json_decode($formsFields[0]->f_get_formulaires_par_product_ad)->_result;

            return view('pages.formulaires.formulaire',
                                                    [
                                                        'listeProduits' => $produits,
                                                        'grpChamps' => $grpChamps,
                                                        'formsList' => $forms,
                                                        'permissions'  => $permissions,
                                                        'produit_id' => $request->idproduct
                                                    ]);

        } catch (\Throwable $e) {
            return $e->getMessage();
        }


    }

    public function active_desactive( Request $request){

        //Recherche de la ligne
        $check = Formulaires::find($request->idformulaire);

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

    public function nom_formulaire_by_product(int $idproduit){

        $forms = $this->list_formulaire_by_product($idproduit);
        return $forms;
    }
}
