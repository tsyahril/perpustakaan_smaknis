@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#4A4038]']) }}>
    {{ $value ?? $slot }}
</label>
