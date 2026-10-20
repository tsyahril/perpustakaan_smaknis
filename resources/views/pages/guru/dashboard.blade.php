@extends('layouts.admin')

@section('title', 'Dashboard Guru')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-slate-100">Dashboard Guru</h1>
        <p class="text-sm text-slate-400">Selamat datang, {{ $user->name }}.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-3">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Status Akses</p>
            @if ($user->isPustakawan())
                <p class="mt-1 text-lg font-bold text-purple-700">Pustakawan</p>
                <p class="text-xs text-gray-500 mt-1">Bisa mengelola data perpustakaan, peminjaman, pengembalian, dan denda.</p>
                <a href="{{ route('admin.dashboard') }}" class="inline-block mt-3 px-4 py-2 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">Ke Panel Perpustakaan</a>
            @else
                <p class="mt-1 text-lg font-bold text-gray-800">Guru Biasa</p>
                <p class="text-xs text-gray-500 mt-1">Belum memiliki akses kelola. Minta admin mengaktifkan akses pustakawan bila diperlukan.</p>
            @endif
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Koleksi Buku</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalBuku }}</p>
            <p class="text-xs text-gray-400 mt-1">Total judul terdaftar di perpustakaan.</p>
        </div>
    </div>
</div>
@endsection

