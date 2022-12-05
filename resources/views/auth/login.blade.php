<x-guest-layout>
    <x-auth-card>

        <!-- Main Container -->
        <main id="main-container" style="background-color: #ebeef2;" >
            <!-- Page Content -->
            <div class="hero-static d-flex align-items-center">
              <div class="content">
                <div class="row justify-content-center push">
                  <div class="col-md-8 col-lg-6 col-xl-4">
                    <!-- Sign In Block -->
                    <div class="block block-rounded mb-0">
                      <div class="bg-dark block-header block-header-default">
                        <h3 class="block-title">Se connecter</h3>
                        <div class="block-options">
                            @if (Route::has('password.request'))
                                      <a class="text-primary btn-block-option fs-sm" href="{{ route('password.request') }}">
                                          {{ __('Mot de passe oublié') }}
                                      </a>
                                  @endif
                          <a class="btn-block-option text-white" href="#" data-bs-toggle="tooltip" data-bs-placement="left" title="New Account">
                            <i class="fa fa-user-plus"></i>
                          </a>
                        </div>
                      </div>
                      <div class="block-content">
                        <div class="p-sm-3 px-lg- px-xxl-3 py-lg-5 mb-3">

                          <h4 class="text-dark text-center">
                            Veuillez saisir vos accès pour vous connecter.
                          </h4>
                          <br>

                          <!-- Session Status -->
                          <x-auth-session-status class="mb-4" :status="session('status')" />

                          <!-- Validation Errors -->
                          <x-auth-validation-errors class="mb-4 text-danger" :errors="$errors" />


                          <!-- Sign In Form -->
                          <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _js/pages/op_auth_signin.js) -->
                          <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                          <form method="POST" action="{{ route('login') }}">
                              @csrf

                              <!-- Email Address -->
                              <div>
                                  <x-label for="email" :value="__('Identifiant')" />

                                  <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                              </div>

                              <!-- Password -->
                              <div class="mt-4">
                                  <x-label for="password" :value="__('Mot de passe')" />

                                  <x-input id="password" class="block mt-1 w-full"
                                                  type="password"
                                                  name="password"
                                                  required autocomplete="current-password" />
                              </div>

                              <!-- Remember Me -->
                              {{-- <div class="block mt-4">
                                  <label for="remember_me" class="inline-flex items-center">
                                      <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                                      <span class="ml-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
                                  </label>
                              </div> --}}
                              <div class="clerfix">&nbsp;&nbsp;</div>

                              <div class="flex items-center justify-end mt-4">
                                  {{-- @if (Route::has('password.request'))
                                      <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                                          {{ __('Mot de passe oublié') }}
                                      </a>
                                  @endif --}}

                                  <x-button class="ml-3 float-end">
                                      {{ __('Je me connecte') }}
                                  </x-button>
                              </div>
                          </form>
                          <!-- END Sign In Form -->
                        </div>
                      </div>
                    </div>
                    <!-- END Sign In Block -->
                  </div>
                </div>
                <div class="fs-sm text-dark text-center">
                  <strong>&copy; &nbsp; copyright</strong>&nbsp;{{ date('Y') }}&nbsp;Orange Bank Africa, tous droits réservés<span data-toggle="year-copy"></span>
                </div>
              </div>
            </div>
            <!-- END Page Content -->
          </main>
          <!-- END Main Container -->



    </x-auth-card>

</x-guest-layout>

