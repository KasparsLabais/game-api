<div class="flex flex-col bg-slate-100">
    <div class="flex flex-col px-2 py-2 bg-slate-200 shadow">
        <p class="bg-slate-200  py-2 px-2 raleway text-lg">{{ $title }}</p>
    </div>
    <div class="w-full flex flex-col raleway py-2 px-2">
        {{ $slot }}
    </div>
</div>