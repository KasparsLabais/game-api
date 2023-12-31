@if($isALink)
    <a href="{{ $link }}" class="w-full text-center fira-sans py-2 px-2 md:px-6 shadow-md bg-yellow-400	text-slate-100 font-semibold text-lg">{{ $slot }}</a>
@else
    <button type="{{ $type }}" @if($onClick != '') onclick="{{ $onClick }}" @endif class="fira-sans py-2 px-2 md:px-6 shadow-md bg-yellow-400 text-slate-100 font-semibold text-lg">{{ $slot }}</button>
@endif
