@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        <div class="flex gap-2 items-center justify-between sm:hidden">

            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-400 bg-white border border-slate-200 cursor-not-allowed rounded-xl dark:text-zinc-500 dark:bg-[#121214] dark:border-zinc-800/80">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all dark:bg-[#121214] dark:border-zinc-800/80 dark:text-zinc-300 dark:hover:text-primary hover:bg-slate-50 dark:hover:bg-zinc-900">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all dark:bg-[#121214] dark:border-zinc-800/80 dark:text-zinc-300 dark:hover:text-primary hover:bg-slate-50 dark:hover:bg-zinc-900">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-400 bg-white border border-slate-200 cursor-not-allowed rounded-xl dark:text-zinc-500 dark:bg-[#121214] dark:border-zinc-800/80">
                    {!! __('pagination.next') !!}
                </span>
            @endif

        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-center">

            <div>
                <span class="inline-flex shadow-sm rounded-xl border border-slate-200 dark:border-zinc-800/80 bg-white dark:bg-[#121214] overflow-hidden p-1 gap-1">

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center justify-center w-9 h-9 text-slate-300 bg-transparent cursor-not-allowed rounded-lg dark:text-zinc-600" aria-hidden="true">
                                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 text-slate-500 bg-transparent rounded-lg hover:text-slate-900 hover:bg-slate-100 focus:outline-none transition-colors dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-slate-400 bg-transparent cursor-default dark:text-zinc-500">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-white bg-primary rounded-lg cursor-default shadow-sm">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-slate-600 bg-transparent rounded-lg hover:text-slate-900 hover:bg-slate-100 focus:outline-none transition-colors dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 text-slate-500 bg-transparent rounded-lg hover:text-slate-900 hover:bg-slate-100 focus:outline-none transition-colors dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center justify-center w-9 h-9 text-slate-300 bg-transparent cursor-not-allowed rounded-lg dark:text-zinc-600" aria-hidden="true">
                                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
