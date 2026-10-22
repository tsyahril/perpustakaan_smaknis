@props(['judul', 'bukus', 'lihat' => null, 'subjudul' => null])

@if ($bukus->isNotEmpty())
    <section {{ $attributes->merge(['class' => 'wadah']) }} x-data="rak">
        <div class="mb-4 flex items-end justify-between gap-4">
            <div>
                <h2 class="judul-bagian">{{ $judul }}</h2>
                @if ($subjudul)
                    <p class="mt-0.5 text-sm text-slate-500">{{ $subjudul }}</p>
                @endif
            </div>
            @if ($lihat)
                <a href="{{ $lihat }}" class="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-brand-700 hover:text-brand-800">
                    Lihat semua <x-member.ikon nama="chevron-right" kelas="h-4 w-4" />
                </a>
            @endif
        </div>

        <div class="relative -mx-1">
            <button type="button" x-show="kiri" x-cloak @click="geser(-1)" aria-label="Geser ke kiri"
                    class="absolute -left-3 top-[38%] z-10 hidden h-10 w-10 items-center justify-center rounded-full bg-white shadow-kartu-naik ring-1 ring-slate-200 hover:bg-slate-50 md:flex">
                <x-member.ikon nama="chevron-left" />
            </button>

            <div x-ref="jalur" @scroll.passive="cek()" class="sembunyi-scroll flex snap-x snap-mandatory gap-4 overflow-x-auto px-1 pb-4 pt-1">
                @foreach ($bukus as $buku)
                    <div class="w-40 shrink-0 snap-start sm:w-44">
                        <x-member.buku-card :buku="$buku" />
                    </div>
                @endforeach
            </div>

            <button type="button" x-show="kanan" x-cloak @click="geser(1)" aria-label="Geser ke kanan"
                    class="absolute -right-3 top-[38%] z-10 hidden h-10 w-10 items-center justify-center rounded-full bg-white shadow-kartu-naik ring-1 ring-slate-200 hover:bg-slate-50 md:flex">
                <x-member.ikon nama="chevron-right" />
            </button>
        </div>
    </section>
@endif
