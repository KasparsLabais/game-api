<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta charset="utf-8">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Tailwind -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Stylesheets -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

    </head>
    <body class="flex flex-col w-full h-screen justify-between">
        <div>
            @include('game-api::partials.nav')
        </div>
        <div>
            @yield('body')
        </div>
        <div>
            @include('game-api::partials.footer')
        </div>
    </body>
</html>