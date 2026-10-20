@extends('layouts.admin')

@section('title', 'Tambah Anggota')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-slate-100">Tambah Anggota</h1>
        <p class="text-sm text-slate-400">Isi nama, gmail, password, dan role. Siswa wajib pakai kolom kelas, guru wajib pakai kolom bagian.</p>
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
            <p class="mt-1 text-xs text-gray-400">Kalau nama ini sama dengan kartu anggota yang sudah ada, keduanya otomatis disambung.</p>
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
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="pustakawan" {{ old('role') === 'pustakawan' ? 'selected' : '' }}>Pustakawan</option>
                <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ old('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
            <p class="mt-1 text-xs text-gray-400">Pustakawan punya akses penuh perpustakaan, sama seperti admin.</p>
        </div>

        {{-- Kolom kelas untuk siswa / bagian untuk guru --}}
        <div id="kelasWrapper" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
            <label id="kelasLabel" class="block text-sm font-semibold text-blue-800 mb-1">Kelas</label>
            <select name="kelas" id="kelasSelect"
                class="w-full px-3 py-2 text-sm border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"></select>
            <p id="kelasHint" class="mt-1 text-xs text-blue-600"></p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Kartu Anggota (opsional)</label>
            @php $kartuTerpilih = (string) old('id_anggota', 'auto'); @endphp
            <select name="id_anggota"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="auto" {{ $kartuTerpilih === 'auto' ? 'selected' : '' }}>Otomatis dari nama</option>
                @foreach ($daftarAnggota as $a)
                    <option value="{{ $a->id }}" {{ $kartuTerpilih === (string) $a->id ? 'selected' : '' }}>
                        {{ $a->nomor_anggota }} â€” {{ $a->nama }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-400">Tautan ini dipakai untuk menghitung Total Pinjam pada halaman daftar anggota.</p>
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
        var DAFTAR_KELAS = @json($daftarKelas);
        var BAGIAN_GURU = @json($bagianGuru);
        var role = document.getElementById('role');
        var wrap = document.getElementById('kelasWrapper');
        var label = document.getElementById('kelasLabel');
        var hint = document.getElementById('kelasHint');
        var sel = document.getElementById('kelasSelect');
        var current = @json(old('kelas', ''));

        function toggle() {
            var isSiswa = role.value === 'siswa';
            var isGuru = role.value === 'guru';

            if (!isSiswa && !isGuru) {
                wrap.classList.add('hidden');
                sel.innerHTML = '';
                sel.disabled = true;
                return;
            }

            wrap.classList.remove('hidden');
            sel.disabled = false;

            var opsi = isSiswa ? DAFTAR_KELAS : BAGIAN_GURU;
            label.textContent = isSiswa ? 'Kelas (khusus siswa)' : 'Bagian / Jabatan (khusus guru)';
            hint.textContent = isSiswa
                ? 'Pilih kelas siswa, cth: X A atau XI B.'
                : 'Pilih bagian guru, cth: Staf atau Guru BK.';

            sel.innerHTML = '<option value="">-- Pilih --</option>' + opsi.map(function (o) {
                return '<option value="' + o + '">' + o + '</option>';
            }).join('');

            if (current && opsi.indexOf(current) !== -1) {
                sel.value = current;
            }
        }

        role.addEventListener('change', function () {
            current = sel.value;
            toggle();
        });

        toggle();
    })();
</script>
@endsection
