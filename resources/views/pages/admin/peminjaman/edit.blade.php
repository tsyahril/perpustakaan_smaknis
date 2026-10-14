@extends('layouts.admin')

@section('title', 'Edit Peminjaman')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Edit Peminjaman</h1>
        <p class="text-sm text-gray-500">Ubah data peminjaman buku.</p>
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

    <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-6">
        <!-- Info Peminjaman -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-800">Informasi Peminjaman</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Anggota</p>
                    <p class="font-medium text-gray-800">{{ $peminjaman->anggota->nama }}</p>
                    <p class="text-xs text-gray-400">{{ $peminjaman->anggota->nomor_anggota }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Buku</p>
                    <p class="font-medium text-gray-800">{{ $peminjaman->buku->judul }}</p>
                    <p class="text-xs text-gray-400">{{ $peminjaman->buku->penulis }}</p>
                </div>
            </div>
        </div>

        <!-- Form Edit -->
        <form action="{{ route('admin.peminjaman.update', $peminjaman) }}" method="POST" class="space-y-4 border-t border-gray-200 pt-6">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pinjam <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', $peminjaman->tanggal_pinjam->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                    @error('tanggal_pinjam')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali Rencana <span class="text-red-600">*</span></label>
                    <input type="date" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana', $peminjaman->tanggal_kembali_rencana->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                    @error('tanggal_kembali_rencana')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.peminjaman.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
