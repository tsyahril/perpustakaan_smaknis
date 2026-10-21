@extends('layouts.admin')

@section('title', 'Detail Buku')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-[#2D251E]">Detail Buku</h1>
            <p class="text-sm text-[#8B7E71]">Informasi lengkap buku.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.buku.index') }}" class="px-4 py-2 text-sm font-semibold text-[#7C6F62] bg-[#F7F4EF] rounded-lg hover:bg-[#F0EAE1]">Kembali</a>
            <a href="{{ route('admin.buku.edit', $buku) }}" class="px-4 py-2 text-sm font-semibold text-white bg-sepia-700 rounded-lg hover:bg-sepia-800">Edit</a>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-[#E8E1D7] p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-shrink-0">
                @if ($buku->coverUrl())
                    <img src="{{ $buku->coverUrl() }}" alt="Cover {{ $buku->judul }}" class="w-40 h-56 object-cover rounded-lg border border-[#E8E1D7]">
                @else
                    <div class="w-40 h-56 rounded-lg border border-[#E8E1D7] bg-[#F7F4EF] flex items-center justify-center text-[#6B5E52] text-sm">Tanpa cover</div>
                @endif
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-bold text-[#2D251E]">{{ $buku->judul }}</h2>
                <p class="text-sm text-[#8B7E71] mt-0.5">{{ $buku->penulis }}</p>

                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-[#8B7E71]">Penerbit</dt>
                        <dd class="text-[#332B24]">{{ $buku->penerbit }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-[#8B7E71]">ISBN</dt>
                        <dd class="text-[#332B24]">{{ $buku->isbn }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-[#8B7E71]">Tahun Terbit</dt>
                        <dd class="text-[#332B24]">{{ $buku->tahun_terbit }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-[#8B7E71]">Kategori</dt>
                        <dd class="text-[#332B24]">{{ $buku->kategori->nama ?? 'â€”' }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-[#8B7E71]">Stok</dt>
                        <dd class="text-[#332B24]">{{ $buku->stok }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if ($buku->deskripsi)
            <div class="mt-6 pt-4 border-t border-[#F0EAE1]">
                <p class="text-sm font-medium text-[#4A4038] mb-1">Deskripsi</p>
                <p class="text-sm text-[#7C6F62] leading-relaxed">{{ $buku->deskripsi }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

