<script>
    const openHamburger = () => {
        document.getElementById('mobile-nav').classList.remove('hidden');
        document.getElementById('nav-overlay').classList.remove('hidden');
    }
    const closeHamburger = () => {
        document.getElementById('mobile-nav').classList.add('hidden');
        document.getElementById('nav-overlay').classList.add('hidden');
    }
    const closeAvatarMenu = () => {
        const m = document.getElementById('avatar-menu');
        if (!m) return;
        m.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
        m.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
    }
    const toggleAvatarMenu = () => {
        const m = document.getElementById('avatar-menu');
        if (!m) return;
        if (m.classList.contains('opacity-0')) {
            m.classList.remove('opacity-0', '-translate-y-2', 'pointer-events-none');
            m.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
        } else {
            closeAvatarMenu();
        }
    }
    document.addEventListener('click', (e) => {
        const wrap = document.getElementById('avatar-wrap');
        if (wrap && !wrap.contains(e.target)) closeAvatarMenu();
    });
</script>

<div class="flex w-full items-center justify-between gap-3 px-4 md:px-8 py-2">

    {{-- Brand --}}
    <a href="/" class="russo text-xl md:text-2xl text-white tracking-wide shrink-0">Quiz Crave</a>

    {{-- Center toolbar: an online-count "button" sitting next to the browser
         address bar (which works as a live notification ticker). --}}
    <div class="hidden md:flex flex-1 max-w-2xl items-center gap-2">
        {{-- Online users — styled like a browser toolbar button --}}
        <div class="flex items-center gap-2 bg-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-3 py-1.5 shrink-0" title="Players online now">
            <span class="relative flex h-2.5 w-2.5">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span>
            </span>
            <span class="fira-sans text-main-dark text-sm font-bold whitespace-nowrap"><span data-online-users-count>&ndash;</span> online</span>
        </div>
        {{-- Address bar ticker --}}
        <div class="flex flex-1 items-center bg-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-3 py-1.5 min-w-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" class="w-4 h-4 shrink-0 stroke-main-dark">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            <span id="nav-ticker" class="ml-2 fira-sans text-main-dark text-sm truncate transition-opacity duration-300">Welcome to Quiz Crave</span>
        </div>
    </div>

    {{-- Right: toolbar links + avatar / login --}}
    <div class="flex items-center gap-2 shrink-0">
        {{-- Compact online badge (mobile only) --}}
        <div class="md:hidden flex items-center gap-1.5 bg-white border-4 border-main-dark shadow-[2px_2px_0_0_#14323B] px-2 py-1.5" title="Players online">
            <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
            </span>
            <span class="fira-sans text-main-dark text-xs font-bold" data-online-users-count>&ndash;</span>
        </div>
        <div class="hidden md:flex items-center gap-1">
            <a href="/" class="fira-sans font-bold uppercase text-sm tracking-wide text-white px-3 py-2 hover:bg-main-dark/20 transition-colors">Home</a>
            {{-- Play dropdown: pick which game to browse --}}
            <div class="relative group">
                <button type="button" class="flex items-center gap-1.5 fira-sans font-bold uppercase text-sm tracking-wide text-white px-3 py-2 hover:bg-main-dark/20 transition-colors">
                    Play
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 transition-transform duration-150 group-hover:rotate-180"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                </button>
                {{-- pt-2 is a hover bridge so the menu doesn't close on the gap --}}
                <div class="absolute left-0 top-full w-52 pt-2 z-50 origin-top transition-all duration-150 opacity-0 -translate-y-2 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto">
                    <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-2 flex flex-col gap-2">
                        <a href="/trivia" class="flex items-center gap-3 px-3 py-2 fira-sans font-bold text-main-dark border-4 border-transparent transition-all duration-100 hover:border-main-dark hover:bg-brand hover:text-white hover:shadow-[3px_3px_0_0_#14323B]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5 shrink-0" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="8.5" cy="8.5" r="1.4" fill="currentColor"/><circle cx="15.5" cy="8.5" r="1.4" fill="currentColor"/><circle cx="12" cy="12" r="1.4" fill="currentColor"/><circle cx="8.5" cy="15.5" r="1.4" fill="currentColor"/><circle cx="15.5" cy="15.5" r="1.4" fill="currentColor"/></svg>
                            Trivia
                        </a>
                        <a href="/battleship" class="flex items-center gap-3 px-3 py-2 fira-sans font-bold text-main-dark border-4 border-transparent transition-all duration-100 hover:border-main-dark hover:bg-main-dark hover:text-white hover:shadow-[3px_3px_0_0_#14323B]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 shrink-0" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5h18l-1.8 4.5a2 2 0 0 1-1.86 1.26H6.66A2 2 0 0 1 4.8 18L3 13.5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5V6m0 0 3.5 2M12 6 8.5 8M6.5 13.5v-3h11v3"/></svg>
                            Battleships
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin panel — only for users who can access it. --}}
        @if(Auth::check() && method_exists(Auth::user(), 'hasPermission') && Auth::user()->hasPermission('admin.access'))
            <a href="/admin/dashboard" class="hidden md:inline-flex items-center gap-1.5 fira-sans font-black uppercase text-sm tracking-wide bg-main-dark text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-3 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" class="w-4 h-4 stroke-current shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                Admin
            </a>
        @endif

        @auth
            {{-- Avatar with slide-down dropdown --}}
            <div id="avatar-wrap" class="relative hidden md:block">
                <button type="button" onclick="toggleAvatarMenu()" aria-label="Account menu"
                        class="block w-10 h-10 bg-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] overflow-hidden transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">
                    <img src="{{ Auth::user()->avatar ?: '/images/default-avatar.jpg' }}" onerror="this.src='/images/default-avatar.jpg'" class="w-full h-full object-cover">
                </button>
                <div id="avatar-menu" class="absolute right-0 top-full mt-3 w-56 bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-2 flex flex-col gap-2 z-50 origin-top transition-all duration-150 opacity-0 -translate-y-2 pointer-events-none">
                    <div class="px-2 py-1 fira-sans text-xs uppercase tracking-widest text-main-dark/50">{{ Auth::user()->username }}</div>
                    <a href="/profile" class="flex items-center gap-3 px-3 py-2 fira-sans font-bold text-main-dark border-4 border-transparent transition-all duration-100 hover:border-main-dark hover:bg-pop hover:shadow-[3px_3px_0_0_#14323B]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" class="w-5 h-5 stroke-main-dark shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        Profile
                    </a>
                    <a href="/trv/management" class="flex items-center gap-3 px-3 py-2 fira-sans font-bold text-main-dark border-4 border-transparent transition-all duration-100 hover:border-main-dark hover:bg-brand hover:text-white hover:shadow-[3px_3px_0_0_#14323B]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" class="w-5 h-5 stroke-current shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>
                        My Trivias
                    </a>
                    <a href="/trv/seasons" class="flex items-center gap-3 px-3 py-2 fira-sans font-bold text-main-dark border-4 border-transparent transition-all duration-100 hover:border-main-dark hover:bg-pop hover:shadow-[3px_3px_0_0_#14323B]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" class="w-5 h-5 stroke-main-dark shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                        </svg>
                        Seasons
                    </a>
                    <a href="/trv/events" class="flex items-center gap-3 px-3 py-2 fira-sans font-bold text-main-dark border-4 border-transparent transition-all duration-100 hover:border-main-dark hover:bg-main-accent hover:text-white hover:shadow-[3px_3px_0_0_#14323B]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" class="w-5 h-5 stroke-current shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.25-13.5h16.5a1.5 1.5 0 0 1 1.5 1.5v1.5a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V9a1.5 1.5 0 0 1 1.5-1.5Z" />
                        </svg>
                        Events
                    </a>
                    <a href="/logout" class="flex items-center gap-3 px-3 py-2 fira-sans font-bold text-main-dark border-4 border-transparent transition-all duration-100 hover:border-main-dark hover:bg-main-accent hover:text-white hover:shadow-[3px_3px_0_0_#14323B]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" class="w-5 h-5 stroke-current shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        @else
            <a href="/login" class="hidden md:inline-block fira-sans font-black uppercase text-sm tracking-wide bg-main-accent text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">Login</a>
        @endauth

        {{-- Mobile hamburger --}}
        <button type="button" class="md:hidden flex items-center justify-center w-10 h-10 bg-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] text-main-dark" onclick="openHamburger()" aria-label="Menu">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>
</div>

{{-- Mobile slide-in nav --}}
<div id="nav-overlay" class="hidden top-0 left-0 opacity-70 bg-main-dark w-screen h-screen fixed z-40" onclick="closeHamburger()"></div>
<div id="mobile-nav" class="hidden flex flex-col fixed right-0 top-0 bg-white border-l-4 border-main-dark w-3/4 h-full z-50">
    <div class="flex justify-end p-4 border-b-4 border-main-dark" onclick="closeHamburger()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6 text-main-dark cursor-pointer">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </div>
    <div class="flex flex-col p-4 gap-3">
        <a href="/" class="fira-sans font-bold text-main-dark border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-white">Home</a>
        <span class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/40 px-1 pt-1">Play</span>
        <a href="/trivia" class="fira-sans font-bold text-main-dark border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-white">Trivia</a>
        <a href="/battleship" class="fira-sans font-bold text-white border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-main-dark">Battleships</a>
        @auth
            <a href="/profile" class="fira-sans font-bold text-main-dark border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-pop">Profile</a>
            <a href="/trv/management" class="fira-sans font-bold text-white border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-brand">My Trivias</a>
            <a href="/trv/seasons" class="fira-sans font-bold text-main-dark border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-pop">Seasons</a>
            <a href="/trv/events" class="fira-sans font-bold text-white border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-main-accent">Events</a>
            @if(method_exists(Auth::user(), 'hasPermission') && Auth::user()->hasPermission('admin.access'))
                <a href="/admin/dashboard" class="fira-sans font-bold text-white border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-main-dark">Admin Panel</a>
            @endif
            <a href="/logout" class="fira-sans font-bold text-white border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-main-accent">Logout</a>
        @else
            <a href="/login" class="fira-sans font-bold text-white border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-main-accent">Login</a>
        @endauth
    </div>
</div>

<script>
    (function () {
        var el = document.getElementById('nav-ticker');
        if (!el) return;

        var online = null, liveGames = null;
        function messages() {
            var m = [];
            @auth m.push('Welcome back, {{ Auth::user()->username }}'); @endauth
            if (liveGames !== null) m.push(liveGames > 0 ? (liveGames + (liveGames === 1 ? ' live game' : ' live games') + ' — jump in') : 'No live games yet — start one');
            m.push('Enter a game PIN on the home page to join');
            return m;
        }

        var i = 0;
        function tick() {
            var msgs = messages();
            el.style.opacity = '0';
            setTimeout(function () {
                el.textContent = msgs[i % msgs.length];
                el.style.opacity = '1';
                i++;
            }, 300);
        }
        el.textContent = messages()[0];
        setInterval(tick, 4500);

        // Update every online-count badge (desktop toolbar + mobile).
        function applyOnline(c) {
            online = c;
            document.querySelectorAll('[data-online-users-count]').forEach(function (s) { s.textContent = c; });
        }

        // Live stats from the shared socket (created by game.js).
        var sock = window.socket;
        if (sock) {
            function refresh() {
                sock.emit('getOnlineUsersCount', {}, applyOnline);
                sock.emit('getGameInstances', {}, function (games) { liveGames = games ? Object.keys(games).length : 0; });
            }
            sock.on('onlineUsersCount', applyOnline);
            if (sock.connected) refresh();
            sock.on('connect', refresh);
        }
    })();
</script>
