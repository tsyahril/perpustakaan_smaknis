@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="flex h-10 items-center gap-1 rounded-lg px-3 text-sm text-slate-300"><x-member.ikon nama="chevron-left" kelas="h-4 w-4" /> Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex h-10 items-center gap-1 rounded-lg px-3 text-sm font-medium text-slate-600 hover:bg-white hover:shadow-kartu"><x-member.ikon nama="chevron-left" kelas="h-4 w-4" /> Sebelumnya</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-1 text-slate-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="flex h-10 min-w-[2.5rem] items-center justify-center rounded-lg bg-brand-600 px-3 text-sm font-bold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="flex h-10 min-w-[2.5rem] items-center justify-center rounded-lg px-3 text-sm font-medium text-slate-600 hover:bg-white hover:shadow-kartu" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex h-10 items-center gap-1 rounded-lg px-3 text-sm font-medium text-slate-600 hover:bg-white hover:shadow-kartu">Berikutnya <x-member.ikon nama="chevron-right" kelas="h-4 w-4" /></a>
        @else
            <span class="flex h-10 items-center gap-1 rounded-lg px-3 text-sm text-slate-300">Berikutnya <x-member.ikon nama="chevron-right" kelas="h-4 w-4" /></span>
        @endif
    </nav>
@endif
