@extends('layouts.admin')

@section('title', 'Data Buku')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Data Buku</h1>
            <p class="text-sm text-gray-500">Kelola koleksi buku perpustakaan.</p>
        </div>
        <a href="{{ route('admin.buku.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Buku
        </a>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="GET" action="{{ route('admin.buku.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-gray-100">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul / penulis / ISBN..."
                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            <select name="id_kategori" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                <option value="">Semua Kategori</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ (string) $idKategori === (string) $kategori->id ? 'selected' : '' }}>{{ $kategori->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Cari</button>
            @if ($search || $idKategori)
                <a href="{{ route('admin.buku.index') }}" class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Cover</th>
                        <th class="px-4 py-3 text-left font-semibold">Judul</th>
                        <th class="px-4 py-3 text-left font-semibold">ISBN</th>
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-center font-semibold">Tahun</th>
                        <th class="px-4 py-3 text-center font-semibold">Stok</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($bukus as $i => $buku)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $bukus->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                @if ($buku->coverUrl())
                                    <img src="{{ $buku->coverUrl() }}" alt="Cover {{ $buku->judul }}" class="w-10 h-14 object-cover rounded border border-gray-200">
                                @else
                                    <div class="w-10 h-14 rounded border border-gray-200 bg-gray-100"></div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $buku->judul }}</p>
                                <p class="text-xs text-gray-400">{{ $buku->penulis }} · {{ $buku->penerbit }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $buku->isbn }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $buku->kategori->nama ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $buku->tahun_terbit }}</td>
                            <td class="px-4 py-3 text-center text-gray-800 font-medium">{{ $buku->stok }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.buku.show', $buku) }}" class="text-sm text-gray-600 hover:underline">Detail</a>
                                    <span class="text-gray-200">|</span>
                                    <a href="{{ route('admin.buku.edit', $buku) }}" class="text-sm text-blue-700 hover:underline">Edit</a>
                                    <span class="text-gray-200">|</span>
                                    <form action="{{ route('admin.buku.destroy', $buku) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus buku {{ $buku->judul }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400">Belum ada data buku.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bukus->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $bukus->links() }}</div>
        @endif
    </div>
</div>
@endsection
