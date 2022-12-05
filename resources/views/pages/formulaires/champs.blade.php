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


        let dataSend = {}, optionFields = {}, optionsData = [], fields = [], indexOption;

        $('.minMaxValue').attr('hidden', true);

        //Grise le bouton si aucun n'a été saisie
        ( fields.length == 0 )? $('#btnRegister').attr('disabled',true) : $('#btnRegister').removeAttr('disabled');
        // Masquer le spinner
        $('#spinner').hide();

        $title = $('#staticBackdropLabel').text('Saisir un nouveau champs');
        $('#btnModif').hide();
        $('#btnRegister').show();

        //Sélection du produits
        $('#selectProduits').on('change', ()=>{
            let produitId = $('#selectProduits').val();
            dataSend.product = parseInt(produitId,10);
        });

        //Sélection du type de champs
        $('#selectTypeChp').on('change', ()=>{
            let typeChamps = $('#selectTypeChp').val();
            dataSend.field_type = typeChamps;
            // Affiche le tableau des option si le type de champs sélectionné est sélect
            ( typeChamps == 'select' || typeChamps == 'checkbox' || typeChamps == 'radio' )? $('#optionSelect').removeAttr( "hidden" ) : $('#optionSelect').attr( "hidden", true);
            ( typeChamps == 'number' )? $('.minMaxValue').removeAttr( "hidden" ) : $('.minMaxValue').attr( "hidden", true);
            optionsData = [];
            f_optionSelect(optionsData);
        });

        let f_optionSelect = (dataOptions)=>{
            dataOptions.forEach((item, index) => {
                $("#optionTable").append(`<tr>
                                            <td class="fw-semibold">${index + 1}</td>
                                            <td class="fw-semibold">${item.option_name}</td>
                                            <td class="fw-semibold">${item.option_value}</td>
                                            <td class="fw-semibold">
                                                <button class="btn btn-primary sm editOption" data-id="${index}">
                                                    <i class="far fa-pen-to-square"></i>
                                                </button>
                                            </td>
                                        </tr>`);
            });
        };

        // Saisie des options
        $('#ajoutOption').on('click', ()=>{
            optionFields.option_name = $('#option').val();
            optionFields.option_value = $('#valeur').val();

            if( optionFields.option_name.trim() == "" || optionFields.option_value.trim() == "" ){
                $('#errorOption').empty();
                $('#errorOption').append('Veuillez renseigner les champs');
                $('#errorOption').css('border-color','red');
                return;
            }else{
                $('#errorOption').empty();
            }

            //Vérifie si l'option est déjà saisie
            if( optionsData.some((option) => option.option_name == optionFields.option_name) ){
                $('#errorOption').empty();
                $('#errorOption').append('Option déjà saisie');
                $('#errorOption').css('border-color','red');
                return;
            }else{
                $('#errorOption').empty();
            }

            optionsData.push(optionFields);

            // Affichage des options du champs de sélection
            $('#optionTable').empty();

            f_optionSelect(optionsData);

            // Vide les champs du formulaire
            $('#option').val('');
            $('#valeur').val('');
            optionFields = {};
        });

        // Modification des options
        $('#modifOption').on('click', ()=>{
            optionFields.option_name = $('#option').val();
            optionFields.option_value = $('#valeur').val();

            optionsData = optionsData.filter((item)=>{
                if( item !== optionsData[indexOption] ){
                    return item;
                }
            });

            optionsData.push(optionFields);

            // Affichage des options du champs de sélection
            $('#optionTable').empty();

            f_optionSelect(optionsData);

            // Vide les champs du formulaire
            $('#option').val('');
            $('#valeur').val('');
            optionFields = {};
            $('#modifOption').attr('hidden', true);
            $('#ajoutOption').show();
        });

        // Ajouter les champs au tableau
        $('#addTab').on('click', (e)=>{
            e.preventDefault();

            dataSend.field_name = $('#field_name').val().trim();
            dataSend.field_label = $('#field_label').val().trim();
            dataSend.field_value = $('#field_value').val().trim();

            dataSend.field_placeholder = $('#field_placeholder').val();
            dataSend.length = $('#length').val().trim() || null;
            dataSend.value_min = $('#value_min').val().trim() || null;
            dataSend.value_max = $('#value_max').val().trim() || null;

            dataSend.product = parseInt($('#selectProduits').val(),10);
            dataSend.field_type = $('#selectTypeChp').val();

            var words_field_name = dataSend.field_name.split(' ');

            // Controlle des champs et validation du formulaire anvant soumission
            valid = true;
            /*********************************************** champs produits *********************************************/
            if ( isNaN(dataSend.product)) {
                $('#erreurProduit').empty();
                $('#erreurProduit').append('Veuillez selectionner le produit');
                valid = false;
            }else{
                $('#erreurProduit').empty();
            }


            /*********************************************** Champs type de champs*********************************************/

            if ( dataSend.field_type == '---Sélectionner le type de champs---' ) {
                $('#erreurTypeChps').empty();
                $('#erreurTypeChps').append('Veuillez selectionner le type de champs');
                valid = false;
            }else{
                $('#erreurTypeChps').empty();
                $('#erreurTypeChps').css('border','green');
            }

            /*********************************************** Nom du champs*********************************************/
            if ( dataSend.field_name == "" ) {
                $('#nomChamps').empty();
                $('#nomChamps').append('Le nom du champs est obligatoire');
                valid = false;
            }else{
                $('#nomChamps').empty();
            }

            if ( words_field_name.length > 1 ) {
                $('#nomChamps').empty();
                $('#nomChamps').append('Le nom du champs est invalide');
                valid = false;
            }else{
                $('#nomChamps').empty();
            }

            fields.filter((item)=>{
                    if(item.field_name == dataSend.field_name){
                        $('#nomChamps').empty();
                        $('#nomChamps').append('Vous avez dejà saisie cette rubrique');
                        valid = false;
                    }else{
                        $('#nomChamps').empty();
                    }
            });

            /*********************************************** label du champs*********************************************/
            if ( dataSend.field_label == "" ) {
                $('#erreurLabel').empty();
                $('#erreurLabel').append('Le label du champs est obligatoire');
                valid = false;
            }else{
                $('#erreurLabel').empty();
            }

            fields.filter((item)=>{
                    if(item.field_label == dataSend.field_label){
                        $('#nomChamps').empty();
                        $('#nomChamps').append('Vous avez dejà saisie ce label');
                        valid = false;
                    }else{
                        $('#nomChamps').empty();
                    }
            });

            if( valid == true ){

                dataSend.field_options = ( optionsData.length == 0)? null : JSON.stringify(optionsData);;

                fields.push(dataSend);

                optionsData = [];
                $('#optionTable').empty();
                dataSend = {};// Réinitialise l'objet




                // Affichage des champs
                $('#fields').empty();

                fields.forEach(item => {
                    $("#fields").append(`<tr>
                                            <td class="fw-semibold">${item.product}</td>
                                            <td class="fw-semibold">${item.field_name}</td>
                                            <td class="fw-semibold">${item.field_type}</td>
                                            <td class="fw-semibold">${item.field_label}</td>
                                            <td class="fw-semibold">${item.field_options}</td>
                                            <td class="fw-semibold"></td>
                                        </tr>`);
                });


            ( fields.length == 0 )? $('#btnRegister').attr('disabled',true) : $('#btnRegister').removeAttr('disabled');
            $("#formChamps")[0].reset();

            }


        return valid;
        })


        //Enregistrement des champs
        $('#btnRegister').on('click',function(e){
            e.preventDefault();


            $('#passwordFields').show();
            //Suprime les message d'alerte ( Erreur et succès lors de la validation du formulaire )
            $("#warning").empty();
            $("#successMsg").empty();
            $('#spinner').show();

            $('#afficheSuccess').attr( "hidden", true );

            //Récupération des données à poste
            let dataPost= {
                "_token": "{{ csrf_token() }}",
                fields: JSON.stringify(fields)
            }

            $.ajax({
                url: "{{ url('/champs') }}",
                type:"POST",
                data:dataPost,
                success:function(response){

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formChamps")[0].reset();

                        $('#afficheErrors').attr( "hidden", true );
                        $('#spinner').hide();
                        //Affichage du message de succès
                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="">formulaire</a>'
                        })

                        window.location.reload();

                    }else{

                        console.log(response);

                        Swal.fire({
                            icon: 'warning',
                            title: 'Oops!!!',
                            text: response._message,
                            footer: '<a href="">formulaire</a>'
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
            $('#tableChamps').hide();
            $('#addTab').hide();

            //Récupération des détails de la ligne en cours de modification
            datas = $(this).data('id');

            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Modification du champ [ ${datas.field_name} ]`);

            //Affection des valeurs aux champs du formualaire
            //Récupération des données à poste

            $('#field_name').val(datas.field_name);
            $('#field_label').val(datas.field_label);
            $('#field_value').val(datas.field_value);
            $('#field_placeholder').val(datas.field_placeholder);
            $('#length').val(datas.length);
            $('#value_min').val(datas.value_min);
            $('#value_max').val(datas.value_max);
            $('#selectProduits').val(datas.product);
            $('#selectTypeChp').val(datas.field_type);

            if( datas.field_type == 'select' || datas.field_type == 'radio' || datas.field_type == 'checkbox' ){
                $('#optionSelect').removeAttr( "hidden" );
                optionsData = JSON.parse(datas.field_options);
                // Affichage des options du champs de sélection
                $('#optionTable').empty();

                    optionsData.forEach((item, index) => {
                        $("#optionTable").append(`<tr>
                                                    <td class="fw-semibold">${index}</td>
                                                    <td class="fw-semibold">${item.option_name}</td>
                                                    <td class="fw-semibold">${item.option_value}</td>
                                                    <td class="fw-semibold">
                                                        <button class="btn btn-primary sm editOption" data-id="${index}">
                                                            <i class="far fa-pen-to-square"></i>
                                                        </button>
                                                    </td>
                                                </tr>`);
                    });

            }else{
                $('#optionSelect').attr( "hidden", true);
            }




            $('#staticBackdrop').modal('show');

        });

        $('#btnModif').on('click', function (e) {
            e.preventDefault();

            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();

            //Récupération des données à poste
            let dataEdit = {
                "_token": "{{ csrf_token() }}",
                field_name: $('#field_name').val(),
                field_label: $('#field_label').val(),
                field_value: $('#field_value').val() || null,
                field_placeholder: $('#field_placeholder').val(),
                field_options: optionsData || null,
                length: $('#length').val(),
                value_min: $('#value_min').val() || 0,
                value_max: $('#value_max').val() || 0,
                product: $('#selectProduits').val(),
                field_type: $('#selectTypeChp').val(),
            }

            //Ajax
            $.ajax({
                url:  `{{ url('/champs') }}/${datas.id}`,
                type:"PUT",
                data: dataEdit,
                success:function(response){

                    if( response._status == 1 ){

                        //Réinitialisation du formualaires
                        $("#formChamps")[0].reset();

                        $('#afficheErrors').attr( "hidden", true );

                        //Affichage du message de succès
                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="#">formulaire</a>'
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
        $('#createField').on('click', function(){
            //Rédefinition du titre de la modal
            $('#staticBackdropLabel').empty();
            $title = $('#staticBackdropLabel').text(`Saisir un nouveau champs`);
            $('#btnModif').hide();
            $('#btnRegister').show();
            $('#tableChamps').show();
            $('#addTab').show();
            resetForm();
        });

        //Réinitialisation du formualaires
        let resetForm = function(){
            $("#formChamps")[0].reset();
        }

        //Suprimer un champs
     $('body').on('click', '.delete', function () {
                //e.preventDefault();

                $('#btnModif').show();
                $('#btnRegister').hide();

                //Récupération des détails de la ligne en cours de modification
                let data = $(this).data('id');

                Swal.fire({
                    title: `Supression du champs [ ${data.field_name} ] ?`,
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
                        url: `{{ url('champs/${data.id}') }}`,
                        data: { "_token": "{{ csrf_token() }}" },
                        dataType: 'json',
                        success: function(res){

                            if( res._status == 100 ){

                                Swal.fire(res._message, '', 'error');

                            }else{

                                Swal.fire('Supression effectuée avec succès', '', 'success');

                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 2000);

                                }
                            }


                        });

                    }
                });
    });



    //Restorations de tous les champs suprimés
    $('body').on('click', '#restore', function () {
                //e.preventDefault();

                //Récupération des détails de la ligne en cours de modification
                let data = $(this).data('id');

                Swal.fire({
                    title: `Restoration de tous les champs suprimés`,
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
                        url: `{{ url('champs/restoreall') }}`,
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
                    title: `${msg} le formulaire [ ${ligne.field_name} ] ?`,
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
                        url: `{{ url('champs/active_desactive') }}`,
                        data: {
                                "_token": "{{ csrf_token() }}",
                                idchamp: parseInt(ligne.id,10),
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


    //Modification des option de champs select
    $('body').on('click', '.editOption', function(e){
        e.preventDefault();

        $('#ajoutOption').hide();
        $('#modifOption').removeAttr('hidden', true);

        indexOption = $(this).data('id');
        $('#option').val(optionsData[indexOption].option_name);
        $('#valeur').val(optionsData[indexOption].option_value);

    });
});

  </script>

@endsection

@section('content')

@include('layouts.main.breadcrumb', [
    'titre' => 'Champs',
    'soustitre' => 'Gestion des champs de formualaires',
    'chemin' => "Formulaire"
    ])
  <!-- Page Content -->
  <div class="content">

    <!---------------------- Début Modal pour la saisie, la modification et la consultation des produits---------------------------------->
        <!-- Button trigger modal -->
        <div class="row">
            <div class="col">
                <button type="button" class="btn btn-primary mb-2" id="createField" title="Saisir un nouveu champs" style="float: right;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
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
                <div class="row">

                    <div class="col">
                        <label for="r_libelle" class="form-label">Produits</label>
                        <select class="form-select" aria-label="Default select example" id="selectProduits">
                            <option selected>---Sélectionner le produits---</option>
                            @foreach($produits as $produits)
                                <option value="{{ $produits->id }}">{{ $produits->r_nom_produit }}</option>
                            @endforeach
                          </select>
                          <span class="error" id="erreurProduit" ></span>
                    </div>

                    <div class="col">
                        <label for="r_libelle" class="form-label">Type de champs</label>
                        <select class="form-select" aria-label="Default select example" id="selectTypeChp">
                            <option selected>---Sélectionner le type de champs---</option>
                            @foreach($typeChps as $key => $typeChp)
                                <option value="{{ $typeChp->r_libelle }}">{{ $typeChp->r_libelle }}</option>
                            @endforeach
                          </select>
                          <span class="error" id="erreurTypeChps" ></span>
                    </div>

                </div>
                <form method="post" id="formChamps">
                    @csrf

                    <div class="row mt-3">
                        <div class="col">
                            <label for="field_name" class="form-label">Nom du champs</label>
                            <input type="text" class="form-control" name="field_name" id="field_name" placeholder="" autocomplete="off">
                            <span class="error" id="nomChamps" ></span>
                        </div>
                        <div class="col">
                            <label for="field_label" class="form-label">Labelle</label>
                            <input type="text" class="form-control" name="field_label" id="field_label" placeholder="" autocomplete="off">
                            <span class="error" id="erreurLabel" ></span>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="field_value" class="form-label">Valeur</label>
                            <input type="text" class="form-control" name="field_value" id="field_value" placeholder="" autocomplete="off">
                        </div>
                        <div class="col">
                            <label for="field_placeholder" class="form-label">Placeholder</label>
                            <input type="text" class="form-control" name="field_placeholder" id="field_placeholder" placeholder="" autocomplete="off">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="length" class="form-label">Taille</label>
                            <input type="number" class="form-control" min="1" name="length" id="length" placeholder="" autocomplete="off">
                        </div>
                        {{-- <div class="col">
                            <label for="rang" class="form-label">Rang du champ</label>
                            <input type="number" class="form-control" min="1" name="rang" id="rang" placeholder="" autocomplete="off">
                        </div> --}}
                        <div class="col minMaxValue">
                            <label for="value_min" class="form-label">Valeur minimale</label>
                            <input type="number" min="0" class="form-control" name="value_min" id="value_min" placeholder="" autocomplete="off">
                        </div>
                        <div class="col minMaxValue">
                            <label for="r_libelle" class="form-label">Valeur maximale</label>
                            <input type="number" max="0" class="form-control" name="value_max" id="value_max" placeholder="" autocomplete="off">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

{{-- ---------------------------------------------------Saisir des options des champs de sélections-------------------------------------------------------------- --}}

                    <div class="card mb-2" id="optionSelect" hidden style="border-color: #ff7900;" >
                        <div class="card-body">

                            <form id="formOptionSelect" method="post" >
                                <div class="row m-0">
                                    <div class="col">
                                        <label for="option" class="form-label">Option</label>
                                        <input type="text" class="form-control" name="option" id="option" placeholder="" autocomplete="off">
                                    </div>
                                    <div class="col">
                                        <label for="valeur" class="form-label">valeur</label>
                                        <input type="text" class="form-control" name="valeur" id="valeur" placeholder="" autocomplete="off">
                                    </div>
                                    <div class="col">
                                        <label for="r_libelle" class="form-label" style="color: #fff;" >Ajouter</label><br>
                                        <input type="button" class="btn btn-primary" value="Ajouter" id="ajoutOption" autocomplete="off">
                                        <input type="button" class="btn btn-success" value="Modifier" id="modifOption" autocomplete="off" hidden>
                                    </div>

                                </div>
                                <span class="error" id="errorOption" ></span>
                            </form>



                            <table class="table table-bordered table-striped table-vcenter fs-sm mt-2" >

                                <thead>
                                <tr>
                                    <th>Ordre</th>
                                    <th>Option</th>
                                    <th>valeur</th>
                                    <th style="width: 20%;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="optionTable"></tbody>
                            </table>

                        </div>
                      </div>

                      <button class="btn btn-primary float-end" id="addTab">
                        <i class="fa fa-plus"></i>
                      </button>


                      <br><br>
{{----------------------------------------------- Liste des champs saisie et à poster --------------------------------------------}}
                    <table class="table table-bordered table-striped table-vcenter fs-sm" id="tableChamps" >
                        <caption class="text-primary fs-6" >Liste des champs à enregistrer</caption>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Nom du champs</th>
                                <th>Type de champs</th>
                                <th>Label</th>
                                <th>Option</th>
                                <th style="width: 20%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="fields"></tbody>
                    </table>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary" id="btnRegister" >Enregistrer &nbsp;
                            <span class="spinner-border spinner-border-sm me-2" id="spinner" role="status" hidden aria-hidden="true"></span>
                          </button>
                        </button>
                        <button class="btn btn-primary" id="btnModif">Modifier</button>
                    </div>
                </form>
                <!-- Fin Formulaire de saisie des produits -->

            </div>

        </div>
        </div>
    </div>
    <!---------------------- Fin Modal pour la saisie, la modification et la consultation des produits---------------------------------->


    <!---------------------- Début Affichage des champs dans la table---------------------------------->

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <small>Liste des champs</small>
                </h3>
            </div>

            <div class="row m-2">
                <div class="col">
                    <form action="{{ url('/champs/champs_by_product') }}" method="POST">
                        @csrf

                        <div class="row">

                            <div class="col-4">
                                <label for="r_libelle" class="form-label">Produits</label>
                                <select class="form-select" aria-label="Default select example" name="id" id="selectProduits">
                                    <option selected>---Sélectionner le produits---</option>
                                    @foreach($produit as $produits)
                                        <option value="{{ $produits->id }} " {{ $produits->id == $produit_id ? 'selected' : '' }} >{{ $produits->r_nom_produit }}</option>
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
                <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm ">
                    <thead>
                    <tr >
                        <th style="width: 20%;">Champs</th>
                        <th>Type de champs</th>
                        <th>Options</th>
                        <th style="width: 18%;">Status</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                    </thead>
                    <tbody>



                        @foreach ($champs as $champ)
                            <tr>
                                <td class="fw-semibold">{{ $champ->field_name }}</td>
                                <td class="d-none d-sm-table-cell">{{ $champ->field_type }}</td>
                                <td class="d-none d-sm-table-cell">{{ $champ->field_options }}</td>

                                <td class="d-none d-sm-table-cell">
                                    @switch($champ->r_status)
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
                                    <i class="far fa-pen-to-square fontawesome edit text-primary m-1" data-id="{{ $champ }}" title="Modifier le champs {{ $champ->field_name }}"></i>
                                    <i class="fa fa-trash-can fontawesome delete text-danger m-1" data-id="{{ json_encode($champ) }}" title="Supprimer le champs {{ $champ->field_name }}"></i>


                                    @if ( $champ->r_status == 0)
                                    <i class="fa fa-check fontawesome changestatus text-danger m-1" data-id="{{ json_encode($champ) }} ; {{ 1 }}" title="Activer le champs {{ $champ->field_name }}"></i>

                                    @endif

                                    @if ( $champ->r_status == 1)
                                    <i class="fa fa-arrow-right-from-bracket fontawesome changestatus text-dark m-1" data-id="{{ json_encode($champ) }} ; {{ 0 }}" title="Désactiver le champs {{ $champ->field_name }}"></i>

                                    @endif

                                </td>

                            </tr>
                        @endforeach


                    </tbody>
                </table>
                <button class="btn btn-success sm " id="restore" data-id="{{ $produit }}" title="Restorer tous les formulaires suprimés" >
                    <i class="far fa-window-restore"></i>
                </button>
            </div>
        </div>


    <!---------------------- Fin Affichage des produits dans la table---------------------------------->



</div>
  <!-- END Page Content -->
@endsection
