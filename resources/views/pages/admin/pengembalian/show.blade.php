
@extends('layouts.admin')

@section('title', 'Detail Pengembalian')

@section('content')
@php
    $bisaKelola = auth()->user()?->canManageLibrary();
    $first = $items->first();
    $anggota = $first->anggota;
    $totalDenda = (int) $items->sum('denda');
    $belumLunas = $items->filter(fn ($item) => $item->denda > 0 && ! $item->isDendaLunas());
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">Detail Pengembalian</h1>
            <p class="text-sm text-slate-400 mt-1">
                Kelola riwayat dan status setiap buku secara terpisah.
            </p>
        </div>

        <a href="{{ route('admin.pengembalian.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200">
            â† Kembali ke Pengembalian
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Ringkasan peminjaman --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">Peminjam</p>
            <p class="font-bold text-gray-800 mt-2">{{ $anggota->nama }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $anggota->nomor_anggota }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">Kode Peminjaman</p>
            <p class="font-bold text-blue-700 mt-2 font-mono">
                {{ $first->kode_peminjaman ?? 'TRX-' . $first->id }}
            </p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">Jumlah Buku</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $items->count() }}</p>
            <p class="text-xs text-gray-400">buku dalam riwayat ini</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">Total Denda</p>
            <p class="text-xl font-bold mt-2 {{ $totalDenda > 0 ? 'text-red-600' : 'text-gray-800' }}">
                Rp{{ number_format($totalDenda, 0, ',', '.') }}
            </p>
            @if ($totalDenda > 0)
                <span class="inline-block mt-2 text-xs font-semibold px-2.5 py-1 rounded-full {{ $belumLunas->isNotEmpty() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ $belumLunas->isNotEmpty() ? 'Ada denda belum lunas' : 'Semua lunas' }}
                </span>
            @else
                <span class="inline-block mt-2 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">
                    Tidak ada denda
                </span>
            @endif
        </div>
    </div>

    {{-- Daftar buku --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h2 class="font-bold text-slate-100">Daftar Buku</h2>
            <p class="text-sm text-slate-400 mt-1">
                Periksa kondisi dan denda masing-masing buku.
            </p>
        </div>

        @if ($bisaKelola)
            <form id="hapusMassal"
                  method="POST"
                  action="{{ route('admin.pengembalian.destroy-selected') }}"
                  data-confirm="Hapus buku terpilih yang dendanya sudah lunas atau tidak memiliki denda?">
                @csrf
            </form>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                        @if ($bisaKelola)
                            <th class="px-4 py-4 text-center">
                                <input type="checkbox" id="pilihSemuaDetail">
                            </th>
                        @endif
                        <th class="px-4 py-4 text-left font-semibold">Buku</th>
                        <th class="px-4 py-4 text-center font-semibold">Tanggal Kembali</th>
                        <th class="px-4 py-4 text-center font-semibold">Kondisi</th>
                        <th class="px-4 py-4 text-right font-semibold">Denda</th>
                        @if ($bisaKelola)
                            <th class="px-4 py-4 text-center font-semibold">Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($items as $item)
                        <tr class="hover:bg-gray-50">
                            @if ($bisaKelola)
                                <td class="px-4 py-4 text-center align-top">
                                    <input type="checkbox"
                                           form="hapusMassal"
                                           name="transaksi_ids[]"
                                           value="{{ $item->id }}"
                                           class="pilih-buku-detail"
                                           {{ ! $item->isDendaLunas() ? 'disabled' : '' }}>
                                </td>
                            @endif

                            <td class="px-4 py-4 align-top">
                                <p class="font-semibold text-gray-800">
                                    {{ $item->buku->judul }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Penulis: {{ $item->buku->penulis ?? '-' }}
                                </p>
                                @if ($item->catatan)
                                    <p class="text-xs text-gray-400 mt-1">
                                        Catatan: {{ $item->catatan }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-center text-xs text-gray-600 align-top">
                                {{ $item->tanggal_kembali_aktual?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td class="px-4 py-4 text-center align-top">
                                @if (($item->kondisi ?? 'baik') === 'baik')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Baik</span>
                                @elseif ($item->kondisi === 'rusak')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Rusak</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Hilang</span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-right align-top">
                                <p class="font-bold text-gray-800">
                                    Rp{{ number_format($item->denda, 0, ',', '.') }}
                                </p>

                                @if ($item->denda > 0)
                                    <span class="inline-flex mt-2 px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->isDendaLunas() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $item->isDendaLunas() ? 'Lunas' : 'Belum lunas' }}
                                    </span>
                                @else
                                    <span class="inline-flex mt-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        Tidak ada denda
                                    </span>
                                @endif
                            </td>

                            @if ($bisaKelola)
                                <td class="px-4 py-4 text-center align-top">
                                    @if ($item->isDendaLunas())
                                        <form action="{{ route('admin.pengembalian.destroy', $item->id) }}"
                                              method="POST"
                                              data-confirm="Yakin ingin menghapus riwayat buku ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex px-3 py-2 rounded-lg bg-red-50 text-red-600 text-xs font-semibold hover:bg-red-100">
                                                Hapus Buku
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">
                                            Denda belum lunas
                                        </span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($bisaKelola)
            <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-gray-500">
                    Buku dengan denda belum lunas tidak dapat dipilih untuk dihapus.
                </p>

                <button type="submit"
                        form="hapusMassal"
                        class="inline-flex justify-center px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700">
                    Hapus Buku Terpilih
                </button>
            </div>
        @endif
    </div>
</div>

<script>
    const pilihSemuaDetail = document.getElementById('pilihSemuaDetail');

    if (pilihSemuaDetail) {
        pilihSemuaDetail.addEventListener('change', function () {
            document.querySelectorAll('.pilih-buku-detail:not(:disabled)')
                .forEach(function (checkbox) {
                    checkbox.checked = pilihSemuaDetail.checked;
                });
        });
    }
</script>
@endsection

