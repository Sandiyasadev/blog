@if ($paginator->hasPages())
    <nav class="flex justify-center mt-8 gap-2" role="navigation" aria-label="Pagination Navigation">
        @if ($paginator->onFirstPage())
            <span class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 bg-white text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="flex items-end justify-center w-10 h-10 pb-2 text-gray-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="flex items-center justify-center w-10 h-10 rounded-full bg-primary text-white font-bold text-sm shadow-sm">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition-colors text-sm">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
        @else
            <span class="flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 bg-white text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </span>
        @endif
    </nav>
@endif
