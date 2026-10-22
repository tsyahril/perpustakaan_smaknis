@extends('layouts.member')

@php
    use App\Support\Format;

    $alur = [
        ['Cari dan pilih', "Telusuri katalog, lalu masukkan buku ke keranjang pinjam. Satu peminjaman maksimal {$maksBuku} buku."],
        ['Ajukan peminjaman', 'Tentukan tanggal rencana kembali di keranjang, lalu kirim pengajuan. Stok buku langsung ditahan untukmu.'],
        ['Tunggu persetujuan', 'Pustakawan memeriksa pengajuanmu. Hasilnya muncul di Pinjaman saya dan di lonceng pengingat.'],
        ['Ambil bukunya', 'Setelah disetujui, datang ke perpustakaan untuk mengambil buku yang kamu pinjam.'],
    ];
    $tarif = [
        ['Terlambat mengembalikan', Format::rupiah($setting->denda_terlambat_per_hari) . ' per hari per buku'],
        ['Buku rusak', Format::rupiah($setting->denda_rusak) . ' per buku'],
        ['Buku hilang', Format::rupiah($setting->denda_hilang) . ' per buku'],
    ];
    $tanya = [
        ['Kenapa tombol pinjam meminta saya masuk?', 'Peminjaman dicatat atas nama anggota, jadi kamu perlu masuk dengan akun yang sudah terhubung dengan kartu anggota.'],
        ['Bagaimana mendapatkan akun?', 'Akun anggota dibuat oleh admin atau pustakawan sekolah. Kalau belum punya, atau akunmu belum terhubung dengan kartu anggota, hubungi pustakawan.'],
        ['Bolehkah meminjam buku yang sama dua kali?', 'Tidak selama buku itu masih kamu pinjam atau masih menunggu persetujuan. Kembalikan dulu, baru bisa meminjam lagi.'],
        ['Pengajuan saya salah, bisa dibatalkan?', 'Bisa, selama statusnya masih menunggu persetujuan. Buka Pinjaman saya, pilih pengajuannya, lalu klik Batalkan. Stok langsung kembali ke katalog.'],
        ['Stok buku habis, apa yang bisa dilakukan?', 'Stok bertambah saat buku dikembalikan. Simpan bukunya ke favorit supaya mudah dicari lagi.'],
    ];
@endphp

@section('title', 'Panduan peminjaman')

@section('content')
<x-member.remah-roti :item="['Panduan peminjaman' => null]" />

<div class="wadah max-w-4xl pb-4 pt-4">
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Panduan peminjaman</h1>
    <p class="mt-2 text-sm text-slate-500">Semua yang perlu kamu tahu sebelum meminjam buku di Perpustakaan SMAKNIS.</p>

    <section class="mt-8" id="cara-meminjam">
        <h2 class="judul-bagian">Cara meminjam</h2>
        <ol class="mt-4 grid gap-4 sm:grid-cols-2">
            @foreach ($alur as $n => [$judul, $uraian])
                <li class="flex gap-4 rounded-2xl bg-white p-5 shadow-kartu ring-1 ring-slate-100">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-extrabold text-white">{{ $n + 1 }}</span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ $judul }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $uraian }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="mt-10" id="pengembalian">
        <h2 class="judul-bagian">Mengembalikan buku</h2>
        <div class="mt-4 rounded-2xl bg-white p-5 text-sm leading-relaxed text-slate-600 shadow-kartu ring-1 ring-slate-100">
            <p>Bawa bukunya ke perpustakaan sebelum tanggal rencana kembali. Pustakawan akan memeriksa kondisi buku dan mencatat pengembalian, lalu statusnya berubah menjadi <span class="font-semibold text-slate-800">Dikembalikan</span> di halaman Pinjaman saya.</p>
            <p class="mt-3">Masa pinjam maksimal <span class="font-semibold text-slate-800">{{ $maksHari }} hari</span> sejak tanggal pengajuan, dan tiap judul hanya bisa dipinjam satu eksemplar dalam satu waktu.</p>
        </div>
    </section>

    <section class="mt-10" id="denda">
        <h2 class="judul-bagian">Aturan denda</h2>
        <div class="mt-4 overflow-hidden rounded-2xl bg-white shadow-kartu ring-1 ring-slate-100">
            <dl class="divide-y divide-slate-100 text-sm">
                @foreach ($tarif as [$label, $nilai])
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <dt class="text-slate-600">{{ $label }}</dt>
                        <dd class="font-bold text-slate-900">{{ $nilai }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
        <p class="mt-3 text-xs text-slate-500">Tarif mengikuti pengaturan terbaru dari perpustakaan. Denda dihitung otomatis dan dibayarkan langsung ke pustakawan.</p>
    </section>

    <section class="mt-10" id="tanya-jawab">
        <h2 class="judul-bagian">Pertanyaan umum</h2>
        <div class="mt-4 divide-y divide-slate-100 overflow-hidden rounded-2xl bg-white shadow-kartu ring-1 ring-slate-100">
            @foreach ($tanya as [$tanyaItem, $jawab])
                <details class="group">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        {{ $tanyaItem }}
                        <x-member.ikon nama="chevron-down" kelas="h-4 w-4 shrink-0 text-slate-400 transition group-open:rotate-180" />
                    </summary>
                    <p class="px-5 pb-4 text-sm leading-relaxed text-slate-500">{{ $jawab }}</p>
                </details>
            @endforeach
        </div>
    </section>

    <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ route('katalog.index') }}" class="btn-primary">Mulai cari buku</a>
        @guest <a href="{{ route('login') }}" class="btn-soft">Masuk</a> @endguest
    </div>
</div>
@endsection
