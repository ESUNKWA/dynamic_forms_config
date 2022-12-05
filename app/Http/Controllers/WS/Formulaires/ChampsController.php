<?php

namespace App\Http\Controllers\WS\Formulaires;

use App\Http\Controllers\Controller;
use App\Models\c;
use Illuminate\Http\Request;
use App\Models\Formulaires\Champs;
use App\Http\Traits\cryptData;
use App\Http\Traits\SendResponses;

class ChampsController extends Controller
{
    use SendResponses, cryptData;
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
        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs = $this->decryptData($request->p_data);
        //return $inputs;

        $fields = $inputs['p_champs'];

        try {

            foreach ($fields as $field) {

                // Enregistrement des données dans la base de données
                $dataSave = Champs::create([
                    'product' => $field['product'],
                    'field_label' => $field['field_label'],
                    'field_name' => $field['field_name'],
                    'field_options' => $field['field_options'],
                    'field_placeholder' => $field['field_placeholder'],
                    'field_type' => $field['field_type'],
                    'field_value' => $field['field_value'],
                    'length' => $field['length'],
                    'value_max' => $field['value_max'],
                    'value_min' => $field['value_min'],
                    'r_creer_par' => $field['r_creer_par']
                ]);

            }

            if( $dataSave->id ){
                return $this->crypt($this->responseSuccess('Enregistrement effectué avec succès'));
            }

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function show(c $c)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function edit(c $c)
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
    public function update(Request $request, int $idchamps)
    {
        //$datas = $this->crypt($request->all()); //Ceci est pour mes tests
        //return $datas;

        //Décryptage des données récues
        $inputs = $this->decryptData($request->p_data);
        //return $inputs;

        try {

            $check = Champs::find($idchamps);

            // Enregistrement des données dans la base de données
            $check->update([
                'product' => $inputs['product'],
                'field_label' => $inputs['field_label'],
                'field_name' => $inputs['field_name'],
                'field_options' => $inputs['field_options'],
                'field_placeholder' => $inputs['field_placeholder'],
                'field_type' => $inputs['field_type'],
                'field_value' => $inputs['field_value'],
                'length' => $inputs['length'],
                'value_max' => $inputs['value_max'],
                'value_min' => $inputs['value_min'],
                'r_modifier_par' => $inputs['r_creer_par'],
            ]);

            if( $check->id ){
                return $this->crypt($this->responseSuccess('Modification effectué avec succès'));
            }

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }
    }

    public function champs_by_product(Request $request){

        try {

            $listeChps = Champs::orderBy('id','ASC')
                            ->where('product', $request->idproduit)
                            ->get();
            $response = $this->crypt($this->responseSuccess('Liste des champs',$listeChps));
            return $response;

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\c  $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {

            $check = Champs::find($id);
            $check->delete();
             return $this->crypt($this->responseSuccess('Supression effectué avec succès'));

         } catch (\Throwable $e) {
             return $this->crypt($this->responseCatchError($e->getMessage()));
         }
    }


    public function restore($id)
    {
        try {

            Champs::withTrashed()->find($id)->restore();
            return $this->crypt($this->responseCatchError($this->responseSuccess('Restoration effectuée avec succès')));

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }


    }

    public function restoreAll()
    {
        try {

            Champs::onlyTrashed()->restore();
            return $this->crypt($this->responseSuccess('Les données ont bién étes restorées'));

        } catch (\Throwable $e) {
            return $this->crypt($this->responseCatchError($e->getMessage()));
        }


    }
}
