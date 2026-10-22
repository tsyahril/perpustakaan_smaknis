@extends('layouts.member')

@section('title', 'Favorit')

@section('content')
<x-member.remah-roti :item="['Favorit' => null]" />

<div class="wadah grid grid-cols-[minmax(0,1fr)] gap-6 pb-4 pt-4 lg:grid-cols-[15rem_minmax(0,1fr)]">
    <aside class="lg:sticky lg:top-40 lg:self-start"><x-member.menu-akun /></aside>

    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Buku favorit</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $bukus->total() }} buku tersimpan. Klik ikon hati pada buku untuk menambah atau melepasnya.</p>

        @if ($bukus->isEmpty())
            <x-member.kosong class="mt-6" ikon="heart" judul="Belum ada buku favorit" pesan="Simpan buku yang ingin kamu baca nanti supaya gampang ditemukan lagi.">
                <a href="{{ route('katalog.index') }}" class="btn-primary">Jelajahi katalog</a>
            </x-member.kosong>
        @else
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 xl:grid-cols-4">
                @foreach ($bukus as $buku)
                    <x-member.buku-card :buku="$buku" />
                @endforeach
            </div>
            <div class="mt-10">{{ $bukus->onEachSide(1)->links('vendor.pagination.member') }}</div>
        @endif
    </div>
</div>
@endsection
