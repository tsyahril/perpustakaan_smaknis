@extends('layouts.admin')

@section('title', 'Detail Denda')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Detail Denda</h1>
        <p class="text-sm text-gray-500">Rincian denda satu transaksi.</p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-4 text-sm">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-500 mb-1">Anggota</p>
                <p class="font-medium text-gray-800">{{ $denda->anggota->nama }}</p>
                <p class="text-xs text-gray-400">{{ $denda->anggota->nomor_anggota }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Buku</p>
                <p class="font-medium text-gray-800">{{ $denda->buku->judul }}</p>
                <p class="text-xs text-gray-400">{{ $denda->buku->penulis }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Rencana → Aktual</p>
                <p class="font-medium text-gray-800">{{ $denda->tanggal_kembali_rencana->format('d/m/Y') }} → {{ $denda->tanggal_kembali_aktual->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Kondisi</p>
                <p class="font-medium text-gray-800">{{ ucfirst($denda->kondisi ?? '-') }}</p>
            </div>
        </div>
        <div class="border-t border-gray-100 pt-4 space-y-1">
            <p class="text-gray-600">Hari terlambat: <strong>{{ $denda->hari_terlambat }} hari</strong></p>
            <p class="text-gray-600">Denda terlambat: <strong>Rp{{ number_format($denda->denda_terlambat, 0, ',', '.') }}</strong></p>
            <p class="text-gray-600">Denda kondisi ({{ $denda->kondisi }}): <strong>Rp{{ number_format($denda->denda_kondisi, 0, ',', '.') }}</strong></p>
            <p class="text-gray-800 text-base pt-1">Total: <strong class="text-red-700">Rp{{ number_format($denda->denda, 0, ',', '.') }}</strong></p>
            @if ($denda->catatan)
                <p class="text-gray-500 pt-2">Catatan: {{ $denda->catatan }}</p>
            @endif
        </div>
        <div class="flex gap-2 pt-2">
            <a href="{{ route('admin.denda.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Kembali</a>
            <a href="{{ route('admin.denda.tagihan', $denda->anggota_id) }}" class="px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">Lihat Tagihan Anggota</a>
            @if ($denda->denda_dibayar)
                <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-semibold rounded-lg">Lunas</span>
            @else
                <form action="{{ route('admin.denda.bayar', $denda) }}" method="POST" data-confirm="Tandai denda ini sudah lunas?">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-700 text-white text-sm font-semibold rounded-lg hover:bg-green-800">Tandai Lunas</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
