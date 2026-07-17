@extends('admin.layouts.admin')

@section('title', __('admin.seo_settings'))

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.seo_dashboard') }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.seo_desc') }}</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard.seo.search-insights') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(99,102,241,0.25)] hover:shadow-[0_12px_25px_rgba(99,102,241,0.35)] transition-all active:scale-[0.98]">
                <i class="fa-solid fa-chart-line text-[14px]"></i>
                {{ __('admin.google_search_insights') }}
            </a>
        </div>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Sitemap Stats Card -->
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl p-6 flex flex-col justify-between dashboard-card-reveal">
            <div>
                <div class="w-12 h-12 rounded-xl bg-primary/10 dark:bg-primary/20 flex items-center justify-center text-primary mb-4">
                    <i class="fa-solid fa-sitemap text-xl"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ __('admin.dynamic_xml_sitemap') }}</h2>
                <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400 mb-6">{{ __('admin.sitemap_desc') }}</p>
                
                <div class="space-y-4 mb-6 bg-slate-50 dark:bg-white/5 rounded-xl p-4 border border-slate-100 dark:border-white/5">
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-1">{{ __('admin.sitemap_url') }}</div>
                        <a href="{{ $sitemapUrl }}" target="_blank" class="text-[13px] font-bold text-primary hover:text-primary-light truncate block transition-colors">{{ $sitemapUrl }}</a>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('admin.indexed_links') }}</div>
                        <div class="text-[14px] font-black text-slate-900 dark:text-white">{{ $indexedLinks }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ __('admin.last_generated') }}</div>
                        <div class="text-[13px] font-bold text-slate-700 dark:text-zinc-300">{{ is_string($lastGenerated) ? $lastGenerated : $lastGenerated->diffForHumans() }}</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.dashboard.seo.sitemap.regenerate') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
                    <i class="fa-solid fa-rotate text-[14px]"></i>
                    {{ __('admin.regenerate_sitemap_now') }}
                </button>
            </form>
        </div>

        <!-- Meta Editor Link -->
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl p-6 flex flex-col justify-between dashboard-card-reveal" style="animation-delay: 100ms;">
            <div>
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-500 mb-4">
                    <i class="fa-regular fa-file-lines text-xl"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ __('admin.static_pages_seo') }}</h2>
                <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400 mb-6">{{ __('admin.static_pages_seo_desc') }}</p>
            </div>
            <a href="{{ route('admin.pages.index') }}" class="w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-[#121214] hover:bg-slate-50 dark:hover:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] transition-all shadow-sm">
                <i class="fa-solid fa-arrow-right rtl:fa-arrow-left text-[14px]"></i>
                {{ __('admin.manage_pages') }}
            </a>
        </div>

        <!-- Redirects Manager Card -->
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl p-6 flex flex-col justify-between dashboard-card-reveal" style="animation-delay: 200ms;">
            <div>
                <div class="w-12 h-12 rounded-xl bg-orange-500/10 dark:bg-orange-500/20 flex items-center justify-center text-orange-500 mb-4">
                    <i class="fa-solid fa-route text-xl"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ __('admin.redirects_manager') }}</h2>
                <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400 mb-6">{{ __('admin.redirects_desc') }}</p>
            </div>
            <a href="{{ route('admin.seo.redirects.index') }}" class="w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-[#121214] hover:bg-slate-50 dark:hover:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] transition-all shadow-sm">
                <i class="fa-solid fa-arrow-right rtl:fa-arrow-left text-[14px]"></i>
                {{ __('admin.manage_redirects') }}
            </a>
        </div>

        <!-- 404 Error Logger Card -->
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl p-6 flex flex-col justify-between dashboard-card-reveal" style="animation-delay: 300ms;">
            <div>
                <div class="w-12 h-12 rounded-xl bg-red-500/10 dark:bg-red-500/20 flex items-center justify-center text-red-500 mb-4">
                    <i class="fa-solid fa-link-slash text-xl"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ __('admin.404_logger') }}</h2>
                <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400 mb-6">{{ __('admin.404_logger_desc') }}</p>
            </div>
            <a href="{{ route('admin.seo.failed-links.index') }}" class="w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-[#121214] hover:bg-slate-50 dark:hover:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] transition-all shadow-sm">
                <i class="fa-solid fa-arrow-right rtl:fa-arrow-left text-[14px]"></i>
                {{ __('admin.view_404_logs') }}
            </a>
        </div>

        <!-- Robots.txt Editor Card -->
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl p-6 flex flex-col justify-between dashboard-card-reveal" style="animation-delay: 400ms;">
            <div>
                <div class="w-12 h-12 rounded-xl bg-teal-500/10 dark:bg-teal-500/20 flex items-center justify-center text-teal-500 mb-4">
                    <i class="fa-solid fa-robot text-xl"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ __('admin.robots_editor') }}</h2>
                <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400 mb-6">{{ __('admin.robots_editor_desc') }}</p>
            </div>
            <a href="{{ route('admin.seo.robots') }}" class="w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-[#121214] hover:bg-slate-50 dark:hover:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] transition-all shadow-sm">
                <i class="fa-solid fa-arrow-right rtl:fa-arrow-left text-[14px]"></i>
                {{ __('admin.edit_robots_txt') }}
            </a>
        </div>
    </div>
</div>
@endsection
