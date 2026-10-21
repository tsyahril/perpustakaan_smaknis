
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
            <h1 class="text-2xl font-bold text-[#2D251E]">Detail Pengembalian</h1>
            <p class="text-sm text-[#8B7E71] mt-1">
                Kelola riwayat dan status setiap buku secara terpisah.
            </p>
        </div>

        <a href="{{ route('admin.pengembalian.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#F7F4EF] text-[#4A4038] text-sm font-semibold hover:bg-[#F0EAE1]">
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
        <div class="bg-white border border-[#E8E1D7] rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-[#97897B]">Peminjam</p>
            <p class="font-bold text-[#332B24] mt-2">{{ $anggota->nama }}</p>
            <p class="text-xs text-[#8B7E71] mt-1">{{ $anggota->nomor_anggota }}</p>
        </div>

        <div class="bg-white border border-[#E8E1D7] rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-[#97897B]">Kode Peminjaman</p>
            <p class="font-bold text-sepia-700 mt-2 font-mono">
                {{ $first->kode_peminjaman ?? 'TRX-' . $first->id }}
            </p>
        </div>

        <div class="bg-white border border-[#E8E1D7] rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-[#97897B]">Jumlah Buku</p>
            <p class="text-2xl font-bold text-[#332B24] mt-2">{{ $items->count() }}</p>
            <p class="text-xs text-[#8B7E71]">buku dalam riwayat ini</p>
        </div>

        <div class="bg-white border border-[#E8E1D7] rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-[#97897B]">Total Denda</p>
            <p class="text-xl font-bold mt-2 {{ $totalDenda > 0 ? 'text-red-600' : 'text-[#332B24]' }}">
                Rp{{ number_format($totalDenda, 0, ',', '.') }}
            </p>
            @if ($totalDenda > 0)
                <span class="inline-block mt-2 text-xs font-semibold px-2.5 py-1 rounded-full {{ $belumLunas->isNotEmpty() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ $belumLunas->isNotEmpty() ? 'Ada denda belum lunas' : 'Semua lunas' }}
                </span>
            @else
                <span class="inline-block mt-2 text-xs font-semibold px-2.5 py-1 rounded-full bg-[#F7F4EF] text-[#7C6F62]">
                    Tidak ada denda
                </span>
            @endif
        </div>
    </div>

    {{-- Daftar buku --}}
    <div class="bg-white border border-[#E8E1D7] rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-[#F0EAE1]">
            <h2 class="font-bold text-[#2D251E]">Daftar Buku</h2>
            <p class="text-sm text-[#8B7E71] mt-1">
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
                    <tr class="bg-[#FAF9F7] text-[#97897B] text-xs uppercase tracking-wide">
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

                <tbody class="divide-y divide-[#F0EAE1]">
                    @foreach ($items as $item)
                        <tr class="hover:bg-[#FAF9F7]">
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
                                <p class="font-semibold text-[#332B24]">
                                    {{ $item->buku->judul }}
                                </p>
                                <p class="text-xs text-[#97897B] mt-1">
                                    Penulis: {{ $item->buku->penulis ?? '-' }}
                                </p>
                                @if ($item->catatan)
                                    <p class="text-xs text-[#8B7E71] mt-1">
                                        Catatan: {{ $item->catatan }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-center text-xs text-[#7C6F62] align-top">
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
                                <p class="font-bold text-[#332B24]">
                                    Rp{{ number_format($item->denda, 0, ',', '.') }}
                                </p>

                                @if ($item->denda > 0)
                                    <span class="inline-flex mt-2 px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->isDendaLunas() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $item->isDendaLunas() ? 'Lunas' : 'Belum lunas' }}
                                    </span>
                                @else
                                    <span class="inline-flex mt-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F7F4EF] text-[#7C6F62]">
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
                                        <span class="text-xs text-[#8B7E71]">
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
            <div class="p-4 border-t border-[#F0EAE1] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-[#97897B]">
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

