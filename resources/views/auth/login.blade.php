@extends('layouts.guest')

@section('content')
<div class="relative min-h-screen overflow-hidden bg-[#FAF9F7] text-[#2D251E] selection:bg-sepia-500/30">

    <!-- Background decorations -->
    <div class="pointer-events-none absolute -left-40 top-20 h-96 w-96 rounded-full bg-sepia-600/10 blur-[110px]"></div>
    <div class="pointer-events-none absolute -right-32 bottom-0 h-96 w-96 rounded-full bg-sepia-500/10 blur-[120px]"></div>

    <!-- Top navigation -->
    <header class="relative z-10 flex items-center justify-between border-b border-[#E8E1D7] px-5 py-5 sm:px-10 lg:px-16">
        <a href="{{ url('/') }}" class="group flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-sepia-300/20 bg-gradient-to-br from-sepia-500/25 to-sepia-900/30 shadow-lg shadow-sepia-950/30 transition duration-300 group-hover:border-sepia-300/40">
                <svg class="h-6 w-6 text-sepia-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M8 7h8M8 11h7" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-extrabold tracking-[0.15em] text-[#2D251E] sm:text-base">SMAKNIS</p>
                <p class="mt-0.5 text-[9px] font-semibold tracking-[0.22em] text-sepia-700/70 sm:text-[10px]">DIGITAL LIBRARY</p>
            </div>
        </a>

        <div class="flex items-center gap-3 sm:gap-5">
            <div class="hidden items-center gap-2 text-xs text-[#8B7E71] sm:flex">
                <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,0.6)]"></span>
                Sistem Perpustakaan Sekolah
            </div>

            {{-- Tombol kembali ke landing page (beranda) --}}
            <a href="{{ url('/') }}"
               class="group/back inline-flex items-center gap-2 rounded-xl border border-sepia-300/40 bg-[#FFFFFF] px-3.5 py-2.5 text-xs font-bold text-sepia-700 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-sepia-400/60 hover:bg-sepia-500/10 hover:text-sepia-800 focus:outline-none focus:ring-4 focus:ring-sepia-600/15 sm:px-4">
                <svg class="h-4 w-4 transition duration-300 group-hover/back:-translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5m6-6-6 6 6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </header>

    <!-- Main content -->
    <main class="relative z-10 mx-auto grid min-h-[calc(100vh-81px)] max-w-[1440px] grid-cols-1 lg:grid-cols-[1.08fr_0.92fr]">

        <!-- Left: Branding and visual -->
        <section class="relative flex flex-col justify-center overflow-hidden px-6 py-12 sm:px-12 lg:px-16 xl:px-24 lg:py-16">

            <!-- Decorative grid -->
            <div class="pointer-events-none absolute inset-0 opacity-[0.12]"
                 style="background-image:linear-gradient(rgba(148,163,184,.2) 1px,transparent 1px),linear-gradient(90deg,rgba(148,163,184,.2) 1px,transparent 1px);background-size:42px 42px;mask-image:linear-gradient(to bottom,black,transparent 90%)">
            </div>

            <div class="relative z-10 mx-auto w-full max-w-2xl">
                <div class="mb-7 inline-flex items-center gap-2 rounded-full border border-sepia-300/15 bg-sepia-400/[0.07] px-4 py-2 text-[10px] font-semibold tracking-[0.19em] text-sepia-700 sm:text-xs">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    KNOWLEDGE · GROWTH · FUTURE
                </div>

                <h1 class="max-w-xl text-4xl font-black leading-[1.12] tracking-tight text-[#2D251E] sm:text-5xl xl:text-6xl">
                    Jelajahi Ilmu.<br>
                    <span class="bg-gradient-to-r from-sepia-300 via-sepia-200 to-sepia-300 bg-clip-text text-transparent">
                        Bangun Masa Depan.
                    </span>
                </h1>

                <p class="mt-6 max-w-lg text-sm leading-7 text-[#8B7E71] sm:text-base sm:leading-8">
                    Temukan wawasan baru, perluas pengetahuan, dan mulai perjalanan belajarmu melalui Perpustakaan SMAKNIS.
                </p>

                <!-- Library illustration -->
                <div class="relative mt-10 overflow-hidden rounded-[2rem] border border-[#E8E1D7] bg-gradient-to-br from-[#F0EAE1] via-[#FFFFFF] to-[#FAF9F7] p-5 shadow-2xl shadow-black/5 sm:mt-12 sm:p-8">

                    <div class="absolute -right-10 -top-16 h-48 w-48 rounded-full border border-sepia-300/[0.08]"></div>
                    <div class="absolute -right-3 -top-9 h-32 w-32 rounded-full border border-sepia-300/[0.10]"></div>

                    <div class="relative flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-bold tracking-[0.2em] text-sepia-700">RUANG PENGETAHUAN</p>
                            <h2 class="mt-2 text-xl font-bold text-[#2D251E] sm:text-2xl">Satu buku, banyak kemungkinan.</h2>
                            <p class="mt-2 max-w-xs text-xs leading-6 text-[#8B7E71] sm:text-sm">
                                Awali langkah kecilmu dengan membaca dan terus belajar.
                            </p>
                        </div>

                        <div class="hidden h-20 w-20 shrink-0 items-center justify-center rounded-3xl border border-sepia-200/15 bg-sepia-300/[0.07] sm:flex">
                            <svg class="h-12 w-12 text-sepia-700" viewBox="0 0 64 64" fill="none">
                                <path d="M8 14c9-4 17-2 24 3v36c-7-5-15-7-24-3V14Z" fill="#8b5e34" fill-opacity=".28" stroke="#d6c3a9" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M56 14c-9-4-17-2-24 3v36c7-5 15-7 24-3V14Z" fill="#a88461" fill-opacity=".2" stroke="#e7dccd" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M14 23c5-1 9 0 13 2M14 29c5-1 9 0 13 2M38 25c4-2 8-3 12-2M38 31c4-2 8-3 12-2" stroke="#bfdbfe" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>

                    <div class="my-6 h-px bg-gradient-to-r from-sepia-300/30 via-[#F0EAE1] to-transparent"></div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-[#E8E1D7] bg-[#F0EAE1] p-4 transition duration-300 hover:border-sepia-300/20 hover:bg-[#F0EAE1]">
                            <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-sepia-400/10 text-sepia-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 1 4 17.5v-12Z" stroke-width="1.6" stroke-linejoin="round"/>
                                    <path d="M4 17.5A2.5 2.5 0 0 1 6.5 15H20M8 7h7M8 10h6" stroke-width="1.6" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-[#2D251E]">Koleksi Buku</p>
                            <p class="mt-1 text-[11px] leading-5 text-[#8B7E71]">Referensi untuk memperluas wawasan.</p>
                        </div>

                        <div class="rounded-2xl border border-[#E8E1D7] bg-[#F0EAE1] p-4 transition duration-300 hover:border-sepia-300/20 hover:bg-[#F0EAE1]">
                            <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-sepia-400/10 text-sepia-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 10.5 12 5l9 5.5-9 5.5-9-5.5Z" stroke-width="1.6" stroke-linejoin="round"/>
                                    <path d="M7 13v4c3.2 2.5 6.8 2.5 10 0v-4M21 10.5V16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-[#2D251E]">Ruang Belajar</p>
                            <p class="mt-1 text-[11px] leading-5 text-[#8B7E71]">Tempat tumbuh bersama ilmu.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-7 flex flex-wrap items-center gap-x-5 gap-y-2 text-[10px] font-medium tracking-wide text-[#97897B] sm:text-xs">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-sepia-700/70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 3 5 6v5c0 4.5 2.8 7.7 7 10 4.2-2.3 7-5.5 7-10V6l-7-3Z" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="m9 12 2 2 4-4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Akses pengguna terdaftar
                    </span>
                    <span class="hidden h-1 w-1 rounded-full bg-[#C9BFB1] sm:block"></span>
                    <span>Belajar tanpa batas</span>
                </div>
            </div>
        </section>

        <!-- Right: Login form -->
        <section class="relative flex items-center justify-center px-5 pb-12 pt-5 sm:px-10 sm:pb-16 lg:border-l lg:border-[#E8E1D7] lg:px-12 lg:py-16 xl:px-20">

            <div class="pointer-events-none absolute inset-0 hidden bg-gradient-to-br from-sepia-500/[0.035] via-transparent to-sepia-400/[0.025] lg:block"></div>

            <div class="relative w-full max-w-md">

                <div class="mb-7 flex items-center gap-3 lg:hidden">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-sepia-300/20 bg-sepia-400/10">
                        <svg class="h-5 w-5 text-sepia-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" stroke-width="1.6" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#2D251E]">Perpustakaan SMAKNIS</p>
                        <p class="text-[10px] tracking-wider text-[#8B7E71]">DIGITAL LIBRARY SYSTEM</p>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-[#E8E1D7] bg-gradient-to-b from-[#F7F4EF]/95 to-[#FFFFFF]/95 p-6 shadow-2xl shadow-black/5 backdrop-blur-xl sm:p-9">

                    <div class="mb-8">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl border border-sepia-300/20 bg-gradient-to-br from-sepia-400/20 to-sepia-900/20 shadow-lg shadow-sepia-950/30">
                            <svg class="h-6 w-6 text-sepia-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <p class="mb-2 text-[10px] font-bold tracking-[0.22em] text-sepia-700 sm:text-xs">AKSES PERPUSTAKAAN</p>
                        <h2 class="text-3xl font-black tracking-tight text-[#2D251E] sm:text-[2.1rem]">Selamat datang.</h2>
                        <p class="mt-3 text-sm leading-6 text-[#8B7E71]">
                            Masuk ke akunmu dan lanjutkan perjalanan belajar hari ini.
                        </p>
                    </div>

                    <!-- Session status -->
                    <x-auth-session-status class="mb-5 text-sm text-emerald-700" :status="session('status')" />

                    <!-- Login form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Username -->
                        <div>
                            <label for="username" class="mb-2 block text-xs font-semibold tracking-wide text-[#3D342C]">
                                Username
                            </label>

                            <div class="group relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#97897B] transition group-focus-within:text-sepia-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <circle cx="12" cy="8" r="3.5" stroke-width="1.6"/>
                                        <path d="M4.5 19.5c1.4-3.2 4.2-5 7.5-5s6.1 1.8 7.5 5" stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                </div>

                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    value="{{ old('username') }}"
                                    placeholder="cth: siswa01"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    class="block w-full rounded-xl border border-[#E8E1D7] bg-[#FFFFFF]/90 py-3.5 pl-12 pr-4 text-sm text-[#2D251E] outline-none transition duration-200 placeholder:text-[#7C6F62] hover:border-[#D6CDC0] focus:border-sepia-400/70 focus:ring-4 focus:ring-sepia-600/10"
                                >
                            </div>

                            @error('username')
                                <p class="mt-2 text-xs leading-5 text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="password" class="block text-xs font-semibold tracking-wide text-[#3D342C]">
                                    Password
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                       class="text-[11px] font-medium text-sepia-700 transition hover:text-sepia-800 hover:underline">
                                        Lupa password?
                                    </a>
                                @endif
                            </div>

                            <div class="group relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#97897B] transition group-focus-within:text-sepia-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <rect x="4" y="10" width="16" height="11" rx="2" stroke-width="1.6"/>
                                        <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3" stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                </div>

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Masukkan password"
                                    required
                                    autocomplete="current-password"
                                    class="block w-full rounded-xl border border-[#E8E1D7] bg-[#FFFFFF]/90 py-3.5 pl-12 pr-12 text-sm text-[#2D251E] outline-none transition duration-200 placeholder:text-[#7C6F62] hover:border-[#D6CDC0] focus:border-sepia-400/70 focus:ring-4 focus:ring-sepia-600/10"
                                >

                                <button
                                    type="button"
                                    id="togglePassword"
                                    aria-label="Tampilkan password"
                                    aria-pressed="false"
                                    class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-[#97897B] transition hover:text-sepia-800 focus:outline-none focus:text-sepia-700"
                                >
                                    <svg id="eyeIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z" stroke-width="1.6" stroke-linejoin="round"/>
                                        <circle cx="12" cy="12" r="3" stroke-width="1.6"/>
                                    </svg>
                                </button>
                            </div>

                            @error('password')
                                <p class="mt-2 text-xs leading-5 text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember me -->
                        <div class="flex items-center pt-1">
                            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2.5">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    value="1"
                                    class="h-4 w-4 rounded border-[#B9AE9E] bg-[#FFFFFF] text-sepia-700 focus:ring-2 focus:ring-sepia-600/30 focus:ring-offset-0"
                                >
                                <span class="text-xs text-[#8B7E71] transition hover:text-[#3D342C]">
                                    Ingat saya di perangkat ini
                                </span>
                            </label>
                        </div>

                        <!-- Submit button -->
                        <button
                            type="submit"
                            class="group relative flex w-full items-center justify-center gap-3 overflow-hidden rounded-xl bg-gradient-to-r from-sepia-600 via-sepia-600 to-sepia-500 px-5 py-4 text-sm font-bold text-white shadow-lg shadow-sepia-950/50 transition duration-300 hover:-translate-y-0.5 hover:from-sepia-500 hover:via-sepia-500 hover:to-sepia-500 hover:shadow-sepia-900/50 focus:outline-none focus:ring-4 focus:ring-sepia-600/20 active:translate-y-0"
                        >
                            <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-[#F0EAE1] to-transparent transition duration-700 group-hover:translate-x-full"></span>
                            <span class="relative">Masuk ke Perpustakaan</span>
                            <svg class="relative h-4 w-4 transition duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M5 12h14m-6-6 6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>

                    <div class="mt-7 flex items-center gap-3">
                        <div class="h-px flex-1 bg-[#F0EAE1]"></div>
                        <span class="text-[9px] font-semibold tracking-[0.17em] text-[#7C6F62]">SMAKNIS LIBRARY</span>
                        <div class="h-px flex-1 bg-[#F0EAE1]"></div>
                    </div>

                    <p class="mt-5 text-center text-[11px] leading-5 text-[#97897B]">
                        Gunakan <span class="font-semibold text-[#7C6F62]">username</span> dan password akunmu untuk mengakses sistem perpustakaan.
                    </p>
                </div>

                <p class="mt-6 text-center text-[10px] tracking-wide text-[#7C6F62]">
                    &copy; {{ date('Y') }} Perpustakaan SMAKNIS. All rights reserved.
                </p>
            </div>
        </section>
    </main>
</div>

<!-- Toggle password visibility -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    if (!passwordInput || !toggleButton || !eyeIcon) {
        console.error('Elemen password atau tombol mata tidak ditemukan.');
        return;
    }

    toggleButton.addEventListener('click', function () {
        const showPassword = passwordInput.type === 'password';

        passwordInput.type = showPassword ? 'text' : 'password';

        toggleButton.setAttribute(
            'aria-label',
            showPassword ? 'Sembunyikan password' : 'Tampilkan password'
        );

        toggleButton.setAttribute('aria-pressed', String(showPassword));

        eyeIcon.innerHTML = showPassword
            ? '<path d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A10.7 10.7 0 0 1 12 5c6 0 9.5 7 9.5 7a16 16 0 0 1-3 3.8M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.5 7 9.5 7a10 10 0 0 0 4-.8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>'
            : '<path d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z" stroke-width="1.6" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke-width="1.6"/>';
    });
});
</script>

@endsection
