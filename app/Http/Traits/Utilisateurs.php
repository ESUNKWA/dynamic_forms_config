<?php
    namespace App\Http\Traits;
    use Illuminate\Foundation\Auth\User;
/**
 * Gestion dynimique des reponses HTTP
 */
trait Utilisateurs
{
    public function listeUtilisateur(){
        $listeUtilisateurs = User::select('users.id', 'users.name', 'users.lastname', 'users.email', 'users.phone','users.r_status',
                                            'users.r_canal_cnx','roles.id as role_id', 'roles.name as role_name', 'roles.slug',
                                            'users.r_validateur_wkf', 'users.path_name')
                                 //->leftJoin('users_roles','users.id','=','users_roles.user_id')
                                 ->leftJoin('roles','roles.id','=','users.r_role')
                                  ->get();
        return $listeUtilisateurs;
    }

    public function listevalidateurs(){
        $listeUtilisateurs = User::select('users.id', 'users.name', 'users.lastname', 'users.email', 'users.phone','users.r_status',
                                            'users.r_canal_cnx','roles.id as role_id', 'roles.name as role_name', 'roles.slug')
                                 //->leftJoin('users_roles','users.id','=','users_roles.user_id')
                                 ->leftJoin('roles','roles.id','=','users.r_role')
                                 ->where('r_validateur_wkf', true)
                                  ->get();
        return $listeUtilisateurs;
    }
}
