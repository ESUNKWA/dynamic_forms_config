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

        $title = $('#staticBackdropLabel').text('Saisir un nouveau type de client');
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
            let r_code = $('#r_code').val();
            let r_libelle = $('#r_libelle').val();
            let r_description = $('#r_description').val();


            // Controlle des champs et validation du formulaire avant soumission

            /*********************************************** nom du produit produits *********************************************/
            if ( r_code.trim() == "") {
                $('#erreur_code').empty();
                $('#erreur_code').append('Veuillez saisir le code');
                return;
            }else{
                $('#erreur_code').empty();
            }

            if ( r_libelle.trim() == "") {
                $('#erreur_libelle').empty();
                $('#erreur_libelle').append('Veuillez saisir le libellé du type de client');
                return;
            }else{
                $('#erreur_libelle').empty();
            }

            $('.spinnerRegister').removeAttr('hidden');

            $.ajax({
                url: "{{ url('/typeclient') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    r_code:r_code,
                    r_libelle:r_libelle,
                    r_description:r_description
                },
                success:function(response){

                    $('.spinnerRegister').attr('hidden', true);

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formProduits")[0].reset();

                        $('#afficheErrors').attr( "hidden", true );

                        //Affichage du message de succès
                        $("#successMsg").html(`<span>${response._message}</span>`);
                        //$('#afficheSuccess').removeAttr( "hidden" );

                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="">Produits</a>'
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);


                    }else{



                        //Convertion du retourn objet et tableau
                        let warning                          = Object.values(response._detailsAvertissement).flat();

                        //Ajoute dans erreurs dans la liste pour affichage

                            const element = warning;
                                Swal.fire({
                                icon: 'warning',
                                title: 'Attention !!!',
                                text: warning[0],
                                footer: '<a href="">Type de client</a>'
                            });

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
            $title = $('#staticBackdropLabel').text(`Modification du type de client [ ${datas.r_libelle} ]`);

            //Affection des valeurs aux champs du formualaire
            $('#r_code').val(datas.r_code);
            $('#r_libelle').val(datas.r_libelle);
            $('#r_description').val(datas.r_description);
            //let id = $('#r_description').val(datas.id);

            $('#staticBackdrop').modal('show');

        });

        $('#btnModif').on('click', function (e) {
            e.preventDefault();


            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Modification du type de client [ ${datas.r_libelle} ]`);

            //Affection des valeurs aux champs du formualaire
            let dataSend = {
                "_token": "{{ csrf_token() }}",
                r_libelle: $('#r_libelle').val(),
                r_description: $('#r_description').val()
            }
            $('.spinnerModif').removeAttr('hidden', true);
           // let id = $('#r_description').val(datas.id);

            //Ajax
            $.ajax({
                url:  `{{ url('/typeclient') }}/${datas.id}`,
                type:"PUT",
                data: dataSend,
                success:function(response){

                    $('.spinnerModif').attr('hidden');

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formProduits")[0].reset();

                        $('#afficheErrors').attr( "hidden", true );

                        //Affichage du message de succès
                        $("#successMsg").html(`<span>${response._message}</span>`);
                       // $('#afficheSuccess').removeAttr( "hidden" );
                       Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="">type de client</a>'
                        });

                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
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
            $title = $('#staticBackdropLabel').text(`Saisir un nouveau type de client`);
            $('#btnModif').hide();
            $('#btnRegister').show();
            $('#afficheErrors').attr( "hidden", true );
            resetForm();
        });

        //Réinitialisation du formualaires
        let resetForm = function(){
            $("#formProduits")[0].reset();
        }

        //Affichage des données pour la modification
        $('body').on('click', '.delete', function () {
                //e.preventDefault();

                $('#btnModif').show();
                $('#btnRegister').hide();

                //Récupération des détails de la ligne en cours de modification
                let data = $(this).data('id');

                Swal.fire({
                    title: `Supression du produit [ ${data.r_libelle} ] ?`,
                    text: "Vous confirmé ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui je confirme!',
                    cancelButtonText:'Annuler'
                    }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                        type:"DELETE",
                        url: `{{ url('typeclient/${data.id}') }}`,
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


        //changement de status
        $('body').on('click', '.changestatus', function () {
                //e.preventDefault();

                $('#btnModif').show();
                $('#btnRegister').hide();

                //Récupération des détails de la ligne en cours de modification
                let data = $(this).data('id');

                let ligne = JSON.parse(data.split(';')[0]);
                let val = parseInt(data.split(';')[1]);
                let msg = ( val == 0 )? 'Désactiver' : 'Activer';

                Swal.fire({
                    title: `${msg} le type de client [ ${ligne.r_libelle} ] ?`,
                    text: "Vous confirmé ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui je confirme!',
                    cancelButtonText:'Annuler'
                    }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                        type:"POST",
                        url: `{{ url('typeclient/active_desactive') }}`,
                        data: {
                                "_token": "{{ csrf_token() }}",
                                id: ligne.id,
                                r_status: val,
                             },
                        dataType: 'json',
                        success: function(res){
                                        Swal.fire(`${res._message}`, '', 'success');
                                        window.location.reload();
                                }
                        });

                    }
                });

        });


        //Restorations de tous les produits suprimés
        $('body').on('click', '#restore', function () {
                //e.preventDefault();

                //Récupération des détails de la ligne en cours de modification
                let data = $(this).data('id');

                Swal.fire({
                    title: `Restoration de tous les type de clients`,
                    text: "Vous confirmé ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui je confirme!',
                    cancelButtonText:'Annuler'
                    }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                        type:"GET",
                        url: `{{ url('typeclients/restoreall') }}`,
                        data: { "_token": "{{ csrf_token() }}" },
                        dataType: 'json',
                        success: function(res){
                                Swal.fire('Restorations effectuées avec succès', '', 'success');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);

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
    'titre' => 'Type de clients',
    'soustitre' => 'Gestion des types de clients',
    'chemin' => ""
    ])
  <!-- END Hero -->

  <!-- Page Content -->
  <div class="content">

    <!---------------------- Début Modal pour la saisie, la modification et la consultation des produits---------------------------------->
    <!-- Button trigger modal -->
    <div class="row">
        <div class="col">
            <button type="button" class="btn btn-primary mb-2" id="test"
            style="float: right;" data-bs-toggle="modal" data-bs-target="#staticBackdrop"
            title="Saisir un nouveau type de client">
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
            <form method="post" id="formProduits">
                @csrf

                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label for="r_code" class="form-label is-required">Code</label>
                            <input type="text" class="form-control" name="r_code" id="r_code" placeholder="">
                            <span class="error" id="erreur_code" ></span>

                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label for="r_libelle" class="form-label is-required">Libellé</label>
                            <input type="text" class="form-control" name="r_libelle" id="r_libelle" placeholder="">
                            <span class="error" id="erreur_libelle" ></span>

                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                    <textarea class="form-control" name="r_description" id="r_description" rows="3"></textarea>
                </div>

                <x-axterix></x-axterix>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cliquer pour fermer">Fermer</button>
                    <button class="btn btn-primary" id="btnRegister" title="Cliquer pour enregistrer" >
                        Enregistrer &nbsp;
                        <div class="spinner-border spinner-border-sm spinnerRegister" role="status" hidden>
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                    <button class="btn btn-primary" id="btnModif" title="Cliquer pour enregistrer la modification">
                        Modifier &nbsp;
                        <div class="spinner-border spinner-border-sm spinnerModif" role="status" hidden>
                            <span class="visually-hidden">Loading...</span>
                        </div>

                    </button>
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
          <h2 class="block-title">
            <small class="text-dark" ><strong>Liste des types de clients</strong></small>
          </h2>
        </div>
        <div class="block-content block-content-full">
          <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm">
            <thead>
              <tr>
                <th style="width: 10%;">Code</th>
                <th class="d-none d-sm-table-cell" style="width: 20%;">Libellé</th>
                <th style="width: 20%;">Description</th>
                <th style="width: 15%;">Date de création</th>
                <th style="width: 17%;">Status</th>
                <th style="width: 15%;">Action</th>
              </tr>
            </thead>
            <tbody>

                @foreach ($typeClients as $typeClient)
                    <tr>
                        <td class="fw-semibold">{{ $typeClient->r_code }}</td>
                        <td class="d-none d-sm-table-cell">{{ $typeClient->r_libelle }}</td>
                        <td class="d-none d-sm-table-cell">{{ $typeClient->r_description }}</td>
                        <td class="d-none d-sm-table-cell">{{ $typeClient->created_at->format('d.m.Y à H:i:s') }}</td>
                        <td class="text-muted">

                            @switch($typeClient->r_status)
                                @case(1)
                                    <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Actif
                                        &nbsp;<i class="fa fa-user-check"></i>
                                    </span>
                                @break

                                @default
                                <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-danger-light text-danger">Inactif
                                    &nbsp;<i class="fa fa-user-lock"></i>
                                </span>

                            @endswitch

                        </td>
                        <td class="text-muted">
                            <i class="far fa-pen-to-square fontawesome edit text-primary m-1" data-id="{{ $typeClient }}" title="Modifier le type de client {{ $typeClient->r_libelle }}"></i>
                            <i class="fa fa-trash-can fontawesome delete text-danger m-1" data-id="{{ $typeClient }}" title="Supprimer le type de client {{ $typeClient->r_libelle }}"></i>
                            @if ( $typeClient->r_status     == 0)

                                <i class="fa fa-check changestatus fontawesome text-success m-1" data-id="{{ $typeClient }} ; {{ 1 }}" title="Activer le type de client [ {{ $typeClient->r_libelle }} ]"></i>

                            @endif

                             @if ( $typeClient->r_status     == 1)

                                <i class="fa fa-arrow-right-from-bracket fontawesome changestatus text-danger m-1" data-id="{{ $typeClient}} ; {{ 0 }}" title="Désactiver le type de client [ {{ $typeClient->r_libelle }} ]"></i>

                            @endif
                          </td>
                        </td>
                    </tr>
                @endforeach


            </tbody>
          </table>
          @isset($typeClient)
          <button class="btn btn-success sm " id="restore" data-id="{{ $typeClient  }}" title="Restorer" >
            <i class="far fa-window-restore"></i>
          </button>
          @endif

        </div>
      </div>

  </div>
<!---------------------- Fin Affichage des produits dans la table---------------------------------->



  </div>
  <!-- END Page Content -->
@endsection
