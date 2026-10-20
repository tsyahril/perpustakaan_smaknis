@extends('layouts.admin')

@section('title', 'Dashboard Pustakawan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-400">
                Perpustakaan SMK
            </p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-100">
                Dashboard Pustakawan
            </h1>
            <p class="mt-1 text-sm text-slate-400">
                Selamat datang, {{ $user->name }}. Pantau kegiatan operasional perpustakaan di sini.
            </p>
        </div>

        <div class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-700 bg-[#0b1d32] px-4 py-3 text-sm text-slate-300">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            Panel operasional
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 shadow-lg shadow-black/10">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-400">Total Koleksi Buku</p>
                    <p class="mt-3 text-3xl font-bold text-white">
                        {{ number_format($totalBuku, 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs text-slate-500">Buku terdaftar di katalog</p>
                </div>
                <div class="rounded-xl border border-blue-400/20 bg-blue-500/10 p-3 text-blue-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5.5A2.5 2.5 0 016.5 3H20v16H6.5A2.5 2.5 0 014 16.5v-11zM4 16.5A2.5 2.5 0 016.5 14H20M8 7h8"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.buku.index') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-blue-300 transition hover:text-blue-200">
                Kelola buku <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        <div class="rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 shadow-lg shadow-black/10">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-400">Pengajuan Peminjaman</p>
                    <p class="mt-3 text-3xl font-bold text-white">
                        {{ number_format($pengajuanPeminjaman, 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs text-slate-500">Menunggu persetujuan</p>
                </div>
                <div class="rounded-xl border border-amber-400/20 bg-amber-500/10 p-3 text-amber-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.request-peminjaman.index') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-amber-300 transition hover:text-amber-200">
                Lihat pengajuan <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        <div class="rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 shadow-lg shadow-black/10">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-400">Peminjaman Aktif</p>
                    <p class="mt-3 text-3xl font-bold text-white">
                        {{ number_format($peminjamanAktif, 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs text-slate-500">Belum dikembalikan</p>
                </div>
                <div class="rounded-xl border border-emerald-400/20 bg-emerald-500/10 p-3 text-emerald-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.peminjaman.index') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-emerald-300 transition hover:text-emerald-200">
                Lihat peminjaman <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        <div class="rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 shadow-lg shadow-black/10">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-400">Riwayat Pengembalian</p>
                    <p class="mt-3 text-3xl font-bold text-white">
                        {{ number_format($riwayatPengembalian, 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs text-slate-500">Transaksi yang dikembalikan</p>
                </div>
                <div class="rounded-xl border border-violet-400/20 bg-violet-500/10 p-3 text-violet-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12a9 9 0 109-9 9 9 0 00-6.36 2.64L3 8m0-5v5h5m4-1v5l3 2"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.pengembalian.index') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-violet-300 transition hover:text-violet-200">
                Buka riwayat <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>

    {{-- Aksi cepat --}}
    <div>
        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-100">Akses Operasional</h2>
            <p class="mt-1 text-sm text-slate-400">Pilih menu untuk melanjutkan pekerjaan.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

            <a href="{{ route('admin.buku.index') }}" class="group rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 transition duration-200 hover:-translate-y-0.5 hover:border-blue-400/50 hover:bg-[#102640]">
                <div class="flex items-start gap-4">
                    <div class="rounded-xl border border-blue-400/20 bg-blue-500/10 p-3 text-blue-300 transition group-hover:bg-blue-500/20">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5.5A2.5 2.5 0 016.5 3H20v16H6.5A2.5 2.5 0 014 16.5v-11zM4 16.5A2.5 2.5 0 016.5 14H20"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-100">Kelola Data Buku</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-400">Atur koleksi dan informasi buku perpustakaan.</p>
                        <span class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-blue-300">Buka menu <span aria-hidden="true">&rarr;</span></span>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.request-peminjaman.index') }}" class="group rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 transition duration-200 hover:-translate-y-0.5 hover:border-amber-400/50 hover:bg-[#102640]">
                <div class="flex items-start gap-4">
                    <div class="rounded-xl border border-amber-400/20 bg-amber-500/10 p-3 text-amber-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-100">Pengajuan Peminjaman</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-400">Periksa dan tindak lanjuti permintaan peminjaman.</p>
                        <span class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-amber-300">Buka menu <span aria-hidden="true">&rarr;</span></span>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.request-pengembalian.index') }}" class="group rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 transition duration-200 hover:-translate-y-0.5 hover:border-emerald-400/50 hover:bg-[#102640]">
                <div class="flex items-start gap-4">
                    <div class="rounded-xl border border-emerald-400/20 bg-emerald-500/10 p-3 text-emerald-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12a9 9 0 109-9 9 9 0 00-6.36 2.64L3 8m0-5v5h5m4-1v5l3 2"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-100">Pemeriksaan Pengembalian</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-400">Tinjau permintaan dan proses pengembalian buku.</p>
                        <span class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-emerald-300">Buka menu <span aria-hidden="true">&rarr;</span></span>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.pengembalian.index') }}" class="group rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 transition duration-200 hover:-translate-y-0.5 hover:border-violet-400/50 hover:bg-[#102640]">
                <div class="flex items-start gap-4">
                    <div class="rounded-xl border border-violet-400/20 bg-violet-500/10 p-3 text-violet-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v6h6M20 20v-6h-6M5 15a7 7 0 0012 2l3-3M19 9A7 7 0 007 7L4 10"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-100">Riwayat Pengembalian</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-400">Lihat catatan pengembalian buku yang telah diproses.</p>
                        <span class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-violet-300">Buka menu <span aria-hidden="true">&rarr;</span></span>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.denda.index') }}" class="group rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 transition duration-200 hover:-translate-y-0.5 hover:border-rose-400/50 hover:bg-[#102640]">
                <div class="flex items-start gap-4">
                    <div class="rounded-xl border border-rose-400/20 bg-rose-500/10 p-3 text-rose-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v8m-4-4h8m5-8H3v16h18V4z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-100">Pengelolaan Denda</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-400">Periksa tagihan dan status pembayaran denda.</p>
                        <span class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-rose-300">Buka menu <span aria-hidden="true">&rarr;</span></span>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.peminjaman.index') }}" class="group rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 transition duration-200 hover:-translate-y-0.5 hover:border-cyan-400/50 hover:bg-[#102640]">
                <div class="flex items-start gap-4">
                    <div class="rounded-xl border border-cyan-400/20 bg-cyan-500/10 p-3 text-cyan-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-100">Data Peminjaman</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-400">Lihat dan kelola catatan transaksi peminjaman.</p>
                        <span class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-cyan-300">Buka menu <span aria-hidden="true">&rarr;</span></span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Ringkasan denda --}}
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-700 bg-[#0b1d32] p-5 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-3">
            <div class="rounded-xl border border-rose-400/20 bg-rose-500/10 p-3 text-rose-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v8m-4-4h8M5 3h14v18H5z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-slate-100">Denda Belum Lunas</h2>
                <p class="mt-1 text-sm text-slate-400">Transaksi pengembalian dengan denda yang belum ditandai lunas.</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-2xl font-bold text-white">{{ number_format($dendaBelumLunas, 0, ',', '.') }}</span>
            <a href="{{ route('admin.denda.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">
                Periksa denda <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>

</div>
@endsection