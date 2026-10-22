@props(['buku'])

@php
    $habis = $buku->stok <= 0;
    $menipis = ! $habis && $buku->stok <= 3;
@endphp

<article class="group relative flex h-full flex-col rounded-xl bg-white p-3 shadow-kartu ring-1 ring-slate-100 transition duration-200 hover:-translate-y-0.5 hover:shadow-kartu-naik"
         x-data="tombolFavorit(@js(route('favorit.toggle', $buku)), @js($sesi->favorit($buku->id)))">
    <a href="{{ route('katalog.show', $buku) }}" class="block" tabindex="-1" aria-hidden="true">
        <div class="relative">
            <x-member.cover :buku="$buku" class="shadow-md transition duration-300 group-hover:shadow-lg" />
            @if ($habis)
                <span class="absolute inset-x-0 bottom-0 bg-slate-900/75 py-1 text-center text-[11px] font-semibold uppercase tracking-wide text-white">Stok habis</span>
            @elseif ($menipis)
                <span class="absolute left-2 top-2 rounded-md bg-amber-400 px-1.5 py-0.5 text-[10px] font-bold uppercase text-amber-950">Sisa {{ $buku->stok }}</span>
            @endif
        </div>
    </a>

    <form method="POST" action="{{ route('favorit.toggle', $buku) }}" class="absolute right-5 top-5">
        @csrf
        <button type="submit" @click.prevent="ubah()" :disabled="memuat"
                class="flex h-8 w-8 items-center justify-center rounded-full bg-white/95 shadow ring-1 ring-slate-200 transition hover:scale-110"
                :class="aktif ? 'text-rose-500' : 'text-slate-400 hover:text-rose-500'"
                :aria-pressed="aktif" aria-label="Favorit: {{ $buku->judul }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :fill="aktif ? 'currentColor' : 'none'" aria-hidden="true"><use href="#i-heart"/></svg>
        </button>
    </form>

    <div class="mt-3 flex flex-1 flex-col">
        @if ($buku->kategori)
            <a href="{{ route('katalog.index', ['kategori' => $buku->id_kategori]) }}" class="truncate text-[11px] font-semibold uppercase tracking-wide text-brand-600 hover:underline">{{ $buku->kategori->nama }}</a>
        @endif
        <h3 class="mt-1 line-clamp-2 text-sm font-bold leading-snug text-slate-900">
            <a href="{{ route('katalog.show', $buku) }}" class="hover:text-brand-700">{{ $buku->judul }}</a>
        </h3>
        <p class="mt-1 truncate text-xs text-slate-500">{{ $buku->penulis }}</p>

        <div class="mt-auto flex items-center justify-between gap-2 pt-3" x-data="tombolKeranjang(@js(route('keranjang.tambah', $buku)), @js($sesi->dikeranjang($buku->id)))">
            <span @class(['flex items-center gap-1.5 text-xs font-semibold', 'text-emerald-600' => ! $habis, 'text-slate-400' => $habis])>
                <span @class(['h-1.5 w-1.5 rounded-full', 'bg-emerald-500' => ! $habis, 'bg-slate-300' => $habis])></span>
                {{ $habis ? 'Tidak tersedia' : $buku->stok . ' tersedia' }}
            </span>
            @unless ($habis)
                <form method="POST" action="{{ route('keranjang.tambah', $buku) }}">
                    @csrf
                    <button type="submit" @click.prevent="sudah ? (window.location.href = @js(route('keranjang.index'))) : tambah()" :disabled="memuat"
                            class="flex h-8 w-8 items-center justify-center rounded-full border transition"
                            :class="sudah ? 'border-brand-600 bg-brand-600 text-white' : 'border-brand-200 text-brand-600 hover:border-brand-600 hover:bg-brand-600 hover:text-white'"
                            :aria-label="sudah ? 'Lihat keranjang' : @js('Tambah ke keranjang: ' . $buku->judul)">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <use href="#i-cart" x-show="! sudah"/>
                            <use href="#i-check" x-show="sudah" x-cloak/>
                        </svg>
                    </button>
                </form>
            @endunless
        </div>
    </div>
</article>
