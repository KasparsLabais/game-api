@if($isALink)
    <a href="{{ $link }}" class="w-full text-center fira-sans py-2 px-2 md:px-6 shadow-md bg-lime-700 text-slate-100 font-semibold text-lg shadow-lime-700 rounded">{{ $slot }}</a>
@else
    <button type="{{ $type }}" @if($onClick != '') onclick="{{ $onClick }}" @endif class="w-full fira-sans py-2 px-2 md:px-6 shadow-md bg-lime-700 text-slate-100 font-semibold text-lg shadow-lime-700 rounded">{{ $slot }}</button>
@endif