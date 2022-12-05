<?php

use Illuminate\Support\Facades\Route;
use App\Models\permissions\RolesPermissions;

function PermissionRole($role)
{
    //dd($role);
    return RolesPermissions::select('permissions.slug')
            ->where('role_id', $role)
            ->join('permissions', 'permissions.id', 'roles_permissions.permission_id')
            ->get();
}

/**
 * Cette fonction renvoie la liste des route selon le préfix
 */
function routelist($routeprefix)
{
    $allRoutes = [];
    $path = \Illuminate\Support\Facades\Route::getRoutes();

        foreach ($path as $pathname) {

            $route = explode('/', $pathname->uri);

            if ( $route[0] == $routeprefix && $pathname->methods[0] == 'GET') {

                array_push($allRoutes, [$pathname->uri,$pathname->methods[0]]);

            }

        }

    return $allRoutes;
}
