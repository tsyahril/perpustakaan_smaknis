@extends('layouts.admin')

@section('title', 'Tambah Anggota')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Tambah Anggota</h1>
        <p class="text-sm text-gray-500">Isi data nama, gmail, password, dan role anggota.</p>
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

    <form action="{{ route('admin.anggota.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="cth: Budi Santoso"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Gmail</label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="cth: nama@gmail.com"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi password"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
            <select id="role" name="role" required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">— Pilih Role —</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ old('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
        </div>

        <div id="aksesGuruWrapper" class="hidden bg-purple-50 border border-purple-200 rounded-lg p-4">
            <label class="block text-sm font-semibold text-purple-800 mb-1">Setting Akses Guru</label>
            <p class="text-xs text-purple-600 mb-2">Tentukan apakah guru ini menjadi <b>pustakawan</b> atau <b>guru biasa</b>.</p>
            <select id="is_petugas" name="is_petugas"
                class="w-full px-3 py-2 text-sm border border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-white">
                <option value="0" {{ old('is_petugas') == '0' ? 'selected' : '' }}>Guru Biasa</option>
                <option value="1" {{ old('is_petugas') == '1' ? 'selected' : '' }}>Pustakawan (bisa kelola perpustakaan)</option>
            </select>
        </div>

        <div id="aksesAdminInfo" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm font-semibold text-red-800">Akses: Semua</p>
            <p class="text-xs text-red-600 mt-0.5">Admin otomatis memiliki semua akses perpustakaan.</p>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('admin.anggota.index') }}"
                class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
            <button type="submit"
                class="px-4 py-2 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">Simpan</button>
        </div>
    </form>
</div>

<script>
    (function () {
        var role = document.getElementById('role');
        var wrapper = document.getElementById('aksesGuruWrapper');
        var adminInfo = document.getElementById('aksesAdminInfo');
        function toggle() {
            if (role.value === 'guru') wrapper.classList.remove('hidden');
            else wrapper.classList.add('hidden');
            if (role.value === 'admin') adminInfo.classList.remove('hidden');
            else adminInfo.classList.add('hidden');
        }
        role.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endsection
