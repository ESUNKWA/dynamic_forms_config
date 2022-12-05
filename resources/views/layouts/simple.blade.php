<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

  <title>OBA | Système centralisé</title>

  <meta name="description" content="OneUI - Bootstrap 5 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">
  <meta name="author" content="pixelcave">
  <meta name="robots" content="noindex, nofollow">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Icons -->
  <link rel="shortcut icon" href="{{ asset('/media/favicons/favicon.png') }}">
  <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('/media/favicons/favicon-192x192.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/media/favicons/apple-touch-icon-180x180.png') }}">

  <!-- Styles -->
  @yield('css_before')
  <link rel="stylesheet" id="css-main" href="{{ asset('/css/oneui.css') }}">

  <!-- You can include a specific file from public/css/themes/ folder to alter the default color theme of the template. eg: -->
  <!-- <link rel="stylesheet" id="css-theme" href="{{ mix('/css/themes/amethyst.css') }}"> -->
  @yield('css_after')

  <!-- Scripts -->
  <script>
    window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
  </script>
</head>

<body>
  <div id="page-container">
    <!-- Main Container -->
    <main id="main-container">
      @yield('content')
    </main>
    <!-- END Main Container -->
  </div>
  <!-- END Page Container -->

  <!-- OneUI Core JS -->
  <script src="{{ asset('/js/oneui.app.js') }}"></script>

  <!-- Laravel Scaffolding JS -->
  <!-- <script src="{{ asset('/js/laravel.app.js') }}"></script> -->

  @yield('js_after')
</body>

</html>
