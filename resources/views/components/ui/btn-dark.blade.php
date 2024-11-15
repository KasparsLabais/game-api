@if($isALink)
    <a href="{{ $link }}" class="w-full text-center fira-sans py-2 px-2 md:px-6 shadow btn-main-dark font-semibold text-lg shadow-gray-900 my-2">{{ $slot }}</a>
@else
    <button type="{{ $type }}" @if($onClick != '') onclick="{{ $onClick }}" @endif class="w-full fira-sans py-2 px-2 md:px-6 shadow btn-main-dark font-semibold text-lg shadow-gray-900 my-2">{{ $slot }}</button>
@endif