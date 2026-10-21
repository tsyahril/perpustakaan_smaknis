<header class="bg-white border-b border-gray-200 sticky top-0 z-20">
    <div class="flex items-center justify-between px-4 md:px-6 py-3">
        <div class="flex items-center gap-2">
            <button @click="mobileOpen = true" type="button" aria-label="Buka menu"
                class="md:hidden p-2 rounded-md text-gray-500 hover:text-blue-700 hover:bg-gray-100 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <h2 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
        </div>

        <div class="flex items-center gap-3 md:gap-4">
            <div class="relative hidden sm:block">
                <input type="text" placeholder="Cari buku, anggota..."
                    class="w-56 pl-9 pr-4 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-md border border-transparent focus:border-blue-700 focus:bg-white focus:outline-none">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="relative">
                <button id="profileButton" type="button"
                    class="flex items-center gap-3 pl-3 border-l border-gray-200 focus:outline-none">
                    <div class="w-9 h-9 rounded-full bg-blue-700 text-white font-semibold flex items-center justify-center text-sm flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-semibold text-gray-800 leading-tight">{{ auth()->user()->name ?? '' }}</p>
                        <span class="text-xs text-blue-700 font-medium">{{ auth()->user()?->aksesLabel() ?? '' }}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="profileDropdown"
                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name ?? '' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium text-blue-700 bg-blue-50 rounded">
                            {{ auth()->user()?->aksesLabel() ?? '' }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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
            if (!dropdown.classList.contains('hidden') && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') dropdown.classList.add('hidden');
        });
    })();
</script>
