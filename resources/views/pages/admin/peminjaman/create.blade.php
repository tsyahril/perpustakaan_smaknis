@extends('layouts.admin')

@section('title', 'Tambah Peminjaman')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Tambah Peminjaman</h1>
        <p class="text-sm text-gray-500">Isi data peminjaman buku oleh anggota.</p>
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

    <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
        @csrf

        <div class="relative" id="anggotaPicker">
            <label class="block text-sm font-medium text-gray-700 mb-1">Anggota <span class="text-red-600">*</span></label>
            @php $anggotaTerpilih = $anggotas->firstWhere('id', old('anggota_id')); @endphp
            <input type="text" id="anggotaKeyword" autocomplete="off" required
                placeholder="Ketik nama / nomor anggota..."
                value="{{ $anggotaTerpilih ? $anggotaTerpilih->nama . ' (' . $anggotaTerpilih->nomor_anggota . ')' : '' }}"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            <input type="hidden" name="anggota_id" id="anggotaId" value="{{ old('anggota_id') }}">
            <div id="anggotaDropdown" class="hidden absolute z-10 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg">
                @foreach ($anggotas as $anggota)
                    <button type="button" data-id="{{ $anggota->id }}"
                        data-cari="{{ strtolower($anggota->nama . ' ' . $anggota->nomor_anggota) }}"
                        data-label="{{ $anggota->nama }} ({{ $anggota->nomor_anggota }})"
                        class="anggota-opsi flex w-full items-center justify-between gap-2 px-3 py-2 text-sm text-left text-gray-700 hover:bg-blue-50">
                        <span class="font-medium">{{ $anggota->nama }}</span>
                        <span class="text-xs text-gray-400">{{ $anggota->nomor_anggota }}</span>
                    </button>
                @endforeach
                <p id="anggotaKosong" class="hidden px-3 py-2 text-xs text-gray-400">Tidak ada anggota yang cocok.</p>
            </div>
            @error('anggota_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <script>
            (function () {
                var wrap = document.getElementById('anggotaPicker');
                var keyword = document.getElementById('anggotaKeyword');
                var hiddenId = document.getElementById('anggotaId');
                var dropdown = document.getElementById('anggotaDropdown');
                var emptyMsg = document.getElementById('anggotaKosong');
                var options = Array.prototype.slice.call(dropdown.querySelectorAll('.anggota-opsi'));

                function filter() {
                    var q = keyword.value.toLowerCase().trim();
                    var shown = 0;
                    options.forEach(function (opt) {
                        var cocok = !q || opt.getAttribute('data-cari').indexOf(q) !== -1;
                        opt.classList.toggle('hidden', !cocok);
                        if (cocok) shown++;
                    });
                    emptyMsg.classList.toggle('hidden', shown > 0);
                    dropdown.classList.remove('hidden');
                }

                keyword.addEventListener('input', function () {
                    hiddenId.value = '';
                    filter();
                });
                keyword.addEventListener('focus', filter);

                options.forEach(function (opt) {
                    opt.addEventListener('click', function () {
                        hiddenId.value = opt.getAttribute('data-id');
                        keyword.value = opt.getAttribute('data-label');
                        dropdown.classList.add('hidden');
                    });
                });

                document.addEventListener('click', function (e) {
                    if (!wrap.contains(e.target)) dropdown.classList.add('hidden');
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') dropdown.classList.add('hidden');
                });

                wrap.closest('form').addEventListener('submit', function (e) {
                    if (!hiddenId.value) {
                        e.preventDefault();
                        keyword.focus();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'warning', title: 'Pilih anggota', text: 'Ketik lalu pilih anggota dari daftar saran.' });
                        }
                    }
                });
            })();
        </script>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buku <span class="text-red-600">*</span></label>
            <select name="buku_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                <option value="">Pilih Buku</option>
                @foreach ($bukus as $buku)
                    <option value="{{ $buku->id }}" {{ old('buku_id') == $buku->id ? 'selected' : '' }}>
                        {{ $buku->judul }} (Stok: {{ $buku->stok }})
                    </option>
                @endforeach
            </select>
            @error('buku_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pinjam <span class="text-red-600">*</span></label>
                <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                @error('tanggal_pinjam')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali Rencana <span class="text-red-600">*</span></label>
                <input type="date" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana') }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                @error('tanggal_kembali_rencana')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">
                Simpan Peminjaman
            </button>
            <a href="{{ route('admin.peminjaman.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
