@php
    $user = auth()->user();
    $isPerpus = $user?->canManageLibrary();
    $isAdmin = $user?->isAdmin();

    $dashboardRoute = $user?->isAdmin()
        ? 'admin.dashboard'
        : ($user?->isPustakawan() ? 'pustakawan.dashboard' : 'guru.dashboard');

    // Ikon SVG inline (dipakai saat sidebar mengecil / collapsed)
    $icons = [
        'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
        'master' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm4 2h8M8 11h8M8 15h5"/>',
        'peminjaman' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'pengembalian' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a8 8 0 11-16 0 8 8 0 0116 0z"/>',
        'denda' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1m9-5a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'anggota' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>',
        'laporan' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14"/>',
    ];

    // Sub-item Data Master
    $itemBuku = [
        ['label' => 'Data Buku', 'route' => 'admin.buku.index', 'active' => 'admin.buku.*'],
        ['label' => 'Kategori', 'route' => 'admin.kategori.index', 'active' => 'admin.kategori.*', 'adminOnly' => true],
        ['label' => 'Penulis', 'route' => 'admin.penulis.index', 'active' => 'admin.penulis.*', 'adminOnly' => true],
        ['label' => 'Penerbit', 'route' => 'admin.penerbit.index', 'active' => 'admin.penerbit.*', 'adminOnly' => true],
    ];

    $itemPengguna = [
        ['label' => 'Data Anggota', 'route' => 'admin.anggota.index', 'active' => 'admin.anggota.*', 'adminOnly' => true],
        ['label' => 'Kelas & Bagian', 'route' => 'admin.kelas.index', 'active' => 'admin.kelas.*', 'adminOnly' => true],
    ];

    $subMasterBuku = collect($itemBuku)->filter(fn ($i) => empty($i['adminOnly']) || $isAdmin)->values();
    $subMasterPengguna = collect($itemPengguna)->filter(fn ($i) => empty($i['adminOnly']) || $isAdmin)->values();
@endphp

<aside
    id="sidebar"
    :class="{
        'md:w-20': collapsed,
        'md:w-64': !collapsed,
        'translate-x-0': mobileOpen,
        '-translate-x-full': !mobileOpen
    }"
    class="fixed inset-y-0 left-0 z-30 flex h-screen w-64 flex-shrink-0 flex-col
           border-r border-[#E9E2D8] bg-white text-[#5A4D41]
           transition-all duration-300 ease-in-out
           md:sticky md:top-0 md:translate-x-0">

    {{-- HEADER SIDEBAR --}}
    <div
        :class="collapsed ? 'md:justify-center md:px-3' : ''"
        class="flex items-center gap-3 border-b border-[#F0EBE4] bg-white px-5 py-5">

        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center
                    rounded-xl bg-[#8B5E34] text-white shadow-[0_4px_12px_rgba(139,94,52,0.3)]">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5
                    S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18
                    7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5
                    c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18
                    16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>

        <div x-show="!collapsed" x-transition.opacity class="min-w-0 flex-1 whitespace-nowrap">
            <h1 class="text-lg font-extrabold tracking-wide text-[#2D251E]">SiPerpus</h1>
            <p class="mt-0.5 text-[10px] text-[#A89888]">Sistem Informasi Perpustakaan</p>
        </div>

        <button @click="mobileOpen = false" type="button" aria-label="Tutup menu"
            class="ml-auto flex h-8 w-8 flex-shrink-0 items-center justify-center
                   rounded-lg text-[#7A6A5B] transition hover:bg-[#F7F2EA] md:hidden">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- COLLAPSE BUTTON --}}
    <button @click="collapsed = !collapsed" type="button"
        title="Ciutkan atau lebarkan sidebar"
        class="absolute -right-3 top-20 z-40 hidden h-7 w-7 items-center
               justify-center rounded-full border border-[#E9E2D8] bg-white
               text-[#8B5E34] shadow-md transition hover:bg-[#F7F2EA] md:flex">
        <svg x-show="!collapsed" class="h-4 w-4" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M15 19l-7-7 7-7"/>
        </svg>
        <svg x-show="collapsed" class="h-4 w-4" fill="none"
            stroke="currentColor" viewBox="0 0 24 24" style="display:none">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    {{-- NAVIGATION --}}
    <nav class="no-scrollbar flex-1 space-y-6 overflow-y-auto px-3 py-6 text-sm">

        {{-- DASHBOARD --}}
        <div>
            <p x-show="!collapsed"
                class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-[#B5A695]">
                Menu Utama
            </p>

            <a href="{{ route($dashboardRoute) }}"
                :class="collapsed ? 'justify-center px-0' : ''"
                title="Dashboard"
                class="group flex items-center gap-3 rounded-xl px-3.5 py-3
                       transition-all duration-200
                       {{ request()->routeIs('admin.dashboard', 'pustakawan.dashboard', 'guru.dashboard')
                            ? 'bg-[#8B5E34] font-semibold text-white shadow-[0_4px_12px_rgba(139,94,52,0.3)]'
                            : 'text-[#5A4D41] hover:bg-[#F7F2EA] hover:text-[#2D251E]' }}">

                <svg class="h-5 w-5 flex-shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">{!! $icons['dashboard'] !!}</svg>

                <span x-show="!collapsed" class="flex-1 whitespace-nowrap">Dashboard</span>
            </a>
        </div>

        {{-- DATA MASTER: ADMIN DAN PUSTAKAWAN --}}
        @if ($isPerpus)
        <div>
            <p x-show="!collapsed"
                class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-[#B5A695]">
                Data Master
            </p>

            <div class="space-y-4">

                {{-- Kelompok 1: Data Buku --}}
                <div x-data="{
                    open: {{ request()->routeIs('admin.buku.*', 'admin.kategori.*', 'admin.penulis.*', 'admin.penerbit.*') ? 'true' : 'false' }}
                }">
                    <button
                        @click="if (collapsed) { collapsed = false; open = true } else { open = !open }"
                        type="button"
                        :class="collapsed ? 'justify-center px-0' : ''"
                        title="Data Buku"
                        class="flex w-full items-center gap-3 rounded-xl px-3.5 py-3
                               text-[#2D251E] transition-all duration-200
                               hover:bg-[#F7F2EA]">

                        <svg class="h-5 w-5 flex-shrink-0 text-[#8B5E34]" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">{!! $icons['master'] !!}</svg>

                        <span x-show="!collapsed" class="flex-1 whitespace-nowrap text-left text-[13px] font-semibold">
                            Data Buku
                        </span>

                        <svg x-show="!collapsed" :class="open ? 'rotate-180' : ''"
                            class="h-4 w-4 flex-shrink-0 text-[#A89888] transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open && !collapsed" x-transition.opacity
                        class="ml-5 mt-1 space-y-1 border-l-2 border-[#EFE8DE] pl-3">

                        @foreach ($subMasterBuku as $sub)
                            <a href="{{ route($sub['route']) }}"
                                class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition
                                {{ request()->routeIs($sub['active'])
                                    ? 'bg-[#8B5E34] font-semibold text-white'
                                    : 'text-[#5A4D41] hover:bg-[#F7F2EA] hover:text-[#2D251E]' }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                <span>{{ $sub['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Kelompok 2: Data Pengguna (Anggota, Kelas & Bagian) --}}
                @if ($isAdmin)
                <div x-data="{
                    open: {{ request()->routeIs('admin.anggota.*', 'admin.kelas.*') ? 'true' : 'false' }}
                }">
                    <button
                        @click="if (collapsed) { collapsed = false; open = true } else { open = !open }"
                        type="button"
                        :class="collapsed ? 'justify-center px-0' : ''"
                        title="Data Pengguna"
                        class="flex w-full items-center gap-3 rounded-xl px-3.5 py-3
                               text-[#2D251E] transition-all duration-200
                               hover:bg-[#F7F2EA]">

                        <svg class="h-5 w-5 flex-shrink-0 text-[#8B5E34]" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">{!! $icons['anggota'] !!}</svg>

                        <span x-show="!collapsed" class="flex-1 whitespace-nowrap text-left text-[13px] font-semibold">
                            Data Pengguna
                        </span>

                        <svg x-show="!collapsed" :class="open ? 'rotate-180' : ''"
                            class="h-4 w-4 flex-shrink-0 text-[#A89888] transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open && !collapsed" x-transition.opacity
                        class="ml-5 mt-1 space-y-1 border-l-2 border-[#EFE8DE] pl-3">

                        @foreach ($subMasterPengguna as $sub)
                            <a href="{{ route($sub['route']) }}"
                                class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition
                                {{ request()->routeIs($sub['active'])
                                    ? 'bg-[#8B5E34] font-semibold text-white'
                                    : 'text-[#5A4D41] hover:bg-[#F7F2EA] hover:text-[#2D251E]' }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                <span>{{ $sub['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endif

        {{-- TRANSAKSI: ADMIN DAN PUSTAKAWAN --}}
        @if ($isPerpus)
        <div>
            <p x-show="!collapsed"
                class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-[#B5A695]">
                Transaksi
            </p>

            <div class="space-y-1">

                <a href="{{ route('admin.peminjaman.index') }}"
                    :class="collapsed ? 'justify-center px-0' : ''"
                    title="Peminjaman"
                    class="flex items-center gap-3 rounded-xl px-3.5 py-3 transition
                    {{ request()->routeIs('admin.peminjaman.*')
                        ? 'bg-[#8B5E34] font-semibold text-white shadow-[0_4px_12px_rgba(139,94,52,0.3)]'
                        : 'text-[#5A4D41] hover:bg-[#F7F2EA] hover:text-[#2D251E]' }}">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">{!! $icons['peminjaman'] !!}</svg>
                    <span x-show="!collapsed">Peminjaman</span>
                </a>

                <a href="{{ route('admin.pengembalian.index') }}"
                    :class="collapsed ? 'justify-center px-0' : ''"
                    title="Pengembalian"
                    class="flex items-center gap-3 rounded-xl px-3.5 py-3 transition
                    {{ request()->routeIs('admin.pengembalian.*')
                        ? 'bg-[#8B5E34] font-semibold text-white shadow-[0_4px_12px_rgba(139,94,52,0.3)]'
                        : 'text-[#5A4D41] hover:bg-[#F7F2EA] hover:text-[#2D251E]' }}">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">{!! $icons['pengembalian'] !!}</svg>
                    <span x-show="!collapsed">Pengembalian</span>
                </a>

                <a href="{{ route('admin.denda.index') }}"
                    :class="collapsed ? 'justify-center px-0' : ''"
                    title="Denda"
                    class="flex items-center gap-3 rounded-xl px-3.5 py-3 transition
                    {{ request()->routeIs('admin.denda.*')
                        ? 'bg-[#8B5E34] font-semibold text-white shadow-[0_4px_12px_rgba(139,94,52,0.3)]'
                        : 'text-[#5A4D41] hover:bg-[#F7F2EA] hover:text-[#2D251E]' }}">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">{!! $icons['denda'] !!}</svg>
                    <span x-show="!collapsed">Denda</span>
                </a>

            </div>
        </div>
        @endif

        {{-- MANAJEMEN: KHUSUS ADMIN --}}
        @if ($isAdmin)
        <div>
            <p x-show="!collapsed"
                class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-[#B5A695]">
                Manajemen
            </p>

            <div class="space-y-1">
                <a href="#"
                    :class="collapsed ? 'justify-center px-0' : ''"
                    title="Laporan"
                    class="flex items-center gap-3 rounded-xl px-3.5 py-3
                           text-[#5A4D41] transition hover:bg-[#F7F2EA] hover:text-[#2D251E]">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">{!! $icons['laporan'] !!}</svg>
                    <span x-show="!collapsed">Laporan</span>
                </a>
            </div>
        </div>
        @endif
    </nav>

    {{-- FOOTER SIDEBAR --}}
    <div x-show="!collapsed" class="border-t border-[#F0EBE4] px-5 py-4">
        <p class="text-xs font-semibold text-[#2D251E]">SiPerpus</p>
        <p class="mt-1 text-[10px] text-[#A89888]">Sistem Informasi Perpustakaan</p>
    </div>
</aside>
