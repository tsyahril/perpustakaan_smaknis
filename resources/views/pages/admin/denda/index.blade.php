@extends('layouts.admin')

@section('title', 'Data Denda')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Data Denda</h1>
        <p class="text-sm text-gray-500">Rincian denda terlambat / rusak / hilang + tagihan per anggota. Ubah tarif di <a href="{{ route('admin.setting-denda.index') }}" class="text-blue-700 hover:underline font-medium">Setting Denda</a>.</p>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Total Denda Keseluruhan</p>
            <p class="text-2xl font-bold text-gray-800">Rp{{ number_format($grandTotal, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Jumlah Transaksi Denda</p>
            <p class="text-2xl font-bold text-gray-800">{{ $dendas->total() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Rata-rata Denda</p>
            <p class="text-2xl font-bold text-gray-800">
                @if ($dendas->total() > 0)
                    Rp{{ number_format($grandTotal / max($dendas->total(), 1), 0, ',', '.') }}
                @else
                    Rp0
                @endif
            </p>
        </div>
    </div>

    {{-- Tagihan per anggota --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-sm">Tagihan per Anggota</h2>
            <p class="text-xs text-gray-500">Klik nama untuk melihat riwayat denda apa saja yang dia dapat.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        <th class="px-4 py-2 text-left font-semibold">Anggota</th>
                        <th class="px-4 py-2 text-center font-semibold">Jumlah Kasus</th>
                        <th class="px-4 py-2 text-right font-semibold">Terlambat</th>
                        <th class="px-4 py-2 text-right font-semibold">Kondisi</th>
                        <th class="px-4 py-2 text-right font-semibold">Total Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tagihan as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.denda.tagihan', $t->anggota_id) }}" class="font-medium text-blue-700 hover:underline">{{ $t->anggota->nama ?? '-' }}</a>
                                <p class="text-xs text-gray-400">{{ $t->anggota->nomor_anggota ?? '' }}</p>
                            </td>
                            <td class="px-4 py-2 text-center">{{ $t->jumlah }}</td>
                            <td class="px-4 py-2 text-right text-gray-600">Rp{{ number_format($t->total_terlambat, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right text-gray-600">Rp{{ number_format($t->total_kondisi, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right font-semibold text-red-700">Rp{{ number_format($t->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-sm">Belum ada tagihan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="GET" action="{{ route('admin.denda.index') }}" class="grid md:grid-cols-4 gap-2 p-4 border-b border-gray-100">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama anggota / judul buku..."
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            <select name="jenis" class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">Semua jenis denda</option>
                <option value="terlambat" {{ $jenis === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="rusak" {{ $jenis === 'rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="hilang" {{ $jenis === 'hilang' ? 'selected' : '' }}>Hilang</option>
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Filter</button>
            @if ($search || $jenis !== '')
                <a href="{{ route('admin.denda.index') }}" class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Anggota</th>
                        <th class="px-4 py-3 text-left font-semibold">Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Kondisi / Telat</th>
                        <th class="px-4 py-3 text-right font-semibold">Rincian Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($dendas as $i => $denda)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $dendas->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.denda.tagihan', $denda->anggota_id) }}" class="font-medium text-gray-800 hover:text-blue-700 hover:underline">{{ $denda->anggota->nama }}</a>
                                <p class="text-xs text-gray-400">{{ $denda->anggota->nomor_anggota }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $denda->buku->judul }}</p>
                                <p class="text-xs text-gray-400">Kembali: {{ $denda->tanggal_kembali_aktual->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $denda->kondisi === 'baik' ? 'bg-green-100 text-green-800' : ($denda->kondisi === 'rusak' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($denda->kondisi ?? 'baik') }}
                                </span>
                                <span class="block mt-1 text-xs text-gray-500">{{ $denda->hari_terlambat }} hari telat</span>
                            </td>
                            <td class="px-4 py-3 text-right text-xs space-y-0.5">
                                <p class="text-gray-500">Terlambat: Rp{{ number_format($denda->denda_terlambat, 0, ',', '.') }}</p>
                                <p class="text-gray-500">Kondisi: Rp{{ number_format($denda->denda_kondisi, 0, ',', '.') }}</p>
                                <p class="font-bold text-red-700">Total: Rp{{ number_format($denda->denda, 0, ',', '.') }}</p>
                                <a href="{{ route('admin.denda.show', $denda) }}" class="text-blue-700 hover:underline">Detail →</a>
                                @if ($denda->denda_dibayar)
                                    <p class="text-xs text-green-600 font-semibold">Lunas</p>
                                @else
                                    <form action="{{ route('admin.denda.bayar', $denda) }}" method="POST" class="inline mt-1" data-confirm="Tandai denda ini sudah lunas?">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-green-700 hover:underline">Tandai Lunas</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-400">Belum ada data denda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($dendas->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $dendas->links() }}</div>
        @endif
    </div>
</div>
@endsection
