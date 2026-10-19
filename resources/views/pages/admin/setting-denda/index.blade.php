@extends('layouts.admin')

@section('title', 'Setting Denda')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Setting Denda</h1>
        <p class="text-sm text-gray-500">Tarif dipakai saat verifikasi pengembalian. Kalau tarif diubah, semua denda yang sudah tersimpan otomatis dihitung ulang — cek hasilnya di <a href="{{ route('admin.denda.index') }}" class="text-blue-700 hover:underline font-medium">Data Denda</a>.</p>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.setting-denda.update') }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Denda terlambat per hari (Rp) <span class="text-red-600">*</span></label>
            <input type="number" name="denda_terlambat_per_hari" min="0" max="1000000" value="{{ old('denda_terlambat_per_hari', $setting->denda_terlambat_per_hari) }}" required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            <p class="mt-1 text-xs text-gray-400">Dikalikan otomatis dengan jumlah hari terlambat. Berlaku ke riwayat yang sudah ada.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Denda buku rusak (Rp) <span class="text-red-600">*</span></label>
            <input type="number" name="denda_rusak" min="0" max="10000000" value="{{ old('denda_rusak', $setting->denda_rusak) }}" required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            <p class="mt-1 text-xs text-gray-400">Buku fisik kembali, stok bertambah, tapi kena denda ini. Berlaku ke riwayat yang sudah ada.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Denda buku hilang (Rp) <span class="text-red-600">*</span></label>
            <input type="number" name="denda_hilang" min="0" max="10000000" value="{{ old('denda_hilang', $setting->denda_hilang) }}" required
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
            <p class="mt-1 text-xs text-gray-400">Buku tidak kembali, stok tidak bertambah, kena denda ini + denda telat bila ada. Berlaku ke riwayat yang sudah ada.</p>
        </div>

        <div class="flex gap-3 pt-2 border-t border-gray-100">
            <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">Simpan Setting</button>
            <a href="{{ route('admin.denda.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Lihat Data Denda</a>
        </div>
    </form>
</div>
@endsection
