@extends('layouts.admin')

@section('title', $tab === 'pengajuan' ? 'Pengajuan Pengembalian' : 'Riwayat Pengembalian')

@section('content')
@php
    $bisaKelola = auth()->user()?->canManageLibrary();
    $urlPengajuan = route('admin.pengembalian.index', ['tab' => 'pengajuan']);
    $urlRiwayat = route('admin.pengembalian.index');
@endphp

<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#2D251E]">Pengembalian</h1>
            <p class="mt-1 text-sm text-[#8B7E71]">
                Pengajuan pengembalian dan riwayat pengembalian tersatukan dalam satu halaman.
            </p>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tab: Pengajuan | Riwayat --}}
    <div class="flex flex-wrap items-end gap-6 border-b border-[#E8E1D7]">
        <a href="{{ $urlPengajuan }}"
           class="inline-flex items-center gap-2 -mb-px border-b-2 pb-3 text-sm transition
                  {{ $tab === 'pengajuan'
                      ? 'border-sepia-600 font-semibold text-sepia-700'
                      : 'border-transparent text-[#8B7E71] hover:border-[#DDD5C9] hover:text-[#2D251E]' }}">
            Pengajuan
            <span class="inline-flex min-w-6 items-center justify-center rounded-full px-2 py-0.5 text-[11px] font-bold
                         {{ $tab === 'pengajuan' ? 'bg-sepia-600 text-white' : 'bg-[#F0EAE1] text-[#6B5E52]' }}">
                {{ $tab === 'pengajuan' ? $requests->total() : 0 }}
            </span>
        </a>

        <a href="{{ $urlRiwayat }}"
           class="inline-flex items-center gap-2 -mb-px border-b-2 pb-3 text-sm transition
                  {{ $tab === 'riwayat'
                      ? 'border-sepia-600 font-semibold text-sepia-700'
                      : 'border-transparent text-[#8B7E71] hover:border-[#DDD5C9] hover:text-[#2D251E]' }}">
            Riwayat
        </a>
    </div>

    @if ($tab === 'pengajuan')

        {{-- ================= TAB PENGAJUAN ================= --}}
        <p class="text-sm leading-6 text-[#8B7E71]">
            Pinjaman aktif yang menunggu verifikasi pengembalian. Setiap baris mewakili satu peminjaman.
            Riwayat buku yang sudah selesai ada di tab <a href="{{ $urlRiwayat }}" class="font-semibold text-sepia-700 hover:underline">Riwayat</a>.
        </p>

        @if ($telatCount > 0)
            <div class="flex items-start gap-3 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-700">
                <span class="font-bold">!</span>
                <span><strong>{{ $telatCount }} pinjaman</strong> telah melewati tanggal rencana pengembalian. Segera lakukan verifikasi.</span>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] shadow-sm">

            {{-- Filter --}}
            <div class="border-b border-[#E8E1D7] bg-[#F7F4EF] p-4 sm:p-5">
                <div class="mb-3">
                    <h2 class="text-sm font-semibold text-[#2D251E]">Cari transaksi pengembalian</h2>
                    <p class="mt-1 text-xs text-[#8B7E71]">Cari berdasarkan nama peminjam atau judul buku.</p>
                </div>

                <form method="GET" action="{{ $urlPengajuan }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <input type="hidden" name="tab" value="pengajuan">

                    {{-- Pencarian peminjam --}}
                    <div class="relative">
                        <label for="namaPeminjamKeyword" class="mb-1.5 block text-xs font-medium text-[#6B5E52]">Nama peminjam</label>
                        <input
                            type="text"
                            id="namaPeminjamKeyword"
                            autocomplete="off"
                            placeholder="Ketik nama peminjam..."
                            value="{{ $namaPeminjam }}"
                            class="w-full rounded-lg border border-[#B9AE9E] bg-[#FFFFFF] px-3 py-2.5 text-sm text-[#2D251E] outline-none transition placeholder:text-[#97897B] focus:border-sepia-500 focus:ring-2 focus:ring-sepia-600/20">
                        <input type="hidden" name="nama_peminjam" id="namaPeminjamHidden" value="{{ $namaPeminjam }}">

                        <div id="namaPeminjamDropdown" class="absolute z-30 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-lg border border-[#E8E1D7] bg-[#FFFFFF] shadow-xl">
                            <button type="button" data-nama="" class="peminjam-opsi block w-full border-b border-[#E8E1D7] px-3 py-2.5 text-left text-sm text-[#8B7E71] transition hover:bg-[#F7F4EF]">Semua peminjam</button>
                            @foreach ($daftarPeminjam as $p)
                                <button type="button" data-nama="{{ $p->nama }}" class="peminjam-opsi block w-full border-b border-[#E8E1D7] px-3 py-2.5 text-left transition last:border-b-0 hover:bg-[#F7F4EF]">
                                    <span class="block text-sm font-medium text-[#2D251E]">{{ $p->nama }}</span>
                                    <span class="mt-0.5 block text-xs text-[#8B7E71]">{{ $p->nomor_anggota }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Pencarian buku --}}
                    <div class="relative">
                        <label for="namaBukuKeyword" class="mb-1.5 block text-xs font-medium text-[#6B5E52]">Judul buku</label>
                        <input
                            type="text"
                            id="namaBukuKeyword"
                            autocomplete="off"
                            placeholder="Ketik judul buku..."
                            value="{{ $namaBuku }}"
                            class="w-full rounded-lg border border-[#B9AE9E] bg-[#FFFFFF] px-3 py-2.5 text-sm text-[#2D251E] outline-none transition placeholder:text-[#97897B] focus:border-sepia-500 focus:ring-2 focus:ring-sepia-600/20">
                        <input type="hidden" name="nama_buku" id="namaBukuHidden" value="{{ $namaBuku }}">

                        <div id="namaBukuDropdown" class="absolute z-30 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-lg border border-[#E8E1D7] bg-[#FFFFFF] shadow-xl">
                            <button type="button" data-judul="" class="buku-opsi block w-full border-b border-[#E8E1D7] px-3 py-2.5 text-left text-sm text-[#8B7E71] transition hover:bg-[#F7F4EF]">Semua buku</button>
                            @foreach ($daftarBuku as $b)
                                <button type="button" data-judul="{{ $b->judul }}" class="buku-opsi block w-full border-b border-[#E8E1D7] px-3 py-2.5 text-left transition last:border-b-0 hover:bg-[#F7F4EF]">
                                    <span class="block text-sm font-medium text-[#2D251E]">{{ $b->judul }}</span>
                                    <span class="mt-0.5 block text-xs text-[#8B7E71]">Stok: {{ $b->stok }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tombol filter --}}
                    <div class="flex items-end gap-2">
                        <button type="submit" class="inline-flex min-h-[42px] flex-1 items-center justify-center gap-2 rounded-lg bg-sepia-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sepia-700 focus:outline-none focus:ring-2 focus:ring-sepia-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M19 10.5a8.5 8.5 0 11-17 0 8.5 8.5 0 0117 0z"/>
                            </svg>
                            Filter
                        </button>
                        @if ($namaPeminjam !== '' || $namaBuku !== '')
                            <a href="{{ $urlPengajuan }}" class="inline-flex min-h-[42px] items-center justify-center rounded-lg border border-[#B9AE9E] bg-[#FFFFFF] px-4 py-2.5 text-sm font-semibold text-[#6B5E52] transition hover:bg-[#F0EAE1] hover:text-[#2D251E]">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Judul tabel --}}
            <div class="flex flex-col gap-1 border-b border-[#E8E1D7] px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div>
                    <h2 class="text-sm font-semibold text-[#2D251E]">Daftar Pengajuan Pengembalian</h2>
                    <p class="mt-1 text-xs text-[#8B7E71]">Periksa informasi pinjaman sebelum membuka proses verifikasi.</p>
                </div>
                <span class="text-xs text-[#8B7E71]">
                    @if ($requests->total() > 0)
                        Menampilkan {{ $requests->firstItem() }}&ndash;{{ $requests->lastItem() }} dari {{ $requests->total() }} transaksi
                    @else
                        Belum ada transaksi
                    @endif
                </span>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#E8E1D7] bg-[#F7F4EF] text-xs uppercase tracking-wider text-[#8B7E71]">
                            <th class="w-14 px-4 py-4 text-center font-semibold">No</th>
                            <th class="min-w-[190px] px-4 py-4 text-left font-semibold">Peminjam</th>
                            <th class="min-w-[240px] px-4 py-4 text-left font-semibold">Buku yang dipinjam</th>
                            <th class="min-w-[180px] px-4 py-4 text-left font-semibold">Tanggal</th>
                            <th class="min-w-[155px] px-4 py-4 text-center font-semibold">Status</th>
                            <th class="min-w-[130px] px-4 py-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E1D7]/70">
                        @forelse ($requests as $i => $g)
                            <tr class="transition-colors hover:bg-[#F7F4EF]">
                                <td class="px-4 py-4 text-center align-top text-xs font-medium text-[#97897B]">
                                    {{ $requests->firstItem() + $i }}
                                </td>

                                {{-- Data peminjam --}}
                                <td class="px-4 py-4 align-top">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-sepia-400/30 bg-sepia-500/10 text-xs font-bold text-sepia-700">
                                            {{ strtoupper(substr($g->anggota->nama ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold leading-5 text-[#2D251E]">{{ $g->anggota->nama }}</p>
                                            <p class="mt-1 text-xs text-[#8B7E71]">{{ $g->anggota->nomor_anggota }}</p>
                                            @if ($g->anggota->kelas)
                                                <p class="mt-1 text-xs text-[#97897B]">Kelas {{ $g->anggota->kelas->nama }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Daftar buku --}}
                                <td class="px-4 py-4 align-top">
                                    <div class="space-y-3">
                                        @foreach ($g->items as $item)
                                            <div class="flex items-start gap-2">
                                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-sepia-500"></span>
                                                <div class="min-w-0">
                                                    <p class="font-medium leading-5 text-[#3D342C]">{{ $item->buku->judul }}</p>
                                                    <p class="mt-1 text-xs text-[#8B7E71]">Rencana kembali: {{ $item->tanggalRencanaLabel() }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="inline-flex items-center rounded-md border border-[#DDD5C9] bg-[#F7F4EF] px-2 py-1 text-xs font-medium text-[#6B5E52]">
                                            {{ $g->jumlah_buku }} buku
                                        </div>
                                    </div>
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-4 py-4 align-top">
                                    <div class="space-y-2 text-xs">
                                        <div>
                                            <p class="mb-1 text-[#97897B]">Tanggal pinjam</p>
                                            <p class="font-medium text-[#3D342C]">{{ $g->tanggal_pinjam->format('d/m/Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="mb-1 text-[#97897B]">Rencana kembali</p>
                                            <p class="font-medium text-[#3D342C]">{{ $g->tanggal_kembali_rencana?->format('d/m/Y') ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4 text-center align-top">
                                    @if ($g->terlambat > 0)
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-red-400/30 bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Telat {{ $g->terlambat }} hari
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-400/30 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu verifikasi
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi: hanya Admin dan Pustakawan --}}
                                <td class="px-4 py-4 text-center align-top">
                                    @if ($bisaKelola)
                                        <a href="{{ route('admin.pengembalian.edit', $g->key) }}"
                                           class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-sepia-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-sepia-700 focus:outline-none focus:ring-2 focus:ring-sepia-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Verifikasi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-14 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl border border-[#E8E1D7] bg-[#F7F4EF] text-[#8B7E71]">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                        </svg>
                                    </div>
                                    <p class="mt-3 font-semibold text-[#3D342C]">Tidak ada pengajuan pengembalian</p>
                                    <p class="mt-1 text-sm text-[#8B7E71]">Transaksi yang cocok dengan pencarian akan muncul di sini.</p>
                                    @if ($namaPeminjam !== '' || $namaBuku !== '')
                                        <a href="{{ $urlPengajuan }}" class="mt-3 inline-block text-sm font-semibold text-sepia-700 hover:text-sepia-800">Hapus filter</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($requests->hasPages())
                <div class="border-t border-[#E8E1D7] bg-[#F7F4EF] px-4 py-4 sm:px-5">
                    {{ $requests->withQueryString()->links() }}
                </div>
            @endif
        </div>

        <script>
            function combo(keywordId, hiddenId, dropdownId, opsiClass, attr) {
                const kw = document.getElementById(keywordId);
                const hd = document.getElementById(hiddenId);
                const dd = document.getElementById(dropdownId);

                if (!kw || !hd || !dd) return;

                const opts = Array.from(dd.querySelectorAll('.' + opsiClass));

                function filter() {
                    const q = kw.value.toLowerCase().trim();

                    opts.forEach(o => {
                        const value = (o.getAttribute(attr) || '').toLowerCase();
                        o.classList.toggle('hidden', !q || !value.includes(q));
                    });

                    dd.classList.remove('hidden');
                }

                kw.addEventListener('input', () => { hd.value = kw.value; filter(); });
                kw.addEventListener('focus', filter);

                opts.forEach(o => o.addEventListener('click', () => {
                    hd.value = o.getAttribute(attr) || '';
                    kw.value = o.getAttribute(attr) || '';
                    dd.classList.add('hidden');
                }));

                document.addEventListener('click', e => {
                    if (!kw.parentElement.contains(e.target)) dd.classList.add('hidden');
                });
            }

            combo('namaPeminjamKeyword', 'namaPeminjamHidden', 'namaPeminjamDropdown', 'peminjam-opsi', 'data-nama');
            combo('namaBukuKeyword', 'namaBukuHidden', 'namaBukuDropdown', 'buku-opsi', 'data-judul');
        </script>

    @else

        {{-- ================= TAB RIWAYAT ================= --}}
        {{-- Filter dan pencarian --}}
        <div class="overflow-hidden rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] shadow-sm">
            <div class="border-b border-[#E8E1D7] bg-[#F7F4EF] px-4 py-4 sm:px-5">
                <h2 class="font-semibold text-[#2D251E]">Pencarian dan Filter</h2>
                <p class="mt-1 text-xs text-[#8B7E71]">Cari berdasarkan nama peminjam, judul buku, atau kondisi buku.</p>
            </div>

            <form method="GET"
                  action="{{ $urlRiwayat }}"
                  class="grid grid-cols-1 gap-3 p-4 md:grid-cols-12 sm:p-5">

                <div class="md:col-span-4">
                    <label for="nama-peminjam" class="mb-1.5 block text-xs font-semibold text-[#6B5E52]">
                        Nama Peminjam
                    </label>
                    <input id="nama-peminjam" type="text" name="nama_peminjam"
                           value="{{ $namaPeminjam }}"
                           placeholder="Cari nama peminjam..."
                           class="w-full rounded-xl border border-[#B9AE9E] bg-[#FFFFFF] px-3.5 py-2.5 text-sm text-[#2D251E] placeholder:text-[#97897B] focus:border-sepia-500 focus:outline-none focus:ring-2 focus:ring-sepia-600/20">
                </div>

                <div class="md:col-span-4">
                    <label for="nama-buku" class="mb-1.5 block text-xs font-semibold text-[#6B5E52]">
                        Judul Buku
                    </label>
                    <input id="nama-buku" type="text" name="nama_buku"
                           value="{{ $namaBuku }}"
                           placeholder="Cari judul buku..."
                           class="w-full rounded-xl border border-[#B9AE9E] bg-[#FFFFFF] px-3.5 py-2.5 text-sm text-[#2D251E] placeholder:text-[#97897B] focus:border-sepia-500 focus:outline-none focus:ring-2 focus:ring-sepia-600/20">
                </div>

                <div class="md:col-span-2">
                    <label for="kondisi-buku" class="mb-1.5 block text-xs font-semibold text-[#6B5E52]">
                        Kondisi
                    </label>
                    <select id="kondisi-buku" name="kondisi"
                            class="w-full rounded-xl border border-[#B9AE9E] bg-[#FFFFFF] px-3.5 py-2.5 text-sm text-[#2D251E] focus:border-sepia-500 focus:outline-none focus:ring-2 focus:ring-sepia-600/20">
                        <option value="">Semua kondisi</option>
                        <option value="baik" {{ $kondisi === 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ $kondisi === 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="hilang" {{ $kondisi === 'hilang' ? 'selected' : '' }}>Hilang</option>
                    </select>
                </div>

                <div class="flex items-end gap-2 md:col-span-2">
                    <button type="submit"
                            class="flex-1 rounded-xl bg-sepia-600 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-sepia-700">
                        Cari Data
                    </button>

                    <a href="{{ $urlRiwayat }}"
                       class="inline-flex items-center justify-center rounded-xl border border-[#B9AE9E] bg-[#FFFFFF] px-3 py-2.5 text-sm font-semibold text-[#6B5E52] transition hover:bg-[#F0EAE1] hover:text-[#2D251E]">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabel ringkasan --}}
        <div class="overflow-hidden rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] shadow-sm">

            <div class="flex flex-col gap-3 border-b border-[#E8E1D7] bg-[#F7F4EF] p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div>
                    <h2 class="font-bold text-[#2D251E]">Data Pengembalian</h2>
                    <p class="mt-1 text-xs text-[#8B7E71]">
                        Klik Detail untuk melihat buku dan informasi dendanya.
                    </p>
                </div>

                <span class="inline-flex w-fit items-center rounded-full border border-sepia-400/30 bg-sepia-500/10 px-3 py-1.5 text-xs font-semibold text-sepia-700">
                    {{ $pengembalians->total() }} transaksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-[#E8E1D7] bg-[#F7F4EF] text-xs uppercase tracking-wider text-[#8B7E71]">
                            <th class="w-16 px-5 py-4 text-center font-semibold">No.</th>
                            <th class="px-5 py-4 font-semibold">Kode Peminjaman</th>
                            <th class="px-5 py-4 font-semibold">Peminjam</th>
                            <th class="px-5 py-4 text-center font-semibold">Jumlah Buku</th>
                            <th class="px-5 py-4 text-center font-semibold">Tanggal Kembali</th>
                            <th class="px-5 py-4 text-center font-semibold">Detail</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#E8E1D7]/70">
                        @forelse ($pengembalians as $i => $g)
                            <tr class="transition-colors hover:bg-[#F7F4EF]">

                                <td class="px-5 py-4 text-center text-[#8B7E71]">
                                    {{ $pengembalians->firstItem() + $i }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex whitespace-nowrap rounded-lg border border-sepia-400/30 bg-sepia-500/10 px-2.5 py-1 font-mono text-xs font-semibold text-sepia-700">
                                        {{ $g->kode ?: 'TRX-' . $g->items->first()->id }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-semibold leading-5 text-[#2D251E]">
                                        {{ $g->anggota->nama }}
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-[#8B7E71]">
                                        No. Anggota: {{ $g->anggota->nomor_anggota }}
                                    </p>
                                    @if ($g->anggota->kelas)
                                        <p class="mt-0.5 text-xs text-[#8B7E71]">
                                            Kelas: {{ $g->anggota->kelas->nama }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex min-w-10 items-center justify-center rounded-lg border border-sepia-400/30 bg-sepia-500/10 px-3 py-1.5 font-bold text-sepia-700">
                                        {{ $g->jumlah_buku }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center">
                                    <span class="font-medium text-[#3D342C]">
                                        {{ $g->tanggal_kembali_aktual?->format('d/m/Y') ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('admin.pengembalian.show', $g->key) }}"
                                       class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-sepia-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-sepia-700">
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
                                        <svg class="h-10 w-10 text-[#97897B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.5v11m-4-7h8M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                        </svg>
                                        <p class="font-semibold text-[#3D342C]">Data tidak ditemukan</p>
                                        <p class="text-sm text-[#8B7E71]">
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
                <div class="flex flex-col gap-3 border-t border-[#E8E1D7] bg-[#F7F4EF] px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-[#8B7E71]">
                        Menampilkan {{ $pengembalians->firstItem() }}&ndash;{{ $pengembalians->lastItem() }}
                        dari {{ $pengembalians->total() }} transaksi
                    </p>
                    <div class="text-sm">
                        {{ $pengembalians->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>

    @endif
</div>
@endsection
