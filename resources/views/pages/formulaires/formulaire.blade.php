@extends('layouts.backend')

@section('css_before')
<!-- Page JS Plugins CSS -->
<link rel="stylesheet" href="{{ asset('/css/stepper.css') }}">
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
<script src="{{ asset('/js/stepper.js') }}"></script>

<script>



    //Cette fonction est exécuté au chargement de la page
    $(function(){


        let datas, champsTab = [], formFields = [], formFieldSend = [], formFieldsViews = [], obj = {}, inputID = [], idProduit = 0, idgrpeChamps = 0;

        $title = $('#staticBackdropLabel').text('Saisir un nouveau produit');
        $('#btnModif').hide();
        $('#btnRegister').show();

        //Saisie de formulaire
        $('#btnRegister').on('click',function(e){
            e.preventDefault();

            //Suprime les message d'alerte ( Erreur et succès lors de la validation du formulaire )
            $("#warning").empty();
            $("#successMsg").empty();

            $('#afficheSuccess').attr( "hidden", true );

            //Récupération des données à poste

            let dataPost= {
                "_token": "{{ csrf_token() }}",
                r_nom: $('#r_nom').val(),
                r_produit: parseInt(idProduit,10),
                r_description: $('#r_description').val(),
                r_niveau: $('#niveau').val(),
                r_champs: JSON.stringify(formFieldSend)
            }

            $('.spinnerRegister').removeAttr('hidden');

            $.ajax({
                url: "{{ url('/formulaires') }}",
                type:"POST",
                data: dataPost,
                success:function(response){
                    $('.spinnerRegister').attr('hidden', true);
                    if( response._status == 1 ){
                        $("#forms")[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Super...',
                            text: response._message,
                            footer: '<a href="#">formulaire</a>'
                        });

                        setTimeout(() => {
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



            $('#btnModif').on('click', function (e) {
                e.preventDefault();

                //Rédefinition du titre de la modal
                $('#staticBackdropLabel').empty();

                //Récupération des données à poste

                let dataPost= {
                    "_token": "{{ csrf_token() }}",
                    r_nom: $('#r_nom').val(),
                    r_produit: parseInt(idProduit,10) || datas.product_id,
                    r_description: $('#r_description').val(),
                    r_champs: JSON.stringify(formFieldSend) || null
                }
                $('.spinnerModif').removeAttr('hidden');


                //Ajax
                $.ajax({
                    url:  `{{ url('/formulaires') }}/${datas.form_id}`,
                    type:"PUT",
                    data:dataPost,
                    success:function(response){

                        if( response._status == 1 ){

                            //Réinitialisation du formualaires
                            $("#forms")[0].reset();
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
                $('#test').on('click', function(){
                    //Rédefinition du titre de la modal
                    $('#staticBackdropLabel').empty();
                    $title = $('#staticBackdropLabel').text(`Saisir un nouveau produit`);
                    $('#btnModif').hide();
                    $('#btnRegister').show();
                    resetForm();
                });

                //Réinitialisation du formualaires
                let resetForm = function(){
                    $("#formProduits")[0].reset();
                }
                /**************************************************Géneration des champs du formulaire**************************************/
                // Affichage des champs par produits
                let afficheChamps = (tabChamps)=>{

                    $('#champsProd').empty();

                    tabChamps.forEach((item, index) => {
                        $("#champsProd").append(`<tr>
                            <td class="fw-semibold">
                                <input class="form-check-input chooseFields" type="checkbox" value="" data-id="${item.id}">
                            </td>
                            <td class="fw-semibold">${item.field_name}</td>
                            <td class="fw-semibold">${item.field_label}</td>
                            <td class="fw-semibold">${item.length}</td>

                        </tr>`);
                    });
                };

                // Affichage des formulaires par produits
                let afficheFormsByProduct = (tabForms)=>{

                    $('#formsProd').empty();

                    tabForms.forEach((item, index) => {
                        $("#formsProd").append(`<tr>
                            <td class="fw-semibold">${item.r_nom}</td>
                            <td class="fw-semibold">${item.r_nom_niveau}</td>
                            <td class="fw-semibold">${(item.r_status == 1)? "<span class='fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success'>Actif &nbsp;<i class='fa fa-user-check'></i>'</span>": "<span class='fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-danger-light text-danger'>Inactif &nbsp;<i class='fa fa-user-check'></i>'</span>"}</td>
                        </tr>`);
                    });
                };

                // Affichage des champs du formulaire
                let afficheChampsForms = (tabChampsForms)=>{

                    $('#champsForm').empty();
                    formFieldSend = [];
                    tabChampsForms.forEach((item, index) => {


                        if (item.champs.length >= 1) {
                            $("#champsForm").append(`
                            <tr class="fw-semibold">
                                <td colspan="5" class="text-danger" ><i class="fa fa-2x fa-caret-right"></i>&nbsp;<span>${item.grp_chp}</span><td>
                                    <tr>`);


                                        item.champs.forEach((elt, i) => {
                                           formFieldSend.push(elt);
                                            $('#champsForm tr:last').after(`
                                            <tr>
                                                <td class="fw-semibold">${elt.field_name}</td>
                                                <td class="fw-semibold">${elt.field_label}</td>
                                                <td class="fw-semibold">${elt.length || elt.field_length}</td>
                                                <td class="fw-semibold">
                                                    <input type="checkbox" min="1" class="estObligatoire" data-id="${elt.id}">
                                                </td>
                                                <td class="fw-semibold">
                                                    <input type="number" min="1" value="${elt.field_rang}" class="form-control setrang" data-id="${elt.id}" placeholder="rang">
                                                </td>
                                                <td class="fw-semibold" style="text-align: center;">
                                                    <i class="fa fa-circle-minus fontawesome retirerchps text-danger m-1" title="Rétirer le champs ${elt.field_name}" data-id="${elt.id}"></i>
                                                </td>
                                            </tr>
                                            `);
                                        });
                                    }

                                });
                            };

                            // Récapitulatif des champs
                            let recapFormsFields = (tabChampsForms)=>{

                                $('#recapFields').empty();

                                tabChampsForms.forEach((item, index) => {

                                    if (item.champs.length >= 1) {
                                        $("#recapFields").append(`
                                        <tr class="fw-semibold">
                                            <td colspan="5" class="text-danger"><i class="fa fa-2x fa-caret-right"></i>&nbsp;${item.grp_chp}<td>
                                                <tr>
                                                    `);
                                                    item.champs.forEach((elt, i) => {
                                                        $('#recapFields tr:last').after(`
                                                        <tr>
                                                            <td class="fw-semibold">${elt.field_name}</td>
                                                            <td class="fw-semibold">${elt.field_label}</td>
                                                            <td class="fw-semibold">${elt.length || elt.field_length}</td>
                                                            <td class="fw-semibold">
                                                                <input type="checkbox" min="1" disabled class="estObligatoire" data-id="${elt.id}">
                                                            </td>
                                                            <td class="fw-semibold">
                                                                <input type="number" min="1" disabled value="${elt.field_rang}" class="form-control setrang" data-id="${elt.id}" placeholder="rang">
                                                            </td>
                                                        </tr>
                                                        `);
                                                    });
                                                }

                                            });
                                        };

                                        //Affectation de l'attribut obligatoire aux champs
                                        $('body').on('change','.estObligatoire', function(){

                                            let nullable, index, champs;

                                            idChamps = $(this).data('id');

                                            if ($(this).is(':checked')) {
                                                $(this).attr('value', true);
                                            } else {
                                                $(this).attr('value', false);
                                            }

                                            formFieldSend.filter((el) => {
                                                if( el.id == idChamps ){
                                                    return el.r_es_obligatoire = ($(this).val() == 'true')? true : false;
                                                }
                                            });

                                        });

                                        //Sélection de produit pour l'affichage des champs
                                        $('#produitsselect').on('change', ()=>{
                                            idProduit = $('#produitsselect').val();

                                            champsTab = [];
                                            formFields = [];
                                            formFields = [];
                                            formFieldsViews = []

                                            $('#champsProd').empty();
                                            $('#champsForm').empty();

                                            $('#productNameForm').text($('#produitsselect :selected').text());

                                            // Vérifie si des champs ont étés sélectionner
                                            if(idProduit == 0 ){
                                                $('#btnNextInitial').attr('disabled', true);
                                                $('#btnNextTwo').attr('disabled', true);
                                                $('#erreurSelectProduit').empty();
                                                $('#erreurSelectProduit').css('border-color','red');
                                                $('#erreurSelectProduit').append('Veuillez sélectionner le produit');

                                                return;
                                            }
                                            $('#btnNextInitial').removeAttr('disabled');
                                            //$('#btnNextTwo').removeAttr('disabled');
                                            $('#erreurSelectProduit').empty();
                                            $('#erreurSelectProduit').css('border-color','green');

                                            $.ajax({
                                                type:"GET",
                                                url: `{{ url('/formulaires/nom_formulaire_by_product') }}/${idProduit}`,
                                                data: { "_token": "{{ csrf_token() }}" },
                                                dataType: 'json',
                                                success: function(res){
                                                    $('#niveau').val(`Niveau ${res.length + 1}`);
                                                    // Affichage du tableau des formulaires
                                                    afficheFormsByProduct(res);
                                                }
                                            });


                                            $.ajax({
                                                type:"GET",
                                                url: `{{ url('/champs/list_champs_by_product') }}/${idProduit}`,
                                                data: { "_token": "{{ csrf_token() }}" },
                                                dataType: 'json',
                                                success: function(res){

                                                    champsTab = res._result;

                                                    // Affichage du tableau des champs
                                                    afficheChamps(champsTab);
                                                }
                                            });
                                        });

                                        //Choix des champs à ajouter pour le formualire
                                        $('body').on('click', '.chooseFields', function () {
                                            let checkedValue;

                                            if ($('body .chooseFields').is(':checked')) {
                                                checkedValue = $(this).attr('value', 'true');
                                            } else {
                                                checkedValue = $(this).attr('value', 'false');
                                            }

                                            let id = $(this).data('id'); // Id du champs sélectionné
                                            inputID.push(id);

                                        });

                                        //Sélection groupe de champs
                                        $('#grpchamp').on('change', ()=>{

                                            idgrpeChamps = parseInt($('#grpchamp').val(),10);
                                            obj.grp_chp = $('#grpchamp :selected').text();

                                        });

                                        //Choix des champs pour la génération formulaire
                                        $('body').on('click', '.addFormsFields', function () {

                                            let existegrpe = false;

                                            // Vérifie si des champs ont étés sélectionner
                                            if( champsTab.length == 0 ){
                                                $('#erreurSelectProduit').empty();
                                                $('#erreurSelectProduit').css('border-color','red');
                                                $('#erreurSelectProduit').append('Veuillez sélectionner le produit');

                                                return;
                                            }else{
                                                $('#erreurSelectProduit').empty();
                                                $('#erreurSelectProduit').css('border-color','red');
                                            }

                                            // Vérifie si les champs ont étés affectés à un groupe
                                            if( idgrpeChamps == 0 || idgrpeChamps == undefined ){
                                                $('#erreurSelectGprchp').empty();
                                                $('#erreurSelectGprchp').css('border-color','red');
                                                $('#erreurSelectGprchp').append('Veuillez affecter un groupe au champs sélectionnés');

                                                return;
                                            }else{
                                                $('#erreurSelectGprchp').empty();
                                                $('#erreurSelectGprchp').css('border-color','red');
                                            }

                                            champsTab.filter((item, index)=>{
                                                if( inputID.includes(item.id) ){
                                                    item.r_rang = index + 1;
                                                    item.r_es_obligatoire = false;
                                                    item.r_grp_champs = idgrpeChamps;
                                                    formFields.push(item);
                                                    formFieldSend.push(item);
                                                }
                                            });


                                            //Désactive le btn suivant: etape 2
                                            $('#btnNextTwo').removeAttr('disabled');

                                            // Supressions du tableau des champs du champs affecté au formulaire
                                            champsTab = champsTab.filter((item)=> !inputID.includes(item.id) );

                                            //Formatage des champs du formulaire à visualider avant la validation
                                            obj.champs = formFields;

                                            formFieldsViews.forEach((el, index)=>{
                                                if ( el.grp_chp == obj.grp_chp) {
                                                    existegrpe = true
                                                    formFieldsViews[index].champs.push(obj.champs[0]);
                                                }
                                            });

                                            if( existegrpe == false ){
                                                formFieldsViews.push(obj);
                                            }
                                            // Affichage du tableau des champs par produits
                                            afficheChamps(champsTab);

                                            // Affichage du tableau des champs du formulaire
                                            afficheChampsForms(formFieldsViews);

                                            // Récapitulatif des champs
                                            recapFormsFields(formFieldsViews);

                                            inputID = [];
                                            obj = {};
                                            formFields = [];
                                        })

                                        //Choix des champs pour la génération formulaire
                                        $('body').on('click', '.retirerchps', function () {

                                            let inputID = {}, ids = [];
                                            inputID = $(this).data('id'); // Id du champs sélectionné

                                            //return;
                                            // Récupération du champs du formulaire
                                            formFieldSend.filter((item)=>{
                                                if( item.id == inputID ){
                                                    champsTab.push(item);
                                                }
                                            });

                                            // Supressions du champs du formulaire
                                            formFieldSend = formFieldSend.filter((item)=> ( item.id !== inputID ) );
                                            formFieldsViews.filter((item, index)=>{

                                                formFieldsViews[index].champs = item.champs.filter( (elt) => elt.id !== inputID )

                                            } );

                                            // Affichage du tableau des champs par produits
                                            afficheChamps(champsTab);

                                            // Affichage du tableau des champs du formulaire
                                            afficheChampsForms(formFieldsViews);
                                        });

                                        $('#r_nom').on('change', ()=>{
                                            $('#titreFormulaire').text($('#r_nom').val());
                                        });


                                        $('#btnNewForm').on('click', (e)=>{
                                            e.preventDefault();

                                            // Cacher le btn qui permet de saisir un nouveau formulaire au chargement de la page
                                            $('#btnNewForm').hide();

                                            // Afficher le btn qui permet d'afficher la liste des formulaire au chargement de la page
                                            $('#btnListForm').removeAttr('hidden', true);

                                            $('#btnModif').hide();
                                            $('#btnRegister').show();

                                            $('.listForms').attr('hidden',true);// Cache la liste des formulaires
                                            $('#forms').removeAttr('hidden');// Affiche le formulaire de saisie
                                            $('#r_nom').val('');
                                            $('#produitsselect').val(0);
                                            $('#grpchamp').val(0);
                                        });

                                        $('#btnListForm').on('click', ()=>{

                                            // Cacher le btn qui permet de saisir un nouveau formulaire au chargement de la page
                                            $('#btnListForm').attr('hidden', true);

                                            // Afficher le btn qui permet d'afficher la liste des formulaire au chargement de la page
                                            $('#btnNewForm').show();

                                            $('.listForms').removeAttr('hidden');// Affiche la liste des formulaires
                                            $('#forms').attr('hidden',true);// Affiche le formulaire de saisie

                                            $('#btnModif').hide();
                                            $('#btnRegister').hide();



                                            window.location.reload();
                                        });

                                        /**************************************************Fin Géneration des champs du formulaire**************************************/


                                        //Voir des détails (champs) du formulaires
                                        $('body').on('click', '.formLigne', function(){

                                            $('#btnModif').show();
                                            $('#btnRegister').hide();
                                            $('#produitsselect').attr('disabled',true);
                                            $('#btnNextInitial').removeAttr('disabled');

                                            //$formInfos = $(this).data('id');
                                            datas = $(this).data('id');

                                            $.ajax({
                                                type:"GET",
                                                url: `{{ url('/formulaires/nom_formulaire_by_product') }}/${datas.product_id}`,
                                                data: { "_token": "{{ csrf_token() }}" },
                                                dataType: 'json',
                                                success: function(res){
                                                    $('#niveau').val(`Niveau ${res.length + 1}`);

                                                    // Affichage du tableau des formulaires
                                                    afficheFormsByProduct(res);
                                                }
                                            });

                                            $.ajax({
                                                type:"GET",
                                                url: `{{ url('/champs/list_champs_by_product') }}/${datas.product_id}`,
                                                data: { "_token": "{{ csrf_token() }}" },
                                                dataType: 'json',
                                                success: function(res){

                                                    champsTab = res._result;

                                                    // Affichage du tableau des champs
                                                    afficheChamps(champsTab);
                                                }
                                            });

                                            $('#forms').removeAttr('hidden');
                                            $('.listForms').attr('hidden', true);
                                            $('#btnNextOne').removeAttr('disabled');

                                            let tabs = [];
                                            datas.form.forEach((item)=>{
                                                for (const key in item) {
                                                    if (Object.hasOwnProperty.call(item, key)) {
                                                        const element = item[key];
                                                        //Renommage des clés
                                                        element['grp_chp'] = element['form_group_name'];
                                                        element['champs'] = element['form_field_group'];

                                                        delete element['form_group_name'];
                                                        delete element['form_field_group'];

                                                        tabs.push(element);

                                                    }
                                                }

                                            });

                                            formFieldsViews = tabs;

                                            //Affectation des valeurs du formulaire pour consultation
                                            $('#r_nom').val(datas.form_name),
                                            $('#produitsselect').val(datas.product_id),
                                            $('#titreFormulaire').text(datas.form_name);
                                            $('#productNameForm').text(datas.product_name);

                                            afficheChampsForms(tabs);
                                            recapFormsFields(tabs);

                                            $('#btnNextTwo').removeAttr('disabled');
                                        });


                                        /*----------------------------------------------Validation du formulaire-----------------------------------------------*/
                                        $('#btnNextOne').attr('disabled', true);
                                        $('#btnNextTwo').attr('disabled', true);
                                        $('#btnNextInitial').attr('disabled', true);
                                        //Champs de saisie du libellé du formulaire
                                        $('#r_nom').on('keyup', ()=>{

                                            if( $('#r_nom').val() !== "" ){
                                                $('#btnNextOne').removeAttr('disabled');
                                                $('#erreurNom').empty();
                                                $('#r_nom').css('border-color','green');

                                            }else{
                                                $('#btnNextOne').attr('disabled', true);
                                                $('#erreurNom').empty();
                                                $('#r_nom').css('border-color','red');
                                                $('#erreurNom').append('Le nom du formulaire est obligatoire');

                                            }
                                        });


                                        //Restorations de tous les formulaires suprimés
                                        $('body').on('click', '#restore', function () {
                                            //e.preventDefault();

                                            //Récupération des détails de la ligne en cours de modification
                                            let data = $(this).data('id');

                                            Swal.fire({
                                                title: `Restoration de tous les formulaires suprimés`,
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
                                                        url: `{{ url('formulaires/restoreall') }}`,
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


                                        //Suprimer un formulaire
                                        $('body').on('click', '.delete', function () {
                                            //e.preventDefault();

                                            $('#btnModif').show();
                                            $('#btnRegister').hide();

                                            //Récupération des détails de la ligne en cours de modification
                                            let data = $(this).data('id');

                                            Swal.fire({
                                                title: `Supression du [ ${data.form_name} ] ?`,
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
                                                        url: `{{ url('/formulaires') }}/${data.form_id}`,
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
                                                title: `${msg} le formulaire [ ${ligne.form_name} ] ?`,
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
                                                        url: `{{ url('/formulaires/active_desactive') }}`,
                                                        data: {
                                                            "_token": "{{ csrf_token() }}",
                                                            idformulaire: parseInt(ligne.form_id,10),
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



                                        // Saisie des rangs pour chaque champs
                                        $('body').on('change', '.setrang', function(){
                                            let idChamps = $(this).data('id');
                                            let valEnCours = $(this).val();

                                            formFieldSend.filter((el) => {
                                                if( el.id == idChamps ){
                                                    return el.r_rang = parseInt(valEnCours);
                                                }
                                            });

                                        })

                                    });

                                </script>

                                @endsection



                                @section('content')

                                @include('layouts.main.breadcrumb', [
                                'titre' => 'Formulaire',
                                'soustitre' => 'Configuration de formulaire',
                                'chemin' => "Formulaire"
                                ])
                                <!-- Page Content -->
                                <div class="content">

                                    <div class="row">

                                        <div class="col-sm-6 mb-2">

                                            <form action="{{ url('/formulaires/formulaire_by_product') }}" method="POST" class="listForms">
                                                @csrf
                                                <div class="row">
                                                    <div class="col">
                                                        <label for="exampleFormControlInput1" class="form-label">Produits oba</label>
                                                        <select class="form-select" aria-label="Default select example" name="idproduct" >
                                                            <option selected value="0" >---Sélectionner un produit---</option>

                                                            @foreach($listeProduits as $produit)
                                                            <option value="{{ $produit->id }}" {{ $produit->id == $produit_id ? 'selected' : '' }}>{{ $produit->r_nom_produit }}</option>
                                                            @endforeach

                                                        </select>
                                                        <span class="error" id="erreurSelectProduit" ></span>
                                                    </div>

                                                    <div class="col-2">
                                                        <label for="r_libelle" class="form-label" style="color: #fff;">Produits</label>
                                                        <button class="btn btn-primary" type="submit">Recherche</button>
                                                    </div>

                                                </div>

                                            </form>
                                        </div>


                                        <div class="col">
                                            <button class="btn btn-primary float-end m-1" id="btnNewForm" title="Saisir un nouveau formulaire" >Nouveau</button>
                                            <button class="btn btn-dark float-end m-1" id="btnListForm" title="Voir la liste des formulaires" hidden >Voir les formulaires</button>
                                        </div>
                                    </div>

                                    {{------------------------------------------------------Affichages des formulaires--------------------------------}}
                                    <!---------------------- Début Affichage des produits dans la table---------------------------------->

                                    <div class="block block-rounded listForms">
                                        <div class="block-header block-header-default">
                                            <h3 class="block-title">
                                                <small>Liste des formulaire configurés</small>
                                            </h3>
                                        </div>

                                        <div class="block-content block-content-full">
                                            <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/tables_datatables.js -->
                                            <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons fs-sm">
                                                <thead>
                                                    <tr>
                                                        {{-- <th style="width: 30%;">Produit</th> --}}
                                                        <th>Formulaire</th>
                                                        <th style="width: 15%;">Status</th>
                                                        <th style="width: 15%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>



                                                    @foreach ($formsList as $forms)

                                                    <tr>
                                                        {{-- <td class="fw-semibold">{{ $forms->product_name }}</td> --}}
                                                        <td class="d-none d-sm-table-cell">{{ $forms->form_name }}</td>
                                                        <td class="d-none d-sm-table-cell">
                                                            @switch($forms->form_status)
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

                                                            <i class="far fa-pen-to-square fontawesome text-primary formLigne m-1" data-id="{{ json_encode($forms) }}"></i>
                                                            <i class="fa fa-trash-can fontawesome delete text-danger m-1" data-id="{{ json_encode($forms) }}"></i>


                                                            @if ( $forms->form_status == 0)

                                                            <i class="fa fa-check fontawesome changestatus text-success m-1" data-id="{{ json_encode($forms) }} ; {{ 1 }}" title="Activer le formulaire [ {{ $forms->form_name }} ]"></i>

                                                            @endif

                                                            @if ( $forms->form_status == 1)

                                                            <i class="fa fa-arrow-right-from-bracket fontawesome changestatus text-danger m-1" data-id="{{ json_encode($forms) }} ; {{ 0 }}" title="Désactiver le formulaire {{ $forms->form_name }}"></i>

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


                                    {{-- Saisie et définition des champs du formulaires --}}
                                    <form action="#" class="form" id="forms" onsubmit="event.preventDefault()" hidden >

                                        {{-- Entête des steppers --}}
                                        <div class="progressbar">
                                            <div class=" progress" id="progress"></div>

                                            <div
                                            class="progress-step progress-step-active"
                                            data-title="Intitulé du Formulaire"
                                            ></div>

                                            <div class="progress-step" data-title="Produits"></div>
                                            <div class="progress-step" data-title="Sélection des champs"></div>
                                            <div class="progress-step" data-title="Récapitulatif du formulaire"></div>
                                        </div>

                                        {{-- Contenu 1: Intitulé du formulaire --}}
                                        <div class="step-forms step-forms-active">

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label for="exampleFormControlInput1" class="form-label is-required">Produits oba</label>
                                                    <select class="form-select" aria-label="Default select example" id="produitsselect" >
                                                        <option selected value="0" >---Sélectionner un produit---</option>

                                                        @foreach($listeProduits as $produit)
                                                        <option value="{{ $produit->id }}" >{{ $produit->r_nom_produit }}</option>
                                                        @endforeach

                                                    </select>
                                                    <span class="error" id="erreurSelectProduit" ></span>
                                                </div>
                                            </div>
                                            <br>
                                            <table class="table table-bordered table-striped table-vcenter fs-sm" >

                                                <thead>
                                                    <tr>
                                                        <th>Nom du formulaire</th>
                                                        <th>Niveau</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="formsProd"></tbody>
                                            </table>

                                            <div class="mt-5" >
                                                <button class="btn btn-next btn-primary btn-sm width-50 ml-auto" id="btnNextInitial">Suivant&nbsp;<i class="fa fa-2x fa-arrow-right-long"></i></button>
                                            </div>
                                        </div>

                                        <div class="step-forms">

                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <i class="far fa-5x fa-file-lines"></i>
                                                </div>
                                                <div class="col-lg-9">
                                                    <div class="group-inputs">
                                                        <label for="r_nom" class="form-label is-required">Nom du formulaire</label>
                                                        <input type="text" class="form-control" name="r_nom" id="r_nom" />
                                                        <span class="error" id="erreurNom" ></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col">
                                                    <div class="mt-3 group-inputs">
                                                        <label for="r_description" class="form-label">Description</label>
                                                        <textarea class="form-control" name="r_description" id="r_description" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="btns-group mt-5">
                                                <button href="#" class="btn btn-prev btn-dark btn-sm"><i class="fa fa-2x fa-arrow-left-long"></i>&nbsp;Précédent</button>
                                                <button href="#" class="btn btn-next btn-primary btn-sm" id="btnNextOne">Suivant&nbsp;<i class="fa fa-2x fa-arrow-right-long"></i></button>
                                            </div>
                                        </div>

                                        {{-- Contenu 2: Sélection du champs --}}
                                        <div class="step-forms">
                                            <div class="row mt-3">
                                                <div class="col-sm-12">
                                                    <div class="card">
                                                        <div class="card-body">

                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <label for="exampleFormControlInput1" class="form-label is-required">Niveau</label>
                                                                    <input type="text" class="form-control text-danger" readonly name="niveau" id="niveau">
                                                                </div>

                                                                <div class="col-sm-6">
                                                                    <label for="exampleFormControlInput1" class="form-label is-required">Regroupement des champs</label>
                                                                    <select class="form-select" aria-label="Default select example" id="grpchamp" >
                                                                        <option selected value="0">---Sélectionner un groupe de champs---</option>

                                                                        @foreach($grpChamps as $grpChamp)
                                                                        <option value="{{ $grpChamp->id }}" >{{ $grpChamp->r_nom }}</option>
                                                                        @endforeach

                                                                    </select>
                                                                    <span class="error" id="erreurSelectGprchp" ></span>

                                                                </div>

                                                            </div>

                                                            <div class="clearfix">&nbsp;</div>

                                                            <h6 class="card-title ">Liste des champs</h6>

                                                            <table class="table table-bordered table-striped table-vcenter fs-sm" >

                                                                <thead>
                                                                    <tr>
                                                                        <th>Action</th>
                                                                        <th>Rubrique</th>
                                                                        <th>Label</th>
                                                                        <th>taille</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="champsProd"></tbody>
                                                            </table>

                                                            <button type="button" class="btn btn-primary sm addFormsFields float-end" id="addFormsFields" data-id="${item.id}"
                                                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                            Ajouter &nbsp;<i class="fa fa-caret-right"></i></button>

                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-sm-12 mt-3">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h6 class="card-title is-required">Champs du formulaire</h6>

                                                            <table class="table table-bordered table-striped table-vcenter fs-sm" >

                                                                <thead>
                                                                    <tr>
                                                                        <th>Rubrique</th>
                                                                        <th>Label</th>
                                                                        <th style="width: 20%;">taille</th>
                                                                        <th style="width: 20%;">Obligatoire</th>
                                                                        <th style="width: 20%;">rang</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="champsForm"></tbody>
                                                            </table>



                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="btns-group mt-5">
                                                <button href="#" class="btn btn-prev btn-dark btn-sm"><i class="fa fa-2x fa-arrow-left-long"></i>&nbsp;Précédent</button>
                                                <button href="#" class="btn btn-next btn-primary btn-sm" id="btnNextTwo">Suivant&nbsp;<i class="fa fa-2x fa-arrow-right-long"></i></button>
                                            </div>
                                        </div>

                                        {{-- Contenu 3: Récapitulatif --}}

                                        <div class="step-forms">



                                            <div class="card">
                                                <div class="card-body">
                                                    <h5><strong id="titreFormulaire" ></strong></h5>
                                                    <h5>Service : <strong id="productNameForm"></h4>
                                                    </div>
                                                </div>


                                                <table class="table table-bordered table-striped table-vcenter fs-sm">
                                                    <caption style="color: #000; font-size: 15px;" >Liste des champs du formulaire</caption>
                                                    <thead>
                                                        <tr>
                                                            <th>Rubrique</th>
                                                            <th>Label</th>
                                                            <th style="width: 20%;">taille</th>
                                                            <th style="width: 20%;">Obligatoire</th>
                                                            <th style="width: 20%;">rang</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="recapFields" ></tbody>
                                                </table>



                                                <div class="btns-group mt-5">
                                                    <a href="#" class="btn btn-prev btn-dark btn-sm"><i class="fa fa-2x fa-arrow-left-long "></i>&nbsp; Précédent</a>
                                                    {{-- <input type="button" class="btn -btn-primary btnRegister" value="Enregistrer" id="btnRegister" /> --}}
                                                    <button class="btn btn-primary" id="btnRegister" title="Cliquer pour enregistrer" >
                                                        Enregistrer &nbsp;
                                                        <div class="spinner-border spinner-border-sm spinnerRegister" role="status" hidden>
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </button>

                                                    {{-- <input type="button" class="btn -btn-primary " value="Modifier" id="btnModif" /> --}}
                                                    <button class="btn btn-primary" id="btnModif" title="Cliquer pour enregistrer la modification" id="btnModif">
                                                        Modifier &nbsp;
                                                        <div class="spinner-border spinner-border-sm spinnerModif" role="status" hidden>
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>

                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="clearfix">&nbsp;</div>
                                        <x-axterix></x-axterix>

                                    </div>
                                    <!-- END Page Content -->
                                    @endsection
