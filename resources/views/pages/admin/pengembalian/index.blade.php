@extends('layouts.admin')

@section('title', 'Riwayat Pengembalian')

@section('content')
<div class="space-y-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Pengembalian (Riwayat)</h1>
        <p class="text-sm text-gray-500">Diurutkan dari yang terbaru. Dikelompokkan per kali pinjam, klik <strong>Detail</strong> untuk melihat rincian buku, tanggal, denda, dan kondisi. Yang masih dipinjam ada di <a href="{{ route('admin.request-pengembalian.index') }}" class="text-blue-700 hover:underline font-medium">Request Pengembalian</a>.</p>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <form method="GET" action="{{ route('admin.pengembalian.index') }}" class="grid md:grid-cols-4 gap-2 p-4 border-b border-gray-100">
            <div class="relative">
                <input type="text" id="namaPeminjamKeyword" autocomplete="off" placeholder="Ketik nama peminjam..."
                    value="{{ $namaPeminjam }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                <input type="hidden" name="nama_peminjam" id="namaPeminjamHidden" value="{{ $namaPeminjam }}">
                <div id="namaPeminjamDropdown" class="hidden absolute z-10 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg">
                    <button type="button" data-nama="" class="peminjam-opsi block w-full px-3 py-2 text-sm text-left text-gray-500 hover:bg-blue-50">— Semua peminjam —</button>
                    @foreach ($daftarPeminjam as $p)
                        <button type="button" data-nama="{{ $p->nama }}" class="peminjam-opsi block w-full px-3 py-2 text-sm text-left text-gray-700 hover:bg-blue-50">
                            <span class="font-medium">{{ $p->nama }}</span>
                            <span class="block text-xs text-gray-400">{{ $p->nomor_anggota }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="relative">
                <input type="text" id="namaBukuKeyword" autocomplete="off" placeholder="Ketik judul buku..."
                    value="{{ $namaBuku }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-600">
                <input type="hidden" name="nama_buku" id="namaBukuHidden" value="{{ $namaBuku }}">
                <div id="namaBukuDropdown" class="hidden absolute z-10 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg">
                    <button type="button" data-judul="" class="buku-opsi block w-full px-3 py-2 text-sm text-left text-gray-500 hover:bg-blue-50">— Semua buku —</button>
                    @foreach ($daftarBuku as $b)
                        <button type="button" data-judul="{{ $b->judul }}" class="buku-opsi block w-full px-3 py-2 text-sm text-left text-gray-700 hover:bg-blue-50">
                            <span class="font-medium">{{ $b->judul }}</span>
                            <span class="block text-xs text-gray-400">Stok: {{ $b->stok }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            <select name="kondisi" class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">Semua kondisi</option>
                <option value="baik" {{ $kondisi === 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak" {{ $kondisi === 'rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="hilang" {{ $kondisi === 'hilang' ? 'selected' : '' }}>Hilang</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700">Filter</button>
                @if ($namaPeminjam !== '' || $namaBuku !== '' || $kondisi !== '')
                    <a href="{{ route('admin.pengembalian.index') }}" class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Reset</a>
                @endif
            </div>
        </form>

        @php $bisaKelola = auth()->user()?->canManageLibrary(); @endphp

        @if ($bisaKelola)
            <form method="POST" action="{{ route('admin.pengembalian.destroy-selected') }}" data-confirm="Hapus semua riwayat terpilih?">
            @csrf
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b border-gray-100">
                        @if ($bisaKelola)
                            <th class="px-3 py-3 text-center font-semibold w-10">
                                <input type="checkbox" id="pilihSemua" class="align-middle">
                            </th>
                        @endif
                        <th class="px-4 py-3 text-left font-semibold w-10">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Peminjam</th>
                        <th class="px-4 py-3 text-left font-semibold">Buku</th>
                        <th class="px-4 py-3 text-center font-semibold">Kembali</th>
                        <th class="px-4 py-3 text-center font-semibold">Denda</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pengembalians as $i => $g)
                        <tr class="hover:bg-gray-50">
                            @if ($bisaKelola)
                                <td class="px-3 py-3 text-center">
                                    <input type="checkbox" name="group_keys[]" value="{{ $g->key }}" class="pilih-baris align-middle" {{ $g->denda_belum_lunas > 0 ? 'disabled' : '' }}>
                                </td>
                            @endif
                            <td class="px-4 py-3 text-gray-400 align-top">{{ $pengembalians->firstItem() + $i }}</td>
                            <td class="px-4 py-3 align-top">
                                <p class="font-medium text-gray-800">{{ $g->anggota->nama }}</p>
                                <p class="text-xs text-gray-400">{{ $g->anggota->nomor_anggota }}{{ $g->anggota->kelas ? ' · ' . $g->anggota->kelas->nama : '' }}</p>
                                @if ($g->kode)
                                    <p class="text-xs font-mono text-blue-700">{{ $g->kode }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                @foreach ($g->items as $item)
                                    <p class="text-sm text-gray-800">• {{ $item->buku->judul }}</p>
                                    <p class="text-xs text-gray-400">{{ ucfirst($item->kondisi ?? 'baik') }}</p>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 text-xs align-top">
                                {{ $g->tanggal_kembali_aktual->format('d/m/Y') }}
                                <span class="block text-gray-400">{{ $g->jumlah_buku }} buku</span>
                            </td>
                            <td class="px-4 py-3 text-center align-top">
                                @if ($g->total_denda > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $g->denda_belum_lunas > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        Rp{{ number_format($g->total_denda, 0, ',', '.') }}
                                    </span>
                                    <span class="block mt-1 text-xs {{ $g->denda_belum_lunas > 0 ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                        {{ $g->denda_belum_lunas > 0 ? 'Belum dibayar' : 'Lunas' }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center align-top">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    <a href="{{ route('admin.pengembalian.show', $g->key) }}" class="text-sm text-blue-700 hover:underline">Detail</a>
                                    @if ($bisaKelola)
                                        @if ($g->denda_belum_lunas > 0)
                                            <span class="text-xs text-gray-300" title="Tidak bisa dihapus: denda belum dibayar">Hapus</span>
                                        @else
                                            <form action="{{ route('admin.pengembalian.destroy', $g->key) }}" method="POST" class="inline" data-confirm="Hapus riwayat pengembalian ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $bisaKelola ? 7 : 6 }}" class="px-4 py-10 text-center text-gray-400">Belum ada riwayat pengembalian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bisaKelola)
            <div class="p-4 border-t border-gray-100 flex items-center gap-3">
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">Hapus Terpilih</button>
                <span class="text-xs text-gray-400">Riwayat dengan denda belum dibayar tidak bisa dipilih / dihapus.</span>
            </div>
            </form>
        @endif

        @if ($pengembalians->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $pengembalians->links() }}</div>
        @endif
    </div>
</div>

<script>
    function combo(keywordId, hiddenId, dropdownId, opsiClass, attr) {
        const kw = document.getElementById(keywordId);
        const hd = document.getElementById(hiddenId);
        const dd = document.getElementById(dropdownId);
        const opts = Array.from(dd.querySelectorAll('.' + opsiClass));
        function filter() {
            const q = kw.value.toLowerCase().trim();
            opts.forEach(o => {
                const cocok = !q || o.getAttribute(attr).toLowerCase().includes(q);
                o.classList.toggle('hidden', !cocok);
            });
            dd.classList.remove('hidden');
        }
        kw.addEventListener('input', () => { hd.value = kw.value; filter(); });
        kw.addEventListener('focus', filter);
        opts.forEach(o => o.addEventListener('click', () => {
            hd.value = o.getAttribute(attr);
            kw.value = o.getAttribute(attr);
            dd.classList.add('hidden');
        }));
        document.addEventListener('click', e => {
            if (!kw.parentElement.contains(e.target)) dd.classList.add('hidden');
        });
    }
    combo('namaPeminjamKeyword', 'namaPeminjamHidden', 'namaPeminjamDropdown', 'peminjam-opsi', 'data-nama');
    combo('namaBukuKeyword', 'namaBukuHidden', 'namaBukuDropdown', 'buku-opsi', 'data-judul');

    const pilihSemua = document.getElementById('pilihSemua');
    if (pilihSemua) {
        pilihSemua.addEventListener('change', () => {
            document.querySelectorAll('.pilih-baris:not(:disabled)').forEach(cb => {
                cb.checked = pilihSemua.checked;
            });
        });
    }
</script>
@endsection