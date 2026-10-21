@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-sepia-400 text-start text-base font-medium text-sepia-700 bg-sepia-50 focus:outline-none focus:text-sepia-800 focus:bg-sepia-100 focus:border-sepia-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[#7C6F62] hover:text-[#332B24] hover:bg-[#FAF9F7] hover:border-[#DDD5C9] focus:outline-none focus:text-[#332B24] focus:bg-[#FAF9F7] focus:border-[#DDD5C9] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
