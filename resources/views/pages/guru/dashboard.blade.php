@extends('layouts.member')

@php
    use App\Support\Format;

    $kartu = [
        ['Sedang dipinjam', $ringkasan['dipinjam'], 'book-open', 'bg-sky-50 text-sky-600'],
        ['Menunggu persetujuan', $ringkasan['menunggu'], 'clock', 'bg-amber-50 text-amber-600'],
        ['Terlambat', $ringkasan['terlambat'], 'alert-circle', 'bg-red-50 text-red-600'],
        ['Denda belum lunas', Format::rupiah($ringkasan['denda']), 'card', 'bg-rose-50 text-rose-600'],
    ];
@endphp

@section('title', 'Akun saya')

@section('content')
<x-member.remah-roti :item="['Akun saya' => null]" />

<div class="wadah grid grid-cols-[minmax(0,1fr)] gap-6 pb-4 pt-4 lg:grid-cols-[15rem_minmax(0,1fr)]">
    <aside class="lg:sticky lg:top-40 lg:self-start"><x-member.menu-akun /></aside>

    <div class="min-w-0 space-y-6">
        {{-- Kartu anggota --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-800 to-brand-950 p-6 text-white shadow-kartu-naik sm:p-8">
            <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full border-[28px] border-white/[0.06]"></div>
            <div class="pointer-events-none absolute -bottom-24 right-24 h-48 w-48 rounded-full border-[24px] border-white/[0.05]"></div>
            <div class="relative flex flex-wrap items-start justify-between gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-200">Kartu anggota perpustakaan</p>
                    <h1 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $user->name }}</h1>
                    <p class="mt-1 text-sm text-brand-100">{{ $user->aksesLabel() }}@if ($user->kelas) &middot; {{ $user->kelas }}@endif &middot; Username: {{ $user->username }}</p>
                </div>
                @if ($anggota)
                    <div class="sm:text-right">
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-brand-200">Nomor anggota</p>
                        <p class="mt-1 font-mono text-lg font-bold tracking-wider">{{ $anggota->nomor_anggota }}</p>
                        <span @class(['lencana mt-2 ring-0', 'bg-emerald-400/20 text-emerald-200' => $anggota->status === 'aktif', 'bg-white/10 text-slate-300' => $anggota->status !== 'aktif'])>
                            {{ $anggota->status === 'aktif' ? 'Anggota aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                @endif
            </div>
        </section>

        @unless ($anggota)
            <div class="flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <x-member.ikon nama="alert-circle" kelas="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
                <p><span class="font-bold">Akunmu belum terhubung dengan kartu anggota.</span> Kamu tetap bisa menjelajahi katalog, tetapi baru bisa meminjam setelah pustakawan menghubungkannya.</p>
            </div>
        @endunless

        @if ($user->canManageLibrary())
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-brand-200 bg-brand-50 p-4 text-sm text-brand-900">
                <p><span class="font-bold">Kamu punya akses {{ $user->isAdmin() ? 'admin' : 'pustakawan' }}.</span> Kelola buku, peminjaman, dan denda dari panel perpustakaan.</p>
                <a href="{{ $user->isAdmin() ? route('admin.dashboard') : route('pustakawan.dashboard') }}" class="btn-primary !py-2">Buka panel</a>
            </div>
        @endif

        {{-- Ringkasan --}}
        <section class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            @foreach ($kartu as [$label, $nilai, $ikon, $warna])
                <div class="rounded-2xl bg-white p-4 shadow-kartu ring-1 ring-slate-100">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $warna }}"><x-member.ikon :nama="$ikon" kelas="h-5 w-5" /></span>
                    <p class="mt-3 text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">{{ $nilai }}</p>
                    <p class="text-xs text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            {{-- Jatuh tempo --}}
            <section class="rounded-2xl bg-white p-5 shadow-kartu ring-1 ring-slate-100">
                <h2 class="text-sm font-bold text-slate-900">Batas kembali terdekat</h2>
                @forelse ($jatuhTempo as $t)
                    <a href="{{ route('pinjaman.show', $t->group_key) }}" class="mt-3 flex items-center gap-3 rounded-xl p-2 -mx-2 hover:bg-slate-50">
                        <x-member.cover :buku="$t->buku" class="!w-10 shrink-0 shadow" />
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-semibold text-slate-800">{{ $t->buku->judul }}</span>
                            <span class="block text-xs text-slate-500">{{ Format::tanggal($t->tanggal_kembali_rencana) }}</span>
                        </span>
                        <span @class(['text-xs font-bold', 'text-red-600' => $t->isTelat(), 'text-slate-500' => ! $t->isTelat()])>{{ Format::sisaHari($t->tanggal_kembali_rencana) }}</span>
                    </a>
                @empty
                    <p class="mt-3 rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Tidak ada buku yang sedang kamu pinjam.</p>
                @endforelse
            </section>

            {{-- Pinjaman terbaru --}}
            <section class="rounded-2xl bg-white p-5 shadow-kartu ring-1 ring-slate-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Aktivitas terbaru</h2>
                    <a href="{{ route('pinjaman.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">Semua pinjaman</a>
                </div>
                @forelse ($terbaru as $grup)
                    <a href="{{ route('pinjaman.show', $grup->key) }}" class="mt-3 flex items-center justify-between gap-3 rounded-xl p-2 -mx-2 hover:bg-slate-50">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-slate-800">{{ $grup->items->first()->buku->judul }}@if ($grup->jumlahBuku() > 1) <span class="font-normal text-slate-400">+{{ $grup->jumlahBuku() - 1 }}</span>@endif</span>
                            <span class="block text-xs text-slate-500">{{ Format::tanggalSingkat($grup->tanggalPinjam()) }}</span>
                        </span>
                        <x-member.lencana-status :status="$grup->status()" />
                    </a>
                @empty
                    <p class="mt-3 rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada riwayat peminjaman.</p>
                @endforelse
            </section>
        </div>

        @if ($rekomendasi->isNotEmpty())
            <x-member.rak class="!px-0" judul="Mungkin kamu suka" :bukus="$rekomendasi" />
        @else
            <section class="flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-white p-5 shadow-kartu ring-1 ring-slate-100">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Mulai menjelajah</h2>
                    <p class="mt-1 text-sm text-slate-500">Ada {{ number_format($totalBuku, 0, ',', '.') }} judul di perpustakaan. Pinjam atau favoritkan beberapa buku, nanti rekomendasi muncul di sini.</p>
                </div>
                <a href="{{ route('katalog.index') }}" class="btn-primary">Buka katalog</a>
            </section>
        @endif

        {{-- Kata sandi --}}
        <section class="rounded-2xl bg-white p-5 shadow-kartu ring-1 ring-slate-100">
            <h2 class="text-sm font-bold text-slate-900">Ubah kata sandi</h2>
            @if (session('status') === 'password-updated')
                <p class="mt-3 rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700">Kata sandi berhasil diperbarui.</p>
            @endif
            <form method="POST" action="{{ route('password.update') }}" class="mt-4 grid gap-4 sm:grid-cols-3">
                @csrf
                @method('PUT')
                @foreach ([['current_password', 'Kata sandi saat ini', 'current-password'], ['password', 'Kata sandi baru', 'new-password'], ['password_confirmation', 'Ulangi kata sandi baru', 'new-password']] as [$nama, $label, $auto])
                    <div>
                        <label for="{{ $nama }}" class="text-xs font-semibold text-slate-600">{{ $label }}</label>
                        <input id="{{ $nama }}" type="password" name="{{ $nama }}" autocomplete="{{ $auto }}" required class="input mt-1.5">
                        @if ($nama !== 'password_confirmation')
                            @error($nama, 'updatePassword')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                        @endif
                    </div>
                @endforeach
                <div class="sm:col-span-3"><button type="submit" class="btn-primary">Simpan kata sandi</button></div>
            </form>
        </section>
    </div>
</div>
@endsection
