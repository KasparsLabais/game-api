<div class="flex flex-col mt-4 md:px-12">
    <div class="flex flex-row">
        <h1 class="josefin-sans text-yellow-400 font-semibold text-2xl">{{ $title }}</h1>
        <hr>
    </div>
    <div class="w-full flex flex-col">
        {{ $slot }}
    </div>
</div>