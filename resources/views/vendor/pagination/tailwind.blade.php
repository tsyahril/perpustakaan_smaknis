@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex items-center justify-between gap-3">
        <p class="text-xs text-gray-500">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
            ({{ $paginator->total() }} data)
        </p>

        <div class="flex items-center gap-1">
            {{-- Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-default">&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100">&lsaquo;</a>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-1.5 text-sm text-gray-400">...</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1.5 text-sm font-semibold text-white bg-blue-700 border border-blue-700 rounded-lg">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-100">&rsaquo;</a>
            @else
                <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-default">&rsaquo;</span>
            @endif
        </div>
    </nav>
@endif
