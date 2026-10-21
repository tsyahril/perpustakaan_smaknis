@extends('layouts.admin')

@section('title', $tab === 'pengajuan' ? 'Pengajuan Peminjaman' : 'Data Peminjaman')

@section('content')
@php
    $bisaKelola = auth()->user()?->canManageLibrary();
    $urlPengajuan = route('admin.peminjaman.index', ['tab' => 'pengajuan']);
    $urlData = route('admin.peminjaman.index');
@endphp

<div class="space-y-5">

    {{-- Judul halaman --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#2D251E]">Peminjaman</h1>
            <p class="mt-1 text-sm text-[#8B7E71]">
                Pengajuan dan data peminjaman buku tersatukan dalam satu halaman.
            </p>
        </div>

        @if ($tab === 'data' && $bisaKelola)
            <a href="{{ route('admin.peminjaman.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-sepia-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sepia-950/20 transition hover:bg-sepia-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Peminjaman
            </a>
        @endif
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tab: Pengajuan | Data Peminjaman --}}
    <div class="flex flex-wrap items-end gap-6 border-b border-[#E8E1D7]">
        <a href="{{ $urlPengajuan }}"
           class="inline-flex items-center gap-2 -mb-px border-b-2 pb-3 text-sm transition
                  {{ $tab === 'pengajuan'
                      ? 'border-sepia-600 font-semibold text-sepia-700'
                      : 'border-transparent text-[#8B7E71] hover:border-[#DDD5C9] hover:text-[#2D251E]' }}">
            Pengajuan
            <span class="inline-flex min-w-6 items-center justify-center rounded-full px-2 py-0.5 text-[11px] font-bold
                         {{ $tab === 'pengajuan' ? 'bg-sepia-600 text-white' : 'bg-[#F0EAE1] text-[#6B5E52]' }}">
                {{ $counts['diajukan'] ?? 0 }}
            </span>
        </a>

        <a href="{{ $urlData }}"
           class="inline-flex items-center gap-2 -mb-px border-b-2 pb-3 text-sm transition
                  {{ $tab === 'data'
                      ? 'border-sepia-600 font-semibold text-sepia-700'
                      : 'border-transparent text-[#8B7E71] hover:border-[#DDD5C9] hover:text-[#2D251E]' }}">
            Data Peminjaman
        </a>
    </div>

    @if ($tab === 'pengajuan')

        {{-- ================= TAB PENGAJUAN ================= --}}
        <p class="text-sm leading-6 text-[#8B7E71]">
            Pengajuan pinjam yang menunggu persetujuan. Pilih nama peminjam dulu, lalu daftar pengajuannya.
            Yang sudah disetujui otomatis pindah ke tab <a href="{{ $urlData }}" class="font-semibold text-sepia-700 hover:underline">Data Peminjaman</a>.
        </p>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            @foreach (['diajukan' => ['Menunggu', 'bg-yellow-50 border-yellow-200 text-yellow-800'], 'disetujui' => ['Disetujui', 'bg-green-50 border-green-200 text-green-800'], 'ditolak' => ['Ditolak', 'bg-red-50 border-red-200 text-red-800']] as $key => [$label, $cls])
                <a href="{{ route('admin.peminjaman.index', ['tab' => 'pengajuan', 'status' => $key, 'nama_peminjam' => $namaPeminjam]) }}"
                    class="rounded-xl border p-4 transition hover:border-sepia-400 {{ $status === $key ? 'border-sepia-600 bg-[#FFFFFF]' : 'border-[#E8E1D7] bg-[#FFFFFF]' }}">
                    <p class="text-xs text-[#8B7E71]">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-bold text-[#2D251E]">{{ $counts[$key] ?? 0 }}</p>
                </a>
            @endforeach
        </div>

        <div class="overflow-hidden rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] shadow-sm">
            <form method="GET" action="{{ $urlPengajuan }}" class="grid gap-3 border-b border-[#E8E1D7]/80 p-4 md:grid-cols-4">
                <input type="hidden" name="tab" value="pengajuan">

                <div>
                    <label class="mb-1 block text-xs font-medium text-[#6B5E52]">Nama Peminjam</label>
                    <select name="nama_peminjam" class="w-full rounded-lg border border-[#B9AE9E] bg-[#FFFFFF] px-3 py-2 text-sm text-[#2D251E] focus:outline-none focus:ring-2 focus:ring-sepia-600/30">
                        <option value="">-- Semua peminjam --</option>
                        @foreach ($daftarPeminjam as $nama)
                            <option value="{{ $nama }}" {{ $namaPeminjam === $nama ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-[#6B5E52]">Status Pengajuan</label>
                    <select name="status" class="w-full rounded-lg border border-[#B9AE9E] bg-[#FFFFFF] px-3 py-2 text-sm text-[#2D251E] focus:outline-none focus:ring-2 focus:ring-sepia-600/30">
                        <option value="diajukan" {{ $status === 'diajukan' ? 'selected' : '' }}>Menunggu persetujuan</option>
                        <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="semua" {{ $status === 'semua' ? 'selected' : '' }}>Semua pengajuan</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 rounded-lg bg-sepia-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sepia-700">Tampilkan</button>
                </div>

                <div class="flex items-end gap-2">
                    @if ($namaPeminjam !== '' || ($status ?? '') !== 'diajukan')
                        <a href="{{ $urlPengajuan }}" class="rounded-lg border border-[#B9AE9E] bg-[#FFFFFF] px-4 py-2 text-sm font-semibold text-[#6B5E52] transition hover:bg-[#F0EAE1] hover:text-[#2D251E]">Reset</a>
                    @endif
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#E8E1D7]/80 bg-[#F7F4EF] text-xs text-[#8B7E71]">
                            <th class="w-10 px-4 py-3 text-left font-semibold">No</th>
                            <th class="px-4 py-3 text-left font-semibold">Peminjam</th>
                            <th class="px-4 py-3 text-left font-semibold">Buku</th>
                            <th class="px-4 py-3 text-center font-semibold">Pinjam / Rencana</th>
                            <th class="px-4 py-3 text-center font-semibold">Status</th>
                            <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E1D7]/70">
                        @forelse ($requests as $i => $g)
                            <tr class="transition-colors hover:bg-[#F7F4EF]">
                                <td class="align-top px-4 py-3 text-[#8B7E71]">{{ $requests->firstItem() + $i }}</td>

                                <td class="align-top px-4 py-3">
                                    <p class="font-medium text-[#2D251E]">{{ $g->anggota->nama }}</p>
                                    <p class="text-xs text-[#8B7E71]">{{ $g->anggota->nomor_anggota }}{{ $g->anggota->kelas ? ' &middot; ' . $g->anggota->kelas->nama : '' }}</p>
                                    @if ($g->kode)
                                        <p class="font-mono text-xs text-sepia-700">{{ $g->kode }}</p>
                                    @endif
                                </td>

                                <td class="align-top px-4 py-3">
                                    @foreach ($g->items as $item)
                                        <p class="text-sm text-[#2D251E]">&bull; {{ $item->buku->judul }}</p>
                                        <p class="text-xs text-[#8B7E71]">
                                            Rencana: {{ $item->tanggalRencanaLabel() }}
                                            @if (! $item->tanggal_kembali_rencana)
                                                <span class="text-[#97897B]">(tanpa rencana)</span>
                                            @endif
                                        </p>
                                    @endforeach
                                </td>

                                <td class="align-top px-4 py-3 text-center text-xs text-[#6B5E52]">
                                    {{ $g->tanggal_pinjam->format('d/m/Y') }}
                                    <span class="block text-[#8B7E71]">{{ $g->jumlah_buku }} buku</span>
                                </td>

                                <td class="align-top px-4 py-3 text-center">
                                    @if ($g->status_persetujuan === 'diajukan')
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">Diajukan</span>
                                    @elseif ($g->status_persetujuan === 'disetujui')
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Disetujui</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Ditolak</span>
                                    @endif
                                    @if ($g->catatan)
                                        <span class="mt-1 block text-xs text-[#8B7E71]">{{ $g->catatan }}</span>
                                    @endif
                                </td>

                                {{-- Aksi hanya untuk Admin dan Pustakawan --}}
                                <td class="align-top px-4 py-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-2">
                                        @if ($bisaKelola && $g->status_persetujuan === 'diajukan')
                                            <form action="{{ route('admin.request-peminjaman.approve', $g->key) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-green-700 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-green-800">Setujui</button>
                                            </form>
                                            <form action="{{ route('admin.request-peminjaman.reject', $g->key) }}" method="POST" class="inline" onsubmit="return confirm('Tolak pengajuan ini? Semua buku akan ditolak dan stok dikembalikan.')">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-red-600 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-red-700">Tolak</button>
                                            </form>
                                        @elseif ($g->status_persetujuan === 'diajukan')
                                            <span class="text-xs text-[#97897B]">Menunggu</span>
                                        @else
                                            <span class="text-xs text-[#97897B]">Selesai</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-[#8B7E71]">
                                    Tidak ada pengajuan{{ $namaPeminjam !== '' ? ' dari peminjam "' . $namaPeminjam . '"' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($requests->hasPages())
                <div class="border-t border-[#E8E1D7]/80 p-4">{{ $requests->links() }}</div>
            @endif
        </div>

    @else

        {{-- ================= TAB DATA PEMINJAMAN ================= --}}
        <div class="overflow-hidden rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] shadow-sm">

            {{-- Pencarian dan filter --}}
            <div class="border-b border-[#E8E1D7] bg-[#F7F4EF] p-4 sm:p-5">
                <form method="GET" action="{{ $urlData }}"
                      class="grid grid-cols-1 gap-3 md:grid-cols-12">

                    <div class="md:col-span-6">
                        <label for="search-peminjaman" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-[#6B5E52]">
                            Cari Peminjaman
                        </label>
                        <input id="search-peminjaman" type="text" name="search"
                               value="{{ $search }}"
                               placeholder="Nama anggota atau judul buku..."
                               class="w-full rounded-xl border border-[#B9AE9E] bg-[#FFFFFF] px-3.5 py-2.5 text-sm text-[#2D251E] placeholder:text-[#97897B] focus:border-sepia-500 focus:outline-none focus:ring-2 focus:ring-sepia-600/20">
                    </div>

                    <div class="md:col-span-3">
                        <label for="status-peminjaman" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-[#6B5E52]">
                            Status
                        </label>
                        <select id="status-peminjaman" name="status"
                                class="w-full rounded-xl border border-[#B9AE9E] bg-[#FFFFFF] px-3.5 py-2.5 text-sm text-[#2D251E] focus:border-sepia-500 focus:outline-none focus:ring-2 focus:ring-sepia-600/20">
                            <option value="">Semua Status</option>
                            <option value="pinjam" {{ $status === 'pinjam' ? 'selected' : '' }}>Masih Dipinjam</option>
                            <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2 md:col-span-3">
                        <button type="submit"
                                class="flex-1 rounded-xl bg-sepia-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sepia-700">
                            Filter
                        </button>

                        @if ($search || $status !== '')
                            <a href="{{ $urlData }}"
                               class="inline-flex items-center justify-center rounded-xl border border-[#B9AE9E] bg-[#FFFFFF] px-4 py-2.5 text-sm font-semibold text-[#6B5E52] transition hover:bg-[#F0EAE1] hover:text-[#2D251E]">
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
                        <tr class="border-b border-[#E8E1D7] bg-[#F7F4EF] text-xs uppercase tracking-wider text-[#8B7E71]">
                            <th class="w-14 px-4 py-4 text-center font-semibold">No.</th>
                            <th class="px-4 py-4 font-semibold">Kode / Anggota</th>
                            <th class="px-4 py-4 font-semibold">Buku / Stok</th>
                            <th class="px-4 py-4 text-center font-semibold">Tanggal Pinjam</th>
                            <th class="px-4 py-4 text-center font-semibold">Status</th>
                            <th class="w-36 px-4 py-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#E8E1D7]/70">
                        @forelse ($peminjamans as $i => $peminjaman)
                            <tr class="transition-colors hover:bg-[#F7F4EF]">

                                <td class="px-4 py-4 text-center text-[#8B7E71]">
                                    {{ $peminjamans->firstItem() + $i }}
                                </td>

                                <td class="px-4 py-4">
                                    @if ($peminjaman->kode_peminjaman)
                                        <p class="mb-1 font-mono text-xs font-semibold text-sepia-700">
                                            {{ $peminjaman->kode_peminjaman }}
                                        </p>
                                    @endif

                                    <p class="font-semibold leading-5 text-[#2D251E]">
                                        {{ $peminjaman->anggota->nama }}
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-[#8B7E71]">
                                        {{ $peminjaman->anggota->nomor_anggota }}
                                        &middot;
                                        {{ ucfirst($peminjaman->anggota->jenis_anggota) }}
                                        @if ($peminjaman->anggota->kelas)
                                            &middot; {{ $peminjaman->anggota->kelas->nama }}
                                        @endif
                                    </p>
                                </td>

                                <td class="px-4 py-4">
                                    <p class="max-w-xs font-medium leading-5 text-[#2D251E]">
                                        {{ $peminjaman->buku->judul }}
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-[#8B7E71]">
                                        {{ $peminjaman->buku->penulis }}
                                    </p>
                                    <p class="mt-1 text-xs text-[#8B7E71]">
                                        Sisa stok:
                                        <span class="font-bold {{ $peminjaman->buku->stok > 0 ? 'text-green-700' : 'text-red-700' }}">
                                            {{ $peminjaman->buku->stok }}
                                        </span>
                                    </p>
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <p class="whitespace-nowrap font-medium text-[#3D342C]">
                                        {{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}
                                    </p>
                                    <p class="my-1 text-xs text-[#97897B]">&darr; batas pengembalian</p>
                                    <p class="whitespace-nowrap text-xs font-semibold text-sepia-700">
                                        {{ $peminjaman->tanggalRencanaLabel() }}
                                    </p>

                                    @if ($peminjaman->tanggal_kembali_rencana)
                                        <p class="mt-1 text-xs text-[#8B7E71]">
                                            {{ $peminjaman->tanggal_kembali_rencana->diffInDays($peminjaman->tanggal_pinjam) }} hari
                                        </p>
                                    @else
                                        <p class="mt-1 text-xs text-amber-700">Tanpa rencana</p>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-center">
                                    @if ($peminjaman->status === 'pinjam')
                                        @if ($peminjaman->hitungHariTerlambat() > 0)
                                            <span class="inline-flex max-w-full items-center justify-center whitespace-nowrap rounded-full border border-red-400/40 bg-red-500/15 px-3 py-1.5 text-xs font-bold text-red-700">
                                                Terlambat {{ $peminjaman->hitungHariTerlambat() }} hari
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center whitespace-nowrap rounded-full border border-sepia-400/40 bg-sepia-500/15 px-3 py-1.5 text-xs font-bold text-sepia-700">
                                                Dipinjam
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex max-w-full items-center justify-center whitespace-nowrap rounded-full border border-emerald-400/40 bg-emerald-500/15 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                            Dikembalikan
                                        </span>
                                        @if ($peminjaman->kondisi)
                                            <p class="mt-1.5 text-xs text-[#8B7E71]">
                                                Kondisi: {{ ucfirst($peminjaman->kondisi) }}
                                            </p>
                                        @endif
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        @if ($bisaKelola && $peminjaman->status === 'pinjam')
                                            <a href="{{ route('admin.peminjaman.edit', $peminjaman) }}"
                                               class="inline-flex items-center rounded-lg border border-sepia-400/40 bg-sepia-500/10 px-3 py-1.5 text-xs font-semibold text-sepia-700 transition hover:bg-sepia-500/20">
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.peminjaman.destroy', $peminjaman) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Hapus peminjaman ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center rounded-lg border border-red-400/30 bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-500/20">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center rounded-lg border border-[#DDD5C9] bg-[#F7F4EF] px-3 py-1.5 text-xs font-medium text-[#6B5E52]">
                                                {{ $peminjaman->status === 'pinjam' ? 'Aktif' : 'Selesai' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-14 text-center">
                                    <p class="font-semibold text-[#3D342C]">Belum ada data peminjaman</p>
                                    <p class="mt-1 text-sm text-[#8B7E71]">
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
                <div class="flex flex-col gap-3 border-t border-[#E8E1D7] bg-[#F7F4EF] px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-[#8B7E71]">
                        Menampilkan {{ $peminjamans->firstItem() }}&ndash;{{ $peminjamans->lastItem() }}
                        dari {{ $peminjamans->total() }} data
                    </p>
                    <div class="text-sm">
                        {{ $peminjamans->links() }}
                    </div>
                </div>
            @endif
        </div>

    @endif
</div>
@endsection
