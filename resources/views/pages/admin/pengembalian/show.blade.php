@extends('layouts.admin')

@section('title', 'Detail Pengembalian')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Detail Pengembalian</h1>
            <p class="text-sm text-gray-500">Rincian buku, tanggal, kondisi, dan denda satu kali pinjam.</p>
        </div>
        <a href="{{ route('admin.pengembalian.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Kembali</a>
    </div>

    @php
        $first = $items->first();
        $anggota = $first->anggota;
        $totalDenda = (int) $items->sum('denda');
        $belumLunas = $items->filter(fn ($i) => $i->denda > 0 && ! $i->isDendaLunas());
    @endphp

    <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">Peminjam</p>
                <p class="font-medium text-gray-800">{{ $anggota->nama }}</p>
                <p class="text-xs text-gray-400">{{ $anggota->nomor_anggota }}{{ $anggota->kelas ? ' · ' . $anggota->kelas->nama : '' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Kode Peminjaman</p>
                <p class="font-medium text-gray-800">{{ $first->kode_peminjaman ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Jumlah Buku</p>
                <p class="font-medium text-gray-800">{{ $items->count() }} buku</p>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="text-sm text-gray-600">
                Total denda:
                <strong class="{{ $totalDenda > 0 ? 'text-red-700' : 'text-gray-800' }}">Rp{{ number_format($totalDenda, 0, ',', '.') }}</strong>
                @if ($totalDenda > 0)
                    · <span class="{{ $belumLunas->isNotEmpty() ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold' }}">
                        {{ $belumLunas->isNotEmpty() ? 'Belum dibayar' : 'Lunas' }}
                    </span>
                @endif
            </div>

            @if (auth()->user()?->canManageLibrary())
                @if ($belumLunas->isNotEmpty())
                    <span class="text-xs text-gray-400" title="Tidak bisa dihapus: denda belum dibayar">Hapus (terkunci)</span>
                @else
                    <form action="{{ route('admin.pengembalian.destroy', $groupKey) }}" method="POST" data-confirm="Hapus riwayat pengembalian ini?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">Hapus Riwayat</button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        <th class="px-4 py-3 text-left font-semibold">Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Rencana</th>
                        <th class="px-4 py-3 text-center font-semibold">Aktual</th>
                        <th class="px-4 py-3 text-center font-semibold">Kondisi</th>
                        <th class="px-4 py-3 text-center font-semibold">Telat</th>
                        <th class="px-4 py-3 text-right font-semibold">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $item->buku->judul }}</p>
                                <p class="text-xs text-gray-400">{{ $item->buku->penulis }}</p>
                                @if ($item->catatan)
                                    <p class="text-xs text-gray-400">Catatan: {{ $item->catatan }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 text-xs">{{ $item->tanggalRencanaLabel() }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 text-xs">{{ $item->tanggal_kembali_aktual?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if (($item->kondisi ?? 'baik') === 'baik')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Baik</span>
                                @elseif ($item->kondisi === 'rusak')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Rusak</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Hilang</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-xs {{ $item->hari_terlambat > 0 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                {{ $item->hari_terlambat }} hari
                            </td>
                            <td class="px-4 py-3 text-right text-xs">
                                <p class="text-gray-500">Terlambat Rp{{ number_format($item->denda_terlambat, 0, ',', '.') }}</p>
                                <p class="text-gray-500">Kondisi Rp{{ number_format($item->denda_kondisi, 0, ',', '.') }}</p>
                                <p class="font-bold {{ $item->denda > 0 ? 'text-red-700' : 'text-gray-400' }}">Rp{{ number_format($item->denda, 0, ',', '.') }}</p>
                                @if ($item->denda > 0)
                                    <p class="text-xs {{ $item->isDendaLunas() ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $item->isDendaLunas() ? 'Lunas' : 'Belum dibayar' }}
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
