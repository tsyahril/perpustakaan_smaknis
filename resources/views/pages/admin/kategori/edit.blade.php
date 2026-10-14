@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Edit Kategori</h1>
        <p class="text-sm text-gray-500">Perbarui nama kategori.</p>
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

    <form action="{{ route('admin.kategori.update', $kategori) }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
            <input type="text" name="nama" value="{{ old('nama', $kategori->nama) }}" required maxlength="100"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">Update</button>
        </div>
    </form>
</div>
@endsection
