@props(['buku'])

@php
    $url = $buku->coverUrl();
    $warna = [
        'from-brand-700 to-brand-950',
        'from-emerald-600 to-emerald-900',
        'from-rose-600 to-rose-900',
        'from-violet-600 to-violet-900',
        'from-amber-600 to-amber-800',
        'from-slate-600 to-slate-900',
    ][$buku->id % 6];
@endphp

<div {{ $attributes->merge(['class' => 'relative aspect-[2/3] overflow-hidden rounded-md bg-slate-100']) }}>
    @if ($url)
        <img src="{{ $url }}" alt="Sampul buku {{ $buku->judul }}" loading="lazy" class="h-full w-full object-cover">
    @else
        <div class="flex h-full w-full flex-col items-center justify-center gap-3 bg-gradient-to-br {{ $warna }} p-4 text-center text-white">
            <x-member.ikon nama="book-open" kelas="h-7 w-7 opacity-70" />
            <span class="line-clamp-4 text-sm font-bold leading-snug">{{ $buku->judul }}</span>
            <span class="line-clamp-1 text-[11px] text-white/70">{{ $buku->penulis }}</span>
        </div>
    @endif
</div>
