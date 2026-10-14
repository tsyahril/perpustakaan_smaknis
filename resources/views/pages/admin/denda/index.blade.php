@extends('layouts.admin')

@section('title', 'Data Denda')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Data Denda</h1>
            <p class="text-sm text-gray-500">Laporan denda dari pengembalian buku terlambat.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <!-- Summary Card -->
    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Total Denda</p>
            <p class="text-2xl font-bold text-gray-800">Rp{{ number_format($totalDenda, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Jumlah Transaksi Denda</p>
            <p class="text-2xl font-bold text-gray-800">{{ $dendas->total() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-1">Rata-rata Denda</p>
            <p class="text-2xl font-bold text-gray-800">
                @if ($dendas->total() > 0)
                    Rp{{ number_format($totalDenda / $dendas->total(), 0, ',', '.') }}
                @else
                    Rp0
                @endif
            </p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="GET" action="{{ route('admin.denda.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-gray-100">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama anggota / judul buku..."
                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Cari</button>
            @if ($search)
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
                        <th class="px-4 py-3 text-center font-semibold">Tgl Kembali Rencana</th>
                        <th class="px-4 py-3 text-center font-semibold">Tgl Kembali Aktual</th>
                        <th class="px-4 py-3 text-center font-semibold">Hari Terlambat</th>
                        <th class="px-4 py-3 text-right font-semibold">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($dendas as $i => $denda)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $dendas->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $denda->anggota->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $denda->anggota->nomor_anggota }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $denda->buku->judul }}</p>
                                <p class="text-xs text-gray-400">{{ $denda->buku->penulis }}</p>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $denda->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $denda->tanggal_kembali_aktual->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $denda->tanggal_kembali_aktual->diffInDays($denda->tanggal_kembali_rencana) }} hari
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    Rp{{ number_format($denda->denda, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">Belum ada data denda.</td>
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
