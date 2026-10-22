@extends('layouts.member')

@php
    $habis = $buku->stok <= 0;
    $dikeranjang = $sesi->dikeranjang($buku->id);
    $detail = array_filter([
        'Penulis' => $buku->penulis,
        'Penerbit' => $buku->penerbit,
        'Tahun terbit' => $buku->tahun_terbit,
        'ISBN' => $buku->isbn,
        'Kategori' => $buku->kategori?->nama,
        'Eksemplar tersedia' => $buku->stok,
        'Total dipinjam' => $jumlahDipinjam > 0 ? $jumlahDipinjam . ' kali' : null,
    ], fn ($nilai) => $nilai !== null && $nilai !== '');

    $remah = ['Katalog' => route('katalog.index')];
    if ($buku->kategori) {
        $remah[$buku->kategori->nama] = route('katalog.index', ['kategori' => $buku->id_kategori]);
    }
    $remah[$buku->judul] = null;
@endphp

@section('title', $buku->judul)
@section('deskripsi', \Illuminate\Support\Str::limit(strip_tags((string) $buku->deskripsi) ?: "{$buku->judul} oleh {$buku->penulis}", 150))

@section('content')
<x-member.remah-roti :item="$remah" />

<div class="wadah pt-5">
    <div class="grid gap-x-10 gap-y-6 lg:grid-cols-[300px_1fr] xl:grid-cols-[320px_1fr_340px]">

        {{-- Sampul --}}
        <div class="lg:row-span-3 lg:self-start xl:row-span-2 lg:sticky lg:top-40">
            <div class="mx-auto max-w-[260px] lg:max-w-none">
                <x-member.cover :buku="$buku" class="rounded-xl shadow-kartu-naik ring-1 ring-slate-200" />
            </div>
        </div>

        {{-- Judul --}}
        <div class="lg:col-start-2">
            @if ($buku->kategori)
                <a href="{{ route('katalog.index', ['kategori' => $buku->id_kategori]) }}" class="lencana bg-brand-50 text-brand-700 ring-brand-100 hover:bg-brand-100">{{ $buku->kategori->nama }}</a>
            @endif
            <h1 class="mt-3 text-2xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-3xl">{{ $buku->judul }}</h1>
            <p class="mt-2 text-sm text-slate-500">
                oleh <a href="{{ route('katalog.index', ['penulis' => $buku->penulis]) }}" class="font-semibold text-brand-700 hover:underline">{{ $buku->penulis }}</a>
                <span class="mx-1.5 text-slate-300">&middot;</span>{{ $buku->penerbit }}, {{ $buku->tahun_terbit }}
            </p>
        </div>

        {{-- Kartu aksi --}}
        <aside class="lg:col-start-2 xl:col-start-3 xl:row-span-2 xl:row-start-1 xl:self-start xl:sticky xl:top-40">
            <div class="rounded-2xl bg-white p-5 shadow-kartu ring-1 ring-slate-100">
                <p class="text-sm font-bold text-slate-900">Pinjam buku ini</p>

                <div @class([
                    'mt-3 flex items-center gap-2 rounded-xl px-3.5 py-2.5 text-sm font-semibold',
                    'bg-emerald-50 text-emerald-700' => ! $habis,
                    'bg-slate-100 text-slate-500' => $habis,
                ])>
                    <span @class(['h-2 w-2 rounded-full', 'bg-emerald-500' => ! $habis, 'bg-slate-400' => $habis])></span>
                    {{ $habis ? 'Stok sedang habis' : $buku->stok . ' eksemplar tersedia' }}
                </div>

                <div class="mt-4 space-y-2.5" x-data="tombolKeranjang(@js(route('keranjang.tambah', $buku)), @js($dikeranjang))">
                    @if ($pinjamanAktif)
                        @php $telat = $pinjamanAktif->status_persetujuan === 'disetujui' && $pinjamanAktif->isTelat(); @endphp
                        <div @class(['rounded-xl border p-3.5 text-sm', 'border-sky-200 bg-sky-50 text-sky-900' => ! $telat, 'border-red-200 bg-red-50 text-red-900' => $telat])>
                            <p class="font-bold">{{ match (true) { $telat => 'Buku ini terlambat dikembalikan', $pinjamanAktif->status_persetujuan === 'diajukan' => 'Pengajuanmu menunggu persetujuan', default => 'Buku ini sedang kamu pinjam' } }}</p>
                            @if ($pinjamanAktif->status_persetujuan === 'disetujui' && $pinjamanAktif->tanggal_kembali_rencana)
                                <p class="mt-0.5 text-xs">Batas kembali {{ \App\Support\Format::tanggal($pinjamanAktif->tanggal_kembali_rencana) }}</p>
                            @endif
                            <a href="{{ route('pinjaman.show', $pinjamanAktif->group_key) }}" class="mt-2 inline-flex items-center gap-1 text-xs font-bold underline-offset-2 hover:underline">Lihat detail <x-member.ikon nama="arrow-right" kelas="h-3 w-3" /></a>
                        </div>
                    @elseif ($habis)
                        <button type="button" class="btn-primary w-full" disabled>Stok habis</button>
                        <p class="text-xs leading-relaxed text-slate-500">Stok bertambah saat buku dikembalikan. Simpan ke favorit agar mudah dicari lagi.</p>
                    @else
                        <form method="POST" action="{{ route('keranjang.tambah', $buku) }}">
                            @csrf
                            <input type="hidden" name="langsung" value="1">
                            <button type="submit" class="btn-primary w-full" :disabled="memuat" @click.prevent="tambah(true)">
                                {{ $sesi->login() ? 'Pinjam sekarang' : 'Masuk untuk meminjam' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('keranjang.tambah', $buku) }}">
                            @csrf
                            <button type="submit" class="btn-outline w-full" :disabled="memuat"
                                    @click.prevent="sudah ? (window.location.href = @js(route('keranjang.index'))) : tambah()">
                                <x-member.ikon nama="cart" kelas="h-4 w-4" x-show="! sudah" />
                                <x-member.ikon nama="check" kelas="h-4 w-4" x-show="sudah" x-cloak />
                                <span x-text="sudah ? 'Di keranjang, lihat' : 'Tambah ke keranjang'">{{ $dikeranjang ? 'Di keranjang, lihat' : 'Tambah ke keranjang' }}</span>
                            </button>
                        </form>
                    @endif
                </div>

                <div class="mt-4 flex gap-2 border-t border-slate-100 pt-4">
                    <form method="POST" action="{{ route('favorit.toggle', $buku) }}" class="flex-1" x-data="tombolFavorit(@js(route('favorit.toggle', $buku)), @js($sesi->favorit($buku->id)))">
                        @csrf
                        <button type="submit" class="btn-soft w-full" :class="aktif && '!border-rose-200 !bg-rose-50 !text-rose-600'" :disabled="memuat" @click.prevent="ubah()" :aria-pressed="aktif">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :fill="aktif ? 'currentColor' : 'none'" aria-hidden="true"><use href="#i-heart"/></svg>
                            <span x-text="aktif ? 'Favorit' : 'Simpan'">{{ $sesi->favorit($buku->id) ? 'Favorit' : 'Simpan' }}</span>
                        </button>
                    </form>
                    <div class="flex-1" x-data="bagikan(@js($buku->judul))">
                        <button type="button" class="btn-soft w-full" @click="bagikan()"><x-member.ikon nama="share" kelas="h-4 w-4" /> Bagikan</button>
                    </div>
                </div>

                <ul class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                    <li class="flex gap-2"><x-member.ikon nama="info" kelas="h-4 w-4 shrink-0 text-slate-400" /> Satu peminjaman maksimal {{ $maksBuku }} buku.</li>
                    <li class="flex gap-2"><x-member.ikon nama="calendar" kelas="h-4 w-4 shrink-0 text-slate-400" /> Pengajuan diproses pustakawan, lalu bukunya diambil di perpustakaan.</li>
                    <li class="flex gap-2"><x-member.ikon nama="clock" kelas="h-4 w-4 shrink-0 text-slate-400" /> Terlambat mengembalikan dikenai denda per hari. <a href="{{ route('panduan') }}#denda" class="font-semibold text-brand-700 hover:underline">Lihat aturan</a></li>
                </ul>
            </div>
        </aside>

        {{-- Deskripsi dan detail --}}
        <div class="space-y-8 lg:col-start-2">
            <section x-data="{ penuh: false }">
                <h2 class="text-base font-bold text-slate-900">Deskripsi</h2>
                @if (filled($buku->deskripsi))
                    <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600" :class="! penuh && 'line-clamp-6'">{{ $buku->deskripsi }}</p>
                    @if (mb_strlen($buku->deskripsi) > 400)
                        <button type="button" class="mt-2 text-sm font-semibold text-brand-700 hover:underline" @click="penuh = ! penuh" x-text="penuh ? 'Tampilkan lebih sedikit' : 'Baca selengkapnya'">Baca selengkapnya</button>
                    @endif
                @else
                    <p class="mt-2 text-sm text-slate-400">Belum ada deskripsi untuk buku ini.</p>
                @endif
            </section>

            <section>
                <h2 class="text-base font-bold text-slate-900">Detail buku</h2>
                <dl class="mt-3 divide-y divide-slate-100 overflow-hidden rounded-2xl bg-white text-sm shadow-kartu ring-1 ring-slate-100">
                    @foreach ($detail as $label => $nilai)
                        <div class="grid grid-cols-[9rem_1fr] gap-4 px-4 py-3 sm:grid-cols-[12rem_1fr]">
                            <dt class="text-slate-500">{{ $label }}</dt>
                            <dd class="font-medium text-slate-800">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        </div>
    </div>
</div>

<div class="mt-14 space-y-12">
    @if ($buku->kategori)
        <x-member.rak :judul="'Lainnya di ' . $buku->kategori->nama" :bukus="$terkait" :lihat="route('katalog.index', ['kategori' => $buku->id_kategori])" />
    @endif
    <x-member.rak :judul="'Karya lain dari ' . $buku->penulis" :bukus="$karyaPenulis" :lihat="route('katalog.index', ['penulis' => $buku->penulis])" />
</div>
@endsection
