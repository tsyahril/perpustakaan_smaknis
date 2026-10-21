@extends('layouts.admin')

@section('title', 'Dashboard Guru')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-[#2D251E]">Dashboard Guru</h1>
        <p class="text-sm text-[#8B7E71]">Selamat datang, {{ $user->name }}.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-3">
        <div class="bg-white rounded-lg border border-[#E8E1D7] p-4">
            <p class="text-xs text-[#97897B]">Status Akses</p>
            @if ($user->isPustakawan())
                <p class="mt-1 text-lg font-bold text-sepia-700">Pustakawan</p>
                <p class="text-xs text-[#97897B] mt-1">Bisa mengelola data perpustakaan, peminjaman, pengembalian, dan denda.</p>
                <a href="{{ route('admin.dashboard') }}" class="inline-block mt-3 px-4 py-2 text-sm font-semibold text-white bg-sepia-700 rounded-lg hover:bg-sepia-800">Ke Panel Perpustakaan</a>
            @else
                <p class="mt-1 text-lg font-bold text-[#332B24]">Guru Biasa</p>
                <p class="text-xs text-[#97897B] mt-1">Belum memiliki akses kelola. Minta admin mengaktifkan akses pustakawan bila diperlukan.</p>
            @endif
        </div>
        <div class="bg-white rounded-lg border border-[#E8E1D7] p-4">
            <p class="text-xs text-[#97897B]">Koleksi Buku</p>
            <p class="mt-1 text-2xl font-bold text-[#332B24]">{{ $totalBuku }}</p>
            <p class="text-xs text-[#8B7E71] mt-1">Total judul terdaftar di perpustakaan.</p>
        </div>
    </div>
</div>
@endsection

