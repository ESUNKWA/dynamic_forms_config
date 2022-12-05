<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        {{-- <link rel="stylesheet" href="{{ asset('/css/app.css') }}"> --}}

        <link rel="stylesheet" id="css-main" href="{{ asset('/css/oneui.css') }}">

        <link href="https://cdn.jsdelivr.net/npm/boosted@5.2.0/dist/css/orange-helvetica.min.css" rel="stylesheet" integrity="sha384-A0Qk1uKfS1i83/YuU13i2nx5pk79PkIfNFOVzTcjCMPGKIDj9Lqx9lJmV7cdBVQZ" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/boosted@5.2.0/dist/css/boosted.min.css" rel="stylesheet" integrity="sha384-CYwBBOBitXWralvRBTTEteu86YHHydT/+Wsv4kmxP6oF/SaesPQeMxLbblEUyZf/" crossorigin="anonymous">

{{--  <link rel="stylesheet" href="{{ asset('boosted/dist/css/boosted.min.css') }}"> --}}

    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
        @yield('js_after')
        {{-- <script src="{{ asset('/js/app.js') }}"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/boosted@5.2.0/dist/js/boosted.bundle.min.js" integrity="sha384-NkAB+hiFHsv6dxwL0C4lJtfq1RblxY6+DRFn5QZDpgCdwB5RiOGjaJB0Weq0uCy3" crossorigin="anonymous"></script>

    </body>


</html>
