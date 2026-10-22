@extends('layouts.member')

@php
    use App\Enums\StatusPinjaman;
    use App\Support\Format;

    $status = $grup->status();
    $ditutup = in_array($status, [StatusPinjaman::Ditolak, StatusPinjaman::Dibatalkan], true);
    $totalDenda = $grup->denda($setting);

    // Posisi pada alur: 1 diajukan, 2 disetujui, 3 dikembalikan.
    $langkah = match ($status) {
        StatusPinjaman::Menunggu => 1,
        StatusPinjaman::Dipinjam, StatusPinjaman::Terlambat => 2,
        StatusPinjaman::Dikembalikan => 3,
        default => 0,
    };
    $daftarLangkah = ['Diajukan', 'Disetujui', 'Dikembalikan'];
@endphp

@section('title', 'Detail peminjaman')

@section('content')
<x-member.remah-roti :item="['Pinjaman saya' => route('pinjaman.index'), ($grup->kode ?? 'Detail') => null]" />

<div class="wadah grid grid-cols-[minmax(0,1fr)] gap-6 pb-4 pt-4 lg:grid-cols-[15rem_minmax(0,1fr)]">
    <aside class="lg:sticky lg:top-40 lg:self-start"><x-member.menu-akun /></aside>

    <div class="space-y-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kode peminjaman</p>
                <h1 class="mt-1 break-all text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">{{ $grup->kode ?? 'Peminjaman #' . $grup->items->first()->id }}</h1>
                <p class="mt-1 text-sm text-slate-500">Diajukan {{ Format::tanggal($grup->tanggalPinjam(), true) }}</p>
            </div>
            <x-member.lencana-status :status="$status" class="!px-3.5 !py-1.5 !text-sm" />
        </div>

        {{-- Alur status --}}
        @if ($ditutup)
            <div class="flex gap-3 rounded-2xl border border-slate-200 bg-white p-5 text-sm shadow-kartu">
                <x-member.ikon nama="info" kelas="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />
                <div>
                    <p class="font-bold text-slate-900">{{ $status === StatusPinjaman::Dibatalkan ? 'Pengajuan ini kamu batalkan' : 'Pengajuan ini ditolak pustakawan' }}</p>
                    @if ($status === StatusPinjaman::Ditolak && $grup->items->first()->catatan)
                        <p class="mt-1 text-slate-600">Catatan: {{ $grup->items->first()->catatan }}</p>
                    @endif
                    <p class="mt-1 text-slate-500">Stok buku sudah dikembalikan ke katalog. Kamu bisa mengajukan lagi kapan saja.</p>
                </div>
            </div>
        @else
            <ol class="grid grid-cols-3 rounded-2xl bg-white p-5 shadow-kartu ring-1 ring-slate-100">
                @foreach ($daftarLangkah as $n => $label)
                    <li class="relative flex flex-col items-center text-center">
                        @unless ($loop->first)
                            <span @class(['absolute right-1/2 top-4 h-0.5 w-full', 'bg-brand-600' => $langkah >= $n + 1, 'bg-slate-200' => $langkah < $n + 1])></span>
                        @endunless
                        <span @class([
                            'relative z-10 flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold ring-4 ring-white',
                            'bg-brand-600 text-white' => $langkah >= $n + 1,
                            'bg-slate-200 text-slate-500' => $langkah < $n + 1,
                        ])>
                            @if ($langkah >= $n + 1) <x-member.ikon nama="check" kelas="h-4 w-4" /> @else {{ $n + 1 }} @endif
                        </span>
                        <span @class(['mt-2 text-xs font-semibold', 'text-slate-900' => $langkah >= $n + 1, 'text-slate-400' => $langkah < $n + 1])>{{ $label }}</span>
                    </li>
                @endforeach
            </ol>

            @if ($status === StatusPinjaman::Dipinjam)
                <div class="flex gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
                    <x-member.ikon nama="calendar" kelas="mt-0.5 h-5 w-5 shrink-0 text-sky-500" />
                    <p>Kembalikan buku ke perpustakaan sebelum <span class="font-bold">{{ Format::tanggal($grup->batasKembali(), true) }}</span> ({{ Format::sisaHari($grup->batasKembali()) }}). Pengembalian dicatat langsung oleh pustakawan.</p>
                </div>
            @elseif ($status === StatusPinjaman::Terlambat)
                <div class="flex gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-900">
                    <x-member.ikon nama="alert-circle" kelas="mt-0.5 h-5 w-5 shrink-0 text-red-500" />
                    <p>Pengembalian sudah terlambat <span class="font-bold">{{ $grup->hariTerlambat() }} hari</span>. Perkiraan denda saat ini <span class="font-bold">{{ Format::rupiah($totalDenda) }}</span>, bertambah setiap hari sampai buku dikembalikan.</p>
                </div>
            @elseif ($status === StatusPinjaman::Menunggu)
                <div class="flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <x-member.ikon nama="clock" kelas="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
                    <p>Pengajuanmu sudah masuk dan menunggu pustakawan. Setelah disetujui, bukunya bisa diambil di perpustakaan.</p>
                </div>
            @endif
        @endif

        {{-- Daftar buku --}}
        <section class="overflow-hidden rounded-2xl bg-white shadow-kartu ring-1 ring-slate-100">
            <h2 class="border-b border-slate-100 px-5 py-3.5 text-sm font-bold text-slate-900">{{ $grup->jumlahBuku() }} buku dalam peminjaman ini</h2>
            <ul class="divide-y divide-slate-100">
                @foreach ($grup->items as $item)
                    @php $dendaItem = $grup->dendaItem($item, $setting); @endphp
                    <li class="flex gap-4 p-5">
                        <a href="{{ route('katalog.show', $item->buku) }}" class="w-14 shrink-0 sm:w-16"><x-member.cover :buku="$item->buku" class="shadow" /></a>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('katalog.show', $item->buku) }}" class="line-clamp-2 text-sm font-bold text-slate-900 hover:text-brand-700">{{ $item->buku->judul }}</a>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $item->buku->penulis }}</p>

                            <dl class="mt-3 grid gap-x-6 gap-y-1.5 text-xs sm:grid-cols-2">
                                <div class="flex justify-between gap-3 sm:block"><dt class="text-slate-400">Rencana kembali</dt><dd class="font-semibold text-slate-700">{{ $item->tanggalRencanaLabel() }}</dd></div>
                                @if ($item->status === 'dikembalikan')
                                    <div class="flex justify-between gap-3 sm:block"><dt class="text-slate-400">Dikembalikan</dt><dd class="font-semibold text-slate-700">{{ Format::tanggalSingkat($item->tanggal_kembali_aktual) }}</dd></div>
                                    <div class="flex justify-between gap-3 sm:block"><dt class="text-slate-400">Kondisi</dt><dd class="font-semibold capitalize text-slate-700">{{ $item->kondisi ?? 'baik' }}</dd></div>
                                @endif
                                @if ($dendaItem > 0)
                                    <div class="flex justify-between gap-3 sm:block">
                                        <dt class="text-slate-400">Denda</dt>
                                        <dd class="font-semibold text-red-600">
                                            {{ Format::rupiah($dendaItem) }}
                                            @if ($item->status === 'dikembalikan')
                                                <span class="font-normal text-slate-400">({{ $item->denda_dibayar ? 'lunas' : 'belum lunas' }})</span>
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Rincian denda --}}
        @if ($totalDenda > 0)
            <section class="rounded-2xl bg-white p-5 shadow-kartu ring-1 ring-slate-100">
                <h2 class="text-sm font-bold text-slate-900">Rincian denda</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    @php
                        $dendaTerlambat = (int) $grup->items->sum(fn ($t) => $t->status === 'dikembalikan' ? $t->denda_terlambat : $t->hitungDenda(null, null, $setting)['denda_terlambat']);
                        $dendaKondisi = (int) $grup->items->sum(fn ($t) => $t->status === 'dikembalikan' ? $t->denda_kondisi : 0);
                    @endphp
                    @if ($dendaTerlambat > 0)
                        <div class="flex justify-between"><dt class="text-slate-500">Keterlambatan</dt><dd class="font-semibold">{{ Format::rupiah($dendaTerlambat) }}</dd></div>
                    @endif
                    @if ($dendaKondisi > 0)
                        <div class="flex justify-between"><dt class="text-slate-500">Buku rusak atau hilang</dt><dd class="font-semibold">{{ Format::rupiah($dendaKondisi) }}</dd></div>
                    @endif
                    <div class="flex justify-between border-t border-slate-100 pt-3 text-base"><dt class="font-bold">Total</dt><dd class="font-extrabold text-red-600">{{ Format::rupiah($totalDenda) }}</dd></div>
                </dl>
                <p class="mt-3 text-xs text-slate-500">Pembayaran denda dicatat oleh pustakawan di perpustakaan.</p>
            </section>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('pinjaman.index') }}" class="btn-soft"><x-member.ikon nama="chevron-left" kelas="h-4 w-4" /> Semua pinjaman</a>
            @if ($status === StatusPinjaman::Menunggu)
                <x-member.konfirmasi :action="route('pinjaman.batal', $grup->key)" judul="Batalkan pengajuan?" pesan="Buku akan dikembalikan ke katalog dan pengajuan ini tidak bisa dilanjutkan." tombol="Ya, batalkan">
                    <button type="button" class="btn-soft text-red-600 hover:!bg-red-50">Batalkan pengajuan</button>
                </x-member.konfirmasi>
            @endif
        </div>
    </div>
</div>
@endsection
