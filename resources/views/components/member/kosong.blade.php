@props(['ikon' => 'book-open', 'judul', 'pesan' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center']) }}>
    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600">
        <x-member.ikon :nama="$ikon" kelas="h-7 w-7" />
    </span>
    <h3 class="mt-4 text-base font-bold text-slate-900">{{ $judul }}</h3>
    @if ($pesan)
        <p class="mt-1.5 max-w-sm text-sm text-slate-500">{{ $pesan }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-5 flex flex-wrap justify-center gap-3">{{ $slot }}</div>
    @endif
</div>
