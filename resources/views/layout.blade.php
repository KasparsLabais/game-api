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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200;400;600&family=Raleway:wght@200;400;600;800&display=swap" rel="stylesheet">

        <script src="https://cdn.socket.io/4.5.0/socket.io.min.js" integrity="sha384-7EyYLQZgWBi67fBtVxw60/OWl1kjsfrPFcaU0pp0nAh+i8FD068QogUvg85Ewy1k" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

        <!-- Stylesheets -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('/vendor/game-api/js/game.js') }}?202310112030"></script>


        <style>
            .raleway {
                font-family: 'Raleway', sans-serif;
            }
            .fira-sans {
                font-family: 'Fira Sans', sans-serif;
            }
        </style>

        <script>
            @if(Auth::check())
                window.username  = '{!! Auth::user()->username !!}';
                window.id  = {!! Auth::user()->id !!};
                window.playerToken =  '{!! Auth::user()->unique_token !!}'
                window.avatar = @if(is_null(Auth::user()->avatar)) '/images/default-avatar.jpg' @else '{!! Auth::user()->avatar !!}' @endif;
            @else
                window.username = 'guest';
                window.id = 0;
                window.playerToken = '';
                window.avatar = '/images/default-avatar.jpg';
            @endif
        </script>
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