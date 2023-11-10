<div class="flex flex-col mt-4 px-4 md:px-12">
    <div class="flex flex-row">
        <h1 class="fira-sans font-semibold text-2xl">{{ $title }}</h1>
        <hr>
    </div>
    <div class="w-full flex flex-col">
        {{ $slot }}
    </div>
</div>