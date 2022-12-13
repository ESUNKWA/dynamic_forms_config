<?php

namespace App\Http\Controllers\Apply\Config;

use App\Http\Controllers\Controller;
use App\Http\Traits\SendResponses;
use App\Models\Client;
use App\Models\ClientEntreprise;
use App\Models\cr;
use App\Models\Entreprise;
use App\Models\TypeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EntrepriseController extends Controller
{
    use SendResponses;

    public function __construct(){
        $this->middleware('access')->only('index');
    }

    public function index()
    {
        try {

            $entreprises = Entreprise::select('t_entreprises.*','t_clients.r_nom','t_clients.r_prenoms','t_clients.r_telephone','t_clients.r_email','t_clients.r_type_client', 't_forme_juridiques.r_libelle')
                            ->orderBy('r_nom_entp', 'ASC')
                            ->join('t_forme_juridiques', 't_forme_juridiques.id', 't_entreprises.r_forme_juridique')
                            ->join('t_clients', 't_clients.id','t_entreprises.r_represantant')->get();

            $typeClients                                                      = TypeClient::orderBy('r_libelle', 'ASC')->get();

            $permissions                                       = PermissionRole(Auth::user()->r_role);

            return view('pages.entreprise', ['entreprises' => $entreprises, 'permissions'=> $permissions, 'typeClients' => $typeClients]);

        } catch (\Throwable $e) {

            return $e->getMessage();

        }
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
        $inputsentp = $request->entreprise;
        $inputscli = $request->represantant;

        try {

            DB::beginTransaction();

            //Insertion des données du représentant
            $inputscli['r_password'] = Hash::make($inputscli['r_telephone']);
            $insertclient = Client::create($inputscli);

            //Insertion des données de l'entreprise
            $domain_mail=explode('@', $inputsentp['r_email_entp']);
            $inputsentp['r_domain_email']= $domain_mail[count($domain_mail) -1];

            $inputsentp['r_represantant'] = $insertclient->id;
            $insertion = Entreprise::create($inputsentp);

            //Insertion relation client/entreprise
            $insert = ClientEntreprise::create([
                'r_entreprise' => $insertion->id,
                'r_client' => $insertclient->id
            ]);

            DB::commit();

            $response = $this->responseSuccess('Enregistrement effectué avec succès');
            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->responseCatchError($e->getMessage());

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function show(cr $cr)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function edit(cr $cr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, cr $cr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {
            //Recherche de la ligne
            $check                                                        = Entreprise::find($id);

            $response                                                     = ( $check )? $check->delete() : $this->responseValidationForm('Ligne non trouvée');

            return $response;

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function active_desactive( Request $request){

        //Recherche de la ligne
        $check                                                            = Entreprise::find($request->identreprise);

        $check->update([
            'r_status'                                                    => $request->r_status
        ]);

        if( $check->id ){
            if ( $check->r_status == 0) {
                return $this->responseSuccess('Désactivation effectuée avec succès');
            }else{
                return $this->responseSuccess('Activation effectuée avec succès');

            }         }

    }

    public function restore($id)
    {
        try {

            Entreprise::withTrashed()->find($id)->restore();
            return $this->responseSuccess('Restoration effectuée avec succès');

        } catch (\Throwable $e) {
            return $e->getMessage();
        }


    }

    public function restoreAll()
    {
        try {

            Entreprise::onlyTrashed()->restore();
            return $this->responseSuccess('Les données ont bién étes restorées');

        } catch (\Throwable $e) {
            return $e->getMessage();
        }


    }
}
