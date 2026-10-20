@extends('layouts.admin')

@section('title', 'Request Peminjaman')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-slate-100">Request Peminjaman</h1>
        <p class="text-sm text-slate-400">Pengajuan pinjam yang menunggu persetujuan pustakawan. Pilih nama peminjam dulu, lalu daftar pengajuannya. Yang sudah disetujui ada di <a href="{{ route('admin.peminjaman.index') }}" class="text-blue-700 hover:underline font-medium">Data Peminjaman</a>.</p>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-3 gap-3">
        @foreach (['diajukan' => ['Menunggu', 'bg-yellow-50 border-yellow-200 text-yellow-800'], 'disetujui' => ['Disetujui', 'bg-green-50 border-green-200 text-green-800'], 'ditolak' => ['Ditolak', 'bg-red-50 border-red-200 text-red-800']] as $key => [$label, $cls])
            <a href="{{ route('admin.request-peminjaman.index', ['status' => $key, 'nama_peminjam' => $namaPeminjam]) }}"
                class="bg-[#0b1d32] rounded-lg border p-4 hover:border-blue-400 {{ $status === $key ? 'border-blue-600' : 'border-slate-700' }}">
                <p class="text-xs text-slate-400">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-100">{{ $counts[$key] ?? 0 }}</p>
            </a>
        @endforeach
    </div>

    <div class="bg-[#0b1d32] rounded-lg border border-slate-700 overflow-hidden">
        <form method="GET" action="{{ route('admin.request-peminjaman.index') }}" class="grid md:grid-cols-4 gap-2 p-4 border-b border-slate-700/80">
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Nama Peminjam</label>
                <select name="nama_peminjam" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg text-slate-100 bg-[#071426] border-slate-600 focus:outline-none focus:ring-2 focus:ring-sky-400/30">
                    <option value="" class="bg-[#0b1d32] text-slate-100">-- Semua peminjam --</option>
                    @foreach ($daftarPeminjam as $nama)
                        <option value="{{ $nama }}" {{ $namaPeminjam === $nama ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Status Pengajuan</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                    <option value="diajukan" {{ $status === 'diajukan' ? 'selected' : '' }}>Menunggu persetujuan</option>
                    <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="semua" {{ $status === 'semua' ? 'selected' : '' }}>Semua pengajuan</option>
                </select>
            </div>
            <div class="self-end flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Tampilkan</button>
            </div>
            <div class="self-end flex gap-2">
                <a href="{{ route('admin.request-peminjaman.index') }}" class="px-4 py-2 text-sm font-semibold bg-slate-800 text-slate-300 rounded-lg hover:bg-slate-700 text-center">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-[#142c47] text-slate-400 text-xs border-b border-slate-700/80">
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Peminjam</th>
                        <th class="px-4 py-3 text-left font-semibold">Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Pinjam / Rencana</th>
                        <th class="px-4 py-3 text-center font-semibold">Status</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/70">
                    @forelse ($requests as $i => $g)
                        <tr class="hover:bg-[#142c47]">
                            <td class="px-4 py-3 text-slate-400 align-top">{{ $requests->firstItem() + $i }}</td>
                            <td class="px-4 py-3 align-top">
                                <p class="font-medium text-slate-100">{{ $g->anggota->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $g->anggota->nomor_anggota }}{{ $g->anggota->kelas ? ' Â· ' . $g->anggota->kelas->nama : '' }}</p>
                                @if ($g->kode)
                                    <p class="text-xs font-mono text-blue-700">{{ $g->kode }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                @foreach ($g->items as $item)
                                    <p class="text-sm text-slate-100">â€¢ {{ $item->buku->judul }}</p>
                                    <p class="text-xs text-slate-400">
                                        Rencana: {{ $item->tanggalRencanaLabel() }}
                                        @if (! $item->tanggal_kembali_rencana)
                                            <span class="text-slate-400">(tanpa rencana)</span>
                                        @endif
                                    </p>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 text-center text-slate-300 text-xs align-top">
                                {{ $g->tanggal_pinjam->format('d/m/Y') }}
                                <span class="block text-slate-400">{{ $g->jumlah_buku }} buku</span>
                            </td>
                            <td class="px-4 py-3 text-center align-top">
                                @if ($g->status_persetujuan === 'diajukan')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Diajukan</span>
                                @elseif ($g->status_persetujuan === 'disetujui')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                @endif
                                @if ($g->catatan)
                                    <span class="block mt-1 text-xs text-slate-400">{{ $g->catatan }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center align-top">
                                <div class="inline-flex items-center justify-center gap-2">
                                    @if ($g->status_persetujuan === 'diajukan')
                                        <form action="{{ route('admin.request-peminjaman.approve', $g->key) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-white bg-green-700 px-2.5 py-1 rounded-lg hover:bg-green-800">Setujui</button>
                                        </form>
                                        <form action="{{ route('admin.request-peminjaman.reject', $g->key) }}" method="POST" class="inline" onsubmit="return confirm('Tolak pengajuan ini? Semua buku akan ditolak dan stok dikembalikan.')">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-white bg-red-600 px-2.5 py-1 rounded-lg hover:bg-red-700">Tolak</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-500">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                                Tidak ada pengajuan{{ $namaPeminjam !== '' ? ' dari peminjam "' . $namaPeminjam . '"' : '' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->hasPages())
            <div class="p-4 border-t border-slate-700/80">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
@endsection

