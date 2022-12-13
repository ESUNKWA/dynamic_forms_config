<?php

namespace App\Providers;

use App\Http\Traits\Permissions\Roles;
use App\Http\Traits\Utilisateurs;
use App\Models\permissions\Role;
use App\Models\permissions\RolesPermissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    use Utilisateurs;
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerPolicies();

        /* define a superadmin user role */
        Gate::define('superadmin', function($user) {
            return $user->r_es_super_admin;
        });

        /* define a admin user role */
        Gate::define('isAdmin', function($user) {

            $role = Role::select('*')->where('id', $user->r_role)->first();

            if( $role->slug == 'admin'){
                return $role->name;
            }
         });

         /* define a manager user role */
         Gate::define('isManager', function($user) {
             return $user->r_role == 2;
         });

         /* define a user role */
         Gate::define('isUser', function($user) {
             return $user->r_role == 3;
         });

        // Gate::define('access-admin', function (UsersRoles $usersrole) {
        //     return $usersrole->user_id;
        // });
    }
}
