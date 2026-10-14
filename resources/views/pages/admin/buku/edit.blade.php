@extends('layouts.admin')

@section('title', 'Edit Buku')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Edit Buku</h1>
        <p class="text-sm text-gray-500">Perbarui data <span class="font-medium">{{ $buku->judul }}</span>.</p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.buku.update', $buku) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cover <span class="font-normal text-gray-400">(kosongkan jika tidak diganti, maks 5MB)</span></label>
            <div class="flex items-start gap-3">
                @if ($buku->coverUrl())
                    <img src="{{ $buku->coverUrl() }}" alt="Cover {{ $buku->judul }}" class="w-16 h-24 object-cover rounded border border-gray-200">
                @endif
                <input type="file" name="cover" accept="image/*"
                    class="w-full text-sm text-gray-600 file:mr-3 file:px-3 file:py-2 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 file:border file:border-gray-300 file:rounded-lg hover:file:bg-gray-200">
                @error('cover')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
            <input type="text" name="judul" value="{{ old('judul', $buku->judul) }}" required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Penulis</label>
                <input type="text" name="penulis" value="{{ old('penulis', $buku->penulis) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                <input type="text" name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                <input type="text" name="isbn" value="{{ old('isbn', $buku->isbn) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
                <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" required min="1000" max="{{ date('Y') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="id_kategori" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ (string) old('id_kategori', $buku->id_kategori) === (string) $kategori->id ? 'selected' : '' }}>{{ $kategori->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                <input type="number" name="stok" value="{{ old('stok', $buku->stok) }}" required min="0"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="font-normal text-gray-400">(opsional)</span></label>
            <textarea name="deskripsi" rows="4"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">{{ old('deskripsi', $buku->deskripsi) }}</textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('admin.buku.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">Update</button>
        </div>
    </form>
</div>
@endsection
