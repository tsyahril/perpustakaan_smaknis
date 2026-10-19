@extends('layouts.admin')

@section('title', 'Tagihan Anggota')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Tagihan: {{ $anggota->nama }}</h1>
        <p class="text-sm text-gray-500">{{ $anggota->nomor_anggota }} · Total tagihan <strong class="text-red-700">Rp{{ number_format($total, 0, ',', '.') }}</strong></p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        <th class="px-4 py-3 text-left font-semibold">Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Kembali</th>
                        <th class="px-4 py-3 text-center font-semibold">Kondisi / Telat</th>
                        <th class="px-4 py-3 text-right font-semibold">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($riwayat as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $r->buku->judul }}</p>
                                <p class="text-xs text-gray-400">{{ $r->buku->penulis }}</p>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $r->tanggal_kembali_aktual->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-xs text-gray-600">
                                {{ ucfirst($r->kondisi ?? '-') }} · {{ $r->hari_terlambat }} hari
                                @if ($r->catatan)
                                    <span class="block text-gray-400">{{ $r->catatan }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-xs text-gray-500">Terlambat Rp{{ number_format($r->denda_terlambat, 0, ',', '.') }} + Kondisi Rp{{ number_format($r->denda_kondisi, 0, ',', '.') }}</p>
                                <p class="font-bold text-red-700">Rp{{ number_format($r->denda, 0, ',', '.') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-gray-400">Anggota ini tidak punya tagihan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($riwayat->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $riwayat->links() }}</div>
        @endif
    </div>

    <a href="{{ route('admin.denda.index') }}" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Kembali ke Denda</a>
</div>
@endsection
