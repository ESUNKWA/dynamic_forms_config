<?php

namespace App\Http\Controllers\WS;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Workflows\FormulaireSaisieValeur;

class filesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Uplode files.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($files, int $idformsaisi)
    {


        return $files;

        $ext = $files->r_fichier->extension();

        try {

            $fileName                                               = time().'.'.$ext;
                //return $fileName;

                FormulaireSaisieValeur::create([
                    'r_formulaire_saisi' => $idformsaisi,
                    'r_champs'           => $files->idchamps,
                    'r_valeur'           => url('/').'/images/test/'.$fileName,
                ]);

                $image                                      = url('/').'/storage/images/docs_clients/'.$fileName;


                return $image;

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function show(File $c)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function edit(File $c)
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
    public function update(Request $request, File $c)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $c)
    {
        //
    }
}
