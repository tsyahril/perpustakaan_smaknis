@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-[#DDD5C9] focus:border-sepia-700 focus:ring-sepia-700 rounded-md shadow-sm']) !!}>
