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
    $(function(){

        let dataSend = {}, detailWorkf = {}, nbreTache = 0, task = {};

        $('#btnModif').hide();
        $('#btnRegister').show();

        $('body').on('click','.newCreateTask', function(){

            detailWorkf                                            = $(this).data('id');
            $("#tacheForms")[0].reset();
            $('#titre').empty();
            $('#titre').append(`Saisie des tâches du [ ${detailWorkf.name} ]`);

            $.ajax({
                url: ` {{ url('/formulaires/nom_formulaire_by_product') }}/${detailWorkf.r_produit}`,
                type: "GET",
                data: dataSend,
                success:function(response){

                    $('#formWkf').empty();
                    response.forEach((item, i)=>{
                        $('#formWkf').append(`
                        <option value="${item.id}" >${item.r_nom}</option>`
                        );
                    });
                    //f_afficheTaches(response);

                },
                error: function(response) {
                    console.log(response);

                }
            });

            $.ajax({
                url: ` {{ url('workflows/listTache') }}/${detailWorkf.id}`,
                type: "GET",
                data: dataSend,
                success:function(response){
                    console.log(response);
                    nbreTache = response.length;
                    $('#r_rang').val(nbreTache + 1);
                    f_afficheTaches(response);
                },
                error: function(response) {
                    console.log(response);

                }
            });

            $('#task_niveau').modal('show');
        });

        //-----------------------------------------------
        let f_afficheTaches                                        = (datas)=>{
            $("#tachesWorkf").empty();
            datas.forEach((item, index)                            => {
                $("#tachesWorkf").append(`<tr>
                    <td class="fw-semibold">${item.r_nom_niveau}</td>
                    <td class="fw-semibold">${item.r_nom}</td>
                    <td class="fw-semibold">${item.tache}</td>
                    <td class="fw-semibold">${item.name} ${item.lastname}</td>
                    <td class="fw-semibold">
                        <input type="number" min="1" class="form-control" readonly value="${item.r_rang}" aria-describedby="emailHelp">
                    </td>
                    <td class="fw-semibold">
                        <button class="btn btn-primary sm edittask" data-task='${JSON.stringify(item)}'>
                            <i class="far fa-pen-to-square"></i>
                        </button>
                    </td>
                </tr>`);
            });
        };

        //Enregistrer les tâches
        $('#btnRegister').on('click',function(e){
            e.preventDefault();

            //Récupération des données à poste
            dataSend                                               = {
                "_token": "{{ csrf_token() }}",
                workflow_id: detailWorkf.id,
                niveau: $('#niveau').val(),
                utilisateur: parseInt($('#utilisateur').val(),10),
                formulaire: parseInt($('#formWkf').val(),10),
                tache: $('#tache').val(),
                r_rang: $('#r_rang').val()
            };

            // Controlle des champs et validation du formulaire avant soumission

            if ( dataSend.niveau.trim()                            == "") {
                $('#erreur_niveau').empty();
                $('#erreur_niveau').append('Veuillez saisir le niveau de validation du workflows');
                return;
            }else{
                $('#erreur_niveau').empty();
            }

            if ( $('#utilisateur').val()                           == 0) {
                $('#erreur_utilisateur').empty();
                $('#erreur_utilisateur').append(`Veuillez sélectionner l'utilisateur`);
                return;
            }else{
                $('#erreur_utilisateur').empty();
            }

            if ( dataSend.tache.trim()                             == "") {
                $('#erreur_tache').empty();
                $('#erreur_tache').append(`Veuillez saisir la tâche à éffectuer au ${dataSend.niveau}`);
                return;
            }else{
                $('#erreur_tache').empty();
            }


            $('.spinnerRegister').removeAttr('hidden');

            $.ajax({
                url: "{{ url('/workflows/addTask') }}",
                type: "POST",
                data: dataSend,
                success:function(response){

                    $('.spinnerRegister').attr('hidden', true);

                    if( response._status                           == 1 ){

                        //Réinitialisation du formualaires
                        $("#tacheForms")[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="">Tâches</a>'
                        });

                        setTimeout(()                              => {

                            $.ajax({
                                url: `{{ url('/workflows/listTache') }}/${detailWorkf.id}`,
                                type: "GET",
                                data: dataSend,
                                success:function(response){
                                    nbreTache = response.length;
                                    f_afficheTaches(response);

                                },
                                error: function(response) {
                                    console.log(response);

                                }
                            });

                        }, 1000);


                    }else{
                        //Convertion du retourn objet et tableau
                        let warning                                = Object.values(response).flat();

                        //Ajoute dans erreurs dans la liste pour affichage
                        for (let index                         = 0; index < warning.length; index++) {
                            const element                          = warning[index];
                            $("#warning").append(`<li class="text-primary m-1" >${element}</li>`);
                            //Supression de l'attribut <hidden> pour afficher les erreurs de validaion du formulaire
                                $('#afficheErrors').removeAttr( "hidden" );
                            }
                        }

                    },
                    error: function(response) {
                        console.log(response);

                    }
                });


            });

            //Affichage du détail des tâches en vue d'une modification

            $('body').on('click', '.edittask', function(){

                $('#btnModif').show();
                $('#btnRegister').hide();

                task = $(this).data('task');

                $('#formWkf').val(task.form_id);
                $('#utilisateur').val(task.validateur_id);
                $('#niveau').val(task.r_nom_niveau);
                $('#r_rang').val(task.r_rang);
                $('#tache').val(task.tache);

            });


            $('#btnModif').on('click', function(){

                //Récupération des données à poste
                dataSend                                               = {
                    "_token": "{{ csrf_token() }}",
                    niveau: $('#niveau').val(),
                    utilisateur: parseInt($('#utilisateur').val(),10),
                    formulaire: parseInt($('#formWkf').val(),10),
                    tache: $('#tache').val(),
                    r_rang: $('#r_rang').val(),
                    idtask: task.idtask,
                    validateur_id: task.validateur_id
                };
                $('.spinnerRegister').removeAttr('hidden');

                $.ajax({
                    url: `{{ url('/workflows/modiftask') }}`,
                    type: "POST",
                    data: dataSend,
                    success:function(response){
                        //Réinitialisation du formualaires
                        $("#tacheForms")[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="">Tâches</a>'
                        });
                        $('.spinnerRegister').attr('hidden',true);

                        setTimeout(()                              => {

                            $.ajax({
                                url: `{{ url('/workflows/listTache') }}/${task.workflow_id}`,
                                type: "GET",
                                data: dataSend,
                                success:function(response){
                                    nbreTache = response.length;

                                    f_afficheTaches(response);

                                },
                                error: function(response) {
                                    console.log(response);

                                }
                            });

                        }, 500);


                    },
                    error: function(response) {
                        console.log(response);

                    }
                });

            });







        });

        //Affichage des données pour la modification
        $('body').on('click', '.delete', function () {
                //e.preventDefault();

                $('#btnModif').show();
                $('#btnRegister').hide();

                //Récupération des détails de la ligne en cours de modification
                let data                                     = $(this).data('id');

                console.log(data);
                //return;

                Swal.fire({
                    title: `Suprimer le workflow [ ${data.name} ] ?`,
                    text: "Vous confirmé ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui je confirme!',
                    cancelButtonText:'Annuler'
                }).then((result)                         => {
                    if (result.isConfirmed) {

                        $.ajax({
                            type:"DELETE",
                            url: `{{ url('entreprise/${data.id}') }}`,
                            data: { "_token": "{{ csrf_token() }}" },
                            dataType: 'json',
                            success: function(res){
                                Swal.fire('Supression effectuée avec succès', '', 'success');
                                window.location.reload();
                            }
                        });

                    }
                });

            });

    </script>

    @endsection


    @section('content')

    <!-- breadcrumb -->
    @include('layouts.main.breadcrumb', [
    'titre'                                                        => 'Gestion des workflows',
    'soustitre'                                                    => 'workflows',
    'chemin'                                                       => "Configuration des workflows"
    ])
    <!-- END breadcrumb -->

    <div class="content">

        <div class="row">
            <div class="col-md-12">
                <a href="{{ url('/workflows/creation') }}"
                class="btn btn-primary" title="Saisir un nouveau workflow"
                style="float: right;" >Nouveau</a>
            </div>
        </div>

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <small>Liste des workflows</small>
                </h3>
            </div>
            <div class="block-content block-content-full">
                <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/tables_datatables.js -->
                <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm">
                    <tr>
                        <th width="30%">Nom du produit</th>
                        <th width="30%" >Nom du workflow</th>
                        <th>Date de création</th>
                        <th width="15%">Actions</th>
                    </tr>
                    @foreach($workflows as $workflow)
                    <tr>
                        <td class="fw-semibold">{{ $workflow->r_nom_produit }}</td>
                        <td class="fw-semibold">{{ $workflow->name }}</td>
                        <td class="d-none d-sm-table-cell">{{ $workflow->created_at->format('d.m.Y à H:i:s') }}</td>
                        <td>
                            {{-- <a href="/workflows/{{$workflow->id}}" class="btn btn-primary sm" ty><i class="fa fa-eye"></i></a> - --}}

                            <a href="{{ url('/workflows') }}/{{$workflow->id}}/edit" class="btn btn-ligth sm"
                                title="Modification du workflows [ {{ $workflow->name }} ]"  style="padding: 0px; margin: 0px;"
                                ><i class="fa fa-edit text-primary"></i></a>

                                <i class="fa fa-calendar-minus fontawesome newCreateTask text-info m-1" data-id="{{ $workflow }}" title="Saisie des tâches du workflow [ {{ $workflow->name }} ]"></i>

                                <i class="fa fa-trash-alt fontawesome m-1 delete" title="Supression du workflows [ {{ $workflow->name }} ]" data-id="{{ $workflow }}"></i>
                            </td>
                        </tr>
                        @endforeach
                    </table>

                </div>
            </div>

        </div>

        {{-- Début saisie des niveaux de validation et taches par workflows --}}

        <!-- Début modal Modal -->
        <div class="modal fade" id="task_niveau" tabindex="-1" aria-labelledby="task_niveauLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titre"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"><span class="visually-hidden">Close</span></button>
                    </div>
                    <hr>
                    <div class="modal-body">

                        <form id="tacheForms" >

                            <div class="row">
                                <div class="col mb-3">
                                    <label for="exampleFormControlTextarea1" class="form-label">Formulaires</label>
                                    <select class="form-select" aria-label="Default select example" name="formWkf" id="formWkf" >
                                        <option value="0" >---Sélectionnez le formulaire--</option>
                                    </select>
                                    <span class="error" id="erreur_formWkf" ></span>

                                </div>
                            </div>

                            <div class="row">

                                <div class="col mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Niveau du workflow</label>
                                    <input type="text" class="form-control" id="niveau" name="niveau">
                                    <span class="error" id="erreur_niveau" ></span>
                                </div>
                                <div class="col mb-3">
                                    <label for="exampleFormControlTextarea1" class="form-label">Validateurs</label>
                                    <select class="form-select" aria-label="Default select example" name="utilisateur" id="utilisateur" >
                                        <option value="0" >---Affecter un validateur à la tâche--</option>
                                        @foreach($validateurs as $key => $validateur)
                                        <option value="{{ $validateur->id }}" >{{ $validateur->name }} {{ $validateur->lastname }}</option>
                                        @endforeach
                                    </select>
                                    <span class="error" id="erreur_utilisateur" ></span>

                                </div>
                                <div class="col-2 mb-3">
                                    <label for="r_rang" class="form-label">Rang validateur</label>
                                    <input type="number" min="1" class="form-control" id="r_rang" name="r_rang">
                                    <span class="error" id="erreur_rang" ></span>
                                </div>

                            </div>



                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Tâche à effectuer</label>
                                <textarea class="form-control" rows="5" cols="12" name="tache" id="tache" ></textarea>
                                <span class="error" id="erreur_tache" ></span>
                            </div>
                        </form>

                        <button type="button" class="btn btn-primary" id="btnModif">Modifier
                            <div class="spinner-border spinner-border-sm spinnerRegister" role="status" hidden>
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>


                        <div class="clearfix">&nbsp;&nbsp;</div>

                        <table class="table table-bordered table-striped table-vcenter fs-sm mt-2" >

                            <thead>
                                <tr>
                                    <th>Niveau de validation</th>
                                    <th>Formulaire</th>
                                    <th>Tâches</th>
                                    <th>Validateur</th>
                                    <th>Rang</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tachesWorkf"></tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" id="btnRegister">Enregistrer</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin modal Modal -->
        {{-- Fin saisie des niveaux de validation et taches par workflows --}}


    </div>
    @endsection
