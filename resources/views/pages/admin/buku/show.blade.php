@extends('layouts.admin')

@section('title', 'Detail Buku')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-100">Detail Buku</h1>
            <p class="text-sm text-slate-400">Informasi lengkap buku.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.buku.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Kembali</a>
            <a href="{{ route('admin.buku.edit', $buku) }}" class="px-4 py-2 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">Edit</a>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-shrink-0">
                @if ($buku->coverUrl())
                    <img src="{{ $buku->coverUrl() }}" alt="Cover {{ $buku->judul }}" class="w-40 h-56 object-cover rounded-lg border border-gray-200">
                @else
                    <div class="w-40 h-56 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center text-gray-300 text-sm">Tanpa cover</div>
                @endif
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-bold text-slate-100">{{ $buku->judul }}</h2>
                <p class="text-sm text-slate-400 mt-0.5">{{ $buku->penulis }}</p>

                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-gray-400">Penerbit</dt>
                        <dd class="text-gray-800">{{ $buku->penerbit }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-gray-400">ISBN</dt>
                        <dd class="text-gray-800">{{ $buku->isbn }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-gray-400">Tahun Terbit</dt>
                        <dd class="text-gray-800">{{ $buku->tahun_terbit }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-gray-400">Kategori</dt>
                        <dd class="text-gray-800">{{ $buku->kategori->nama ?? 'â€”' }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 flex-shrink-0 text-gray-400">Stok</dt>
                        <dd class="text-gray-800">{{ $buku->stok }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if ($buku->deskripsi)
            <div class="mt-6 pt-4 border-t border-gray-100">
                <p class="text-sm font-medium text-gray-700 mb-1">Deskripsi</p>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $buku->deskripsi }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

