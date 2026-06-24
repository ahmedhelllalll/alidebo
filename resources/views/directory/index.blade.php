@extends('layouts.app')

@section('title', __('directory.title'))

@push('styles')
<style>
    /* Ensure content is below nav */
    .directory-container {
        padding-top: 80px;
    }
    @media (min-width: 1024px) {
        .directory-container {
            padding-top: 96px;
        }
    }
    /* Hide scrollbar for the custom select dropdowns */
    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
</style>
@endpush

@section('content')
<div class="directory-container relative min-h-screen bg-white dark:bg-[#0a0a0c] overflow-hidden">
    
    <!-- Ambient Corner Glows -->
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
        <!-- Top Left Glow -->
        <div class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full"></div>
        <!-- Top Right Glow -->
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-primary/10 dark:bg-primary/10 blur-[140px] rounded-full"></div>
    </div>

    <form action="{{ route('directory.index') }}" method="GET" class="relative flex flex-col min-h-screen">
        
        <!-- Premium Hero Section -->
        <div class="relative z-[2000] max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16 lg:pb-24 text-center reveal">
            @php
                $words = explode(' ', __('directory.hero_headline'));
                $firstWord = array_shift($words);
                $rest = implode(' ', $words);
            @endphp
            <h1 class="text-4xl md:text-5xl lg:text-7xl font-[900] tracking-tight text-slate-900 dark:text-white mb-6 leading-tight">
                {{ $firstWord }}
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary to-primary-light">
                    {{ $rest }}
                </span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 dark:text-zinc-400 font-medium leading-relaxed max-w-2xl mx-auto mb-10">
                {{ __('directory.hero_desc') }}
            </p>
            
            <!-- Hero Search Bar (Height Style) -->
            <div class="relative max-w-2xl mx-auto group z-[100]"
                 x-data="{
                    query: '{{ request('search') }}',
                    results: [],
                    loading: false,
                    showDropdown: false,
                    searchTimer: null,
                    typeLabels: {
                        'company': '{{ __('directory.company') }}',
                        'category': '{{ __('directory.category') }}',
                        'country': '{{ __('directory.country') }}',
                        'city': '{{ __('directory.city') }}'
                    },
                    scrollToFit() {
                        if(this.$refs.dropdown) {
                            const rect = this.$refs.dropdown.getBoundingClientRect();
                            if(rect.bottom > window.innerHeight) {
                                const targetY = window.scrollY + rect.bottom - window.innerHeight + 24;
                                if (window.lenis) {
                                    window.lenis.scrollTo(targetY, { duration: 1.2, easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)) });
                                } else {
                                    window.scrollTo({ top: targetY, behavior: 'smooth' });
                                }
                            }
                        }
                    },
                    fetchResults() {
                        if (this.query.length < 2) {
                            this.results = [];
                            this.showDropdown = false;
                            return;
                        }
                        this.loading = true;
                        this.showDropdown = true;
                        this.$nextTick(() => this.scrollToFit());
                        
                        clearTimeout(this.searchTimer);
                        this.searchTimer = setTimeout(async () => {
                            try {
                                let res = await fetch(`{{ route('directory.search') }}?q=${encodeURIComponent(this.query)}`);
                                this.results = await res.json();
                            } catch (e) {
                                this.results = [];
                            } finally {
                                this.loading = false;
                                setTimeout(() => this.scrollToFit(), 50);
                            }
                        }, 300);
                    }
                 }"
                 @click.away="showDropdown = false"
            >
                <div class="absolute inset-0 bg-primary/20 blur-xl rounded-2xl opacity-0 group-focus-within:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-50 flex items-center bg-white dark:bg-[#121214]/80 backdrop-blur-xl border border-slate-200/80 dark:border-zinc-800/80 rounded-2xl shadow-sm transition-all duration-300 group-focus-within:border-primary/50 group-focus-within:ring-1 group-focus-within:ring-primary/50">
                    <svg class="w-6 h-6 ms-5 text-slate-400 dark:text-zinc-500 group-focus-within:text-primary transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" 
                           x-model="query"
                           @input="fetchResults"
                           @focus="query.length >= 2 ? showDropdown = true : null"
                           @keydown.enter.prevent="if(query.length > 0) $event.target.form.submit()"
                           placeholder="{{ __('directory.search_placeholder') }}" 
                           class="w-full bg-transparent border-0 py-4 px-4 text-base font-medium text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-zinc-500 focus:ring-0"
                           autocomplete="off">
                    @if(request('search'))
                        <a href="{{ route('directory.index', request()->except('search')) }}" class="me-4 text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>

                <!-- Dropdown -->
                <div x-ref="dropdown" x-show="showDropdown" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute top-full left-0 right-0 mt-3 bg-white dark:bg-[#121214]/95 backdrop-blur-2xl border border-slate-200 dark:border-zinc-800/80 rounded-2xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] overflow-hidden z-[99999] text-left">
                    
                    <!-- Loading State -->
                    <div x-show="loading" class="p-4 flex items-center justify-center text-slate-400 dark:text-zinc-500 gap-3">
                        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-sm font-medium">{{ __('directory.searching') }}</span>
                    </div>

                    <!-- No Results -->
                    <div x-show="!loading && results.length === 0" class="p-6 text-center text-slate-500 dark:text-zinc-400 text-sm font-medium">
                        {{ __('directory.no_results_for') }} "<span x-text="query" class="text-slate-900 dark:text-white"></span>"
                    </div>

                    <!-- Results List -->
                    <div x-show="!loading && results.length > 0" data-lenis-prevent="true" class="max-h-[340px] overflow-y-auto overscroll-contain scroll-smooth p-3 scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-zinc-800 space-y-1">
                        <template x-for="item in results" :key="item.id + item.type">
                            <a :href="item.url" class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors group shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-zinc-900 ring-1 ring-slate-200 dark:ring-white/5 flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="item.type === 'company' && item.logo">
                                        <img :src="item.logo" :alt="item.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="item.type === 'company' && !item.logo">
                                        <span class="text-base font-bold text-slate-400 dark:text-zinc-500 uppercase" x-text="item.name.substring(0, 1)"></span>
                                    </template>
                                    <template x-if="item.type === 'category'">
                                        <svg class="w-5 h-5 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </template>
                                    <template x-if="item.type === 'country' || item.type === 'city'">
                                        <svg class="w-5 h-5 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-primary transition-colors" x-text="item.name"></h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-200/50 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400 uppercase tracking-wider" x-text="typeLabels[item.type]"></span>
                                        <template x-if="item.type === 'company' && item.category">
                                            <span class="text-[11px] font-medium text-slate-400 dark:text-zinc-500 truncate" x-text="item.category"></span>
                                        </template>
                                    </div>
                                </div>
                                <div class="text-slate-300 dark:text-zinc-600 group-hover:text-primary group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-all">
                                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </a>
                        </template>
                        
                        <!-- View All Results Action -->
                        <div class="mt-2 pt-2 border-t border-slate-100 dark:border-zinc-800/50">
                            <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 text-sm font-bold text-primary hover:bg-primary/5 rounded-xl transition-colors">
                                {{ __('directory.view_all_results') }} "<span x-text="query"></span>"
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative z-10 max-w-[1400px] w-full mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-8 pb-24 flex-1">
            
            <!-- Mobile Filter Toggle -->
            <button type="button" onclick="document.getElementById('filter-sidebar').classList.toggle('hidden')" class="lg:hidden w-full flex items-center justify-between p-4 rounded-2xl border border-slate-200 dark:border-zinc-800/80 bg-white/60 dark:bg-zinc-900/40 backdrop-blur-xl shadow-sm text-sm font-bold text-slate-900 dark:text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    {{ __('directory.filters') }}
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Minimalist Sidebar (Filters) -->
            <aside id="filter-sidebar" class="hidden lg:block w-full lg:w-[260px] shrink-0">
                <div class="sticky top-28 space-y-8 p-1">
                    
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400 dark:text-zinc-500">{{ __('directory.filters') }}</h2>
                        @if(request('category') || request('city') || request('search'))
                        <a href="{{ route('directory.index') }}" class="text-xs font-bold text-primary hover:text-primary-light transition-colors">{{ __('directory.clear_filters') }}</a>
                        @endif
                    </div>

                    <!-- Categories (Pill List) -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-900 dark:text-white px-1">{{ __('directory.categories') }}</label>
                        <div class="flex flex-col gap-1">
                            <label class="cursor-pointer group relative overflow-hidden rounded-xl transition-all">
                                <input type="radio" name="category" value="" onchange="this.form.submit()" {{ !request('category') ? 'checked' : '' }} class="peer hidden">
                                <div class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-zinc-400 transition-colors group-hover:bg-slate-100 dark:group-hover:bg-zinc-800/50 peer-checked:bg-primary/10 peer-checked:text-primary peer-checked:font-bold">
                                    {{ __('directory.all_categories') }}
                                </div>
                            </label>
                            @foreach($categories as $category)
                            <label class="cursor-pointer group relative overflow-hidden rounded-xl transition-all">
                                <input type="radio" name="category" value="{{ $category->id }}" onchange="this.form.submit()" {{ request('category') == $category->id ? 'checked' : '' }} class="peer hidden">
                                <div class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-zinc-400 transition-colors group-hover:bg-slate-100 dark:group-hover:bg-zinc-800/50 peer-checked:bg-primary/10 peer-checked:text-primary peer-checked:font-bold">
                                    {{ $category->name }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Locations (Custom Select UI) -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-900 dark:text-white px-1">{{ __('directory.locations') }}</label>
                        <input type="hidden" name="city" id="city-filter-input" value="{{ request('city') }}">
                        <div x-data="{
                                open: false,
                                selectedId: '{{ request('city') }}',
                                selectedName: '{{ request('city') ? ($cities->firstWhere('id', request('city'))->name ?? __('directory.all_locations')) : __('directory.all_locations') }}',
                                selectOption(id, name) {
                                    this.selectedId = id;
                                    this.selectedName = name;
                                    this.open = false;
                                    document.getElementById('city-filter-input').value = id;
                                    this.$el.closest('form').submit();
                                }
                             }"
                             class="relative w-full"
                             @click.away="open = false"
                        >
                            <!-- Dropdown Button -->
                            <button type="button" 
                                    @click="open = !open" 
                                    class="w-full flex items-center justify-between bg-white dark:bg-zinc-900/60 border border-slate-200 dark:border-zinc-800 rounded-xl py-3 px-4 text-sm font-medium text-slate-900 dark:text-white transition-all hover:border-slate-300 dark:hover:border-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary/50 shadow-sm"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <!-- Location Pin Icon -->
                                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="truncate" x-text="selectedName"></span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-300 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown List -->
                            <div x-show="open" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 right-0 z-[3000] mt-2 max-h-60 overflow-y-auto bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800 rounded-xl shadow-lg p-1.5 space-y-0.5 scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-zinc-800"
                            >
                                <!-- All Locations Option -->
                                <button type="button" 
                                        @click="selectOption('', '{{ __('directory.all_locations') }}')"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors"
                                        :class="selectedId === '' ? 'bg-primary/5 dark:bg-primary/10 text-primary font-bold' : ''"
                                >
                                    <span>{{ __('directory.all_locations') }}</span>
                                    <template x-if="selectedId === ''">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </template>
                                </button>

                                @foreach($cities as $city)
                                    <button type="button" 
                                            @click="selectOption('{{ $city->id }}', '{{ $city->name }}')"
                                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors"
                                            :class="selectedId === '{{ $city->id }}' ? 'bg-primary/5 dark:bg-primary/10 text-primary font-bold' : ''"
                                    >
                                        <span>{{ $city->name }}</span>
                                        <template x-if="selectedId === '{{ $city->id }}'">
                                            <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        </template>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </aside>

            <!-- Right Grid (Companies) -->
            <div class="flex-1 w-full min-w-0 flex flex-col">
                
                <!-- Top Bar -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-zinc-800/80 mb-6 reveal">
                    <div class="text-sm font-medium text-slate-500 dark:text-zinc-400">
                        <span class="text-slate-900 dark:text-white font-bold">{{ $businesses->total() }}</span> {{ __('directory.results_found') }}
                    </div>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <label class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest shrink-0">{{ __('directory.sort_by') }}</label>
                        <input type="hidden" name="sort" id="sort-filter-input" value="{{ request('sort', 'newest') }}">
                        @php
                            $currentSort = request('sort', 'newest');
                            $sortNames = [
                                'newest' => __('directory.sort_newest'),
                                'oldest' => __('directory.sort_oldest'),
                                'a-z' => __('directory.sort_az'),
                                'z-a' => __('directory.sort_za')
                            ];
                            $currentSortName = $sortNames[$currentSort] ?? __('directory.sort_newest');
                        @endphp
                        <div x-data="{
                                open: false,
                                selectedSort: '{{ $currentSort }}',
                                selectedSortName: '{{ $currentSortName }}',
                                selectSort(val, name) {
                                    this.selectedSort = val;
                                    this.selectedSortName = name;
                                    this.open = false;
                                    document.getElementById('sort-filter-input').value = val;
                                    this.$el.closest('form').submit();
                                }
                             }"
                             class="relative w-full sm:w-44"
                             @click.away="open = false"
                        >
                            <!-- Dropdown Button -->
                            <button type="button" 
                                    @click="open = !open" 
                                    class="w-full flex items-center justify-between bg-white/60 dark:bg-zinc-900/40 border border-slate-200 dark:border-zinc-800/80 rounded-xl py-2 px-3.5 text-sm font-bold text-slate-900 dark:text-white transition-all hover:border-slate-300 dark:hover:border-zinc-700 focus:outline-none focus:ring-2 focus:ring-primary/50 shadow-sm"
                            >
                                <div class="flex items-center gap-2 min-w-0">
                                    <!-- Sort Icon (Fit Icon) -->
                                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                                    </svg>
                                    <span class="truncate" x-text="selectedSortName"></span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-300 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown List -->
                            <div x-show="open" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 z-[3000] mt-2 w-44 bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800 rounded-xl shadow-lg p-1.5 space-y-0.5"
                            >
                                <button type="button" 
                                        @click="selectSort('newest', '{{ __('directory.sort_newest') }}')"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors"
                                        :class="selectedSort === 'newest' ? 'bg-primary/5 dark:bg-primary/10 text-primary font-bold' : ''"
                                >
                                    <span>{{ __('directory.sort_newest') }}</span>
                                    <template x-if="selectedSort === 'newest'">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </template>
                                </button>
                                <button type="button" 
                                        @click="selectSort('oldest', '{{ __('directory.sort_oldest') }}')"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors"
                                        :class="selectedSort === 'oldest' ? 'bg-primary/5 dark:bg-primary/10 text-primary font-bold' : ''"
                                >
                                    <span>{{ __('directory.sort_oldest') }}</span>
                                    <template x-if="selectedSort === 'oldest'">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </template>
                                </button>
                                <button type="button" 
                                        @click="selectSort('a-z', '{{ __('directory.sort_az') }}')"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors"
                                        :class="selectedSort === 'a-z' ? 'bg-primary/5 dark:bg-primary/10 text-primary font-bold' : ''"
                                >
                                    <span>{{ __('directory.sort_az') }}</span>
                                    <template x-if="selectedSort === 'a-z'">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </template>
                                </button>
                                <button type="button" 
                                        @click="selectSort('z-a', '{{ __('directory.sort_za') }}')"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors"
                                        :class="selectedSort === 'z-a' ? 'bg-primary/5 dark:bg-primary/10 text-primary font-bold' : ''"
                                >
                                    <span>{{ __('directory.sort_za') }}</span>
                                    <template x-if="selectedSort === 'z-a'">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento Grid -->
                @if($businesses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($businesses as $business)
                        <div class="reveal luxury-card group relative flex flex-col h-full bg-white dark:bg-[#09090b] rounded-[1.5rem] overflow-hidden border border-slate-200/60 dark:border-zinc-800/60 transition-all duration-300 hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-1 z-10">
                            
                            {{-- Cover Image --}}
                            <div class="h-32 sm:h-40 bg-slate-100 dark:bg-zinc-800 relative overflow-hidden shrink-0 border-b border-slate-100 dark:border-zinc-800/50">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent z-10"></div>
                                @if($business->cover)
                                    <img src="{{ $business->cover_url }}" alt="{{ $business->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 dark:from-zinc-800 dark:to-zinc-900 transition-transform duration-700 group-hover:scale-105"></div>
                                @endif
                            </div>

                            {{-- Card Content --}}
                            <div class="p-6 sm:p-8 relative flex-1 flex flex-col">
                                
                                {{-- SaaS Logo Avatar --}}
                                <div class="absolute -top-10 start-6 sm:start-8 w-16 h-16 rounded-2xl bg-white dark:bg-zinc-900 border-4 border-white dark:border-[#09090b] shadow-sm flex items-center justify-center overflow-hidden z-20 transition-transform duration-300 group-hover:-translate-y-1">
                                    @if($business->logo)
                                        <img src="{{ $business->logo_url }}" alt="{{ $business->name }} logo" loading="lazy" decoding="async" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-primary/10 text-primary flex items-center justify-center font-[900] text-xl uppercase">
                                            {{ mb_substr($business->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mt-8 flex-1 flex flex-col">
                                    <div class="flex items-start justify-between gap-4 mb-3">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-xl font-bold text-slate-900 dark:text-white line-clamp-1 group-hover:text-primary transition-colors duration-300">{{ $business->name }}</h3>
                                            @if($business->is_claimed)
                                                <div title="{{ __('directory.verified') }}" class="text-blue-500 shrink-0 bg-blue-50 dark:bg-blue-500/10 p-1.5 rounded-full flex items-center justify-center shadow-sm">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-50 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 opacity-0 -translate-x-2 rtl:translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 rtl:group-hover:translate-x-0 transition-all duration-300 shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </div>
                                    </div>
                                    
                                    <p class="text-sm text-slate-500 dark:text-zinc-400 line-clamp-2 mb-8 font-medium leading-relaxed flex-1">
                                        {{ $business->description ?? '...' }}
                                    </p>
                                    
                                    {{-- SaaS Tags --}}
                                    <div class="flex items-center gap-2.5 flex-wrap pt-5 border-t border-slate-100 dark:border-zinc-800/80 mt-auto">
                                        @if($business->category)
                                            <span class="px-3 py-1.5 rounded-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200/60 dark:border-zinc-800/60 text-xs font-semibold text-slate-600 dark:text-zinc-400 transition-colors group-hover:border-primary/30 group-hover:text-primary dark:group-hover:text-primary-light flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                                {{ $business->category->name }}
                                            </span>
                                        @endif
                                        @if($business->city)
                                            <span class="px-3 py-1.5 rounded-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200/60 dark:border-zinc-800/60 text-xs font-semibold text-slate-600 dark:text-zinc-400 transition-colors group-hover:border-primary/30 group-hover:text-primary dark:group-hover:text-primary-light flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $business->city->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Absolute Link covering card -->
                            <a href="{{ route('business.view', $business->slug) }}" class="absolute inset-0 z-30 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-inset rounded-[1.5rem]" aria-label="View {{ $business->name }}"></a>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center reveal">
                        {{ $businesses->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center py-32 px-4 text-center border border-dashed border-slate-200 dark:border-zinc-800/80 rounded-[24px] reveal my-auto">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-zinc-800/50 rounded-2xl flex items-center justify-center mb-6 ring-1 ring-slate-100 dark:ring-white/5">
                            <svg class="w-8 h-8 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ __('directory.no_results') }}</h3>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 font-medium max-w-sm">{{ __('directory.try_different') }}</p>
                        <a href="{{ route('directory.index') }}" class="mt-6 px-5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm shadow-sm hover:bg-slate-800 dark:hover:bg-slate-100 transition-all relative z-30">
                            {{ __('directory.clear_filters') }}
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </form>
</div>
@endsection
