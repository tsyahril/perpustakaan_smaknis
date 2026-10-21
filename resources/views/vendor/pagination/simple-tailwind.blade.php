@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-[#97897B] bg-white border border-[#DDD5C9] cursor-default leading-5 rounded-md dark:text-[#7C6F62] dark:bg-[#FFFFFF] dark:border-[#B9AE9E]">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-[#4A4038] bg-white border border-[#DDD5C9] leading-5 rounded-md hover:text-[#97897B] focus:outline-none focus:ring ring-[#DDD5C9] focus:border-sepia-300 active:bg-[#F7F4EF] active:text-[#4A4038] transition ease-in-out duration-150 dark:bg-[#FFFFFF] dark:border-[#B9AE9E] dark:text-[#6B5E52] dark:focus:border-sepia-700 dark:active:bg-[#F0EAE1] dark:active:text-[#6B5E52]">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-[#4A4038] bg-white border border-[#DDD5C9] leading-5 rounded-md hover:text-[#97897B] focus:outline-none focus:ring ring-[#DDD5C9] focus:border-sepia-300 active:bg-[#F7F4EF] active:text-[#4A4038] transition ease-in-out duration-150 dark:bg-[#FFFFFF] dark:border-[#B9AE9E] dark:text-[#6B5E52] dark:focus:border-sepia-700 dark:active:bg-[#F0EAE1] dark:active:text-[#6B5E52]">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-[#97897B] bg-white border border-[#DDD5C9] cursor-default leading-5 rounded-md dark:text-[#7C6F62] dark:bg-[#FFFFFF] dark:border-[#B9AE9E]">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
