<?php

use App\Http\Controllers\Apply\Produits\DocumentController;
use App\Http\Controllers\SendNotifyController;
use App\Http\Controllers\testController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\WS\Auth\AuthController;
use App\Http\Controllers\WS\Clients\clientController;
use App\Http\Controllers\WS\Clients\TypeClientController;
use App\Http\Controllers\WS\Dash;
use App\Http\Controllers\WS\Formulaires\ChampsController;
use App\Http\Controllers\WS\Formulaires\FormulaireController;
use App\Http\Controllers\WS\Produits\DocumentController as ProduitsDocumentController;
use App\Http\Controllers\WS\Produits\produitController;
use App\Http\Controllers\WS\Workflows\FormulaireSaisieController;
use App\Models\Workflows\FormulaireSaisie;
use Illuminate\Support\Facades\Route;
/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::middleware('auth:sanctum')->group( function () {

    /**Supression intelligente et restoration des produits**/
    Route::get('produits/restore/{id}', [produitController::class,'restore']);
    Route::get('produits/restoreall', [produitController::class,'restoreAll']);

    /**Supression intelligente et restoration des formulaires**/
    Route::get('formulaires/restore/{id}', [FormulaireController::class,'restore']);
    Route::get('formulaires/restoreall', [FormulaireController::class,'restoreAll']);

    //Liste des formulaires par produits
    Route::post('getformbyproduct', [FormulaireController::class,'getformbyproduct']);

    Route::post('detail_forms_soumit', [FormulaireSaisieController::class,'detail_forms_soumit']);


    Route::post('validation_workflows', [WorkflowController::class,'validate_workflows']);
    Route::post('workflows_validate_list', [WorkflowController::class,'liste_workflows_valides']);
    Route::post('liste_workflows_rejetes', [WorkflowController::class,'liste_workflows_rejetes']);
    Route::post('suivi_validation_wkf', [WorkflowController::class,'suivi_validation_wkf']);

    /***********************************************************Champs*****************************************************************************/
    Route::post('champs_by_product', [ChampsController::class,'champs_by_product']);
    Route::get('champs/restore/{id}', [ChampsController::class,'restore']);
    Route::get('champs/restoreall', [ChampsController::class,'restoreAll']);

    Route::apiResources([
        /*Routes des produits et des formulaires*/
       'produits'          => produitController::class,
       'documents'          => ProduitsDocumentController::class,
       'formulaires'       => FormulaireController::class,
       'champs'            => ChampsController::class,

       /*Route pour la gestion des soumissin des formulaires*/
        'formulaire_saisi' => FormulaireSaisieController::class,

       /*Routes des utilisateurs et clients*/
        'utilisateurs'     => AuthController::class,
        'typeclients'      => TypeClientController::class,
        'clients'          => clientController::class,
    ]);

    /***************************Déconnexion utilisateur*************/
    Route::post('logout', [AuthController::class,'logout']);

    /***************************Déconnexion client*************/
    Route::post('client/logout', [clientController::class,'logout']);

    Route::post('forumulaire/clientsubmit', [FormulaireSaisieController::class,'clients_submits_forms']);
    Route::post('updatedemande', [FormulaireSaisieController::class,'updatedemande']);
    Route::post('forumulaire/getdata', [FormulaireSaisieController::class,'get_data_submit']);
    Route::post('forumulaire/list_soumission_precedent_par_demande', [FormulaireSaisieController::class,'list_soumission_precedent_par_demande']);

    Route::post('forumulaire/forminit', [FormulaireSaisieController::class,'get_first_forms_client_by_product']);
    Route::post('forumulaire/produits_demandes', [FormulaireSaisieController::class,'f_produits_demandes']);
    Route::post('forumulaire/consult_demande_en_cours', [FormulaireSaisieController::class,'demande_en_cours']);


    Route::post('uplodefile', [testController::class, 'uploadfile']);

    /************************************************************Tableau de bord*******************************************************************/
    Route::get('dashback', [Dash::class,'dashbackofficeweb']);
    Route::post('dashclient', [Dash::class,'dashClientWeb']);

    Route::get('listevalidateurs', [AuthController::class,'validateurs']);

});


/***************************login utilisateur*************/
Route::post('login', [AuthController::class,'login']);

Route::post('passwordchange', [AuthController::class,'passwordchange']);
Route::post('verifotp/{mode}', [AuthController::class,'verifotp']);

/***************************login client*****************/
Route::post('client/login', [clientController::class,'login']);
Route::post('client/register', [clientController::class,'registerCpte']);
Route::get('client/liste_cli_entp', [clientController::class,'liste_cli_entp']);

Route::get('client/typeclients', [TypeClientController::class,'index']);


Route::post('sendMessageGoogle', [SendNotifyController::class, 'sendMessageGoogle']);

Route::post('sendotp/{mode}', [AuthController::class,'sendOtp']);


/************************************************************Tableau de bord SC*******************************************************************/
Route::get('dashsc', [Dash::class,'dash_sc']);
