//Cette fonction est exécuté au chargement de la page
$(function(){

    $title = $('#staticBackdropLabel').text('Saisir un nouveau produit');
    $('#btnModif').hide();
    $('#btnRegister').show();
})

//POST
$('#formProduits').on('submit',function(e){
    e.preventDefault();

    //Suprime les message d'alerte ( Erreur et succès lors de la validation du formulaire )
    $("#warning").empty();
    $("#successMsg").empty();


    $('#afficheSuccess').attr( "hidden", true );

    //Récupération des données à poste
    let r_nom_produit = $('#r_nom_produit').val();
    let r_description = $('#r_description').val();


    $.ajax({
        url: "/produits",
        type:"POST",
        data:{
            "_token": "{{ csrf_token() }}",
            r_nom_produit:r_nom_produit,
            r_description:r_description
        },
        success:function(response){

            if( response._status == 1 ){

                //Réinitialisation du formualaires
                $("#formProduits")[0].reset();

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

//Affichage des données pour la modification
$('body').on('click', '.edit', function (e) {
    e.preventDefault();

    $('#btnModif').show();
    $('#btnRegister').hide();

    //Récupération des détails de la ligne en cours de modification
    var datas = $(this).data('id');

    //Rédefinition du titre de la modal
    $('#staticBackdropLabel').empty();
    $title = $('#staticBackdropLabel').text(`Modification du produit [ ${datas.r_nom_produit} ]`);

    //Affection des valeurs aux champs du formualaire
    $('#r_nom_produit').val(datas.r_nom_produit);
    $('#r_description').val(datas.r_description);
    let id = $('#r_description').val(datas.id);

    $('#staticBackdrop').modal('show');

});

$('body').on('click', '.edit', function (e) {
    e.preventDefault();

    //Récupération des détails de la ligne en cours de modification
    var datas = $(this).data('id');

    //Rédefinition du titre de la modal
    $('#staticBackdropLabel').empty();
    $title = $('#staticBackdropLabel').text(`Modification du produit [ ${datas.r_nom_produit} ]`);

    //Affection des valeurs aux champs du formualaire
    $('#r_nom_produit').val(datas.r_nom_produit);
    $('#r_description').val(datas.r_description);
    let id = $('#r_description').val(datas.id);

    $('#staticBackdrop').modal('show');

    //Ajax
    $.ajax({
        url: "/produits",
        type:"PUT",
        data:{
            "_token": "{{ csrf_token() }}",
            r_nom_produit:r_nom_produit,
            r_description:r_description
        },
        success:function(response){

            if( response._status == 1 ){

                //Réinitialisation du formualaires
                $("#formProduits")[0].reset();

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
