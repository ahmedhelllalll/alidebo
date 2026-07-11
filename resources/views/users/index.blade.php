@extends('users.layout')

@section('title', __('dashboard.index.title') . ' | ' . ($business->name ?? __('dashboard.index.welcome')))
@section('page_title', __('nav.dashboard') ?? 'Dashboard')

@push('styles')
<style>
/* Elite Graph Tooltip Overrides */
.apexcharts-tooltip {
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
    border-radius: 14px !important;
    font-family: inherit !important;
    transition: all 0.2s ease;
}
.dark .apexcharts-tooltip {
    background: rgba(24, 24, 27, 0.85) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4) !important;
}
.apexcharts-tooltip-title {
    background: transparent !important;
    border-bottom: 1px dashed rgba(0,0,0,0.08) !important;
    font-weight: 800 !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    padding: 12px 14px 6px !important;
    color: #64748b !important;
}
.dark .apexcharts-tooltip-title {
    border-bottom: 1px dashed rgba(255,255,255,0.08) !important;
    color: #a1a1aa !important;
}
.apexcharts-tooltip-series-group {
    padding: 8px 14px 12px !important;
}
.apexcharts-tooltip-text-y-value {
    font-weight: 900 !important;
    font-size: 15px !important;
    color: #f45018 !important;
}
.apexcharts-tooltip-text-y-label {
    color: inherit !important;
}
</style>
@endpush

@section('content')
<div class="space-y-6 lg:space-y-8">

    {{-- High-End Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] ltr:tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">
                {{ __('dashboard.index.title') }}
            </h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">
                {{ __('dashboard.index.welcome') }} <span class="text-slate-900 dark:text-zinc-200 font-bold">{{ $business ? $business->name : __('dashboard.index.your_business') }}</span>
            </p>
        </div>

        @if($business)
        <div class="flex items-center gap-3">
            <a href="{{ route('business.view', $business->slug) }}" target="_blank" class="px-4 py-2 bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] rounded-xl text-[13px] font-bold text-slate-700 dark:text-zinc-300 hover:shadow-[0_8px_20px_rgba(0,0,0,0.08)] hover:-translate-y-0.5 transition-all flex items-center gap-2 group">
                <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 dark:group-hover:text-zinc-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>{{ __('dashboard.index.view_page') }}</span>
            </a>
            <a href="{{ route('business.edit') }}" class="flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-primary-light text-white rounded-xl font-bold text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>{{ __('dashboard.index.edit_data') }}</span>
            </a>
        </div>
        @endif
    </div>

    @if(!$business)
        {{-- Empty State --}}
        <div class="gsap-stagger relative overflow-hidden bg-white dark:bg-[#18181b] border border-dashed border-slate-200 dark:border-zinc-800 rounded-[22px] p-16 text-center max-w-2xl mx-auto mt-10 hover:border-primary/30 dark:hover:border-primary/30 hover:shadow-[0_20px_50px_rgba(244,80,24,0.05)]">
            <div class="absolute -top-20 -end-20 w-48 h-48 bg-primary/[0.03] dark:bg-primary/[0.02] rounded-full blur-3xl"></div>
            
            <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-orange-400/5 border border-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>

            <h2 class="text-xl font-[900] text-slate-900 dark:text-white tracking-tight">{{ __('dashboard.index.start_journey') }}</h2>
            <p class="text-slate-500 dark:text-zinc-500 mt-3 text-sm max-w-md mx-auto leading-relaxed">
                {{ __('dashboard.index.create_profile_desc') }}
            </p>

            <div class="mt-8">
                <a href="{{ route('business.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primary-light text-white text-sm font-bold rounded-xl shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98] group relative z-10">
                    {{ __('dashboard.index.create_profile_btn') }}
                    <svg class="w-4 h-4 rtl:-scale-x-100 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    @else
        {{-- Profile Banner Overview --}}
        <div class="gsap-stagger relative bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 rounded-[22px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)] group hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)]">
            <div class="relative h-32 sm:h-40 bg-slate-100 dark:bg-zinc-800/50">
                @if($business->cover)
                    <img src="{{ $business->cover_url }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-50 dark:from-zinc-800 dark:to-zinc-900/50">
                        <svg class="w-10 h-10 text-slate-300 dark:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            </div>

            <div class="px-6 sm:px-8 pb-6 sm:pb-8 relative flex flex-col sm:flex-row gap-5 items-start sm:items-end -mt-12 sm:-mt-14 z-10">
                <div class="relative p-1 bg-white dark:bg-[#18181b] rounded-[20px] shadow-sm shrink-0">
                    @if($business->logo)
                        <img src="{{ $business->logo_url }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-contain bg-slate-50 dark:bg-[#121214] border border-slate-100 dark:border-zinc-800/80">
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-slate-50 dark:bg-zinc-800 flex items-center justify-center">
                            <span class="text-2xl font-[900] text-slate-400 dark:text-zinc-500 uppercase">{{ mb_substr($business->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="absolute bottom-1 end-1 w-4 h-4 rounded-full border-[2.5px] border-white dark:border-[#18181b] {{ $business->status == 'approved' ? 'bg-emerald-500' : ($business->status == 'pending' ? 'bg-amber-500' : 'bg-red-500') }}"></div>
                </div>

                <div class="flex-1 w-full flex flex-col sm:flex-row justify-between sm:items-end gap-4 sm:mb-2">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-[900] text-slate-900 dark:text-white tracking-tight">{{ $business->name }}</h2>
                        <div class="flex flex-wrap items-center gap-2.5 text-slate-500 dark:text-zinc-400 text-xs mt-1.5 font-medium">
                            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>{{ $business->city->name ?? 'N/A' }}</span>
                            <span class="w-1 h-1 bg-slate-300 dark:bg-zinc-700 rounded-full"></span>
                            <span>{{ $business->category->name ?? 'General' }}</span>
                        </div>
                    </div>

                    @php
                        $statusBadgeArr = [
                            'approved' => ['color' => 'bg-emerald-50 border-emerald-200/60 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'pending' => ['color' => 'bg-amber-50 border-amber-200/60 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'rejected' => ['color' => 'bg-rose-50 border-rose-200/60 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ];
                        $currS = $statusBadgeArr[$business->status] ?? $statusBadgeArr['pending'];
                    @endphp
                    <div class="flex items-center gap-3">
                        {{-- Copy Link Button --}}
                        <div class="flex items-center gap-2 p-1 bg-slate-50 dark:bg-[#121214] border border-slate-200/60 dark:border-zinc-800/80 rounded-xl">
                            <input type="text" readonly value="{{ url($business->slug) }}" id="profileUrl" class="bg-transparent border-none text-[11px] sm:text-xs font-bold flex-1 px-3 py-1.5 focus:ring-0 text-slate-600 dark:text-zinc-400 truncate outline-none w-32 sm:w-48">
                            <button onclick="copyToClipboard()" id="copyBtn" class="px-3 py-1.5 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 text-[11px] font-bold rounded-lg border border-slate-200/60 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700 transition-colors shadow-sm whitespace-nowrap active:scale-95">
                                {{ __('dashboard.index.copy_link') ?? 'Copy Link' }}
                            </button>
                        </div>
                        
                        <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl border {{ $currS['color'] }} text-[11px] font-bold tracking-wide uppercase shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currS['icon'] }}"/></svg>
                            {{ __('dashboard.index.status_' . ($business->status == 'approved' ? 'verified' : $business->status)) }}
                        </span>
                    </div>
                </div>
            </div>
            
            @if($business->status == 'rejected' && $business->rejection_reason)
                <div class="border-t border-rose-100 dark:border-rose-900/30 bg-rose-50/50 dark:bg-rose-500/5 px-6 sm:px-8 py-4 flex items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 sm:mt-0 p-1.5 bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-lg shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-[11px] font-bold tracking-widest uppercase text-rose-800 dark:text-rose-400 mb-0.5">{{ __('dashboard.index.rejection_reason') }}</h4>
                            <p class="text-[13px] font-medium text-rose-700 dark:text-rose-300">"{{ $business->rejection_reason }}"</p>
                        </div>
                    </div>
                    <a href="{{ route('business.edit') }}" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-colors">
                        {{ __('dashboard.index.edit_data_now') }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 relative z-10 mt-6 lg:mt-8">
            {{-- Total Views --}}
            <div class="stat-card group relative overflow-hidden rounded-[22px] bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgba(244,80,24,0.12)] hover:-translate-y-1.5 cursor-default">
                <div class="absolute -top-20 -end-20 w-48 h-48 bg-primary/[0.03] dark:bg-primary/[0.02] rounded-full blur-3xl group-hover:scale-[1.4] transition-transform duration-700"></div>
                <div class="relative z-10 p-5 pb-3">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary/10 to-orange-400/5 border border-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </div>
                            <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider leading-tight">{{ __('dashboard.index.total_views') }}</p>
                        </div>
                        @if(isset($viewsChange))
                            <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-1 rounded-lg {{ $viewsChange >= 0 ? 'bg-orange-50 dark:bg-primary/10 text-primary' : 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' }}">
                                <i class="fa-solid {{ $viewsChange >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} text-[8px]"></i>
                                {{ $viewsChange > 0 ? '+' : '' }}{{ $viewsChange }}%
                            </span>
                        @endif
                    </div>
                    <h3 class="gsap-counter text-[30px] lg:text-[34px] font-[800] text-slate-900 dark:text-white tracking-tight leading-none" data-value="{{ $totalViews ?? 0 }}">0</h3>
                </div>
                <div class="stat-sparkline h-12 w-full opacity-60 group-hover:opacity-100 transition-opacity duration-500" data-color="#f45018" data-values="5,3,6,4,8,7,10"></div>
            </div>

            {{-- Views This Week --}}
            <div class="stat-card group relative overflow-hidden rounded-[22px] bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgba(16,185,129,0.12)] hover:-translate-y-1.5 cursor-default">
                <div class="absolute -top-20 -end-20 w-48 h-48 bg-emerald-500/[0.03] dark:bg-emerald-500/[0.02] rounded-full blur-3xl group-hover:scale-[1.4] transition-transform duration-700"></div>
                <div class="relative z-10 p-5 pb-3">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/10 to-teal-500/5 border border-emerald-500/10 flex items-center justify-center text-emerald-500 dark:text-emerald-400 group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                            </div>
                            <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider leading-tight">{{ __('dashboard.index.views_this_week') ?? 'Views This Week' }}</p>
                        </div>
                        @if(isset($weekViewsChange))
                            <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-1 rounded-lg {{ $weekViewsChange >= 0 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' }}">
                                <i class="fa-solid {{ $weekViewsChange >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} text-[8px]"></i>
                                {{ $weekViewsChange > 0 ? '+' : '' }}{{ $weekViewsChange }}%
                            </span>
                        @endif
                    </div>
                    <h3 class="gsap-counter text-[30px] lg:text-[34px] font-[800] text-slate-900 dark:text-white tracking-tight leading-none" data-value="{{ $currentWeekViews ?? 0 }}">0</h3>
                </div>
                <div class="stat-sparkline h-12 w-full opacity-60 group-hover:opacity-100 transition-opacity duration-500" data-color="#10b981" data-values="3,5,4,7,6,8,9"></div>
            </div>

            {{-- Leads --}}
            <div class="stat-card group relative overflow-hidden rounded-[22px] bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgba(99,102,241,0.12)] hover:-translate-y-1.5 cursor-default">
                <a href="{{ route('dashboard.leads.index') }}" class="absolute inset-0 z-20"></a>
                <div class="absolute -top-20 -end-20 w-48 h-48 bg-indigo-500/[0.03] dark:bg-indigo-500/[0.02] rounded-full blur-3xl group-hover:scale-[1.4] transition-transform duration-700"></div>
                <div class="relative z-10 p-5 pb-3">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500/10 to-purple-500/5 border border-indigo-500/10 flex items-center justify-center text-indigo-500 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider leading-tight">{{ __('dashboard.index.leads') }}</p>
                        </div>
                    </div>
                    <h3 class="gsap-counter text-[30px] lg:text-[34px] font-[800] text-slate-900 dark:text-white tracking-tight leading-none" data-value="{{ $totalLeads ?? 0 }}">0</h3>
                </div>
                <div class="stat-sparkline h-12 w-full opacity-60 group-hover:opacity-100 transition-opacity duration-500" data-color="#6366f1" data-values="2,4,3,5,7,6,8"></div>
            </div>

            {{-- Reviews --}}
            <div class="stat-card group relative overflow-hidden rounded-[22px] bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgba(245,158,11,0.12)] hover:-translate-y-1.5 cursor-default">
                <a href="{{ route('dashboard.reviews.index') }}" class="absolute inset-0 z-20"></a>
                <div class="absolute -top-20 -end-20 w-48 h-48 bg-amber-500/[0.03] dark:bg-amber-500/[0.02] rounded-full blur-3xl group-hover:scale-[1.4] transition-transform duration-700"></div>
                <div class="relative z-10 p-5 pb-3">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500/10 to-orange-500/5 border border-amber-500/10 flex items-center justify-center text-amber-500 dark:text-amber-400 group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider leading-tight">{{ __('dashboard.index.reviews') }}</p>
                        </div>
                    </div>
                    <h3 class="gsap-counter text-[30px] lg:text-[34px] font-[800] text-slate-900 dark:text-white tracking-tight leading-none" data-value="{{ $totalReviews ?? 0 }}">0</h3>
                </div>
                <div class="stat-sparkline h-12 w-full opacity-60 group-hover:opacity-100 transition-opacity duration-500" data-color="#f59e0b" data-values="4,6,3,5,2,4,3"></div>
            </div>
        </div>

        {{-- Analytics & Quick Actions Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 relative mt-6 lg:mt-8 w-full">
            
            {{-- Analytics Chart --}}
            <div class="lg:col-span-2 relative overflow-hidden rounded-[22px] flex flex-col">
                @if($business->status != 'approved')
                <div class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-white/60 dark:bg-[#18181b]/60 backdrop-blur-[4px] rounded-[22px]">
                    <div class="bg-white dark:bg-[#121214] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-[20px] p-6 sm:p-8 text-center max-w-sm mx-4">
                        <div class="w-14 h-14 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-amber-200 dark:border-amber-500/20">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-[900] text-slate-900 dark:text-white mb-2 tracking-tight">{{ __('dashboard.index.data_locked') ?? 'Data Locked' }}</h3>
                        <p class="text-[13px] text-slate-500 dark:text-zinc-400 font-medium leading-relaxed">{{ __('dashboard.index.data_available_after_approval') ?? 'Your analytics data will be available here once your business profile is approved.' }}</p>
                    </div>
                </div>
                @endif

                <div class="chart-card bg-white dark:bg-[#18181b] backdrop-blur-md p-4 sm:p-6 rounded-[22px] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col flex-1 min-h-[350px] overflow-hidden relative">
                    <div id="chart-loading" class="absolute inset-0 flex items-center justify-center bg-white/60 dark:bg-zinc-900/60 backdrop-blur-[2px] z-20 hidden rounded-[22px]">
                        <div class="w-8 h-8 border-4 border-slate-200 border-t-primary dark:border-zinc-700 dark:border-t-primary rounded-full animate-spin"></div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2 shrink-0">
                        <h2 class="text-[15px] sm:text-[16px] font-[900] text-slate-900 dark:text-white tracking-tight">
                            {{ __('dashboard.index.visits_analytics') }}
                        </h2>
                        <div class="flex p-0.5 bg-slate-100 dark:bg-zinc-800 rounded-xl border border-slate-200/50 dark:border-zinc-700/50 shadow-inner">
                            <button onclick="setChartPeriod('week')" class="period-btn px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all" data-period="week">{{ __('dashboard.index.week') }}</button>
                            <button onclick="setChartPeriod('month')" class="period-btn px-4 py-1.5 rounded-lg text-[11px] font-bold transition-all bg-white dark:bg-zinc-700 text-slate-900 dark:text-white shadow-sm" data-period="month">{{ __('dashboard.index.month') }}</button>
                        </div>
                    </div>
                    
                    <div class="w-full h-full flex-1 min-h-[280px]">
                        <div id="viewsChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Panel --}}
            <div class="lg:col-span-1 flex flex-col h-full">
                <div class="chart-card bg-white dark:bg-[#18181b] backdrop-blur-md p-6 rounded-[22px] border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col flex-1">
                    <h2 class="text-[15px] sm:text-[16px] font-[900] text-slate-900 dark:text-white tracking-tight mb-5">
                        {{ __('dashboard.index.quick_actions') ?? 'Quick Actions' }}
                    </h2>
                    
                    <div class="space-y-2 flex-1">
                        <a href="{{ route('business.edit') }}" class="flex items-center gap-4 p-3.5 rounded-xl bg-slate-50/50 dark:bg-zinc-900/30 hover:bg-slate-100 dark:hover:bg-zinc-800/80 transition-colors group border border-slate-100 dark:border-zinc-800/60 hover:border-slate-200 dark:hover:border-zinc-700">
                            <div class="w-11 h-11 rounded-[14px] bg-gradient-to-br from-primary/10 to-orange-400/5 text-primary flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-[13px] font-bold text-slate-900 dark:text-zinc-100">{{ __('dashboard.index.edit_business') ?? 'Edit Business Info' }}</h4>
                                <p class="text-[11px] font-medium text-slate-500 dark:text-zinc-500 mt-0.5">{{ __('dashboard.index.update_business_details') ?? 'Update your business details' }}</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('dashboard.reviews.index') }}" class="flex items-center gap-4 p-3.5 rounded-xl bg-slate-50/50 dark:bg-zinc-900/30 hover:bg-slate-100 dark:hover:bg-zinc-800/80 transition-colors group border border-slate-100 dark:border-zinc-800/60 hover:border-slate-200 dark:hover:border-zinc-700">
                            <div class="w-11 h-11 rounded-[14px] bg-gradient-to-br from-amber-500/10 to-orange-500/5 text-amber-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-[13px] font-bold text-slate-900 dark:text-zinc-100">{{ __('dashboard.index.manage_reviews') ?? 'Manage Reviews' }}</h4>
                                <p class="text-[11px] font-medium text-slate-500 dark:text-zinc-500 mt-0.5">{{ __('dashboard.index.respond_customer_feedback') ?? 'Respond to customer feedback' }}</p>
                            </div>
                        </a>

                        <button onclick="toggleSupportChat()" class="w-full flex items-center gap-4 p-3.5 rounded-xl bg-slate-50/50 dark:bg-zinc-900/30 hover:bg-slate-100 dark:hover:bg-zinc-800/80 transition-colors group border border-slate-100 dark:border-zinc-800/60 hover:border-slate-200 dark:hover:border-zinc-700 text-start">
                            <div class="w-11 h-11 rounded-[14px] bg-gradient-to-br from-emerald-500/10 to-teal-500/5 text-emerald-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-[13px] font-bold text-slate-900 dark:text-zinc-100">{{ __('dashboard.index.contact_support') ?? 'Contact Support' }}</h4>
                                <p class="text-[11px] font-medium text-slate-500 dark:text-zinc-500 mt-0.5">{{ __('dashboard.index.get_help_elite_team') ?? 'Get help from our elite team' }}</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
{{-- Include FontAwesome for icons --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Safe check for GSAP
        if (typeof gsap !== 'undefined') {
            // Use a context to prevent conflicts and ensure cleanup
            const userDashCtx = gsap.context(() => {
                // Header entrance
                gsap.from('.dashboard-header-reveal', {
                    y: -20,
                    opacity: 0,
                    duration: 0.8,
                    ease: "power3.out",
                    delay: 0.1,
                    clearProps: 'all'
                });
                
                // Stagger Stats Cards
                gsap.from('.stat-card', {
                    y: 30,
                    opacity: 0,
                    duration: 0.8,
                    stagger: 0.1,
                    ease: "back.out(1.2)",
                    delay: 0.3,
                    clearProps: 'all',
                    onComplete: () => {
                        document.querySelectorAll('.stat-card').forEach(el => el.classList.add('transition-all', 'duration-500'));
                    }
                });
                
                // Chart Stagger
                gsap.from('.chart-card', {
                    y: 40,
                    opacity: 0,
                    duration: 0.8,
                    ease: "power3.out",
                    delay: 0.5,
                    clearProps: 'all'
                });

                // Set transitions on gsap-stagger after layout GSAP finishes (layout GSAP runs independently)
                setTimeout(() => {
                    document.querySelectorAll('.gsap-stagger').forEach(el => el.classList.add('transition-all', 'duration-500'));
                }, 1000);
            });

            // GSAP Number Counters
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
        }

        // Setup sparklines
        if (typeof ApexCharts !== 'undefined') {
            document.querySelectorAll('.stat-sparkline').forEach(el => {
                const color = el.getAttribute('data-color') || '#f45018';
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
        }
    });

    // Copy to clipboard
    window.copyToClipboard = function() {
        const copyText = document.getElementById("profileUrl");
        if(copyText) {
            copyText.select();
            navigator.clipboard.writeText(copyText.value);
            const btn = document.getElementById("copyBtn");
            if(btn) {
                const originalText = btn.innerHTML;
                btn.innerHTML = 'Copied!';
                btn.classList.add('bg-emerald-500', 'text-white', 'border-emerald-500');
                btn.classList.remove('bg-white', 'dark:bg-zinc-800', 'text-slate-700', 'dark:text-zinc-300', 'border-slate-200/60', 'dark:border-zinc-700');

                setTimeout(() => { 
                    btn.innerHTML = originalText; 
                    btn.classList.remove('bg-emerald-500', 'text-white', 'border-emerald-500');
                    btn.classList.add('bg-white', 'dark:bg-zinc-800', 'text-slate-700', 'dark:text-zinc-300', 'border-slate-200/60', 'dark:border-zinc-700');
                }, 2000);
            }
        }
    };

    // Chart rendering
    let viewsChart = null;

    window.setChartPeriod = function(period) {
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'dark:bg-zinc-700', 'text-slate-900', 'dark:text-white', 'shadow-sm');
            btn.classList.add('text-slate-500', 'dark:text-zinc-400');
        });
        const activeBtn = document.querySelector(`.period-btn[data-period="${period}"]`);
        if (activeBtn) {
            activeBtn.classList.add('bg-white', 'dark:bg-zinc-700', 'text-slate-900', 'dark:text-white', 'shadow-sm');
            activeBtn.classList.remove('text-slate-500', 'dark:text-zinc-400');
        }

        const loader = document.getElementById('chart-loading');
        if (loader) loader.classList.remove('hidden');

        fetch(`/dashboard/views-chart?period=${period}`)
            .then(res => res.json())
            .then(data => {
                const isDark = document.documentElement.classList.contains('dark');
                const primaryColor = '#f45018';
                const textColor = isDark ? '#71717a' : '#94a3b8';
                const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';

                if (viewsChart) {
                    viewsChart.updateOptions({ 
                        xaxis: { 
                            categories: data.labels,
                            tickAmount: data.labels.length
                        } 
                    });
                    viewsChart.updateSeries([{ name: '{{ __("dashboard.index.views") ?? "Views" }}', data: data.values }]);
                } else {
                    const options = {
                        series: [{
                            name: '{{ __("dashboard.index.views") ?? "Views" }}',
                            data: data.values
                        }],
                        chart: {
                            type: 'area',
                            height: 300,
                            toolbar: { show: false },
                            zoom: { enabled: false },
                            fontFamily: 'inherit',
                            background: 'transparent',
                            parentHeightOffset: 0,
                            dropShadow: {
                                enabled: true,
                                color: primaryColor,
                                top: 12,
                                left: 0,
                                blur: 10,
                                opacity: 0.2
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800,
                                animateGradually: { enabled: true, delay: 150 },
                                dynamicAnimation: { enabled: true, speed: 350 }
                            }
                        },
                        colors: [primaryColor],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.35,
                                opacityTo: 0.0,
                                stops: [0, 90, 100]
                            }
                        },
                        dataLabels: { enabled: false },
                        stroke: { 
                            curve: 'smooth', 
                            width: 3.5,
                            lineCap: 'round'
                        },
                        markers: {
                            size: 0,
                            colors: ['#fff'],
                            strokeColors: primaryColor,
                            strokeWidth: 3,
                            hover: { size: 6 }
                        },
                        xaxis: {
                            type: 'category',
                            categories: data.labels,
                            tickAmount: data.labels.length,
                            tickPlacement: 'between',
                            crosshairs: {
                                show: true,
                                stroke: { color: gridColor, width: 1, dashArray: 4 }
                            },
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: { 
                                rotate: 0,
                                trim: false,
                                hideOverlappingLabels: false,
                                style: { colors: textColor, fontSize: '12px', fontWeight: 600, fontFamily: 'inherit' },
                                offsetY: 5
                            },
                            tooltip: { enabled: false }
                        },
                        yaxis: {
                            labels: { 
                                style: { colors: textColor, fontSize: '13px', fontWeight: 700, fontFamily: 'inherit' },
                                offsetX: -10,
                                formatter: function(val) {
                                    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                                    if (val >= 1000) return (val / 1000).toFixed(1) + 'k';
                                    return val;
                                }
                            }
                        },
                        grid: {
                            borderColor: gridColor,
                            strokeDashArray: 4,
                            padding: { top: 15, right: 30, bottom: 0, left: 30 },
                            xaxis: { lines: { show: false } },
                            yaxis: { lines: { show: true } }
                        },
                        theme: { mode: isDark ? 'dark' : 'light' },
                        legend: { show: false },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(val) {
                                    return val !== undefined ? val.toLocaleString('{{ app()->getLocale() }}') + ' {{ __("dashboard.index.views") ?? "Views" }}' : '0';
                                }
                            },
                            marker: { show: false }
                        }
                    };
                    viewsChart = new ApexCharts(document.querySelector("#viewsChart"), options);
                    viewsChart.render();


                    // Watch for theme changes
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                const newTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
                                viewsChart.updateOptions({ theme: { mode: newTheme } });
                            }
                        });
                    });
                    observer.observe(document.documentElement, { attributes: true });
                }

                if (loader) loader.classList.add('hidden');
            })
            .catch(() => {
                if (loader) loader.classList.add('hidden');
            });
    };

    @if($business && $business->status == 'approved')
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => setChartPeriod('month'), 300);
        });
    @endif

    // --- Support Chat Logic (Polling) ---
    const botAvatarUrl = '{{ asset("images/logo.webp") }}';
    let chatPolling = null;
    let chatOpen = false;

    window.toggleSupportChat = function() {
        const modal = document.getElementById('supportChatModal');
        chatOpen = !chatOpen;
        if(chatOpen) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'translate-y-4');
            }, 10);
            fetchChatMessages();
            chatPolling = setInterval(fetchChatMessages, 3000);
        } else {
            modal.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
            clearInterval(chatPolling);
        }
    }

    function fetchChatMessages() {
        fetch('{{ route("support.chat.fetch") }}')
            .then(res => res.json())
            .then(data => {
                const chatBody = document.getElementById('chatMessagesBody');
                if(!chatBody) return;
                chatBody.innerHTML = '';
                
                chatBody.innerHTML = `
                    <div class="flex justify-center my-6">
                        <span class="text-[10px] font-bold text-slate-400 bg-white dark:bg-zinc-800/50 border border-slate-100 dark:border-white/5 px-4 py-1.5 rounded-full shadow-sm uppercase tracking-widest">{{ __('dashboard.index.chat_started') }}</span>
                    </div>
                `;

                data.messages.forEach(msg => {
                    const isUser = msg.sender_type === 'user';
                    const avatar = isUser ? '' : `<div class="relative shrink-0"><div class="w-7 h-7 rounded-full bg-gradient-to-tr from-primary to-orange-400 p-[1px]"><div class="w-full h-full bg-white dark:bg-zinc-900 rounded-full flex items-center justify-center overflow-hidden"><img src="${botAvatarUrl}" class="w-4 h-4 object-contain" alt="Bot"></div></div></div>`;
                    
                    const html = `
                        <div class="flex ${isUser ? 'justify-end' : 'justify-start'} mb-5 gap-2.5">
                            ${!isUser ? avatar : ''}
                            <div class="flex flex-col ${isUser ? 'items-end' : 'items-start'} max-w-[80%]">
                                <div class="px-4 py-2.5 text-[13px] leading-relaxed shadow-sm ${isUser ? 'bg-primary text-white rounded-[20px] rounded-br-[4px]' : 'bg-white dark:bg-[#121214] text-slate-900 dark:text-zinc-100 rounded-[20px] rounded-bl-[4px] border border-slate-100 dark:border-white/5'}">
                                    ${msg.message}
                                </div>
                                <span class="text-[9px] text-slate-400 mt-1.5 px-1 font-bold tracking-wide uppercase">${msg.time_ago}</span>
                            </div>
                        </div>
                    `;
                    chatBody.insertAdjacentHTML('beforeend', html);
                });
                chatBody.scrollTop = chatBody.scrollHeight;
            });
    }

    window.sendChatMessage = function(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        if(!input) return;
        const msg = input.value.trim();
        if(!msg) return;

        input.value = '';
        input.disabled = true;

        fetch('{{ route("support.chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: msg })
        })
        .then(res => res.json())
        .then(data => {
            input.disabled = false;
            input.focus();
            fetchChatMessages();
        })
        .catch(err => {
            input.disabled = false;
        });
    }
</script>
@endpush

{{-- Support Chat Modal --}}
<div id="supportChatModal" class="hidden opacity-0 translate-y-4 fixed bottom-6 end-6 lg:bottom-8 lg:end-8 z-[100] w-[calc(100vw-3rem)] sm:w-[400px] bg-slate-50 dark:bg-[#09090b] border border-slate-200/60 dark:border-white/5 rounded-[24px] shadow-[0_30px_70px_rgba(0,0,0,0.18)] dark:shadow-[0_30px_70px_rgba(0,0,0,0.6)] flex flex-col overflow-hidden transition-all duration-300 transform">
    {{-- Header --}}
    <div class="bg-white/90 dark:bg-[#121214]/90 backdrop-blur-md border-b border-slate-100 dark:border-white/5 px-5 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-primary to-orange-400 p-[1.5px] shadow-sm">
                    <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-full flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/logo.webp') }}" class="w-6 h-6 object-contain" alt="AliDebo">
                    </div>
                </div>
                <div class="absolute bottom-0 end-0 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white dark:border-[#121214]"></div>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">{{ __('dashboard.index.priority_support') }}</h3>
                <p class="text-[10px] font-bold text-primary tracking-widest uppercase">{{ __('dashboard.index.elite_team') }}</p>
            </div>
        </div>
        <button onclick="toggleSupportChat()" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-zinc-800 dark:hover:text-zinc-300 transition-colors active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    
    {{-- Body --}}
    <div id="chatMessagesBody" class="flex-1 h-[400px] overflow-y-auto p-5 custom-scrollbar bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjAwLDIwMCwyMDAsMC4xNSkiLz48L3N2Zz4=')] dark:bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wMykiLz48L3N2Zz4=')]">
        <!-- Messages loaded here -->
    </div>

    {{-- Footer --}}
    <div class="p-4 bg-white/90 dark:bg-[#121214]/90 backdrop-blur-md border-t border-slate-100 dark:border-white/5">
        <form onsubmit="sendChatMessage(event)" class="relative flex items-end gap-2 group">
            <textarea id="chatInput" rows="1" placeholder="{{ __('dashboard.index.type_message') }}" onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendChatMessage(event); }" class="w-full bg-slate-100/50 dark:bg-zinc-900/50 border border-slate-200/60 dark:border-zinc-800 rounded-2xl px-4 py-3 text-[13px] text-slate-900 dark:text-white placeholder:text-slate-400 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary/30 focus:ring-4 focus:ring-primary/5 transition-all resize-none outline-none overflow-hidden pe-12" oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 100) + 'px'"></textarea>
            
            <button type="submit" class="absolute bottom-1.5 end-1.5 w-9 h-9 flex items-center justify-center bg-primary text-white rounded-[14px] hover:bg-primary-dark transition-all transform hover:scale-105 active:scale-95 shadow-sm shadow-primary/20">
                <svg class="w-4 h-4 rtl:-scale-x-100 translate-x-[1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </form>
        <div class="text-center mt-2.5">
            <span class="text-[9px] text-slate-400/70 font-bold uppercase tracking-wider">{{ __('dashboard.index.powered_by_ai') ?? 'Powered by AliDebo AI Routing' }}</span>
        </div>
    </div>
</div>
@endsection