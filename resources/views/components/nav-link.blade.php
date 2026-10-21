@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-sepia-400 text-sm font-medium leading-5 text-[#2D251E] focus:outline-none focus:border-sepia-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-[#97897B] hover:text-[#4A4038] hover:border-[#DDD5C9] focus:outline-none focus:text-[#4A4038] focus:border-[#DDD5C9] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
