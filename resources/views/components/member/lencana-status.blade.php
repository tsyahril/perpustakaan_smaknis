@props(['status'])

<span {{ $attributes->merge(['class' => 'lencana ' . $status->kelasLencana()]) }}>{{ $status->label() }}</span>
