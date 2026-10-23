@php
    $user = $sesi->user();
    $kataCari = is_string(request('q')) ? request('q') : '';
    $aktif = fn (string ...$rute) => request()->routeIs(...$rute);
    $pengingat = $sesi->pengingat();
    $tautanNav = [
        ['Beranda', route('beranda'), $aktif('beranda')],
        ['Katalog', route('katalog.index'), $aktif('katalog.*') && ! request()->hasAny(['urut', 'tersedia'])],
        ['Panduan', route('panduan'), $aktif('panduan')],
    ];
@endphp

<div class="hidden border-b border-slate-200 bg-slate-100/80 text-xs text-slate-600 md:block">
    <div class="wadah flex h-9 items-center justify-between">
        <p>Perpustakaan digital SMAKNIS &middot; cari, pinjam, dan pantau bukumu dari mana saja</p>
        <div class="flex items-center gap-5">
            <a href="{{ route('panduan') }}" class="hover:text-brand-700">Panduan peminjaman</a>
            <a href="{{ route('panduan') }}#denda" class="hover:text-brand-700">Aturan denda</a>
            @if ($user?->canManageLibrary())
                <a href="{{ $user->isAdmin() ? route('admin.dashboard') : route('pustakawan.dashboard') }}" class="font-semibold text-brand-700 hover:underline">Panel {{ $user->isAdmin() ? 'Admin' : 'Pustakawan' }}</a>
            @endif
        </div>
    </div>
</div>

<header class="sticky top-0 z-40 border-b border-slate-200 bg-white" x-data="{ menu: false, kategori: false, cariKategori: '' }" @keydown.escape.window="kategori = false; menu = false">
    <div class="wadah flex flex-wrap items-center gap-x-3 gap-y-2 py-3 lg:gap-x-5">

        <button type="button" class="-ml-1 rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" @click="menu = ! menu" :aria-expanded="menu" aria-label="Buka menu">
            <x-member.ikon nama="menu" x-show="! menu" />
            <x-member.ikon nama="x" x-show="menu" x-cloak />
        </button>

        <a href="{{ route('beranda') }}" class="flex shrink-0 items-center gap-2.5" aria-label="Beranda Perpustakaan SMAKNIS">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-white">
                <x-member.ikon nama="book-open" kelas="h-5 w-5" />
            </span>
            <span class="hidden leading-tight sm:block">
                <span class="block text-base font-extrabold tracking-tight text-brand-950">SMAKNIS</span>
                <span class="block text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-600">Library</span>
            </span>
        </a>

        {{-- Kotak cari: di layar kecil turun ke baris sendiri. --}}
        <form action="{{ route('katalog.index') }}" method="GET" role="search"
              class="relative order-last w-full lg:order-none lg:w-auto lg:flex-1"
              x-data="pencarian(@js(route('katalog.saran')), @js($kataCari))" @click.outside="tutup()">
            <label for="cari-global" class="sr-only">Cari buku, penulis, atau kategori</label>
            <input id="cari-global" type="search" name="q" x-model="q" @input="ketik()" @focus="ketik()"
                   @keydown.down.prevent="turun()" @keydown.up.prevent="naik()" @keydown.enter="kirim($event)" @keydown.escape="tutup()"
                   autocomplete="off" placeholder="Cari buku atau penulis..."
                   class="input !rounded-full !bg-slate-100 py-2.5 pl-11 pr-24 !ring-0 focus:!bg-white focus:!ring-2">
            <x-member.ikon nama="search" kelas="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 rounded-full bg-brand-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-brand-700">Cari</button>

            <div x-show="buka" x-cloak x-transition.opacity.duration.100ms
                 class="absolute inset-x-0 top-full z-50 mt-2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-kartu-naik">
                <template x-if="hasil.kategori.length">
                    <div class="border-b border-slate-100 p-2">
                        <p class="px-3 pb-1 pt-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Kategori</p>
                        <template x-for="(k, n) in hasil.kategori" :key="k.url">
                            <a :href="k.url" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-slate-50" :class="aktif === n && 'bg-slate-100'">
                                <x-member.ikon nama="layers" kelas="h-4 w-4 text-brand-600" />
                                <span x-text="k.nama"></span>
                            </a>
                        </template>
                    </div>
                </template>
                <div class="p-2">
                    <p class="px-3 pb-1 pt-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Buku</p>
                    <template x-for="(b, n) in hasil.buku" :key="b.url">
                        <a :href="b.url" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50" :class="aktif === hasil.kategori.length + n && 'bg-slate-100'">
                            <span class="h-12 w-8 shrink-0 overflow-hidden rounded bg-slate-200">
                                <img x-show="b.cover" :src="b.cover" alt="" class="h-full w-full object-cover">
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold text-slate-800" x-text="b.judul"></span>
                                <span class="block truncate text-xs text-slate-500" x-text="b.penulis"></span>
                            </span>
                            <span class="text-[11px] font-semibold" :class="b.tersedia ? 'text-emerald-600' : 'text-slate-400'" x-text="b.tersedia ? 'Tersedia' : 'Habis'"></span>
                        </a>
                    </template>
                </div>
                <button type="submit" class="flex w-full items-center justify-between border-t border-slate-100 bg-slate-50 px-5 py-2.5 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                    <span>Lihat semua hasil untuk "<span x-text="q"></span>"</span>
                    <x-member.ikon nama="arrow-right" kelas="h-4 w-4" />
                </button>
            </div>
        </form>

        <nav class="ml-auto flex items-center gap-1 sm:gap-2 lg:ml-0" aria-label="Akun">
            <a href="{{ route('favorit.index') }}" class="relative rounded-full p-2.5 text-slate-600 hover:bg-slate-100 hover:text-brand-700" aria-label="Favorit">
                <x-member.ikon nama="heart" kelas="h-[22px] w-[22px]" />
                <span class="absolute right-0.5 top-0.5 min-w-[18px] rounded-full bg-rose-500 px-1 text-center text-[10px] font-bold leading-[18px] text-white"
                      x-show="$store.member.favorit > 0" @if ($sesi->jumlahFavorit() === 0) style="display: none" @endif
                      x-text="$store.member.favorit">{{ $sesi->jumlahFavorit() }}</span>
            </a>

            <a href="{{ route('keranjang.index') }}" class="relative rounded-full p-2.5 text-slate-600 hover:bg-slate-100 hover:text-brand-700" aria-label="Keranjang pinjam">
                <x-member.ikon nama="cart" kelas="h-[22px] w-[22px]" />
                <span class="absolute right-0.5 top-0.5 min-w-[18px] rounded-full bg-brand-600 px-1 text-center text-[10px] font-bold leading-[18px] text-white"
                      x-show="$store.member.keranjang > 0" @if ($sesi->jumlahKeranjang() === 0) style="display: none" @endif
                      x-text="$store.member.keranjang">{{ $sesi->jumlahKeranjang() }}</span>
            </a>

            @if ($user)
                <div class="relative" x-data="{ buka: false }" @click.outside="buka = false" @keydown.escape="buka = false">
                    <button type="button" class="relative rounded-full p-2.5 text-slate-600 hover:bg-slate-100 hover:text-brand-700" @click="buka = ! buka" :aria-expanded="buka" aria-label="Pengingat">
                        <x-member.ikon nama="bell" kelas="h-[22px] w-[22px]" />
                        @if ($pengingat->isNotEmpty())
                            <span class="absolute right-1 top-1 h-2.5 w-2.5 rounded-full bg-amber-400 ring-2 ring-white"></span>
                        @endif
                    </button>
                    <div x-show="buka" x-cloak x-transition.origin.top.right
                         class="absolute right-0 top-full z-50 mt-2 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-kartu-naik">
                        <p class="border-b border-slate-100 px-4 py-3 text-sm font-bold text-slate-900">Pengingat</p>
                        @forelse ($pengingat as $p)
                            <a href="{{ $p['url'] }}" class="flex gap-3 border-b border-slate-50 px-4 py-3 last:border-0 hover:bg-slate-50">
                                <span @class([
                                    'mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full',
                                    'bg-red-50 text-red-600' => $p['tipe'] === 'terlambat',
                                    'bg-amber-50 text-amber-600' => $p['tipe'] === 'tempo',
                                    'bg-emerald-50 text-emerald-600' => $p['tipe'] === 'disetujui',
                                    'bg-slate-100 text-slate-500' => $p['tipe'] === 'ditolak',
                                ])>
                                    <x-member.ikon :nama="match ($p['tipe']) { 'terlambat' => 'alert-circle', 'tempo' => 'clock', 'disetujui' => 'check-circle', default => 'x' }" kelas="h-4 w-4" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-semibold text-slate-800">{{ $p['judul'] }}</span>
                                    <span class="block text-xs text-slate-500">{{ $p['detail'] }}</span>
                                </span>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-sm text-slate-500">Belum ada pengingat. Semua aman.</div>
                        @endforelse
                    </div>
                </div>

                <div class="relative ml-1 border-l border-slate-200 pl-2 sm:pl-3" x-data="{ buka: false }" @click.outside="buka = false" @keydown.escape="buka = false">
                    <button type="button" class="flex items-center gap-2 rounded-full p-1 pr-2 hover:bg-slate-100" @click="buka = ! buka" :aria-expanded="buka">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}</span>
                        <span class="hidden max-w-[120px] truncate text-sm font-semibold text-slate-700 md:block">{{ \Illuminate\Support\Str::before($user->name, ' ') }}</span>
                        <x-member.ikon nama="chevron-down" kelas="hidden h-4 w-4 text-slate-400 md:block" />
                    </button>
                    <div x-show="buka" x-cloak x-transition.origin.top.right
                         class="absolute right-0 top-full z-50 mt-2 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1 shadow-kartu-naik">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="truncate text-sm font-bold text-slate-900">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $user->aksesLabel() }}@if ($user->kelas) &middot; {{ $user->kelas }}@endif</p>
                        </div>
                        <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50"><x-member.ikon nama="user" kelas="h-4 w-4 text-slate-400" /> Akun saya</a>
                        <a href="{{ route('pinjaman.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50"><x-member.ikon nama="package" kelas="h-4 w-4 text-slate-400" /> Pinjaman saya</a>
                        <a href="{{ route('favorit.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50"><x-member.ikon nama="heart" kelas="h-4 w-4 text-slate-400" /> Favorit</a>
                        @if ($user->canManageLibrary())
                            <a href="{{ $user->isAdmin() ? route('admin.dashboard') : route('pustakawan.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-brand-700 hover:bg-brand-50"><x-member.ikon nama="layout" kelas="h-4 w-4" /> Panel {{ $user->isAdmin() ? 'admin' : 'pustakawan' }}</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50"><x-member.ikon nama="log-out" kelas="h-4 w-4" /> Keluar</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-primary ml-1 !px-5 !py-2">Masuk</a>
            @endif
        </nav>
    </div>

    {{-- Navigasi kedua (desktop) --}}
    <nav class="hidden border-t border-slate-100 lg:block" aria-label="Navigasi utama">
        <div class="wadah flex h-11 items-center gap-1 text-sm">
            @foreach ($tautanNav as [$label, $url, $sedangAktif])
                <a href="{{ $url }}" @class([
                    'rounded-md px-3 py-1.5 font-medium transition',
                    'bg-brand-50 font-semibold text-brand-700' => $sedangAktif,
                    'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $sedangAktif,
                ])>{{ $label }}</a>
            @endforeach

            @if ($kategoris->isNotEmpty())
                <span class="mx-1.5 h-4 w-px bg-slate-200"></span>

                <div @click.outside="kategori = false">
                    <button type="button" @click="kategori = ! kategori" :aria-expanded="kategori"
                            class="flex items-center gap-1.5 rounded-md px-3 py-1.5 font-medium transition"
                            :class="kategori
                                ? 'bg-brand-50 font-semibold text-brand-700'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'">
                        <x-member.ikon nama="grid" kelas="h-4 w-4" />
                        Kategori
                        <x-member.ikon nama="chevron-down" kelas="h-4 w-4 transition" ::class="kategori && 'rotate-180'" />
                    </button>

                    {{-- Panel semua kategori, turun di bawah header --}}
                    <div x-show="kategori" x-cloak x-transition.opacity.duration.150ms
                         class="absolute inset-x-0 top-full z-40 border-b border-slate-200 bg-white shadow-kartu-naik">
                        <div class="wadah py-5">
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <p class="text-sm font-bold text-slate-900">Semua kategori <span class="font-medium text-slate-400">({{ $kategoris->count() }})</span></p>
                                <input type="search" x-model="cariKategori" placeholder="Cari kategori..." class="input !w-64 !py-2" aria-label="Cari kategori">
                            </div>
                            <ul class="grid max-h-80 grid-cols-2 gap-x-6 gap-y-0.5 overflow-y-auto pr-2 lg:grid-cols-4">
                                @foreach ($kategoris as $k)
                                    <li x-show="@js(\Illuminate\Support\Str::lower($k->nama)).includes(cariKategori.toLowerCase().trim())">
                                        <a href="{{ route('katalog.index', ['kategori' => $k->id]) }}" class="flex items-center justify-between gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-700">
                                            <span class="truncate">{{ $k->nama }}</span>
                                            <span class="text-xs text-slate-400">{{ $k->bukus_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </nav>

    {{-- Menu seluler --}}
    <div x-show="menu" x-cloak x-transition class="max-h-[70vh] overflow-y-auto border-t border-slate-100 bg-white lg:hidden">
        <div class="wadah space-y-1 py-3">
            @foreach ($tautanNav as [$label, $url, $sedangAktif])
                <a href="{{ $url }}" @class([
                    'block rounded-lg px-3 py-2.5 text-sm font-medium',
                    'bg-brand-50 font-semibold text-brand-700' => $sedangAktif,
                    'text-slate-700 hover:bg-slate-50' => ! $sedangAktif,
                ])>{{ $label }}</a>
            @endforeach

            @if ($kategoris->isNotEmpty())
                <details class="rounded-lg">
                    <summary class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        <span class="flex items-center gap-2">
                            <x-member.ikon nama="grid" kelas="h-4 w-4 text-slate-400" />
                            Kategori
                        </span>
                        <x-member.ikon nama="chevron-down" kelas="h-4 w-4 text-slate-400" />
                    </summary>
                    <ul class="mt-1 grid grid-cols-2 gap-1 pl-2">
                        @foreach ($kategoris as $k)
                            <li><a href="{{ route('katalog.index', ['kategori' => $k->id]) }}" class="block truncate rounded-md px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ $k->nama }}</a></li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </div>
    </div>
</header>
