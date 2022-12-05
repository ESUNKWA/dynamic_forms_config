<?php
namespace App\Http\Controllers\Apply\Permissions;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\permissions\Role;
use App\Http\Traits\SendResponses;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Permissions\Roles;
use App\Models\permissions\Permission;
use App\Models\permissions\Roleshistoriques;
use App\Models\permissions\RolesPermissions;

class PermissionController extends Controller
{
    use SendResponses, Roles;

    public function __construct(){
        $this->middleware('access')->only('index');
    }
    public function index() {
        $listePermissions                                                    = $this->listePermissions(0);
        $permissions                                                         = PermissionRole(Auth::user()->r_role);

        return view('pages.roles.permission', [
            'Listepermissions'                                               => $listePermissions,
            'permissions'                                                    => $permissions]);
        }

        public function listePermission(int $idrole){
            $listePermissions                                                = $this->listePermissions($idrole);
            return $listePermissions;
        }

        /**
        * Cette fonction permet de saisir de nouvelle permissions
        */
        public function store(Request $request){
            try {

                $dev_role                                                    = new Permission();
                $dev_role->slug                                              = $request->slug;
                $dev_role->name                                              = $request->name;
                $dev_role->uri                                              = '/'.$request->uri;
                $dev_role->save();

                return $this->responseSuccess('Permission enregistrée avec succès');

            } catch (\Throwable $e) {
                return $e->getMessage();
            }
        }

        public function update(Request $request, int $id){
            $check = Permission::find($id);
            $check->update([
                'name' => $request->name,
                'uri' => '/'.$request->uri
            ]);
            return $this->responseSuccess('Modification effectuée avec succès');
        }
        /**
        * Saisir un nouveau role et affecter des permission
        */
        public function Permission()
        {

            // Rechercche la permission à affecté
            $permission                                                      = Permission::where('slug','creer-utilisateurs')->first();

            // Enregistrer le new role : cas 1 - New rôle
            /* $dev_role                                                     = new Role();
            $dev_role->slug                                                  = 'secretaire';
            $dev_role->name                                                  = 'Sécrétaire de direction';
            $dev_role->save(); */

            // Cas 2: rôle déjà existant
            $role                                                            = Role::where('slug','admin')->first();


            // Affecte le role aux permissions
            $role->permissions()->attach($permission);

            //Rechercher l'utilisateur
            $user                                                            = User::find(3);

            // Affecte un role à un utilisateur
            $user->roles()->attach($role);

            // Affecte permissions aux utilisateurs
            $user->permissions()->attach($permission);

            return 10;
            //return redirect()->back();
        }

        public function affecter_role_user($user, $slug){

            try {
                DB::beginTransaction();

                $role                                                        = Role::where('slug',$slug)->first();
                //Rechercher l'utilisateur
                $user                                                        = User::find($user);
                // Affecte un role à un utilisateur
                $user->roles()->attach($role);

                //Historiques des rôles utilisateurs

                $check                                                       = Roleshistoriques::where('r_utilisateur', $user)
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->first();

                if ($check) {

                    $check->update(['r_date_fin'                             => now()]);

                }

                $insertion                                                   = Roleshistoriques::create([
                    'r_utilisateur'                                          => $user,
                    'r_role'                                                 => $role,
                    'r_date_debut'                                           => now(),
                    'r_date_fin'                                             => null,
                    'r_creer_par'                                            => Auth::user()->id
                ]);

                DB::commit();

                return $this->responseSuccess('Le rôle a bien été affecté');


            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->responseValidation('Une erreur est survenue', $e->getMessage());
            }


        }

        public function affecter_role_user_new(Request $request){

            $role                                                            = Role::where('slug',$request->role)->first();

            //Rechercher l'utilisateur
            $user                                                            = User::find($request->user);
            // Affecte un role à un utilisateur
            $user->roles()->attach($role);
        }

        public function affecter_permission_role( Request $request ){

            $inputs                                                          = $request->data;

            try {

                //Supression de la config précédente
                $check                                                       = RolesPermissions::whereIn('role_id', [$request->idrole])->delete();

                if( isset($inputs) ){
                    foreach ($inputs as $value) {

                        // Rechercche la permission à affecté
                        $permission                                          = Permission::where('slug',$value['permission'])->first();
                        // Cas 2: rôle déjà existant
                        $role                                                = Role::where('slug',$value['role'])->first();
                        // Affecte le role aux permissions
                        $role->permissions()->attach($permission);

                    }
                }

                return $this->responseSuccess('enregistré avec succès');


            } catch (\Throwable $e) {

                return $e->getMessage();

            }


        }

        public function affecter_permission_user( Request $request ){

            $permission                                                      = Permission::where('slug', $request->permission)->first();

            //Rechercher l'utilisateur
            $user                                                            = User::find($request->user);

            // Affecte permissions aux utilisateurs
            $user->permissions()->attach($permission);
        }

        public  function routespermission(string $prefix){
            $routes                                                              = routelist($prefix);
            return $routes;
        }
    }
