

<x-guest-layout>

    @section('js_after')

    <!-- jQuery (required for DataTables plugin) -->
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>

    <script>
        $(function(){

            $('#sendOTP').on('click',function(e){
                e.preventDefault();

                //Récupération des données à poste
                let dataPost= {
                    "_token": "{{ csrf_token() }}",
                    email: $('#email').val()
                }

                if ( dataPost.email.trim()                        == "") {
                    $('#otp_error').empty();
                    $('#otp_error').append('Veuillez saisir votre adresse email');
                    $('#erreurs').removeAttr('hidden', true);
                    return;
                }else{
                    $('#erreurs').attr('hidden', true);
                    $('#otp_error').empty();
                }

                $('.spinnerRegister').removeAttr('hidden');

                $.ajax({
                    //url: "{{ url('/oba/sendMessageGoogle') }}",
                    url: "{{ url('/oba/sendotp') }}/1",
                    type:"POST",
                    data:dataPost,
                    success:function(response){
                        $('.spinnerRegister').attr('hidden', true);
                        switch (response._status) {

                            case 0:
                            $('#erreurs').removeAttr('hidden', true);
                            $('#otp_error').empty();
                            //$('#otp_error').append('Une erreur est survenue lors de l\'envoie de l\'OTP, Veuillez contacter le service informatique');
                            $('#otp_error').append(response._avertissement);
                            break;

                            case 1:
                            $('#indication').empty();
                            $('#indication').append(`Un OTP à été envoyé sur l'adresse email ${dataPost.email}. La durée d'expiration est de 2 mn`);
                            $('#formOne').attr('hidden', true);
                            $('#otpForms').removeAttr('hidden');
                            break;

                            default:
                            $('#erreurs').removeAttr('hidden', true);
                            $('#otp_error').empty();
                            $('#otp_error').append('Une erreur est survenue lors de l\'envoie de l\'OTP, Veuillez contacter le service informatique');
                            break;
                        }

                    },
                    error: function(response) {
                        console.log(response);

                    }
                });


            });

            $('#btnotpverify').on('click',function(e){
                e.preventDefault();

                let dataPost= {
                    "_token": "{{ csrf_token() }}",
                    otp: $('#otp').val(),
                    email: $('#email').val(),
                    mode: 1
                }

                if ( dataPost.otp.trim()                        == "") {
                    $('#otp_error').empty();
                    $('#otp_error').append('Veuillez saisir l\'OTP');
                    $('#erreurs').removeAttr('hidden', true);
                    return;
                }else{
                    $('#otp_error').empty();
                    $('#erreurs').attr('hidden', true);
                }
                $('.spinnerRegister').removeAttr('hidden');
                $.ajax({
                    url: "{{ url('/oba/verifotp') }}/1",
                    type:"POST",
                    data:dataPost,
                    success:function(response){
                        $('.spinnerRegister').attr('hidden', true);

                        switch (response._status) {
                            case 0:
                            $('#otp_error').empty();
                            $('#otp_error').append(response._avertissement);
                            $('#erreurs').removeAttr('hidden', true);
                            break;

                            case 1:
                            $('#indication').empty();
                            $('#indication').append(`Vous pouvez maintenant définir un nouveau mot de passe`);

                            $('#otpForms').attr('hidden', true);
                            $('#formOne').attr('hidden', true);
                            $('#newmdpForms').removeAttr('hidden');
                            break;

                            default:
                            $('#indication').empty();
                            $('#indication').append('Une erreur est survenue lors de l\'envoie de l\'OTP, Veuillez contacter le service informatique');

                            break;
                        }

                    },
                    error: function(response) {
                        console.log(response);

                    }
                });

            });

            $('#btnNewPass').on('click',function(e){
                e.preventDefault();

                //Récupération des données à poste
                let dataPost= {
                    "_token": "{{ csrf_token() }}",
                    email: $('#email').val(),
                    mdp: $('#mdp').val(),
                    mdpConfirm: $('#mdpConfirm').val()
                }

                //Validation des champs
                if ( dataPost.mdp.trim()                        == "") {
                    $('#otp_error').empty();
                    $('#otp_error').append('Veuillez saisir le nouveau mot de passe');
                    $('#erreurs').removeAttr('hidden', true);
                    return;
                }else{
                    $('#otp_error').empty();
                    $('#erreurs').attr('hidden', true);
                }

                if ( dataPost.mdpConfirm.trim()                        == "") {
                    $('#otp_error').empty();
                    $('#otp_error').append('Veuillez confirmer le mot de passe');
                    $('#erreurs').removeAttr('hidden', true);
                    return;
                }else{
                    $('#otp_error').empty();
                    $('#erreurs').attr('hidden', true);
                }

                if ( dataPost.mdpConfirm.trim()                        !== dataPost.mdp.trim()) {
                    $('#otp_error').empty();
                    $('#otp_error').append('Les mots de passes ne correspondent pas');
                    $('#erreurs').removeAttr('hidden', true);
                    return;
                }else{
                    $('#otp_error').empty();
                    $('#erreurs').attr('hidden', true);
                }

                $('.spinnerRegister').removeAttr('hidden');

                $.ajax({
                    url: "{{ url('/passwordchange') }}",
                    type:"POST",
                    data:dataPost,
                    success:function(response){

                        $('.spinnerRegister').attr('hidden', true);


                        if( response._status == 1 ){

                            $('#otp_success').empty();
                            $('#otp_success').append(response._message);
                            $('#success').removeAttr('hidden', true);

                            setTimeout(() => {
                                location.href = "{{ url('/login') }}"
                            }, 1000);


                        }else{

                        }

                    },
                    error: function(response) {
                        console.log(response);

                    }
                });


            });

        })
    </script>

    @endsection

    <x-auth-card>



        <main id="main-container" style="background-color: #ebeef2;" >
            <!-- Page Content -->
            <div class="hero-static d-flex align-items-center">

                <div class="content">
                    <div class="row justify-content-center push">
                        <div class="col-md-6">

                            <div class="block block-rounded mb-0">
                                <div class="block-container m-3">

                                    <div class="block-content" id="success" hidden>
                                        <div class="alert alert-success alert-dismissible" role="alert" style="
                                        color: #3d6208;
                                        background-color: #e0edcf;
                                        border-color: #d1e3b6;
                                        box-shadow: 0 0.125rem #d4e6bc;
                                        ">
                                        <p class="mb-0" id="otp_success"></p>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                </div>

                                <div class="block-content" id="erreurs" hidden>
                                    <div class="alert alert-danger alert-dismissible" role="alert" style="color: #841717;
                                    background-color: #f8d4d4;
                                    border-color: #f5bebe;
                                    box-shadow: 0 0.125rem #f4bebe" >
                                    <p class="mb-0" id="otp_error"></p>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="
                                    position: absolute;
                                    top: 0;
                                    right: 0;
                                    z-index: 2;
                                    padding: 1.25rem 1rem;
                                    "></button>
                                </div>
                            </div>




                            <div class="container col-12 m-0">

                                <div class="clearfix" >&nbsp;</div>

                                <x-slot name="logo">
                                    <a href="/">
                                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                                    </a>
                                </x-slot>


                                <div class="mb-4 text-sm text-dark">
                                    <h6 id="indication">
                                        Mot de passe oublié? Aucun problème. <div class="clearfix" >&nbsp;</div> Indiquez-nous simplement votre adresse e-mail et nous vous enverrons par e-mail un lien de réinitialisation de mot de passe qui vous permettra d'en choisir un nouveau.
                                    </h6>
                                </div>

                                <!-- Session Status -->
                                <x-auth-session-status class="mb-4" :status="session('status')" />

                                <!-- Validation Errors -->
                                <x-auth-validation-errors class="mb-4" :errors="$errors" />

                                <form method="POST" id="formOne">
                                    @csrf

                                    <!-- Email Address -->
                                    <div>
                                        <x-label for="email" :value="__('Adresse email')" />

                                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <x-button id="sendOTP" class="float-end" >
                                            {{ __('Envoyer') }} &nbsp;
                                            <div class="spinner-border spinner-border-sm spinnerRegister" role="status" hidden>
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </x-button>

                                    </div>
                                </form>

                                <div class="clearfix" >&nbsp;&nbsp;</div>

                                <form method="POST" id="otpForms" hidden>
                                    @csrf

                                    <!-- Email Address -->
                                    <div>
                                        <x-label for="otp" :value="__('OTP')" />
                                        <x-input id="otp" class="block mt-1 w-full" type="text" name="otp" :value="old('otp')" required autofocus />
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <x-button id="btnotpverify" class="float-end">
                                            {{ __('Valider') }}&nbsp;
                                            <div class="spinner-border spinner-border-sm spinnerRegister" role="status" hidden>
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </x-button>
                                    </div>
                                </form>

                                <div class="clearfix" >&nbsp;&nbsp;</div>

                                <form method="POST" id="newmdpForms" hidden>
                                    @csrf

                                    <!-- Email Address -->
                                    <div>
                                        <x-label for="mdp" :value="__('Nouveau mot de passe')" />
                                        <x-input id="mdp" class="block mt-1 w-full" type="password" name="mdp" :value="old('mdp')" required autofocus />
                                        <span class="error" id="erreur_mdp" ></span>

                                    </div>

                                    <div>
                                        <x-label for="mdpConfirm" :value="__('Confirmation du mot de passe')" />
                                        <x-input id="mdpConfirm" class="block mt-1 w-full" type="password" name="mdpConfirm" :value="old('mdpConfirm')" required autofocus />
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <x-button id="btnNewPass" class="float-end" >
                                            {{ __('Enregistrer') }}&nbsp;
                                            <div class="spinner-border spinner-border-sm spinnerRegister" role="status" hidden>
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </x-button>
                                    </div>
                                </form>


                                <div class="clearfix" >&nbsp;&nbsp;</div>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>


</x-auth-card>
</x-guest-layout>
