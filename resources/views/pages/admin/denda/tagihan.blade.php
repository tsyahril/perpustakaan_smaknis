@extends('layouts.admin')

@section('title', 'Tagihan Anggota')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-[#2D251E]">Tagihan: {{ $anggota->nama }}</h1>
        <p class="text-sm text-[#8B7E71]">{{ $anggota->nomor_anggota }} Â· Total tagihan <strong class="text-red-700">Rp{{ number_format($total, 0, ',', '.') }}</strong></p>
    </div>

    <div class="bg-white rounded-lg border border-[#E8E1D7] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-[#FAF9F7] text-[#97897B] text-xs border-b border-[#F0EAE1]">
                        <th class="px-4 py-3 text-left font-semibold">Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Kembali</th>
                        <th class="px-4 py-3 text-center font-semibold">Kondisi / Telat</th>
                        <th class="px-4 py-3 text-right font-semibold">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0EAE1]">
                    @forelse ($riwayat as $r)
                        <tr class="hover:bg-[#FAF9F7]">
                            <td class="px-4 py-3">
                                <p class="font-medium text-[#332B24]">{{ $r->buku->judul }}</p>
                                <p class="text-xs text-[#8B7E71]">{{ $r->buku->penulis }}</p>
                            </td>
                            <td class="px-4 py-3 text-center text-[#7C6F62]">{{ $r->tanggal_kembali_aktual->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-xs text-[#7C6F62]">
                                {{ ucfirst($r->kondisi ?? '-') }} Â· {{ $r->hari_terlambat }} hari
                                @if ($r->catatan)
                                    <span class="block text-[#8B7E71]">{{ $r->catatan }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-xs text-[#97897B]">Terlambat Rp{{ number_format($r->denda_terlambat, 0, ',', '.') }} + Kondisi Rp{{ number_format($r->denda_kondisi, 0, ',', '.') }}</p>
                                <p class="font-bold text-red-700">Rp{{ number_format($r->denda, 0, ',', '.') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-[#8B7E71]">Anggota ini tidak punya tagihan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($riwayat->hasPages())
            <div class="p-4 border-t border-[#F0EAE1]">{{ $riwayat->links() }}</div>
        @endif
    </div>

    <a href="{{ route('admin.denda.index') }}" class="inline-block px-4 py-2 bg-[#F7F4EF] text-[#4A4038] text-sm font-semibold rounded-lg hover:bg-[#F0EAE1]">Kembali ke Denda</a>
</div>
@endsection

