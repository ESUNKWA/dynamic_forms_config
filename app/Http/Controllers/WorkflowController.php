<?php

//namespace the42coders\Workflows\Http\Controllers;
namespace App\Http\Controllers;

use App\Http\Traits\Workflows\FormsWorkflows;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use the42coders\Workflows\Loggers\WorkflowLog;
use the42coders\Workflows\Tasks\Task;
use the42coders\Workflows\Triggers\ReRunTrigger;
use the42coders\Workflows\Triggers\Trigger;
//use App\Http\Controllers\Controller;
//use the42coders\Workflows\Workflow;

use App\Models\Workflow;
use App\Models\Formulaires\Formulaires as Forms;
use App\Http\Traits\Utilisateurs;
use App\Models\Workflows\Taches;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Workflows\Niveau_validation;
use App\Http\Traits\SendResponses;
use App\Http\Traits\Formulaires\Produits;
use App\Models\Workflows\FormulaireSaisie;
use App\Models\Workflows\ValidationForms;
use App\Http\Traits\cryptData;
use App\Models\Workflows\Triggers;
use App\Http\Traits\Formulaires\Formulaires;
use App\Models\Client;

class WorkflowController extends Controller
{
    use Formulaires, Utilisateurs, SendResponses, Produits, cryptData, FormsWorkflows;

    public function __construct(){
        $this->middleware('access')->only('index');
    }
    public function index()
    {
        $workflows                                                            = Workflow::select('workflows.*','prd.r_nom_produit')
        ->join('t_produits as prd', 'prd.id', '=', 'workflows.r_produit')
        ->get();
        $validateurs                                                         = $this->listevalidateurs();
        $permissions                                                          = PermissionRole(Auth::user()->r_role);


        return view('workflowconfig.index',
        [
            'workflows'=> $workflows, 'validateurs'=> $validateurs,
            'permissions' => $permissions
        ]);
    }

    public function show($id)
    {
        $workflow                                                             = Workflow::find($id);
        return view('workflowconfig.create', ['workflows'                     => $workflow->name]);
        //return view('workflows::diagram', ['workflow'                       => $workflow]);
    }

    public function create()
    {
        //$formulaires                                                        = $this->formulaires();
        $produits                                                             = $this->listeProduits();
        $permissions                                                          = PermissionRole(Auth::user()->r_role);

        //return view('workflows::create');
        return view('workflowconfig.create',['workflows'                      => '', 'produits' => $produits,
        'permissions'                                                         => $permissions]);
    }

    public function store(Request $request)
    {

        $errors = [
            'r_produit' => 'required',
            'name' => 'required|unique:workflows',
        ];
        $erreurs = [
            'r_produit.required' => 'produit non defini',
            'name.required' => 'Veuillez saisir le nom du workflow',
            'name.unique' => 'Nom du workflow existe déjà',
        ];

        $validation = Validator::make($request->all(), $errors, $erreurs);

        if($validation->fails()){
            return back()->withErrors($validation->errors())->withInput();
          }

        $workflow                                                             = Workflow::create($request->all());

        return redirect(route('index', ['workflows'                           => $workflow]));
    }

    public function edit($id)
    {
        $workflow                                                             = Workflow::find($id);
        $produits                                                             = $this->listeProduits();
        $permissions                                                          = PermissionRole(Auth::user()->r_role);

        return view('workflowconfig.edit', [
            'workflow'                                                        => $workflow,
            'produits'                                                        => $produits
            , 'permissions'                                                   => $permissions
        ]);
    }

    public function update(Request $request, $id)
    {
        $workflow                                                             = Workflow::find($id);

        $workflow->update($request->all());

        return redirect(route('index'));
    }

    public function list_validateur_wkf($idproduit){

        try {
            $req                                                              = DB::select('SELECT sc_workflows.f_validateur_par_wkf(?)', [json_encode(['idproduit'=>$idproduit])]);
            $retourServeur                                                    = json_decode($req[0]->f_get_clients_submits_forms);
            return $retourServeur;
        } catch (\Throwable $e) {
            //return $e->getMessage();
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }

    }

    /**
    * Deletes the Workflow and over cascading also the Tasks, TaskLogs, WorkflowLogs and Triggers.
    *
    * @param $id
    * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    */
    public function delete($id)
    {
        $workflow                                                             = Workflow::find($id);

        $workflow->delete();

        return redirect(route('index'));
    }

    //Saisie de la tâche et des niveaux de validation du workflow
    public function addTask(Request $request)
    {
        //Utilisateur connecté qui mene l'action
        $userId                                                               = Auth::user()->id;

        //Validation des données postées
        $errors                                                               = [
            'workflow_id'                                                     => 'required',
            'utilisateur'                                                     => 'required',
            'niveau'                                                          => 'required',
            'tache'                                                           => 'required'
        ];
        $erreurs                                                              = [
            'workflow_id.required'                                            => 'Veuillez sélectionner le workflow',
            'utilisateur.required'                                            => 'Veuillez sélectionnez le validateur',
            'niveau.required'                                                 => 'veuillez saisir le niveau de validation',
            'tache.required'                                                  => 'veuillez saisir la t$aches'
        ];

        $validation                                                           = Validator::make($request->all(),$errors, $erreurs);

        if( !$validation->fails() ){

            try {

                DB::beginTransaction();
                //Enregistrement des données dans la table tasks
                $insertion                                                    = Taches::create([
                    'workflow_id'                                             => $request->workflow_id,
                    'name'                                                    => $request->tache,
                    'r_formulaire'                                            => $request->formulaire,
                    'r_creer_par'                                             => $userId,
                ]);

                //Enregistrement des données dans la table t_niveau_validation

                Niveau_validation::create([
                    'r_task'                                                  => $insertion->id,
                    'r_nom_niveau'                                            => $request->niveau,
                    'r_utilisateur'                                           => $request->utilisateur,
                    'r_rang'                                                  => $request->r_rang,
                ]);

                DB::commit();

                // Retour du serveur
                return $this->responseSuccess('La tâche à bien été ajouté');

            } catch (\Throwable $e) {
                DB::rollback();
                return $e->getMessage();
            }


        }else{
            return $validation->errors();
        }

    }

    public function modifTask(Request $request)
    {
        //Utilisateur connecté qui mene l'action
        $userId                                                               = Auth::user()->id;

        //Validation des données postées
        $errors                                                               = [
            'utilisateur'                                                     => 'required',
            'niveau'                                                          => 'required',
            'tache'                                                           => 'required'
        ];
        $erreurs                                                              = [
            'utilisateur.required'                                            => 'Veuillez sélectionnez le validateur',
            'niveau.required'                                                 => 'veuillez saisir le niveau de validation',
            'tache.required'                                                  => 'veuillez saisir la t$aches'
        ];

        $validation                                                           = Validator::make($request->all(),$errors, $erreurs);

        if( !$validation->fails() ){

            try {

                DB::beginTransaction();
                //Modifier des données dans la table tasks
                $check = Taches::find($request->idtask);
                $check->update([
                    'name'                                                    => $request->tache,
                    'r_rang'                                                   => $request->r_rang,
                ]);

                //Modifier des données dans la table t_niveau_validation
                $checknv = Niveau_validation::where('r_task',$request->idtask)->first();

                $checknv->update([
                    'r_nom_niveau'                                            => $request->niveau,
                    'r_rang'                                                  => $request->r_rang,
                    'r_utilisateur'                                            => $request->utilisateur
                ]);

                DB::commit();

                // Retour du serveur
                return $this->responseSuccess('La modification a bien été effectuée', $request->utilisateur);

            } catch (\Throwable $e) {
                DB::rollback();
                return $e->getMessage();
            }


        }else{
            return $validation->errors();
        }

    }

    public function listTache(int $idworkflow){

        try {

            $taches = Taches::select('tasks.id as idtask','tasks.name as tache', 'niv_vald.r_utilisateur as validateur_id',
            'niv_vald.r_nom_niveau','niv_vald.r_rang', 'users.name as name', 'users.lastname as lastname', 'fm.r_nom',
            'fm.id as form_id', 'tasks.workflow_id as workflow_id')
            ->join('t_niveau_validations as niv_vald', 'tasks.id', '=', 'niv_vald.r_task' )
            ->join('users', 'users.id', '=', 'niv_vald.r_utilisateur' )
            ->join('t_formulaires as fm', 'fm.id', '=','tasks.r_formulaire')
            ->where('workflow_id', $idworkflow)
            ->orderBy('niv_vald.r_rang', 'asc')
            ->get();

            return $taches;

        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
    * Validation des différents étapes du workflows
    */
    public function validate_workflows(Request $request){

        //$datas                                                              = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs                                                               = $this->decryptData($request->p_data);


        $errors                                                               = [
            'idforms_saisi'                                                   => 'required',
            'idformulaire'                                                    => 'required',
            'idclient'                                                        => 'required',
            'id_niv_validation'                                               => 'required',// l'identifiant du niveau de validation
        ];
        $erreurs                                                              = [
            'idforms_saisi.required'                                          => 'Référence donnée non définie',
            'idformulaire.required'                                           => 'Formulaire non définie',
            'idclient.required'                                               => 'Client non définie',
            'id_niv_validation.required'                                      => 'Référence validation non définie',
        ];
        // controlle des champs
        $validation                                                           = Validator::make($inputs, $errors, $erreurs);

        if( !$validation->fails() ){

            try {

                DB::beginTransaction();

                $validateForms                                                = ValidationForms::create([
                    'r_formulaire_saisi'                                      => $inputs['idforms_saisi'],
                    'r_formulaire'                                            => $inputs['idformulaire'],
                    'r_client'                                                => $inputs['idclient'],
                    'r_niveau_validation'                                     => $inputs['id_niv_validation'],
                    'r_status'                                                => $inputs['r_validation_valeur'],
                    'r_commentaire'                                           => $inputs['r_commentaire'],
                    'r_validateur'                                            => $inputs['id_validateur']
                ]);


                try {

                    $checkFormSaisi                                               =  FormulaireSaisie::find($validateForms->r_formulaire_saisi);
                    $checkFormSaisi->update([
                        'r_status'                                                => $inputs['r_validation_valeur']
                    ]);



                    switch ($checkFormSaisi->r_status ) {

                        case 2:
                            //Mise à true si tout les étapes de validation ont étés approuvées

                            //Récupère le nombre total de validation pour un formulaire soumit
                            $totalValidation                                          = ValidationForms::select('t_validations_forms.id')
                            ->join('t_formulaire_saisi', 't_formulaire_saisi.id', 't_validations_forms.r_formulaire_saisi')
                            ->where('t_validations_forms.r_client', $inputs['idclient'])
                            ->where('t_validations_forms.r_formulaire', $inputs['idformulaire'])
                            ->where('t_validations_forms.r_status', 2)
                            ->where('t_formulaire_saisi.r_reference', $checkFormSaisi->r_reference)
                            ->count();

                            // Récupère le nombre total de validation pour un workflow
                            $total_a_valider                                          = $this->get_nbre_validation($inputs['idformulaire']);

                            //return $totalValidation;

                            // Termine la vadidation de workflows
                            if( $totalValidation                                      == $total_a_valider ){

                                // Valider le formulaire saisie
                                $checkFS                                              =  FormulaireSaisie::find($validateForms->r_formulaire_saisi);
                                $checkFS->update([
                                    'r_validate'                                      => true
                                ]);

                                //Récupérer de l'id du dernier formulaire d'un produit
                                $checkLastFormProduct                                 = Forms::select('id')
                                ->where('r_produit', $inputs['r_product'])
                                ->orderBy('id', 'DESC')
                                ->limit(1)
                                ->first();

                                if ( $checkLastFormProduct->id                        == $inputs['idformulaire']) {
                                    //Terminer le workflow
                                    Triggers::where('r_reference',$checkFS->r_reference)->update([
                                        'r_status'                                    => 1
                                    ]);


                                    //Envoi de mail au client après la dernière validation

                                    $client = Client::find($inputs['idclient'])->first();

                                    $data = [
                                        'titre'                                     => 'Demande',
                                        'message' =>'Votre demande à été validée avec succès',
                                        'email' => $client->r_email];

                                        try {
                                            $sendMail                                         = new SendNotifyController();
                                            $sendMail->sendMessageGoogle( new Request($data) );
                                        } catch (\Throwable $e) {
                                            return $this->crypt($this->responseCatchError( $e->getMessage()));
                                        }

                                    }

                                }
                                $response                                                 = $this->crypt($this->responseSuccess('Workflows validé avec succès'));

                                break;

                                case 3:
                                    $checkFS                                              =  FormulaireSaisie::find($validateForms->r_formulaire_saisi);
                                    $checkFS->update([
                                        'r_validate'                                      => false
                                    ]);

                                    Triggers::where('r_reference',$checkFS->r_reference)->update([
                                        'r_status'                                    => 2
                                    ]);
                                    $response                                                 = $this->crypt($this->responseSuccess('Formulaire Rejeté'));

                                    break;

                                    default:
                                    $check                                               =  FormulaireSaisie::find($validateForms->r_formulaire_saisi);

                                    //$checkFormSaisi                                               =  FormulaireSaisie::where('id',$validateForms->r_formulaire_saisi)->first();
                                    $check->update([
                                        'r_status'                                                => $inputs['r_validation_valeur']
                                    ]);
                                    Triggers::where('r_reference',$check->r_reference)->update([
                                        'r_status'                                    => 3
                                    ]);
                                    $response                                                 = $this->crypt($this->responseValidation('Workflows Rejeté'));

                                    break;
                                }

                            } catch (\Throwable $e) {
                                //return $e->getMessage();
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }

                            DB::commit();

                            //Envoi de mail---------------------------------------------------------------------------------------------------
                            try {

                                //Récupération du rang du validateur
                                $rangValidateur                                           = $this->get_validateur_par_wkfl($inputs['r_product'], $inputs['id_validateur']);

                                //Envoi du mail au validateur concerné
                                $req                                                      = DB::select('SELECT sc_workflows.f_validateur_par_wkf(?)', [json_encode(['idproduit'=>$inputs['r_product']])]);
                                $retourServeur                                            = json_decode($req[0]->f_validateur_par_wkf);
                                $validations                                              = $retourServeur->_result[0]->workflow_task;

                                foreach ($validations as $value) {

                                    if( $value->validateur_rang                           == $rangValidateur->r_rang + 1 ){
                                        $value->validateur_email;


                                        $data = [
                                            'titre'                                     => 'Demande',
                                            'message' =>'Vous avez une validation en attente, veuillez vous connecter à l\'application de gestion des workflows',
                                            'email' => $value->validateur_email];

                                            try {
                                                $sendMail                                         = new SendNotifyController();
                                                $sendMail->sendMessageGoogle( new Request($data) );
                                            } catch (\Throwable $e) {
                                                return $e->getMessage();
                                            }

                                        }

                                    }

                                } catch (\Throwable $e) {
                                    //return $e->getMessage();
                                    return $this->crypt($this->responseCatchError($e->getMessage()));
                                }
                                //Envoi de mail---------------------------------------------------------------------------------------------------


                                return $response;

                            } catch (\Throwable $e) {
                                DB::rollBack();
                                //return $e->getMessage();
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }

                        }else{
                            return $this->crypt($this->responseValidation('Avertissement lié au paramètres, voir le détails',$validation->errors()));

                        }

                    }




                    /**
                    * Liste des workflows validés
                    */
                    public function liste_workflows_valides(Request $request){

                        //$datas                                                              = $this->crypt($request->all()); //Ceci est pour mes tests
                        //return $datas;

                        //Décryptage des données récues
                        $inputs                                                               = $this->decryptData($request->p_data);
                        //return $inputs;

                        //Validation des données postées
                        $errors                                                               = [
                            'idproduit'                                                       => 'required'
                        ];
                        $erreurs                                                              = [
                            'idproduit.required'                                              => 'Veuillez sélectionner le produit'
                        ];

                        $validation                                                           = Validator::make($inputs,$errors, $erreurs);

                        if( !$validation->fails() ){

                            try {

                                $query                                                        = "SELECT sc_workflows.f_list_workflows_valides_par_produit(?)";

                                $workflows_list                                               = DB::select($query, [json_encode($inputs)]);

                                $resultat                                                     = $workflows_list[0]->f_list_workflows_valides_par_produit;

                                //return $this->crypt($this->responseSuccess('Liste des workflows en cours par produit', json_decode($resultat)));
                                return $this->crypt(json_decode($resultat));

                            } catch (\Throwable $e) {
                                //return $e->getMessage();
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }

                        }else{
                            return $this->crypt($this->responseValidation('Avertissement lié au paramètres d\'envoie, voir le détails',$validation->errors()));

                        }
                    }

                    /**
                    * Liste des workflows rejetés
                    */
                    public function liste_workflows_rejetes(Request $request){

                        //$datas                                                              = $this->crypt($request->all()); //Ceci est pour mes tests
                        //return $datas;

                        //Décryptage des données récues
                        $inputs                                                               = $this->decryptData($request->p_data);
                        //return $inputs;

                        //Validation des données postées
                        $errors                                                               = [
                            'idproduit'                                                       => 'required'
                        ];
                        $erreurs                                                              = [
                            'idproduit.required'                                              => 'Veuillez sélectionner le produit'
                        ];

                        $validation                                                           = Validator::make($inputs,$errors, $erreurs);

                        if( !$validation->fails() ){

                            try {

                                $query                                                        = "SELECT sc_workflows.f_list_workflows_rejetes_par_produit(?)";

                                $workflows_list                                               = DB::select($query, [json_encode($inputs)]);

                                $resultat                                                     = $workflows_list[0]->f_list_workflows_rejetes_par_produit;

                                return $this->crypt(json_decode($resultat));

                            } catch (\Throwable $e) {
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }

                        }else{
                            return $this->crypt($this->responseValidation('Avertissement lié au paramètres d\'envoie, voir le détails',$validation->errors()));

                        }
                    }

                    public function suivi_validation_wkf(Request $request){

                        //$datas                                                              = $this->crypt($request->all()); //Ceci est pour mes tests
                        //return $datas;

                        //Décryptage des données récues
                        $inputs                                                               = $this->decryptData($request->p_data);
                        //return $inputs;

                        $errors                                                               = [
                            'idproduit'                                                       => 'required',
                            'reference'                                                       => 'required',
                        ];
                        $erreurs                                                              = [
                            'idproduit.required'                                              => 'Produit non définie',
                            'reference.required'                                              => 'Réference non définie',
                        ];
                        // controlle des champs
                        $validation                                                           = Validator::make($inputs, $errors, $erreurs);

                        if( !$validation->fails() ){

                            try {
                                $req                                                          = DB::select('SELECT sc_workflows.f_suivi_validation_wkf(?)', [json_encode($inputs)]);
                                $retourServeur                                                = json_decode($req[0]->f_suivi_validation_wkf);
                                return $this->crypt($this->responseSuccess('Suivie des validations de workflows',$retourServeur->_result));
                            } catch (\Throwable $e) {
                                return $e->getMessage();
                                return $this->crypt($this->responseCatchError($e->getMessage()));
                            }

                        }else{
                            return $this->crypt($this->responseValidation('Erreur lié au paramètres d\'envoi', $validation->errors()));
                        }
                    }



                    public function destroy(int $id)
                    {
                        try {
                            //Recherche de la ligne
                            $check                                                          = Workflow::find($id);

                            $response                                                       = ( $check )? $check->delete() : $this->responseValidationForm('Ligne non trouvée');

                            return $response;

                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }

                    public function restore($id)
                    {
                        try {

                            Workflow::withTrashed()->find($id)->restore();
                            return $this->responseSuccess('Restoration effectuée avec succès');

                        } catch (\Throwable $e) {
                            return $e->getMessage();
                        }


                    }

                    public function restoreAll()
                    {
                        try {

                            Workflow::onlyTrashed()->restore();
                            return $this->responseSuccess('Les données ont bién étes restorées');

                        } catch (\Throwable $e) {
                            return $e->getMessage();
                        }


                    }

                    /******************************************************************************************************************************/
                    public function addTrigger($id, Request $request)
                    {
                        $workflow                                                             = Workflow::find($id);

                        if (array_key_exists($request->name, config('workflows.triggers.types'))) {
                            $trigger                                                          = config('workflows.triggers.types')[$request->name]::create([
                                'type'                                                        => config('workflows.triggers.types')[$request->name],
                                'workflow_id'                                                 => $workflow->id,
                                'name'                                                        => $request->name,
                                'data_fields'                                                 => null,
                                'pos_x'                                                       => $request->pos_x,
                                'pos_y'                                                       => $request->pos_y,
                            ]);
                        }

                        return [
                            'trigger'                                                         => $trigger,
                            'node_id'                                                         => $request->id,
                        ];
                    }

                    public function changeConditions($id, Request $request)
                    {
                        $workflow                                                             = Workflow::find($id);

                        if ($request->type                                                    == 'task') {
                            $element                                                          = $workflow->tasks->find($request->id);
                        }

                        if ($request->type                                                    == 'trigger') {
                            $element                                                          = $workflow->triggers->find($request->id);
                        }

                        $element->conditions                                                  = $request->data;
                        $element->save();

                        return $element;
                    }

                    public function changeValues($id, Request $request)
                    {
                        $workflow                                                             = Workflow::find($id);

                        if ($request->type                                                    == 'task') {
                            $element                                                          = $workflow->tasks->find($request->id);
                        }

                        if ($request->type                                                    == 'trigger') {
                            $element                                                          = $workflow->triggers->find($request->id);
                        }

                        $data                                                                 = [];

                        foreach ($request->data as $key                                       => $value) {
                            $path                                                             = explode('->', $key);
                            $data[$path[0]][$path[1]]                                         = $value;
                        }
                        $element->data_fields                                                 = $data;
                        $element->save();

                        return $element;
                    }

                    public function updateNodePosition($id, Request $request)
                    {
                        $element                                                              = $this->getElementByNode($id, $request->node);

                        $element->pos_x                                                       = $request->node['pos_x'];
                        $element->pos_y                                                       = $request->node['pos_y'];
                        $element->save();

                        return ['status'                                                      => 'success'];
                    }

                    public function getElementByNode($workflow_id, $node)
                    {
                        if ($node['data']['type']                                             == 'task') {
                            $element                                                          = Task::where('workflow_id', $workflow_id)->where('id', $node['data']['task_id'])->first();
                        }

                        if ($node['data']['type']                                             == 'trigger') {
                            $element                                                          = Trigger::where('workflow_id', $workflow_id)->where('id', $node['data']['trigger_id'])->first();
                        }

                        return $element;
                    }

                    public function addConnection($id, Request $request)
                    {
                        $workflow                                                             = Workflow::find($id);

                        if ($request->parent_element['data']['type']                          == 'trigger') {
                            $parentElement                                                    = Trigger::where('workflow_id', $workflow->id)->where('id', $request->parent_element['data']['trigger_id'])->first();
                        }
                        if ($request->parent_element['data']['type']                          == 'task') {
                            $parentElement                                                    = Task::where('workflow_id', $workflow->id)->where('id', $request->parent_element['data']['task_id'])->first();
                        }
                        if ($request->child_element['data']['type']                           == 'trigger') {
                            $childElement                                                     = Trigger::where('workflow_id', $workflow->id)->where('id', $request->child_element['data']['trigger_id'])->first();
                        }
                        if ($request->child_element['data']['type']                           == 'task') {
                            $childElement                                                     = Task::where('workflow_id', $workflow->id)->where('id', $request->child_element['data']['task_id'])->first();
                        }

                        $childElement->parentable_id                                          = $parentElement->id;
                        $childElement->parentable_type                                        = get_class($parentElement);

                        $childElement->save();

                        return ['status'                                                      => 'success'];
                    }

                    public function removeConnection($id, Request $request)
                    {
                        $workflow                                                             = Workflow::find($id);

                        $childTask                                                            = Task::where('workflow_id', $workflow->id)->where('node_id', $request->input_id)->first();

                        $childTask->parentable_id                                             = 0;
                        $childTask->parentable_type                                           = null;
                        $childTask->save();

                        return ['status'                                                      => 'success'];
                    }

                    public function removeTask($id, Request $request)
                    {
                        $workflow                                                             = Workflow::find($id);

                        $element                                                              = $this->getElementByNode($id, $request->node);

                        $element->delete();

                        return [
                            'status'                                                          => 'success',
                        ];
                    }

                    public function getElementSettings($id, Request $request)
                    {
                        $workflow                                                             = Workflow::find($id);

                        if ($request->type                                                    == 'task') {
                            $element                                                          = Task::where('workflow_id', $workflow->id)->where('id', $request->element_id)->first();
                        }
                        if ($request->type                                                    == 'trigger') {
                            $element                                                          = Trigger::where('workflow_id', $workflow->id)->where('id', $request->element_id)->first();
                        }

                        return view('workflows::layouts.settings_overlay', [
                            'element'                                                         => $element,
                        ]);
                    }

                    public function getElementConditions($id, Request $request)
                    {
                        $workflow                                                             = Workflow::find($id);

                        if ($request->type                                                    == 'task') {
                            $element                                                          = Task::where('workflow_id', $workflow->id)->where('id', $request->element_id)->first();
                        }
                        if ($request->type                                                    == 'trigger') {
                            $element                                                          = Trigger::where('workflow_id', $workflow->id)->where('id', $request->element_id)->first();
                        }

                        $filter                                                               = [];

                        foreach (config('workflows.data_resources') as $resourceName          => $resourceClass) {
                            $filter[$resourceName]                                            = $resourceClass::getValues($element, null, null);
                        }

                        return view('workflows::layouts.conditions_overlay', [
                            'element'                                                         => $element,
                            'conditions'                                                      => $element->conditions,
                            'allFilters'                                                      => $filter,
                        ]);
                    }

                    public function loadResourceIntelligence($id, Request $request)
                    {
                        $workflow                                                             = Workflow::find($id);

                        if ($request->type                                                    == 'task') {
                            $element                                                          = Task::where('workflow_id', $workflow->id)->where('id', $request->element_id)->first();
                        }
                        if ($request->type                                                    == 'trigger') {
                            $element                                                          = Trigger::where('workflow_id', $workflow->id)->where('id', $request->element_id)->first();
                        }

                        if (in_array($request->resource, config('workflows.data_resources'))) {
                            $className                                                        = $request->resource ?? 'the42coders\\Workflows\\DataBuses\\ValueResource';
                            $resource                                                         = new $className();
                            $html                                                             = $resource->loadResourceIntelligence($element, $request->value, $request->field_name);
                        }

                        return response()->json([
                            'html'                                                            => $html,
                            'id'                                                              => $request->field_name,
                        ]);
                    }

                    public function getLogs($id)
                    {
                        $workflow                                                             = Workflow::find($id);

                        $workflowLogs                                                         = $workflow->logs()->orderBy('start', 'desc')->get();
                        //TODO: get Pagination working

                        return view('workflows::layouts.logs_overlay', [
                            'workflowLogs'                                                    => $workflowLogs,
                        ]);
                    }

                    public function reRun($workflowLogId)
                    {
                        $log                                                                  = WorkflowLog::find($workflowLogId);

                        ReRunTrigger::startWorkflow($log);

                        return [
                            'status'                                                          => 'started',
                        ];
                    }

                    public function triggerButton(Request $request, $triggerId)
                    {
                        $trigger                                                              = Trigger::findOrFail($triggerId);
                        $className                                                            = $request->model_class;
                        $resource                                                             = new $className();

                        $model                                                                = $resource->find($request->model_id);

                        $trigger->start($model, []);

                        return redirect()->back()->with('sucess', 'Button Triggered a Workflow');
                    }
                }
