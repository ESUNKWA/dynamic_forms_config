<?php
use App\Http\Controllers\Apply\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\Apply\MonprofilController;


use App\Http\Controllers\Apply\Dash\dashbordController;
use App\Http\Controllers\Apply\Permissions\RoleController;
use App\Http\Controllers\Apply\Produits\produitController;
use App\Http\Controllers\Apply\Config\ConventionController;
use App\Http\Controllers\Apply\Config\EntrepriseController;
use App\Http\Controllers\Apply\Config\TypeClientController;
use App\Http\Controllers\Apply\Formulaires\ChampsController;
use App\Http\Controllers\Apply\Formulaires\FormulaireController;
use App\Http\Controllers\Apply\Formulaires\TypeChampsController;
use App\Http\Controllers\Apply\Permissions\PermissionController;
use App\Http\Controllers\Apply\Utilsateurs\utilisateurController;

use App\Http\Controllers\Apply\Formulaires\GroupeChampsController;
use App\Http\Controllers\Apply\Permissions\PermissionController as permissions;
use App\Http\Controllers\Apply\Produits\DocumentController;

Route::view('/pages/slick', 'pages.slick');
Route::view('/pages/datatables', 'pages.datatables');
Route::view('/pages/blank', 'pages.blank');

//Première page
Route::get('/', function () {
    return view('/landing');
});

Route::get('/refused', function () {
    return view('pages.refused');
});

/* Mes routes */

//\the42coders\Workflows\Workflows::routes();
//\Workflows\Workflows::routes();

Route::group(['prefix' => 'test'], function () {
    Route::GET('/creation', [TestController::class, 'creation_super_admin']);
});

Route::post('passwordchange', [utilisateurController::class,'passwordchange']);

Route::group(['middleware' => ['auth']], function()
{
    Route::group(['prefix'=>'workflows'], function(){
        Route::get('/', [WorkflowController::class, 'index'])->name('index');
        Route::get('creation', [WorkflowController::class, 'create'])->name('creation');
        Route::post('store', [WorkflowController::class, 'store'])->name('store');
        Route::get('{id}', [WorkflowController::class, 'show'])->name('workflow.show');
        Route::get('{id}/edit', [WorkflowController::class, 'edit'])->name('workflow.edit');
        Route::get('{id}/delete', [WorkflowController::class, 'delete'])->name('workflow.delete');
        Route::post('{id}/update', [WorkflowController::class, 'update'])->name('workflow.update');

        Route::post('/addTask', [WorkflowController::class,'addTask'])->name('addTask');
        Route::post('/modiftask', [WorkflowController::class,'modifTask'])->name('modifTask');

        Route::get('/listTache/{idworkfl}', [WorkflowController::class,'listTache'])->name('listTache');

    });

    Route::group(['prefix' => 'permissions'], function(){
        Route::get('listepermission_role/{idrole}', [permissions::class,'listePermission']);
        Route::post('affecter_permission_role', [permissions::class,'affecter_permission_role']);
        Route::get('/getroute/{prefix}', [PermissionController::class,'routespermission']);
    });

    Route::group(['prefix' => 'champs'], function(){
        Route::get('/restore/{id}', [ChampsController::class,'restore']);
        Route::get('/restoreall', [ChampsController::class,'restoreAll']);
        Route::post('/active_desactive', [ChampsController::class,'active_desactive']);
        route::post('/champs_by_product', [ChampsController::class,'champs_by_product']);
        Route::get('/list_champs_by_product/{id}', [ChampsController::class,'list_champs_by_product']);
    });


    Route::group(['prefix' => 'produits'], function(){
        Route::get('/restore/{id}', [produitController::class,'restore']);
        Route::get('/restoreall', [produitController::class,'restoreAll']);
        Route::post('/active_desactive', [produitController::class,'active_desactive']);
        Route::post('/listeproduit', [produitController::class,'produitParTypeClient']);
    });

    Route::group(['prefix' => 'typeclient'], function(){
        Route::get('/restore/{id}', [TypeClientController::class,'restore']);
        Route::get('/restoreall', [TypeClientController::class,'restoreAll']);
        Route::post('/active_desactive', [TypeClientController::class,'active_desactive']);
    });

    Route::group(['prefix' => 'roles'], function(){
        route::post('/affecter_role_user', [RoleController::class,'affecter_role_user']);
        route::get('/historik_role/{iduser}', [RoleController::class,'historik_role_utilisateur']);
        Route::get('/restoreall', [RoleController::class,'restoreAll']);
    });

    Route::group(['prefix' => 'entreprise'], function(){
        Route::post('/active_desactive', [EntrepriseController::class,'active_desactive']);
        Route::get('/restoreall', [EntrepriseController::class,'restoreAll']);
    });


    Route::group(['prefix' => 'utilisateurs'], function(){
        Route::get('/validateurs', [utilisateurController::class,'validateurs']);
        Route::get('/listeutilisateurs', [utilisateurController::class,'utilisateurs']);
        Route::post('/affect_validateur', [utilisateurController::class,'affect_validateur']);
        route::post('/active_desactive/{id}', [utilisateurController::class,'active_desactive']);
    });


    Route::group(['prefix' => 'formulaires'], function(){
        Route::post('/formulaire_by_product', [FormulaireController::class,'formulaire_by_product']);
        Route::post('/active_desactive', [FormulaireController::class,'active_desactive']);
        Route::get('/restoreall', [FormulaireController::class,'restoreAll']);

        Route::get('/nom_formulaire_by_product/{idproduit}', [FormulaireController::class,'nom_formulaire_by_product']);

    });

    Route::get('monprofil', [MonprofilController::class,'index'])->name('monprofil');


    Route::resources([
        'accueil'          => dashbordController::class,
        'produits'         => produitController::class,
        'documents'         => DocumentController::class,


        'typechamps'       => TypeChampsController::class,
        'champs'           => ChampsController::class,
        'gpechamps'        => GroupeChampsController::class,
        'formulaires'      => FormulaireController::class,
        'utilisateurs'     => utilisateurController::class,

        'typeclient'       => TypeClientController::class,
        'convention'       => ConventionController::class,
        'entreprise'       => EntrepriseController::class,

        //Rôles
        'roles'            => RoleController::class,
        'permissions'      => permissions::class,
    ]);

});


require __DIR__.'/auth.php';
