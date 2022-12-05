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


        let datas, dataSend = {}, tab = [];

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


            $.ajax({
                url: "{{ url('/roles') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    name:name,
                    slug:slug,
                    r_description:r_description
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
                            footer: '<a href="">Rôle</a>'
                        });

                        setTimeout(()                => {
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

            //Affichage des données pour la modification
            $('body').on('click', '.edit', function () {
                //e.preventDefault();

                $('#btnModif').show();
                $('#btnRegister').hide();

                //Récupération des détails de la ligne en cours de modification
                datas = $(this).data('id');

                //Rédefinition du titre de la modal
                $('#staticBackdropLabel').empty();
                $title = $('#staticBackdropLabel').text(`Modification du produit [ ${datas.r_nom_produit} ]`);

                //Affection des valeurs aux champs du formualaire
                $('#name').val(datas.name);
                $('#slug').val(datas.slug);

                $('#staticBackdrop').modal('show');

            });

            //Affichage des permissions
            $('body').on('click', '.affectPermission', function () {
                //e.preventDefault();
                tab = [];
                $('#btnModif').show();
                $('#btnRegister').hide();

                //Récupération des détails de la ligne en cours de modification
                datas = $(this).data('id');

                //Rédefinition du titre de la modal
                $('#staticBackdropLabel1').empty();
                $title = $('#staticBackdropLabel1').text(`Affection de permission au rôle [ ${datas.name} ]`);

                //Affection des valeurs aux champs du formualaire
                $('#name').val(datas.name);
                $('#slug').val(datas.slug);

                // Récupération des permissions
                $.ajax({
                    url:  `{{ url('/permissions/listepermission_role') }}/${datas.id}`,
                    type:"GET",
                    success:function(response){
                        tab = [];
                        response.forEach(item => {

                            if( item.affected == true ){
                                tab.push({
                                    role: datas.slug,
                                    permission: item.slug,
                                    affected: item.affected
                                })
                            }
                        });

                        affichePermission(response);
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });

                $('#affectPermission').modal('show');

            });

            //Choix des permission à affecter à un role
            $('body').on('click', '.choosePermission', function () {

                let obj = {};
                //Récupération des détails de la ligne en cours de modification
                dkem = $(this).data('id');

                console.log(dkem);

                obj.role = datas.slug;
                obj.permission = dkem.slug;

                if ( $(this).is(':checked') == true) {
                    tab.push(obj);
                }else{
                    tab = tab.filter((el) => el.permission !== obj.permission);
                }

                console.log(tab);

            });

            // Affichage des convention par produit
            let affichePermission = (datas)=>{
                $('#permissionstable').empty();
                datas.forEach((item, index) => {

                    $("#permissionstable").append(`
                    <tr>
                        <td class="fw-semibold">
                            <input class="form-check-input choosePermission" id='dkem${item.slug}' type="checkbox" data-id='${JSON.stringify(item)}'>
                        </td>
                        <td class="fw-semibold">${item.name}</td>
                        <td class="fw-semibold" hidden>${item.slug}</td>
                    </tr>`);
                    $(`#dkem${item.slug}`).prop('checked', item.affected);
                });
            };

            $('#btnModif').on('click', function (e) {
                e.preventDefault();

                //Rédefinition du titre de la modal
                $('#staticBackdropLabel').empty();
                $title = $('#staticBackdropLabel').text(`Modification du produit [ ${datas.r_nom_produit} ]`);

                //Affection des valeurs aux champs du formualaire
                $('#r_nom_produit').val(datas.r_nom_produit);
                $('#r_description').val(datas.r_description);
                let id = $('#r_description').val(datas.id);

                //Ajax
                $.ajax({
                    url:  `/roles/${id}`,
                    type:"PUT",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        r_nom_produit:r_nom_produit,
                        r_description:r_description
                    },
                    success:function(response){

                        if( response._status == 1 ){

                            //Réinitialisation du formualaires
                            $("#formRoles")[0].reset();

                            $('#afficheErrors').attr( "hidden", true );

                            //Affichage du message de succès
                            $("#successMsg").html(`<span>${response._message}</span>`);
                            $('#afficheSuccess').removeAttr( "hidden" );
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


                // Enregistrement des permissions affectées à un rôle
                $('#btnRegisterRolePermi').on('click', function (e) {
                    e.preventDefault();
                    dataSend._token = "{{ csrf_token() }}";
                    dataSend.data = tab;
                    dataSend.idrole = datas.id;

                    //Ajax
                    $.ajax({
                        url:  `{{ url('/permissions/affecter_permission_role') }}`,
                        type:"POST",
                        data: dataSend,
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
                                window.location.reload();
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
                        $title = $('#staticBackdropLabel').text(`Saisir un nouveau rôle`);
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

                            Swal.fire({
                                title: `Vous confirmer la supression du produit  [ ${data.name} ] ?`,
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
                                        type:"DELETE",
                                        url: `{{ url('/roles') }}/${data.id}`,
                                        data: { "_token": "{{ csrf_token() }}"},
                                        dataType: 'json',
                                        success: function(res){
                                            Swal.fire(res._message, 'success')
                                            setTimeout(() => {
                                                window.location.reload();
                                            }, 500);
                                        }
                                    });
                                }
                            });



                        });
                    });

                    //Enregistrement des permissions affectés à un rôle

//Restorations de tous les produits suprimés
$('body').on('click', '#restore', function () {
                    //e.preventDefault();

                    //Récupération des détails de la ligne en cours de modification
                    let data                                 = $(this).data('id');

                    Swal.fire({
                        title: `Restoration de tous les rôles suprimés`,
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
                                url: `{{ url('roles/restoreall') }}`,
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
                </script>

                @endsection



                @section('content')
                <!-- breadcrumb -->
                @include('layouts.main.breadcrumb', [
                'titre' => 'Rôles',
                'soustitre' => 'Rôles utilisateurs',
                'chemin' => "Utilisateurs"
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
                                            <label for="name" class="form-label">Nom du rôle</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="slug" class="form-label">Identifiant (slug)</label>
                                            <input type="text" class="form-control" name="slug" id="slug" placeholder="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="r_description" class="form-label">Description</label>
                                            <textarea class="form-control" name="r_description" id="r_description" rows="3"></textarea>
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



                    <!-- Modal pour affectation de permissions-->
                    <div class="modal fade" id="affectPermission" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel1"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"><span class="visually-hidden">Close</span></button>
                                </div>
                                <hr>
                                <div class="modal-body" style="width: 100%;height: 300px;overflow: auto;">

                                    <table class="table table-bordered table-striped table-vcenter fs-sm " >

                                        <thead>
                                            <tr>
                                                <th>Choix</th>
                                                <th>Permission</th>
                                                <th hidden >Identifiant(slug)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="permissionstable"></tbody>
                                    </table>

                                    <div class="modal-footer">
                                        <button  type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        <button class="btn btn-primary" id="btnRegisterRolePermi" >Enregistrer</button>
                                        <button hidden class="btn btn-primary" id="btnModif">Modifier</button>
                                    </div>
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
                                        <th>Liste des rôles utilisateurs</th>
                                        <th class="d-none d-sm-table-cell" style="width: 30%;">Slug</th>
                                        <th style="width: 15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>



                                    @foreach ($listeRoles as $role)
                                    <tr>
                                        <td class="fw-semibold">{{ $role->name }}</td>
                                        <td class="d-none d-sm-table-cell">{{ $role->slug }}</td>
                                        <td class="text-muted" >
                                            <i class="far fa-pen-to-square edit m-1 text-primary fontawesome" data-id="{{ $role }}" title="Modification du rôle {{ $role->name }}"></i>
                                            <i class="fa fa-trash-can delete m-1 text-danger fontawesome" data-id="{{ $role }}" title="Supression du rôle {{ $role->name }}"></i>
                                            <i class="far fa-folder-open text-success affectPermission m-1 fontawesome" data-id="{{ $role }}" title="Liste des permissions du rôle {{ $role->name }}"></i>
                                        </td>
                                    </td>
                                </tr>
                                @endforeach


                            </tbody>
                        </table>
                        @isset($role)
                        <button class="btn btn-success sm " id="restore" data-id="{{ $role  }}" title="Restorer tous les produits suprimés" >
                            <i class="far fa-window-restore"></i>
                        </button>
                        @endif
                    </div>
                </div>

            </div>
            <!---------------------- Fin Affichage des produits dans la table---------------------------------->

            <!-- END Page Content -->
            @endsection

