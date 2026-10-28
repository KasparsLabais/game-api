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
            <a href="/trivia" class="fira-sans font-bold uppercase text-sm tracking-wide text-white px-3 py-2 hover:bg-main-dark/20 transition-colors">Play</a>
        </div>

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
        <a href="/trivia" class="fira-sans font-bold text-main-dark border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-white">Play Trivia</a>
        @auth
            <a href="/profile" class="fira-sans font-bold text-main-dark border-4 border-main-dark px-4 py-3 shadow-[3px_3px_0_0_#14323B] bg-pop">Profile</a>
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
