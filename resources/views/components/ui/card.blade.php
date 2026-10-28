<div class="flex flex-col">
    @if($addHeader)
    <h2 class="relative self-start inline-block mt-2 mb-6">
        <span aria-hidden="true" class="absolute inset-0 -rotate-2 bg-green-300"></span>
        <span class="relative block russo text-main-dark text-sm md:text-lg tracking-widest uppercase px-4 py-1.5">{{ $title }}</span>
    </h2>
    @endif
    <div class="w-full flex flex-col raleway @if($addHeader) py-2 px-2 @endif ">
        {{ $slot }}
    </div>
</div>