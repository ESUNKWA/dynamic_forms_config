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
        //Définition des masks de saisi
        $('#mntmin').inputmask('9999999999');
        $('#mntmax').inputmask('9999999999');
        $('#taux').inputmask('99');
        var datas;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $title                                               = $('#staticBackdropLabel').text('Saisir un nouveau produit');
        $('#btnModif').hide();
        $('#btnRegister').show();

        //POST
        $('#btnRegister').on('click',function(e){
            e.preventDefault();

            var formData                                     = new FormData($("#formProduits")[0]);
            //formData.append('_method', 'post');
            formData.append('_token', "{{ csrf_token() }}");

            //Suprime les message d'alerte ( Erreur et succès lors de la validation du formulaire )
            $("#warning").empty();

            $('#afficheSuccess').attr( "hidden", true );
            // Controlle des champs et validation du formulaire avant soumission

            if ( $('#r_type_client').val().trim()                               == 0) {
                $('#erreur_type_client').empty();
                $('#erreur_type_client').append('Veuillez saisir le type de client');
                return;
            }else{
                $('#erreur_type_client').empty();
            }

            if ( $('#r_nom_produit').val().trim()                               == "") {
                $('#erreur_nom_produit').empty();
                $('#erreur_nom_produit').append('Veuillez saisir le nom du produit');
                return;
            }else{
                $('#erreur_nom_produit').empty();
            }

            $('.spinnerRegister').removeAttr('hidden');

            $.ajax({
                url: "{{ url('/produits') }}",
                type:"POST",
                data: formData,
                processData: false,
                contentType: false,
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
                        }, 500);


                    }else{
                        //Convertion du retourn objet et tableau
                        let warning                          = Object.values(response).flat();

                        //Ajoute dans erreurs dans la liste pour affichage
                        for (let index                       = 0; index < warning.length; index++) {
                            const element                    = warning[index];
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Oops !!!',
                                    text: element,
                                    footer: '<a href="">Produits</a>'
                                });
                            }
                        }

                    },
                    error: function(response) {
                        console.log(response);
                        $('.spinnerRegister').attr('hidden');
                    }
                });


            });

            //Affichage des données pour la modification
            $('body').on('click', '.edit', function () {
                //e.preventDefault();

                $('#btnModif').show();
                $('#btnRegister').hide();

                //Récupération des détails de la ligne en cours de modification
                datas                                        = $(this).data('id');

                //Rédefinition du titre de la modal
                $('#staticBackdropLabel').empty();
                $title                                       = $('#staticBackdropLabel').text(`Modification du produit [ ${datas.r_nom_produit} ]`);

                //Affection des valeurs aux champs du formualaire
                $('#r_type_client').val(datas.r_type_client);
                $('#r_nom_produit').val(datas.r_nom_produit);
                $('#r_description').val(datas.r_description);
                $('#blah').attr('src',datas.path_name);

                $('#staticBackdrop').modal('show');

            });

            $('#btnModif').on('click', function(e){
                e.preventDefault();

                var formDataUpdate                           = new FormData($("#formProduits")[0]);
                formDataUpdate.append('_method', 'put');
                formDataUpdate.append('_token', "{{ csrf_token() }}");

                $('.spinnerModif').removeAttr('hidden', true);
                // let id                                    = $('#r_description').val(datas.id);

                //Ajax
                $.ajax({
                    url:  `{{ url('/produits') }}/${datas.id}`,
                    type:"POST",
                    data: formDataUpdate,
                    processData: false,
                    contentType: false,
                    success:function(response){

                        $('.spinnerModif').attr('hidden');

                        if( response._status                 == 1 ){

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
                                footer: '<a href="">formulaire</a>'
                            });

                            setTimeout(()                    => {
                                window.location.reload();
                            }, 500);
                        }else{
                            //Convertion du retourn objet et tableau
                            let warning                      = Object.values(response).flat();

                            //Ajoute dans erreurs dans la liste pour affichage
                            for (let index                   = 0; index < warning.length; index++) {
                                const element                = warning[index];
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
                    $title                                   = $('#staticBackdropLabel').text(`Saisir un nouveau produit`);
                    $('#btnModif').hide();
                    $('#btnRegister').show();
                    $('#afficheErrors').attr( "hidden", true );
                    $('#blah').attr('src','');
                    resetForm();
                });

                //Réinitialisation du formualaires
                let resetForm                                = function(){
                    $("#formProduits")[0].reset();
                }

                //Affichage des données pour la modification
                $('body').on('click', '.delete', function () {
                    //e.preventDefault();

                    $('#btnModif').show();
                    $('#btnRegister').hide();

                    //Récupération des détails de la ligne en cours de modification
                    let data                                 = $(this).data('id');

                    Swal.fire({
                        title: `Supression du produit [ ${data.r_nom_produit} ] ?`,
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
                                url: `{{ url('produits/${data.id}') }}`,
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
                    let data                                 = $(this).data('id');

                    let ligne                                = JSON.parse(data.split(';')[0]);
                    let val                                  = parseInt(data.split(';')[1]);
                    let msg                                  = ( val == 0 )? 'Désactiver' : 'Activer';

                    Swal.fire({
                        title: `${msg} le produit [ ${ligne.r_nom_produit} ] ?`,
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
                                url: `{{ url('produits/active_desactive') }}`,
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    idproduit: ligne.id,
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
                    let data                                 = $(this).data('id');

                    Swal.fire({
                        title: `Restoration de tous les produits suprimés`,
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
                                url: `{{ url('produits/restoreall') }}`,
                                data: { "_token": "{{ csrf_token() }}" },
                                dataType: 'json',
                                success: function(res){
                                    Swal.fire('Restorations effectuées avec succès', '', 'success');
                                    setTimeout(()            => {
                                        window.location.reload();
                                    }, 2000);

                                }
                            });

                        }
                    });

                });


                /**************************************************Conventions**************************************/
                $('body').on('click', '.convention', function(){

                    $('#btnModifConv').hide();
                    $('#btnRegisterConv').show();
                    $("#formConvention")[0].reset();
                    $('#modalConventionTitle').empty();
                    datas                                    = $(this).data('id');
                    $title                                   = $('#modalConventionTitle').text(`Convention du produit [ ${datas.r_nom_produit} ]`);

                    afficheConvention(datas.id);

                    $('#modalConvention').modal('show');
                });


                // Affichage des convention par produit
                let afficheConvention                        = (idproduit)=>{

                    $.ajax({
                        url: `{{ url('/convention') }}/${idproduit}`,
                        type:"GET",
                        success:function(response){

                            $('#conventionTable').empty();

                            response.forEach((item, index)       => {
                                $("#conventionTable").append(`<tr>
                                    <td class="fw-semibold">${item.r_nom_entp}</td>
                                    <td class="fw-semibold">${item.r_description}</td>
                                    <td class="fw-semibold">
                                        <i class="far fa-pen-to-square editConvention fontawesome text-primary m-1" data-id='${JSON.stringify(item)}' title="Modifier la convention [ ${item.r_nom_entp} ]"></i>
                                    </td>

                                </tr>`);
                            });

                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                };-

                //Affichage des données pour la modification
                $('body').on('click', '.editConvention', function(e) {
                    e.preventDefault();
                    $('#btnModifConv').show();
                    $('#btnRegisterConv').hide();

                    //Récupération des détails de la ligne en cours de modification
                    datas                                        = $(this).data('id');

                    //Affection des valeurs aux champs du formualaire
                    $('#mntmin').val(datas.r_mnt_min);
                    $('#mntmax').val(datas.r_mnt_max);
                    $('#entreprise').val(datas.r_entreprise);
                    $('#taux').val(datas.r_taux);
                    $('#r_desc').val(datas.r_description);

                    //$('#modalConvention').modal('show');

                });

                //Saisie nouvelle convention
                $('#btnRegisterConv').on('click',function(e){
                    e.preventDefault();

                    //Suprime les message d'alerte ( Erreur et succès lors de la validation du formulaire )
                    $("#warning").empty();

                    $('#afficheSuccess').attr( "hidden", true );

                    //Récupération des données à poste
                    let obj                                      = {
                        entreprise: $('#entreprise').val(),
                        produit: datas.id,
                        mntmin: $('#mntmin').val().trim(),
                        mntmax: $('#mntmax').val().trim(),
                        taux: $('#taux').val().trim(),
                        r_description: $('#r_desc').val().trim(),
                    } ;

                    // Controlle des champs et validation du formulaire avant soumission
                    if ( obj.entreprise                              == 0) {
                        $('#erreur_entp').empty();
                        $('#erreur_entp').append('Veuillez sélectionner le nom de l`\'entreprise');
                        return;
                    }else{
                        $('#erreur_entp').empty();
                    }

                    if ( obj.mntmin                              == 0) {
                        $('#erreur_mntmin').empty();
                        $('#erreur_mntmin').append('Veuillez saisir le montant le montant minimum');
                        return;
                    }else{
                        $('#erreur_mntmin').empty();
                    }

                    if ( obj.mntmax                              == 0) {
                        $('#mntmax').empty();
                        $('#mntmax').append('Veuillez saisir le montant le montant maximum');
                        return;
                    }else{
                        $('#mntmax').empty();
                    }

                    $('.spinnerRegister').removeAttr('hidden');

                    $.ajax({
                        url: "{{ url('/convention') }}",
                        type:"POST",
                        data: obj,
                        success:function(response){

                            $('.spinnerRegister').attr('hidden', true);

                            if( response._status                 == 1 ){

                                //Réinitialisation du formualaires
                                $("#formConvention")[0].reset();

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

                                setTimeout(()                    => {
                                    afficheConvention(obj.produit);
                                }, 500);

                            }else{
                                //Convertion du retourn objet et tableau
                                let warning                      = Object.values(response).flat();

                                //Ajoute dans erreurs dans la liste pour affichage
                                for (let index                   = 0; index < warning.length; index++) {
                                    const element                = warning[index];
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

                    //Modification de convention
                    $('#btnModifConv').on('click',function(e){
                        e.preventDefault();

                        //Suprime les message d'alerte ( Erreur et succès lors de la validation du formulaire )
                        $("#warning").empty();

                        $('#afficheSuccess').attr( "hidden", true );

                        //Récupération des données à poste
                        let obj                                      = {
                            entreprise: $('#entreprise').val(),
                            produit: datas.r_produit,
                            mntmin: $('#mntmin').val(),
                            mntmax: $('#mntmax').val(),
                            taux: $('#taux').val(),
                            r_description: $('#r_desc').val(),
                        } ;

                        /*********************************************** nom du produit produits *********************************************/
                        if ( entreprise                              == undefined || entreprise == null || entreprise == "") {
                            $('#erreur_entp').empty();
                            $('#erreur_entp').append('Veuillez saisir le nom du produit');
                            return;
                        }else{
                            $('#erreur_entp').empty();
                        }

                        $('.spinnerRegister').removeAttr('hidden');

                        $.ajax({
                            url: "{{ url('/convention') }}/" + datas.id,
                            type:"PUT",
                            data: obj,
                            success:function(response){

                                $('.spinnerRegister').attr('hidden', true);

                                if( response._status                 == 1 ){

                                    //Réinitialisation du formualaires
                                    $("#formConvention")[0].reset();

                                    $('#afficheErrors').attr( "hidden", true );

                                    //Affichage du message de succès
                                    $("#successMsg").html(`<span>${response._message}</span>`);
                                    //$('#afficheSuccess').removeAttr( "hidden" );

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Super...',
                                        text: response._message,
                                        footer: '<a href="">Convention</a>'
                                    });

                                    setTimeout(()                    => {
                                        afficheConvention(obj.produit);
                                    }, 500);
                                    $('#btnModif').hide();
                                    $('#btnRegister').show();
                                }else{
                                    //Convertion du retourn objet et tableau
                                    let warning                      = Object.values(response).flat();

                                    //Ajoute dans erreurs dans la liste pour affichage
                                    for (let index                   = 0; index < warning.length; index++) {
                                        const element                = warning[index];
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



                    });

                    function readURL(input) {
                        if (input.files && input.files[0]) {
                            var reader                               = new FileReader();

                            reader.onload                            = function (e) {
                                $('#blah')
                                .attr('src', e.target.result);
                            };

                            reader.readAsDataURL(input.files[0]);
                        }
                    }

                </script>

                @endsection


                @section('content')
                <!-- breadcrumb -->
                @include('layouts.main.breadcrumb', [
                'titre'                                              => 'Produits',
                'soustitre'                                          => 'Gestion des produits',
                'chemin'                                             => ""
                ])
                <!-- END breadcrumb -->

                <!-- Page Content -->
                <div class="content">

                    <!---------------------- Début Modal pour la saisie, la modification et la consultation des produits---------------------------------->
                    <!-- Button trigger modal -->
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-primary mb-2" id="test"
                            style="float: right;" data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                            title="Saisir un nouveau produit">
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

                                <!-- Formulaire de saisie des produits -->
                                <form method="post" id="formProduits" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="r_type_client" class="form-label">Type de client</label>
                                        <select class="form-select" aria-label="Default select example" name="r_type_client" id="r_type_client">
                                            <option value="0" selected>---Sélectionner le type de client---</option>
                                            @foreach ($listeTypeClients as $listeTypeClient)
                                            <option value="{{ $listeTypeClient->id }}" >{{ $listeTypeClient->r_libelle }}</option>
                                            @endforeach
                                        </select>
                                        <span class="error" id="erreur_type_client" ></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="r_nom_produit" class="form-label">Nom du produit</label>
                                        <input type="text" class="form-control" name="r_nom_produit" id="r_nom_produit" placeholder="">
                                        <span class="error" id="erreur_nom_produit" ></span>

                                    </div>

                                    <div class="mb-3">
                                        <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                                        <textarea class="form-control" name="r_description" id="r_description" rows="3"></textarea>
                                    </div>



                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Image du produit</label>
                                                <input type="file" class="form-control" name="image" id="image" onchange="readURL(this);">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <img id="blah" src="" class="rounded float-end"
                                                width="200px;" height="200px;" alt="image du produit" />
                                            </div>
                                        </div>
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



                <!-- Modal conventions -->
                <div class="modal fade" id="modalConvention" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalConventionTitle"></h5>
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
                                <form method="post" id="formConvention" enctype="multipart/form-data">
                                    @csrf


                                        <div class="mb-3">
                                            <label for="entreprise" class="form-label">Liste des entreprise</label>
                                            <select class="form-select" aria-label="Default select example" id="entreprise">
                                                <option value="0" selected>---Sélectionner l'entreprise---</option>
                                                @foreach ($clientEntp as $entp)
                                                <option value="{{ $entp->id }}" >{{ $entp->r_nom_entp }}</option>
                                                @endforeach
                                            </select>
                                            <span class="error" id="erreur_entp" ></span>
                                        </div>




                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="mntmin" class="form-label">Montant minimum</label>
                                            <input type="text" class="form-control" name="mntmin" id="mntmin">
                                            <span class="error" id="erreur_mntmin" ></span>

                                        </div>
                                        <div class="col">
                                            <label for="mntmax" class="form-label">Montant maximum</label>
                                            <input type="text" class="form-control" name="mntmax" id="mntmax">
                                            <span class="error" id="mntmax" ></span>
                                        </div>
                                        <div class="col">
                                            <label for="taux" class="form-label">Taux</label>
                                            <input type="text" class="form-control" name="taux" id="taux" min="0" max="100">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="r_desc" class="form-label">Description</label>
                                        <textarea class="form-control" name="r_desc" id="r_desc" rows="3"></textarea>
                                    </div>

                                    <table class="table table-bordered table-striped table-vcenter fs-sm" >

                                        <thead>
                                            <tr>
                                                <th>Entreprise</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="conventionTable"></tbody>
                                    </table>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cliquer pour fermer">Fermer</button>
                                        <button class="btn btn-primary" id="btnRegisterConv" title="Cliquer pour enregistrer" >
                                            Enregistrer &nbsp;
                                            <div class="spinner-border spinner-border-sm spinnerRegister" role="status" hidden>
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </button>
                                        <button class="btn btn-primary" id="btnModifConv" title="Cliquer pour enregistrer la modification">
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
                            <small>Liste des produits</small>
                        </h3>
                    </div>

                    <div class="row m-2">
                        <div class="col">
                            <form action="{{ url('/produits/listeproduit') }}" method="POST">
                                @csrf

                                <div class="row">

                                    <div class="col-4">
                                        <label for="r_type_client" class="form-label">Type de client</label>
                                        <select class="form-select" aria-label="Default select example" name="r_type_client" id="r_type_clients">
                                            <option>---Sélectionner le type de client---</option>
                                            @foreach ($listeTypeClients as $listeTypeClient)
                                            <option value="{{ $listeTypeClient->id }}" {{ $listeTypeClient->id == $type_client_id ? 'selected' : '' }} >{{ $listeTypeClient->r_libelle }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-2">
                                        <label for="r_libelle" class="form-label" style="color: #fff;">Produits</label>
                                        <button class="btn btn-primary" type="submit">Recherche</button>
                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>

                    <div class="block-content block-content-full">
                        <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/tables_datatables.js -->
                        <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom du produit</th>
                                    <th class="d-none d-sm-table-cell" style="width: 30%;">Description</th>
                                    <th style="width: 18%;">Status</th>
                                    <th style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($produits as $produit)
                                <tr>
{{--                                     <td class="fw-semibold"><img src="{{ $produit->path_name }}" alt="" width="65" height="65"></td>
 --}}                                    <td class="fw-semibold"><img src="{{ $produit->path_name }}" alt="" width="65" height="65"></td>
                                    <td class="fw-semibold">{{ $produit->r_nom_produit }}</td>
                                    <td class="d-none d-sm-table-cell">{{ $produit->r_description }}</td>
                                    <td class="text-muted">

                                        @switch($produit->r_status)
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

                                        <i class="far fa-pen-to-square edit fontawesome text-primary m-1" data-id="{{ $produit }}" title="Modifier le produit [ {{ $produit->r_nom_produit }} ]"></i>

                                        <i class="fa fa-trash-can sm delete fontawesome text-danger m-1" data-id="{{ $produit }}" title="Suprimer le produit [ {{ $produit->r_nom_produit }} ]"></i>

                                        <i class="far fa-file-lines convention fontawesome m-1" data-id="{{ $produit }}" title="Convention du produit [ {{ $produit->r_nom_produit }} ]"></i>


                                        @if ( $produit->r_status     == 0)

                                        <i class="fa fa-check changestatus fontawesome text-success m-1" data-id="{{ $produit }} ; {{ 1 }}" title="Activer le produit [ {{ $produit->r_nom_produit }} ]"></i>

                                        @endif

                                        @if ( $produit->r_status     == 1)

                                        <i class="fa fa-arrow-right-from-bracket fontawesome changestatus text-danger m-1" data-id="{{ $produit}} ; {{ 0 }}" title="Désactiver le produit [ {{ $produit->r_nom_produit }} ]"></i>

                                        @endif

                                    </td>
                                </td>
                            </tr>
                            @endforeach


                        </tbody>
                    </table>
                    @isset($produit)
                    <button class="btn btn-success sm " id="restore" data-id="{{ $produit  }}" title="Restorer tous les produits suprimés" >
                        <i class="far fa-window-restore"></i>
                    </button>
                    @endif

                </div>
            </div>

        </div>
        <!---------------------- Fin Affichage des produits dans la table---------------------------------->


        <!-- END Page Content -->
        @endsection

