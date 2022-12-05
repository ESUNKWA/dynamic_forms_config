

<x-guest-layout>

    @section('js_after')

    <!-- jQuery (required for DataTables plugin) -->
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>


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
                                    <div class="mb-4 text-sm text-dark">
                                        <h6 id="indication">
                                            Oops !!!!!!!!.
                                            <div class="clearfix" >&nbsp;</div>
                                        Accès réfusé</h6>
                                    </div>

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
