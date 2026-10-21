@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Heading --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#2D251E]">Dashboard Admin</h1>
            <p class="mt-1 text-sm text-[#8B7E71]">
                Ringkasan data dan aktivitas perpustakaan.
            </p>
        </div>
        <div class="inline-flex w-fit items-center gap-2 rounded-lg border border-[#E8E1D7] bg-[#FFFFFF] px-3 py-2 text-xs text-[#6B5E52]">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            Data database aktual
        </div>
    </div>

    {{-- Kartu statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] p-5 shadow-lg shadow-black/5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-[#8B7E71]">Total Peminjaman</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-[#2D251E]">{{ number_format($totalPeminjaman, 0, ',', '.') }}</p>
                    <p class="mt-2 text-xs text-[#97897B]">Transaksi yang disetujui</p>
                </div>
                <div class="rounded-xl border border-sepia-400/20 bg-sepia-500/10 p-3 text-sepia-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4.75A1.75 1.75 0 016.75 3H20v16H6.75A1.75 1.75 0 005 20.75V4.75zM5 18a2 2 0 012-2h13M9 7h7M9 11h7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] p-5 shadow-lg shadow-black/5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-[#8B7E71]">Peminjaman Aktif</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-[#2D251E]">{{ number_format($peminjamanAktif, 0, ',', '.') }}</p>
                    <p class="mt-2 text-xs text-[#97897B]">Belum dikembalikan</p>
                </div>
                <div class="rounded-xl border border-emerald-400/20 bg-emerald-500/10 p-3 text-emerald-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] p-5 shadow-lg shadow-black/5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-[#8B7E71]">Total Anggota</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-[#2D251E]">{{ number_format($totalAnggota, 0, ',', '.') }}</p>
                    <p class="mt-2 text-xs text-[#97897B]">Terdaftar di perpustakaan</p>
                </div>
                <div class="rounded-xl border border-sepia-400/20 bg-sepia-500/10 p-3 text-sepia-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm10 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] p-5 shadow-lg shadow-black/5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-[#8B7E71]">Koleksi Buku</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-[#2D251E]">{{ number_format($totalBuku, 0, ',', '.') }}</p>
                    <p class="mt-2 text-xs text-[#97897B]">Data katalog buku</p>
                </div>
                <div class="rounded-xl border border-amber-400/20 bg-amber-500/10 p-3 text-amber-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5.5A2.5 2.5 0 016.5 3H20v16H6.5A2.5 2.5 0 014 16.5v-11zM4 16.5A2.5 2.5 0 016.5 14H20M8 7h8M8 10h8"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik dan transaksi terbaru: dua panel terpisah --}}
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
    {{-- Grafik --}}
    <div class="min-w-0 overflow-hidden rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] shadow-lg shadow-black/5">
        <div class="flex flex-col gap-3 border-b border-[#E8E1D7] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-lg font-bold text-[#2D251E]">Tren Peminjaman Bulanan</h2>
                <p class="mt-1 text-sm text-[#8B7E71]">12 bulan terakhir · transaksi yang disetujui</p>
            </div>
            <div class="rounded-lg border border-sepia-400/20 bg-sepia-500/10 px-3 py-2">
                <p class="text-xs text-sepia-700">Total bulan dengan nilai tertinggi</p>
                <p class="mt-1 text-lg font-bold text-[#2D251E]">{{ number_format($nilaiTertinggi, 0, ',', '.') }} <span class="text-xs font-medium text-[#8B7E71]">peminjaman</span></p>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <div class="mb-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-[#8B7E71]">
                <span class="inline-flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-sepia-400"></span>
                    Jumlah peminjaman
                </span>
                <span>Nilai bulan tanpa peminjaman ditampilkan sebagai 0.</span>
            </div>

            <div class="w-full overflow-x-auto">
                <svg viewBox="0 0 700 220" role="img" aria-label="Grafik garis jumlah peminjaman per bulan" class="h-auto min-w-[520px] w-full">
                    @foreach ([0, 1, 2, 3] as $grid)
                        <line x1="36" y1="{{ 40 + ($grid * 42) }}" x2="664" y2="{{ 40 + ($grid * 42) }}" stroke="#e8e1d7" stroke-width="1" stroke-dasharray="4 5"/>
                    @endforeach

                    <path d="{{ $chartArea }}" fill="#8b5e34" fill-opacity="0.13"/>
                    <polyline points="{{ $chartPolyline }}" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>

                    @foreach ($chartPoints as $point)
                        <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4" fill="#93c5fd" stroke="#FFFFFF" stroke-width="2">
                            <title>{{ $point['bulan'] }}: {{ $point['total'] }} peminjaman</title>
                        </circle>
                        <text x="{{ $point['x'] }}" y="194" fill="#94a3b8" font-size="10" text-anchor="middle">{{ $point['bulan'] }}</text>
                    @endforeach

                    <text x="36" y="215" fill="#64748b" font-size="9">12 bulan terakhir</text>
                    <text x="664" y="215" fill="#64748b" font-size="9" text-anchor="end">Data per bulan</text>
                </svg>
            </div>

            {{-- Ringkasan tren --}}
            <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2">
                <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                    <div class="flex items-center gap-2 text-sm font-semibold text-emerald-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14l5-5 5 5M12 9v12"/>
                        </svg>
                        Bulan paling ramai
                    </div>
                    @if ($bulanTeramai)
                        <p class="mt-2 text-lg font-bold text-[#2D251E]">{{ $bulanTeramai['bulan'] }} {{ $bulanTeramai['tahun'] }}</p>
                        <p class="mt-1 text-sm text-[#8B7E71]">{{ number_format($bulanTeramai['total'], 0, ',', '.') }} peminjaman</p>
                    @else
                        <p class="mt-2 text-sm text-[#6B5E52]">Belum ada data peminjaman yang disetujui pada periode ini.</p>
                    @endif
                </div>

                <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4">
                    <div class="flex items-center gap-2 text-sm font-semibold text-amber-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Bulan paling sepi
                    </div>
                    @if ($bulanTersepi)
                        <p class="mt-2 text-lg font-bold text-[#2D251E]">{{ $bulanTersepi['bulan'] }} {{ $bulanTersepi['tahun'] }}</p>
                        <p class="mt-1 text-sm text-[#8B7E71]">{{ number_format($bulanTersepi['total'], 0, ',', '.') }} peminjaman</p>
                    @else
                        <p class="mt-2 text-sm text-[#6B5E52]">Belum ada data untuk dibandingkan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- Tabel transaksi terbaru --}}
    <div class="min-w-0 overflow-hidden rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] shadow-lg shadow-black/5">
        <div class="flex flex-col gap-3 border-b border-[#E8E1D7] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-lg font-bold text-[#2D251E]">Transaksi Terbaru</h2>
                <p class="mt-1 text-sm text-[#8B7E71]">Aktivitas peminjaman dan pengembalian terbaru.</p>
            </div>
            <a href="{{ route('admin.peminjaman.index') }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-[#B9AE9E] px-3 py-2 text-xs font-semibold text-[#6B5E52] transition hover:bg-[#FFFFFF] hover:text-[#2D251E]">
                Lihat semua peminjaman
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-[#E8E1D7] bg-[#F7F4EF] text-xs uppercase tracking-wider text-[#6B5E52]">
                        <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                        <th class="px-5 py-3 text-left font-semibold">Buku</th>
                        <th class="px-5 py-3 text-left font-semibold">Tanggal Pinjam</th>
                        <th class="px-5 py-3 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8E1D7]/70">
                    @forelse ($transaksiTerbaru as $transaksi)
                        <tr class="transition hover:bg-[#F7F4EF]/70">
                            <td class="px-5 py-4">
                                <p class="font-medium text-[#2D251E]">{{ $transaksi->anggota?->nama ?? 'Anggota tidak tersedia' }}</p>
                                <p class="mt-1 text-xs text-[#97897B]">{{ $transaksi->anggota?->nomor_anggota ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-sm font-medium text-[#3D342C]">{{ $transaksi->buku?->judul ?? 'Buku tidak tersedia' }}</p>
                                @if ($transaksi->kode_peminjaman)
                                    <p class="mt-1 text-xs text-[#97897B]">{{ $transaksi->kode_peminjaman }}</p>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-[#6B5E52]">
                                {{ $transaksi->tanggal_pinjam?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($transaksi->status_persetujuan === 'diajukan')
                                    <span class="inline-flex rounded-full border border-amber-400/30 bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-700">Menunggu</span>
                                @elseif ($transaksi->status_persetujuan === 'ditolak')
                                    <span class="inline-flex rounded-full border border-red-400/30 bg-red-500/10 px-2.5 py-1 text-xs font-semibold text-red-700">Ditolak</span>
                                @elseif ($transaksi->status === 'dikembalikan')
                                    <span class="inline-flex rounded-full border border-emerald-400/30 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-700">Dikembalikan</span>
                                @else
                                    <span class="inline-flex rounded-full border border-sepia-400/30 bg-sepia-500/10 px-2.5 py-1 text-xs font-semibold text-sepia-700">Dipinjam</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <p class="font-semibold text-[#3D342C]">Belum ada transaksi</p>
                                <p class="mt-1 text-sm text-[#8B7E71]">Transaksi peminjaman akan muncul di sini setelah data tersedia.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    </div>

    {{-- Permintaan verifikasi --}}
    <div class="rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] p-5 sm:flex sm:items-center sm:justify-between sm:gap-5">
        <div class="flex items-start gap-3">
            <div class="rounded-xl border border-amber-400/20 bg-amber-500/10 p-3 text-amber-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-[#2D251E]">Request peminjaman menunggu verifikasi</h2>
                <p class="mt-1 text-sm text-[#8B7E71]">Permintaan yang diajukan dan belum diproses.</p>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-4 sm:mt-0">
            <span class="text-2xl font-bold text-[#2D251E]">{{ number_format($menungguVerifikasi, 0, ',', '.') }}</span>
            <a href="{{ route('admin.peminjaman.index', ['tab' => 'pengajuan']) }}" class="inline-flex items-center gap-2 rounded-lg bg-sepia-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sepia-700">
                Periksa pengajuan
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>


</div>
@endsection
