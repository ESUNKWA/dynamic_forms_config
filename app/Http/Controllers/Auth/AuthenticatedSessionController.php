<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\permissions\Permission;
use App\Models\permissions\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller

{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
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

            return view('auth.login');

        }catch(\Throwable $e){
            DB::rollBack();
            return $this->responseCatchError($e->getMessage());
        }


    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {

       if( $request->authenticate() == 2 ){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        throw ValidationException::withMessages([
            'email' => trans('Vous n\'êtes pas autorisé à vous connecter via cette interface'),
        ]);
       }else{
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
       }



    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
