@extends('layouts.admin')

@section('title', 'Data Peminjaman')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Data Peminjaman</h1>
            <p class="text-sm text-gray-500">Pinjaman aktif yang sudah disetujui. Pengajuan baru ada di <a href="{{ route('admin.request-peminjaman.index') }}" class="text-blue-700 hover:underline font-medium">Request Peminjaman</a>.</p>
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
        <form method="GET" action="{{ route('admin.peminjaman.index') }}" class="grid md:grid-cols-3 gap-2 p-4 border-b border-gray-100">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama anggota / judul buku..."
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">Semua Status</option>
                <option value="pinjam" {{ $status === 'pinjam' ? 'selected' : '' }}>Masih Dipinjam</option>
                <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Filter</button>
                @if ($search || $status !== '')
                    <a href="{{ route('admin.peminjaman.index') }}" class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Reset</a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Kode / Anggota</th>
                        <th class="px-4 py-3 text-left font-semibold">Buku / Stok</th>
                        <th class="px-4 py-3 text-center font-semibold">Tgl Pinjam → Rencana</th>
                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($peminjamans as $i => $peminjaman)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $peminjamans->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                @if ($peminjaman->kode_peminjaman)
                                    <p class="text-xs font-mono text-blue-700">{{ $peminjaman->kode_peminjaman }}</p>
                                @endif
                                <p class="font-medium text-gray-800">{{ $peminjaman->anggota->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $peminjaman->anggota->nomor_anggota }} · {{ ucfirst($peminjaman->anggota->jenis_anggota) }}{{ $peminjaman->anggota->kelas ? ' · ' . $peminjaman->anggota->kelas->nama : '' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $peminjaman->buku->judul }}</p>
                                <p class="text-xs text-gray-400">{{ $peminjaman->buku->penulis }} · Sisa stok: <span class="font-semibold {{ $peminjaman->buku->stok > 0 ? 'text-green-700' : 'text-red-600' }}">{{ $peminjaman->buku->stok }}</span></p>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 text-xs">
                                {{ $peminjaman->tanggal_pinjam->format('d/m/Y') }} → {{ $peminjaman->tanggalRencanaLabel() }}
                                @if ($peminjaman->tanggal_kembali_rencana)
                                    <span class="block text-gray-400">{{ $peminjaman->tanggal_kembali_rencana->diffInDays($peminjaman->tanggal_pinjam) }} hari</span>
                                @else
                                    <span class="block text-gray-400">tanpa rencana</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($peminjaman->status === 'pinjam')
                                    @if ($peminjaman->hitungHariTerlambat() > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Telat {{ $peminjaman->hitungHariTerlambat() }} hari</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Dipinjam</span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Kembali{{ $peminjaman->kondisi ? ' · ' . ucfirst($peminjaman->kondisi) : '' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    @if ($peminjaman->status === 'pinjam')
                                        <a href="{{ route('admin.peminjaman.edit', $peminjaman) }}" class="text-sm text-blue-700 hover:underline">Edit</a>
                                        <span class="text-gray-200">|</span>
                                        <form action="{{ route('admin.peminjaman.destroy', $peminjaman) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Hapus peminjaman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-300">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">Belum ada data peminjaman yang disetujui.</td>
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
