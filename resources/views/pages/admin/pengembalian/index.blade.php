@extends('layouts.admin')

@section('title', 'Data Pengembalian')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Data Pengembalian</h1>
            <p class="text-sm text-gray-500">Proses pengembalian buku yang sedang dipinjam.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="GET" action="{{ route('admin.pengembalian.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-gray-100">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama anggota / judul buku..."
                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Cari</button>
            @if ($search)
                <a href="{{ route('admin.pengembalian.index') }}" class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Anggota</th>
                        <th class="px-4 py-3 text-left font-semibold">Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Tgl Pinjam</th>
                        <th class="px-4 py-3 text-center font-semibold">Tgl Kembali Rencana</th>
                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pengembalians as $i => $pengembalian)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $pengembalians->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $pengembalian->anggota->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $pengembalian->anggota->nomor_anggota }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $pengembalian->buku->judul }}</p>
                                <p class="text-xs text-gray-400">{{ $pengembalian->buku->penulis }}</p>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $pengembalian->tanggal_pinjam->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $pengembalian->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Sedang Dipinjam
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.pengembalian.edit', $pengembalian) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-green-700 rounded-lg hover:bg-green-800">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Kembalikan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">Semua buku sudah dikembalikan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengembalians->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $pengembalians->links() }}</div>
        @endif
    </div>
</div>
@endsection
