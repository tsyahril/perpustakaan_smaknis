@php
    $menu = [
        ['Akun saya', route('guru.dashboard'), 'user', request()->routeIs('guru.dashboard')],
        ['Pinjaman saya', route('pinjaman.index'), 'package', request()->routeIs('pinjaman.*')],
        ['Favorit', route('favorit.index'), 'heart', request()->routeIs('favorit.*')],
        ['Keranjang pinjam', route('keranjang.index'), 'cart', request()->routeIs('keranjang.*')],
    ];
@endphp

<nav aria-label="Menu akun" class="flex gap-1 overflow-x-auto rounded-2xl bg-white p-2 shadow-kartu ring-1 ring-slate-100 sembunyi-scroll lg:flex-col lg:overflow-visible">
    @foreach ($menu as [$label, $url, $ikon, $sedangAktif])
        <a href="{{ $url }}" @class([
            'flex shrink-0 items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition',
            'bg-brand-50 font-semibold text-brand-700' => $sedangAktif,
            'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! $sedangAktif,
        ])>
            <x-member.ikon :nama="$ikon" kelas="h-[18px] w-[18px]" />
            {{ $label }}
        </a>
    @endforeach
</nav>
