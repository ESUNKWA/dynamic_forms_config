<?php

namespace App\Http\Controllers\Apply\Config;

use App\Models\Convention;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SendResponses;

class ConventionController extends Controller
{
    use SendResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
        $userId                                                  = Auth::user()->id;

        //Récupération des données postés par le client
        $inputs                                                  = $request->all();

        //Validation des données avant le stockage dans la base de données
        $errors                                                  = [
            'entreprise'                                         => 'required'
        ];
        $erreurs                                                 = [
            'entreprise.required'                                => 'Le nom de l\'entreprise est réquis'
        ];

        $inputsValidations                                       = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                // Enregistrement des données dans la base de données
                $dataSave                                        = Convention::create([
                    'r_entreprise'                               => $request->entreprise,
                    'r_description'                              => $request->r_description,
                    'r_produit'                                  => $request->produit,
                    'r_taux'                                     => $request->taux,
                    'r_mnt_min'                                  => $request->mntmin,
                    'r_mnt_max'                                  => $request->mntmax,
                    'r_creer_par'                                => $userId
                ]);

                return $this->responseSuccess('Convention enregistrée avec succès');


            } catch (\Throwable $e) {
                return $e->getMessage();
            }

        }else{

            return $inputsValidations->errors();

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Convention  $convention
     * @return \Illuminate\Http\Response
     */
    public function show(int $idproduit)
    {
        try {
            $conventions = Convention::orderBy('id','ASC')
            ->select('t_conventions.*', 't_entreprises.r_nom_entp')
                                        ->join('t_entreprises', 't_entreprises.id', 't_conventions.r_entreprise')
                                        ->where('r_produit', $idproduit)->get();
            return $conventions;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Convention  $convention
     * @return \Illuminate\Http\Response
     */
    public function edit(Convention $convention)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Convention  $convention
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $idconvention)
    {
        $userId                                                  = Auth::user()->id;

        //Récupération des données postés par le client
        $inputs                                                  = $request->all();

        //Validation des données avant le stockage dans la base de données
        $errors                                                  = [
            'entreprise'                                         => 'required'
        ];
        $erreurs                                                 = [
            'entreprise.required'                                => 'Le nom de l\'entreprise est réquis'
        ];

        $inputsValidations                                       = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            $check = Convention::find($idconvention);

            try {

                // Enregistrement des données dans la base de données
                $check->update([
                    'r_entreprise'                               => $request->entreprise,
                    'r_description'                              => $request->r_description,
                    'r_produit'                                  => $request->produit,
                    'r_taux'                                     => $request->taux,
                    'r_mnt_min'                                  => $request->mntmin,
                    'r_mnt_max'                                  => $request->mntmax,
                    'r_modifier_par'                             => $userId
                ]);

                return $this->responseSuccess('Modification enregistrée avec succès');


            } catch (\Throwable $e) {
                return $e->getMessage();
            }

        }else{

            return $inputsValidations->errors();

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Convention  $convention
     * @return \Illuminate\Http\Response
     */
    public function destroy(Convention $convention)
    {
        //
    }
}
