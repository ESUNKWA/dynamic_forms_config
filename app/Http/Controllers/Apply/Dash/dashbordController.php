<?php

namespace App\Http\Controllers\Apply\Dash;

use App\Http\Controllers\Controller;
use App\Models\crR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class dashbordController extends Controller
{

    public function __construct(){
        $this->middleware('access')->only('index');
    }

    /***Tableau de bord***/
    public function index()
    {


        $permissions                                       = PermissionRole(Auth::user()->r_role);

        return view('pages.dashboard', ['permissions'  => $permissions]);

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\crR  $crR
     * @return \Illuminate\Http\Response
     */
    public function show(crR $crR)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\crR  $crR
     * @return \Illuminate\Http\Response
     */
    public function edit(crR $crR)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\crR  $crR
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, crR $crR)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\crR  $crR
     * @return \Illuminate\Http\Response
     */
    public function destroy(crR $crR)
    {
        //
    }
}
