<?php

namespace App\Http\Controllers\Apply\Produits;

use App\Http\Controllers\Controller;
use App\Http\Traits\SendResponses;
use App\Models\DocProduit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    use SendResponses;
    /***Liste es documents relatifs à un produits */
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

    /**Saisie des documents produits*/
    public function store(Request $request)
    {
        $userId                                                             = Auth::user()->id;

        //Récupération des données postés par le client
        $inputs                                                             = $request->all();

        if( isset($inputs['document']) ){
            $this->fileName                                                 = time().'.'.$request->document->extension();
        }
        //Validation des données avant le stockage dans la base de données

        $errors                                                             = [
            'r_libelle'                                                 => 'required|min:2'
        ];
        $erreurs                                                            = [
            'r_libelle.required'                                        => 'Le libellé du document est réquis',
            'r_libelle.min'                                             => 'Veuillez saisir un libellé valide',
        ];

        $inputsValidations                                                  = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                // Enregistrement des données dans la base de données
                if( !isset($inputs['document']) ){
                    unset($inputs['document_path']);
                }else{
                    $inputs['document_path']                                    =  url('/').'/storage/images/docproduits/'.$this->fileName;
                }
                $inputs['r_created_by']                                     = $userId;
                $dataSave                                                   = DocProduit::create($inputs);

                // Stockage des images
                if( isset($inputs['document']) && trim($this->fileName) && $dataSave ){
                    $image                                                  = $request->document->storeAs(
                        'images/docproduits',
                        $this->fileName,
                        'public'
                    );
                }

                if( $dataSave->id ){
                    return $this->responseSuccess('Enregistrement effectué avec succès');
                }

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
     * @param  \App\Models\DocProduit  $docProduit
     * @return \Illuminate\Http\Response
     */
    public function show(int $idproduit)
    {
        try {

            $documents = DocProduit::where('r_produit', $idproduit)->get();

            return $this->responseSuccess('Liste des documents',$documents);

        } catch (\Throwable $e) {
            return $e->getMessage();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DocProduit  $docProduit
     * @return \Illuminate\Http\Response
     */
    public function edit(DocProduit $docProduit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DocProduit  $docProduit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $userId                                                             = Auth::user()->id;

        //Récupération des données postés par le client
        $inputs                                                             = $request->all();
        return $inputs;
        if( isset($inputs['document']) ){
            $this->fileName                                                 = time().'.'.$request->document->extension();
        }
        //Validation des données avant le stockage dans la base de données

        $errors                                                             = [
            'r_libelle'                                                 => 'required|min:2'
        ];
        $erreurs                                                            = [
            'r_libelle.required'                                        => 'Le libellé du document est réquis',
            'r_libelle.min'                                             => 'Veuillez saisir un libellé valide',
        ];

        $inputsValidations                                                  = Validator::make($inputs, $errors, $erreurs);

        if ( !$inputsValidations->fails() ) {

            try {

                // Enregistrement des données dans la base de données
                if( !isset($inputs['document']) ){
                    unset($inputs['document_path']);
                }else{
                    $inputs['document_path']                                    =  url('/').'/storage/images/docproduits/'.$this->fileName;
                }

                $check                                                      = DocProduit::find($id);
                $check->update($inputs);

                // Stockage des images
                if( isset($inputs['document']) && trim($this->fileName) && $check ){
                    $image                                                  = $request->document->storeAs(
                        'images/docproduits',
                        $this->fileName,
                        'public'
                    );
                }

                if( $check->id ){
                    return $this->responseSuccess('Modification effectuée avec succès');
                }

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
     * @param  \App\Models\DocProduit  $docProduit
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocProduit $docProduit)
    {
        //
    }
}
