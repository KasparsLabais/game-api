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

        <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@200;400;600&family=Josefin+Sans:wght@300;400&family=Raleway:wght@200;400;600;800&display=swap" rel="stylesheet">

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

            .josefin-sans {
                font-family: 'Josefin Sans', sans-serif;
            }
        </style>

        <script>
            @if(Auth::check())
                window.username  = '{!! Auth::user()->username !!}';
                window.id  = {!! Auth::user()->id !!};
                window.playerToken =  '{!! Auth::user()->unique_token !!}'
                window.avatar = @if(is_null(Auth::user()->avatar)) '/images/default-avatar.jpg' @else '{!! Auth::user()->avatar !!}' @endif;
                window.playerType = 'player';
                window.iconFlair = @if(!is_null(Auth::user()->icon_flair_id)) '{!! Auth::user()->iconFlair->icon_url !!}' @else '' @endif;
            @else
                window.username = @if(session('tmp-user-username')) '{{ session('tmp-user-username') }}' @else  'guest' @endif;
                window.id = @if(session('tmp-user-id')) '{{ session('tmp-user-id') }}' @else 0 @endif;
                window.playerToken = @if(session('tmp-user-playertoken')) '{{ session('tmp-user-playertoken') }}' @else '' @endif;
                window.avatar = '/images/default-avatar.jpg';
                window.playerType = 'guest';
                window.iconFlair = '';
            @endif
        </script>
    </head>
    <body class="flex flex-col w-full h-full justify-top bg-zinc-800">
        <div class="bg-yellow-500 z-20">
            @include('game-api::partials.nav')
        </div>
        <div class="min-h-screen">
            @yield('body')
        </div>
        <div>
            @include('game-api::partials.footer')
        </div>
        @include('game-api::partials.notifications')
        <x-modal target="game-modal"></x-modal>
    </body>
</html>