@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-1.5 flex-wrap">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-outline btn-sm" aria-disabled="true" style="opacity:.45;cursor:not-allowed">
                <x-ui.icon name="arrow-left" :size="15" /> <span class="hidden sm:inline">Previous</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-outline btn-sm" aria-label="Previous page">
                <x-ui.icon name="arrow-left" :size="15" /> <span class="hidden sm:inline">Previous</span>
            </a>
        @endif

        {{-- Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="w-9 h-9 flex items-center justify-center text-ink-400 text-[14px]" aria-hidden="true">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              class="w-9 h-9 rounded-lg bg-brand-600 text-white font-bold text-[13.5px] flex items-center justify-center">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" aria-label="Page {{ $page }}"
                           class="w-9 h-9 rounded-lg border border-ink-100 bg-white text-ink-700 font-semibold text-[13.5px]
                                  flex items-center justify-center hover:border-brand-400 hover:text-brand-700 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-outline btn-sm" aria-label="Next page">
                <span class="hidden sm:inline">Next</span> <x-ui.icon name="arrow-right" :size="15" />
            </a>
        @else
            <span class="btn btn-outline btn-sm" aria-disabled="true" style="opacity:.45;cursor:not-allowed">
                <span class="hidden sm:inline">Next</span> <x-ui.icon name="arrow-right" :size="15" />
            </span>
        @endif
    </nav>
@endif
