@extends('admin.layouts.admin')
@section('title', __('admin.dashboard'))
@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] ltr:tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.dashboard') }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.welcome_overview') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.businesses.create') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-bold text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('admin.add_new') ?? 'Add New Business' }}
            </a>
        </div>
    </div>
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5 relative z-10">
        {{-- Active Businesses --}}
        <div class="stat-card group relative overflow-hidden rounded-[22px] bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgba(16,185,129,0.12)] hover:-translate-y-1.5 cursor-default">

            {{-- Glow --}}
            <div class="absolute -top-20 -end-20 w-48 h-48 bg-emerald-500/[0.03] dark:bg-emerald-500/[0.02] rounded-full blur-3xl group-hover:scale-[1.4] transition-transform duration-700"></div>
            <div class="relative z-10 p-5 pb-3">
                {{-- Header row: icon + label + growth --}}
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/10 to-teal-500/5 border border-emerald-500/10 flex items-center justify-center text-emerald-500 dark:text-emerald-400 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider leading-tight">{{ __('admin.active_businesses') ?? 'Active Businesses' }}</p>
                    </div>
                    @if(isset($stats['business_growth']))
                        <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-1 rounded-lg {{ $stats['business_growth'] >= 0 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' }}">
                            <i class="fa-solid {{ $stats['business_growth'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} text-[8px]"></i>
                            {{ $stats['business_growth'] > 0 ? '+' : '' }}{{ $stats['business_growth'] }}%
                        </span>
                    @endif
                </div>
                {{-- Big number --}}
                <h3 class="gsap-counter text-[30px] lg:text-[34px] font-[800] text-slate-900 dark:text-white tracking-tight leading-none" data-value="{{ $stats['active_businesses'] ?? 0 }}">0</h3>
            </div>
            {{-- Mini sparkline --}}
            <div class="stat-sparkline h-12 w-full opacity-60 group-hover:opacity-100 transition-opacity duration-500" data-color="#10b981" data-values="3,5,4,7,6,8,9"></div>
        </div>
        {{-- Total Users --}}
        <div class="stat-card group relative overflow-hidden rounded-[22px] bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgba(99,102,241,0.12)] hover:-translate-y-1.5 cursor-default">

            <div class="absolute -top-20 -end-20 w-48 h-48 bg-indigo-500/[0.03] dark:bg-indigo-500/[0.02] rounded-full blur-3xl group-hover:scale-[1.4] transition-transform duration-700"></div>
            <div class="relative z-10 p-5 pb-3">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500/10 to-purple-500/5 border border-indigo-500/10 flex items-center justify-center text-indigo-500 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider leading-tight">{{ __('admin.users') }}</p>
                    </div>
                    @if(isset($stats['user_growth']))
                        <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-1 rounded-lg {{ $stats['user_growth'] >= 0 ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' }}">
                            <i class="fa-solid {{ $stats['user_growth'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} text-[8px]"></i>
                            {{ $stats['user_growth'] > 0 ? '+' : '' }}{{ $stats['user_growth'] }}%
                        </span>
                    @endif
                </div>
                <h3 class="gsap-counter text-[30px] lg:text-[34px] font-[800] text-slate-900 dark:text-white tracking-tight leading-none" data-value="{{ $stats['users'] ?? 0 }}">0</h3>
            </div>
            <div class="stat-sparkline h-12 w-full opacity-60 group-hover:opacity-100 transition-opacity duration-500" data-color="#6366f1" data-values="2,4,3,5,7,6,8"></div>
        </div>
        {{-- Platform Views --}}
        <div class="stat-card group relative overflow-hidden rounded-[22px] bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgba(244,80,24,0.12)] hover:-translate-y-1.5 cursor-default">

            <div class="absolute -top-20 -end-20 w-48 h-48 bg-primary/[0.03] dark:bg-primary/[0.02] rounded-full blur-3xl group-hover:scale-[1.4] transition-transform duration-700"></div>
            <div class="relative z-10 p-5 pb-3">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary/10 to-orange-400/5 border border-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider leading-tight">{{ __('admin.platform_views') ?? 'Platform Views' }}</p>
                    </div>
                    @if(isset($stats['views_growth']))
                        <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-1 rounded-lg {{ $stats['views_growth'] >= 0 ? 'bg-orange-50 dark:bg-primary/10 text-primary' : 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' }}">
                            <i class="fa-solid {{ $stats['views_growth'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} text-[8px]"></i>
                            {{ $stats['views_growth'] > 0 ? '+' : '' }}{{ $stats['views_growth'] }}%
                        </span>
                    @endif
                </div>
                <h3 class="gsap-counter text-[30px] lg:text-[34px] font-[800] text-slate-900 dark:text-white tracking-tight leading-none" data-value="{{ $stats['total_views'] ?? 0 }}">0</h3>
            </div>
            <div class="stat-sparkline h-12 w-full opacity-60 group-hover:opacity-100 transition-opacity duration-500" data-color="#f45018" data-values="5,3,6,4,8,7,10"></div>
        </div>
        {{-- Pending Approvals --}}
        <div class="stat-card group relative overflow-hidden rounded-[22px] bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgba(245,158,11,0.12)] hover:-translate-y-1.5 cursor-default">

            <div class="absolute -top-20 -end-20 w-48 h-48 bg-amber-500/[0.03] dark:bg-amber-500/[0.02] rounded-full blur-3xl group-hover:scale-[1.4] transition-transform duration-700"></div>
            <div class="relative z-10 p-5 pb-3">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500/10 to-orange-500/5 border border-amber-500/10 flex items-center justify-center text-amber-500 dark:text-amber-400 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider leading-tight">{{ __('admin.approvals') }}</p>
                    </div>
                    @if(isset($stats['claimed_businesses']))
                        <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-1 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400" title="Claimed Businesses">
                            <i class="fa-solid fa-check-circle text-[8px]"></i>
                            {{ $stats['claimed_businesses'] }}
                        </span>
                    @endif
                </div>
                <h3 class="gsap-counter text-[30px] lg:text-[34px] font-[800] text-slate-900 dark:text-white tracking-tight leading-none" data-value="{{ $stats['pending'] ?? 0 }}">0</h3>
            </div>
            <div class="stat-sparkline h-12 w-full opacity-60 group-hover:opacity-100 transition-opacity duration-500" data-color="#f59e0b" data-values="4,6,3,5,2,4,3"></div>
        </div>
        {{-- Unread Leads --}}
        <div class="stat-card group relative overflow-hidden rounded-[22px] bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgba(249,115,22,0.12)] hover:-translate-y-1.5 cursor-default">

            <div class="absolute -top-20 -end-20 w-48 h-48 bg-orange-500/[0.03] dark:bg-orange-500/[0.02] rounded-full blur-3xl group-hover:scale-[1.4] transition-transform duration-700"></div>
            <div class="relative z-10 p-5 pb-3">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500/10 to-red-500/5 border border-orange-500/10 flex items-center justify-center text-orange-500 dark:text-orange-400 group-hover:scale-110 transition-transform duration-500">
                            <i class="fa-solid fa-headset text-[14px]"></i>
                        </div>
                        <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider leading-tight">{{ __('admin.unread_leads') ?? 'Unread Leads' }}</p>
                    </div>
                    @if(isset($stats['total_leads']))
                        <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-1 rounded-lg bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400" title="{{ __('admin.total') ?? 'Total Leads' }}">
                            <i class="fa-solid fa-inbox text-[8px]"></i>
                            {{ $stats['total_leads'] }}
                        </span>
                    @endif
                </div>
                <h3 class="gsap-counter text-[30px] lg:text-[34px] font-[800] text-slate-900 dark:text-white tracking-tight leading-none" data-value="{{ $stats['unread_leads'] ?? 0 }}">0</h3>
            </div>
            <div class="stat-sparkline h-12 w-full opacity-60 group-hover:opacity-100 transition-opacity duration-500" data-color="#f97316" data-values="6,4,7,5,8,6,9"></div>
        </div>
    </div>
    {{-- Analytics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 mt-6 relative z-10 w-full overflow-hidden">
        {{-- Registration & Views Chart --}}
        <div class="chart-card md:col-span-2 bg-white dark:bg-[#18181b] backdrop-blur-md p-4 sm:p-6 rounded-[22px] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col min-h-[350px] overflow-hidden relative">
            {{-- Loading spinner overlay for chart --}}
            <div id="chart-loading" class="absolute inset-0 flex items-center justify-center bg-white/60 dark:bg-zinc-900/60 backdrop-blur-[2px] z-20 hidden rounded-[22px]">
                <div class="w-8 h-8 border-4 border-slate-200 border-t-primary dark:border-zinc-700 dark:border-t-primary rounded-full animate-spin"></div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2 shrink-0">
                <h2 id="chart-title" class="text-[15px] sm:text-[16px] font-[900] text-slate-900 dark:text-white tracking-tight">
                    {{ __('admin.platform_activity') }}
                </h2>
                <div class="flex p-0.5 bg-slate-100 dark:bg-zinc-800 rounded-xl border border-slate-200/50 dark:border-zinc-700/50 shadow-inner">
                    <button onclick="setAdminChartPeriod('week')" class="admin-period-btn px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all" data-period="week">{{ __('admin.week') }}</button>
                    <button onclick="setAdminChartPeriod('month')" class="admin-period-btn px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all" data-period="month">{{ __('admin.month') }}</button>
                </div>
            </div>
            {{-- Custom Chart Legend --}}
            <div class="flex flex-wrap items-center justify-between gap-x-2 gap-y-2 mb-3 px-1 w-full">
                <div class="flex items-center gap-2 chart-legend-item" data-series="0">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 ring-[3px] ring-emerald-500/20 shrink-0"></span>
                    <span class="text-[12px] font-bold text-slate-600 dark:text-zinc-400">{{ __('admin.users') }}</span>
                </div>
                <div class="flex items-center gap-2 chart-legend-item" data-series="1">
                    <span class="w-3 h-3 rounded-full bg-indigo-500 ring-[3px] ring-indigo-500/20 shrink-0"></span>
                    <span class="text-[12px] font-bold text-slate-600 dark:text-zinc-400">{{ __('admin.businesses') }}</span>
                </div>
                <div class="flex items-center gap-2 chart-legend-item" data-series="2">
                    <span class="w-3 h-3 rounded-full bg-primary ring-[3px] ring-primary/20 shrink-0"></span>
                    <span class="text-[12px] font-bold text-slate-600 dark:text-zinc-400">{{ __('admin.platform_views') }}</span>
                </div>
            </div>
            <div id="registrationChart" class="w-full h-full flex-1 min-h-[250px]"></div>
        </div>
        {{-- Business Status Pie Chart --}}
        <div class="chart-card md:col-span-2 lg:col-span-1 bg-white dark:bg-[#18181b] backdrop-blur-md p-4 sm:p-6 rounded-[22px] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col min-h-[350px] overflow-hidden">
            <div class="flex items-center justify-between mb-4 shrink-0">
                <h2 class="text-[15px] sm:text-[16px] font-[900] text-slate-900 dark:text-white tracking-tight">{{ __('admin.business_statuses') }}</h2>
            </div>
            {{-- Custom Donut Legend --}}
            <div class="flex flex-wrap items-center justify-between gap-x-2 gap-y-2 mb-4 px-1 w-full">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="text-[11px] font-bold text-slate-500 dark:text-zinc-400">{{ __('admin.approved') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-[11px] font-bold text-slate-500 dark:text-zinc-400">{{ __('admin.pending') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 shrink-0"></span>
                    <span class="text-[11px] font-bold text-slate-500 dark:text-zinc-400">{{ __('admin.rejected') }}</span>
                </div>
            </div>
            <div id="statusChart" class="w-full h-full flex-1 flex items-center justify-center min-h-[250px]"></div>
        </div>
    </div>
    {{-- Quick Actions --}}
    <div class="mt-6 relative z-10 w-full overflow-hidden">
        <h2 class="text-[15px] sm:text-[16px] font-[900] text-slate-900 dark:text-white tracking-tight mb-4 px-1">{{ __('admin.quick_actions') ?? 'Quick Actions' }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.businesses.create') }}" class="group flex flex-col items-center justify-center p-5 bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[20px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(244,80,24,0.1)] transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-[13px] font-[900] text-slate-700 dark:text-zinc-300 group-hover:text-primary transition-colors text-center">{{ __('admin.add_new_business') ?? 'New Business' }}</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="group flex flex-col items-center justify-center p-5 bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[20px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-indigo-500/10 text-indigo-500 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-[13px] font-[900] text-slate-700 dark:text-zinc-300 group-hover:text-indigo-500 transition-colors text-center">{{ __('admin.manage_users') ?? 'Manage Users' }}</span>
            </a>
            <a href="{{ route('admin.businesses.index', ['status' => 'pending']) }}" class="group flex flex-col items-center justify-center p-5 bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[20px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[13px] font-[900] text-slate-700 dark:text-zinc-300 group-hover:text-amber-500 transition-colors text-center">{{ __('admin.pending_approvals') ?? 'Pending Approvals' }}</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="group flex flex-col items-center justify-center p-5 bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[20px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <span class="text-[13px] font-[900] text-slate-700 dark:text-zinc-300 group-hover:text-emerald-500 transition-colors text-center">{{ __('admin.manage_categories') ?? 'Manage Categories' }}</span>
            </a>
        </div>
    </div>
    {{-- Dynamic Lists Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mt-6 relative z-10">
        {{-- Recent Businesses --}}
        <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-zinc-800/60 flex items-center justify-between bg-slate-50/30 dark:bg-zinc-900/20">
                <h2 class="text-[15px] font-[900] text-slate-900 dark:text-white tracking-tight">{{ __('admin.recent_businesses') }}</h2>
                <a href="{{ route('admin.businesses.index') }}" class="text-[13px] font-bold text-primary hover:text-primary-dark dark:hover:text-primary-light transition-colors flex items-center gap-1 group">
                    {{ __('admin.view_all') }}
                    <svg class="w-4 h-4 rtl:rotate-180 transform group-hover:translate-x-1 transition-transform rtl:group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-zinc-800/50 flex-1">
                @forelse(collect($recent_businesses ?? [])->take(5) as $business)
                <div class="px-6 py-4 hover:bg-slate-50/80 dark:hover:bg-zinc-800/30 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4 group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-700/50 flex items-center justify-center font-[900] text-slate-400 dark:text-zinc-500 shadow-sm overflow-hidden shrink-0 group-hover:border-primary/30 transition-colors">
                            @if($business->logo)
                                <img src="{{ $business->logo_url }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($business->name, 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-[14px] leading-tight group-hover:text-primary transition-colors">{{ $business->name }}</p>
                            <p class="text-[12px] font-medium text-slate-500 dark:text-zinc-500 mt-0.5">{{ $business->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center shrink-0">
                        <x-admin.badge :type="$business->status" :text="__('admin.' . $business->status)" />
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-slate-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center text-slate-300 dark:text-zinc-600 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <p class="text-[14px] font-[900] text-slate-900 dark:text-white">{{ __('admin.no_data') }}</p>
                </div>
                @endforelse
            </div>
        </div>
        {{-- Recent Users --}}
        <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-zinc-800/60 flex items-center justify-between bg-slate-50/30 dark:bg-zinc-900/20">
                <h2 class="text-[15px] font-[900] text-slate-900 dark:text-white tracking-tight">{{ __('admin.recent_users') }}</h2>
                <a href="{{ route('admin.users.index') }}" class="text-[13px] font-bold text-primary hover:text-primary-dark dark:hover:text-primary-light transition-colors flex items-center gap-1 group">
                    {{ __('admin.view_all') }}
                    <svg class="w-4 h-4 rtl:rotate-180 transform group-hover:translate-x-1 transition-transform rtl:group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-zinc-800/50 flex-1">
                @forelse(collect($recent_users ?? [])->take(5) as $user)
                <div class="px-6 py-4 hover:bg-slate-50/80 dark:hover:bg-zinc-800/30 transition-all flex items-center justify-between gap-4 group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-slate-100 to-slate-200 dark:from-zinc-800 dark:to-zinc-700 border border-slate-200/60 dark:border-zinc-600/50 flex items-center justify-center font-[900] text-slate-600 dark:text-zinc-300 shadow-inner overflow-hidden shrink-0 group-hover:border-slate-300 dark:group-hover:border-zinc-500 transition-colors">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-[14px] leading-tight group-hover:text-slate-700 dark:group-hover:text-zinc-200 transition-colors">{{ $user->name }}</p>
                            <p class="text-[12px] font-medium text-slate-500 dark:text-zinc-500 mt-0.5">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="shrink-0 text-[12px] text-slate-400 dark:text-zinc-500 font-bold whitespace-nowrap bg-slate-50 dark:bg-zinc-800/50 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-zinc-700/50">
                        {{ $user->created_at->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-slate-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center text-slate-300 dark:text-zinc-600 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-[14px] font-[900] text-slate-900 dark:text-white">{{ __('admin.no_data') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Safe check for GSAP
        if (typeof gsap === 'undefined') return;
        // Header entrance
        gsap.from('.dashboard-header-reveal', {
            y: -20,
            opacity: 0,
            duration: 0.8,
            ease: "power3.out",
            delay: 0.1
        });
        // Stagger Stats Cards
        gsap.from('.stat-card', {
            y: 30,
            opacity: 0,
            duration: 0.8,
            stagger: 0.1,
            ease: "back.out(1.2)",
            delay: 0.2,
            onComplete: function() {
                // Let native CSS handle hover effects after GSAP entrance clears
                gsap.set('.stat-card', { clearProps: "transform,opacity" });
                document.querySelectorAll('.stat-card').forEach(el => {
                    el.classList.add('transition-all', 'duration-500');
                });
            }
        });
        // GSAP Number Counters for UI luxury feel
        const counters = document.querySelectorAll('.gsap-counter');
        counters.forEach(counter => {
            const finalValue = parseInt(counter.getAttribute('data-value').replace(/,/g, ''), 10) || 0;
            const formatValue = { val: 0 };
            gsap.to(formatValue, {
                val: finalValue,
                duration: 2,
                ease: "power3.out",
                delay: 0.5,
                onUpdate: function() {
                    counter.innerHTML = Math.ceil(formatValue.val).toLocaleString('{{ app()->getLocale() }}');
                }
            });
        });
        // Charts Stagger
        gsap.from('.chart-card', {
            y: 40,
            opacity: 0,
            duration: 0.8,
            stagger: 0.15,
            ease: "power3.out",
            delay: 0.5
        });
        // Lists Stagger
        gsap.from('.list-card', {
            y: 40,
            opacity: 0,
            duration: 0.8,
            stagger: 0.15,
            ease: "power3.out",
            delay: 0.7
        });
        // Initialize ApexCharts
        if (typeof ApexCharts !== 'undefined') {
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#a1a1aa' : '#64748b';
            const gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            
            let regChart = null;

            // Registration Chart Options
            const regOptions = {
                series: [{
                    name: '{{ __("admin.users") }}',
                    data: []
                }, {
                    name: '{{ __("admin.businesses") }}',
                    data: []
                }, {
                    name: '{{ __("admin.platform_views") }}',
                    data: []
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'inherit',
                    background: 'transparent',
                    parentHeightOffset: 0,
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                    }
                },
                colors: ['#10b981', '#6366f1', '#f45018'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: {
                    categories: [],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: textColor } }
                },
                yaxis: {
                    labels: { style: { colors: textColor } }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4,
                },
                theme: { mode: isDarkMode ? 'dark' : 'light' },
                legend: { show: false },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light',
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(val) {
                            return val !== undefined ? val.toLocaleString('{{ app()->getLocale() }}') : '0';
                        }
                    },
                    style: { fontSize: '12px', fontFamily: 'inherit' },
                    marker: { show: true }
                }
            };
            regChart = new ApexCharts(document.querySelector("#registrationChart"), regOptions);
            regChart.render();

            // Function to load dynamic chart data
            window.setAdminChartPeriod = function(period) {
                document.querySelectorAll('.admin-period-btn').forEach(btn => {
                    btn.classList.remove('bg-white', 'dark:bg-zinc-700', 'text-slate-900', 'dark:text-white', 'shadow-sm');
                    btn.classList.add('text-slate-500', 'dark:text-zinc-400');
                });
                const activeBtn = document.querySelector(`.admin-period-btn[data-period="${period}"]`);
                if (activeBtn) {
                    activeBtn.classList.add('bg-white', 'dark:bg-zinc-700', 'text-slate-900', 'dark:text-white', 'shadow-sm');
                    activeBtn.classList.remove('text-slate-500', 'dark:text-zinc-400');
                }

                const loader = document.getElementById('chart-loading');
                if (loader) loader.classList.remove('hidden');

                fetch(`{{ route('admin.dashboard.chart-data') }}?period=${period}`)
                    .then(res => res.json())
                    .then(data => {
                        if (regChart) {
                            regChart.updateOptions({
                                xaxis: {
                                    categories: data.labels
                                }
                            });
                            regChart.updateSeries([
                                {
                                    name: '{{ __("admin.users") }}',
                                    data: data.users
                                },
                                {
                                    name: '{{ __("admin.businesses") }}',
                                    data: data.businesses
                                },
                                {
                                    name: '{{ __("admin.platform_views") }}',
                                    data: data.views
                                }
                            ]);
                        }
                        if (loader) loader.classList.add('hidden');
                    })
                    .catch(err => {
                        console.error('Failed to load chart data:', err);
                        if (loader) loader.classList.add('hidden');
                    });
            };

            // Load initial chart period (week)
            setAdminChartPeriod('week');

            // Status Pie Chart
            const statusData = {
                approved: {{ $businessStatuses['approved'] ?? 0 }},
                pending: {{ $businessStatuses['pending'] ?? 0 }},
                rejected: {{ $businessStatuses['rejected'] ?? 0 }}
            };
            const statusOptions = {
                series: [statusData.approved, statusData.pending, statusData.rejected],
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'inherit',
                    background: 'transparent',
                    parentHeightOffset: 0,
                },
                labels: ['{{ __("admin.approved") }}', '{{ __("admin.pending") }}', '{{ __("admin.rejected") }}'],
                colors: ['#10b981', '#f59e0b', '#ef4444'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: { show: true },
                                value: {
                                    show: true,
                                    formatter: function (val) {
                                        return val;
                                    }
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: '{{ __("admin.total") }}',
                                    color: textColor
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { width: 0 },
                theme: { mode: isDarkMode ? 'dark' : 'light' },
                legend: { show: false },
                tooltip: { theme: isDarkMode ? 'dark' : 'light' }
            };
            const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
            statusChart.render();
            // Watch for theme changes specifically if you have a toggleTheme function
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        const isDark = document.documentElement.classList.contains('dark');
                        const newTheme = isDark ? 'dark' : 'light';
                        if (regChart) regChart.updateOptions({ theme: { mode: newTheme } });
                        statusChart.updateOptions({ theme: { mode: newTheme } });
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });

            // --- Sparkline Mini-Charts for Stat Cards ---
            document.querySelectorAll('.stat-sparkline').forEach(el => {
                const color = el.getAttribute('data-color') || '#6366f1';
                const rawValues = el.getAttribute('data-values') || '0,0,0,0,0,0,0';
                const data = rawValues.split(',').map(Number);
                const sparkOpts = {
                    series: [{ data: data }],
                    chart: {
                        type: 'area',
                        height: '100%',
                        width: '100%',
                        sparkline: { enabled: true },
                        animations: { enabled: true, easing: 'easeinout', speed: 1200 },
                        background: 'transparent',
                    },
                    stroke: { curve: 'smooth', width: 1.5 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.02,
                            stops: [0, 100]
                        }
                    },
                    colors: [color],
                    tooltip: { enabled: false },
                };
                const sparkChart = new ApexCharts(el, sparkOpts);
                sparkChart.render();
            });

            // --- Interactive Legend Toggle for Main Chart ---
            document.querySelectorAll('.chart-legend-item').forEach(item => {
                item.style.cursor = 'pointer';
                item.style.transition = 'opacity 0.3s ease';
                item.addEventListener('click', function() {
                    const seriesIndex = parseInt(this.getAttribute('data-series'));
                    if (regChart) {
                        regChart.toggleSeries(regChart.w.config.series[seriesIndex].name);
                        // Toggle visual dim
                        if (this.style.opacity === '0.35') {
                            this.style.opacity = '1';
                        } else {
                            this.style.opacity = '0.35';
                        }
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection
