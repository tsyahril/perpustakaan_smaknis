@extends('layouts.admin')

@section('title', 'Data Buku')

@section('content')
<div class="space-y-6 text-slate-100">

    {{-- Header halaman --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10">
                    <svg class="h-6 w-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.5C9.5 4.5 6 4.5 3 5.5v13c3-1 6.5-1 9 1m0-13c2.5-2 6-2 9-1v13c-3-1-6.5-1-9 1m0-13v13"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">Data Buku</h1>
                    <p class="mt-1 text-sm text-slate-400">Kelola koleksi buku perpustakaan.</p>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.buku.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-400/20 bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-[#06101f]">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Buku
        </a>
    </div>

    {{-- Pesan validasi --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-400/20 bg-red-950/40 px-4 py-3 text-sm text-red-200">
            <p class="mb-2 font-semibold">Ada hal yang perlu diperiksa:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Panel utama --}}
    <div class="overflow-hidden rounded-2xl border border-[#263e5b] bg-[#0b1d32] shadow-xl shadow-black/10">

        {{-- Pencarian dan filter --}}
        <div class="border-b border-[#263e5b] bg-[#0e2239] p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.buku.index') }}"
                  class="grid grid-cols-1 gap-3 md:grid-cols-12">

                <div class="relative md:col-span-6">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                        <path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari judul, penulis, atau ISBN..."
                        class="w-full rounded-xl border border-[#304966] bg-[#071426] py-3 pl-11 pr-4 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div class="md:col-span-3">
                    <select name="id_kategori"
                            class="w-full rounded-xl border border-[#304966] bg-[#071426] px-3 py-3 text-sm text-slate-200 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ (string) $idKategori === (string) $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 md:col-span-3">
                    <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4"/>
                        </svg>
                        Cari
                    </button>

                    @if ($search || $idKategori)
                        <a href="{{ route('admin.buku.index') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-[#304966] bg-[#142b45] px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-[#1b3858] hover:text-white">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Informasi tabel --}}
        <div class="flex flex-col gap-1 border-b border-[#263e5b] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-white">Koleksi Buku</h2>
                <p class="mt-1 text-xs text-slate-400">Informasi buku yang tersimpan di perpustakaan.</p>
            </div>
            <div class="text-xs text-slate-400">
                @if ($bukus->total() > 0)
                    Menampilkan
                    <span class="font-semibold text-blue-300">{{ $bukus->firstItem() }}–{{ $bukus->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-slate-200">{{ $bukus->total() }}</span> buku
                @else
                    Belum ada data buku
                @endif
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-[#304966] bg-[#193451] text-xs uppercase tracking-wider text-slate-300">
                        <th class="whitespace-nowrap px-4 py-4 text-left font-semibold">No.</th>
                        <th class="whitespace-nowrap px-4 py-4 text-left font-semibold">Cover</th>
                        <th class="min-w-56 px-4 py-4 text-left font-semibold">Informasi Buku</th>
                        <th class="whitespace-nowrap px-4 py-4 text-left font-semibold">ISBN</th>
                        <th class="whitespace-nowrap px-4 py-4 text-left font-semibold">Kategori</th>
                        <th class="whitespace-nowrap px-4 py-4 text-center font-semibold">Tahun</th>
                        <th class="whitespace-nowrap px-4 py-4 text-center font-semibold">Stok</th>
                        <th class="whitespace-nowrap px-4 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#263e5b]">
                    @forelse ($bukus as $i => $buku)
                        <tr class="transition-colors hover:bg-[#142b45]">

                            <td class="whitespace-nowrap px-4 py-4 text-slate-500">
                                {{ $bukus->firstItem() + $i }}
                            </td>

                            <td class="px-4 py-4">
                                @if ($buku->coverUrl())
                                    <img
                                        src="{{ $buku->coverUrl() }}"
                                        alt="Cover {{ $buku->judul }}"
                                        loading="lazy"
                                        class="h-16 w-12 rounded-lg border border-[#344e6c] object-cover shadow-md shadow-black/20">
                                @else
                                    <div class="flex h-16 w-12 items-center justify-center rounded-lg border border-[#344e6c] bg-[#142b45]">
                                        <svg class="h-6 w-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 4.75A1.75 1.75 0 0 1 7.75 3H20v16H7.75A1.75 1.75 0 0 0 6 20.75m0-16v16m0-16H4v16h2"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>

                            <td class="min-w-56 px-4 py-4">
                                <p class="font-semibold leading-5 text-slate-100">{{ $buku->judul }}</p>
                                <p class="mt-1.5 text-xs text-slate-400">
                                    {{ $buku->penulis ?: 'Penulis tidak tersedia' }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $buku->penerbit ?: 'Penerbit tidak tersedia' }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-slate-300">
                                {{ $buku->isbn ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                <span class="inline-flex rounded-lg border border-blue-400/15 bg-blue-500/10 px-2.5 py-1 text-xs font-medium text-blue-200">
                                    {{ $buku->kategori->nama ?? 'Tanpa kategori' }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-center text-slate-300">
                                {{ $buku->tahun_terbit ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                <span class="inline-flex min-w-9 items-center justify-center rounded-lg border border-[#344e6c] bg-[#142b45] px-2.5 py-1.5 font-semibold text-slate-100">
                                    {{ $buku->stok }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.buku.show', $buku) }}"
                                       class="rounded-lg border border-[#344e6c] bg-[#142b45] px-2.5 py-2 text-xs font-medium text-slate-200 transition hover:border-blue-400/50 hover:bg-[#1b3858] hover:text-white">
                                        Detail
                                    </a>

                                    <a href="{{ route('admin.buku.edit', $buku) }}"
                                       class="rounded-lg border border-blue-400/20 bg-blue-500/10 px-2.5 py-2 text-xs font-semibold text-blue-300 transition hover:bg-blue-500/20">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.buku.destroy', $buku) }}"
                                          method="POST"
                                          class="inline"
                                          data-confirm="Hapus buku {{ $buku->judul }}?">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="rounded-lg border border-red-400/20 bg-red-500/10 px-2.5 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/20">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-[#304966] bg-[#142b45]">
                                    <svg class="h-7 w-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.5C9.5 4.5 6 4.5 3 5.5v13c3-1 6.5-1 9 1m0-13c2.5-2 6-2 9-1v13c-3-1-6.5-1-9 1m0-13v13"/>
                                    </svg>
                                </div>
                                <p class="mt-4 font-semibold text-slate-200">Data buku belum tersedia</p>
                                <p class="mt-1 text-sm text-slate-400">Coba ubah kata pencarian atau tambahkan buku baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($bukus->hasPages())
            <div class="border-t border-[#263e5b] bg-[#0e2239] px-4 py-4 sm:px-5">
                {{ $bukus->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
