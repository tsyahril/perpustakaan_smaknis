@php
    // Data awal untuk store Alpine (lihat resources/js/member.js).
    $pesanFlash = session('success') ?? session('error');
    $dataAwal = [
        'login' => $sesi->login(),
        'urlLogin' => route('login'),
        'favorit' => $sesi->jumlahFavorit(),
        'keranjang' => $sesi->jumlahKeranjang(),
        'flash' => $pesanFlash ? ['pesan' => $pesanFlash, 'tipe' => session('success') ? 'sukses' : 'galat'] : null,
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('deskripsi', 'Cari, pinjam, dan pantau buku Perpustakaan SMAKNIS secara online.')">

    <title>@yield('title', 'Beranda') | Perpustakaan SMAKNIS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>window.memberBoot = @json($dataAwal);</script>
    @vite(['resources/css/member.css', 'resources/js/member.js'])
    @stack('head')
</head>
<body class="flex min-h-screen flex-col">
    <a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:shadow-lg">Lewati ke konten</a>

    @include('partials.member.sprite')

    <x-member.header />

    <main id="konten" class="flex-1">
        @yield('content')
    </main>

    <x-member.footer />
    <x-member.toast />

    @stack('scripts')
</body>
</html>
