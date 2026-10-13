<aside id="sidebar" :class="{ 'md:w-20': collapsed, 'md:w-64': !collapsed, 'translate-x-0': mobileOpen, '-translate-x-full': !mobileOpen }"
       class="bg-blue-700 text-white flex-shrink-0 flex flex-col fixed md:static inset-y-0 left-0 z-30 md:z-auto md:translate-x-0 w-64 shadow-2xl transition-all duration-300 relative border-r border-blue-600">

    <!-- Brand / Logo Header -->
    <div :class="collapsed ? 'md:justify-center md:px-4' : ''" class="px-6 py-5 border-b border-blue-600/60 flex items-center gap-3.5 overflow-hidden">
        <div class="w-10 h-10 rounded-xl bg-white text-blue-700 flex-shrink-0 flex items-center justify-center font-bold text-xl shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">
            <h1 class="text-lg font-bold text-white tracking-wide leading-none">SiPerpus</h1>
            <p class="text-[11px] text-blue-200 mt-1">Sistem Informasi Perpustakaan</p>
        </div>
        <!-- Close (mobile drawer) -->
        <button @click="mobileOpen = false" type="button" aria-label="Tutup menu"
            class="md:hidden ml-auto w-7 h-7 flex-shrink-0 inline-flex items-center justify-center rounded-lg bg-blue-600 text-blue-100 hover:bg-blue-500 hover:text-white focus:outline-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Collapse toggle melayang di tepi sidebar (desktop) -->
    <button @click="collapsed = !collapsed" type="button" title="Ciutkan / lebarkan menu"
        class="hidden md:flex absolute -right-3 top-[4.5rem] w-6 h-6 items-center justify-center rounded-full bg-white text-blue-700 border border-gray-200 shadow hover:text-blue-900 hover:shadow-md focus:outline-none z-40">
        <svg x-show="!collapsed" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
        </svg>
        <svg x-show="collapsed" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-5 space-y-6 overflow-y-auto text-sm">
        
        <!-- MENU UTAMA -->
        <div>
            <p x-show="!collapsed" class="px-3 text-[10px] font-bold text-blue-200/70 uppercase tracking-wider mb-2">Menu Utama</p>
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white text-blue-800 font-bold shadow-md' : 'text-blue-100 hover:bg-blue-600 hover:text-white' }}"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   :title="collapsed ? 'Dashboard' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Dashboard</span>
                </a>

                <a href="#" 
                   class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-blue-100 hover:bg-blue-600 hover:text-white transition-all duration-200"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   :title="collapsed ? 'Data Buku' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Data Buku</span>
                </a>

                <a href="#" 
                   class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-blue-100 hover:bg-blue-600 hover:text-white transition-all duration-200"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   :title="collapsed ? 'Data Penulis' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Data Penulis</span>
                </a>
            </div>
        </div>

        <!-- TRANSAKSI -->
        <div>
            <p x-show="!collapsed" class="px-3 text-[10px] font-bold text-blue-200/70 uppercase tracking-wider mb-2">Transaksi</p>
            <div class="space-y-1">
                <a href="#" 
                   class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-blue-100 hover:bg-blue-600 hover:text-white transition-all duration-200"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   :title="collapsed ? 'Peminjaman' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Peminjaman</span>
                </a>

                <a href="#" 
                   class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-blue-100 hover:bg-blue-600 hover:text-white transition-all duration-200"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   :title="collapsed ? 'Denda' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Denda</span>
                </a>
            </div>
        </div>

        <!-- MANAJEMEN -->
        <div>
            <p x-show="!collapsed" class="px-3 text-[10px] font-bold text-blue-200/70 uppercase tracking-wider mb-2">Manajemen</p>
            <div class="space-y-1">
                <a href="{{ route('admin.anggota.index') }}" 
                   class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-blue-100 hover:bg-blue-600 hover:text-white transition-all duration-200"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   :title="collapsed ? 'Data Pengguna' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Data Pengguna</span>
                </a>

                <a href="#" 
                   class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-medium text-blue-100 hover:bg-blue-600 hover:text-white transition-all duration-200"
                   :class="collapsed ? 'justify-center px-0' : ''"
                   :title="collapsed ? 'Laporan' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Laporan</span>
                </a>
            </div>
        </div>
    </nav>
</aside>