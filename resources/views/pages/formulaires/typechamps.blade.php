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

        $title = $('#staticBackdropLabel').text('Saisir un nouveau type de champs');
        $('#btnModif').hide();
        $('#btnRegister').show();

        //POST
        $('#btnRegister').on('click',function(e){
            e.preventDefault();
            $('#passwordFields').show();
            //Suprime les message d'alerte ( Erreur et succès lors de la validation du formulaire )
            $("#warning").empty();
            $("#successMsg").empty();


            $('#afficheSuccess').attr( "hidden", true );

            //Récupération des données à poste
            let dataPost= {
                "_token": "{{ csrf_token() }}",
                r_libelle: $('#r_libelle').val(),
                r_description: $('#r_description').val()
            }

            // Controlle des champs et validation du formulaire avant soumission

            /*********************************************** nom du produit produits *********************************************/
            if ( dataPost.r_libelle.trim() == "") {
                $('#erreur_type_champ').empty();
                $('#erreur_type_champ').append('Veuillez saisir le nom du type de champ');
                return;
            }else{
                $('#erreur_type_champ').empty();
            }

            $.ajax({
                url: "{{ url('/typechamps') }}",
                type:"POST",
                data:dataPost,
                success:function(response){

                    console.log(response);

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formTypeChamps")[0].reset();

                        //Affichage du message de succès
                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="">formulaire</a>'
                        });

                        window.location.reload();

                    }else{

                        //Convertion du retourn objet et tableau
                        let warning = Object.values(response).flat();

                        //Ajoute dans erreurs dans la liste pour affichage
                            for (let index = 0; index < warning.length; index++) {
                            const element = warning[index];

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Avertissemnt !!',
                                    text: response._message,
                                    footer: '<a href="">formulaire</a>'
                                });

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
            $('#btnModif').show();
            $('#btnRegister').hide();

            //Récupération des détails de la ligne en cours de modification
            datas = $(this).data('id');

            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Modification du type de champs [ ${datas.r_libelle} ]`);

            //Affection des valeurs aux champs du formualaire
            //Récupération des données à poste

            $('#r_libelle').val(datas.r_libelle);
            $('#r_description').val(datas.r_description);

            $('#staticBackdrop').modal('show');

        });

        $('#btnModif').on('click', function (e) {
            e.preventDefault();

            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Modification du type de champs [ ${datas.r_libelle} ]`);

            //Affection des valeurs aux champs du formualaire
            $('#r_libelle').val();
            $('#r_description').val();

            let dataPost= {
                "_token": "{{ csrf_token() }}",
                r_libelle: $('#r_libelle').val(),
                r_description: $('#r_description').val()
            }

            console.log(dataPost);

            //Ajax
            $.ajax({
                url:  `{{ url('/typechamps') }}/${datas.id}`,
                type:"PUT",
                data: dataPost,
                success:function(response){

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formTypeChamps")[0].reset();

                        $('#afficheErrors').attr( "hidden", true );

                        //Affichage du message de succès
                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="#">formulaire</a>'
                        })

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
            $title = $('#staticBackdropLabel').text(`Saisir un nouveau type de champs`);
            $('#btnModif').hide();
            $('#btnRegister').show();
            resetForm();
        });

        //Réinitialisation du formualaires
        let resetForm = function(){
            $("#formTypeChamps")[0].reset();
        }

        //Activation et désactivation d'utilisateur
    $('body').on('click', '.delete', function () {
            //e.preventDefault();

            $('#btnModif').show();
            $('#btnRegister').hide();

            //Récupération des détails de la ligne en cours de modification
            let data = $(this).data('id');
            let ligne = JSON.parse(data.split(';')[0]);
            let val = parseInt(data.split(';')[1]);
            let msg = ( val == 0 )? 'Désactiver' : 'Activer';

            Swal.fire({
                title: `${msg} les accès de [ ${ligne.name} ${ligne.lastname} ] ?`,
                text: "Vous confirmé",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui je confirme!',
                cancelButtonText:'Annuler'
                }).then((result) => {
                if (result.isConfirmed) {

                    // ajax
                    $.ajax({
                        type:"POST",
                        url: `{{ url('/active_desactive') }}/${ligne.id}`,
                        data: { "_token": "{{ csrf_token() }}", r_status: val},
                        dataType: 'json',
                        success: function(res){
                            Swal.fire('Saved!', '', 'success')
                            window.location.reload();
                    }
                    });
                }
                });
        });



    });


  </script>

@endsection



@section('content')
  <!-- breadcrumb -->
  @include('layouts.main.breadcrumb', [
    'titre' => 'Type de champs',
    'soustitre' => 'Type de champs',
    'chemin' => "Formulaire"
    ])
  <!-- END Hero -->

  <!-- Page Content -->
  <div class="content">

        <!---------------------- Début Modal pour la saisie, la modification et la consultation des produits---------------------------------->
            <!-- Button trigger modal -->
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-primary mb-2" id="test" title="Saisir un nouvel utilisateur" style="float: right;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
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
                    <form method="post" id="formTypeChamps">
                        @csrf

                        <div class="mb-3">
                            <label for="r_libelle" class="form-label">Nom du type de champs</label>
                            <input type="text" class="form-control" name="r_libelle" id="r_libelle" placeholder="" autocomplete="off">
                            <span class="error" id="erreur_type_champ" ></span>
                        </div>

                        <div class="col">
                            <div class="mb-3">
                                <label for="r_description" class="form-label">Description</label>
                                <textarea class="form-control" name="r_description" id="r_description" rows="3"></textarea>
                            </div>
                        </div>



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
                    <small>Liste des type de champs</small>
                </h3>
                </div>
                <div class="block-content block-content-full">
                <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/tables_datatables.js -->
                <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm">
                    <thead>
                    <tr>
                        <th>Type de champs</th>
                        <th>Description</th>
                        <th style="width: 20%;">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($typeChamps as $typeChamp)
                            <tr>
                                <td class="fw-semibold">{{ $typeChamp->r_libelle }}</td>
                                <td class="d-none d-sm-table-cell">{{ $typeChamp->r_description }}</td>

                                <td class="text-muted">
                                    <i class="far fa-pen-to-square fontawesome edit text-primary m-1" data-id="{{ $typeChamp }}" title="Modifier le type de champs {{ $typeChamp->r_libelle }}"></i>
                                </td>

                            </tr>
                        @endforeach


                    </tbody>
                </table>
                </div>
            </div>

        <!---------------------- Fin Affichage des produits dans la table---------------------------------->



  </div>
  <!-- END Page Content -->
@endsection
