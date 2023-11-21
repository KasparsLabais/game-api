<div class="flex flex-col">
    @if($addHeader)
    <div class="flex flex-col px-2 py-2 bg-yellow-400 shadow">
        <h1 class="bg-slate-200  py-2 px-2 raleway text-lg">{{ $title }}</h1>
    </div>
    @endif
    <div class="w-full flex flex-col raleway @if($addHeader) py-2 px-2 @endif ">
        {{ $slot }}
    </div>
</div>