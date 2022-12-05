<!doctype html>
<html lang="{{ config('app.locale') }}">

    <?php
    $public_folder = '';
    /* if (strpos(php_sapi_name(), 'cli') === true) {
        // Run from command
        $public_folder = "public/";
    } */
    $public_folder = "/";
?>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <meta name="description" content="OneUI - Bootstrap 5 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">
  <meta name="author" content="pixelcave">
  <meta name="robots" content="noindex, nofollow">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Icons -->
  <link rel="shortcut icon" href="{{ asset($public_folder.'media/favicons/favicon.png') }}">
  <link rel="icon" sizes="192x192" type="image/png" href="{{ asset($public_folder.'images/orangebank-banque.jpg') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($public_folder.'media/favicons/apple-touch-icon-180x180.png') }}">

  <!-- Styles -->
  @yield('css_before')
  <link rel="stylesheet" id="css-main" href="{{ asset($public_folder.'css/oneui.css') }}">

  <!-- You can include a specific file from public/css/themes/ folder to alter the default color theme of the template. eg: -->
  <!-- <link rel="stylesheet" id="css-theme" href="{{ mix('css/themes/amethyst.css') }}"> -->
  @yield('css_after')

  <link rel="stylesheet" href="{{ asset($public_folder.'css/app.css') }}">

  <!-- ---------------------------------->
  {{-- <link rel="stylesheet" href="{{ asset('boosted/dist/css/boosted.min.css') }}"> --}}
  <link rel="stylesheet" href="{{ asset($public_folder.'css/workflow.css') }}">
  <link rel="stylesheet" href="{{ asset($public_folder.'css/style.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/boosted@5.2.0/dist/css/orange-helvetica.min.css" rel="stylesheet" integrity="sha384-A0Qk1uKfS1i83/YuU13i2nx5pk79PkIfNFOVzTcjCMPGKIDj9Lqx9lJmV7cdBVQZ" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/boosted@5.2.0/dist/css/boosted.min.css" rel="stylesheet" integrity="sha384-CYwBBOBitXWralvRBTTEteu86YHHydT/+Wsv4kmxP6oF/SaesPQeMxLbblEUyZf/" crossorigin="anonymous">
  <!-- Scripts -->
  <script>
    window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
  </script>
</head>

<body>
  <div id="page-container" class="sidebar-o enable-page-overlay sidebar-dark side-scroll page-header-fixed main-content-narrow">
    <!-- Side Overlay-->
        @include('layouts.main.sidebar')
    <!-- END Side Overlay -->

    <!-- Sidebar -->

        @include('layouts.main.nav')

    <!-- END Sidebar -->

    <!-- Header -->
        @include('layouts.main.header')
    <!-- END Header -->



    <!-- Main Container -->
    <main id="main-container">

      @yield('content')

    </main>
    <!-- END Main Container -->

    <!-- Footer -->

    @include('layouts.main.footer')

    <!-- END Footer -->
  </div>
  <!-- END Page Container -->

  <!-- OneUI Core JS -->
  <script src="{{ asset($public_folder.'js/oneui.app.js') }}"></script>

  <!-- Laravel Scaffolding JS -->
  <!-- <script src="{{ mix('/js/laravel.app.js') }}"></script> -->

  @yield('js_after')

  <script src="https://cdn.jsdelivr.net/npm/boosted@5.2.0/dist/js/boosted.bundle.min.js" integrity="sha384-NkAB+hiFHsv6dxwL0C4lJtfq1RblxY6+DRFn5QZDpgCdwB5RiOGjaJB0Weq0uCy3" crossorigin="anonymous"></script>
  <script src="{{ asset($public_folder.'js/app.js') }}"></script>
 <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script src="{{ asset($public_folder.'js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>

 <!-- Désactivation du clic droit-->
{{--  <script>
    document.oncontextmenu = function() {
  return false;
}
 </script> --}}


</body>

</html>
