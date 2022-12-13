<?php

namespace App\Http\Controllers\Apply;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonprofilController extends Controller
{
    public function index(){
        $permissions                                       = PermissionRole(Auth::user()->r_role);
        return view('pages.monprofil', ['permissions'  => $permissions]);
    }
}
