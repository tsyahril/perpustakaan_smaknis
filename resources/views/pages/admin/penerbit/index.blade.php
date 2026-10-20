@extends('layouts.admin')

@section('title', 'Data Penerbit')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-slate-100">Data Penerbit</h1>
        <p class="text-sm text-slate-400">Daftar penerbit dari koleksi buku. Penerbit baru otomatis tercatat saat tambah buku.</p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="GET" action="{{ route('admin.penerbit.index') }}" class="flex flex-col md:flex-row gap-2 p-4 border-b border-gray-100">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari penerbit..."
                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Cari</button>
            @if ($search)
                <a href="{{ route('admin.penerbit.index') }}" class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Nama Penerbit</th>
                        <th class="px-4 py-3 text-center font-semibold">Jumlah Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($penerbits as $i => $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $penerbits->firstItem() + $i }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $row->penerbit }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $row->jumlah_buku }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.buku.index', ['search' => $row->penerbit]) }}" class="text-sm text-blue-700 hover:underline">Lihat Buku</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-400">Belum ada data penerbit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($penerbits->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $penerbits->links() }}</div>
        @endif
    </div>
</div>
@endsection

