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
        $('#phone').inputmask('09-99-99-99-99');
        let datas;

        $title = $('#staticBackdropLabel').text('Saisir un nouveau utilisateur');
        $('#btnModif').hide();
        $('#btnRegister').show();

        //POST
        $('#btnRegister').on('click',function(e){
            e.preventDefault();

            var formData                                     = new FormData($("#formUtilisateur")[0]);
            //formData.append('_method', 'post');
            formData.append('_token', "{{ csrf_token() }}");

            $('#passwordFields').show();
            //Suprime les message d'alerte ( Erreur et succès lors de la validation du formulaire )
            $("#warning").empty();
            $("#successMsg").empty();


            $('#afficheSuccess').attr( "hidden", true );

            //Récupération des données à poste
            let dataPost= {
                "_token": "{{ csrf_token() }}",
                name: $('#name').val(),
                lastname: $('#lastname').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                r_canal_cnx : $('#r_canal_cnx ').val(),
                user_role : $('#user_role ').val()
            }

            if( dataPost.name == "" || dataPost.name == undefined | dataPost.name == null ){
                $('#erreur_nom').empty();
                $('#erreur_nom').append('Veuillez saisir le nom de l\'utilisateur');
                return;
            }else{
                $('#erreur_nom').empty();
            }

            if( dataPost.lastname == "" || dataPost.lastname == undefined | dataPost.lastname == null){
                $('#erreur_prenoms').empty();
                $('#erreur_prenoms').append('Veuillez saisir le prenom de l\'utilisateur');
                return;
            }else{
                $('#erreur_prenoms').empty();
            }

            if( dataPost.phone == "" ){
                $('#erreur_phone').empty();
                $('#erreur_phone').append('Veuillez saisir le numéro de téléphone');
                return;
            }else{
                $('#erreur_phone').empty();
            }

            if( dataPost.email == "" ){
                $('#erreur_email').empty();
                $('#erreur_email').append('Veuillez saisir l\'adresse email de l\'utilisateur');
                return;
            }else{
                $('#erreur_email').empty();
            }

            if( dataPost.r_canal_cnx == 0 ){
                $('#erreur_canal').empty();
                $('#erreur_canal').append('Veuillez sélectionner type d\'utilisateur');
                return;
            }else{
                $('#erreur_canal').empty();
            }

            if( dataPost.user_role == 0 ){
                $('#erreur_role').empty();
                $('#erreur_role').append('Veuillez affecter un rôle à l\'utilisateur');
                return;
            }else{
                $('#erreur_role').empty();
            }

            dataPost['phone'] = dataPost.phone.split('-').join('');
            dataPost['phone'] = dataPost.phone.split('_').join('');

            if( dataPost.phone.length !== 10 ){
                $('#erreur_phone').empty();
                $('#erreur_phone').append('Veuillez saisir un numéro de téléphone valide');
                return;
            }else{
                $('#erreur_phone').empty();
            }

            $.ajax({
                url: "{{ url('/utilisateurs') }}",
                type:"POST",
                data:formData,
                processData: false,
                contentType: false,
                success:function(response){

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formUtilisateur")[0].reset();

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

        //Affichage des données pour la modification
        $('body').on('click', '.edit', function () {
            //e.preventDefault();
            $('#passwordFields').hide();
            $('#btnModif').show();
            $('#btnRegister').hide();

            //Récupération des détails de la ligne en cours de modification
            datas = $(this).data('id');

            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Modification de l'utilisateur [ ${datas.name} ${datas.lastname} ]`);

            //Affection des valeurs aux champs du formualaire
            //Récupération des données à poste

            $('#name').val(datas.name);
            $('#lastname').val(datas.lastname);
            $('#email').val(datas.email);
            $('#phone').val(datas.phone);
            $('#r_canal_cnx').val(datas.r_canal_cnx);
            $('#user_role').val(datas.role_id);
            //$('#description').val(datas.r_canal_cnx);
            $('#blah').attr('src',datas.path_name);
            //$('#blah').empty();
            $('#staticBackdrop').modal('show');

        });

        $('#btnModif').on('click', function (e) {
            e.preventDefault();

            var formDataUpdate                           = new FormData($("#formUtilisateur")[0]);
                formDataUpdate.append('_method', 'put');
                formDataUpdate.append('_token', "{{ csrf_token() }}");

            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Modification de l'utilisateur [ ${datas.name} ${datas.lastname} ]`);

            //Affection des valeurs aux champs du formualaire
            $('#r_nom_produit').val(datas.r_nom_produit);
            $('#r_description').val(datas.r_description);

            /* let dataPost= {
                "_token": "{{ csrf_token() }}",
                name: $('#name').val(),
                lastname: $('#lastname').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                r_canal_cnx : $('#type_users ').val()
            } */

            //Ajax
            $.ajax({
                url:  `{{ url('/utilisateurs') }}/${datas.id}`,
                type:"POST",
                data: formDataUpdate,
                processData: false,
                contentType: false,
                success:function(response){

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formUtilisateur")[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="">Utilisateurs</a>'
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
            $title = $('#staticBackdropLabel').text(`Saisir un nouveau utilisateur`);
            $('#btnModif').hide();
            $('#btnRegister').show();
            resetForm();
        });

        //Réinitialisation du formualaires
        let resetForm = function(){
            $("#formUtilisateur")[0].reset();
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
                        url: `{{ url('/utilisateurs/active_desactive/${ligne.id}') }}`,
                        data: { "_token": "{{ csrf_token() }}", r_status: val},
                        dataType: 'json',
                        success: function(res){

                            Swal.fire(res._message, '', 'success');
                            setTimeout(() => {
                                window.location.reload()
                            }, 500);
                        }
                    });
                }
            });
        });


/*----------------------------------------------------------------------------------------------------
      ------------------------------Affection de rôles à un utilisateur----------------------------------*/

      //Affichage des rôles
      $('body').on('click', '.setRole', function () {
            //e.preventDefault();
            $("#myForm").empty();
            $('#permissionstable').empty();
            //Récupération des détails de la ligne en cours de modification
            let data = $(this).data('id');
            datas = JSON.parse(data.split(';')[0]);
            let roles = JSON.parse(data.split(';')[1]);

            for (let index = 0; index < roles.length; index++) {
                            const element = roles[index];
                            $("#myForm").append(` <label class="mt-2"><input type="radio" class="chooserole"
                                                    name="radioName" value="${element.slug}" data-id="${element.id}" />
                                                    &nbsp; ${element.name}</label> <br />`);
                        }

            //Rédefinition du titre de la modal
            $title = $('#modalSetRoleTitle').text(`Affectation de rôle à [ ${datas.name} ${datas.lastname} ]`);


            $('#modalSetRole').modal('show');

        });


        $('body').on('click', '.historikRole', function () {
            //e.preventDefault();
            //Récupération des détails de la ligne en cours de modification
            let data = $(this).data('id');
            $.ajax({
                        type:"GET",
                        url: `{{ url('/roles/historik_role') }}/${data.id}`,
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        dataType: 'json',
                        success: function(res){
                            afficheRolesHistorik(res);
                    }

            });

            //Rédefinition du titre de la modal
            $title = $('#modalhistotikRoleTitle').text(`Historique des rôles affectés à ${data.name} ${data.lastname}`);


            $('#modalhistotikRole').modal('show');

        });

        // Affichage des convention par produit
        let affichePermission = (datas)=>{
                $('#permissionstable').empty();
                datas.forEach((item, index) => {

                    $("#permissionstable").append(`
                    <tr>
                        <td class="fw-semibold">
                            <input class="form-check-input choosePermission" id='dkem${item.slug}'
                            type="checkbox" data-id='${JSON.stringify(item)}'>
                        </td>
                        <td class="fw-semibold">${item.name}</td>
                        <td class="fw-semibold">${item.slug}</td>
                    </tr>`);
                    $(`#dkem${item.slug}`).prop('checked', item.affected);
                    $(`#dkem${item.slug}`).attr('disabled', true);
                    if ($(`#dkem${item.slug}`).is(":checked"))
                    {
                        $(`#dkem${item.slug}`).css('background-color', '#FF7900');
                    }
                });
            };

        // Affichage des convention par produit
        let afficheRolesHistorik = (datas)=>{
                $('#historikrole').empty();
                datas.forEach((item, index) => {

                    $("#historikrole").append(`
                    <tr>
                        <td class="fw-semibold">${item.name}</td>
                        <td class="fw-semibold">${item.r_date_debut}</td>
                        <td class="fw-semibold">${(item.r_date_fin == null)? 'En cours':item.r_date_fin}</td>
                    </tr>`);
                });
            };

        //Choix du rôle
        $('body').on('change', '.chooserole', function(){
            let idrole = $(this).data('id');

            // ajax
            $.ajax({
                        type:"GET",
                        url: `{{ url('/permissions/listepermission_role') }}/${idrole}`,
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        dataType: 'json',
                        success: function(res){
                            affichePermission(res);
                    }

            });

        });

        // Affecter un rôle à un utilisateur
        $('#btnAffectRole').on('click', function() {

            let role = $('input[name=radioName]:checked', '#myForm').val();

            // ajax
            $.ajax({
                        type:"POST",
                        url: `{{ url('/roles/affecter_role_user') }}`,
                        data: {
                            "_token": "{{ csrf_token() }}",
                            role: role,
                            user: datas.id,
                        },
                        dataType: 'json',
                        success: function(res){

                            if( res._status == 1 ){
                                Swal.fire(res._message, '', 'success');
                                setTimeout(() => {

                                    window.location.reload();
                                }, 500);
                            }else{
                                Swal.fire(res._avertissement, '', 'error');
                            }


                    }
                    });
            });

        // End
    });




    function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
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
    'titre' => 'Utilisateurs',
    'soustitre' => 'Gestion des utilisateurs',
    'chemin' => ""
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
                    <form method="post" id="formUtilisateur" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name" class="form-label is-required">Nom</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="" autocomplete="off">
                                    <span class="error" id="erreur_nom" ></span>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="lastname" class="form-label is-required">Prenoms</label>
                                    <input type="text" class="form-control" name="lastname" id="lastname" placeholder="">
                                    <span class="error" id="erreur_prenoms" ></span>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="phone" class="form-label is-required">Téléphone</label>
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="">
                                    <span class="error" id="erreur_phone" ></span>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="email" class="form-label is-required">Adresse email</label>
                                    <input type="email" required class="form-control" name="email" id="email" placeholder="">
                                    <span class="error" id="erreur_email" ></span>

                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="r_canal_cnx" class="form-label is-required">Type utilisateur</label>
                                    <select class="form-select" aria-label="Default select example" id="r_canal_cnx" name="r_canal_cnx" >
                                        <option value="0" selected>--sélectionner le type d'utilisateur--</option>
                                        <option value="1">Utilisateur système centralisé</option>
                                        <option value="2">Utilisateur web backoffice</option>
                                      </select>
                                      <span class="error" id="erreur_canal" ></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="user_role" class="form-label is-required">Rôle utilisateur</label>
                                    <select class="form-select" aria-label="Default select example" id="user_role" name="user_role" >
                                        <option value="0" selected>--sélectionner le rôle--</option>
                                        @foreach($listeRoles as $key => $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                      </select>
                                      <span class="error" id="erreur_role" ></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Photo</label>
                                    <input type="file" class="form-control" name="photo" id="photo" onchange="readURL(this);">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <img id="blah" src="" class="rounded float-end"
                                    width="200px;" height="200px;" alt="Photo de profil" />
                                </div>
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

        <!---------------------- Début Modal pour voir l'historique des rôles---------------------------------->

        <!-- Modal -->
        <div class="modal fade" id="modalhistotikRole" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalhistotikRoleTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"><span class="visually-hidden">Close</span></button>
                </div>
                <hr>
                <div class="modal-body">

                    <table class="table table-bordered table-striped table-vcenter fs-sm mt-2" >

                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Période du</th>
                                <th>Au</th>
                            </tr>
                        </thead>
                        <tbody id="historikrole"></tbody>
                    </table>

                    <!-- Formulaire de saisie des produits -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>

                    </div>
                    <!-- Fin Formulaire de saisie des produits -->

                </div>

            </div>
            </div>
        </div>
    <!---------------------- Fin Modal pour voir l'historique des rôles---------------------------------->


        <!---------------------- Début Affichage des produits dans la table---------------------------------->

            <div class="block block-rounded">
                <div class="block-header block-header-default">
                <h3 class="block-title">
                    <small>Liste des utilisateurs</small>
                </h3>
                </div>
                <div class="block-content block-content-full">
                <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/tables_datatables.js -->
                <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prenoms</th>
                        <th>Rôle</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Canal de connexion</th>

                        <th style="width: 15%;">Action</th>

                    </tr>
                    </thead>
                    <tbody>



                        @foreach ($utilisateurs as $utilisateur)
                            <tr>
                                <td class="fw-semibold">{{ $utilisateur->name }}</td>
                                <td class="d-none d-sm-table-cell">{{ $utilisateur->lastname }}</td>
                                <td class="d-none d-sm-table-cell fw-bold text-danger">{{ $utilisateur->role_name }}</td>
                                <td class="text-muted">{{ $utilisateur->email }}</td>
                                <td class="text-muted">

                                    @switch($utilisateur->r_status)
                                        @case(1)
                                            <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Compte actif
                                                &nbsp;<i class="fa fa-user-check"></i>
                                            </span>
                                        @break

                                        @default
                                            <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-danger-light text-danger">Compte inactif
                                                &nbsp;<i class="fa fa-user-lock"></i>
                                            </span>
                                    @endswitch

                                </td>
                                <td class="text-muted">

                                    @switch($utilisateur->r_canal_cnx)
                                        @case(1)
                                            <strong class="text-dark" >Tous les canaux</strong>
                                        @break

                                        @default
                                        <strong class="text-info" >Interface web backoffice</strong>

                                    @endswitch

                                </td>

                                <td class="text-muted">

                                    @canany(['superadmin','isAdmin'])
                                    <i class="far fa-pen-to-square edit fontawesome m-1 text-primary" data-id="{{ $utilisateur }}"></i>

                                        @canany(['superadmin','isAdmin'])
                                            @if ( $utilisateur->r_status == 0)

                                            <i class="fa fa-check delete fontawesome m-1 text-success" data-id="{{ $utilisateur }} ; {{ 1 }}" title="Activer les accès de l'utilisateur {{ $utilisateur->name }}"></i>

                                            @endif

                                            @if ( $utilisateur->r_status == 1)

                                                <i class="fa fa-arrow-right-from-bracket delete fontawesome m-1 text-danger" data-id="{{ $utilisateur}} ; {{ 0 }}" title="Désactiver les accès de l'utilisateur {{ $utilisateur->name }}"></i>

                                            @endif
                                        @endcanany
                                        <i class="fa fa-users-rectangle setRole fontawesome m-1 text-info" data-id="{{ $utilisateur }} ; {{ $listeRoles }}" title="Affecter un role à {{ $utilisateur->name }}"></i>
                                    @endcanany



                                    <i class="fa fa-clock historikRole fontawesome m-1 text-dark" data-id="{{ $utilisateur }}" title="Historique des rôles affectés à {{ $utilisateur->name }}"></i>


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
