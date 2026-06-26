@extends('layouts.app')

@section('title', __('directory.title'))

@push('styles')
    <style>
        /* Ensure content is below nav */
        .directory-container {
            padding-top: 72px;
        }

        @media (min-width: 640px) {
            .directory-container {
                padding-top: 80px;
            }
        }

        @media (min-width: 1024px) {
            .directory-container {
                padding-top: 96px;
            }
        }

        /* Custom Scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }

        .dark .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #27272a;
        }

        /* Desktop: always show filter panel regardless of Alpine x-show state */
        @media (min-width: 1024px) {
            .filter-panel-wrapper {
                display: block !important;
                opacity: 1 !important;
                transform: none !important;
            }
            .filter-panel-wrapper[x-cloak] {
                display: block !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="directory-container relative min-h-screen bg-white dark:bg-[#0a0a0c] overflow-clip">

        <!-- Ambient Corner Glows -->
        <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
            <div
                class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full">
            </div>
            <div
                class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-primary/10 dark:bg-primary/10 blur-[140px] rounded-full">
            </div>
        </div>

        <form id="directory-form" action="{{ route('directory.index') }}" method="GET"
            class="relative flex flex-col min-h-screen" :class="filtersOpen ? 'z-[999999]' : 'z-10'" x-data="directoryForm()">

            {{-- Mobile Drawer Backdrop --}}
            <div x-show="filtersOpen" x-cloak
                @click="filtersOpen = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="lg:hidden fixed inset-0 bg-slate-900/50 dark:bg-black/60 backdrop-blur-sm z-[999998]">
            </div>

            {{-- Mobile Drawer Panel --}}
            <div x-show="filtersOpen" x-cloak
                x-init="$watch('filtersOpen', value => document.body.style.overflow = value ? 'hidden' : '')"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="ltr:-translate-x-full rtl:translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="ltr:-translate-x-full rtl:translate-x-full"
                class="lg:hidden fixed inset-y-0 start-0 w-[300px] max-w-[80vw] bg-white dark:bg-[#121214] shadow-2xl z-[999999] flex flex-col h-full border-e border-slate-100 dark:border-zinc-800/80">
                
                <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-zinc-800/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">
                            {{ __('directory.filters') ?? 'Filters' }}
                        </h2>
                    </div>
                    
                    <button type="button" @click="filtersOpen = false"
                        class="p-2 -me-2 text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-300 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-5 sidebar-scroll" data-lenis-prevent>
                    <div class="mb-4">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-zinc-500 mb-4">
                            {{ __('directory.categories') }}</h3>
                        <div class="space-y-4">
                            @foreach($categories as $category)
                                <label class="flex items-start gap-3.5 cursor-pointer group">
                                    <div class="relative flex items-center justify-center w-5 h-5 mt-0.5">
                                        <input type="checkbox" value="{{ $category->id }}" x-model="selectedCategories"
                                            @change="submitForm()"
                                            class="peer appearance-none w-5 h-5 border-2 border-slate-200 dark:border-zinc-700 rounded bg-transparent checked:bg-primary checked:border-primary transition-colors cursor-pointer">
                                        <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-[15px] font-semibold text-slate-600 dark:text-zinc-400 group-hover:text-primary transition-colors flex-1 leading-snug">
                                        {{ $category->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="max-w-[1400px] mx-auto w-full px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8 flex flex-col lg:flex-row items-start gap-4 sm:gap-6 lg:gap-8">

                <!-- Sidebar (Desktop only) -->
                <aside class="hidden lg:block w-[320px] flex-shrink-0 lg:sticky lg:top-28 z-20 self-start">
                    <div id="filter-panel" class="filter-panel-wrapper">
                        <div
                            class="bg-white dark:bg-[#121214] rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.06)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] border border-slate-100 dark:border-zinc-800/80 flex flex-col h-auto w-full">

                            <div class="flex items-center gap-3.5 mb-8">
                                <div
                                    class="w-10 h-10 rounded-[1rem] bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-black text-slate-900 dark:text-white">
                                    {{ __('directory.filters') ?? 'Filters' }}</h2>
                            </div>

                            <!-- Categories -->
                            <div class="mb-4 lg:mb-8">
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-zinc-500 mb-5">
                                    {{ __('directory.categories') }}</h3>
                                <div class="space-y-4">
                                    @foreach($categories as $category)
                                        <label class="flex items-start gap-3.5 cursor-pointer group">
                                            <div class="relative flex items-center justify-center w-5 h-5 mt-0.5">
                                                <input type="checkbox" value="{{ $category->id }}" x-model="selectedCategories"
                                                    @change="submitForm()"
                                                    class="peer appearance-none w-5 h-5 border-2 border-slate-200 dark:border-zinc-700 rounded bg-transparent checked:bg-primary checked:border-primary transition-colors cursor-pointer">
                                                <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <span
                                                class="text-[15px] font-semibold text-slate-600 dark:text-zinc-400 group-hover:text-primary transition-colors flex-1 leading-snug">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </aside>

                <!-- Main Content Area -->
                <main class="flex-1 flex flex-col min-w-0">

                    <!-- Search Bar -->
                    <div class="mb-4 sm:mb-6 lg:mb-8 relative z-[90]" x-data="directorySearch()">
                        <div
                            class="relative flex flex-col md:flex-row items-stretch md:items-center bg-white dark:bg-[#121214] border border-slate-200/80 dark:border-zinc-800/80 rounded-xl sm:rounded-2xl shadow-sm focus-within:border-primary/50 focus-within:ring-1 focus-within:ring-primary/50 overflow-visible">
                            <div class="flex-1 w-full flex items-center relative group p-2"
                                @click.away="showDropdown = false">
                                <svg class="w-6 h-6 ms-4 text-slate-400 dark:text-zinc-500 group-focus-within:text-primary transition-colors shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" name="search" x-model="query" x-ref="searchInput"
                                    @keydown.window.prevent.cmd.k="$refs.searchInput.focus()"
                                    @keydown.window.prevent.ctrl.k="$refs.searchInput.focus()" @input="fetchResults"
                                    @focus="query.length >= 2 ? showDropdown = true : null"
                                    @keydown.down.prevent="moveActive('down')" @keydown.up.prevent="moveActive('up')"
                                    @keydown.enter.prevent="selectActive()" @keydown.escape.prevent="showDropdown = false"
                                    placeholder="{{ trans('directory.search_placeholder') === 'directory.search_placeholder' ? 'Search companies, services...' : trans('directory.search_placeholder') }}"
                                    class="w-full bg-transparent border-0 py-2.5 sm:py-3 px-3 sm:px-4 text-sm sm:text-base font-medium text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-zinc-500 focus:ring-0"
                                    autocomplete="off">



                                <button type="button" x-show="query"
                                    @click="query = ''; results = { categories: [], locations: [], companies: [] }; showDropdown = false; submitForm();"
                                    class="me-2 text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300 transition-colors focus:outline-none shrink-0"
                                    x-cloak>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <button type="button" @click="if(query.trim()) { showSearchHint = false; showDropdown = false; submitForm() } else { showSearchHint = true; setTimeout(() => showSearchHint = false, 2500) }"
                                    class="hidden md:flex items-center justify-center w-11 h-11 bg-primary hover:bg-primary-light text-white rounded-xl shadow-lg shadow-primary/20 transition-all duration-300 active:scale-95 shrink-0 me-1"
                                    aria-label="Search">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>

                                {{-- Search hint message --}}
                                <div x-show="showSearchHint" x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute top-full left-0 right-0 mt-2 z-[99999]">
                                    <div class="flex items-center gap-2 px-4 py-2.5 bg-amber-50 dark:bg-amber-500/10 border border-amber-200/80 dark:border-amber-500/20 rounded-xl text-amber-700 dark:text-amber-400 text-sm font-semibold">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ __('directory.search_hint') }}</span>
                                    </div>
                                </div>

                                <!-- Search Dropdown -->
                                <div x-ref="dropdown" x-show="showDropdown" x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                    class="absolute top-full left-0 right-0 mt-3 bg-white dark:bg-[#121214]/95 backdrop-blur-2xl border border-slate-200 dark:border-zinc-800/80 rounded-2xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] overflow-y-auto max-h-[400px] sidebar-scroll scroll-smooth snap-y snap-mandatory scroll-p-3 z-[99999] text-left" data-lenis-prevent>
                                    <!-- Loading State -->
                                    <div x-show="loading" class="p-3 space-y-2">
                                        <div
                                            class="flex items-center gap-4 p-3 rounded-xl bg-slate-50/50 dark:bg-zinc-800/30 animate-pulse">
                                            <div class="w-12 h-12 rounded-xl bg-slate-200 dark:bg-zinc-700/50 shrink-0">
                                            </div>
                                            <div class="flex-1 space-y-2.5">
                                                <div class="h-3.5 bg-slate-200 dark:bg-zinc-700/50 rounded-lg w-2/3"></div>
                                                <div class="h-2.5 bg-slate-200 dark:bg-zinc-700/50 rounded-lg w-1/3"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- No Results -->
                                    <div x-show="!loading && query.length >= 2 && resultsCount === 0" x-cloak class="p-8 text-center flex flex-col items-center justify-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 dark:bg-zinc-800/50 mb-4 ring-4 ring-white dark:ring-[#121214] shadow-sm">
                                            <svg class="w-8 h-8 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-[15px] font-black text-slate-900 dark:text-white mb-2">{{ __('directory.search_no_results') ?? 'No results found' }}</h3>
                                        <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 max-w-[280px] mx-auto leading-relaxed">{{ __('directory.search_no_results_desc') ?? 'We couldn\'t find anything matching your search. Please try again with different keywords.' }}</p>
                                    </div>
                                    <!-- Results -->
                                    <div x-show="!loading && resultsCount > 0" x-ref="resultsList"
                                        class="p-3 space-y-4">

                                        <!-- Categories -->
                                        <template x-if="results.categories && results.categories.length > 0">
                                            <div>
                                                <div
                                                    class="px-3 py-1.5 text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">
                                                    {{ __('directory.categories') ?? 'Categories' }}
                                                </div>
                                                <div class="space-y-0.5 mt-1">
                                                    <template x-for="(item, idx) in results.categories"
                                                        :key="item.id + item.type">
                                                        <a :href="item.url" @click.prevent="selectResult(item)"
                                                            :data-active="flatResults.indexOf(item) === activeIndex ? 'true' : 'false'"
                                                            class="flex items-center gap-3.5 p-2.5 rounded-xl transition-all duration-150 group border border-transparent hover:bg-slate-50 dark:hover:bg-zinc-800/40 text-slate-700 dark:text-zinc-300 snap-start"
                                                            :class="{'bg-slate-50 dark:bg-zinc-800/60 border-slate-200/60 dark:border-zinc-700/60': flatResults.indexOf(item) === activeIndex}">
                                                            <div
                                                                class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 dark:bg-zinc-800/50 text-slate-500 dark:text-zinc-400 group-hover:bg-primary group-hover:text-white transition-colors shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                                </svg>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-primary transition-colors"
                                                                    x-html="highlight(item.name)"></h4>
                                                            </div>
                                                            <svg class="w-4 h-4 text-slate-300 dark:text-zinc-600 opacity-0 group-hover:opacity-100 transition-opacity rtl:rotate-180 shrink-0"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Locations -->
                                        <template x-if="results.locations && results.locations.length > 0">
                                            <div>
                                                <div
                                                    class="px-3 py-1.5 text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">
                                                    {{ __('directory.locations') ?? 'Locations' }}
                                                </div>
                                                <div class="space-y-0.5 mt-1">
                                                    <template x-for="(item, idx) in results.locations"
                                                        :key="item.id + item.type">
                                                        <a :href="item.url" @click.prevent="selectResult(item)"
                                                            :data-active="flatResults.indexOf(item) === activeIndex ? 'true' : 'false'"
                                                            class="flex items-center gap-3.5 p-2.5 rounded-xl transition-all duration-150 group border border-transparent hover:bg-slate-50 dark:hover:bg-zinc-800/40 text-slate-700 dark:text-zinc-300 snap-start"
                                                            :class="{'bg-slate-50 dark:bg-zinc-800/60 border-slate-200/60 dark:border-zinc-700/60': flatResults.indexOf(item) === activeIndex}">
                                                            <div
                                                                class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 dark:bg-zinc-800/50 text-slate-500 dark:text-zinc-400 group-hover:bg-primary group-hover:text-white transition-colors shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                </svg>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-primary transition-colors"
                                                                    x-html="highlight(item.name)"></h4>
                                                            </div>
                                                            <svg class="w-4 h-4 text-slate-300 dark:text-zinc-600 opacity-0 group-hover:opacity-100 transition-opacity rtl:rotate-180 shrink-0"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Companies -->
                                        <template x-if="results.companies && results.companies.length > 0">
                                            <div>
                                                <div
                                                    class="px-3 py-1.5 text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">
                                                    {{ __('directory.company') ?? 'Companies' }}
                                                </div>
                                                <div class="space-y-0.5 mt-1">
                                                    <template x-for="(item, idx) in results.companies"
                                                        :key="item.id + item.type">
                                                        <a :href="item.url" @click.prevent="selectResult(item)"
                                                            :data-active="flatResults.indexOf(item) === activeIndex ? 'true' : 'false'"
                                                            class="flex items-center gap-3.5 p-2.5 rounded-xl transition-all duration-150 group border border-transparent hover:bg-slate-50 dark:hover:bg-zinc-800/40 text-slate-700 dark:text-zinc-300 snap-start"
                                                            :class="{'bg-slate-50 dark:bg-zinc-800/60 border-slate-200/60 dark:border-zinc-700/60': flatResults.indexOf(item) === activeIndex}">

                                                            <div
                                                                class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-zinc-800/50 border-2 border-white dark:border-[#121214] shadow-sm flex items-center justify-center overflow-hidden shrink-0 transition-transform group-hover:scale-105">
                                                                <template x-if="item.logo">
                                                                    <img :src="item.logo" :alt="item.name"
                                                                        class="w-full h-full object-cover">
                                                                </template>
                                                                <template x-if="!item.logo">
                                                                    <span
                                                                        class="text-xs font-black text-slate-400 dark:text-zinc-500 uppercase"
                                                                        x-text="item.name.substring(0,2)"></span>
                                                                </template>
                                                            </div>

                                                            <div class="flex-1 min-w-0">
                                                                <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-primary transition-colors"
                                                                    x-html="highlight(item.name)"></h4>
                                                                <template x-if="item.category">
                                                                    <p class="text-xs font-medium text-slate-500 dark:text-zinc-400 truncate mt-0.5"
                                                                        x-text="item.category"></p>
                                                                </template>
                                                            </div>
                                                            <svg class="w-4 h-4 text-slate-300 dark:text-zinc-600 opacity-0 group-hover:opacity-100 transition-opacity rtl:rotate-180 shrink-0"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <div class="mt-2 pt-2 border-t border-slate-100 dark:border-zinc-800/50 snap-start">
                                            <button type="button" @click="showDropdown = false; submitForm()"
                                                class="w-full flex items-center justify-center gap-2 p-3 text-sm font-bold text-primary hover:bg-primary/5 dark:hover:bg-primary/10 rounded-xl transition-colors">
                                                {{ __('directory.view_all_results') }} "<span x-text="query"></span>"
                                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Bar -->
                    <div class="mb-4 sm:mb-6 z-[80]" x-data="{ countryOpen: false, sortOpen: false }">
                        {{-- Buttons Row --}}
                        <div class="flex items-center gap-2 sm:gap-3">

                            {{-- Categories Toggle (Left) — only visible on mobile --}}
                            <button type="button" @click="filtersOpen = !filtersOpen; countryOpen = false; sortOpen = false;"
                                class="lg:hidden flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 text-xs font-bold bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800/80 rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-zinc-900 focus:outline-none transition-all"
                                :class="filtersOpen ? 'text-primary border-primary/40 bg-primary/5 dark:bg-primary/10' : 'text-slate-600 dark:text-zinc-400'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span class="hidden sm:inline">{{ __('directory.categories') ?? 'Categories' }}</span>
                                <template x-if="selectedCategories.length > 0">
                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-primary text-white text-[9px] font-black leading-none" x-text="selectedCategories.length"></span>
                                </template>
                                <svg class="w-3 h-3 transition-transform duration-200"
                                    :class="filtersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div class="flex-1 lg:hidden"></div>

                            {{-- Country Filter (with its own relative wrapper for dropdown) --}}
                            <div class="relative">
                                <button type="button" @click="countryOpen = !countryOpen; sortOpen = false; filtersOpen = false;"
                                    class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 text-xs font-bold text-slate-600 dark:text-zinc-400 bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800/80 rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-zinc-900 focus:outline-none transition-all"
                                    :class="countryOpen ? 'border-primary/40 text-primary bg-primary/5 dark:bg-primary/10' : ''">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="hidden sm:inline"
                                        x-text="selectedCountries.length > 0 ? selectedCountries.length + ' {{ __('directory.country') ?? 'Country' }}' : '{{ __('directory.country') ?? 'Country' }}'"></span>
                                    <template x-if="selectedCountries.length > 0">
                                        <span class="sm:hidden inline-flex items-center justify-center w-4 h-4 rounded-full bg-primary text-white text-[9px] font-black leading-none" x-text="selectedCountries.length"></span>
                                    </template>
                                    <svg class="w-3 h-3 transition-transform duration-200"
                                        :class="countryOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                {{-- Country Dropdown --}}
                                <div x-show="countryOpen" x-cloak @click.away="countryOpen = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute end-0 lg:start-0 mt-2 w-56 origin-top-end lg:origin-top-start bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800/80 rounded-xl shadow-lg focus:outline-none z-50 overflow-hidden">
                                    <div class="max-h-64 overflow-y-auto p-2 space-y-1 sidebar-scroll" data-lenis-prevent>
                                        @foreach($countries as $country)
                                            <label
                                                class="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-slate-50 dark:hover:bg-zinc-800/50 rounded-lg transition-colors group">
                                                <div class="relative flex items-center justify-center w-4 h-4 shrink-0">
                                                    <input type="checkbox" value="{{ $country->id }}" x-model="selectedCountries"
                                                        @change="submitForm()"
                                                        class="peer appearance-none w-4 h-4 border-2 border-slate-200 dark:border-zinc-700 rounded bg-transparent checked:bg-primary checked:border-primary transition-colors cursor-pointer">
                                                    <svg class="absolute w-2.5 h-2.5 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="text-sm font-semibold text-slate-700 dark:text-zinc-300 group-hover:text-primary transition-colors truncate">{{ $country->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="p-2 border-t border-slate-100 dark:border-zinc-800/50 bg-slate-50/50 dark:bg-zinc-900/50"
                                        x-show="selectedCountries.length > 0">
                                        <button type="button" @click="selectedCountries = []; submitForm();"
                                            class="w-full py-1.5 text-xs font-bold text-slate-500 hover:text-slate-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors">
                                            {{ __('directory.clear_filters') ?? 'Clear' }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Sort (with its own relative wrapper for dropdown) --}}
                            <div class="relative">
                                <button type="button" @click="sortOpen = !sortOpen; countryOpen = false; filtersOpen = false;"
                                    class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 text-xs font-bold text-slate-600 dark:text-zinc-400 bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800/80 rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-zinc-900 focus:outline-none transition-all"
                                    :class="sortOpen ? 'border-primary/40 text-primary bg-primary/5 dark:bg-primary/10' : ''">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                    </svg>
                                    <span class="hidden sm:inline"
                                        x-text="selectedSort === 'newest' ? '{{ __('directory.sort_newest') ?? 'Newest' }}' : (selectedSort === 'oldest' ? '{{ __('directory.sort_oldest') ?? 'Oldest' }}' : (selectedSort === 'a-z' ? '{{ __('directory.sort_az') ?? 'Name (A-Z)' }}' : '{{ __('directory.sort_za') ?? 'Name (Z-A)' }}'))"></span>
                                    <svg class="w-3 h-3 transition-transform duration-200"
                                        :class="sortOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                {{-- Sort Dropdown --}}
                                <div x-show="sortOpen" x-cloak @click.away="sortOpen = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute end-0 lg:start-0 mt-2 w-44 origin-top-end lg:origin-top-start bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800/80 rounded-xl shadow-lg focus:outline-none z-50 overflow-hidden">
                                    <div class="py-1">
                                        <button type="button" @click="selectedSort = 'newest'; submitForm(); sortOpen = false"
                                            :class="{'bg-primary/5 text-primary': selectedSort === 'newest', 'text-slate-600 dark:text-zinc-400': selectedSort !== 'newest'}"
                                            class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                            {{ __('directory.sort_newest') ?? 'Newest' }}
                                        </button>
                                        <button type="button" @click="selectedSort = 'oldest'; submitForm(); sortOpen = false"
                                            :class="{'bg-primary/5 text-primary': selectedSort === 'oldest', 'text-slate-600 dark:text-zinc-400': selectedSort !== 'oldest'}"
                                            class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                            {{ __('directory.sort_oldest') ?? 'Oldest' }}
                                        </button>
                                        <button type="button" @click="selectedSort = 'a-z'; submitForm(); sortOpen = false"
                                            :class="{'bg-primary/5 text-primary': selectedSort === 'a-z', 'text-slate-600 dark:text-zinc-400': selectedSort !== 'a-z'}"
                                            class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                            {{ __('directory.sort_az') ?? 'Name (A-Z)' }}
                                        </button>
                                        <button type="button" @click="selectedSort = 'z-a'; submitForm(); sortOpen = false"
                                            :class="{'bg-primary/5 text-primary': selectedSort === 'z-a', 'text-slate-600 dark:text-zinc-400': selectedSort !== 'z-a'}"
                                            class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                            {{ __('directory.sort_za') ?? 'Name (Z-A)' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Companies Grid Fragment -->
                    @fragment('business-grid')
                        <div id="business-grid-container" class="relative" data-current-page="{{ $businesses->currentPage() }}"
                            data-last-page="{{ $businesses->lastPage() }}"
                            data-has-more-pages="{{ $businesses->hasMorePages() ? 'true' : 'false' }}">

                            <!-- Loading Overlay -->
                            <div x-show="loadingGrid" x-transition.opacity
                                class="absolute inset-0 z-50 bg-white/60 dark:bg-[#0a0a0c]/60 backdrop-blur-sm flex items-start justify-center pt-20 rounded-3xl"
                                style="display: none;">
                                <div
                                    class="bg-white dark:bg-zinc-900 p-4 rounded-2xl shadow-xl border border-slate-200/50 dark:border-zinc-800/50 flex items-center gap-3">
                                    <div class="w-6 h-6 border-2 border-primary/30 border-t-primary rounded-full animate-spin">
                                    </div>
                                    <span class="text-sm font-bold text-slate-700 dark:text-zinc-300">{{ __('directory.loading') ?? 'Loading...' }}</span>
                                </div>
                            </div>

                            @if($businesses->count() > 0)
                                <div id="business-items-container"
                                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 lg:gap-6 relative z-40">
                                    @fragment('business-items')
                                        @foreach($businesses as $business)
                                            <div
                                                class="reveal luxury-card group relative flex flex-col h-full bg-white dark:bg-[#09090b] rounded-2xl sm:rounded-[1.5rem] overflow-hidden border border-slate-200/60 dark:border-zinc-800/60 transition-all duration-300 hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-1 z-10">

                                                {{-- Cover Image --}}
                                                <div
                                                    class="h-28 sm:h-32 md:h-36 lg:h-40 bg-slate-100 dark:bg-zinc-800 relative overflow-hidden shrink-0 border-b border-slate-100 dark:border-zinc-800/50">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent z-10"></div>
                                                    @if($business->cover)
                                                        <img src="{{ $business->cover_url }}" alt="{{ $business->name }}" loading="lazy"
                                                            decoding="async"
                                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                                    @else
                                                        <img src="{{ asset('images/home-background.webp') }}" alt="" loading="lazy"
                                                            decoding="async"
                                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                                                    @endif
                                                </div>

                                                {{-- Card Content --}}
                                                <div class="p-4 sm:p-5 md:p-6 lg:p-8 relative flex-1 flex flex-col">

                                                    {{-- SaaS Logo Avatar --}}
                                                    <div
                                                        class="absolute -top-8 sm:-top-9 md:-top-10 start-4 sm:start-5 md:start-6 lg:start-8 w-14 h-14 sm:w-15 sm:h-15 md:w-16 md:h-16 rounded-xl sm:rounded-2xl bg-white dark:bg-zinc-900 border-[3px] sm:border-4 border-white dark:border-[#09090b] shadow-sm flex items-center justify-center overflow-hidden z-20 transition-transform duration-300 group-hover:-translate-y-1">
                                                        @if($business->logo)
                                                            <img src="{{ $business->logo_url }}" alt="{{ $business->name }} logo" loading="lazy"
                                                                decoding="async" class="w-full h-full object-cover">
                                                        @else
                                                            <div
                                                                class="w-full h-full bg-primary/10 text-primary flex items-center justify-center font-[900] text-xl uppercase">
                                                                {{ mb_substr($business->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="mt-6 sm:mt-7 md:mt-8 flex-1 flex flex-col">
                                                        <div class="flex items-center justify-between gap-4 mb-2">
                                                            <div class="flex items-center gap-2">
                                                                <h3
                                                                    class="text-base sm:text-lg md:text-xl font-bold text-slate-900 dark:text-white line-clamp-1 group-hover:text-primary transition-colors duration-300">
                                                                    {{ $business->name }}</h3>
                                                                @if($business->is_claimed)
                                                                    <div title="{{ __('directory.verified') }}"
                                                                        class="text-blue-500 shrink-0 bg-blue-50 dark:bg-blue-500/10 p-1.5 rounded-full flex items-center justify-center shadow-sm">
                                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd"
                                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                                clip-rule="evenodd" />
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div
                                                                class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-50 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 opacity-0 -translate-x-2 rtl:translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 rtl:group-hover:translate-x-0 transition-all duration-300 shrink-0">
                                                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 line-clamp-2 mb-4 sm:mb-5 md:mb-6 font-medium leading-relaxed flex-1 relative z-40 selection:bg-primary/20 cursor-text"
                                                            @click.stop>
                                                            {{ $business->description ?? '...' }}
                                                        </p>

                                                        {{-- SaaS Tags --}}
                                                        <div
                                                            class="flex items-center gap-1.5 sm:gap-2 flex-wrap pt-3 sm:pt-4 md:pt-5 border-t border-slate-100 dark:border-zinc-800/80 mt-auto">
                                                            @if($business->category)
                                                                <span
                                                                    class="px-3 py-1 rounded-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200/60 dark:border-zinc-800/60 text-xs font-semibold text-slate-600 dark:text-zinc-400 transition-colors group-hover:border-primary/30 group-hover:text-primary dark:group-hover:text-primary-light">
                                                                    {{ $business->category->name }}
                                                                </span>
                                                            @endif
                                                            @if($business->city)
                                                                <span
                                                                    class="px-3 py-1 rounded-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200/60 dark:border-zinc-800/60 text-xs font-semibold text-slate-600 dark:text-zinc-400 transition-colors group-hover:border-primary/30 group-hover:text-primary dark:group-hover:text-primary-light">
                                                                    {{ $business->city->name }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <a href="{{ route('directory.business.view', $business->slug) }}"
                                                    class="absolute inset-0 z-30 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-inset rounded-2xl sm:rounded-[1.5rem]"
                                                    aria-label="{{ __('directory.view_profile') }} {{ $business->name }}"></a>
                                            </div>
                                        @endforeach
                                    @endfragment
                                </div>

                                <!-- Smart Pagination -->
                                @if($businesses->hasPages())
                                    <div class="mt-6 sm:mt-8 md:mt-10 lg:mt-12 flex justify-center w-full relative z-40" @click.prevent="
                                                let target = $event.target.tagName === 'A' ? $event.target : $event.target.closest('a');
                                                if(target && target.href) { 
                                                    const url = new URL(target.href);
                                                    const page = url.searchParams.get('page');
                                                    if(page) submitForm(page); 
                                                }
                                             ">
                                        {{ $businesses->appends(request()->query())->links('vendor.pagination.tailwind') }}
                                    </div>
                                @endif
                            @else
                                <!-- Empty State -->
                                <div
                                    class="flex flex-col items-center justify-center py-12 sm:py-16 md:py-20 px-4 text-center border border-dashed border-slate-200 dark:border-zinc-800/80 rounded-2xl sm:rounded-[24px]">
                                    <div
                                        class="w-16 h-16 bg-slate-50 dark:bg-zinc-800/50 rounded-2xl flex items-center justify-center mb-6 ring-1 ring-slate-100 dark:ring-white/5">
                                        <svg class="w-8 h-8 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                                        {{ __('directory.no_results') }}</h3>
                                    <p class="text-sm text-slate-500 dark:text-zinc-400 font-medium max-w-md mb-8">
                                        {{ __('directory.try_different') }}</p>

                                    <a href="{{ route('directory.index') }}"
                                        class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-800 dark:hover:bg-slate-100 transition-all">
                                        {{ __('directory.clear_filters') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endfragment

                </main>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('directoryForm', () => {
                let initialCategories = @js(request('category'));
                let initialCountries = @js(request('country'));

                if (typeof initialCategories === 'string' && initialCategories) initialCategories = initialCategories.split(',');
                else if (!Array.isArray(initialCategories)) initialCategories = [];

                if (typeof initialCountries === 'string' && initialCountries) initialCountries = initialCountries.split(',');
                else if (!Array.isArray(initialCountries)) initialCountries = [];

                return {
                    selectedCountries: initialCountries.map(String),
                    selectedCategories: initialCategories.map(String),
                    selectedSort: @js(request('sort', 'newest')),

                    currentPage: @js($businesses->currentPage()),
                    lastPage: @js($businesses->lastPage()),
                    hasMorePages: @js($businesses->hasMorePages()),

                    filtersOpen: false,
                    loadingGrid: false,
                    loadingMore: false,

                    async submitForm(page = 1) {
                        this.loadingGrid = true;
                        const url = new URL('{{ route('directory.index') }}');
                        if (this.selectedCountries.length > 0) url.searchParams.set('country', this.selectedCountries.join(','));
                        if (this.selectedCategories.length > 0) url.searchParams.set('category', this.selectedCategories.join(','));
                        if (this.selectedSort) url.searchParams.set('sort', this.selectedSort);
                        const searchInput = document.querySelector('input[name=\'search\']');
                        if (searchInput && searchInput.value) url.searchParams.set('search', searchInput.value);

                        if (page > 1) {
                            url.searchParams.set('page', page);
                        }

                        window.history.pushState({}, '', url);

                        try {
                            const res = await fetch(url, {
                                headers: {
                                    'X-Alpine-Request': 'true',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const html = await res.text();
                                const container = document.getElementById('business-grid-container');
                                if (container) {
                                    container.outerHTML = html;
                                    this.updatePaginationState(html);
                                    if (page > 1) {
                                        window.scrollTo({ top: 0, behavior: 'smooth' });
                                    }
                                }
                            }
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.loadingGrid = false;
                        }
                    },

                    async loadMore() {
                        if (!this.hasMorePages || this.loadingMore) return;
                        this.loadingMore = true;
                        this.currentPage++;

                        const url = new URL('{{ route('directory.index') }}');
                        if (this.selectedCountries.length > 0) url.searchParams.set('country', this.selectedCountries.join(','));
                        if (this.selectedCategories.length > 0) url.searchParams.set('category', this.selectedCategories.join(','));
                        if (this.selectedSort) url.searchParams.set('sort', this.selectedSort);
                        const searchInput = document.querySelector('input[name=\'search\']');
                        if (searchInput && searchInput.value) url.searchParams.set('search', searchInput.value);

                        url.searchParams.set('page', this.currentPage);
                        url.searchParams.set('append', 'true');

                        try {
                            const res = await fetch(url, {
                                headers: {
                                    'X-Alpine-Request': 'true',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const html = await res.text();
                                const container = document.getElementById('business-items-container');
                                if (container) {
                                    container.insertAdjacentHTML('beforeend', html);
                                    this.hasMorePages = this.currentPage < this.lastPage;
                                }
                            }
                        } catch (e) {
                            console.error(e);
                            this.currentPage--; // Revert page on failure
                        } finally {
                            this.loadingMore = false;
                        }
                    },

                    updatePaginationState(html) {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const grid = doc.getElementById('business-grid-container');
                        if (grid) {
                            this.currentPage = parseInt(grid.dataset.currentPage) || 1;
                            this.lastPage = parseInt(grid.dataset.lastPage) || 1;
                            this.hasMorePages = grid.dataset.hasMorePages === 'true';
                        }
                    }
                };
            });

            Alpine.data('directorySearch', () => ({
                query: @js(request('search') ?? ''),
                results: { categories: [], locations: [], companies: [] },
                loading: false,
                showDropdown: false,
                showSearchHint: false,
                activeIndex: -1,
                get flatResults() {
                    let list = [];
                    if (this.results.categories) list.push(...this.results.categories);
                    if (this.results.locations) list.push(...this.results.locations);
                    if (this.results.companies) list.push(...this.results.companies);
                    return list;
                },
                get resultsCount() {
                    return this.flatResults.length;
                },
                fetchResults() {
                    this.activeIndex = -1;
                    if (this.query.length < 2) {
                        this.results = { categories: [], locations: [], companies: [] };
                        this.showDropdown = false;
                        return;
                    }
                    this.loading = true;
                    this.showDropdown = true;

                    setTimeout(async () => {
                        try {
                            let res = await fetch(`{!! route('directory.search') !!}?q=${encodeURIComponent(this.query)}`);
                            this.results = await res.json();
                        } catch (e) {
                            this.results = { categories: [], locations: [], companies: [] };
                        } finally {
                            this.loading = false;
                        }
                    }, 300);
                },
                highlight(text) {
                    if (!text) return '';
                    if (!this.query) return text;
                    const escapedQuery = this.query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    const regex = new RegExp('(' + escapedQuery + ')', 'gi');
                    return text.replace(regex, `<mark class="bg-primary/20 text-slate-900 dark:text-white rounded px-0.5 font-bold">$1</mark>`);
                },
                selectResult(item) {
                    if (item.url) {
                        window.location.href = item.url;
                    }
                },
                moveActive(dir) {
                    const items = this.flatResults;
                    if (items.length === 0) return;
                    if (dir === 'down') {
                        this.activeIndex = (this.activeIndex + 1) % items.length;
                    } else {
                        this.activeIndex = (this.activeIndex - 1 + items.length) % items.length;
                    }
                    this.$nextTick(() => {
                        const activeEl = this.$refs.resultsList.querySelector('[data-active="true"]');
                        if (activeEl) {
                            activeEl.scrollIntoView({ block: 'nearest' });
                        }
                    });
                },
                selectActive() {
                    const items = this.flatResults;
                    if (this.activeIndex >= 0 && this.activeIndex < items.length) {
                        this.selectResult(items[this.activeIndex]);
                    } else if (this.query.trim().length > 0) {
                        this.showDropdown = false;
                        // Triggers the parent form's submitForm
                        document.getElementById('directory-form').dispatchEvent(new Event('submit', { cancelable: true }));
                    }
                }
            }));
        });
    </script>
@endsection