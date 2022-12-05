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
                                        <button class="btn btn-primary sm editConvention" data-id='${JSON.stringify(item)}' title="Modifier la convention [ ${item.r_nom_entp} ]">
                                            <i class="far fa-pen-to-square"></i>
                                        </button>
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
                'titre'                                              => 'Mon profil',
                'soustitre'                                          => '',
                'chemin'                                             => ""
                ])
                <!-- END breadcrumb -->
                <div class="bg-image" style="background-image: url('assets/media/photos/photo10@2x.jpg');">
                    <div class="bg-primary-dark-op">
                      <div class="content content-full text-center">
                        <div class="my-3">
                          <img class="img-avatar img-avatar-thumb" src=" @if(Auth::user()->path_name)
                          {{ Auth::user()->path_name }}
                          @else
                          {{ url('storage/images/utilisateurs/default_photo.png') }}
                          @endif  " alt="">
                        </div>
                        <h2 class="h4 fw-normal text-white-75">
                            {{ Auth::user()->name }} {{ Auth::user()->lastname }}
                        </h2>

                      </div>
                    </div>
                  </div>

                <!-- Page Content -->
                <div class="content">
                    <!-- Your Block -->
                    <div class="block block-rounded">











                          <div class="block block-rounded">
                            <div class="block-header block-header-default">
                              <h3 class="block-title">Profil utilisateur</h3>
                            </div>
                            <div class="block-content">
                              <form action="be_pages_projects_edit.html" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                                <div class="row push">
                                  <div class="col-lg-4">
                                    <img class="img-avatar img-avatar-thumb block block-rounded mb-0" src=" @if(Auth::user()->path_name)
                                    {{ Auth::user()->path_name }}
                                    @else
                                    {{ url('storage/images/utilisateurs/default_photo.png') }}
                                    @endif  " style="width: 128px; height: 128px;" alt="">

                                  </div>
                                  <div class="col-lg-8 col-xl-5">
                                    <div class="mb-4">
                                      <label class="form-label" for="one-profile-edit-username">Nom</label>
                                      <input type="text" class="form-control" id="one-profile-edit-username" name="one-profile-edit-username" placeholder="Enter your username.." value="{{ Auth::user()->name }}">
                                    </div>
                                    <div class="mb-4">
                                      <label class="form-label" for="one-profile-edit-name">Prenoms</label>
                                      <input type="text" class="form-control" id="one-profile-edit-name" name="one-profile-edit-name" placeholder="Enter your name.." value="{{ Auth::user()->lastname }}">
                                    </div>
                                    <div class="mb-4">
                                      <label class="form-label" for="one-profile-edit-email">Adresse email</label>
                                      <input type="email" class="form-control" id="one-profile-edit-email" disabled placeholder="Enter your email.." value="{{ Auth::user()->email }}">
                                    </div>
                                    <div class="mb-4">
                                      <div class="mb-4">
                                        <label for="one-profile-edit-avatar" class="form-label">Numéro de téléphone</label>
                                        <input class="form-control" type="tel" value="{{ Auth::user()->phone }}" disabled >
                                      </div>
                                    </div>
                                    <div class="mb-4">
                                        <a class="btn btn-primary" href="be_pages_generic_profile.html">
                                            Modifier
                                          </a>
                                    </div>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>





                    </div>
                    <!-- END Your Block -->
                  </div>
        <!---------------------- Fin Affichage des produits dans la table---------------------------------->


        <!-- END Page Content -->
        @endsection

