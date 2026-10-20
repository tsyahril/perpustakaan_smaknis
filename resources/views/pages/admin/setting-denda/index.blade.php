@extends('layouts.admin')

@section('title', 'Setting Denda')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10">
            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-2.21 0-4 1.12-4 2.5S9.79 13 12 13s4 1.12 4 2.5S14.21 18 12 18m0-10c1.4 0 2.65.45 3.4 1.15M12 8V6m0 2v10m0 0v2m9-8a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <h1 class="text-2xl font-bold tracking-tight text-slate-100">Setting Denda</h1>
            <p class="mt-1 text-sm leading-6 text-slate-400">
                Atur tarif denda perpustakaan untuk keterlambatan, buku rusak, dan buku hilang.
                Perubahan tarif juga dapat memengaruhi perhitungan denda yang sudah tersimpan.
                <a href="{{ route('admin.denda.index') }}" class="font-semibold text-blue-400 hover:text-blue-300 hover:underline">Lihat Data Denda</a>.
            </p>
        </div>
    </div>

    {{-- Notifikasi berhasil --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Notifikasi validasi --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
            <p class="mb-2 font-semibold">Pengaturan belum berhasil disimpan:</p>
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form pengaturan --}}
    <form action="{{ route('admin.setting-denda.update') }}" method="POST"
          class="overflow-hidden rounded-2xl border border-slate-700 bg-[#0b1d32] shadow-xl shadow-black/10">
        @csrf
        @method('PUT')

        <div class="border-b border-slate-700 bg-[#102640] px-5 py-4 sm:px-6">
            <h2 class="text-base font-semibold text-slate-100">Tarif Denda Perpustakaan</h2>
            <p class="mt-1 text-sm text-slate-400">Masukkan nominal dalam rupiah tanpa titik atau tanda koma.</p>
        </div>

        <div class="space-y-5 p-5 sm:p-6">

            {{-- Denda terlambat --}}
            <div class="rounded-xl border border-slate-700/80 bg-[#102640]/70 p-4 sm:p-5">
                <div class="mb-3 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                            <path stroke-linecap="round" stroke-width="1.8" d="M12 7v5l3 2"/>
                        </svg>
                    </div>
                    <div>
                        <label for="denda_terlambat_per_hari" class="block text-sm font-semibold text-slate-100">
                            Denda terlambat per hari <span class="text-red-400">*</span>
                        </label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">Denda dihitung berdasarkan jumlah hari keterlambatan.</p>
                    </div>
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-slate-400">Rp</span>
                    <input id="denda_terlambat_per_hari" type="number" name="denda_terlambat_per_hari"
                        min="0" max="1000000"
                        value="{{ old('denda_terlambat_per_hari', $setting->denda_terlambat_per_hari) }}"
                        required
                        class="w-full rounded-lg border border-slate-600 bg-[#071426] py-3 pl-11 pr-4 text-sm font-semibold text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <p class="mt-2 text-xs text-slate-500">Batas nominal: Rp1.000.000 per hari.</p>
            </div>

            {{-- Denda buku rusak --}}
            <div class="rounded-xl border border-slate-700/80 bg-[#102640]/70 p-4 sm:p-5">
                <div class="mb-3 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500/10 text-orange-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l9 16H3l9-16z"/>
                            <path stroke-linecap="round" stroke-width="1.8" d="M12 9v4m0 3h.01"/>
                        </svg>
                    </div>
                    <div>
                        <label for="denda_rusak" class="block text-sm font-semibold text-slate-100">
                            Denda buku rusak <span class="text-red-400">*</span>
                        </label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">Tarif yang dikenakan saat buku dikembalikan dalam kondisi rusak.</p>
                    </div>
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-slate-400">Rp</span>
                    <input id="denda_rusak" type="number" name="denda_rusak"
                        min="0" max="10000000"
                        value="{{ old('denda_rusak', $setting->denda_rusak) }}"
                        required
                        class="w-full rounded-lg border border-slate-600 bg-[#071426] py-3 pl-11 pr-4 text-sm font-semibold text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <p class="mt-2 text-xs text-slate-500">Batas nominal: Rp10.000.000.</p>
            </div>

            {{-- Denda buku hilang --}}
            <div class="rounded-xl border border-slate-700/80 bg-[#102640]/70 p-4 sm:p-5">
                <div class="mb-3 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-500/10 text-rose-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.9L2.7 17a2 2 0 001.7 3h15.2a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/>
                        </svg>
                    </div>
                    <div>
                        <label for="denda_hilang" class="block text-sm font-semibold text-slate-100">
                            Denda buku hilang <span class="text-red-400">*</span>
                        </label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">Tarif yang dikenakan saat buku dinyatakan hilang.</p>
                    </div>
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-slate-400">Rp</span>
                    <input id="denda_hilang" type="number" name="denda_hilang"
                        min="0" max="10000000"
                        value="{{ old('denda_hilang', $setting->denda_hilang) }}"
                        required
                        class="w-full rounded-lg border border-slate-600 bg-[#071426] py-3 pl-11 pr-4 text-sm font-semibold text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <p class="mt-2 text-xs text-slate-500">Batas nominal: Rp10.000.000. Denda keterlambatan juga dapat berlaku jika ada.</p>
            </div>

        </div>

        {{-- Tombol aksi --}}
        <div class="flex flex-col-reverse gap-3 border-t border-slate-700 bg-[#102640]/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <a href="{{ route('admin.denda.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-slate-600 bg-[#0b1d32] px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:border-slate-500 hover:bg-slate-800 hover:text-white">
                Lihat Data Denda
            </a>
            <button type="submit"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-[#102640]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>

    <p class="text-xs leading-5 text-slate-500">
        <span class="font-semibold text-slate-400">Perhatian:</span>
        perubahan tarif dapat menghitung ulang denda pada riwayat yang sudah ada sesuai mekanisme aplikasi.
        Periksa kembali Data Denda setelah menyimpan.
    </p>
</div>
@endsection
