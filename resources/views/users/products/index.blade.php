@extends('users.layout')

@section('title', __('products.my_products') . ' | ' . ($business->name ?? config('app.name')))
@section('page_title', __('products.my_products'))

@php
    $currentAppLocale = app()->getLocale();
    $isRtl = $currentAppLocale === 'ar';
    $hasActiveFilters = request()->hasAny(['status', 'category_id', 'search', 'sort']);
    $categoriesData = $categories->map(function ($cat) {
        return [
            'id' => (string) $cat->id,
            'name' => $cat->name,
        ];
    })->values();

    $sortOptions = [
        ['id' => 'latest', 'name' => __('products.sort_latest')],
        ['id' => 'oldest', 'name' => __('products.sort_oldest')],
        ['id' => 'price_asc', 'name' => __('products.sort_price_asc')],
        ['id' => 'price_desc', 'name' => __('products.sort_price_desc')],
    ];
@endphp

@section('content')
<div x-data="productsIndex()" 
dir="{{ $currentAppLocale == 'ar' ? 'rtl' : 'ltr' }}"
class="max-w-7xl mx-auto space-y-6 pb-20 select-text">

    {{-- Toast Notification --}}
    <div x-show="toast.visible" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed top-5 end-5 z-[9999] flex items-center gap-3.5 p-4 rounded-2xl shadow-2xl max-w-md w-full border backdrop-blur-xl bg-white/95 dark:bg-zinc-900/95"
         :class="toast.type === 'success' 
            ? 'border-emerald-500/30 text-zinc-900 dark:text-white shadow-emerald-500/10' 
            : 'border-rose-500/30 text-zinc-900 dark:text-white shadow-rose-500/10'"
         style="display: none;">
        
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
             :class="toast.type === 'success' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'">
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </template>
            <template x-if="toast.type !== 'success'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </template>
        </div>

        <div class="flex-1 min-w-0">
            <p class="text-xs font-black uppercase tracking-wider mb-0.5 text-start"
               :class="toast.type === 'success' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
               x-text="toast.type === 'success' ? '{{ __('products.toast_success_title') }}' : '{{ __('products.toast_error_title') }}'"></p>
            <p class="text-xs sm:text-sm font-bold text-zinc-800 dark:text-zinc-200 leading-snug text-start" x-text="toast.message"></p>
        </div>

        <button type="button" 
                @click="toast.visible = false" 
                class="w-7 h-7 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center transition-colors"
                title="{{ __('products.toast_dismiss') }}"
                aria-label="{{ __('products.toast_dismiss') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-zinc-200/80 dark:border-zinc-800/80">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-zinc-400 hover:text-primary transition-colors">
                    {{ __('nav.home') }}
                </a>
                <span class="text-zinc-400 text-xs">/</span>
                <span class="text-xs font-semibold text-primary">
                    {{ __('products.my_products') }}
                </span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-[900] text-zinc-900 dark:text-white tracking-tight text-start">
                    {{ __('products.my_products') }}
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-primary/10 text-primary border border-primary/20">
                    {{ $statusCounts['all'] }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 font-medium mt-1 text-start">
                {{ __('products.manage_products_desc') }}
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            {{-- Guide Tour Re-trigger Button --}}
            <button type="button" 
                    id="tour-retrigger-btn"
                    @click="$dispatch('open-products-tour')" 
                    class="px-3.5 py-2.5 rounded-xl bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:text-primary dark:hover:text-primary border border-zinc-200/80 dark:border-zinc-700/80 shadow-xs text-xs sm:text-sm font-bold flex items-center gap-1.5 transition-all hover:scale-105 active:scale-95 cursor-pointer shrink-0"
                    title="{{ __('products.tour_guide_button') }}">
                <span class="w-4 h-4 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black text-[11px]">؟</span>
                <span>{{ __('products.tour_guide_button') }}</span>
            </button>

            <a href="{{ route('products.create') }}" 
               id="tour-add-product-btn"
               class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/25 hover:shadow-primary/35 transition-all flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>{{ __('products.add_product') }}</span>
            </a>
        </div>
    </div>

    {{-- Top Controls Bar (Mobile Filter Toggle + Counts + View Mode Switcher) --}}
    <div class="flex flex-wrap items-center justify-between gap-3 p-3 sm:p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 shadow-sm">
        {{-- Left: Mobile Filter Toggle & Results Count --}}
        <div class="flex items-center gap-3">
            {{-- Mobile Drawer Trigger Button --}}
            <button type="button" 
                    @click="mobileFiltersOpen = true" 
                    class="lg:hidden px-3.5 py-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-xs font-bold flex items-center gap-2 transition-colors border border-zinc-200 dark:border-zinc-700">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>{{ __('products.toggle_filters') }}</span>
                @if($hasActiveFilters)
                    <span class="w-2 h-2 rounded-full bg-primary ring-2 ring-white dark:ring-zinc-900"></span>
                @endif
            </button>

            {{-- Products Count Indicator --}}
            <p class="text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-400">
                {{ __('products.showing_products', ['count' => $products->count()]) }}
                @if($products->total() > $products->count())
                    <span class="text-zinc-400 text-xs">({{ __('products.status_all') }}: {{ $products->total() }})</span>
                @endif
            </p>
        </div>

        {{-- Right: View Mode Toggle (Grid / List) --}}
        <div class="flex items-center gap-2 ms-auto">
            <span class="text-xs font-extrabold text-zinc-400 uppercase tracking-wider hidden sm:inline-block">
                {{ __('products.view_mode') }}:
            </span>
            <div id="tour-view-switcher" class="p-1 rounded-xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/80 flex items-center gap-1">
                {{-- Card View Button (Grid) --}}
                <button type="button" 
                        @click="setViewMode('grid')" 
                        :class="viewMode === 'grid' 
                            ? 'bg-primary text-white shadow-sm shadow-primary/30 font-bold' 
                            : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white'"
                        class="px-2.5 py-1.5 rounded-lg text-xs flex items-center gap-1.5 transition-all cursor-pointer"
                        title="{{ __('products.grid_view') }}"
                        aria-label="{{ __('products.grid_view') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span class="hidden md:inline">{{ __('products.grid_view') }}</span>
                </button>

                {{-- Row View Button (List) --}}
                <button type="button" 
                        @click="setViewMode('list')" 
                        :class="viewMode === 'list' 
                            ? 'bg-primary text-white shadow-sm shadow-primary/30 font-bold' 
                            : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white'"
                        class="px-2.5 py-1.5 rounded-lg text-xs flex items-center gap-1.5 transition-all cursor-pointer"
                        title="{{ __('products.list_view') }}"
                        aria-label="{{ __('products.list_view') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span class="hidden md:inline">{{ __('products.list_view') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Main Content Layout (Sidebar Filters + Products Display) --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 xl:grid-cols-4 gap-6 items-start">
        
        {{-- Desktop Filter Sidebar --}}
        <aside id="tour-filters-sidebar" class="hidden lg:block lg:col-span-1 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 p-5 shadow-sm sticky top-6 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800/80">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-zinc-900 dark:text-zinc-100">
                        {{ __('products.filter_title') }}
                    </h3>
                </div>
                @if($hasActiveFilters)
                    <a href="{{ route('products.index') }}" 
                       class="text-[11px] font-bold text-rose-500 hover:text-rose-600 transition-colors">
                        {{ __('products.clear_filters') }}
                    </a>
                @endif
            </div>

            <form action="{{ route('products.index') }}" method="GET" class="space-y-4">
                {{-- Search Input --}}
                <div class="space-y-1.5">
                    <label for="search" class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                        {{ __('products.search') }}
                    </label>
                    <div class="relative rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-zinc-50/50 dark:bg-zinc-800/50 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                        <svg class="w-4 h-4 text-zinc-400 absolute start-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}" 
                               placeholder="{{ __('products.search_placeholder') }}" 
                               class="w-full bg-transparent ps-10 pe-3.5 py-2.5 text-xs font-semibold outline-none border-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 text-start">
                    </div>
                </div>

                {{-- Status Filter (Radio / Segmented) --}}
                <div id="tour-status-badges" class="space-y-2">
                    <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                        {{ __('products.filter_status') }}
                    </label>
                    <div class="space-y-1.5">
                        {{-- All --}}
                        <label class="flex items-center justify-between p-2.5 rounded-xl border transition-all cursor-pointer {{ !request('status') ? 'border-primary bg-primary/[0.05] dark:bg-primary/[0.08] font-bold text-primary' : 'border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 text-zinc-700 dark:text-zinc-300' }}">
                            <div class="flex items-center gap-2.5">
                                <input type="radio" name="status" value="" {{ !request('status') ? 'checked' : '' }} class="text-primary focus:ring-primary h-4 w-4">
                                <span class="text-xs">{{ __('products.status_all') }}</span>
                            </div>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ !request('status') ? 'bg-primary text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500' }}">
                                {{ $statusCounts['all'] }}
                            </span>
                        </label>

                        {{-- Approved --}}
                        <label class="flex items-center justify-between p-2.5 rounded-xl border transition-all cursor-pointer {{ request('status') === 'approved' ? 'border-emerald-500 bg-emerald-500/[0.05] dark:bg-emerald-500/[0.08] font-bold text-emerald-600 dark:text-emerald-400' : 'border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 text-zinc-700 dark:text-zinc-300' }}">
                            <div class="flex items-center gap-2.5">
                                <input type="radio" name="status" value="approved" {{ request('status') === 'approved' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="text-xs">{{ __('products.status_approved') }}</span>
                            </div>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                                {{ $statusCounts['approved'] }}
                            </span>
                        </label>

                        {{-- Pending --}}
                        <label class="flex items-center justify-between p-2.5 rounded-xl border transition-all cursor-pointer {{ request('status') === 'pending' ? 'border-amber-500 bg-amber-500/[0.05] dark:bg-amber-500/[0.08] font-bold text-amber-600 dark:text-amber-400' : 'border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 text-zinc-700 dark:text-zinc-300' }}">
                            <div class="flex items-center gap-2.5">
                                <input type="radio" name="status" value="pending" {{ request('status') === 'pending' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500 h-4 w-4">
                                <span class="text-xs">{{ __('products.status_pending') }}</span>
                            </div>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300">
                                {{ $statusCounts['pending'] }}
                            </span>
                        </label>

                        {{-- Rejected --}}
                        <label class="flex items-center justify-between p-2.5 rounded-xl border transition-all cursor-pointer {{ request('status') === 'rejected' ? 'border-rose-500 bg-rose-500/[0.05] dark:bg-rose-500/[0.08] font-bold text-rose-600 dark:text-rose-400' : 'border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 text-zinc-700 dark:text-zinc-300' }}">
                            <div class="flex items-center gap-2.5">
                                <input type="radio" name="status" value="rejected" {{ request('status') === 'rejected' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500 h-4 w-4">
                                <span class="text-xs">{{ __('products.status_rejected') }}</span>
                            </div>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300">
                                {{ $statusCounts['rejected'] }}
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Category Filter (Custom Alpine Smart Dropdown) --}}
                <div id="tour-category-filter" class="space-y-1.5" x-data="{
                    open: false,
                    search: '',
                    dropUp: false,
                    selectedId: '{{ (string)request('category_id', '') }}',
                    items: {{ json_encode($categoriesData, JSON_UNESCAPED_UNICODE) }},
                    toggle(triggerEl) {
                        if (!this.open) {
                            const rect = triggerEl.getBoundingClientRect();
                            const spaceBelow = window.innerHeight - rect.bottom;
                            const spaceAbove = rect.top;
                            this.dropUp = spaceBelow < 340 && spaceAbove > spaceBelow;
                            this.open = true;
                            this.$nextTick(() => {
                                if (this.$refs.catSearchInput) this.$refs.catSearchInput.focus();
                            });
                        } else {
                            this.open = false;
                        }
                    },
                    select(id) {
                        this.selectedId = id;
                        this.open = false;
                        this.search = '';
                    },
                    get selectedName() {
                        if (!this.selectedId) return '{{ __('products.all_categories') }}';
                        const found = this.items.find(c => c.id == this.selectedId);
                        return found ? found.name : '{{ __('products.all_categories') }}';
                    },
                    get filteredItems() {
                        if (!this.search || this.search.trim() === '') return this.items;
                        const q = this.search.toLowerCase();
                        return this.items.filter(c => c.name.toLowerCase().includes(q));
                    }
                }" @click.outside="open = false" @keydown.escape.window="open = false">
                    
                    <input type="hidden" name="category_id" :value="selectedId">

                    <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                        {{ __('products.filter_category') }}
                    </label>

                    <div class="relative">
                        {{-- Trigger Button --}}
                        <button type="button" 
                                x-ref="catDropdownBtn"
                                @click="toggle($refs.catDropdownBtn)"
                                class="w-full flex items-stretch rounded-xl border bg-white dark:bg-zinc-900 overflow-hidden shadow-xs transition-all duration-300 border-zinc-200 dark:border-zinc-700/80 hover:border-primary/50 text-start cursor-pointer select-none"
                                :class="open ? 'border-primary ring-2 ring-primary/30' : 'focus:border-primary focus:ring-2 focus:ring-primary/30'">
                            
                            {{-- Flush Solid Primary Box matching unit and other form inputs --}}
                            <div class="flex items-center justify-center px-3.5 bg-primary text-white shrink-0" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </div>

                            {{-- Value display + Chevron --}}
                            <div class="flex-1 flex items-center justify-between min-w-0 ps-3.5 pe-3.5 py-2.5">
                                <span class="text-xs font-bold truncate"
                                      :class="selectedId ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 dark:text-zinc-400'"
                                      x-text="selectedName">
                                </span>
                                <svg class="w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-200 ms-2" 
                                     :class="open ? 'rotate-180 text-primary' : ''" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        {{-- Dropdown Popover (Smart Upward / Downward positioning & Pinned Search) --}}
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200 transform"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150 transform"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute start-0 end-0 z-50 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl p-2 flex flex-col"
                             :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                             style="max-height: 320px; display: none;">
                            
                            {{-- Search Input inside Dropdown (Pinned at top, doesn't scroll) --}}
                            <div class="relative mb-2 shrink-0">
                                <input type="text" 
                                       x-ref="catSearchInput"
                                       x-model="search"
                                       placeholder="{{ __('products.search_categories_placeholder') }}"
                                       class="w-full bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/80 rounded-xl ps-9 pe-3 py-2 text-xs font-semibold text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 outline-none focus:border-primary text-start">
                                <svg class="w-4 h-4 text-zinc-400 absolute start-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>

                            {{-- Categories List (Internal scroll, max-height 240px) --}}
                            <div class="overflow-y-auto custom-scrollbar space-y-1 flex-1 min-h-0" style="max-height: 240px; overflow-y: auto;">
                                {{-- All Categories Option --}}
                                <button type="button" 
                                        @click="select('')"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors text-start"
                                        :class="!selectedId 
                                            ? 'bg-primary/10 text-primary dark:bg-primary/20' 
                                            : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60'">
                                    <span>{{ __('products.all_categories') }}</span>
                                    <template x-if="!selectedId">
                                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </template>
                                </button>

                                <template x-for="cat in filteredItems" :key="cat.id">
                                    <button type="button" 
                                            @click="select(cat.id)"
                                            class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors text-start"
                                            :class="selectedId == cat.id 
                                                ? 'bg-primary/10 text-primary dark:bg-primary/20' 
                                                : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60'">
                                        <span x-text="cat.name"></span>
                                        <template x-if="selectedId == cat.id">
                                            <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </template>
                                    </button>
                                </template>
                                <template x-if="filteredItems.length === 0">
                                    <p class="text-xs text-zinc-400 text-center py-3">
                                        {{ __('forms.category_not_found') ?? 'No categories found' }}
                                    </p>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sort Filter (Custom Alpine Smart Dropdown) --}}
                <div class="space-y-1.5" x-data="{
                    open: false,
                    dropUp: false,
                    selectedId: '{{ request('sort', 'latest') }}',
                    items: {{ json_encode($sortOptions, JSON_UNESCAPED_UNICODE) }},
                    toggle(triggerEl) {
                        if (!this.open) {
                            const rect = triggerEl.getBoundingClientRect();
                            const spaceBelow = window.innerHeight - rect.bottom;
                            const spaceAbove = rect.top;
                            this.dropUp = spaceBelow < 220 && spaceAbove > spaceBelow;
                            this.open = true;
                        } else {
                            this.open = false;
                        }
                    },
                    select(id) {
                        this.selectedId = id;
                        this.open = false;
                    },
                    get selectedName() {
                        const found = this.items.find(s => s.id == this.selectedId);
                        return found ? found.name : '{{ __('products.sort_latest') }}';
                    }
                }" @click.outside="open = false" @keydown.escape.window="open = false">
                    
                    <input type="hidden" name="sort" :value="selectedId">

                    <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                        {{ __('products.sort_by') }}
                    </label>

                    <div class="relative">
                        {{-- Trigger Button --}}
                        <button type="button" 
                                x-ref="sortDropdownBtn"
                                @click="toggle($refs.sortDropdownBtn)"
                                class="w-full flex items-stretch rounded-xl border bg-white dark:bg-zinc-900 overflow-hidden shadow-xs transition-all duration-300 border-zinc-200 dark:border-zinc-700/80 hover:border-primary/50 text-start cursor-pointer select-none"
                                :class="open ? 'border-primary ring-2 ring-primary/30' : 'focus:border-primary focus:ring-2 focus:ring-primary/30'">
                            
                            {{-- Flush Solid Primary Box matching unit and other form inputs --}}
                            <div class="flex items-center justify-center px-3.5 bg-primary text-white shrink-0" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </div>

                            {{-- Value display + Chevron --}}
                            <div class="flex-1 flex items-center justify-between min-w-0 ps-3.5 pe-3.5 py-2.5">
                                <span class="text-xs font-bold truncate text-zinc-900 dark:text-zinc-100"
                                      x-text="selectedName">
                                </span>
                                <svg class="w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-200 ms-2" 
                                     :class="open ? 'rotate-180 text-primary' : ''" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        {{-- Dropdown Popover (Smart Upward / Downward positioning) --}}
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200 transform"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150 transform"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute start-0 end-0 z-50 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl p-2 flex flex-col space-y-1"
                             :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                             style="display: none;">
                            
                            <template x-for="opt in items" :key="opt.id">
                                <button type="button" 
                                        @click="select(opt.id)"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors text-start"
                                        :class="selectedId == opt.id 
                                            ? 'bg-primary/10 text-primary dark:bg-primary/20' 
                                            : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60'">
                                    <span x-text="opt.name"></span>
                                    <template x-if="selectedId == opt.id">
                                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="pt-2 space-y-2">
                    <button type="submit" 
                            class="w-full py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-xs font-bold shadow-md shadow-primary/20 transition-all">
                        {{ __('products.apply_filters') }}
                    </button>
                    @if($hasActiveFilters)
                        <a href="{{ route('products.index') }}" 
                           class="block w-full py-2.5 text-center bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-semibold transition-colors">
                            {{ __('products.clear_filters') }}
                        </a>
                    @endif
                </div>
            </form>
        </aside>

        {{-- Mobile Filter Drawer (Slide-over) --}}
        <div x-show="mobileFiltersOpen" 
             class="fixed inset-0 lg:hidden flex justify-end" 
             :style="window.__alideboTourActive ? 'z-index: 9991; display: none;' : 'z-index: 9980; display: none;'"
             style="z-index: 9980; display: none;">
            {{-- Backdrop --}}
            <div x-show="mobileFiltersOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-[380ms]"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-[380ms]"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="if (!window.__alideboTourActive) mobileFiltersOpen = false" 
                 class="fixed inset-0 bg-zinc-900/60 backdrop-blur-sm"></div>

            {{-- Drawer Content --}}
            <div id="tour-filters-drawer"
                 x-show="mobileFiltersOpen" 
                 x-transition:enter="transition ease-out duration-[380ms] transform"
                 x-transition:enter-start="{{ $currentAppLocale == 'ar' ? '-translate-x-full' : 'translate-x-full' }}"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-out duration-[380ms] transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="{{ $currentAppLocale == 'ar' ? '-translate-x-full' : 'translate-x-full' }}"
                 class="relative w-full max-w-xs bg-white dark:bg-zinc-900 p-6 shadow-2xl flex flex-col justify-between overflow-y-auto z-10">
                
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-zinc-200/80 dark:border-zinc-800/80 mb-5">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-zinc-900 dark:text-white">
                                {{ __('products.filter_title') }}
                            </h3>
                        </div>
                        <button type="button" @click="mobileFiltersOpen = false" class="w-8 h-8 rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('products.index') }}" method="GET" class="space-y-4">
                        {{-- Search Input --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.search') }}
                            </label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="{{ __('products.search_placeholder') }}" 
                                   class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-zinc-50 dark:bg-zinc-800/50 px-3.5 py-2.5 text-xs font-semibold text-zinc-900 dark:text-zinc-100 outline-none text-start">
                        </div>

                        {{-- Status Filter --}}
                        <div id="tour-status-badges-mobile" class="space-y-2">
                            <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.filter_status') }}
                            </label>
                            <div class="space-y-1.5">
                                <label class="flex items-center justify-between p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 text-xs">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="status" value="" {{ !request('status') ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                        <span>{{ __('products.status_all') }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800">{{ $statusCounts['all'] }}</span>
                                </label>
                                <label class="flex items-center justify-between p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="status" value="approved" {{ request('status') === 'approved' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                        <span>{{ __('products.status_approved') }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20">{{ $statusCounts['approved'] }}</span>
                                </label>
                                <label class="flex items-center justify-between p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 text-xs text-amber-600 dark:text-amber-400 font-semibold">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="status" value="pending" {{ request('status') === 'pending' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                                        <span>{{ __('products.status_pending') }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-500/20">{{ $statusCounts['pending'] }}</span>
                                </label>
                                <label class="flex items-center justify-between p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 text-xs text-rose-600 dark:text-rose-400 font-semibold">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="status" value="rejected" {{ request('status') === 'rejected' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                                        <span>{{ __('products.status_rejected') }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-500/20">{{ $statusCounts['rejected'] }}</span>
                                </label>
                            </div>
                        </div>

                        {{-- Category Filter (Custom Alpine Smart Dropdown) --}}
                        <div id="tour-category-filter-mobile" class="space-y-1.5" x-data="{
                            open: false,
                            search: '',
                            dropUp: false,
                            selectedId: '{{ (string)request('category_id', '') }}',
                            items: {{ json_encode($categoriesData, JSON_UNESCAPED_UNICODE) }},
                            toggle(triggerEl) {
                                if (!this.open) {
                                    const rect = triggerEl.getBoundingClientRect();
                                    const spaceBelow = window.innerHeight - rect.bottom;
                                    const spaceAbove = rect.top;
                                    this.dropUp = spaceBelow < 340 && spaceAbove > spaceBelow;
                                    this.open = true;
                                    this.$nextTick(() => {
                                        if (this.$refs.catMobileSearchInput) this.$refs.catMobileSearchInput.focus();
                                    });
                                } else {
                                    this.open = false;
                                }
                            },
                            select(id) {
                                this.selectedId = id;
                                this.open = false;
                                this.search = '';
                            },
                            get selectedName() {
                                if (!this.selectedId) return '{{ __('products.all_categories') }}';
                                const found = this.items.find(c => c.id == this.selectedId);
                                return found ? found.name : '{{ __('products.all_categories') }}';
                            },
                            get filteredItems() {
                                if (!this.search || this.search.trim() === '') return this.items;
                                const q = this.search.toLowerCase();
                                return this.items.filter(c => c.name.toLowerCase().includes(q));
                            }
                        }" @click.outside="open = false" @keydown.escape.window="open = false">
                            
                            <input type="hidden" name="category_id" :value="selectedId">

                            <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.filter_category') }}
                            </label>

                            <div class="relative">
                                {{-- Trigger Button --}}
                                <button type="button" 
                                        x-ref="catMobileDropdownBtn"
                                        @click="toggle($refs.catMobileDropdownBtn)"
                                        class="w-full flex items-stretch rounded-xl border bg-white dark:bg-zinc-900 overflow-hidden shadow-xs transition-all duration-300 border-zinc-200 dark:border-zinc-700/80 hover:border-primary/50 text-start cursor-pointer select-none"
                                        :class="open ? 'border-primary ring-2 ring-primary/30' : 'focus:border-primary focus:ring-2 focus:ring-primary/30'">
                                    
                                    {{-- Flush Solid Primary Box --}}
                                    <div class="flex items-center justify-center px-3.5 bg-primary text-white shrink-0" aria-hidden="true">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </div>

                                    {{-- Value display + Chevron --}}
                                    <div class="flex-1 flex items-center justify-between min-w-0 ps-3.5 pe-3.5 py-2.5">
                                        <span class="text-xs font-bold truncate"
                                              :class="selectedId ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 dark:text-zinc-400'"
                                              x-text="selectedName">
                                        </span>
                                        <svg class="w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-200 ms-2" 
                                             :class="open ? 'rotate-180 text-primary' : ''" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>

                                {{-- Dropdown Popover --}}
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200 transform"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150 transform"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute start-0 end-0 z-50 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl p-2 flex flex-col"
                                     :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                                     style="max-height: 320px; display: none;">
                                    
                                    {{-- Search Input inside Dropdown --}}
                                    <div class="relative mb-2 shrink-0">
                                        <input type="text" 
                                               x-ref="catMobileSearchInput"
                                               x-model="search"
                                               placeholder="{{ __('products.search_categories_placeholder') }}"
                                               class="w-full bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/80 rounded-xl ps-9 pe-3 py-2 text-xs font-semibold text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 outline-none focus:border-primary text-start">
                                        <svg class="w-4 h-4 text-zinc-400 absolute start-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>

                                    {{-- Categories List --}}
                                    <div class="overflow-y-auto custom-scrollbar space-y-1 flex-1 min-h-0" style="max-height: 240px; overflow-y: auto;">
                                        {{-- All Categories Option --}}
                                        <button type="button" 
                                                @click="select('')"
                                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors text-start"
                                                :class="!selectedId 
                                                    ? 'bg-primary/10 text-primary dark:bg-primary/20' 
                                                    : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60'">
                                            <span>{{ __('products.all_categories') }}</span>
                                            <template x-if="!selectedId">
                                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                        </button>

                                        <template x-for="cat in filteredItems" :key="cat.id">
                                            <button type="button" 
                                                    @click="select(cat.id)"
                                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors text-start"
                                                    :class="selectedId == cat.id 
                                                        ? 'bg-primary/10 text-primary dark:bg-primary/20' 
                                                        : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60'">
                                                <span x-text="cat.name"></span>
                                                <template x-if="selectedId == cat.id">
                                                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </template>
                                            </button>
                                        </template>
                                        <template x-if="filteredItems.length === 0">
                                            <p class="text-xs text-zinc-400 text-center py-3">
                                                {{ __('forms.category_not_found') ?? 'No categories found' }}
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sort (Custom Alpine Smart Dropdown) --}}
                        <div class="space-y-1.5" x-data="{
                            open: false,
                            dropUp: false,
                            selectedId: '{{ request('sort', 'latest') }}',
                            items: {{ json_encode($sortOptions, JSON_UNESCAPED_UNICODE) }},
                            toggle(triggerEl) {
                                if (!this.open) {
                                    const rect = triggerEl.getBoundingClientRect();
                                    const spaceBelow = window.innerHeight - rect.bottom;
                                    const spaceAbove = rect.top;
                                    this.dropUp = spaceBelow < 220 && spaceAbove > spaceBelow;
                                    this.open = true;
                                } else {
                                    this.open = false;
                                }
                            },
                            select(id) {
                                this.selectedId = id;
                                this.open = false;
                            },
                            get selectedName() {
                                const found = this.items.find(s => s.id == this.selectedId);
                                return found ? found.name : '{{ __('products.sort_latest') }}';
                            }
                        }" @click.outside="open = false" @keydown.escape.window="open = false">
                            
                            <input type="hidden" name="sort" :value="selectedId">

                            <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.sort_by') }}
                            </label>

                            <div class="relative">
                                {{-- Trigger Button --}}
                                <button type="button" 
                                        x-ref="sortMobileDropdownBtn"
                                        @click="toggle($refs.sortMobileDropdownBtn)"
                                        class="w-full flex items-stretch rounded-xl border bg-white dark:bg-zinc-900 overflow-hidden shadow-xs transition-all duration-300 border-zinc-200 dark:border-zinc-700/80 hover:border-primary/50 text-start cursor-pointer select-none"
                                        :class="open ? 'border-primary ring-2 ring-primary/30' : 'focus:border-primary focus:ring-2 focus:ring-primary/30'">
                                    
                                    {{-- Flush Solid Primary Box --}}
                                    <div class="flex items-center justify-center px-3.5 bg-primary text-white shrink-0" aria-hidden="true">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                    </div>

                                    {{-- Value display + Chevron --}}
                                    <div class="flex-1 flex items-center justify-between min-w-0 ps-3.5 pe-3.5 py-2.5">
                                        <span class="text-xs font-bold truncate text-zinc-900 dark:text-zinc-100"
                                              x-text="selectedName">
                                        </span>
                                        <svg class="w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-200 ms-2" 
                                             :class="open ? 'rotate-180 text-primary' : ''" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>

                                {{-- Dropdown Popover (Smart Upward / Downward positioning) --}}
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200 transform"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150 transform"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute start-0 end-0 z-50 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl p-2 flex flex-col space-y-1"
                                     :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                                     style="display: none;">
                                    
                                    <template x-for="opt in items" :key="opt.id">
                                        <button type="button" 
                                                @click="select(opt.id)"
                                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-colors text-start"
                                                :class="selectedId == opt.id 
                                                    ? 'bg-primary/10 text-primary dark:bg-primary/20' 
                                                    : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60'">
                                            <span x-text="opt.name"></span>
                                            <template x-if="selectedId == opt.id">
                                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 space-y-2">
                            <button type="submit" class="w-full py-2.5 bg-primary text-white rounded-xl text-xs font-bold shadow-md shadow-primary/20">
                                {{ __('products.apply_filters') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Products Section (Card Grid View / Row List View / Empty State) --}}
        <div class="lg:col-span-3 xl:col-span-3 space-y-6">
            @if($products->count() > 0)
                
                {{-- 1. Card View (Grid) --}}
                <div x-show="viewMode === 'grid'" 
                     class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($products as $product)
                        @php
                            $coverUrl = $product->cover_image;
                            $productTitle = $product->name ?? __('products.preview_untitled');
                            $catName = $product->category 
                                ? ($currentAppLocale == 'ar' ? ($product->category->name_ar ?? $product->category->name_en) : ($product->category->name_en ?? $product->category->name_ar))
                                : __('products.preview_uncategorized');

                            $productData = [
                                'id' => $product->id,
                                'name' => $productTitle,
                                'category' => $catName,
                                'status' => $product->status,
                                'rejection_reason' => $product->rejection_reason,
                                'formatted_price' => $product->formatted_price,
                                'base_price' => $product->base_price,
                                'currency' => $product->currency,
                                'unit' => $product->localized_unit,
                                'package_details' => $product->package_details,
                                'moq' => $product->moq,
                                'sku' => $product->sku,
                                'short_description' => $product->short_description,
                                'full_description' => $product->full_description,
                                'specifications' => $product->specifications->map(fn($s) => ['name' => $s->name, 'value' => $s->value])->values()->all(),
                                'images' => $product->images->map(fn($img) => [
                                    'id' => $img->id,
                                    'url' => $img->file_url,
                                    'is_primary' => (bool)$img->is_primary,
                                    'order' => $img->order,
                                ])->values()->all(),
                                'cover_image' => $coverUrl,
                                'edit_url' => route('products.edit', $product->id),
                            ];
                        @endphp
                        <div class="group relative rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 hover:border-primary/50 dark:hover:border-primary/50 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden">
                            
                            {{-- Image Cover Container --}}
                            <div class="relative aspect-[4/3] bg-zinc-100 dark:bg-zinc-800/80 overflow-hidden shrink-0 cursor-pointer"
                                 @click="openProductDetail({{ json_encode($productData, JSON_HEX_APOS | JSON_HEX_QUOT) }}, $event)">
                                {{-- Non-Overlapping Top Badges Bar --}}
                                <div class="absolute top-3 inset-x-3 z-10 flex items-center justify-between gap-2 pointer-events-none">
                                    {{-- Status Badge (Start side) --}}
                                    <div class="pointer-events-auto shrink-0">
                                        @if($product->status === 'approved')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-500 text-white shadow-md shadow-emerald-500/20 backdrop-blur-md">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                                {{ __('products.status_approved') }}
                                            </span>
                                        @elseif($product->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-500 text-white shadow-md shadow-amber-500/20 backdrop-blur-md">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                                {{ __('products.status_pending') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-500 text-white shadow-md shadow-rose-500/20 backdrop-blur-md">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                {{ __('products.status_rejected') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Category Pill (End side) --}}
                                    <div class="pointer-events-auto min-w-0 max-w-[55%]">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider bg-black/60 dark:bg-zinc-900/80 text-white backdrop-blur-md border border-white/10 truncate max-w-full">
                                            {{ $catName }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Image or Fallback Placeholder --}}
                                @if($coverUrl)
                                    <img src="{{ $coverUrl }}" 
                                         alt="{{ $productTitle }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-zinc-400 dark:text-zinc-600 bg-zinc-50 dark:bg-zinc-800/40">
                                        <svg class="w-10 h-10 stroke-[1.3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        <span class="text-[11px] font-bold tracking-wider">{{ __('products.no_image') }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Card Body --}}
                            <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <h3 class="text-sm sm:text-base font-extrabold text-zinc-900 dark:text-zinc-100 group-hover:text-primary transition-colors line-clamp-2 text-start leading-snug">
                                        <a href="{{ route('products.edit', $product->id) }}">
                                            {{ $productTitle }}
                                        </a>
                                    </h3>

                                    {{-- Rejection Reason Alert Callout --}}
                                    @if($product->status === 'rejected' && $product->rejection_reason)
                                        <div class="mt-2.5 p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-900/50 text-start">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-[10px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-400">
                                                        {{ __('products.rejection_reason_label') }}
                                                    </p>
                                                    <p class="text-xs font-semibold text-rose-800 dark:text-rose-200 mt-0.5 leading-relaxed">
                                                        {{ $product->rejection_reason }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Commercial Metadata --}}
                                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800/80 space-y-1.5">
                                    {{-- Price & Unit --}}
                                    <div class="flex flex-wrap items-baseline justify-between gap-x-2 gap-y-1">
                                        <div class="text-start">
                                            @if($product->base_price)
                                                <span class="text-base sm:text-lg font-[900] text-primary whitespace-nowrap">
                                                    {{ $product->formatted_price }}
                                                </span>
                                                <span class="text-xs font-bold text-zinc-400 dark:text-zinc-500 whitespace-nowrap">
                                                    / {{ $product->localized_unit }}
                                                </span>
                                                @if($product->package_details)
                                                    <div class="text-[10px] font-bold text-amber-600 dark:text-amber-400 mt-0.5">
                                                        ({{ $product->package_details }})
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-xs font-black text-zinc-500 dark:text-zinc-400">
                                                    {{ __('products.preview_price_on_request') }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- MOQ --}}
                                        @if($product->moq)
                                            <span class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 shrink-0">
                                                {{ __('products.preview_moq') }} {{ $product->moq }} <span class="font-normal">{{ $product->localized_unit }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- SKU & Images count --}}
                                    <div class="flex items-center justify-between text-[11px] font-semibold text-zinc-400 dark:text-zinc-500">
                                        @if($product->sku)
                                            <span class="uppercase tracking-wider">
                                                {{ __('products.preview_sku') }} {{ $product->sku }}
                                            </span>
                                        @else
                                            <span></span>
                                        @endif

                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $product->images->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Quick Actions Footer --}}
                            <div @if($loop->first) id="tour-quick-actions" @endif class="px-4 py-3 bg-zinc-50/80 dark:bg-zinc-800/40 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between gap-2">
                                {{-- 1. Edit Button (Primary Action) --}}
                                <a href="{{ route('products.edit', $product->id) }}" 
                                   class="flex-1 h-9 px-3.5 rounded-xl bg-primary/10 hover:bg-primary text-primary hover:text-white dark:bg-primary/15 dark:hover:bg-primary dark:text-primary-light font-extrabold text-xs border border-primary/25 hover:border-primary flex items-center justify-center gap-1.5 transition-all duration-150 active:scale-95 shadow-xs">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    <span>{{ __('products.edit_product_action') }}</span>
                                </a>

                                {{-- 2. Public Listing / Detail Modal (Secondary Action) --}}
                                @if($product->status === 'approved' && !empty($business->slug))
                                    <a href="{{ url('/' . app()->getLocale() . '/directory/' . $business->slug . '#product-' . $product->id) }}"
                                       target="_blank"
                                       class="w-9 h-9 rounded-xl flex items-center justify-center text-xs transition-all duration-150 border shrink-0 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:text-primary hover:border-primary/50 hover:bg-primary/5 dark:hover:bg-primary/10 border-zinc-200 dark:border-zinc-700 shadow-sm cursor-pointer active:scale-95"
                                       title="{{ __('products.view_public_listing') }}"
                                       aria-label="{{ __('products.view_public_listing') }}">
                                        <svg class="w-4 h-4 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                @else
                                    <button type="button" 
                                            @click="openProductDetail({{ json_encode($productData, JSON_HEX_APOS | JSON_HEX_QUOT) }}, $event)"
                                            class="w-9 h-9 rounded-xl flex items-center justify-center text-xs transition-all duration-150 border shrink-0 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:text-primary hover:border-primary/50 hover:bg-primary/5 dark:hover:bg-primary/10 border-zinc-200 dark:border-zinc-700 shadow-sm cursor-pointer active:scale-95"
                                            title="{{ __('products.product_details') }}"
                                            aria-label="{{ __('products.product_details') }}">
                                        <svg class="w-4 h-4 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                @endif

                                {{-- 3. Delete Button (Tertiary Action) --}}
                                <button type="button" 
                                        @click="openDeleteModal({{ $product->id }}, {{ json_encode($productTitle, JSON_HEX_APOS | JSON_HEX_QUOT) }})"
                                        class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 dark:hover:bg-rose-950/30 dark:hover:border-rose-800/60 border border-zinc-200 dark:border-zinc-700 shadow-sm flex items-center justify-center text-xs transition-all duration-150 active:scale-95 shrink-0"
                                        title="{{ __('products.delete_product_action') }}"
                                        aria-label="{{ __('products.delete_product_action') }}">
                                    <svg class="w-4 h-4 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- 2. Row View (List / Table-style) --}}
                <div x-show="viewMode === 'list'" 
                     class="space-y-3.5" 
                     style="display: none;">
                    @foreach($products as $product)
                        @php
                            $coverUrl = $product->cover_image;
                            $productTitle = $product->name ?? __('products.preview_untitled');
                            $catName = $product->category 
                                ? ($currentAppLocale == 'ar' ? ($product->category->name_ar ?? $product->category->name_en) : ($product->category->name_en ?? $product->category->name_ar))
                                : __('products.preview_uncategorized');

                            $productDataRow = [
                                'id' => $product->id,
                                'name' => $productTitle,
                                'category' => $catName,
                                'status' => $product->status,
                                'rejection_reason' => $product->rejection_reason,
                                'formatted_price' => $product->formatted_price,
                                'base_price' => $product->base_price,
                                'currency' => $product->currency,
                                'unit' => $product->localized_unit,
                                'package_details' => $product->package_details,
                                'moq' => $product->moq,
                                'sku' => $product->sku,
                                'short_description' => $product->short_description,
                                'full_description' => $product->full_description,
                                'specifications' => $product->specifications->map(fn($s) => ['name' => $s->name, 'value' => $s->value])->values()->all(),
                                'images' => $product->images->map(fn($img) => [
                                    'id' => $img->id,
                                    'url' => $img->file_url,
                                    'is_primary' => (bool)$img->is_primary,
                                    'order' => $img->order,
                                ])->values()->all(),
                                'cover_image' => $coverUrl,
                                'edit_url' => route('products.edit', $product->id),
                            ];
                        @endphp
                        <div class="group rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 hover:border-primary/50 dark:hover:border-primary/50 shadow-sm hover:shadow-md transition-all duration-200 p-4 sm:p-5">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                
                                {{-- Col 1: Thumbnail + Product Name + Category & SKU (3 cols on md+) --}}
                                <div class="md:col-span-3 flex items-center gap-3 min-w-0">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden shrink-0 bg-zinc-100 dark:bg-zinc-800 relative border border-zinc-200/60 dark:border-zinc-700/60 cursor-pointer"
                                         @click="openProductDetail({{ json_encode($productDataRow, JSON_HEX_APOS | JSON_HEX_QUOT) }}, $event)">
                                        @if($coverUrl)
                                            <img src="{{ $coverUrl }}" alt="{{ $productTitle }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-zinc-400">
                                                <svg class="w-6 h-6 stroke-[1.4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1 space-y-1">
                                        <h4 class="text-xs sm:text-sm font-extrabold text-zinc-900 dark:text-zinc-100 group-hover:text-primary transition-colors truncate text-start leading-snug">
                                            <a href="{{ route('products.edit', $product->id) }}">
                                                {{ $productTitle }}
                                            </a>
                                        </h4>

                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-block text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                                {{ $catName }}
                                            </span>
                                            @if($product->sku)
                                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-tight">
                                                    {{ $product->sku }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Col 2: Status Badge (2 cols on md+) --}}
                                <div class="md:col-span-2 flex items-center md:justify-center">
                                    @if($product->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ __('products.status_approved') }}
                                        </span>
                                    @elseif($product->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-black uppercase tracking-wider bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            {{ __('products.status_pending') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-black uppercase tracking-wider bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            {{ __('products.status_rejected') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Col 3: Price (2 cols on md+) --}}
                                <div class="md:col-span-2 text-start md:border-s md:border-zinc-200/80 md:dark:border-zinc-800 md:ps-3">
                                    <div class="flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">
                                        <svg class="w-3 h-3 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="truncate">{{ __('products.price_label') }}</span>
                                    </div>
                                    <div class="text-xs sm:text-sm font-black text-primary mt-0.5">
                                        @if($product->base_price)
                                            <div class="whitespace-nowrap">{{ $product->formatted_price }}</div>
                                            <div class="text-[10px] sm:text-[11px] font-normal text-zinc-400 whitespace-nowrap">
                                                / {{ $product->localized_unit }}
                                                @if($product->package_details)
                                                    <span class="text-amber-600 dark:text-amber-400 font-bold block">({{ $product->package_details }})</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-zinc-500">{{ __('products.preview_price_on_request') }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Col 4: MOQ (2 cols on md+) --}}
                                <div class="md:col-span-2 text-start md:border-s md:border-zinc-200/80 md:dark:border-zinc-800 md:ps-3">
                                    <div class="flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">
                                        <svg class="w-3 h-3 text-zinc-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        <span class="truncate">{{ __('products.moq_label') }}</span>
                                    </div>
                                    <div class="text-xs font-black text-zinc-700 dark:text-zinc-300 mt-0.5 whitespace-nowrap">
                                        @if($product->moq)
                                            {{ $product->moq }} <span class="text-[10px] sm:text-[11px] font-normal text-zinc-400 dark:text-zinc-500">{{ $product->localized_unit }}</span>
                                        @else
                                            <span class="text-zinc-400">-</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Col 5: Quick Actions (3 cols on md+) --}}
                                <div class="md:col-span-3 flex items-center justify-start md:justify-end shrink-0 self-center">
                                    <div @if($loop->first) id="tour-quick-actions-row" @endif class="inline-flex items-center gap-1.5 flex-nowrap w-max">
                                        {{-- 1. Edit Button (Primary Action) --}}
                                        <a href="{{ route('products.edit', $product->id) }}" 
                                           class="h-9 px-3 rounded-xl bg-primary/10 hover:bg-primary text-primary hover:text-white dark:bg-primary/15 dark:hover:bg-primary dark:text-primary-light font-extrabold text-xs border border-primary/25 hover:border-primary flex items-center justify-center gap-1.5 transition-all duration-150 active:scale-95 shrink-0 shadow-xs"
                                           title="{{ __('products.edit_product_action') }}">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span>{{ __('products.edit_product_action') }}</span>
                                        </a>

                                        {{-- 2. Public Listing / Detail Modal (Secondary Action) --}}
                                        @if($product->status === 'approved' && !empty($business->slug))
                                            <a href="{{ url('/' . app()->getLocale() . '/directory/' . $business->slug . '#product-' . $product->id) }}"
                                               target="_blank"
                                               class="w-9 h-9 rounded-xl flex items-center justify-center text-xs transition-all duration-150 border shrink-0 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:text-primary hover:border-primary/50 hover:bg-primary/5 dark:hover:bg-primary/10 border-zinc-200 dark:border-zinc-700 shadow-xs cursor-pointer active:scale-95"
                                               title="{{ __('products.view_public_listing') }}"
                                               aria-label="{{ __('products.view_public_listing') }}">
                                                <svg class="w-4 h-4 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        @else
                                            <button type="button" 
                                                    @click="openProductDetail({{ json_encode($productDataRow, JSON_HEX_APOS | JSON_HEX_QUOT) }}, $event)"
                                                    class="w-9 h-9 rounded-xl flex items-center justify-center text-xs transition-all duration-150 border shrink-0 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:text-primary hover:border-primary/50 hover:bg-primary/5 dark:hover:bg-primary/10 border-zinc-200 dark:border-zinc-700 shadow-xs cursor-pointer active:scale-95"
                                                    title="{{ __('products.product_details') }}"
                                                    aria-label="{{ __('products.product_details') }}">
                                                <svg class="w-4 h-4 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        @endif

                                        {{-- 3. Delete Button (Tertiary Action) --}}
                                        <button type="button" 
                                                @click="openDeleteModal({{ $product->id }}, {{ json_encode($productTitle, JSON_HEX_APOS | JSON_HEX_QUOT) }})"
                                                class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 dark:hover:bg-rose-950/30 dark:hover:border-rose-800/60 border border-zinc-200 dark:border-zinc-700 shadow-xs flex items-center justify-center text-xs transition-all duration-150 active:scale-95 shrink-0"
                                                title="{{ __('products.delete_product_action') }}"
                                                aria-label="{{ __('products.delete_product_action') }}">
                                            <svg class="w-4 h-4 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Rejection Reason Alert if rejected (Bottom full-width row) --}}
                            @if($product->status === 'rejected' && $product->rejection_reason)
                                <div class="mt-3 pt-3 border-t border-rose-100 dark:border-rose-900/30">
                                    <div class="p-2.5 rounded-xl bg-rose-50/80 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-900/50 flex items-start gap-2 text-start">
                                        <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-400">
                                                {{ __('products.rejection_reason_label') }}
                                            </p>
                                            <p class="text-xs font-semibold text-rose-800 dark:text-rose-200 mt-0.5 leading-relaxed">
                                                {{ $product->rejection_reason }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="pt-4">
                    {{ $products->links() }}
                </div>

            @else
                {{-- Empty State (Both Card and Row view fallback) --}}
                <div class="p-10 sm:p-14 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 text-center shadow-sm">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-primary/10 text-primary mb-5 shadow-inner">
                        <svg class="w-10 h-10 stroke-[1.3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>

                    @if($hasActiveFilters)
                        <h3 class="text-lg sm:text-xl font-[900] text-zinc-900 dark:text-white tracking-tight">
                            {{ __('products.no_filtered_results') }}
                        </h3>
                        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1 max-w-md mx-auto font-medium">
                            {{ __('products.manage_products_desc') }}
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-bold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>{{ __('products.clear_filters') }}</span>
                            </a>
                        </div>
                    @else
                        <h3 class="text-lg sm:text-xl font-[900] text-zinc-900 dark:text-white tracking-tight">
                            {{ __('products.empty_state_title') }}
                        </h3>
                        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1.5 max-w-md mx-auto font-medium leading-relaxed">
                            {{ __('products.empty_state_desc') }}
                        </p>
                        <div id="tour-quick-actions" class="mt-6">
                            <a href="{{ route('products.create') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary hover:bg-primary/90 text-white text-sm font-bold shadow-lg shadow-primary/25 transition-all hover:scale-105 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>{{ __('products.add_first_product') }}</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal (Alpine.js) --}}
    <div x-show="deleteModal.open" 
         class="fixed inset-0 z-[10000] flex items-center justify-center p-4" 
         style="display: none;">
        {{-- Backdrop --}}
        <div x-show="deleteModal.open" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeDeleteModal()" 
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        {{-- Modal Card --}}
        <div x-show="deleteModal.open" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white dark:bg-[#18181b] border border-black/5 dark:border-white/[0.08] rounded-3xl p-6 sm:p-7 shadow-[0_24px_64px_rgba(0,0,0,0.25)] max-w-sm w-full z-10 text-center">
            
            <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>

            <h3 class="text-base font-[900] text-zinc-900 dark:text-white mb-1">
                {{ __('products.delete_modal_title') }}
            </h3>

            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-3 leading-relaxed">
                {{ __('products.delete_modal_desc') }}
            </p>

            <template x-if="deleteModal.productName">
                <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/60 dark:border-zinc-700/60 mb-5">
                    <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 truncate" x-text="deleteModal.productName"></p>
                </div>
            </template>

            <div class="flex gap-3">
                <button type="button" 
                        @click="closeDeleteModal()" 
                        :disabled="deleteModal.isDeleting"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-colors">
                    {{ __('products.cancel') }}
                </button>
                <button type="button" 
                        @click="confirmDelete()" 
                        :disabled="deleteModal.isDeleting"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/20 transition-all flex items-center justify-center gap-1.5">
                    <template x-if="deleteModal.isDeleting">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <span x-text="deleteModal.isDeleting ? '...' : '{{ __('products.delete_product_action') }}'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Product Detail Quick-View Modal (Phase 4) --}}
    @include('users.products.detail-modal')

    {{-- Reusable Fullscreen Photo Gallery Lightbox Component (Airbnb Pattern) --}}
    <x-gallery-lightbox id="product-gallery-lightbox" />

</div>

{{-- Onboarding Tour for Product Management Listing --}}
@include('users.products.tour', ['page' => 'index'])

<script>
function productsIndex() {
    return {
        viewMode: (new URLSearchParams(window.location.search)).get('view') || localStorage.getItem('alidebo_product_view') || 'grid',
        setViewMode(mode) {
            this.viewMode = mode;
            localStorage.setItem('alidebo_product_view', mode);
        },
        mobileFiltersOpen: false,
        detailModal: {
            open: false,
            product: null,
            activeImageIdx: 0,
            originEl: null
        },
        openProductDetail(product, event) {
            this.detailModal.product = product;
            this.detailModal.activeImageIdx = 0;
            this.detailModal.originEl = event ? event.currentTarget : null;
            this.detailModal.open = true;
        },
        closeDetailModal() {
            this.detailModal.open = false;
            this.detailModal.product = null;
        },
        getActiveHeroImage() {
            if (!this.detailModal.product) return null;
            const images = this.detailModal.product.images;
            if (images && images.length > 0 && images[this.detailModal.activeImageIdx]) {
                return images[this.detailModal.activeImageIdx].url;
            }
            return this.detailModal.product.cover_image || null;
        },
        launchGalleryFromModal(event) {
            if (!this.detailModal.product) return;
            const images = this.detailModal.product.images && this.detailModal.product.images.length > 0
                ? this.detailModal.product.images
                : (this.detailModal.product.cover_image ? [{ id: 'cover', url: this.detailModal.product.cover_image, is_primary: true }] : []);

            if (images.length === 0) return;

            let originRect = null;
            const targetEl = event?.currentTarget;
            if (targetEl && targetEl.getBoundingClientRect) {
                const r = targetEl.getBoundingClientRect();
                originRect = { top: r.top, left: r.left, width: r.width, height: r.height };
            }

            window.dispatchEvent(new CustomEvent('open-photo-gallery', {
                detail: {
                    images: images,
                    startIndex: this.detailModal.activeImageIdx || 0,
                    title: this.detailModal.product.name,
                    originRect: originRect,
                    isRtl: {{ $isRtl ? 'true' : 'false' }}
                }
            }));
        },
        deleteModal: {
            open: false,
            productId: null,
            productName: '',
            isDeleting: false
        },
        openDeleteModal(id, name) {
            this.deleteModal.productId = id;
            this.deleteModal.productName = name;
            this.deleteModal.open = true;
        },
        closeDeleteModal() {
            if (this.deleteModal.isDeleting) return;
            this.deleteModal.open = false;
            this.deleteModal.productId = null;
            this.deleteModal.productName = '';
        },
        toast: {
            visible: false,
            message: '',
            type: 'success'
        },
        showToast(message, type = 'success') {
            this.toast.message = message;
            this.toast.type = type;
            this.toast.visible = true;
            setTimeout(() => { this.toast.visible = false; }, 4000);
        },
        init() {
            let flashMsg = null;
            let flashType = 'success';

            @if(session('success'))
                flashMsg = @json(session('success'));
                flashType = 'success';
            @elseif(session('error'))
                flashMsg = @json(session('error'));
                flashType = 'error';
            @elseif(session('status'))
                flashMsg = @json(session('status'));
                flashType = 'success';
            @endif

            try {
                const stored = sessionStorage.getItem('alidebo_flash');
                if (stored) {
                    sessionStorage.removeItem('alidebo_flash');
                    if (!flashMsg) {
                        const parsed = JSON.parse(stored);
                        if (parsed && parsed.message) {
                            flashMsg = parsed.message;
                            flashType = parsed.type || 'success';
                        }
                    }
                }
            } catch (e) {}

            if (flashMsg) {
                this.showToast(flashMsg, flashType);
            }
        },
        async confirmDelete() {
            if (!this.deleteModal.productId || this.deleteModal.isDeleting) return;
            this.deleteModal.isDeleting = true;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/dashboard/business/products/${this.deleteModal.productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.showToast(data.message || @json(__('products.success_deleted')), 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.showToast(data.message || @json(__('products.server_error')), 'error');
                    this.deleteModal.isDeleting = false;
                }
            } catch (e) {
                console.error(e);
                this.showToast(@json(__('products.network_error')), 'error');
                this.deleteModal.isDeleting = false;
            }
        }
    };
}

document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        Alpine.data('productsIndex', productsIndex);
    }
});
</script>
@endsection
