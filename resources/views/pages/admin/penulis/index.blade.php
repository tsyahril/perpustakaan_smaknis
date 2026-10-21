@extends('layouts.admin')

@section('title', 'Data Penulis')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-[#2D251E]">Data Penulis</h1>
        <p class="text-sm text-[#8B7E71]">Daftar penulis dari koleksi buku. Penulis baru otomatis tercatat saat tambah buku.</p>
    </div>

    <div class="bg-white rounded-lg border border-[#E8E1D7] overflow-hidden">
        <form method="GET" action="{{ route('admin.penulis.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-[#F0EAE1]">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari penulis..."
                class="flex-1 px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg focus:outline-none focus:ring-1 focus:ring-sepia-600 focus:border-sepia-600">
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-[#FFFFFF] text-[#2D251E] rounded-lg hover:bg-[#F0EAE1]">Cari</button>
            @if ($search)
                <a href="{{ route('admin.penulis.index') }}" class="px-4 py-2 text-sm font-semibold bg-[#F7F4EF] text-[#7C6F62] rounded-lg hover:bg-[#F0EAE1] text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-[#FAF9F7] text-[#97897B] text-xs border-b border-[#F0EAE1]">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Nama Penulis</th>
                        <th class="px-4 py-3 text-center font-semibold">Jumlah Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0EAE1]">
                    @forelse ($penulis as $i => $row)
                        <tr class="hover:bg-[#FAF9F7]">
                            <td class="px-4 py-3 text-[#8B7E71]">{{ $penulis->firstItem() + $i }}</td>
                            <td class="px-4 py-3 font-medium text-[#332B24]">{{ $row->penulis }}</td>
                            <td class="px-4 py-3 text-center text-[#7C6F62]">{{ $row->jumlah_buku }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.buku.index', ['search' => $row->penulis]) }}" class="text-sm text-sepia-700 hover:underline">Lihat Buku</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-[#8B7E71]">Belum ada data penulis.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($penulis->hasPages())
            <div class="p-4 border-t border-[#F0EAE1]">{{ $penulis->links() }}</div>
        @endif
    </div>
</div>
@endsection

