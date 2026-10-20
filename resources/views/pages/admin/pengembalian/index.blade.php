@extends('layouts.admin')

@section('title', 'Riwayat Pengembalian')

@section('content')
@php
    $bisaKelola = auth()->user()?->canManageLibrary();
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-100">Riwayat Pengembalian</h1>
            <p class="mt-1 text-sm text-slate-400">
                Lihat ringkasan transaksi dan buka detail untuk mengelola setiap buku.
            </p>
        </div>

        <a href="{{ route('admin.request-pengembalian.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-400/30 bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">
            Request Pengembalian
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-sm font-medium text-rose-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter dan pencarian --}}
    <div class="overflow-hidden rounded-2xl border border-slate-700/80 bg-[#0b1d32] shadow-lg">
        <div class="border-b border-slate-700/80 bg-[#102640] px-4 py-4 sm:px-5">
            <h2 class="font-semibold text-slate-100">Pencarian dan Filter</h2>
            <p class="mt-1 text-xs text-slate-400">Cari berdasarkan nama peminjam, judul buku, atau kondisi buku.</p>
        </div>

        <form method="GET"
              action="{{ route('admin.pengembalian.index') }}"
              class="grid grid-cols-1 gap-3 p-4 md:grid-cols-12 sm:p-5">

            <div class="md:col-span-4">
                <label for="nama-peminjam" class="mb-1.5 block text-xs font-semibold text-slate-300">
                    Nama Peminjam
                </label>
                <input id="nama-peminjam" type="text" name="nama_peminjam"
                       value="{{ $namaPeminjam }}"
                       placeholder="Cari nama peminjam..."
                       class="w-full rounded-xl border border-slate-600 bg-[#071426] px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/20">
            </div>

            <div class="md:col-span-4">
                <label for="nama-buku" class="mb-1.5 block text-xs font-semibold text-slate-300">
                    Judul Buku
                </label>
                <input id="nama-buku" type="text" name="nama_buku"
                       value="{{ $namaBuku }}"
                       placeholder="Cari judul buku..."
                       class="w-full rounded-xl border border-slate-600 bg-[#071426] px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/20">
            </div>

            <div class="md:col-span-2">
                <label for="kondisi-buku" class="mb-1.5 block text-xs font-semibold text-slate-300">
                    Kondisi
                </label>
                <select id="kondisi-buku" name="kondisi"
                        class="w-full rounded-xl border border-slate-600 bg-[#071426] px-3.5 py-2.5 text-sm text-slate-100 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/20">
                    <option value="" class="bg-[#0b1d32] text-slate-100">Semua kondisi</option>
                    <option value="baik" {{ $kondisi === 'baik' ? 'selected' : '' }} class="bg-[#0b1d32] text-slate-100">Baik</option>
                    <option value="rusak" {{ $kondisi === 'rusak' ? 'selected' : '' }} class="bg-[#0b1d32] text-slate-100">Rusak</option>
                    <option value="hilang" {{ $kondisi === 'hilang' ? 'selected' : '' }} class="bg-[#0b1d32] text-slate-100">Hilang</option>
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-2">
                <button type="submit"
                        class="flex-1 rounded-xl bg-blue-600 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">
                    Cari Data
                </button>

                <a href="{{ route('admin.pengembalian.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-600 bg-slate-800 px-3 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-slate-700">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel ringkasan --}}
    <div class="overflow-hidden rounded-2xl border border-slate-700/80 bg-[#0b1d32] shadow-xl shadow-black/10">

        <div class="flex flex-col gap-3 border-b border-slate-700/80 bg-[#102640] p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div>
                <h2 class="font-bold text-slate-100">Data Pengembalian</h2>
                <p class="mt-1 text-xs text-slate-400">
                    Klik Detail untuk melihat buku dan informasi dendanya.
                </p>
            </div>

            <span class="inline-flex w-fit items-center rounded-full border border-sky-400/30 bg-sky-500/10 px-3 py-1.5 text-xs font-semibold text-sky-300">
                {{ $pengembalians->total() }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-700 bg-[#142c47] text-xs uppercase tracking-wider text-slate-300">
                        <th class="w-16 px-5 py-4 text-center font-semibold">No.</th>
                        <th class="px-5 py-4 font-semibold">Kode Peminjaman</th>
                        <th class="px-5 py-4 font-semibold">Peminjam</th>
                        <th class="px-5 py-4 text-center font-semibold">Jumlah Buku</th>
                        <th class="px-5 py-4 text-center font-semibold">Tanggal Kembali</th>
                        <th class="px-5 py-4 text-center font-semibold">Detail</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-700/70">
                    @forelse ($pengembalians as $i => $g)
                        <tr class="transition-colors hover:bg-[#142c47]/80">

                            <td class="px-5 py-4 text-center text-slate-400">
                                {{ $pengembalians->firstItem() + $i }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex whitespace-nowrap rounded-lg border border-sky-400/20 bg-sky-500/10 px-2.5 py-1 font-mono text-xs font-semibold text-sky-300">
                                    {{ $g->kode ?: 'TRX-' . $g->items->first()->id }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold leading-5 text-slate-100">
                                    {{ $g->anggota->nama }}
                                </p>
                                <p class="mt-1 text-xs leading-5 text-slate-400">
                                    No. Anggota: {{ $g->anggota->nomor_anggota }}
                                </p>
                                @if ($g->anggota->kelas)
                                    <p class="mt-0.5 text-xs text-slate-400">
                                        Kelas: {{ $g->anggota->kelas->nama }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex min-w-10 items-center justify-center rounded-lg border border-indigo-400/30 bg-indigo-500/10 px-3 py-1.5 font-bold text-indigo-300">
                                    {{ $g->jumlah_buku }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="font-medium text-slate-200">
                                    {{ $g->tanggal_kembali_aktual?->format('d/m/Y') ?? '-' }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('admin.pengembalian.show', $g->key) }}"
                                   class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-blue-400/30 bg-blue-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-blue-500">
                                    Lihat Detail
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="h-10 w-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.5v11m-4-7h8M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                    </svg>
                                    <p class="font-semibold text-slate-200">Data tidak ditemukan</p>
                                    <p class="text-sm text-slate-400">
                                        Coba ubah kata kunci atau reset filter.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($pengembalians->hasPages())
            <div class="flex flex-col gap-3 border-t border-slate-700/80 bg-[#102640] px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-400">
                    Menampilkan {{ $pengembalians->firstItem() }}–{{ $pengembalians->lastItem() }}
                    dari {{ $pengembalians->total() }} transaksi
                </p>
                <div class="text-sm">
                    {{ $pengembalians->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
