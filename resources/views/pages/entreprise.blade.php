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

        $title                                               = $('#staticBackdropLabel').text('Saisir une nouvelle entreprise');
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
            let dataentp                                     ={
                r_nom_entp : $('#r_nom_entp').val().trim(),
                r_forme_juridique : $('#r_forme_juridique').val(),
                r_raison_social : $('#r_raison_social').val().trim(),
                r_sigle : $('#r_sigle').val().trim(),
                r_rccm : $('#r_rccm').val().trim(),
                r_numero_contribuable : $('#r_numero_contribuable').val().trim(),
                r_numero_social : $('#r_numero_social').val().trim(),
                r_description : $('#r_description').val().trim(),
                r_email_entp : $('#r_email_entp').val().trim(),
            }
            let datarepres                                   ={
                r_nom : $('#r_nom').val().trim(),
                r_prenoms : $('#r_prenoms').val().trim(),
                r_telephone : $('#r_telephone').val().trim(),
                r_email : $('#r_email').val().trim(),
                r_type_client : $('#r_type_client').val(),
            }

            // Controlle des champs et validation du formulaire avant soumission

            if ( dataentp.r_nom_entp.trim()                        == "") {
                $('#erreur_nom_entp').empty();
                $('#erreur_nom_entp').append('Veuillez saisir le nom de l\'entreprise');
                return;
            }else{
                $('#erreur_nom_entp').empty();
            }

            if ( datarepres.r_type_client                         == 0) {
                $('#erreur_type_client').empty();
                $('#erreur_type_client').append('Veuillez sélectionner le type d\'entreprise');
                return;
            }else{
                $('#erreur_type_client').empty();
            }
            if ( dataentp.r_forme_juridique                         == 0 || dataentp.r_forme_juridique                         == undefined) {
                $('#erreur_from_jur').empty();
                $('#erreur_from_jur').append('Le forme jurique est réquis');
                return;
            }else{
                $('#erreur_from_jur').empty();
            }

            if ( dataentp.r_rccm.trim()                           == "") {
                $('#erreur_rccm').empty();
                $('#erreur_rccm').append('Le numéro du régistre de commerce est réquis');
                return;
            }else{
                $('#erreur_rccm').empty();
            }

            if ( dataentp.r_email_entp                           == "") {
                $('#erreur_email_entp').empty();
                $('#erreur_email_entp').append('L\'adresse email est incorrect');
                return;
            }else{
                $('#erreur_email_entp').empty();
            }

            if ( datarepres.r_nom.trim()                           == "") {
                $('#erreur_nom').empty();
                $('#erreur_nom').append('Le nom du representant est réquis');
                return;
            }else{
                $('#erreur_nom').empty();
            }

            if ( datarepres.r_prenoms.trim()                           == "") {
                $('#erreur_prenoms').empty();
                $('#erreur_prenoms').append('Le prenom du representant est réquis');
                return;
            }else{
                $('#erreur_prenoms').empty();
            }
            if ( datarepres.r_telephone.trim()                           == "") {
                $('#erreur_telephone').empty();
                $('#erreur_telephone').append('Le numéro de téléphone est réquis');
                return;
            }else{
                $('#erreur_telephone').empty();
            }

            if ( datarepres.r_telephone.trim().length                           !== 10) {
                $('#erreur_telephone').empty();
                $('#erreur_telephone').append('Le numéro de téléphone est incorrect');
                return;
            }else{
                $('#erreur_telephone').empty();
            }

            if ( datarepres.r_email                           == "") {
                $('#erreur_email').empty();
                $('#erreur_email').append('L\'adresse email est incorrect');
                return;
            }else{
                $('#erreur_email').empty();
            }

            $('.spinnerRegister').removeAttr('hidden');

            $.ajax({
                url: "{{ url('/entreprise') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    represantant: datarepres,
                    entreprise: dataentp,
                },
                success:function(response){

                    $('.spinnerRegister').attr('hidden', true);

                    if( response._status                     == 1 ){

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

                        setTimeout(()                        => {
                            window.location.reload();
                        }, 1000);


                    }else{
                        //Convertion du retourn objet et tableau
                        let warning                          = response?._detailsAvertissement.r_code[0];

                        //Ajoute dans erreurs dans la liste pour affichage

                        const element                    = warning;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Attention !!!',
                            text: element,
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
            datas                                            = $(this).data('id');

            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title                                           = $('#staticBackdropLabel').text(`Modification des données de l\'entreprise [ ${datas.r_nom_entp} ]`);

            //Affection des valeurs aux champs du formualaire
            $('#r_nom_entp').val(datas.r_nom_entp);
            $('#r_forme_juridique').val(datas.r_forme_juridique);
            $('#r_raison_social').val(datas.r_raison_social);
            $('#r_sigle').val(datas.r_sigle);
            $('#r_rccm').val(datas.r_rccm);
            $('#r_numero_contribuable').val(datas.r_numero_contribuable);
            $('#r_numero_social').val(datas.r_numero_social);
            $('#r_description').val(datas.r_description);

            $('#r_nom').val(datas.r_nom);
            $('#r_prenoms').val(datas.r_prenoms);
            $('#r_telephone').val(datas.r_telephone);
            $('#r_email').val(datas.r_email);
            //let id                                         = $('#r_description').val(datas.id);

            $('#staticBackdrop').modal('show');

        });

        $('#btnModif').on('click', function (e) {
            e.preventDefault();


            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title                                           = $('#staticBackdropLabel').text(`Modification du type de client [ ${datas.r_nom_entp} ]`);

            //Affection des valeurs aux champs du formualaire
            let dataSend                                     = {
                "_token": "{{ csrf_token() }}",
                r_nom_entp: $('#r_nom_entp').val(),
                r_description: $('#r_description').val()
            }
            $('.spinnerModif').removeAttr('hidden', true);
            // let id                                         = $('#r_description').val(datas.id);

            //Ajax
            $.ajax({
                url:  `{{ url('/entreprises') }}/${datas.id}`,
                type:"PUT",
                data: dataSend,
                success:function(response){

                    $('.spinnerModif').attr('hidden');

                    if( response._status                     == 1 ){

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

                        setTimeout(()                        => {
                            window.location.reload();
                        }, 1000);
                    }else{
                        //Convertion du retourn objet et tableau
                        let warning                          = Object.values(response).flat();

                        //Ajoute dans erreurs dans la liste pour affichage
                        for (let index                   = 0; index < warning.length; index++) {
                            const element                    = warning[index];
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
                $title                                           = $('#staticBackdropLabel').text(`Saisir une nouvelle entreprise`);
                $('#btnModif').hide();
                $('#btnRegister').show();
                $('#afficheErrors').attr( "hidden", true );
                resetForm();
            });

            //Réinitialisation du formualaires
            let resetForm                                        = function(){
                $("#formProduits")[0].reset();
            }

            //Affichage des données pour la modification
            $('body').on('click', '.delete', function () {
                //e.preventDefault();

                $('#btnModif').show();
                $('#btnRegister').hide();

                //Récupération des détails de la ligne en cours de modification
                let data                                     = $(this).data('id');

                Swal.fire({
                    title: `Suprimer l'entreprise [ ${data.r_nom_entp} ] ?`,
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


            //changement de status
            $('body').on('click', '.changestatus', function () {
                //e.preventDefault();

                $('#btnModif').show();
                $('#btnRegister').hide();

                //Récupération des détails de la ligne en cours de modification
                let data                                     = $(this).data('id');

                let ligne                                    = JSON.parse(data.split(';')[0]);
                let val                                      = parseInt(data.split(';')[1]);
                let msg                                      = ( val == 0 )? 'Désactiver' : 'Activer';

                Swal.fire({
                    title: `${msg} l'entreprise' [ ${ligne.r_nom_entp} ] ?`,
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
                            type:"POST",
                            url: `{{ url('entreprise/active_desactive') }}`,
                            data: {
                                "_token": "{{ csrf_token() }}",
                                identreprise: ligne.id,
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
                let data                                     = $(this).data('id');

                Swal.fire({
                    title: `Restoration de toutes entreprises suprimées`,
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
                            type:"GET",
                            url: `{{ url('entreprise/restoreall') }}`,
                            data: { "_token": "{{ csrf_token() }}" },
                            dataType: 'json',
                            success: function(res){
                                Swal.fire('Restorations effectuées avec succès', '', 'success');
                                setTimeout(()                => {
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
    'titre'                                                  => 'Entreprises',
    'soustitre'                                              => 'Gestion entreprise',
    'chemin'                                                 => ""
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
                title="Saisir une nouvelle entreprise">
                Nouveau
            </button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
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
                                    <label for="r_nom_entp" class="form-label">Nom entreprise</label>
                                    <input type="text" class="form-control" name="r_nom_entp" id="r_nom_entp" placeholder="">
                                    <span class="error" id="erreur_nom_entp" ></span>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_type_client" class="form-label">Type d'entreprise</label>
                                    <select class="form-select" aria-label="Default select example" id="r_type_client">
                                        <option selected value="0">---Sélectionnez le type d'entreprise---</option>
                                        @foreach($typeClients as $key => $typeClient)
                                            <option value="{{ $typeClient->id }}">{{ $typeClient->r_libelle }}</option>
                                        @endforeach
                                    </select>
                                    <span class="error" id="erreur_type_client" ></span>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_forme_juridique" class="form-label">Forme jurique</label>
                                    <select class="form-select" aria-label="Default select example" id="r_forme_juridique">
                                        <option selected value="0">---Sélectionnez la forme jurique---</option>
                                        <option value="1">Société anonyme (SA)</option>
                                        <option value="2">Société à responsabilité limité (SARL)</option>
                                    </select>
                                    <span class="error" id="erreur_from_jur" ></span>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_raison_social" class="form-label">Raison sociale</label>
                                    <input type="text" class="form-control" name="r_raison_social" id="r_raison_social" placeholder="">
                                    <span class="error" id="erreur_nom_entp" ></span>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_sigle" class="form-label">Sigle</label>
                                    <input type="text" class="form-control" name="r_sigle" id="r_sigle" placeholder="">
                                    <span class="error" id="erreur_r_sigle" ></span>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_rccm" class="form-label">N° du régistre de commerce</label>
                                    <input type="text" class="form-control" name="r_rccm" id="r_rccm" placeholder="">
                                    <span class="error" id="erreur_rccm" ></span>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_numero_contribuable" class="form-label">N° contribuable</label>
                                    <input type="text" class="form-control" name="r_numero_contribuable" id="r_numero_contribuable" placeholder="">
                                    <span class="error" id="erreur_r_numero_contribuable" ></span>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_numero_social" class="form-label">N° social</label>
                                    <input type="text" class="form-control" name="r_numero_social" id="r_numero_social" placeholder="">
                                    <span class="error" id="erreur_r_numero_social" ></span>

                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="mb-3">
                                <label for="r_email_entp" class="form-label">Adresse email entreprise</label>
                                <input type="text" class="form-control" name="r_email_entp" id="r_email_entp" placeholder="">
                                <span class="error" id="erreur_email_entp" ></span>

                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="r_nom" id="r_nom" placeholder="">
                                    <span class="error" id="erreur_nom" ></span>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_prenoms" class="form-label">Prenoms</label>
                                    <input type="text" class="form-control" name="r_prenoms" id="r_prenoms" placeholder="">
                                    <span class="error" id="erreur_prenoms" ></span>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_telephone" class="form-label">Numéro de téléphone</label>
                                    <input type="text" class="form-control" name="r_telephone" id="r_telephone" placeholder="">
                                    <span class="error" id="erreur_telephone" ></span>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_email" class="form-label">Adresse email</label>
                                    <input type="text" class="form-control" name="r_email" id="r_email" placeholder="">
                                    <span class="error" id="erreur_email" ></span>

                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                            <textarea class="form-control" name="r_description" id="r_description" rows="3"></textarea>
                        </div>

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
            <h3 class="block-title">
                <small><strong>Liste des types de clients</strong></small>
            </h3>
        </div>
        <div class="block-content block-content-full">
            <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/tables_datatables.js -->
            <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm">
                <thead>
                    <tr>
                        <th style="width: 25%;">Entreprise</th>
                        <th class="d-none d-sm-table-cell" style="width: 20%;">Forme juridique</th>
                        <th style="width: 20%;">Date de création</th>
                        <th style="width: 20%;">Status</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($entreprises as $entreprise)

                    <tr>
                        <td class="fw-semibold">{{ $entreprise->r_nom_entp }}</td>
                        <td class="d-none d-sm-table-cell">{{ $entreprise->r_libelle }}</td>
                        <td class="d-none d-sm-table-cell">{{ $entreprise->created_at->format('d.m.Y à H:i:s') }}</td>
                        <td class="text-muted">

                            @switch($entreprise->r_status)
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
                        <td class="text-muted">
                            <i class="far fa-pen-to-square fontawesome edit text-primary m-1" data-id="{{ $entreprise }}" title="Modifier le type de client {{ $entreprise->r_nom_entp }}"></i>
                            <i class="fa fa-trash-can fontawesome delete text-danger m-1" data-id="{{ $entreprise }}" title="Suprimer le type de client {{ $entreprise->r_nom_entp }}"></i>
                            @if ( $entreprise->r_status      == 0)

                            <i class="fa fa-check changestatus fontawesome text-success m-1" data-id="{{ $entreprise }} ; {{ 1 }}" title="Activer le produit [ {{ $entreprise->r_nom_entp }} ]"></i>

                            @endif

                            @if ( $entreprise->r_status     == 1)

                            <i class="fa fa-arrow-right-from-bracket fontawesome changestatus text-danger m-1" data-id="{{ $entreprise}} ; {{ 0 }}" title="Désactiver le produit [ {{ $entreprise->r_nom_entp }} ]"></i>

                            @endif
                        </td>
                    </td>
                </tr>
                @endforeach


            </tbody>
        </table>
        @isset($entreprises)
        <button class="btn btn-success sm " id="restore" data-id="{{ $entreprises  }}" title="Restorer" >
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
