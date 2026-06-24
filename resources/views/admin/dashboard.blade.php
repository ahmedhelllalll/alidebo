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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 relative z-10">
        {{-- Active Businesses --}}
        <div class="stat-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md p-6 rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(16,185,129,0.08)] relative group overflow-hidden">
            <div class="absolute -top-12 -end-12 w-40 h-40 bg-gradient-to-br from-emerald-500/10 to-transparent rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="absolute top-0 end-0 p-4 opacity-[0.03] dark:opacity-[0.02] group-hover:scale-110 transition-transform duration-500">
                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div class="relative z-10 flex flex-col h-full gap-5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 dark:text-emerald-400 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="gsap-counter text-3xl lg:text-4xl font-[900] text-slate-900 dark:text-white tracking-tight" data-value="{{ $stats['active_businesses'] ?? 0 }}">0</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-[13px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-widest">{{ __('admin.active_businesses') ?? 'Active Businesses' }}</p>
                        @if(isset($stats['business_growth']))
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-black px-1.5 py-0.5 rounded-md {{ $stats['business_growth'] >= 0 ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}">
                                <i class="fa-solid {{ $stats['business_growth'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                                {{ $stats['business_growth'] > 0 ? '+' : '' }}{{ $stats['business_growth'] }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Users --}}
        <div class="stat-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md p-6 rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(99,102,241,0.08)] relative group overflow-hidden">
            <div class="absolute -top-12 -end-12 w-40 h-40 bg-gradient-to-br from-indigo-500/10 to-transparent rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="absolute top-0 end-0 p-4 opacity-[0.03] dark:opacity-[0.02] group-hover:scale-110 transition-transform duration-500">
                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="relative z-10 flex flex-col h-full gap-5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500/20 to-purple-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-500 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <h3 class="gsap-counter text-3xl lg:text-4xl font-[900] text-slate-900 dark:text-white tracking-tight" data-value="{{ $stats['users'] ?? 0 }}">0</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-[13px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-widest">{{ __('admin.users') }}</p>
                        @if(isset($stats['user_growth']))
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-black px-1.5 py-0.5 rounded-md {{ $stats['user_growth'] >= 0 ? 'bg-indigo-500/10 text-indigo-500' : 'bg-red-500/10 text-red-500' }}">
                                <i class="fa-solid {{ $stats['user_growth'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                                {{ $stats['user_growth'] > 0 ? '+' : '' }}{{ $stats['user_growth'] }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Platform Views --}}
        <div class="stat-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md p-6 rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(244,80,24,0.08)] relative group overflow-hidden">
            <div class="absolute -top-12 -end-12 w-40 h-40 bg-gradient-to-br from-primary/10 to-transparent rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="absolute top-0 end-0 p-4 opacity-[0.03] dark:opacity-[0.02] group-hover:scale-110 transition-transform duration-500">
                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <div class="relative z-10 flex flex-col h-full gap-5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-primary/20 to-orange-400/10 border border-primary/20 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div>
                    <h3 class="gsap-counter text-3xl lg:text-4xl font-[900] text-slate-900 dark:text-white tracking-tight" data-value="{{ $stats['total_views'] ?? 0 }}">0</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-[13px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-widest">{{ __('admin.platform_views') ?? 'Platform Views' }}</p>
                        @if(isset($stats['views_growth']))
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-black px-1.5 py-0.5 rounded-md {{ $stats['views_growth'] >= 0 ? 'bg-primary/10 text-primary' : 'bg-red-500/10 text-red-500' }}">
                                <i class="fa-solid {{ $stats['views_growth'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                                {{ $stats['views_growth'] > 0 ? '+' : '' }}{{ $stats['views_growth'] }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Pending Approvals --}}
        <div class="stat-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md p-6 rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(245,158,11,0.08)] relative group overflow-hidden">
            <div class="absolute -top-12 -end-12 w-40 h-40 bg-gradient-to-br from-amber-500/10 to-transparent rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="absolute top-0 end-0 p-4 opacity-[0.03] dark:opacity-[0.02] group-hover:scale-110 transition-transform duration-500">
                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="relative z-10 flex flex-col h-full gap-5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500/20 to-orange-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 dark:text-amber-400 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="gsap-counter text-3xl lg:text-4xl font-[900] text-slate-900 dark:text-white tracking-tight" data-value="{{ $stats['pending'] ?? 0 }}">0</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-[13px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-widest">{{ __('admin.approvals') }}</p>
                        @if(isset($stats['claimed_businesses']))
                            <span class="inline-flex items-center gap-1 text-[10px] font-black px-1.5 py-0.5 rounded-md bg-amber-500/10 text-amber-500" title="Claimed Businesses">
                                <i class="fa-solid fa-check-circle"></i>
                                {{ $stats['claimed_businesses'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Analytics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 mt-6 relative z-10 w-full overflow-hidden">
        {{-- Registration Chart --}}
        <div class="chart-card md:col-span-2 bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md p-4 sm:p-6 rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] flex flex-col min-h-[350px] overflow-hidden">
            <div class="flex items-center justify-between mb-2 shrink-0">
                <h2 class="text-[15px] sm:text-[16px] font-[900] text-slate-900 dark:text-white tracking-tight">{{ __('admin.registrations_last_7_days') }}</h2>
            </div>
            <div id="registrationChart" class="w-full h-full flex-1 min-h-[250px]"></div>
        </div>
        {{-- Business Status Pie Chart --}}
        <div class="chart-card md:col-span-2 lg:col-span-1 bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md p-4 sm:p-6 rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] flex flex-col min-h-[350px] overflow-hidden">
            <div class="flex items-center justify-between mb-2 shrink-0">
                <h2 class="text-[15px] sm:text-[16px] font-[900] text-slate-900 dark:text-white tracking-tight">{{ __('admin.business_statuses') }}</h2>
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
            // Registration Chart
            const regOptions = {
                series: [{
                    name: '{{ __("admin.users") }}',
                    data: {!! json_encode($registrations['users'] ?? []) !!}
                }, {
                    name: '{{ __("admin.businesses") }}',
                    data: {!! json_encode($registrations['businesses'] ?? []) !!}
                }],
                chart: {
                    type: 'area',
                    height: '100%',
                    minHeight: 250,
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
                colors: ['#10b981', '#6366f1'],
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
                    categories: {!! json_encode($registrations['categories'] ?? []) !!},
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
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: { theme: isDarkMode ? 'dark' : 'light' }
            };
            const regChart = new ApexCharts(document.querySelector("#registrationChart"), regOptions);
            regChart.render();
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
                    height: '100%',
                    minHeight: 250,
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
                        regChart.updateOptions({ theme: { mode: newTheme } });
                        statusChart.updateOptions({ theme: { mode: newTheme } });
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
        }
    });
</script>
@endpush
@endsection
