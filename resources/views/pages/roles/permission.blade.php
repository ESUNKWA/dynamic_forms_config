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


        let datas;

        $title = $('#staticBackdropLabel').text('Saisir un nouveau rôle');
        $('#btnModif').hide();
        $('#btnRegister').show();

        //POST
        $('#btnRegister').on('click',function(e){
            e.preventDefault();

            //Suprime les message d'alerte ( Erreur et succès lors de la validation du formulaire )
            $("#warning").empty();
            $("#successMsg").empty();


            $('#afficheSuccess').attr( "hidden", true );

            //Récupération des données à poste
            let name = $('#name').val();
            let slug = $('#slug').val();
            let r_description = $('#r_description').val();
            let uri = $('#uriaffiche').val();

            $.ajax({
                url: "{{ url('/permissions') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    name:name,
                    slug:slug,
                    r_description:r_description,
                    uri:uri
                },
                success:function(response){

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formRoles")[0].reset();

                        $('#afficheErrors').attr( "hidden", true );

                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="">Permissions</a>'
                        });

                        setTimeout(()                => {
                            window.location.reload();
                        }, 500);;

                    }else{
                        //Convertion du retourn objet et tableau
                        let warning = Object.values(response).flat();

                        //Ajoute dans erreurs dans la liste pour affichage
                            for (let index = 0; index < warning.length; index++) {
                            const element = warning[index];
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

        //Affichage des données pour la modification
        $('body').on('click', '.edit', function () {
            //e.preventDefault();
            $('#slug').attr('disabled', true);
            $('#btnModif').show();
            $('#btnRegister').hide();

            //Récupération des détails de la ligne en cours de modification
            datas = $(this).data('id');
            let ressources = ( datas.uri !== null )? datas.uri.split('/') : [];

            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Modification de la permission [ ${datas.name} ]`);

            //Affection des valeurs aux champs du formualaire
            $('#name').val(datas.name);
            $('#slug').val(datas.slug);
            $('#uriaffiche').val( ( ressources.length >= 1 )? ressources[ressources.length - 1]: null);
            $('#routes').val(datas.uri);
            $('#r_description').val(datas.r_description);

            $('#staticBackdrop').modal('show');

        });

        $('#btnModif').on('click', function (e) {
            e.preventDefault();

            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Modification du produit [ ${datas.r_nom_produit} ]`);

            //Affection des valeurs aux champs du formualaire
            let name = $('#name').val();
            let slug = $('#slug').val();
            let r_description = $('#r_description').val();
            let uri = $('#uriaffiche').val();
            let id = datas.id
            //Ajax
            $.ajax({
                url:  `{{ url('/permissions') }}/${id}`,
                type:"PUT",
                data:{
                    "_token": "{{ csrf_token() }}",
                    name:name,
                    r_description:r_description,
                    uri:uri
                },
                success:function(response){

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formRoles")[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="">Produits</a>'
                        });

                        setTimeout(()                        => {
                            window.location.reload();
                        }, 500);

                    }else{
                        //Convertion du retourn objet et tableau
                        let warning = Object.values(response).flat();

                        //Ajoute dans erreurs dans la liste pour affichage
                            for (let index = 0; index < warning.length; index++) {
                            const element = warning[index];
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

        //New form
        $('#test').on('click', function(){
            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Saisir une nouvelle permission`);
            $('#btnModif').hide();
            $('#btnRegister').show();
            resetForm();
        });

        //Réinitialisation du formualaires
        let resetForm = function(){
            $("#formRoles")[0].reset();
        }

        //Affichage des données pour la modification
        $('body').on('click', '.delete', function () {
            //e.preventDefault();

            $('#btnModif').show();
            $('#btnRegister').hide();

            //Récupération des détails de la ligne en cours de modification
            let data = $(this).data('id');

            if (confirm(`Vous confirmer la supression du produit  [ ${data.r_nom_produit} ]`) == true) {

                // ajax
                $.ajax({
                    type:"DELETE",
                    url: `{{ url('permissions/${data.id}') }}`,
                    data: { "_token": "{{ csrf_token() }}" },
                    dataType: 'json',
                    success: function(res){
                    window.location.reload();
                }
                });
            }



        });


        $('#routes').on('change', function () {

            const prefix = $(this).val().split('/')[1];

            $.ajax({
                    type:"GET",
                    url: `{{ url('/permissions/getroute') }}/${prefix}`,
                    data: { "_token": "{{ csrf_token() }}" },
                    dataType: 'json',
                    success: function(res){
                        $('#uri').empty();
                        res.forEach(element => {
                            $('#uri').append(`
                                                 <label class="mt-2">
                                                    <input type="radio" class="form-check-input chooseuri" name="radioName" value="${element[0]}" data-id="${element[0]}"/>
                                                    &nbsp; ${element[0]}</label> <br />
                            `);
                        });
                    }
            });

        });

        //Récupération de l'uri
        $('body').on('change', '.chooseuri', function(){
            let uri = $(this).data('id');
            $('#uriaffiche').empty();
            $('#uriaffiche').val(uri);
        });

});





  </script>

@endsection



@section('content')
  <!-- breadcrumb -->
  @include('layouts.main.breadcrumb', [
                                        'titre' => 'Permissions',
                                        'soustitre' => 'Permissions des utilisateurs',
                                        'chemin' => "Utilisateur"
                                        ])
  <!-- END Hero -->

<!-- Page Content -->
<div class="content">


    <!---------------------- Début Modal pour la saisie, la modification et la consultation des produits---------------------------------->
            <!-- Button trigger modal -->
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-primary mb-2" id="test" style="float: right;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
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
                            <form method="post" id="formRoles">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label is-required">Nom de la permission</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="">
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="slug" class="form-label is-required">Identifiant (slug)</label>
                                            <input type="text" class="form-control" name="slug" id="slug" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="uriaffiche" class="form-label is-required">URI</label>
                                            <input type="text" class="form-control" name="uriaffiche" id="uriaffiche" disabled>
                                        </div>
                                    </div>
                                </div>


                                <div class="mb-3">
                                    <label for="r_description" class="form-label">Description</label>
                                    <textarea class="form-control" name="r_description" id="r_description" rows="2"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="r_description" class="form-label is-required">Regroupement</label>
                                            <select class="form-select" aria-label="Default select example" id="routes">
                                                <option selected>---selectionner la route---</option>

                                                <option value="/accueil">Tableau de bors</option>

                                                <option value="/typeclient">Type de client</option>
                                                <option value="/produits">Produits</option>
                                                <option value="/entreprise">Entreprises</option>

                                                <option value="/typechamps">Type de champs</option>
                                                <option value="/champs">Champs</option>
                                                <option value="/gpechamps">Groupe de champs</option>
                                                <option value="/formulaires">Formulaires</option>

                                                <option value="/validateurs">Validateurs</option>
                                                <option value="/workflows">Configuration de workflows</option>

                                                <option value="/roles">Rôles</option>
                                                <option value="/permissions">Permissions</option>
                                                <option value="/utilisateurs">Utilisateurs</option>

                                              </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3" id="uri"></div>
                                    </div>
                                </div>

                                <x-axterix></x-axterix>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button class="btn btn-primary" id="btnRegister" >Enregistrer</button>
                                    <button class="btn btn-primary" id="btnModif">Modifier</button>
                                </div>
                            </form>
                            <!-- Fin Formulaire de saisie des produits -->

                        </div>

                    </div>
                </div>
            </div>
            <!---------------------- Fin Modal pour la saisie, la modification et la consultation des produits---------------------------------->




    <!---------------------- Début Affichage des produits dans la table---------------------------------->

    <div class="block block-rounded">
        <div class="block-header block-header-default">
        <h3 class="block-title">
            <small>Liste des produits</small>
        </h3>
        </div>
        <div class="block-content block-content-full">
        <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/tables_datatables.js -->
        <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm">
            <thead>
            <tr>
                <th>Nom permission</th>
                <th class="d-none d-sm-table-cell" style="width: 30%;">Identifiant (Slug)</th>
                <th style="width: 15%;">Action</th>
            </tr>
            </thead>
            <tbody>



                @foreach ($Listepermissions as $permission)
                    <tr>
                        <td class="fw-semibold">{{ $permission->name }}</td>
                        <td class="d-none d-sm-table-cell">{{ $permission->slug }}</td>
                        <td class="text-muted">
                            <i class="far fa-pen-to-square fontawesome edit text-primary m-1" data-id="{{ json_encode($permission) }}" title="Modifier le type de client "></i>
                        </td>

                    </tr>
                @endforeach


            </tbody>
        </table>
        </div>
    </div>

</div>
    <!---------------------- Fin Affichage des produits dans la table---------------------------------->

  <!-- END Page Content -->
@endsection


