@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-[#2D251E]">Kategori</h1>
            <p class="text-sm text-[#8B7E71]">Kelola kategori buku.</p>
        </div>
        <a href="{{ route('admin.kategori.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-sepia-700 text-white text-sm font-semibold rounded-lg hover:bg-sepia-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kategori
        </a>
    </div>

    <div class="bg-white rounded-lg border border-[#E8E1D7] overflow-hidden">
        <form method="GET" action="{{ route('admin.kategori.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-[#F0EAE1]">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari kategori..."
                class="flex-1 px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg focus:outline-none focus:ring-1 focus:ring-sepia-600 focus:border-sepia-600">
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-[#FFFFFF] text-[#2D251E] rounded-lg hover:bg-[#F0EAE1]">Cari</button>
            @if ($search)
                <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2 text-sm font-semibold bg-[#F7F4EF] text-[#7C6F62] rounded-lg hover:bg-[#F0EAE1] text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-[#FAF9F7] text-[#97897B] text-xs border-b border-[#F0EAE1]">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Nama Kategori</th>
                        <th class="px-4 py-3 text-center font-semibold">Jumlah Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0EAE1]">
                    @forelse ($kategoris as $i => $kategori)
                        <tr class="hover:bg-[#FAF9F7]">
                            <td class="px-4 py-3 text-[#8B7E71]">{{ $kategoris->firstItem() + $i }}</td>
                            <td class="px-4 py-3 font-medium text-[#332B24]">{{ $kategori->nama }}</td>
                            <td class="px-4 py-3 text-center text-[#7C6F62]">{{ $kategori->bukus_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.kategori.edit', $kategori) }}" class="text-sm text-sepia-700 hover:underline">Edit</a>
                                    <span class="text-[#3D342C]">|</span>
                                    <form action="{{ route('admin.kategori.destroy', $kategori) }}" method="POST" class="inline"
                                        data-confirm="Hapus kategori {{ $kategori->nama }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-[#8B7E71]">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kategoris->hasPages())
            <div class="p-4 border-t border-[#F0EAE1]">{{ $kategoris->links() }}</div>
        @endif
    </div>
</div>
@endsection

