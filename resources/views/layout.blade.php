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
        <!--<script src="https://cdn.tailwindcss.com"></script>-->

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Honk&family=Russo+One&family=Space+Grotesk:wght@400;500;600;700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

        <script src="https://cdn.socket.io/4.5.0/socket.io.min.js" integrity="sha384-7EyYLQZgWBi67fBtVxw60/OWl1kjsfrPFcaU0pp0nAh+i8FD068QogUvg85Ewy1k" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

        <!-- Compiled Tailwind CSS + app JS (built from trivia/resources via Vite) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Scripts -->
        <script>window.socketServer = '{{ config('game-api.socket_url') }}';</script>
        <script src="{{ asset('/vendor/game-api/js/game.js') }}?202312251030"></script>


        <style>
            /* Brand typography (2026-10, retro/neo-brutalist): Honk = title only,
               Space Grotesk = headings, Space Mono = body, Russo One = arcade
               accents. Legacy class names are repointed so existing views pick
               up the new fonts without edits. */
            body { font-family: 'Space Mono', ui-monospace, monospace; }
            .raleway { font-family: 'Space Mono', ui-monospace, monospace; }
            .fira-sans { font-family: 'Space Grotesk', ui-sans-serif, sans-serif; }
            .josefin-sans { font-family: 'Space Grotesk', ui-sans-serif, sans-serif; }
            .honk { font-family: 'Honk', system-ui, cursive; font-weight: 400; }
            .russo { font-family: 'Russo One', ui-sans-serif, sans-serif; }
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
    <body class="flex flex-col w-full h-full justify-top @yield('page-bg', 'bg-main-light')">
        {{-- Nav as its own floating block: padded from the sides and top/bottom so
             it doesn't merge with the page edges. --}}
        <div class="px-[15px] lg:px-[30px] pt-2.5 pb-2.5">
            <div class="relative bg-brand text-white z-30 border-4 border-main-dark shadow-[6px_6px_0_0_#14323B]">
                @include('game-api::partials.nav')
            </div>
        </div>
        <div class="min-h-screen">
            @yield('body')
        </div>
        @include('game-api::partials.footer')
        @include('game-api::partials.notifications')
        @include('game-api::partials.friends')
        <x-modal target="game-modal"></x-modal>
    </body>
</html>