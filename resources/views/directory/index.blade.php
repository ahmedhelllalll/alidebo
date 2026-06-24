@extends('layouts.app')

@section('title', __('directory.title'))

@push('styles')
<style>
    /* Fix for content going behind fixed header */
    .directory-container {
        padding-top: 100px;
        padding-bottom: 80px;
    }
    /* Ensure sidebar width on desktop bypassing Tailwind JIT issues */
    @media (min-width: 1024px) {
        .directory-container {
            padding-top: 120px;
            padding-bottom: 120px;
        }
        .directory-sidebar {
            width: 280px !important;
            flex-shrink: 0;
        }
        .directory-sticky {
            max-height: calc(100vh - 8rem);
            overflow-y: auto;
            overscroll-behavior: contain;
        }
    }
</style>
@endpush

@section('content')
<div class="directory-container min-h-screen bg-slate-50 dark:bg-[#09090b]">
    
    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 lg:mb-16 reveal">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-[900] tracking-tight text-slate-900 dark:text-white mb-6 leading-tight">
            <span class="glow-text">{{ __('directory.hero_headline') }}</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-500 dark:text-zinc-400 max-w-2xl font-medium leading-relaxed">
            {{ __('directory.hero_desc') }}
        </p>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('directory.index') }}" method="GET" class="flex flex-col lg:flex-row gap-8 lg:gap-12">
            
            <!-- Mobile Filter Toggle (Hidden on Desktop) -->
            <button type="button" onclick="document.getElementById('filter-sidebar').classList.toggle('hidden')" class="lg:hidden w-full flex items-center justify-between p-4 rounded-2xl border border-slate-200 dark:border-zinc-800/80 bg-white/60 dark:bg-zinc-900/40 backdrop-blur-xl shadow-sm text-sm font-bold text-slate-900 dark:text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    {{ __('directory.filters') }}
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Left Sidebar (Filters) -->
            <aside id="filter-sidebar" class="hidden lg:block w-full directory-sidebar">
                <div class="sticky top-28 space-y-8 p-6 lg:p-8 rounded-3xl border border-slate-200 dark:border-zinc-800/80 bg-white/60 dark:bg-zinc-900/40 backdrop-blur-xl shadow-sm directory-sticky">
                    
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('directory.filters') }}</h2>
                        <a href="{{ route('directory.index') }}" class="text-sm font-semibold text-primary hover:text-primary-light transition-colors">{{ __('directory.clear_filters') }}</a>
                    </div>

                    <!-- Search Input -->
                    <div class="space-y-3">
                        <div class="relative group">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('directory.search_placeholder') }}" 
                                onblur="this.form.submit()"
                                class="w-full bg-slate-100 dark:bg-zinc-800/50 border-0 rounded-2xl py-3 px-4 ps-11 text-sm font-medium text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-primary/50 transition-all">
                            <svg class="w-5 h-5 absolute top-3.5 start-4 text-slate-400 dark:text-zinc-500 group-focus-within:text-primary transition-colors pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <!-- Categories (Vertical List) -->
                    <div class="space-y-4">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-500">{{ __('directory.categories') }}</label>
                        <div class="flex flex-col gap-1">
                            <label class="flex items-center gap-3 cursor-pointer group py-2 px-3 -mx-3 rounded-xl hover:bg-slate-100 dark:hover:bg-zinc-800/50 transition-colors">
                                <input type="radio" name="category" value="" onchange="this.form.submit()" {{ !request('category') ? 'checked' : '' }} class="peer hidden">
                                <div class="w-4 h-4 rounded-full border-2 border-slate-300 dark:border-zinc-600 flex items-center justify-center group-hover:border-primary transition-colors peer-checked:border-primary peer-checked:bg-primary/10">
                                    <div class="w-1.5 h-1.5 rounded-full bg-primary scale-0 peer-checked:scale-100 transition-transform duration-300"></div>
                                </div>
                                <span class="text-sm font-medium text-slate-600 dark:text-zinc-400 group-hover:text-slate-900 dark:group-hover:text-white peer-checked:text-primary peer-checked:font-bold transition-all">
                                    {{ __('directory.all_categories') }}
                                </span>
                            </label>
                            @foreach($categories as $category)
                            <label class="flex items-center gap-3 cursor-pointer group py-2 px-3 -mx-3 rounded-xl hover:bg-slate-100 dark:hover:bg-zinc-800/50 transition-colors">
                                <input type="radio" name="category" value="{{ $category->id }}" onchange="this.form.submit()" {{ request('category') == $category->id ? 'checked' : '' }} class="peer hidden">
                                <div class="w-4 h-4 rounded-full border-2 border-slate-300 dark:border-zinc-600 flex items-center justify-center group-hover:border-primary transition-colors peer-checked:border-primary peer-checked:bg-primary/10">
                                    <div class="w-1.5 h-1.5 rounded-full bg-primary scale-0 peer-checked:scale-100 transition-transform duration-300"></div>
                                </div>
                                <span class="text-sm font-medium text-slate-600 dark:text-zinc-400 group-hover:text-slate-900 dark:group-hover:text-white peer-checked:text-primary peer-checked:font-bold transition-all">
                                    {{ $category->name }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Locations (Cities) -->
                    <div class="space-y-3">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-500">{{ __('directory.locations') }}</label>
                        <div class="relative">
                            <select name="city" onchange="this.form.submit()" class="w-full bg-slate-100 dark:bg-zinc-800/50 border-0 rounded-2xl py-3 px-4 text-sm font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 transition-all appearance-none cursor-pointer">
                                <option value="">{{ __('directory.all_locations') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            <svg class="w-4 h-4 absolute top-3.5 end-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                </div>
            </aside>

            <!-- Right Grid (Companies) -->
            <div class="flex-1 space-y-8">
                
                <!-- Top Bar -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-2xl border border-slate-200 dark:border-zinc-800/80 bg-white/60 dark:bg-zinc-900/40 backdrop-blur-xl shadow-sm reveal">
                    <div class="text-sm font-bold text-slate-600 dark:text-zinc-400 px-2">
                        <span class="text-primary">{{ $businesses->total() }}</span> {{ __('directory.results_found') }}
                    </div>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <label class="text-sm font-bold text-slate-600 dark:text-zinc-400 shrink-0">{{ __('directory.sort_by') }}</label>
                        <div class="relative w-full sm:w-48">
                            <select name="sort" onchange="this.form.submit()" class="w-full bg-white dark:bg-[#0e0e11] border border-slate-200 dark:border-zinc-800 rounded-xl py-2.5 px-4 text-sm font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 transition-all cursor-pointer appearance-none">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('directory.sort_newest') }}</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('directory.sort_oldest') }}</option>
                                <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>{{ __('directory.sort_az') }}</option>
                                <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>{{ __('directory.sort_za') }}</option>
                            </select>
                            <svg class="w-4 h-4 absolute top-3.5 end-3 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Grid -->
                @if($businesses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($businesses as $business)
                        <div class="luxury-card flex flex-col group reveal">
                            <!-- Cover -->
                            <div class="h-32 w-full bg-slate-100 dark:bg-zinc-800 relative overflow-hidden shrink-0 border-b border-slate-100 dark:border-zinc-800/50">
                                @if($business->cover)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($business->cover) }}" alt="{{ $business->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 dark:from-zinc-800 dark:to-zinc-900/50 transition-transform duration-700 group-hover:scale-105"></div>
                                @endif
                            </div>
                            
                            <!-- Body -->
                            <div class="p-6 pt-0 flex-1 flex flex-col relative">
                                <!-- Logo -->
                                <div class="w-16 h-16 rounded-2xl bg-white dark:bg-[#121214] border-4 border-white dark:border-[#121214] shadow-sm -mt-8 mb-4 relative z-10 flex items-center justify-center overflow-hidden shrink-0 self-start transition-transform duration-300 group-hover:-translate-y-1">
                                    @if($business->logo)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($business->logo) }}" alt="{{ $business->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl font-[900] text-slate-400 dark:text-zinc-500 uppercase">{{ mb_substr($business->name, 0, 1) }}</span>
                                    @endif
                                </div>

                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white line-clamp-1 group-hover:text-primary transition-colors">
                                        {{ $business->name }}
                                    </h3>
                                    @if($business->is_claimed)
                                    <div title="{{ __('directory.verified') }}" class="text-blue-500 shrink-0 bg-blue-50 dark:bg-blue-500/10 p-1 rounded-full">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 mb-4 flex-wrap">
                                    @if($business->category)
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-zinc-800/80 text-[11px] font-bold text-slate-600 dark:text-zinc-400 uppercase tracking-wide">
                                        {{ $business->category->name }}
                                    </span>
                                    @endif
                                    @if($business->city)
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-zinc-800/80 text-[11px] font-bold text-slate-600 dark:text-zinc-400 uppercase tracking-wide flex items-center gap-1">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $business->city->name }}
                                    </span>
                                    @endif
                                </div>

                                <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 line-clamp-3 mb-6 flex-1 leading-relaxed">
                                    {{ $business->description ?? '...' }}
                                </p>

                                <a href="{{ route('business.view', $business->slug) }}" class="mt-auto flex items-center justify-center w-full py-2.5 rounded-xl bg-white dark:bg-[#121214] text-sm font-bold text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-zinc-800 border border-slate-200 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700 transition-all duration-300 shadow-sm">
                                    {{ __('directory.view_profile') }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center reveal">
                        {{ $businesses->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center py-24 px-4 text-center bg-white/60 dark:bg-zinc-900/40 backdrop-blur-xl rounded-3xl border border-slate-200 dark:border-zinc-800/80 reveal">
                        <div class="w-20 h-20 bg-slate-100 dark:bg-zinc-800/80 rounded-full flex items-center justify-center mb-6 shadow-inner">
                            <svg class="w-10 h-10 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                        </div>
                        <h3 class="text-xl font-[900] tracking-tight text-slate-900 dark:text-white mb-2">{{ __('directory.no_results') }}</h3>
                        <p class="text-slate-500 dark:text-zinc-400 font-medium max-w-sm">{{ __('directory.try_different') }}</p>
                        <a href="{{ route('directory.index') }}" class="mt-8 px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-sm shadow-lg shadow-black/5 hover:bg-slate-800 dark:hover:bg-slate-100 transition-all">
                            {{ __('directory.clear_filters') }}
                        </a>
                    </div>
                @endif

            </div>
        </form>
    </div>
</div>
@endsection
