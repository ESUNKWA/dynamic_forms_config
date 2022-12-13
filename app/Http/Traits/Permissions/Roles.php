<?php
    namespace App\Http\Traits\Permissions;
    use App\Models\permissions\Role;
    use App\Models\permissions\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Gestion dynimique des reponses HTTP
 */
trait Roles
{
    public function listRoles(){

        if ( Auth::user()->r_es_super_admin == true ) {
            $listeRoles = Role::all();
        }else{
            $listeRoles = Role::where('slug', '<>','super_admin')->get();
        }

        return $listeRoles;
    }

    /**
     * Renvoie les erreurs de validaion des formualires
     * @param $message
     * @param mixed $ErreursValidation
     */
    public function listePermissions(int $idrole){
        try {
            //$listePermissions = Permission::orderBy('name','asc')->get();
            $query = 'SELECT perm.* , rper.role_id, rper.permission_id,

                        (case
                            when rper.role_id is not null then true
                            when rper.role_id is null then false
                        end) as affected

                    from
                    sc_workflows.permissions perm
                    left join sc_workflows.roles_permissions rper on perm.id = rper.permission_id and rper.role_id = ?
                    order by perm.name asc';

            $listePermissions = DB::select($query, [$idrole]);
            return $listePermissions;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

    }

}
