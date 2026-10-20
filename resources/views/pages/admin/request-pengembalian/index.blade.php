@extends('layouts.admin')

@section('title', 'Request Pengembalian')

@section('content')
<div class="space-y-6">

    {{-- Header halaman --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-100">Request Pengembalian</h1>
            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-400">
                Daftar pinjaman yang menunggu verifikasi pengembalian. Setiap baris mewakili satu transaksi pinjaman.
                <a href="{{ route('admin.pengembalian.index') }}" class="font-semibold text-blue-400 hover:text-blue-300 hover:underline">Lihat riwayat pengembalian</a>.
            </p>
        </div>
        <div class="inline-flex w-fit items-center gap-2 rounded-lg border border-slate-700 bg-[#0b1d32] px-3 py-2 text-sm text-slate-300">
            <span class="h-2 w-2 rounded-full bg-blue-400"></span>
            {{ $requests->total() }} transaksi
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <span class="font-bold">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
            <span class="font-bold">!</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($telatCount > 0)
        <div class="flex items-start gap-3 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
            <span class="font-bold">!</span>
            <span><strong>{{ $telatCount }} pinjaman</strong> telah melewati tanggal rencana pengembalian. Segera lakukan verifikasi.</span>
        </div>
    @endif

    {{-- Panel utama --}}
    <div class="overflow-hidden rounded-2xl border border-slate-700 bg-[#0b1d32] shadow-xl shadow-black/10">

        {{-- Filter --}}
        <div class="border-b border-slate-700 bg-[#102640] p-4 sm:p-5">
            <div class="mb-3">
                <h2 class="text-sm font-semibold text-slate-100">Cari transaksi pengembalian</h2>
                <p class="mt-1 text-xs text-slate-400">Cari berdasarkan nama peminjam atau judul buku.</p>
            </div>

            <form method="GET" action="{{ route('admin.request-pengembalian.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                {{-- Pencarian peminjam --}}
                <div class="relative">
                    <label for="namaPeminjamKeyword" class="mb-1.5 block text-xs font-medium text-slate-300">Nama peminjam</label>
                    <input
                        type="text"
                        id="namaPeminjamKeyword"
                        autocomplete="off"
                        placeholder="Ketik nama peminjam..."
                        value="{{ $namaPeminjam }}"
                        class="w-full rounded-lg border border-slate-600 bg-[#071426] px-3 py-2.5 text-sm text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <input type="hidden" name="nama_peminjam" id="namaPeminjamHidden" value="{{ $namaPeminjam }}">

                    <div id="namaPeminjamDropdown" class="absolute z-30 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-lg border border-slate-600 bg-[#0b1d32] shadow-xl">
                        <button type="button" data-nama="" class="peminjam-opsi block w-full border-b border-slate-700/70 px-3 py-2.5 text-left text-sm text-slate-400 transition hover:bg-[#183653]">Semua peminjam</button>
                        @foreach ($daftarPeminjam as $p)
                            <button type="button" data-nama="{{ $p->nama }}" class="peminjam-opsi block w-full border-b border-slate-700/70 px-3 py-2.5 text-left transition last:border-b-0 hover:bg-[#183653]">
                                <span class="block text-sm font-medium text-slate-100">{{ $p->nama }}</span>
                                <span class="mt-0.5 block text-xs text-slate-400">{{ $p->nomor_anggota }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Pencarian buku --}}
                <div class="relative">
                    <label for="namaBukuKeyword" class="mb-1.5 block text-xs font-medium text-slate-300">Judul buku</label>
                    <input
                        type="text"
                        id="namaBukuKeyword"
                        autocomplete="off"
                        placeholder="Ketik judul buku..."
                        value="{{ $namaBuku }}"
                        class="w-full rounded-lg border border-slate-600 bg-[#071426] px-3 py-2.5 text-sm text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <input type="hidden" name="nama_buku" id="namaBukuHidden" value="{{ $namaBuku }}">

                    <div id="namaBukuDropdown" class="absolute z-30 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-lg border border-slate-600 bg-[#0b1d32] shadow-xl">
                        <button type="button" data-judul="" class="buku-opsi block w-full border-b border-slate-700/70 px-3 py-2.5 text-left text-sm text-slate-400 transition hover:bg-[#183653]">Semua buku</button>
                        @foreach ($daftarBuku as $b)
                            <button type="button" data-judul="{{ $b->judul }}" class="buku-opsi block w-full border-b border-slate-700/70 px-3 py-2.5 text-left transition last:border-b-0 hover:bg-[#183653]">
                                <span class="block text-sm font-medium text-slate-100">{{ $b->judul }}</span>
                                <span class="mt-0.5 block text-xs text-slate-400">Stok: {{ $b->stok }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tombol filter --}}
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex min-h-[42px] flex-1 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M19 10.5a8.5 8.5 0 11-17 0 8.5 8.5 0 0117 0z"/>
                        </svg>
                        Filter
                    </button>
                    @if ($namaPeminjam !== '' || $namaBuku !== '')
                        <a href="{{ route('admin.request-pengembalian.index') }}" class="inline-flex min-h-[42px] items-center justify-center rounded-lg border border-slate-600 bg-[#0b1d32] px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-slate-800 hover:text-white">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Judul tabel --}}
        <div class="flex flex-col gap-1 border-b border-slate-700 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div>
                <h2 class="text-sm font-semibold text-slate-100">Daftar Request Pengembalian</h2>
                <p class="mt-1 text-xs text-slate-400">Periksa informasi pinjaman sebelum membuka proses verifikasi.</p>
            </div>
            <span class="text-xs text-slate-400">
                @if ($requests->total() > 0)
                    Menampilkan {{ $requests->firstItem() }}–{{ $requests->lastItem() }} dari {{ $requests->total() }} transaksi
                @else
                    Belum ada transaksi
                @endif
            </span>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-700 bg-[#102640] text-xs uppercase tracking-wider text-slate-300">
                        <th class="w-14 px-4 py-4 text-center font-semibold">No</th>
                        <th class="min-w-[190px] px-4 py-4 text-left font-semibold">Peminjam</th>
                        <th class="min-w-[240px] px-4 py-4 text-left font-semibold">Buku yang dipinjam</th>
                        <th class="min-w-[180px] px-4 py-4 text-left font-semibold">Tanggal</th>
                        <th class="min-w-[155px] px-4 py-4 text-center font-semibold">Status</th>
                        <th class="min-w-[130px] px-4 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/70">
                    @forelse ($requests as $i => $g)
                        <tr class="transition-colors hover:bg-[#102640]/80">
                            <td class="px-4 py-4 text-center align-top text-xs font-medium text-slate-500">
                                {{ $requests->firstItem() + $i }}
                            </td>

                            {{-- Data peminjam --}}
                            <td class="px-4 py-4 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-blue-400/20 bg-blue-500/10 text-xs font-bold text-blue-300">
                                        {{ strtoupper(substr($g->anggota->nama ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold leading-5 text-slate-100">{{ $g->anggota->nama }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $g->anggota->nomor_anggota }}</p>
                                        @if ($g->anggota->kelas)
                                            <p class="mt-1 text-xs text-slate-500">Kelas {{ $g->anggota->kelas->nama }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Daftar buku --}}
                            <td class="px-4 py-4 align-top">
                                <div class="space-y-3">
                                    @foreach ($g->items as $item)
                                        <div class="flex items-start gap-2">
                                            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400"></span>
                                            <div class="min-w-0">
                                                <p class="font-medium leading-5 text-slate-200">{{ $item->buku->judul }}</p>
                                                <p class="mt-1 text-xs text-slate-400">Rencana kembali: {{ $item->tanggalRencanaLabel() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="inline-flex items-center rounded-md border border-slate-600 bg-[#071426] px-2 py-1 text-xs font-medium text-slate-300">
                                        {{ $g->jumlah_buku }} buku
                                    </div>
                                </div>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-4 align-top">
                                <div class="space-y-2 text-xs">
                                    <div>
                                        <p class="mb-1 text-slate-500">Tanggal pinjam</p>
                                        <p class="font-medium text-slate-200">{{ $g->tanggal_pinjam->format('d/m/Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="mb-1 text-slate-500">Rencana kembali</p>
                                        <p class="font-medium text-slate-200">{{ $g->tanggal_kembali_rencana?->format('d/m/Y') ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4 text-center align-top">
                                @if ($g->terlambat > 0)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-red-400/30 bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>
                                        Telat {{ $g->terlambat }} hari
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-400/30 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                        Menunggu verifikasi
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4 text-center align-top">
                                <a href="{{ route('admin.pengembalian.edit', $g->key) }}"
                                   class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Verifikasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-14 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl border border-slate-700 bg-[#102640] text-slate-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                    </svg>
                                </div>
                                <p class="mt-3 font-semibold text-slate-200">Tidak ada request pengembalian</p>
                                <p class="mt-1 text-sm text-slate-400">Transaksi yang cocok dengan pencarian akan muncul di sini.</p>
                                @if ($namaPeminjam !== '' || $namaBuku !== '')
                                    <a href="{{ route('admin.request-pengembalian.index') }}" class="mt-3 inline-block text-sm font-semibold text-blue-400 hover:text-blue-300">Hapus filter</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($requests->hasPages())
            <div class="border-t border-slate-700 bg-[#102640]/60 px-4 py-4 sm:px-5">
                {{ $requests->withQueryString()->links() }}
            </div>
        @endif
    </div>
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
                const cocok = !q || value.includes(q);
                o.classList.toggle('hidden', !cocok);
            });

            dd.classList.remove('hidden');
        }

        kw.addEventListener('input', () => {
            hd.value = kw.value;
            filter();
        });

        kw.addEventListener('focus', filter);

        opts.forEach(o => o.addEventListener('click', () => {
            const value = o.getAttribute(attr) || '';
            hd.value = value;
            kw.value = value;
            dd.classList.add('hidden');
        }));

        document.addEventListener('click', e => {
            if (!kw.parentElement.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });
    }

    combo('namaPeminjamKeyword', 'namaPeminjamHidden', 'namaPeminjamDropdown', 'peminjam-opsi', 'data-nama');
    combo('namaBukuKeyword', 'namaBukuHidden', 'namaBukuDropdown', 'buku-opsi', 'data-judul');
</script>
@endsection
