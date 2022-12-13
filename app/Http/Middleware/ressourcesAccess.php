<?php

namespace App\Http\Middleware;

use App\Models\permissions\RolesPermissions;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ressourcesAccess
{
    /**
    * Middleware pour la gestion des accès au ressources(pages)
    */
    public function handle(Request $request, Closure $next)
    {
        //Récupération du role de l'utilisateur
        if ( Auth::user()->r_es_super_admin == true) {
            return $next($request);
        }else{
            // Récupération de la page demandée ( en cours)
            $path = parse_url(url()->current())['path'];
            $resources = explode("/","$path");


            $uris = [];// Tableau devant contenir toutes les routes
            $uri = '/'.$resources[count($resources)-1];

            $permissions = RolesPermissions::select('permissions.uri')
            ->where('role_id', Auth::user()->r_role)
            ->join('permissions', 'permissions.id', 'roles_permissions.permission_id')
            ->get();

            // Récupération de l'uri
            foreach ($permissions as $route) {
                ( isset($route->uri) )? array_push($uris, $route->uri): null;
            }

            // Vérification et rédirection de l'url si l'url fait partir de la permission
            if ( in_array($uri, $uris) ) {
                return $next($request);
            }

            return redirect('/refused');

        }




    }
}
