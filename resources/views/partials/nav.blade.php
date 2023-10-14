<div class="flex w-full justify-between">
    <div class="flex flex-row px-12">
        <img src="https://via.placeholder.com/65" alt="Logo">
        <div class="flex flex-col justify-center">
            <h1 class="px-2 fira-sans">Party Games</h1>
        </div>
    </div>
    <div class="flex flex-row justify-center raleway">
        <div class="flex flex-col justify-center">
            <a href="/" class="px-8 py-2 hover:text-slate-300 hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                Home
            </a>
        </div>
        <div class="flex flex-col justify-center">
            <a href="/games" class="px-8 py-2 hover:text-slate-300 hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                Games
            </a>
        </div>
        @if(Auth::check())
        <div class="flex flex-col justify-center">
            <a href="/profile" class="px-8 py-2 hover:text-slate-300 hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">Profile</a>
        </div>
        @endif
        @if(Auth::check())
            <div class="flex">
                <a href="/logout" class="px-8 py-2 hover:text-slate-300	hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                    Logout
                </a>
            </div>
        @else
            <div class="flex">
                <a href="/login" class="px-8 py-2 hover:text-slate-300	hover:bg-gray-800 ease-linear duration-200 h-16 flex flex-col justify-center">
                    Login
                </a>
            </div>
        @endif
    </div>
</div>
<hr>