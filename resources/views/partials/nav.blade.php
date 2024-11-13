<script>

    const openHamburger = () => {
        document.getElementById('mobile-nav').classList.remove('hidden');
        document.getElementById('nav-overlay').classList.remove('hidden');
    }

    const closeHamburger = () => {
        document.getElementById('mobile-nav').classList.add('hidden');
        document.getElementById('nav-overlay').classList.add('hidden');
    }

</script>

<div class="flex w-full justify-between">
    <div class="flex flex-row md:px-12">
        <!--<img src="/images/quizcrave2.png" style="height: 65px;" alt="Logo">-->
        <div class="flex flex-col justify-center">
            <h1 class="px-2 fira-sans text-xl font-semibold">Quiz Crave</h1>
        </div>
    </div>
    <div class="flex flex-row justify-center raleway">
        <div class="hidden md:flex">
            <div class="flex flex-col justify-center">
                <a href="/" class="px-8 py-2 hover:font-semibold hover:text-slate-300 hover:bg-zinc-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                    Home
                </a>
            </div>
            <div class="flex flex-col justify-center">
                <a href="/trivia" class="px-8 py-2 hover:font-semibold hover:text-slate-300 hover:bg-zinc-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                    Play Trivia
                </a>
            </div>
            @if(Auth::check())
                <div class="flex flex-col justify-center">
                    <a href="/profile" class="px-8 py-2 hover:font-semibold hover:text-slate-300 hover:bg-zinc-800 ease-linear duration-200 h-16 flex flex-col justify-center">Profile</a>
                </div>
            @endif
            @if(Auth::check())
                <div class="flex flex-col justify-center">
                    <a href="/logout" class="px-8 py-2 hover:font-semibold  hover:text-slate-300	hover:bg-zinc-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                        Logout
                    </a>
                </div>
            @else
                <div class="flex flex-col justify-center">
                    <a href="/login" class="px-8 py-2 hover:font-semibold hover:text-slate-300 hover:bg-zinc-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                        Login
                    </a>
                </div>
            @endif
        </div>
        <div class="flex flex-col justify-center px-4 md:hidden">
            <div onclick="openHamburger()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </div>
            <div id="nav-overlay" class="hidden top-0 left-0 opacity-70 bg-gray-700 w-screen h-screen absolute z-30" onclick="closeHamburger()">
            </div>
            <div id="mobile-nav" class="hidden flex flex-col absolute right-0 top-0 bg-slate-100 w-3/4 h-full mx-auto z-50">
                <div class="flex flex-col justify-center" onclick="closeHamburger()">
                    <span class="border-b border-b-slate-300 px-8 py-2 hover:text-slate-300 hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                        <svg  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                </div>
                <div class="flex flex-col justify-center text-center">
                    <a href="/" class="border-b border-b-slate-300 px-8 py-2 hover:text-slate-300 hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                        Home
                    </a>
                </div>
                <div class="flex flex-col justify-center text-center">
                    <a href="/games" class="border-b border-b-slate-300 px-8 py-2 hover:text-slate-300 hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                        Games
                    </a>
                </div>
                @if(Auth::check())
                    <div class="flex flex-col justify-center text-center">
                        <a href="/profile" class="border-b border-b-slate-300 px-8 py-2 hover:text-slate-300 hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">Profile</a>
                    </div>
                @endif
                @if(Auth::check())
                    <div class="flex flex-col justify-center text-center">
                        <a href="/logout" class="border-b border-b-slate-300 px-8 py-2 hover:text-slate-300	hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                            Logout
                        </a>
                    </div>
                @else
                    <div class="flex flex-col justify-center text-center">
                        <a href="/login" class="border-b border-b-slate-300 px-8 py-2 hover:text-slate-300	hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                            Login
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>