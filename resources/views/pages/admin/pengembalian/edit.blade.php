@extends('layouts.admin')

@section('title', 'Pengembalian Buku')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Pengembalian Buku</h1>
        <p class="text-sm text-gray-500">Proses pengembalian buku dan hitung denda jika terlambat.</p>
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
                    <p class="font-medium text-gray-800">{{ $pengembalian->anggota->nama }}</p>
                    <p class="text-xs text-gray-400">{{ $pengembalian->anggota->nomor_anggota }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Buku</p>
                    <p class="font-medium text-gray-800">{{ $pengembalian->buku->judul }}</p>
                    <p class="text-xs text-gray-400">{{ $pengembalian->buku->penulis }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Tanggal Pinjam</p>
                    <p class="font-medium text-gray-800">{{ $pengembalian->tanggal_pinjam->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Tanggal Kembali Rencana</p>
                    <p class="font-medium text-gray-800">{{ $pengembalian->tanggal_kembali_rencana->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Durasi Peminjaman</p>
                    <p class="font-medium text-gray-800">{{ $pengembalian->tanggal_kembali_rencana->diffInDays($pengembalian->tanggal_pinjam) }} hari</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Status</p>
                    <p class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Sedang Dipinjam
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Pengembalian -->
        <form action="{{ route('admin.pengembalian.update', $pengembalian) }}" method="POST" class="space-y-4 border-t border-gray-200 pt-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali Aktual <span class="text-red-600">*</span></label>
                <input type="date" name="tanggal_kembali_aktual" value="{{ old('tanggal_kembali_aktual', date('Y-m-d')) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600"
                    id="tanggal_kembali_aktual">
                @error('tanggal_kembali_aktual')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview Denda -->
            <div id="preview-denda" class="hidden p-4 rounded-lg bg-yellow-50 border border-yellow-200">
                <p class="text-sm text-gray-600 mb-2">
                    <span class="font-medium">Perkiraan Denda:</span>
                    <span id="denda-value" class="font-semibold text-yellow-800">—</span>
                </p>
                <p class="text-xs text-gray-500">
                    Denda dihitung Rp 5.000 per hari terlambat dari tanggal kembali rencana.
                </p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-4 py-2 bg-green-700 text-white text-sm font-semibold rounded-lg hover:bg-green-800">
                    Simpan Pengembalian
                </button>
                <a href="{{ route('admin.pengembalian.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const inputTanggalKembaliAktual = document.getElementById('tanggal_kembali_aktual');
    const previewDenda = document.getElementById('preview-denda');
    const dendaValue = document.getElementById('denda-value');

    const tanggalRencana = new Date('{{ $pengembalian->tanggal_kembali_rencana->format('Y-m-d') }}');

    function calculateDenda() {
        const tanggalAktual = new Date(inputTanggalKembaliAktual.value);
        const diffTime = tanggalAktual - tanggalRencana;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays > 0) {
            const denda = diffDays * 5000;
            dendaValue.textContent = 'Rp' + denda.toLocaleString('id-ID');
            previewDenda.classList.remove('hidden');
        } else {
            previewDenda.classList.add('hidden');
        }
    }

    inputTanggalKembaliAktual.addEventListener('change', calculateDenda);
    calculateDenda();
</script>
@endsection
