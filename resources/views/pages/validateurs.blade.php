@extends('layouts.backend')

@section('css_before')
<!-- Page JS Plugins CSS -->
<link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
@endsection

@section('js_after')

<!-- jQuery (required for DataTables plugin) -->
<script src="{{ asset('js/lib/jquery.min.js') }}"></script>

<!-- Page JS Plugins -->
<script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables-buttons-jszip/jszip.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables-buttons-pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables-buttons-pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('js/plugins/datatables-buttons/buttons.print.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables-buttons/buttons.html5.min.js') }}"></script>

<!-- Page JS Code -->
<script src="{{ asset('js/pages/tables_datatables.js') }}"></script>

<script>

    //Cette fonction est exécuté au chargement de la page
    $(function(){

        let datas, tab                                                                   = [];

        $title                                                                           = $('#staticBackdropLabel').text('Saisir un nouveau utilisateur');
        $('#btnModif').hide();
        $('#btnRegister').show();

                //New form
                $('#def_validateur').on('click', function(){
                    //Rédefinition du titre de la modal
                    $('#staticBackdropLabel').empty();
                    $title                                                               = $('#staticBackdropLabel').text(`Définition des validateurs`);
                    $('#btnModif').hide();
                    $('#btnRegister').show();

                    $.ajax({
                        type:"GET",
                        url: `{{ url('/utilisateurs/listeutilisateurs') }}`,
                        data: { "_token": "{{ csrf_token() }}"},
                        dataType: 'json',
                        success: function(res){
                            console.log(res);
                            res.forEach((el) => {
                                ( el.r_validateur_wkf == true )? tab.push(el.id): null;
                            });
                            affichevalidateurs(res);
                        }

                    });

                });

                    //Choix des permission à affecter à un role
                    $('body').on('click', '.choosevalidateurs', function () {

                        let obj                                                          = {};
                        //Récupération des détails de la ligne en cours de modification
                        dkem                                                             = $(this).data('id');

                        if ( $(this).is(':checked')                                      == true) {
                            tab.push(dkem.id);
                        }else{
                            tab                                                          = tab.filter((el) => el !== dkem.id);
                        }

                    });

                    //////
                    let affichevalidateurs                                               = (datas)=>{
                        $('#validateurtable').empty();
                        datas.forEach((item, index)                                      => {

                            $("#validateurtable").append(`
                            <tr>
                                <td class="fw-semibold">
                                    <input class="form-check-input choosevalidateurs" id='dkem${item.id}' type="checkbox" data-id='${JSON.stringify(item)}'>
                                </td>
                                <td class="fw-semibold">${item.name}</td>
                                <td class="fw-semibold">${item.lastname}</td>
                                <td class="fw-semibold">${item.role_name}</td>
                            </tr>`);
                            $(`#dkem${item.id}`).prop('checked', item.r_validateur_wkf);
                        });
                    };




                    // Définition des validateurs
                    $('#btnvalidateurs').on('click', function() {
                        // ajax
                        $.ajax({
                            type:"POST",
                            url: `{{ url('/utilisateurs/affect_validateur') }}`,
                            data: {
                                "_token": "{{ csrf_token() }}",
                                users: tab
                            },
                            dataType: 'json',
                            success: function(res){

                                Swal.fire(res._message, '', 'success')

                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);

                            }
                        });
                    });

                    // End
                });


            </script>

            @endsection



            @section('content')
            <!-- breadcrumb -->
            @include('layouts.main.breadcrumb', [
            'titre'                                                                      => 'Validateurs',
            'soustitre'                                                                  => 'Gestion des Validateurs',
            'chemin'                                                                     => "Workflows"
            ])
            <!-- END Hero -->

            <!-- Page Content -->
            <div class="content">

                <!---------------------- Début Modal pour la saisie, la modification et la consultation des produits---------------------------------->
                <!-- Button trigger modal -->
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-primary mb-2" id="def_validateur" title="Saisir un nouvel utilisateur" style="float: right;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                            Nouveau
                        </button>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"><span class="visually-hidden">Close</span></button>
                            </div>
                            <hr>
                            <div class="modal-body">

                                <table class="table table-bordered table-striped table-vcenter fs-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">Est validateur</th>
                                            <th>Nom</th>
                                            <th>Prenoms</th>
                                            <th>Rôle</th>
                                        </tr>
                                    </thead>
                                    <tbody id="validateurtable" >

                                    </tbody>
                                </table>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button class="btn btn-primary" id="btnvalidateurs" >Enregistrer</button>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                <!---------------------- Fin Modal pour la saisie, la modification et la consultation des produits---------------------------------->

                <!---------------------- Début Modal pour l'affectation des roles---------------------------------->

                <!-- Modal -->
                <div class="modal fade" id="modalSetRole" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalSetRoleTitle"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"><span class="visually-hidden">Close</span></button>
                            </div>
                            <hr>
                            <div class="modal-body">

                                <form id="myForm"></form>

                                <table class="table table-bordered table-striped table-vcenter fs-sm mt-2" >

                                    <thead>
                                        <tr>
                                            <th>Choix</th>
                                            <th>Permission</th>
                                            <th>Identifiant(slug)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="permissionstable"></tbody>
                                </table>

                                <!-- Affichage des erreur de validation du formualires -->
                                <div class="alert alert-danger" role="alert" id="afficheErrors" hidden>
                                    <span class="alert-icon"><span class="visually-hidden">Error</span></span>
                                    <ol id="warning" ></ol>
                                </div>
                                <!-- Fin Affichage des erreur de validation du formualires -->

                                <!-- Affichage des message de succès validation du formualires -->
                                <div class="alert alert-success" role="alert" id="afficheSuccess" hidden >
                                    <span class="alert-icon"><span class="visually-hidden">Success</span></span>
                                    <h6 id="successMsg" ></h6>
                                </div>
                                <!-- Fin Affichage des message de succès validation du formualires -->

                                <!-- Formulaire de saisie des produits -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button class="btn btn-primary" id="btnAffectRole" >Enregistrer</button>
                                </div>
                                <!-- Fin Formulaire de saisie des produits -->

                            </div>

                        </div>
                    </div>
                </div>
                <!---------------------- Fin Modal pour l'affectation des rôles---------------------------------->


                <!---------------------- Début Affichage des produits dans la table---------------------------------->

                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h6>
                            <small>Liste des validateurs</small>
                        </h6>
                    </div>
                    <div class="block-content block-content-full">
                        <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/tables_datatables.js -->
                        <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prenoms</th>
                                    <th>Rôle</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>



                                @foreach ($utilisateurs as $utilisateur)
                                <tr>
                                    <td class="fw-semibold">{{ $utilisateur->name }}</td>
                                    <td class="d-none d-sm-table-cell">{{ $utilisateur->lastname }}</td>
                                    <td class="d-none d-sm-table-cell fw-bold text-danger">{{ $utilisateur->role_name }}</td>
                                    <td class="text-muted">

                                        @switch($utilisateur->r_status)
                                        @case(1)
                                            <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Actif
                                                &nbsp;<i class="fa fa-check"></i>
                                            </span>
                                        @break

                                        @default
                                            <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-danger-light text-danger">Inactif
                                                &nbsp;<i class="fa fa-lock"></i>
                                            </span>

                                        @endswitch

                                    </td>


                                </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!---------------------- Fin Affichage des produits dans la table---------------------------------->




        </div>
        <!-- END Page Content -->
        @endsection
