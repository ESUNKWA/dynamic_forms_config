<?php

namespace App\Http\Controllers\Apply\Permissions;

use App\Models\c;
use App\Models\permissions\Roleshistoriques;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\permissions\Role;
use App\Models\permissions\UsersRoles;
use App\Http\Controllers\Controller;
use App\Http\Traits\Permissions\Roles;
use App\Http\Traits\SendResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    use Roles, SendResponses;
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function __construct(){
        $this->middleware('access')->only('index');
    }

    public function index( Request $request )
    {
        $listeRoles                                    = $this->listRoles();
        $permissions                                       = PermissionRole(Auth::user()->r_role);

        return view('pages.roles.roles', ['listeRoles' => $listeRoles, 'permissions'  => $permissions]);
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

        //Controlle des champs

        $errors = [
            'name' => 'required|unique:roles',
            'slug' => 'required|unique:roles',
        ];
        $erreurs = [
            'name.required' => 'Le nom du rôle est réquis',
            'name.unique' => 'Le nom du rôle déjà existant',

            'slug.required' => 'Le nom du slug est réquis',
            'slug.unique' => 'Le nom du slug déjà existant',
        ];

        $validationInputs = Validator::make($request->all(), $errors, $erreurs);

        if ( $validationInputs->fails() ) {

            return $validationInputs->errors();

        }else{
            try {

                $dev_role                                   = new Role();
                $dev_role->slug                             = $request->slug;
                $dev_role->name                             = $request->name;
                $dev_role->save();

                return $this->responseSuccess('Rôle enregistré avec succès');

            } catch (\Throwable $e) {
                return $e->getMessage();
            }
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
    public function update(Request $request, c $c)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\c  $c
    * @return \Illuminate\Http\Response
    */
    public function destroy(int $idrole)
    {
        $usersRole                                      = Role::find($idrole);
        $usersRole->delete();

        return ['_status' => 0, '_message' => 'Suppression effectuée avec succès'];
    }

    public function restoreAll()
    {
        try {

            Role::onlyTrashed()->restore();
            return $this->responseSuccess('Les données ont bién étes restorées');

        } catch (\Throwable $e) {
            return $e->getMessage();
        }


    }

    public function roles_user($id){

        $tab                                            = [];

        $users                                          = UsersRoles::where('user_id', $id)->get();

        foreach ($users as $value) {
            array_push($tab, $value->role_id);
        }

        $roles                                          = Role::whereIn('id',$tab)->get();

        return $roles;

    }

    public function delele_roles_user($iduser,$idrole){

        $usersRole                                      = UsersRoles::where('user_id', $iduser)
        ->where('role_id', $idrole)->first();

        $usersRole->delete();

        return 'ok';

    }

    //Affectation de rôle à un utilisateur
    public function affecter_role_user(Request $request){

        try {

            DB::beginTransaction();

            $role                                       = Role::where('slug',$request->role)->get();

            //Rechercher l'utilisateur
            $user                                       = User::find($request->user);
            $user->update(['r_role' => $role[0]->id]);// Ajout de l'id du role dans la tables des utilisateurs


            // Affecte un role à un utilisateur
            $user->roles()->attach($role);


            //Historiques des rôles utilisateurs

            $check = Roleshistoriques::where('r_utilisateur', $request->user)
            ->orderBy('id', 'DESC')
            ->take(1)
            ->first();

            if (isset($check)) {

            $check->update(['r_date_fin' => now()]);

            }

            $insertion = Roleshistoriques::create([
            'r_utilisateur' => $request->user,
            'r_role' => $role[0]->id,
            'r_date_debut' => now(),
            'r_date_fin' => null,
            'r_creer_par' => Auth::user()->id
            ]);

            DB::commit();

            return $this->responseSuccess('Le rôle a bien été affecté');

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->responseCatchError($e->getMessage());
        }


    }


    public function historik_role_utilisateur(int $id_utilisateur)
    {
        try {
            $check = Roleshistoriques::select('roles.name','t_historiques_users_roles.*')
                                  ->where('r_utilisateur', $id_utilisateur)
                                  ->join('users', 'users.id','t_historiques_users_roles.r_utilisateur')
                                  ->join('roles', 'roles.id','t_historiques_users_roles.r_role')
                                  ->get();
            return $check;
        } catch (\Throwable $e) {
            return $this->responseCatchError($e->getMessage());
        }

    }

}
