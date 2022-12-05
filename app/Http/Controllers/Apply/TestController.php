<?php

namespace App\Http\Controllers\Apply;

use App\Http\Controllers\Apply\Utilsateurs\utilisateurController;
use App\Http\Controllers\Controller;
use App\Http\Traits\SendResponses;
use App\Models\permissions\Permission;
use App\Models\permissions\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    use SendResponses;
    public function creation_super_admin(){



        try{

            DB::beginTransaction();
            //On vérifie le nombre de ligne dans la table users
            $check                                                   = User::select('id')->count();

            //Si la table est vide alors on procède à la crétion rôle et du compte super admin
            if ( $check == 0 ) {

                //Création du role super admin
                $role                                                = new Role();
                $role->slug                                          = 'super_admin';
                $role->name                                          = 'Super administrateur';
                $role->save();

                //Création de la permission
                $permission                                                    = new Permission();
                $permission->slug                                              = 'all';
                $permission->name                                              = 'Tous les droits';
                $permission->uri                                              = '*';
                $permission->save();

                //Affectation du rôle à la permission
                $role->permissions()->attach($permission->id);

                //Création du compte du super admin
                $user                                                = User::create([
                    'name'                                           => 'DEKI',
                    'lastname'                                       => 'Kouadio Esunkwa Moïse',
                    'phone'                                          => '0000000000',
                    'email'                                          => 'superadmin@gmail.com',
                    'r_canal_cnx'                                    => 1,
                    'r_role'                                         =>$role->id,
                    'r_es_super_admin'                               =>true,
                    'r_status'                                       => 1,
                    'r_creer_par'                                    => 0,
                    'password'                                       => Hash::make('super_admin@2022'),
                ]);

            }

            DB::commit();

            return redirect('/login');

        }catch(\Throwable $e){
            DB::rollBack();
            return $this->responseCatchError($e->getMessage());
        }


    }
}
