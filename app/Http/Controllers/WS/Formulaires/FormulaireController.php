<?php

namespace App\Http\Controllers\WS\Formulaires;

use App\Http\Controllers\Controller;
use App\Models\crR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\SendResponses;
use App\Http\Traits\cryptData;
use Illuminate\Support\Facades\Validator;
use App\Models\Formulaires\Formulaires;
use App\Models\Formulaires\Formulaire_champs;

//use App\Http\Controllers\Apply\Formulaires\FormulaireController;

class FormulaireController extends Controller
{
    use SendResponses, cryptData;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {



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
     * Cette fonction permet de.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$datas                                     = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
       $inputs                                       = $this->decryptData($request->p_data);
       // return $inputs;

        $fields                                      = $inputs['r_champs'];

        //Validation des données avant le stockage dans la base de données
        $errors                                      = [
            'r_nom'                                  => 'required|min:2|unique:t_formulaires',
            'r_produit'                              => 'required'
        ];
        $erreurs                                     = [
            'r_nom.required'                         => 'Le nom du formulaire de champs est réquis',
            'r_nom.min'                              => 'Veuillez saisir nom de formulaire valide',
            'r_nom.unique'                           => 'Le nom du formulaire existe dejà',
            'r_produit.unique'                       => 'Veuillez selectionner le produit',
        ];

        $inputsValidations                           = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                DB::beginTransaction();

                // Enregistrement des données dans la base de données
                $FormsSave                           = Formulaires::create([
                    'r_nom'                          => $inputs['r_nom'],
                    'r_produit'                      => $inputs['r_produit'],
                    'r_description'                  => $inputs['r_description'],
                    'r_creer_par'                    => $inputs['r_creer_par']
                ]);

                // Enregistrement des données dans la base de données

                foreach ($fields as $field) {
                    Formulaire_champs::create([
                        'r_formulaire'               => $FormsSave->id,
                        'r_champs'                   => $field['id'],
                        'r_rang'                     => $field['r_rang'],
                        'r_status'                   => 1,
                        'r_grp_champs'               => $field['r_grp_champs'],
                        'r_creer_par'                => $inputs['r_creer_par']
                    ]);
                }

                DB::commit();
                $response                            = $this->crypt($this->responseSuccess('Enregistrement effectué avec succès'));
                return $response;


            } catch (\Throwable $e) {
                DB::rollback();
                //return $this->responseCatchError($e->getMessage());
                return $this->crypt($this->responseCatchError($e->getMessage()));
            }

        }else{
            return $this->crypt($this->responseValidation($inputsValidations->errors()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\crR  $crR
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\crR  $crR
     * @return \Illuminate\Http\Response
     */
    public function edit(crR $crR)
    {
        //
    }

    /**
     * Modification de formulaire
     */
    public function update(Request $request, int $idForm)
    {
        //$datas                                     = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
       $inputs                                       = $this->decryptData($request->p_data);
       //return $inputs;

        $fields                                      = $inputs['r_champs'];

        //Validation des données avant le stockage dans la base de données

        $errors                                      = [
            'r_nom'                                  => 'required|min:2',
            'r_produit'                              => 'required'
        ];
        $erreurs                                     = [
            'r_nom.required'                         => 'Le nom du formulaire de champs est réquis',
            'r_nom.min'                              => 'Veuillez saisir nom de formulaire valide',
            'r_produit.unique'                       => 'Veuillez selectionner le produit',
        ];

        $inputsValidations                           = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                DB::beginTransaction();

                // Modification du formulaire des données dans la base de données

                $checkForms                          = Formulaires::find($idForm);

                $checkForms->update([
                    'r_nom'                          => $inputs['r_nom'],
                    'r_produit'                      => $inputs['r_produit'],
                    'r_description'                  => $inputs['r_description'],
                    'r_modifier_par'                 => $inputs['r_modifier_par']
                ]);

                if( $checkForms->id ){

                    if( count($fields) !== 0 ){

                        $checkFields                 = Formulaire_champs::where('r_formulaire',$idForm);

                        if( $checkFields ){
                            $checkFields->delete();

                            // Enregistrement des données dans la base de données

                            foreach ($fields as $field) {
                                Formulaire_champs::create([
                                    'r_formulaire'   => $checkForms->id,
                                    'r_champs'       => $field['id'],
                                    'r_rang'         => $field['r_rang'],
                                    'r_status'       => 1,
                                    'r_grp_champs'   => $field['r_grp_champs'],
                                    'r_creer_par'    => $inputs['r_modifier_par'],
                                    'r_modifier_par' => $inputs['r_modifier_par']
                                ]);
                            }

                        }else{

                            return $this->crypt($this->responseCatchError('Formulaire non trouvée !'));

                        }


                    }


                }

                DB::commit();

                return $this->crypt($this->responseSuccess('Modification effectué avec succès'));


            } catch (\Throwable $e) {
                DB::rollback();
                return $this->crypt($this->responseCatchError($e->getMessage()));
            }

        }else{

            return $this->crypt($this->responseValidation($inputsValidations->errors()));

        }
    }

    /**
     * Retour la liste des formualires par produits
     */
    public function getformbyproduct(Request $request){

        //$datas                                     = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs                                      = $this->decryptData($request->p_data);
        //return $inputs;

        $idproduit                                   = $inputs['p_idproduit'];
        $idclient                                    = $inputs['p_idclient'];
        $reference                                    = (isset($inputs['r_reference'])? $inputs['r_reference'] : 0);

        try {
            // Sélectionne la liste des formulaires << Il exécute une fonction PL pour la récupération >>
            if( $inputs['r_canal_cnx']               == 3 ){
                $formsFields                         = DB::select('SELECT sc_workflows.f_get_formulaires_par_product_cli(?,?,?)', [$idproduit, $idclient, $reference]);
                $result                              = json_decode($formsFields[0]->f_get_formulaires_par_product_cli)->_result;
            }else{
                $formsFields                         = DB::select('SELECT sc_workflows.f_get_formulaires_par_product_ad(?)', [$idproduit]);
                $result                              = json_decode($formsFields[0]->f_get_formulaires_par_product_ad)->_result;
            }

            $response                                = $this->crypt($this->responseSuccess('Liste des formulaires',$result));
            return $response;

        } catch (\Throwable $e) {
            //return $e->getMessage();
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\crR  $crR
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {

            $check                                   = Formulaires::find($id);

            if( $check ){
                $check->delete();
                 return $this->crypt($this->responseSuccess('Supression effectuée avec succès'));
            }else{
                 return $this->crypt($this->responseValidation('Produit non inexistant'));
            }


         } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
         }
    }


    public function restore($id)
    {
        try {

            Formulaires::withTrashed()->find($id)->restore();
            return $this->crypt($this->responseSuccess('Restoration effectuée avec succès'));

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }


    }

    public function restoreAll()
    {
        try {

            Formulaires::onlyTrashed()->restore();
            return $this->crypt($this->responseSuccess('Les données ont bién étes restorées'));

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }


    }


}
