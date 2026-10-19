<aside
    id="sidebar"
    :class="{
        'md:w-20': collapsed,
        'md:w-64': !collapsed,
        'translate-x-0': mobileOpen,
        '-translate-x-full': !mobileOpen
    }"
    class="fixed inset-y-0 left-0 z-30 flex h-screen w-64 flex-shrink-0 flex-col
           border-r border-blue-100 bg-white text-slate-700 shadow-xl
           transition-all duration-300 ease-in-out
           md:sticky md:top-0 md:translate-x-0">

    <!-- HEADER -->
    <div
        :class="collapsed ? 'md:justify-center md:px-3' : ''"
        class="flex items-center gap-3 border-b border-slate-100
               bg-gradient-to-r from-blue-700 to-blue-600 px-5 py-5">

        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center
                    rounded-xl bg-white text-blue-700 shadow-md">
            <svg class="h-6 w-6" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5
                      S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18
                      7.5 18s3.332.477 4.5 1.253"/>
            </svg>
        </div>

        <div x-show="!collapsed" x-transition.opacity
             class="min-w-0 flex-1 whitespace-nowrap">
            <h1 class="text-lg font-extrabold tracking-wide text-white">
                SiPerpus
            </h1>
            <p class="mt-0.5 text-[10px] text-blue-100">
                Sistem Informasi Perpustakaan
            </p>
        </div>

        <button @click="mobileOpen = false" type="button"
                aria-label="Tutup menu"
                class="ml-auto flex h-8 w-8 flex-shrink-0 items-center
                       justify-center rounded-lg text-white
                       hover:bg-white/15 md:hidden">
            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- COLLAPSE BUTTON -->
    <button
        @click="collapsed = !collapsed"
        type="button"
        title="Ciutkan atau lebarkan sidebar"
        class="absolute -right-3 top-20 z-40 hidden h-7 w-7
               items-center justify-center rounded-full border
               border-slate-200 bg-white text-blue-700 shadow-md
               transition hover:bg-blue-50 md:flex">

        <svg x-show="!collapsed" class="h-4 w-4" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>

        <svg x-show="collapsed" class="h-4 w-4" fill="none"
             stroke="currentColor" viewBox="0 0 24 24"
             style="display:none">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    <!-- NAVIGATION -->
    <nav class="no-scrollbar flex-1 space-y-6 overflow-y-auto px-3 py-6 text-sm">

        <!-- DASHBOARD -->
        <div>
            <p x-show="!collapsed"
               class="mb-2 px-3 text-[10px] font-bold uppercase
                      tracking-[0.15em] text-slate-400">
                Menu Utama
            </p>

            <a href="{{ auth()->user()?->canManageLibrary() ? route('admin.dashboard') : route('guru.dashboard') }}"
               :class="collapsed ? 'justify-center px-0' : ''"
               title="Dashboard"
               class="group flex items-center gap-3 rounded-xl px-3.5 py-3
                      transition-all duration-200
                      {{ request()->routeIs('admin.dashboard', 'guru.dashboard')
                          ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-200'
                          : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">

                <svg class="h-5 w-5 flex-shrink-0" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2
                          a2 2 0 01-2 2H6a2 2 0 01-2-2V6z
                          M14 6a2 2 0 012-2h2a2 2 0 012 2v2
                          a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z
                          M4 16a2 2 0 012-2h2a2 2 0 012 2v2
                          a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z
                          M14 16a2 2 0 012-2h2a2 2 0 012 2v2
                          a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>

                <span x-show="!collapsed" class="flex-1 whitespace-nowrap">
                    Dashboard
                </span>
            </a>
        </div>

        <!-- DATA MASTER -->
        @if (auth()->user()?->canManageLibrary())
        <div>
            <p x-show="!collapsed"
               class="mb-2 px-3 text-[10px] font-bold uppercase
                      tracking-[0.15em] text-slate-400">
                Data Master
            </p>

            <div x-data="{
                open: {{ request()->routeIs(
                    'admin.buku.*',
                    'admin.kategori.*',
                    'admin.kelas.*',
                    'admin.penulis.*',
                    'admin.penerbit.*'
                ) ? 'true' : 'false' }}
            }">

                <!-- DROPDOWN BUTTON -->
                <button
                    @click="if (collapsed) { collapsed = false; open = true } else { open = !open }"
                    type="button"
                    :class="collapsed ? 'justify-center px-0' : ''"
                    title="Data Master"
                    class="flex w-full items-center gap-3 rounded-xl px-3.5 py-3
                           text-slate-600 transition-all duration-200
                           hover:bg-blue-50 hover:text-blue-700">

                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 5a2 2 0 012-2h12a2 2 0
                              012 2v14a2 2 0 01-2 2H6a2 2 0
                              01-2-2V5zm4 2h8M8 11h8M8 15h5"/>
                    </svg>

                    <span x-show="!collapsed"
                          class="flex-1 whitespace-nowrap text-left">
                        Data Master
                    </span>

                    <svg x-show="!collapsed"
                         :class="open ? 'rotate-180' : ''"
                         class="h-4 w-4 flex-shrink-0 transition-transform"
                         fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- DROPDOWN ITEMS -->
                <div x-show="open && !collapsed"
                     x-transition.opacity
                     class="ml-5 mt-1 space-y-1 border-l-2 border-blue-100 pl-3">

                    <a href="{{ route('admin.buku.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5
                              transition
                              {{ request()->routeIs('admin.buku.*')
                                  ? 'bg-blue-50 font-semibold text-blue-700'
                                  : 'text-slate-500 hover:bg-slate-50 hover:text-blue-700' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        <span>Data Buku</span>
                    </a>

                    <a href="{{ route('admin.kategori.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5
                              transition
                              {{ request()->routeIs('admin.kategori.*')
                                  ? 'bg-blue-50 font-semibold text-blue-700'
                                  : 'text-slate-500 hover:bg-slate-50 hover:text-blue-700' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        <span>Kategori</span>
                    </a>

                    <a href="{{ route('admin.kelas.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5
                              transition
                              {{ request()->routeIs('admin.kelas.*')
                                  ? 'bg-blue-50 font-semibold text-blue-700'
                                  : 'text-slate-500 hover:bg-slate-50 hover:text-blue-700' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        <span>Kelas</span>
                    </a>

                    <a href="{{ route('admin.penulis.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5
                              transition
                              {{ request()->routeIs('admin.penulis.*')
                                  ? 'bg-blue-50 font-semibold text-blue-700'
                                  : 'text-slate-500 hover:bg-slate-50 hover:text-blue-700' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        <span>Penulis</span>
                    </a>

                    <a href="{{ route('admin.penerbit.index') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5
                              transition
                              {{ request()->routeIs('admin.penerbit.*')
                                  ? 'bg-blue-50 font-semibold text-blue-700'
                                  : 'text-slate-500 hover:bg-slate-50 hover:text-blue-700' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        <span>Penerbit</span>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- TRANSAKSI -->
        @if (auth()->user()?->canManageLibrary())
        <div>
            <p x-show="!collapsed"
               class="mb-2 px-3 text-[10px] font-bold uppercase
                      tracking-[0.15em] text-slate-400">
                Transaksi
            </p>

            <div class="space-y-1">
                <a href="{{ route('admin.peminjaman.index') }}"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   title="Peminjaman"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-3
                          transition
                          {{ request()->routeIs('admin.peminjaman.*')
                              ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-200'
                              : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">

                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14
                              a2 2 0 002-2V7a2 2 0 00-2-2H5
                              a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>

                    <span x-show="!collapsed">Peminjaman</span>
                </a>

                <a href="{{ route('admin.request-peminjaman.index') }}"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   title="Request Peminjaman"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-3
                          transition
                          {{ request()->routeIs('admin.request-peminjaman.*')
                              ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-200'
                              : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">

                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>

                    <span x-show="!collapsed">Request Peminjaman</span>
                </a>

                <a href="{{ route('admin.pengembalian.index') }}"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   title="Pengembalian"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-3
                          transition
                          {{ request()->routeIs('admin.pengembalian.*')
                              ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-200'
                              : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">

                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 12l2 2 4-4m5 2a8 8 0
                              11-16 0 8 8 0 0116 0z"/>
                    </svg>

                    <span x-show="!collapsed">Pengembalian</span>
                </a>

                <a href="{{ route('admin.request-pengembalian.index') }}"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   title="Request Pengembalian"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-3
                          transition
                          {{ request()->routeIs('admin.request-pengembalian.*')
                              ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-200'
                              : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">

                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M16 15v-1a3 3 0 00-3-3H6a3 3 0 00-3 3v1m18 0v-1a3 3 0 00-3-3h-1m-4-3v6m0 0l-2-2m2 2l2-2"/>
                    </svg>

                    <span x-show="!collapsed">Request Pengembalian</span>
                </a>

                <a href="{{ route('admin.denda.index') }}"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   title="Denda"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-3
                          transition
                          {{ request()->routeIs('admin.denda.*')
                              ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-200'
                              : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">

                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2
                              3 2 3 .895 3 2-1.343 2-3 2m0-8V7
                              m0 1v8m0 0v1m9-5a9 9 0
                              11-18 0 9 9 0 0118 0z"/>
                    </svg>

                    <span x-show="!collapsed">Denda</span>
                </a>

                <a href="{{ route('admin.setting-denda.index') }}"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   title="Setting Denda"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-3
                          transition
                          {{ request()->routeIs('admin.setting-denda.*')
                              ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-200'
                              : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">

                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>

                    <span x-show="!collapsed">Setting Denda</span>
                </a>
            </div>
        </div>
        @endif

        <!-- MANAJEMEN -->
        @if (auth()->user()?->canManageLibrary())
        <div>
            <p x-show="!collapsed"
               class="mb-2 px-3 text-[10px] font-bold uppercase
                      tracking-[0.15em] text-slate-400">
                Manajemen
            </p>

            <div class="space-y-1">
                <a href="{{ route('admin.anggota.index') }}"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   title="Data Anggota"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-3
                          transition
                          {{ request()->routeIs('admin.anggota.*')
                              ? 'bg-blue-600 font-semibold text-white shadow-md shadow-blue-200'
                              : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">

                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15
                              21H3v-1a6 6 0 0112 0v1zm0 0h6v-1
                              a6 6 0 00-9-5.197"/>
                    </svg>

                    <span x-show="!collapsed">Data Anggota</span>
                </a>

                <a href="#"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   title="Laporan"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-3
                          text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">

                    <svg class="h-5 w-5 flex-shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0
                              00-2 2v6a2 2 0 002 2h2zm0 0V9
                              a2 2 0 012-2h2a2 2 0 012 2v10
                              m-6 0a2 2 0 002 2h2a2 2 0
                              002-2m0 0V5a2 2 0 012-2h2
                              a2 2 0 012 2v14"/>
                    </svg>

                    <span x-show="!collapsed">Laporan</span>
                </a>
            </div>
        </div>
        @endif
    </nav>

    <!-- FOOTER -->
    <div x-show="!collapsed"
         class="border-t border-slate-100 px-5 py-4">
        <p class="text-xs font-semibold text-slate-500">SiPerpus</p>
        <p class="mt-1 text-[10px] text-slate-400">
            Sistem Informasi Perpustakaan
        </p>
    </div>
</aside>