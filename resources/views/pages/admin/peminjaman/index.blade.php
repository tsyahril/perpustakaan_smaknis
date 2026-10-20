@extends('layouts.admin')

@section('title', 'Data Peminjaman')

@section('content')
<div class="space-y-5">

    {{-- Judul halaman --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-100">Data Peminjaman</h1>
            <p class="mt-1 text-sm text-slate-400">
                Kelola peminjaman buku yang telah disetujui.
                <a href="{{ route('admin.request-peminjaman.index') }}"
                   class="font-semibold text-sky-400 hover:text-sky-300 hover:underline">
                    Lihat Request Peminjaman
                </a>
            </p>
        </div>

        <a href="{{ route('admin.peminjaman.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-400/30 bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/20 transition hover:bg-blue-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Peminjaman
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

    {{-- Panel utama --}}
    <div class="overflow-hidden rounded-2xl border border-slate-700/80 bg-[#0b1d32] shadow-xl shadow-black/10">

        {{-- Pencarian dan filter --}}
        <div class="border-b border-slate-700/80 bg-[#102640] p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.peminjaman.index') }}"
                  class="grid grid-cols-1 gap-3 md:grid-cols-12">

                <div class="md:col-span-6">
                    <label for="search-peminjaman" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Cari Peminjaman
                    </label>
                    <input id="search-peminjaman" type="text" name="search"
                           value="{{ $search }}"
                           placeholder="Nama anggota atau judul buku..."
                           class="w-full rounded-xl border border-slate-600 bg-[#071426] px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/20">
                </div>

                <div class="md:col-span-3">
                    <label for="status-peminjaman" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Status
                    </label>
                    <select id="status-peminjaman" name="status"
                            class="w-full rounded-xl border border-slate-600 bg-[#071426] px-3.5 py-2.5 text-sm text-slate-100 focus:border-sky-400 focus:outline-none">
                        <option value="" class="bg-[#0b1d32] text-slate-100">Semua Status</option>
                        <option value="pinjam" {{ $status === 'pinjam' ? 'selected' : '' }} class="bg-[#0b1d32] text-slate-100">Masih Dipinjam</option>
                        <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }} class="bg-[#0b1d32] text-slate-100">Sudah Dikembalikan</option>
                    </select>
                </div>

                <div class="flex items-end gap-2 md:col-span-3">
                    <button type="submit"
                            class="flex-1 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">
                        Filter
                    </button>

                    @if ($search || $status !== '')
                        <a href="{{ route('admin.peminjaman.index') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-slate-600 bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-slate-700">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-700 bg-[#142c47] text-xs uppercase tracking-wider text-slate-300">
                        <th class="w-14 px-4 py-4 text-center font-semibold">No.</th>
                        <th class="px-4 py-4 font-semibold">Kode / Anggota</th>
                        <th class="px-4 py-4 font-semibold">Buku / Stok</th>
                        <th class="px-4 py-4 text-center font-semibold">Tanggal Pinjam</th>
                        <th class="px-4 py-4 text-center font-semibold">Status</th>
                        <th class="w-36 px-4 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-700/70">
                    @forelse ($peminjamans as $i => $peminjaman)
                        <tr class="transition-colors hover:bg-[#142c47]/80">

                            <td class="px-4 py-4 text-center text-slate-400">
                                {{ $peminjamans->firstItem() + $i }}
                            </td>

                            <td class="px-4 py-4">
                                @if ($peminjaman->kode_peminjaman)
                                    <p class="mb-1 font-mono text-xs font-semibold text-sky-300">
                                        {{ $peminjaman->kode_peminjaman }}
                                    </p>
                                @endif

                                <p class="font-semibold leading-5 text-slate-100">
                                    {{ $peminjaman->anggota->nama }}
                                </p>
                                <p class="mt-1 text-xs leading-5 text-slate-400">
                                    {{ $peminjaman->anggota->nomor_anggota }}
                                    &middot;
                                    {{ ucfirst($peminjaman->anggota->jenis_anggota) }}
                                    @if ($peminjaman->anggota->kelas)
                                        &middot; {{ $peminjaman->anggota->kelas->nama }}
                                    @endif
                                </p>
                            </td>

                            <td class="px-4 py-4">
                                <p class="max-w-xs font-medium leading-5 text-slate-100">
                                    {{ $peminjaman->buku->judul }}
                                </p>
                                <p class="mt-1 text-xs leading-5 text-slate-400">
                                    {{ $peminjaman->buku->penulis }}
                                </p>
                                <p class="mt-1 text-xs text-slate-400">
                                    Sisa stok:
                                    <span class="font-bold {{ $peminjaman->buku->stok > 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                                        {{ $peminjaman->buku->stok }}
                                    </span>
                                </p>
                            </td>

                            <td class="px-4 py-4 text-center">
                                <p class="whitespace-nowrap font-medium text-slate-200">
                                    {{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}
                                </p>
                                <p class="my-1 text-xs text-slate-500">&darr; batas pengembalian</p>
                                <p class="whitespace-nowrap text-xs font-semibold text-sky-300">
                                    {{ $peminjaman->tanggalRencanaLabel() }}
                                </p>

                                @if ($peminjaman->tanggal_kembali_rencana)
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $peminjaman->tanggal_kembali_rencana->diffInDays($peminjaman->tanggal_pinjam) }} hari
                                    </p>
                                @else
                                    <p class="mt-1 text-xs text-amber-300">Tanpa rencana</p>
                                @endif
                            </td>

                            {{-- Badge status dengan warna kontras --}}
                            <td class="px-4 py-4 text-center">
                                @if ($peminjaman->status === 'pinjam')
                                    @if ($peminjaman->hitungHariTerlambat() > 0)
                                        <span class="inline-flex max-w-full items-center justify-center whitespace-nowrap rounded-full border border-rose-400/40 bg-rose-500/15 px-3 py-1.5 text-xs font-bold text-rose-300">
                                            Terlambat {{ $peminjaman->hitungHariTerlambat() }} hari
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center whitespace-nowrap rounded-full border border-sky-400/40 bg-sky-500/15 px-3 py-1.5 text-xs font-bold text-sky-300">
                                            Dipinjam
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex max-w-full items-center justify-center whitespace-nowrap rounded-full border border-emerald-400/40 bg-emerald-500/15 px-3 py-1.5 text-xs font-bold text-emerald-300">
                                        Dikembalikan
                                    </span>
                                    @if ($peminjaman->kondisi)
                                        <p class="mt-1.5 text-xs text-slate-400">
                                            Kondisi: {{ ucfirst($peminjaman->kondisi) }}
                                        </p>
                                    @endif
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    @if ($peminjaman->status === 'pinjam')
                                        <a href="{{ route('admin.peminjaman.edit', $peminjaman) }}"
                                           class="inline-flex items-center rounded-lg border border-sky-400/30 bg-sky-500/10 px-3 py-1.5 text-xs font-semibold text-sky-300 transition hover:bg-sky-500/20">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.peminjaman.destroy', $peminjaman) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus peminjaman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs font-semibold text-rose-300 transition hover:bg-rose-500/20">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center rounded-lg border border-slate-600 bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-300">
                                            Selesai
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-14 text-center">
                                <p class="font-semibold text-slate-200">Belum ada data peminjaman</p>
                                <p class="mt-1 text-sm text-slate-400">
                                    Data peminjaman yang disetujui akan muncul di sini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($peminjamans->hasPages())
            <div class="flex flex-col gap-3 border-t border-slate-700/80 bg-[#102640] px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-slate-400">
                    Menampilkan {{ $peminjamans->firstItem() }}–{{ $peminjamans->lastItem() }}
                    dari {{ $peminjamans->total() }} data
                </p>
                <div class="text-sm">
                    {{ $peminjamans->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
