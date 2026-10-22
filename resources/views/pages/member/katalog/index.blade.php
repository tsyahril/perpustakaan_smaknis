@extends('layouts.member')

@php
    $judulHalaman = $kategoriAktif?->nama
        ?? ($filter['q'] !== '' ? 'Hasil untuk "' . $filter['q'] . '"' : 'Katalog buku');
    $urlUrut = fn (string $kunci) => route('katalog.index', array_merge(\Illuminate\Support\Arr::except(request()->query(), ['page']), ['urut' => $kunci]));
    $adaFilter = count($chips) > 0;

    // Kategori yang selalu tampil: delapan terbanyak, ditambah yang sedang dipilih.
    $kategoriTampil = $kategoriTeratas->when($kategoriAktif, fn ($k) => $k->push($kategoriAktif))->unique('id')->values();
    $kategoriSisa = $kategoris->whereNotIn('id', $kategoriTampil->pluck('id'))->values();
    $penerbitTampil = $penerbits->take(6);
    $penerbitSisa = $penerbits->slice(6)->values();
@endphp

@section('title', $judulHalaman)

@section('content')
<x-member.remah-roti :item="$kategoriAktif ? ['Katalog' => route('katalog.index'), $kategoriAktif->nama => null] : ['Katalog' => null]" />

<div class="wadah pb-4 pt-4" x-data="{ filter: false }" @keydown.escape.window="filter = false">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $judulHalaman }}</h1>
            <p class="mt-1 text-sm text-slate-500" aria-live="polite">
                @if ($bukus->total() > 0)
                    Menampilkan {{ $bukus->firstItem() }}-{{ $bukus->lastItem() }} dari {{ number_format($bukus->total(), 0, ',', '.') }} buku
                @else
                    Tidak ada buku yang cocok
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" class="btn-soft lg:hidden" @click="filter = true">
                <x-member.ikon nama="sliders" kelas="h-4 w-4" /> Filter
                @if ($adaFilter)<span class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-brand-600 px-1 text-[11px] text-white">{{ count($chips) }}</span>@endif
            </button>
            <label class="flex items-center gap-2 text-sm text-slate-500">
                <span class="hidden sm:inline">Urutkan</span>
                <select class="input !w-auto !py-2 pr-9" onchange="window.location.href = this.value" aria-label="Urutkan buku">
                    @foreach ($urutan as $kunci => $label)
                        <option value="{{ $urlUrut($kunci) }}" @selected($filter['urut'] === $kunci)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>

    @if ($adaFilter)
        <div class="mt-4 flex flex-wrap items-center gap-2">
            @foreach ($chips as $chip)
                <a href="{{ $chip['url'] }}" class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 py-1.5 pl-3 pr-2 text-xs font-semibold text-brand-700 hover:bg-brand-100">
                    {{ $chip['label'] }} <x-member.ikon nama="x" kelas="h-3.5 w-3.5" />
                </a>
            @endforeach
            <a href="{{ route('katalog.index') }}" class="ml-1 text-xs font-semibold text-slate-500 underline-offset-2 hover:text-slate-800 hover:underline">Hapus semua</a>
        </div>
    @endif

    <div class="mt-6 grid gap-8 lg:grid-cols-[17rem_1fr]">

        {{-- Filter: panel samping di desktop, laci di layar kecil --}}
        <div x-show="filter" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 lg:hidden" @click="filter = false"></div>

        <aside class="fixed inset-y-0 left-0 z-50 w-[85%] max-w-sm -translate-x-full overflow-y-auto bg-white p-5 shadow-2xl transition lg:static lg:z-auto lg:w-auto lg:max-w-none lg:translate-x-0 lg:overflow-visible lg:bg-transparent lg:p-0 lg:shadow-none"
               :class="filter && '!translate-x-0'" aria-label="Filter katalog">
            <div class="mb-4 flex items-center justify-between lg:hidden">
                <p class="text-base font-bold">Filter</p>
                <button type="button" class="rounded-lg p-2 hover:bg-slate-100" @click="filter = false" aria-label="Tutup filter"><x-member.ikon nama="x" /></button>
            </div>

            <form method="GET" action="{{ route('katalog.index') }}" class="space-y-7 rounded-2xl lg:bg-white lg:p-5 lg:shadow-kartu lg:ring-1 lg:ring-slate-100">
                @if ($filter['q'] !== '')<input type="hidden" name="q" value="{{ $filter['q'] }}">@endif
                @if ($filter['penulis'])<input type="hidden" name="penulis" value="{{ $filter['penulis'] }}">@endif
                <input type="hidden" name="urut" value="{{ $filter['urut'] }}">

                <fieldset x-data="{ semua: false, cari: '' }">
                    <legend class="mb-3 text-sm font-bold text-slate-900">Kategori</legend>
                    <div class="space-y-0.5">
                        <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                            <input type="radio" name="kategori" value="" class="text-brand-600 focus:ring-brand-500" @checked(! $filter['kategori']) @change="$el.form.requestSubmit()">
                            <span class="flex-1">Semua kategori</span>
                        </label>
                        @foreach ($kategoriTampil as $k)
                            <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                                <input type="radio" name="kategori" value="{{ $k->id }}" class="text-brand-600 focus:ring-brand-500" @checked($filter['kategori'] === $k->id) @change="$el.form.requestSubmit()">
                                <span class="flex-1 truncate">{{ $k->nama }}</span>
                                <span class="text-xs text-slate-400">{{ $k->bukus_count }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if ($kategoriSisa->isNotEmpty())
                        <div x-show="semua" x-cloak class="mt-2 border-t border-slate-100 pt-2">
                            <input type="search" x-model="cari" placeholder="Cari kategori..." class="input mb-2 !py-1.5" aria-label="Cari kategori">
                            <div class="max-h-56 space-y-0.5 overflow-y-auto pr-1">
                                @foreach ($kategoriSisa as $k)
                                    <label x-show="@js(\Illuminate\Support\Str::lower($k->nama)).includes(cari.toLowerCase().trim())" class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                                        <input type="radio" name="kategori" value="{{ $k->id }}" class="text-brand-600 focus:ring-brand-500" @change="$el.form.requestSubmit()">
                                        <span class="flex-1 truncate">{{ $k->nama }}</span>
                                        <span class="text-xs text-slate-400">{{ $k->bukus_count }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="mt-2 text-xs font-semibold text-brand-700 hover:underline" @click="semua = ! semua" x-text="semua ? 'Sembunyikan' : 'Lihat {{ $kategoriSisa->count() }} kategori lainnya'">Lihat {{ $kategoriSisa->count() }} kategori lainnya</button>
                    @endif
                </fieldset>

                <fieldset>
                    <legend class="mb-3 text-sm font-bold text-slate-900">Ketersediaan</legend>
                    <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                        <input type="checkbox" name="tersedia" value="1" class="rounded text-brand-600 focus:ring-brand-500" @checked($filter['tersedia']) @change="$el.form.requestSubmit()">
                        Hanya yang tersedia
                    </label>
                </fieldset>

                @if ($penerbits->isNotEmpty())
                    <fieldset x-data="{ semua: false }">
                        <legend class="mb-3 text-sm font-bold text-slate-900">Penerbit</legend>
                        <div class="space-y-0.5">
                            @foreach ($penerbitTampil as $p)
                                <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                                    <input type="checkbox" name="penerbit[]" value="{{ $p->penerbit }}" class="rounded text-brand-600 focus:ring-brand-500" @checked(in_array($p->penerbit, $filter['penerbit'], true)) @change="$el.form.requestSubmit()">
                                    <span class="flex-1 truncate">{{ $p->penerbit }}</span>
                                    <span class="text-xs text-slate-400">{{ $p->total }}</span>
                                </label>
                            @endforeach
                            @foreach ($penerbitSisa as $p)
                                <label x-show="semua" x-cloak class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                                    <input type="checkbox" name="penerbit[]" value="{{ $p->penerbit }}" class="rounded text-brand-600 focus:ring-brand-500" @checked(in_array($p->penerbit, $filter['penerbit'], true)) @change="$el.form.requestSubmit()">
                                    <span class="flex-1 truncate">{{ $p->penerbit }}</span>
                                    <span class="text-xs text-slate-400">{{ $p->total }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if ($penerbitSisa->isNotEmpty())
                            <button type="button" class="mt-2 text-xs font-semibold text-brand-700 hover:underline" @click="semua = ! semua" x-text="semua ? 'Sembunyikan' : 'Lihat {{ $penerbitSisa->count() }} penerbit lainnya'">Lihat {{ $penerbitSisa->count() }} penerbit lainnya</button>
                        @endif
                    </fieldset>
                @endif

                <fieldset>
                    <legend class="mb-3 text-sm font-bold text-slate-900">Tahun terbit</legend>
                    <div class="flex items-center gap-2">
                        <input type="number" name="tahun_dari" value="{{ $filter['tahun_dari'] }}" min="1000" max="9999" placeholder="Dari" class="input !py-2" aria-label="Tahun terbit dari">
                        <span class="text-slate-300">-</span>
                        <input type="number" name="tahun_sampai" value="{{ $filter['tahun_sampai'] }}" min="1000" max="9999" placeholder="Sampai" class="input !py-2" aria-label="Tahun terbit sampai">
                    </div>
                </fieldset>

                <div class="flex gap-2 pt-1">
                    <button type="submit" class="btn-primary flex-1">Terapkan</button>
                    @if ($adaFilter)
                        <a href="{{ route('katalog.index') }}" class="btn-soft">Reset</a>
                    @endif
                </div>
            </form>
        </aside>

        {{-- Hasil --}}
        <div>
            @if ($bukus->isEmpty())
                <x-member.kosong ikon="search" judul="Belum ada buku yang cocok" pesan="Coba kata kunci yang lebih umum, kurangi filter, atau periksa ejaannya.">
                    <a href="{{ route('katalog.index') }}" class="btn-primary">Lihat semua buku</a>
                </x-member.kosong>
            @else
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 xl:grid-cols-4">
                    @foreach ($bukus as $buku)
                        <x-member.buku-card :buku="$buku" />
                    @endforeach
                </div>

                <div class="mt-10">{{ $bukus->onEachSide(1)->links('vendor.pagination.member') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
