@props(['nama', 'kelas' => 'h-5 w-5'])

<svg {{ $attributes->merge(['class' => $kelas]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! \App\Support\Ikon::markup($nama) !!}</svg>
