@extends('layouts.member')

@section('title', 'Keranjang pinjam')

@section('content')
<x-member.remah-roti :item="['Keranjang pinjam' => null]" />

<div class="wadah grid grid-cols-[minmax(0,1fr)] gap-6 pb-4 pt-4 lg:grid-cols-[15rem_minmax(0,1fr)]">
    <aside class="lg:sticky lg:top-40 lg:self-start"><x-member.menu-akun /></aside>

    <div>
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Keranjang pinjam</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $items->count() }} dari {{ $maksBuku }} buku yang bisa diajukan sekaligus.</p>
            </div>
            @if ($items->isNotEmpty())
                <x-member.konfirmasi :action="route('keranjang.kosongkan')" metode="DELETE" judul="Kosongkan keranjang?" pesan="Semua buku di keranjang akan dihapus. Buku tetap ada di katalog." tombol="Ya, kosongkan">
                    <button type="button" class="text-sm font-semibold text-slate-500 hover:text-red-600">Kosongkan keranjang</button>
                </x-member.konfirmasi>
            @endif
        </div>

        @if ($kartuBermasalah)
            <div class="mt-5 flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <x-member.ikon nama="alert-circle" kelas="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
                <p>
                    @if (! $anggota)
                        <span class="font-bold">Akunmu belum terhubung dengan kartu anggota.</span> Minta pustakawan menghubungkannya sebelum kamu bisa mengajukan peminjaman.
                    @else
                        <span class="font-bold">Kartu anggotamu sedang nonaktif.</span> Hubungi pustakawan untuk mengaktifkannya kembali.
                    @endif
                </p>
            </div>
        @endif

        @if ($items->isEmpty())
            <x-member.kosong class="mt-6" ikon="cart" judul="Keranjangmu masih kosong" pesan="Tambahkan buku dari katalog, lalu ajukan peminjamannya di sini.">
                <a href="{{ route('katalog.index') }}" class="btn-primary">Jelajahi katalog</a>
                <a href="{{ route('pinjaman.index') }}" class="btn-soft">Pinjaman saya</a>
            </x-member.kosong>
        @else
            <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_21rem]">
                <ul class="space-y-3">
                    @foreach ($items as $buku)
                        <li class="flex gap-4 rounded-2xl bg-white p-4 shadow-kartu ring-1 ring-slate-100">
                            <a href="{{ route('katalog.show', $buku) }}" class="w-16 shrink-0 sm:w-20"><x-member.cover :buku="$buku" class="shadow" /></a>
                            <div class="flex min-w-0 flex-1 flex-col">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-brand-600">{{ $buku->kategori?->nama }}</p>
                                <a href="{{ route('katalog.show', $buku) }}" class="mt-0.5 line-clamp-2 text-sm font-bold text-slate-900 hover:text-brand-700 sm:text-base">{{ $buku->judul }}</a>
                                <p class="mt-0.5 truncate text-xs text-slate-500">{{ $buku->penulis }} &middot; {{ $buku->penerbit }}</p>

                                @if ($masalah->has($buku->id))
                                    <p class="mt-2 flex items-start gap-1.5 text-xs font-medium text-red-600"><x-member.ikon nama="alert-circle" kelas="mt-px h-3.5 w-3.5 shrink-0" /> {{ $masalah[$buku->id] }}</p>
                                @else
                                    <p class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-emerald-600"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> {{ $buku->stok }} eksemplar tersedia</p>
                                @endif

                                <div class="mt-auto flex justify-end pt-2">
                                    <form method="POST" action="{{ route('keranjang.hapus', $buku) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs font-semibold text-slate-500 hover:bg-red-50 hover:text-red-600">
                                            <x-member.ikon nama="trash" kelas="h-4 w-4" /> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- Ringkasan pengajuan --}}
                <aside class="xl:sticky xl:top-40 xl:self-start">
                    <form method="POST" action="{{ route('keranjang.checkout') }}" class="rounded-2xl bg-white p-5 shadow-kartu ring-1 ring-slate-100"
                          x-data="{ tanggal: @js(old('tanggal_kembali', today()->addDays(7)->toDateString())) }">
                        @csrf
                        <h2 class="text-base font-bold text-slate-900">Ringkasan pengajuan</h2>

                        <dl class="mt-4 space-y-2.5 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">Jumlah buku</dt><dd class="font-semibold">{{ $items->count() }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Tanggal pinjam</dt><dd class="font-semibold">{{ \App\Support\Format::tanggal(today()) }}</dd></div>
                            @if ($anggota)
                                <div class="flex justify-between"><dt class="text-slate-500">Peminjam</dt><dd class="max-w-[10rem] truncate font-semibold">{{ $anggota->nama }}</dd></div>
                            @endif
                        </dl>

                        <div class="mt-5 border-t border-slate-100 pt-5">
                            <label for="tanggal_kembali" class="text-sm font-semibold text-slate-900">Rencana tanggal kembali</label>
                            <input id="tanggal_kembali" type="date" name="tanggal_kembali" x-model="tanggal" required
                                   min="{{ today()->addDay()->toDateString() }}" max="{{ today()->addDays($maksHari)->toDateString() }}"
                                   class="input mt-2 @error('tanggal_kembali') !border-red-400 @enderror">
                            @error('tanggal_kembali')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-2.5 flex flex-wrap gap-2">
                                @foreach ([3, 7, 14] as $hari)
                                    <button type="button" @click="tanggal = '{{ today()->addDays($hari)->toDateString() }}'"
                                            class="rounded-full border px-3 py-1 text-xs font-semibold transition"
                                            :class="tanggal === '{{ today()->addDays($hari)->toDateString() }}' ? 'border-brand-600 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">{{ $hari }} hari</button>
                                @endforeach
                            </div>
                            <p class="mt-2.5 text-xs text-slate-500">Masa pinjam maksimal {{ $maksHari }} hari.</p>
                        </div>

                        <div class="mt-5 rounded-xl bg-slate-50 p-3.5 text-xs leading-relaxed text-slate-600">
                            Terlambat dikenai denda <span class="font-bold text-slate-800">{{ \App\Support\Format::rupiah($setting->denda_terlambat_per_hari) }}</span> per hari per buku.
                            <a href="{{ route('panduan') }}#denda" class="font-semibold text-brand-700 hover:underline">Aturan lengkap</a>
                        </div>

                        <button type="submit" class="btn-primary mt-5 w-full !py-3" @disabled(! $bisaDiajukan)>Ajukan peminjaman</button>
                        @if ($masalah->isNotEmpty())
                            <p class="mt-2 text-center text-xs text-red-600">Selesaikan masalah pada buku di atas dulu.</p>
                        @endif
                    </form>
                </aside>
            </div>
        @endif
    </div>
</div>
@endsection
