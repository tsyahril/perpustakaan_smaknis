<header class="sticky top-0 z-20 border-b border-[#E8E1D7] bg-[#FAF9F7]/95 text-[#2D251E] shadow-lg shadow-black/5 backdrop-blur-xl">
    <div class="flex items-center justify-between gap-3 px-4 py-3 md:px-6">

        <div class="flex min-w-0 items-center gap-3">
            <button @click="mobileOpen = true" type="button" aria-label="Buka menu"
                class="rounded-xl border border-[#E8E1D7] bg-[#F0EAE1] p-2 text-[#6B5E52] transition hover:border-sepia-300/20 hover:bg-sepia-400/10 hover:text-sepia-800 focus:outline-none md:hidden">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="min-w-0">
                <p class="mb-0.5 text-[9px] font-bold uppercase tracking-[0.2em] text-sepia-700/70">
                    SMAKNIS DIGITAL LIBRARY
                </p>
                <h2 class="truncate text-base font-bold tracking-wide text-[#2D251E] md:text-lg">
                    @yield('title', 'Dashboard')
                </h2>
            </div>
        </div>

        <div class="flex flex-shrink-0 items-center gap-3 md:gap-5">

            <div class="relative hidden sm:block">
                <input
                    type="text"
                    placeholder="Cari buku, anggota..."
                    class="w-56 rounded-xl border border-[#E8E1D7] bg-[#FFFFFF] py-2 pl-9 pr-4 text-sm text-[#2D251E] outline-none transition placeholder:text-[#97897B] hover:border-[#D6CDC0] focus:border-sepia-400/70 focus:ring-4 focus:ring-sepia-600/10 md:w-56"
                    aria-label="Cari buku atau anggota"

                >
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-[#97897B] transition"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="relative">
                <button id="profileButton" type="button" aria-haspopup="true" aria-controls="profileDropdown"
                    class="flex items-center gap-2 border-l border-[#E8E1D7] pl-3 transition focus:outline-none md:gap-3 md:pl-4">

                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border border-sepia-300/20 bg-gradient-to-br from-sepia-500/25 to-sepia-900/30 text-sm font-bold text-sepia-700 shadow-lg shadow-sepia-950/20">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>

                    <div class="hidden text-left md:block">
                        <p class="max-w-36 truncate text-sm font-semibold leading-tight text-[#2D251E]">
                            {{ auth()->user()->name ?? '' }}
                        </p>
                        <span class="mt-1 block text-xs font-medium text-sepia-700">
                            {{ auth()->user()?->aksesLabel() ?? '' }}
                        </span>
                    </div>

                    <svg class="h-4 w-4 text-[#8B7E71] transition hover:text-sepia-800"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="profileDropdown"
                    class="absolute right-0 mt-3 hidden w-64 overflow-hidden rounded-2xl border border-[#E8E1D7] bg-[#FFFFFF] shadow-2xl shadow-black/5">

                    <div class="border-b border-[#E8E1D7] bg-gradient-to-br from-[#F0EAE1] to-[#FFFFFF] px-4 py-4">
                        <p class="truncate text-sm font-bold text-[#2D251E]">
                            {{ auth()->user()->name ?? '' }}
                        </p>
                        <p class="mt-1 truncate text-xs text-[#8B7E71]">
                            {{ auth()->user()->username ?? auth()->user()->email ?? '' }}
                        </p>
                        <span class="mt-3 inline-flex rounded-lg border border-sepia-300/15 bg-sepia-400/[0.08] px-2.5 py-1 text-xs font-semibold text-sepia-700">
                            {{ auth()->user()?->aksesLabel() ?? '' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="p-2">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-rose-700 transition hover:bg-rose-400/10 hover:text-rose-800">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    (function () {
        var button = document.getElementById('profileButton');
        var dropdown = document.getElementById('profileDropdown');

        if (!button || !dropdown) return;

        button.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.classList.contains('hidden') &&
                !dropdown.contains(e.target) &&
                !button.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') dropdown.classList.add('hidden');
        });
    })();
</script>


