@extends('layouts.member')

@section('title', 'Pinjaman saya')

@section('content')
<x-member.remah-roti :item="['Pinjaman saya' => null]" />

<div class="wadah grid grid-cols-[minmax(0,1fr)] gap-6 pb-4 pt-4 lg:grid-cols-[15rem_minmax(0,1fr)]">
    <aside class="lg:sticky lg:top-40 lg:self-start"><x-member.menu-akun /></aside>

    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Pinjaman saya</h1>
        <p class="mt-1 text-sm text-slate-500">Pantau pengajuan, batas kembali, dan denda dalam satu tempat.</p>

        @unless ($anggota)
            <div class="mt-5 flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <x-member.ikon nama="alert-circle" kelas="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
                <p><span class="font-bold">Akunmu belum terhubung dengan kartu anggota,</span> jadi belum ada riwayat peminjaman. Hubungi pustakawan untuk menghubungkannya.</p>
            </div>
        @endunless

        <div class="sembunyi-scroll mt-6 flex gap-1 overflow-x-auto border-b border-slate-200" role="tablist">
            @foreach ($daftarTab as $kunci => $info)
                <a href="{{ route('pinjaman.index', $kunci === 'semua' ? [] : ['status' => $kunci]) }}" role="tab" aria-selected="{{ $tab === $kunci ? 'true' : 'false' }}"
                   @class([
                       '-mb-px flex shrink-0 items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition',
                       'border-brand-600 text-brand-700' => $tab === $kunci,
                       'border-transparent text-slate-500 hover:text-slate-800' => $tab !== $kunci,
                   ])>
                    {{ $info['label'] }}
                    <span @class(['rounded-full px-2 py-0.5 text-[11px]', 'bg-brand-50 text-brand-700' => $tab === $kunci, 'bg-slate-100 text-slate-500' => $tab !== $kunci])>{{ $jumlah[$kunci] }}</span>
                </a>
            @endforeach
        </div>

        @if ($grups->isEmpty())
            <x-member.kosong class="mt-6" ikon="package" judul="Belum ada peminjaman di sini" pesan="Pinjaman yang kamu ajukan akan muncul di halaman ini beserta statusnya.">
                <a href="{{ route('katalog.index') }}" class="btn-primary">Cari buku</a>
            </x-member.kosong>
        @else
            <ul class="mt-6 space-y-4">
                @foreach ($grups as $grup)
                    @php
                        $status = $grup->status();
                        $denda = $grup->denda($setting);
                    @endphp
                    <li class="overflow-hidden rounded-2xl bg-white shadow-kartu ring-1 ring-slate-100">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50/70 px-5 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-semibold tracking-wide text-slate-500">{{ $grup->kode ?? 'Peminjaman #' . $grup->items->first()->id }}</p>
                                <p class="text-xs text-slate-400">Diajukan {{ \App\Support\Format::tanggal($grup->tanggalPinjam()) }}</p>
                            </div>
                            <x-member.lencana-status :status="$status" />
                        </div>

                        <div class="flex gap-4 p-5">
                            <div class="flex -space-x-5">
                                @foreach ($grup->items->take(3) as $item)
                                    <x-member.cover :buku="$item->buku" class="!w-14 shrink-0 shadow ring-2 ring-white sm:!w-16" />
                                @endforeach
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="line-clamp-1 text-sm font-bold text-slate-900">{{ $grup->items->first()->buku->judul }}</p>
                                @if ($grup->jumlahBuku() > 1)
                                    <p class="text-xs text-slate-500">dan {{ $grup->jumlahBuku() - 1 }} buku lainnya</p>
                                @endif

                                <p class="mt-3 flex items-start gap-1.5 text-xs text-slate-600">
                                    @switch($status)
                                        @case(\App\Enums\StatusPinjaman::Menunggu)
                                            <x-member.ikon nama="clock" kelas="h-4 w-4 shrink-0 text-amber-500" /> Menunggu persetujuan pustakawan.
                                            @break
                                        @case(\App\Enums\StatusPinjaman::Dipinjam)
                                            <x-member.ikon nama="calendar" kelas="h-4 w-4 shrink-0 text-sky-500" /> Kembalikan sebelum {{ \App\Support\Format::tanggal($grup->batasKembali()) }} ({{ \App\Support\Format::sisaHari($grup->batasKembali()) }}).
                                            @break
                                        @case(\App\Enums\StatusPinjaman::Terlambat)
                                            <x-member.ikon nama="alert-circle" kelas="h-4 w-4 shrink-0 text-red-500" /> <span class="font-semibold text-red-600">Terlambat {{ $grup->hariTerlambat() }} hari, perkiraan denda {{ \App\Support\Format::rupiah($denda) }}.</span>
                                            @break
                                        @case(\App\Enums\StatusPinjaman::Dikembalikan)
                                            <x-member.ikon nama="check-circle" kelas="h-4 w-4 shrink-0 text-emerald-500" />
                                            @if ($denda > 0)
                                                Denda {{ \App\Support\Format::rupiah($denda) }}, {{ $grup->dendaBelumLunas($setting) > 0 ? 'belum lunas' : 'sudah lunas' }}.
                                            @else
                                                Dikembalikan tanpa denda.
                                            @endif
                                            @break
                                        @default
                                            <x-member.ikon nama="info" kelas="h-4 w-4 shrink-0 text-slate-400" /> {{ $grup->items->first()->catatan ?: 'Pengajuan tidak dilanjutkan.' }}
                                    @endswitch
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-5 py-3">
                            @if ($status === \App\Enums\StatusPinjaman::Menunggu)
                                <x-member.konfirmasi :action="route('pinjaman.batal', $grup->key)" judul="Batalkan pengajuan?" pesan="Buku akan dikembalikan ke katalog dan pengajuan ini tidak bisa dilanjutkan." tombol="Ya, batalkan">
                                    <button type="button" class="btn-soft !px-3 !py-2 text-red-600 hover:!bg-red-50">Batalkan</button>
                                </x-member.konfirmasi>
                            @endif
                            <a href="{{ route('pinjaman.show', $grup->key) }}" class="btn-outline !px-4 !py-2">Lihat detail</a>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-8">{{ $grups->onEachSide(1)->links('vendor.pagination.member') }}</div>
        @endif
    </div>
</div>
@endsection
