@extends('layouts.admin')

@section('title', $tab === 'setting' ? 'Setting Denda' : 'Data Denda')

@section('content')
@php
    $bisaKelola = auth()->user()?->canManageLibrary();
    $isAdmin = auth()->user()?->isAdmin();
    $urlTagihan = route('admin.denda.index');
    $urlSetting = route('admin.denda.index', ['tab' => 'setting']);
@endphp

<div class="space-y-5">

    {{-- Judul halaman --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#2D251E]">Denda</h1>
            <p class="mt-1 text-sm text-[#8B7E71]">
                Data denda, tagihan anggota, dan pengaturan tarif dalam satu halaman.
            </p>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Tab: Tagihan | Setting Denda --}}
    <div class="flex flex-wrap items-end gap-6 border-b border-[#E8E1D7]">
        <a href="{{ $urlTagihan }}"
           class="inline-flex items-center gap-2 -mb-px border-b-2 pb-3 text-sm transition
                  {{ $tab !== 'setting'
                      ? 'border-sepia-600 font-semibold text-sepia-700'
                      : 'border-transparent text-[#8B7E71] hover:border-[#DDD5C9] hover:text-[#2D251E]' }}">
            Data &amp; Tagihan Denda
        </a>

        @if ($isAdmin)
            <a href="{{ $urlSetting }}"
               class="inline-flex items-center gap-2 -mb-px border-b-2 pb-3 text-sm transition
                      {{ $tab === 'setting'
                          ? 'border-sepia-600 font-semibold text-sepia-700'
                          : 'border-transparent text-[#8B7E71] hover:border-[#DDD5C9] hover:text-[#2D251E]' }}">
                Setting Denda
            </a>
        @endif
    </div>

    @if ($tab === 'setting')

        {{-- ================= TAB SETTING DENDA ================= --}}
        <div class="max-w-3xl space-y-5">

            @if ($errors->any())
                <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-700">
                    <p class="mb-2 font-semibold">Pengaturan belum berhasil disimpan:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.setting-denda.update') }}" method="POST"
                  class="overflow-hidden rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] shadow-sm">
                @csrf
                @method('PUT')

                <div class="border-b border-[#E8E1D7] bg-[#F7F4EF] px-5 py-4 sm:px-6">
                    <h2 class="text-base font-semibold text-[#2D251E]">Tarif Denda Perpustakaan</h2>
                    <p class="mt-1 text-sm text-[#8B7E71]">Masukkan nominal dalam rupiah tanpa titik atau tanda koma.</p>
                </div>

                <div class="space-y-5 p-5 sm:p-6">

                    {{-- Denda terlambat --}}
                    <div class="rounded-xl border border-[#E8E1D7]/80 bg-[#F7F4EF]/70 p-4 sm:p-5">
                        <div class="mb-3 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sepia-500/10 text-sepia-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                    <path stroke-linecap="round" stroke-width="1.8" d="M12 7v5l3 2"/>
                                </svg>
                            </div>
                            <div>
                                <label for="denda_terlambat_per_hari" class="block text-sm font-semibold text-[#2D251E]">
                                    Denda terlambat per hari <span class="text-red-500">*</span>
                                </label>
                                <p class="mt-1 text-xs leading-5 text-[#8B7E71]">Denda dihitung berdasarkan jumlah hari keterlambatan.</p>
                            </div>
                        </div>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-[#8B7E71]">Rp</span>
                            <input id="denda_terlambat_per_hari" type="number" name="denda_terlambat_per_hari"
                                min="0" max="1000000"
                                value="{{ old('denda_terlambat_per_hari', $setting->denda_terlambat_per_hari) }}"
                                required
                                class="w-full rounded-lg border border-[#B9AE9E] bg-[#FFFFFF] py-3 pl-11 pr-4 text-sm font-semibold text-[#2D251E] outline-none transition placeholder:text-[#97897B] focus:border-sepia-500 focus:ring-2 focus:ring-sepia-600/20">
                        </div>
                        <p class="mt-2 text-xs text-[#97897B]">Batas nominal: Rp1.000.000 per hari.</p>
                    </div>

                    {{-- Denda buku rusak --}}
                    <div class="rounded-xl border border-[#E8E1D7]/80 bg-[#F7F4EF]/70 p-4 sm:p-5">
                        <div class="mb-3 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sepia-500/10 text-sepia-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l9 16H3l9-16z"/>
                                    <path stroke-linecap="round" stroke-width="1.8" d="M12 9v4m0 3h.01"/>
                                </svg>
                            </div>
                            <div>
                                <label for="denda_rusak" class="block text-sm font-semibold text-[#2D251E]">
                                    Denda buku rusak <span class="text-red-500">*</span>
                                </label>
                                <p class="mt-1 text-xs leading-5 text-[#8B7E71]">Tarif yang dikenakan saat buku dikembalikan dalam kondisi rusak.</p>
                            </div>
                        </div>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-[#8B7E71]">Rp</span>
                            <input id="denda_rusak" type="number" name="denda_rusak"
                                min="0" max="10000000"
                                value="{{ old('denda_rusak', $setting->denda_rusak) }}"
                                required
                                class="w-full rounded-lg border border-[#B9AE9E] bg-[#FFFFFF] py-3 pl-11 pr-4 text-sm font-semibold text-[#2D251E] outline-none transition placeholder:text-[#97897B] focus:border-sepia-500 focus:ring-2 focus:ring-sepia-600/20">
                        </div>
                        <p class="mt-2 text-xs text-[#97897B]">Batas nominal: Rp10.000.000.</p>
                    </div>

                    {{-- Denda buku hilang --}}
                    <div class="rounded-xl border border-[#E8E1D7]/80 bg-[#F7F4EF]/70 p-4 sm:p-5">
                        <div class="mb-3 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sepia-500/10 text-sepia-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.9L2.7 17a2 2 0 001.7 3h15.2a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/>
                                </svg>
                            </div>
                            <div>
                                <label for="denda_hilang" class="block text-sm font-semibold text-[#2D251E]">
                                    Denda buku hilang <span class="text-red-500">*</span>
                                </label>
                                <p class="mt-1 text-xs leading-5 text-[#8B7E71]">Tarif yang dikenakan saat buku dinyatakan hilang.</p>
                            </div>
                        </div>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-[#8B7E71]">Rp</span>
                            <input id="denda_hilang" type="number" name="denda_hilang"
                                min="0" max="10000000"
                                value="{{ old('denda_hilang', $setting->denda_hilang) }}"
                                required
                                class="w-full rounded-lg border border-[#B9AE9E] bg-[#FFFFFF] py-3 pl-11 pr-4 text-sm font-semibold text-[#2D251E] outline-none transition placeholder:text-[#97897B] focus:border-sepia-500 focus:ring-2 focus:ring-sepia-600/20">
                        </div>
                        <p class="mt-2 text-xs text-[#97897B]">Batas nominal: Rp10.000.000. Denda keterlambatan juga dapat berlaku jika ada.</p>
                    </div>

                </div>

                {{-- Tombol aksi --}}
                <div class="flex flex-col-reverse gap-3 border-t border-[#E8E1D7] bg-[#F7F4EF]/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-6">
                    <button type="submit"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-sepia-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sepia-950/30 transition hover:bg-sepia-700 focus:outline-none focus:ring-2 focus:ring-sepia-600 focus:ring-offset-2 focus:ring-offset-[#F7F4EF]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>

            <p class="text-xs leading-5 text-[#97897B]">
                <span class="font-semibold text-[#8B7E71]">Perhatian:</span>
                perubahan tarif dapat menghitung ulang denda pada riwayat yang sudah ada sesuai mekanisme aplikasi.
                Periksa kembali Data Denda setelah menyimpan.
            </p>
        </div>

    @else

        {{-- ================= TAB DATA & TAGIHAN ================= --}}

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

            <div class="bg-[#FFFFFF] rounded-2xl border border-[#E8E1D7] p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-[#8B7E71]">Total Denda Keseluruhan</p>
                        <p class="text-2xl font-bold text-[#2D251E] mt-2">
                            Rp{{ number_format($grandTotal, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-sepia-500/10 text-sepia-700
                                flex items-center justify-center text-xl font-bold">
                        Rp
                    </div>
                </div>
            </div>

            <div class="bg-[#FFFFFF] rounded-2xl border border-[#E8E1D7] p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-[#8B7E71]">Jumlah Transaksi Denda</p>
                        <p class="text-2xl font-bold text-[#2D251E] mt-2">
                            {{ number_format($dendas->total(), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-sepia-500/10 text-sepia-700
                                flex items-center justify-center text-xl">
                        #
                    </div>
                </div>
            </div>

            <div class="bg-[#FFFFFF] rounded-2xl border border-[#E8E1D7] p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-[#8B7E71]">Rata-rata Denda</p>
                        <p class="text-2xl font-bold text-[#2D251E] mt-2">
                            @if ($dendas->total() > 0)
                                Rp{{ number_format($grandTotal / $dendas->total(), 0, ',', '.') }}
                            @else
                                Rp0
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-sepia-500/10 text-sepia-700
                                flex items-center justify-center text-xl">
                        %
                    </div>
                </div>
            </div>

        </div>

        {{-- Tagihan per anggota --}}
        <div class="bg-[#FFFFFF] rounded-2xl border border-[#E8E1D7] shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-[#E8E1D7]">
                <h2 class="text-base font-bold text-[#2D251E]">
                    Tagihan per Anggota
                </h2>
                <p class="text-sm text-[#8B7E71] mt-1">
                    Pilih nama anggota untuk melihat riwayat seluruh tagihannya.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-[#F7F4EF] text-[#8B7E71] text-xs uppercase tracking-wide">
                            <th class="px-5 py-4 text-left font-semibold">Anggota</th>
                            <th class="px-5 py-4 text-center font-semibold">Kasus</th>
                            <th class="px-5 py-4 text-right font-semibold">Terlambat</th>
                            <th class="px-5 py-4 text-right font-semibold">Kondisi</th>
                            <th class="px-5 py-4 text-right font-semibold">Total Tagihan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#EFE9E1]">
                        @forelse ($tagihan as $t)
                            <tr class="hover:bg-[#FBF9F6] transition">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.denda.tagihan', $t->anggota_id) }}"
                                       class="font-semibold text-[#2D251E] hover:text-sepia-700">
                                        {{ $t->anggota->nama ?? '-' }}
                                    </a>
                                    <p class="text-xs text-[#8B7E71] mt-1">
                                        {{ $t->anggota->nomor_anggota ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-center text-[#6B5E52]">
                                    {{ $t->jumlah }}
                                </td>

                                <td class="px-5 py-4 text-right text-[#6B5E52] whitespace-nowrap">
                                    Rp{{ number_format($t->total_terlambat, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-right text-[#6B5E52] whitespace-nowrap">
                                    Rp{{ number_format($t->total_kondisi, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-right font-bold text-sepia-700 whitespace-nowrap">
                                    Rp{{ number_format($t->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-[#8B7E71]">
                                    Belum ada tagihan anggota.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Tabel utama --}}
        <div class="bg-[#FFFFFF] rounded-2xl border border-[#E8E1D7] shadow-sm overflow-hidden">

            {{-- Judul tabel --}}
            <div class="px-5 py-4 border-b border-[#E8E1D7]">
                <h2 class="text-base font-bold text-[#2D251E]">
                    Daftar Transaksi Denda
                </h2>
                <p class="text-sm text-[#8B7E71] mt-1">
                    Tekan tombol Detail untuk melihat rincian dan pembayaran denda.
                </p>
            </div>

            {{-- Filter dan pencarian --}}
            <form method="GET"
                  action="{{ $urlTagihan }}"
                  class="p-4 bg-[#FBF9F6] border-b border-[#E8E1D7]">
                <input type="hidden" name="tab" value="tagihan">

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari anggota atau judul buku..."
                        class="w-full px-4 py-2.5 text-sm bg-[#FFFFFF] border border-[#B9AE9E]
                               rounded-xl text-[#2D251E] placeholder:text-[#97897B] focus:outline-none focus:ring-2 focus:ring-sepia-600/30">

                    <select
                        name="jenis"
                        class="w-full px-4 py-2.5 text-sm bg-[#FFFFFF] border border-[#B9AE9E]
                               rounded-xl text-[#2D251E] focus:outline-none focus:ring-2 focus:ring-sepia-600/30">

                        <option value="">Semua jenis denda</option>
                        <option value="terlambat" {{ $jenis === 'terlambat' ? 'selected' : '' }}>
                            Terlambat
                        </option>
                        <option value="rusak" {{ $jenis === 'rusak' ? 'selected' : '' }}>
                            Buku rusak
                        </option>
                        <option value="hilang" {{ $jenis === 'hilang' ? 'selected' : '' }}>
                            Buku hilang
                        </option>
                    </select>

                    <button
                        type="submit"
                        class="w-full px-4 py-2.5 bg-sepia-600 text-white text-sm
                               font-semibold rounded-xl hover:bg-sepia-700 transition">
                        Terapkan Filter
                    </button>

                    @if ($search || $jenis !== '')
                        <a
                            href="{{ $urlTagihan }}"
                            class="w-full px-4 py-2.5 bg-[#FFFFFF] border border-[#B9AE9E]
                                   text-[#6B5E52] text-sm font-semibold rounded-xl
                                   hover:bg-[#F0EAE1] transition text-center">
                            Reset Filter
                        </a>
                    @endif

                </div>
            </form>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">

                    <thead>
                        <tr class="bg-[#F7F4EF] text-[#8B7E71] text-xs uppercase tracking-wide">
                            <th class="px-5 py-4 text-left font-semibold w-16">No.</th>
                            <th class="px-5 py-4 text-left font-semibold">Anggota</th>
                            <th class="px-5 py-4 text-left font-semibold">Buku</th>
                            <th class="px-5 py-4 text-center font-semibold">Kondisi / Telat</th>
                            <th class="px-5 py-4 text-center font-semibold w-32">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#EFE9E1]">

                        @forelse ($dendas as $i => $denda)
                            <tr class="hover:bg-[#FBF9F6] transition">

                                {{-- Nomor --}}
                                <td class="px-5 py-4 text-[#8B7E71] font-medium">
                                    {{ $dendas->firstItem() + $i }}
                                </td>

                                {{-- Anggota --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-[#2D251E]">
                                        {{ $denda->anggota->nama ?? '-' }}
                                    </p>
                                    <p class="text-xs text-[#8B7E71] mt-1">
                                        {{ $denda->anggota->nomor_anggota ?? '-' }}
                                    </p>
                                </td>

                                {{-- Buku --}}
                                <td class="px-5 py-4">
                                    <p class="font-medium text-[#2D251E]">
                                        {{ $denda->buku->judul ?? '-' }}
                                    </p>
                                    <p class="text-xs text-[#8B7E71] mt-1">
                                        Kembali:
                                        {{ $denda->tanggal_kembali_aktual
                                            ? $denda->tanggal_kembali_aktual->format('d/m/Y')
                                            : '-' }}
                                    </p>
                                </td>

                                {{-- Kondisi dan keterlambatan --}}
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $kondisi = strtolower($denda->kondisi ?? 'baik');
                                    @endphp

                                    <div class="flex flex-col items-center gap-2">

                                        <span class="inline-flex items-center px-3 py-1 rounded-full
                                            text-xs font-semibold
                                            {{ $kondisi === 'baik'
                                                ? 'bg-emerald-50 text-emerald-700'
                                                : ($kondisi === 'rusak'
                                                    ? 'bg-amber-50 text-amber-700'
                                                    : ($kondisi === 'hilang'
                                                        ? 'bg-red-50 text-red-700'
                                                        : 'bg-[#F0EAE1] text-[#6B5E52]')) }}">
                                            {{ ucfirst($kondisi) }}
                                        </span>

                                        @if (($denda->hari_terlambat ?? 0) > 0)
                                            <span class="text-xs text-amber-700 font-medium">
                                                {{ $denda->hari_terlambat }} hari terlambat
                                            </span>
                                        @else
                                            <span class="text-xs text-[#8B7E71]">
                                                Tidak terlambat
                                            </span>
                                        @endif

                                    </div>
                                </td>

                                {{-- Aksi Detail --}}
                                <td class="px-5 py-4 text-center">
                                    <a
                                        href="{{ route('admin.denda.show', $denda) }}"
                                        class="inline-flex items-center justify-center gap-2
                                               px-4 py-2 rounded-xl bg-sepia-50 text-sepia-700
                                               border border-sepia-200 text-xs font-bold
                                               hover:bg-sepia-600 hover:text-white
                                               hover:border-sepia-600 transition whitespace-nowrap">
                                        Detail
                                        <span aria-hidden="true">&rarr;</span>
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-12 h-12 rounded-full bg-[#F0EAE1]
                                                    flex items-center justify-center text-[#8B7E71] text-xl">
                                            !
                                        </div>
                                        <p class="font-semibold text-[#6B5E52]">
                                            Data denda tidak ditemukan
                                        </p>
                                        <p class="text-xs text-[#8B7E71]">
                                            Coba ubah kata pencarian atau filter.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($dendas->hasPages())
                <div class="px-5 py-4 border-t border-[#E8E1D7]
                            flex flex-col sm:flex-row sm:items-center
                            sm:justify-between gap-3">

                    <p class="text-xs text-[#8B7E71]">
                        Menampilkan {{ $dendas->firstItem() ?? 0 }}
                        sampai {{ $dendas->lastItem() ?? 0 }}
                        dari {{ $dendas->total() }} transaksi
                    </p>

                    <div>
                        {{ $dendas->links() }}
                    </div>

                </div>
            @endif

        </div>

    @endif

</div>
@endsection
