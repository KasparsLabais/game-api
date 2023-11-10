@if($isALink)
    <a href="{{ $link }}" class="w-full text-center fira-sans py-2 px-6 shadow bg-gray-200 text-lime-600 font-semibold text-lg shadow-lime-600">{{ $slot }}</a>
@else
    <button type="{{ $type }}" @if($onClick != '') onclick="{{ $onClick }}" @endif class="w-full text-center fira-sans py-2 px-6 shadow bg-gray-200 text-lime-600 font-semibold text-lg shadow-lime-600">{{ $slot }}</button>
@endif