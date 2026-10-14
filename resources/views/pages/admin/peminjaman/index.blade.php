@extends('layouts.admin')

@section('title', 'Data Peminjaman')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Data Peminjaman</h1>
            <p class="text-sm text-gray-500">Kelola peminjaman buku oleh anggota.</p>
        </div>
        <a href="{{ route('admin.peminjaman.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Peminjaman
        </a>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="GET" action="{{ route('admin.peminjaman.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-gray-100">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama anggota / judul buku..."
                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Cari</button>
            @if ($search)
                <a href="{{ route('admin.peminjaman.index') }}" class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-center">Reset</a>
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
                        <th class="px-4 py-3 text-center font-semibold">Durasi</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($peminjamans as $i => $peminjaman)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $peminjamans->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $peminjaman->anggota->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $peminjaman->anggota->nomor_anggota }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $peminjaman->buku->judul }}</p>
                                <p class="text-xs text-gray-400">{{ $peminjaman->buku->penulis }}</p>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $peminjaman->tanggal_kembali_rencana->diffInDays($peminjaman->tanggal_pinjam) }} hari
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.peminjaman.edit', $peminjaman) }}" class="text-sm text-blue-700 hover:underline">Edit</a>
                                    <span class="text-gray-200">|</span>
                                    <form action="{{ route('admin.peminjaman.destroy', $peminjaman) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">Belum ada data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($peminjamans->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $peminjamans->links() }}</div>
        @endif
    </div>
</div>
@endsection
