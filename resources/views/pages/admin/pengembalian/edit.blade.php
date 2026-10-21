
@extends('layouts.admin')

@section('title', 'Verifikasi Pengembalian')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div>
        <h1 class="text-xl font-bold text-[#2D251E]">Verifikasi Pengembalian</h1>
        <p class="text-sm text-[#8B7E71]">
            Centang hanya buku yang benar-benar dikembalikan. Buku yang tidak dicentang tetap berstatus dipinjam.
        </p>
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

    @if (session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @php $anggota = $items->first()->anggota; @endphp

    <div class="bg-white rounded-lg border border-[#E8E1D7] p-6">
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-[#97897B] mb-1">Peminjam</p>
                <p class="font-medium text-[#332B24]">{{ $anggota->nama }}</p>
                <p class="text-xs text-[#8B7E71]">{{ $anggota->nomor_anggota }}</p>
            </div>

            <div>
                <p class="text-sm text-[#97897B] mb-1">Kode Peminjaman</p>
                <p class="font-medium text-[#332B24]">{{ $items->first()->kode_peminjaman ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm text-[#97897B] mb-1">Jumlah Buku Aktif</p>
                <p class="font-medium text-[#332B24]">{{ $items->count() }} buku</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.pengembalian.update', $groupKey) }}"
          method="POST"
          class="bg-white rounded-lg border border-[#E8E1D7] p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="rounded-lg bg-sepia-50 border border-sepia-200 p-4">
            <p class="text-sm font-medium text-[#4A4038] mb-2">
                Terapkan tanggal kembali yang sama ke semua buku
            </p>

            <div class="flex flex-wrap items-center gap-2">
                <input type="date"
                       id="tanggalKembaliSemua"
                       value="{{ $today->format('Y-m-d') }}"
                       class="px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg focus:outline-none focus:ring-1 focus:ring-sepia-600">

                <button type="button"
                        id="terapkanSemua"
                        class="px-3 py-2 text-sm font-semibold bg-sepia-700 text-white rounded-lg hover:bg-sepia-800">
                    Terapkan ke semua buku
                </button>

                <span class="text-xs text-[#97897B]">
                    Atau isi tanggal tiap buku satu per satu di bawah.
                </span>
            </div>
        </div>

        <div class="space-y-3">
            <h2 class="font-semibold text-[#332B24] text-sm">Buku yang dikembalikan</h2>

            @foreach ($items as $item)
                <div class="border border-[#E8E1D7] rounded-lg p-4 space-y-3 bg-[#FAF9F7]">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="transaksi_ids[]"
                            value="{{ $item->id }}"
                            class="mt-1 rounded border-[#DDD5C9] text-sepia-700"
                            {{ in_array($item->id, old('transaksi_ids', [])) ? 'checked' : '' }}
                        >

                        <span>
                            <span class="block font-medium text-[#332B24]">
                                {{ $item->buku->judul }}
                            </span>
                            <span class="text-xs text-[#97897B]">
                                Centang jika buku ini dikembalikan sekarang.
                            </span>
                        </span>
                    </label>

                    <p class="text-xs text-[#8B7E71]">
                        Pinjam: {{ $item->tanggal_pinjam->format('d/m/Y') }}
                        Â· Rencana: {{ $item->tanggalRencanaLabel() }}

                        @if (! $item->tanggal_kembali_rencana)
                            <span class="text-[#8B7E71]">(tidak ada rencana)</span>
                        @endif
                    </p>

                    <div class="grid md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-[#7C6F62] mb-1">
                                Tanggal kembali
                            </label>

                            <input
                                type="date"
                                name="tanggal_kembali_aktual[{{ $item->id }}]"
                                class="tanggalKembali per-buku w-full px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg"
                                value="{{ old('tanggal_kembali_aktual.' . $item->id, $today->format('Y-m-d')) }}"
                            >

                            @error('tanggal_kembali_aktual.' . $item->id)
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#7C6F62] mb-1">
                                Kondisi <span class="text-red-600">*</span>
                            </label>

                            <select
                                name="kondisi[{{ $item->id }}]"
                                class="w-full px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg">
                                <option value="baik" {{ old('kondisi.' . $item->id, 'baik') === 'baik' ? 'selected' : '' }}>
                                    Baik
                                </option>
                                <option value="rusak" {{ old('kondisi.' . $item->id) === 'rusak' ? 'selected' : '' }}>
                                    Rusak
                                </option>
                                <option value="hilang" {{ old('kondisi.' . $item->id) === 'hilang' ? 'selected' : '' }}>
                                    Hilang
                                </option>
                            </select>

                            @error('kondisi.' . $item->id)
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#7C6F62] mb-1">
                                Catatan (opsional)
                            </label>

                            <input
                                type="text"
                                name="catatan[{{ $item->id }}]"
                                value="{{ old('catatan.' . $item->id) }}"
                                class="w-full px-3 py-2 text-sm border border-[#DDD5C9] rounded-lg focus:outline-none focus:ring-1 focus:ring-sepia-600"
                            >
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex gap-3 pt-2 border-t border-[#F0EAE1]">
            <button type="submit"
                    class="px-4 py-2 bg-green-700 text-white text-sm font-semibold rounded-lg hover:bg-green-800">
                Proses Buku Terpilih
            </button>

            <a href="{{ route('admin.pengembalian.index', ['tab' => 'pengajuan']) }}"
               class="px-4 py-2 bg-[#F7F4EF] text-[#4A4038] text-sm font-semibold rounded-lg hover:bg-[#F0EAE1]">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    (function () {
        const semua = document.getElementById('tanggalKembaliSemua');
        const tombol = document.getElementById('terapkanSemua');

        if (!semua || !tombol) return;

        tombol.addEventListener('click', function () {
            document.querySelectorAll('.per-buku').forEach(function (input) {
                input.value = semua.value;
            });
        });
    })();
</script>
@endsection

