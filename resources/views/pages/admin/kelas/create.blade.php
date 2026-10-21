@extends('layouts.admin')

@section('title', 'Tambah Kelas')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-[#2D251E]">Tambah Kelas</h1>
        <p class="text-sm text-[#8B7E71]">Isi nama, tingkat, dan jurusan kelas baru.</p>
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

    <form action="{{ route('admin.kelas.store') }}" method="POST" class="bg-white rounded-lg border border-[#E8E1D7] p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-[#4A4038] mb-1">Nama Kelas</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required maxlength="100" placeholder=""
                class="w-full px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg focus:outline-none focus:ring-1 focus:ring-sepia-600">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-[#4A4038] mb-1">Tingkat</label>
                <select name="tingkat" required
                    class="w-full px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg focus:outline-none focus:ring-1 focus:ring-sepia-600">
                    <option value="">â€” Pilih Tingkat â€”</option>
                    @foreach (['X', 'XI', 'XII'] as $tingkat)
                        <option value="{{ $tingkat }}" {{ old('tingkat') === $tingkat ? 'selected' : '' }}>{{ $tingkat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-[#4A4038] mb-1">Jurusan</label>
                <input type="text" name="jurusan" value="{{ old('jurusan') }}" maxlength="50" placeholder=""
                    class="w-full px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg focus:outline-none focus:ring-1 focus:ring-sepia-600">
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('admin.kelas.index') }}" class="px-4 py-2 text-sm font-semibold text-[#7C6F62] bg-[#F7F4EF] rounded-lg hover:bg-[#F0EAE1]">Batal</a>
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-sepia-700 rounded-lg hover:bg-sepia-800">Simpan</button>
        </div>
    </form>
</div>
@endsection

