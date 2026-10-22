@extends('layouts.member')

@section('title', 'Beranda')

@section('content')
@php
    $sampul = $bukuBaru->take(3);
    $keunggulan = [
        ['search', 'Cari lebih cepat', 'Filter kategori, penerbit, dan tahun terbit untuk menemukan buku yang pas.'],
        ['package', 'Stok terlihat langsung', 'Tahu sebelum datang apakah bukunya masih ada di rak.'],
        ['clock', 'Pantau jatuh tempo', 'Pengingat di lonceng membantu menghindari denda keterlambatan.'],
        ['shield', 'Riwayat tercatat rapi', 'Semua pinjaman, pengembalian, dan denda bisa dibuka kapan saja.'],
    ];
@endphp

{{-- Banner utama --}}
<section class="wadah pt-6" x-data="hero(3)" @mouseenter="berhenti()" @mouseleave="mulai()">
    <div class="relative overflow-hidden rounded-3xl bg-brand-950">
        <div class="relative min-h-[500px] sm:min-h-[360px] lg:min-h-[380px]">

            {{-- Slide 1: sambutan --}}
            <div x-show="i === 0" x-transition.opacity.duration.500ms class="absolute inset-0 grid items-center gap-8 bg-gradient-to-br from-brand-700 via-brand-800 to-brand-950 p-7 sm:px-16 sm:py-12 lg:grid-cols-[1.2fr_1fr]">
                <div class="relative z-10 max-w-xl text-white">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-200">Perpustakaan SMAKNIS</p>
                    <h1 class="mt-3 text-3xl font-extrabold leading-[1.15] tracking-tight sm:text-4xl lg:text-[2.75rem]">Temukan bacaan berikutnya, tanpa antre.</h1>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-brand-100 sm:text-base">Jelajahi {{ number_format($statistik['judul'], 0, ',', '.') }} judul buku, ajukan peminjaman dari ponselmu, lalu ambil di perpustakaan.</p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('katalog.index') }}" class="btn !bg-amber-400 !text-amber-950 hover:!bg-amber-300">Jelajahi katalog <x-member.ikon nama="arrow-right" kelas="h-4 w-4" /></a>
                        <a href="{{ route('panduan') }}" class="btn border border-white/30 text-white hover:bg-white/10">Cara meminjam</a>
                    </div>
                </div>
                <div class="relative hidden h-full lg:block" aria-hidden="true">
                    @foreach ($sampul as $n => $buku)
                        <div class="absolute w-32 xl:w-36" style="right: {{ 2 + $n * 8 }}rem; top: {{ 3 + ($n % 2) * 2.5 }}rem; transform: rotate({{ -7 + $n * 6 }}deg); z-index: {{ $n }};">
                            <x-member.cover :buku="$buku" class="shadow-2xl ring-4 ring-white/10" />
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Slide 2: buku baru --}}
            <div x-show="i === 1" x-cloak x-transition.opacity.duration.500ms class="absolute inset-0 grid items-center gap-8 bg-gradient-to-br from-amber-400 via-amber-400 to-amber-500 p-7 sm:px-16 sm:py-12 lg:grid-cols-[1.2fr_1fr]">
                <div class="max-w-xl text-amber-950">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-900/70">Baru di rak</p>
                    <h2 class="mt-3 text-3xl font-extrabold leading-[1.15] tracking-tight sm:text-4xl lg:text-[2.75rem]">Koleksi terbaru sudah bisa dipinjam.</h2>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-amber-900 sm:text-base">Lihat buku yang baru saja ditambahkan pustakawan, dari novel sampai buku pelajaran.</p>
                    <a href="{{ route('katalog.index', ['urut' => 'terbaru']) }}" class="btn mt-7 bg-brand-950 text-white hover:bg-brand-900">Lihat buku terbaru <x-member.ikon nama="arrow-right" kelas="h-4 w-4" /></a>
                </div>
                <div class="relative hidden h-full lg:block" aria-hidden="true">
                    @foreach ($sampul->reverse()->values() as $n => $buku)
                        <div class="absolute w-32 xl:w-36" style="right: {{ 2 + $n * 8 }}rem; top: {{ 3 + (($n + 1) % 2) * 2.5 }}rem; transform: rotate({{ 7 - $n * 6 }}deg); z-index: {{ $n }};">
                            <x-member.cover :buku="$buku" class="shadow-2xl ring-4 ring-white/30" />
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Slide 3: alur peminjaman --}}
            <div x-show="i === 2" x-cloak x-transition.opacity.duration.500ms class="absolute inset-0 flex flex-col justify-center bg-gradient-to-br from-brand-900 via-brand-950 to-slate-950 p-7 text-white sm:p-12">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-300">Tiga langkah</p>
                <h2 class="mt-3 max-w-xl text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">Dari layar ke tanganmu.</h2>
                <ol class="mt-8 grid gap-4 sm:grid-cols-3">
                    @foreach ([['Pilih buku', 'Masukkan ke keranjang pinjam, maksimal ' . \App\Services\PengajuanPeminjaman::MAKS_BUKU . ' buku.'], ['Ajukan', 'Tentukan tanggal kembali, lalu kirim pengajuan.'], ['Ambil di perpustakaan', 'Pustakawan menyetujui, kamu tinggal mengambil bukunya.']] as $n => [$judul, $uraian])
                        <li class="rounded-2xl bg-white/[0.07] p-4 ring-1 ring-white/10">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-amber-400 text-sm font-extrabold text-amber-950">{{ $n + 1 }}</span>
                            <p class="mt-3 text-sm font-bold">{{ $judul }}</p>
                            <p class="mt-1 text-xs leading-relaxed text-brand-200">{{ $uraian }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <button type="button" @click="mundur()" class="absolute left-3 top-1/2 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-slate-800 shadow-lg hover:bg-white sm:flex" aria-label="Slide sebelumnya"><x-member.ikon nama="chevron-left" /></button>
        <button type="button" @click="maju()" class="absolute right-3 top-1/2 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-slate-800 shadow-lg hover:bg-white sm:flex" aria-label="Slide berikutnya"><x-member.ikon nama="chevron-right" /></button>

        <div class="absolute inset-x-0 bottom-4 flex justify-center gap-2">
            @foreach ([0, 1, 2] as $n)
                <button type="button" @click="i = {{ $n }}" class="h-2 rounded-full transition-all" :class="i === {{ $n }} ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/60'" aria-label="Ke slide {{ $n + 1 }}"></button>
            @endforeach
        </div>
    </div>
</section>

{{-- Kategori pilihan --}}
@if ($kategoriPilihan->isNotEmpty())
    <section class="wadah mt-10">
        <div class="mb-4 flex items-end justify-between gap-4">
            <h2 class="judul-bagian">Kategori pilihan</h2>
            <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-700 hover:text-brand-800">Semua katalog <x-member.ikon nama="chevron-right" kelas="h-4 w-4" /></a>
        </div>
        <div class="grid grid-cols-4 gap-3 sm:gap-4 lg:grid-cols-8">
            @foreach ($kategoriPilihan as $kategori)
                <a href="{{ route('katalog.index', ['kategori' => $kategori->id]) }}" class="group flex flex-col items-center gap-2.5 rounded-2xl bg-white p-3 text-center shadow-kartu ring-1 ring-slate-100 transition hover:-translate-y-0.5 hover:shadow-kartu-naik sm:p-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white sm:h-14 sm:w-14">
                        <x-member.ikon :nama="\App\Support\Ikon::untukKategori($kategori->nama)" kelas="h-6 w-6" />
                    </span>
                    <span class="line-clamp-2 text-xs font-semibold leading-tight text-slate-700 sm:text-sm">{{ $kategori->nama }}</span>
                    <span class="text-[11px] text-slate-400">{{ $kategori->bukus_count }} buku</span>
                </a>
            @endforeach
        </div>
    </section>
@endif

<div class="mt-12 space-y-12">
    @if ($rekomendasi->isNotEmpty())
        <x-member.rak judul="Rekomendasi untukmu" subjudul="Dipilih dari kategori buku yang pernah kamu pinjam atau favoritkan." :bukus="$rekomendasi" />
    @endif

    <x-member.rak judul="Baru ditambahkan" :bukus="$bukuBaru" :lihat="route('katalog.index', ['urut' => 'terbaru'])" />

    <x-member.rak judul="Paling sering dipinjam" subjudul="Favorit teman-temanmu di perpustakaan." :bukus="$terpopuler" :lihat="route('katalog.index', ['urut' => 'populer'])" />

    @if ($terakhirDilihat->isNotEmpty())
        <x-member.rak judul="Terakhir kamu lihat" :bukus="$terakhirDilihat" />
    @endif

    @foreach ($rakKategori as $rak)
        <x-member.rak :judul="'Rak ' . $rak['kategori']->nama" :bukus="$rak['bukus']" :lihat="route('katalog.index', ['kategori' => $rak['kategori']->id])" />
    @endforeach
</div>

{{-- Keunggulan --}}
<section class="wadah mt-14">
    <div class="grid gap-4 rounded-3xl bg-white p-6 shadow-kartu ring-1 ring-slate-100 sm:grid-cols-2 lg:grid-cols-4 lg:p-8">
        @foreach ($keunggulan as [$ikon, $judul, $uraian])
            <div class="flex gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><x-member.ikon :nama="$ikon" kelas="h-5 w-5" /></span>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ $judul }}</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $uraian }}</p>
                </div>
            </div>
        @endforeach
    </div>
    <dl class="mt-6 grid grid-cols-3 divide-x divide-slate-200 text-center">
        @foreach ([['Judul buku', $statistik['judul']], ['Eksemplar tersedia', $statistik['eksemplar']], ['Kategori', $statistik['kategori']]] as [$label, $nilai])
            <div class="px-2">
                <dd class="text-2xl font-extrabold tracking-tight text-brand-700 sm:text-3xl">{{ number_format($nilai, 0, ',', '.') }}</dd>
                <dt class="mt-1 text-xs text-slate-500 sm:text-sm">{{ $label }}</dt>
            </div>
        @endforeach
    </dl>
</section>
@endsection
