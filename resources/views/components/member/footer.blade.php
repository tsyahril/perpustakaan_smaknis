<footer class="mt-16 bg-brand-950 text-slate-300" x-data="{ atas: false }" @scroll.window.passive="atas = window.scrollY > 600">
    <div class="wadah grid gap-10 py-12 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
        <div>
            <a href="{{ route('beranda') }}" class="inline-flex items-center gap-2.5 text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600">
                    <x-member.ikon nama="book-open" />
                </span>
                <span class="text-lg font-extrabold tracking-tight">SMAKNIS Library</span>
            </a>
            <p class="mt-4 max-w-sm text-sm leading-relaxed text-slate-400">
                Perpustakaan sekolah yang bisa dijelajahi dari mana saja. Cari bukunya, ajukan peminjaman, lalu ambil di perpustakaan.
            </p>
        </div>

        <div>
            <h3 class="text-sm font-bold text-white">Jelajahi</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('katalog.index') }}" class="hover:text-white">Semua katalog</a></li>
                <li><a href="{{ route('katalog.index', ['urut' => 'terbaru']) }}" class="hover:text-white">Buku terbaru</a></li>
                <li><a href="{{ route('katalog.index', ['urut' => 'populer']) }}" class="hover:text-white">Paling sering dipinjam</a></li>
                <li><a href="{{ route('katalog.index', ['tersedia' => 1]) }}" class="hover:text-white">Yang tersedia sekarang</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold text-white">Akun</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                @auth
                    <li><a href="{{ route('guru.dashboard') }}" class="hover:text-white">Akun saya</a></li>
                    <li><a href="{{ route('pinjaman.index') }}" class="hover:text-white">Pinjaman saya</a></li>
                    <li><a href="{{ route('favorit.index') }}" class="hover:text-white">Favorit</a></li>
                    <li><a href="{{ route('keranjang.index') }}" class="hover:text-white">Keranjang pinjam</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-white">Masuk</a></li>
                    <li><a href="{{ route('panduan') }}" class="hover:text-white">Cara mendapatkan akun</a></li>
                @endauth
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold text-white">Bantuan</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('panduan') }}" class="hover:text-white">Panduan peminjaman</a></li>
                <li><a href="{{ route('panduan') }}#pengembalian" class="hover:text-white">Cara mengembalikan buku</a></li>
                <li><a href="{{ route('panduan') }}#denda" class="hover:text-white">Aturan denda</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="wadah py-5 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} Perpustakaan SMAKNIS. Semua hak dilindungi.</p>
        </div>
    </div>

    <button type="button" x-show="atas" x-cloak x-transition @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-5 right-5 z-30 rounded-full bg-brand-600 p-3 text-white shadow-kartu-naik hover:bg-brand-700" aria-label="Kembali ke atas">
        <x-member.ikon nama="chevron-up" />
    </button>
</footer>
