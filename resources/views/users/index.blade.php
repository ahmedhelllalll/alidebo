@extends('users.layout')

@section('title', __('dashboard.index.title') . ' | ' . ($business->name ?? __('dashboard.index.welcome')))
@section('page_title', __('nav.dashboard') ?? 'Dashboard')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- High-End Page Header --}}
    <div class="gsap-stagger flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-black/5 dark:border-white/[0.04]">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">
                {{ __('dashboard.index.title') }}
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm font-medium">
                {{ __('dashboard.index.welcome') }} <span class="text-zinc-900 dark:text-zinc-200 font-semibold">{{ $business ? $business->name : __('dashboard.index.your_business') }}</span>
            </p>
        </div>

        @if($business)
        <div class="flex items-center gap-3">
            <a href="{{ route('business.view', $business->slug) }}" target="_blank" class="px-4 py-2 bg-white dark:bg-[#09090b] border border-black/5 dark:border-white/[0.06] shadow-[0_2px_8px_rgba(0,0,0,0.04)] rounded-lg text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all flex items-center gap-2 group">
                <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>{{ __('dashboard.index.view_page') }}</span>
            </a>
            <a href="{{ route('business.edit') }}" class="px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-lg text-xs font-bold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all shadow-sm flex items-center gap-2 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>{{ __('dashboard.index.edit_data') }}</span>
            </a>
        </div>
        @endif
    </div>

    @if(!$business)
        {{-- Empty State Clean Redesign (Vercel Style) --}}
        <div class="gsap-stagger bg-white dark:bg-[#0e0e11] border border-dashed border-black/10 dark:border-white/10 rounded-2xl p-16 text-center max-w-2xl mx-auto mt-10 transition-colors hover:border-black/20 dark:hover:border-white/20 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)]">
            <div class="w-16 h-16 bg-zinc-50 dark:bg-zinc-900 border border-black/5 dark:border-white/5 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-8 h-8 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>

            <h2 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ __('dashboard.index.start_journey') }}</h2>
            <p class="text-zinc-500 dark:text-zinc-400 mt-3 text-sm max-w-md mx-auto leading-relaxed">
                {{ __('dashboard.index.create_profile_desc') }}
            </p>

            <div class="mt-8">
                <a href="{{ route('business.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-sm font-bold rounded-lg hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-transform active:scale-95 shadow-sm group">
                    {{ __('dashboard.index.create_profile_btn') }}
                    <svg class="w-4 h-4 rtl:-scale-x-100 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    @else
        {{-- Modern Dashboard Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-4">

            {{-- Left Content: Profile & Stats --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Profile Showcase Card (Stripe Design Feel) --}}
                <div class="gsap-stagger bg-white dark:bg-zinc-900 border border-black/5 dark:border-white/[0.04] rounded-2xl overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] transition-transform hover:-translate-y-0.5 duration-300">
                    <div class="relative h-40 bg-zinc-100 dark:bg-zinc-800/50">
                        @if($business->cover)
                            <img src="{{ $business->cover_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-zinc-100 to-zinc-50 dark:from-zinc-900 dark:to-zinc-800/50">
                                <svg class="w-10 h-10 text-zinc-300 dark:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </div>

                    <div class="px-6 md:px-8 pb-8 relative flex flex-col md:flex-row gap-5 items-start md:items-end -mt-12">
                        <div class="relative p-1 bg-white dark:bg-zinc-900 rounded-[1.25rem] shadow-sm shrink-0">
                            @if($business->logo)
                                <img src="{{ $business->logo_url }}" class="w-20 h-20 md:w-24 md:h-24 rounded-xl object-contain bg-zinc-50 dark:bg-[#09090b] border border-black/5 dark:border-white/[0.04]">
                            @else
                                <div class="w-20 h-20 md:w-24 md:h-24 rounded-xl bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-zinc-400 dark:text-zinc-500 uppercase">{{ mb_substr($business->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="absolute bottom-0 end-0 w-4 h-4 rounded-full border-[2.5px] border-white dark:border-zinc-900 {{ $business->status == 'approved' ? 'bg-primary' : 'bg-zinc-400' }}"></div>
                        </div>

                        <div class="flex-1 w-full flex flex-col md:flex-row justify-between md:items-end gap-4 md:mb-1">
                            <div>
                                <h2 class="text-xl font-bold dark:text-white text-zinc-900 tracking-tight">{{ $business->name }}</h2>
                                <div class="flex flex-wrap items-center gap-2.5 text-zinc-500 dark:text-zinc-400 text-xs mt-2 font-medium">
                                    <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>{{ $business->city->name ?? 'N/A' }}</span>
                                    <span class="w-1 h-1 bg-zinc-300 dark:bg-zinc-700 rounded-full"></span>
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
                            <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg border {{ $currS['color'] }} text-[11px] font-bold tracking-wide uppercase shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currS['icon'] }}"/></svg>
                                {{ __('dashboard.index.status_' . ($business->status == 'approved' ? 'verified' : $business->status)) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Stats Grid Clean (Linear Style) --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Views Stat --}}
                    <div class="gsap-stagger bg-white dark:bg-zinc-900 border border-black/5 dark:border-white/[0.04] p-5 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] flex items-center justify-between group transition-all hover:-translate-y-0.5 duration-300">
                        <div>
                            <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mb-1.5">{{ __('dashboard.index.total_views') }}</p>
                            <h3 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight tabular-nums">{{ number_format($totalViews) }}</h3>
                            @if(isset($viewsChange) && $viewsChange != 0)
                                <div class="inline-flex items-center gap-1 mt-2 {{ $viewsChange > 0 ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100 dark:border-emerald-500/20' : 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border-rose-100 dark:border-rose-500/20' }} text-[10px] font-bold px-1.5 py-0.5 rounded border">
                                    {!! $viewsChange > 0 ? '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>' : '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 112 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>' !!}
                                    {{ abs($viewsChange) }}%
                                </div>
                            @endif
                        </div>
                        <div class="w-12 h-12 bg-zinc-50 dark:bg-zinc-800 rounded-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 border border-zinc-100 dark:border-zinc-700/50 group-hover:bg-zinc-100 dark:group-hover:bg-zinc-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                    </div>

                    {{-- Weekly Views Stat --}}
                    <div class="gsap-stagger bg-white dark:bg-zinc-900 border border-black/5 dark:border-white/[0.04] p-5 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] flex items-center justify-between group transition-all hover:-translate-y-0.5 duration-300">
                        <div>
                            <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mb-1.5">{{ __('dashboard.index.views_this_week') ?? 'Views This Week' }}</p>
                            <h3 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight tabular-nums">{{ number_format($currentWeekViews ?? 0) }}</h3>
                            @if(isset($weekViewsChange) && $weekViewsChange != 0)
                                <div class="inline-flex items-center gap-1 mt-2 {{ $weekViewsChange > 0 ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100 dark:border-emerald-500/20' : 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border-rose-100 dark:border-rose-500/20' }} text-[10px] font-bold px-1.5 py-0.5 rounded border">
                                    {!! $weekViewsChange > 0 ? '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>' : '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 112 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>' !!}
                                    {{ abs($weekViewsChange) }}%
                                </div>
                            @endif
                        </div>
                        <div class="w-12 h-12 bg-zinc-50 dark:bg-zinc-800 rounded-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 border border-zinc-100 dark:border-zinc-700/50 group-hover:bg-zinc-100 dark:group-hover:bg-zinc-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                        </div>
                    </div>

                    {{-- Active Status --}}
                    <div class="gsap-stagger bg-white dark:bg-zinc-900 border border-black/5 dark:border-white/[0.04] p-5 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] flex items-center justify-between group transition-all hover:-translate-y-0.5 duration-300">
                        <div>
                            <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mb-1.5">{{ __('dashboard.index.account_status') }}</p>
                            <h3 class="text-xl font-bold {{ $business->status == 'approved' ? 'text-emerald-600 dark:text-emerald-400' : ($business->status == 'pending' ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }} tracking-tight">
                                {{ __('dashboard.index.status_' . ($business->status == 'approved' ? 'active' : ($business->status == 'pending' ? 'pending' : 'needs_edit'))) }}
                            </h3>
                            <div class="mt-2 text-[10px] font-semibold text-zinc-500 dark:text-zinc-500 flex items-center gap-1.5 uppercase">
                                <span class="relative flex h-1.5 w-1.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $business->status == 'approved' ? 'bg-emerald-400' : 'bg-amber-400' }} opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-1.5 w-1.5 {{ $business->status == 'approved' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                </span>
                                Real-time sync
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-zinc-50 dark:bg-zinc-800 rounded-full flex items-center justify-center text-zinc-400 dark:text-zinc-500 border border-zinc-100 dark:border-zinc-700/50 group-hover:bg-zinc-100 dark:group-hover:bg-zinc-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $currS['icon'] }}"/></svg>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column: Sidebar Actions --}}
            <div class="space-y-4">

                {{-- Connect Profile Card Clean --}}
                <div class="gsap-stagger bg-white dark:bg-zinc-900 border border-black/5 dark:border-white/[0.04] p-5 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)]">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-1.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/50 rounded-lg text-zinc-700 dark:text-zinc-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">{{ __('dashboard.index.profile_link') }}</h4>
                    </div>

                    <p class="text-zinc-500 dark:text-zinc-400 text-[11px] mb-4 font-medium leading-relaxed">
                        {{ __('dashboard.index.copy_link_desc') }}
                    </p>

                    <div class="flex items-center gap-2 p-1 bg-zinc-50 dark:bg-[#0e0e11] border border-zinc-200 dark:border-zinc-800 rounded-lg focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all">
                        <input type="text" readonly value="{{ url($business->slug) }}" id="profileUrl" class="bg-transparent border-none text-xs font-semibold flex-1 px-2.5 py-1.5 focus:ring-0 text-zinc-900 dark:text-white truncate outline-none">
                        <button onclick="copyToClipboard()" id="copyBtn" class="px-3 py-1.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 text-[11px] font-bold rounded-md hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors shadow-sm whitespace-nowrap active:scale-95">
                            Copy
                        </button>
                    </div>
                </div>

                {{-- Rejection Message if exists --}}
                @if($business->status == 'rejected' && $business->rejection_reason)
                    <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 dark:bg-[#2e1015] dark:border-rose-900/50 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 end-0 p-3 opacity-10">
                            <svg class="w-12 h-12 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 text-rose-800 dark:text-rose-400 mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <h4 class="text-[10px] font-bold tracking-widest uppercase">{{ __('dashboard.index.rejection_reason') }}</h4>
                            </div>
                            <p class="text-rose-700 dark:text-rose-300 text-[11px] mb-4 font-medium leading-relaxed italic">"{{ $business->rejection_reason }}"</p>
                            <a href="{{ route('business.edit') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-800 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300 transition-colors group">
                                {{ __('dashboard.index.edit_data_now') }}
                                <svg class="w-3.5 h-3.5 rtl:-scale-x-100 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endif


                {{-- Statistics Card --}}
                <div class="gsap-stagger bg-white dark:bg-zinc-900 border border-black/5 dark:border-white/[0.04] p-5 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)]">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-1.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/50 rounded-lg text-zinc-700 dark:text-zinc-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">{{ __('dashboard.index.statistics') ?? 'Statistics' }}</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Leads Stat --}}
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mb-1">{{ __('dashboard.index.leads') }}</div>
                            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ $totalLeads }}</div>
                            <a href="{{ route('dashboard.leads.index') }}" class="text-[10px] text-primary hover:underline mt-2 inline-block font-semibold">View Leads &rarr;</a>
                        </div>
                        
                        {{-- Reviews Stat --}}
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mb-1">{{ __('dashboard.index.reviews') }}</div>
                            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ $totalReviews }}</div>
                            <a href="{{ route('dashboard.reviews.index') }}" class="text-[10px] text-primary hover:underline mt-2 inline-block font-semibold">View Reviews &rarr;</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Analytics Section --}}
        <div class="relative mt-6">
            @if($business->status != 'approved')
            <div class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-900/70 backdrop-blur-[4px] rounded-2xl">
                <div class="bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 shadow-2xl rounded-2xl p-6 text-center max-w-sm mx-4 transform transition-all duration-500">
                    <div class="w-14 h-14 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-200 dark:border-amber-500/20">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">{{ __('dashboard.index.data_locked') ?? 'Data Locked' }}</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 font-medium">{{ __('dashboard.index.data_available_after_approval') ?? 'Your analytics data will be available here once your business profile is approved.' }}</p>
                </div>
            </div>
            @endif

            <div class="gsap-stagger bg-white dark:bg-zinc-900 border border-black/5 dark:border-white/[0.04] rounded-2xl p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)]">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/50 rounded-lg text-zinc-800 dark:text-zinc-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">{{ __('dashboard.index.visits_analytics') }}</h3>
                    </div>
                    <div class="flex p-0.5 bg-zinc-100 dark:bg-zinc-800/80 rounded-lg border border-zinc-200/50 dark:border-zinc-700/50 shadow-inner">
                        <button onclick="setChartPeriod('week')" class="period-btn px-4 py-1.5 rounded-md text-[11px] font-bold transition-all" data-period="week">{{ __('dashboard.index.week') }}</button>
                        <button onclick="setChartPeriod('month')" class="period-btn px-4 py-1.5 rounded-md text-[11px] font-bold bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-[0_1px_3px_rgba(0,0,0,0.1)] transition-all" data-period="month">{{ __('dashboard.index.month') }}</button>
                    </div>
                </div>

                <div class="relative h-[300px]">
                    <div id="chart-loading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm z-20 hidden rounded-xl">
                        <div class="w-6 h-6 border-2 border-zinc-200 border-t-primary dark:border-zinc-700 dark:border-t-primary rounded-full animate-spin"></div>
                    </div>
                    <div id="viewsChart" class="w-full h-full"></div>
                </div>
            </div>

        </div>
        </div>
    @endif
</div>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let viewsChart = null;

    function copyToClipboard() {
        const copyText = document.getElementById("profileUrl");
        copyText.select();
        navigator.clipboard.writeText(copyText.value);
        const btn = document.getElementById("copyBtn");
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Copied!';
        btn.classList.add('bg-emerald-600', 'text-white');
        btn.classList.remove('bg-zinc-900', 'dark:bg-white', 'dark:text-zinc-900');

        setTimeout(() => { 
            btn.innerHTML = originalText; 
            btn.classList.remove('bg-emerald-600', 'text-white');
            btn.classList.add('bg-zinc-900', 'dark:bg-white', 'dark:text-zinc-900');
        }, 2000);
    }

    function setChartPeriod(period) {
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'dark:bg-zinc-700', 'text-zinc-900', 'dark:text-white', 'shadow-[0_1px_3px_rgba(0,0,0,0.1)]');
            btn.classList.add('text-zinc-500', 'dark:text-zinc-400');
        });
        const activeBtn = document.querySelector(`[data-period="${period}"]`);
        if (activeBtn) {
            activeBtn.classList.add('bg-white', 'dark:bg-zinc-700', 'text-zinc-900', 'dark:text-white', 'shadow-[0_1px_3px_rgba(0,0,0,0.1)]');
            activeBtn.classList.remove('text-zinc-500', 'dark:text-zinc-400');
        }

        const loader = document.getElementById('chart-loading');
        if (loader) loader.classList.remove('hidden');

        fetch(`/dashboard/views-chart?period=${period}`)
            .then(res => res.json())
            .then(data => {
                const isDark = document.documentElement.classList.contains('dark');
                const primaryColor = '#f45018';
                const textColor = isDark ? '#a1a1aa' : '#52525b';
                const gridColor = isDark ? 'rgba(255,255,255,0.03)' : 'rgba(0,0,0,0.03)';

                if (viewsChart) {
                    viewsChart.updateOptions({ xaxis: { categories: data.labels } });
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
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 400,
                            }
                        },
                        colors: [primaryColor],
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
                        stroke: { curve: 'smooth', width: 2.5 },
                        xaxis: {
                            categories: data.labels,
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: { style: { colors: textColor, fontWeight: 600 } }
                        },
                        yaxis: {
                            labels: { 
                                style: { colors: textColor, fontWeight: 600 },
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
                        },
                        theme: { mode: isDark ? 'dark' : 'light' },
                        legend: { show: false },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
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
                    viewsChart = new ApexCharts(document.querySelector("#viewsChart"), options);
                    viewsChart.render();
                }

                if (loader) loader.classList.add('hidden');
            })
            // Fallback for demo environments
            .catch(() => {
                if (loader) loader.classList.add('hidden');
            });
    }

    @if($business && $business->status == 'approved')
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => setChartPeriod('month'), 300); // Delay slightly to prevent lag during GSAP stagger
        });
    @endif

    // Execute GrowthBar load animation safely
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            document.querySelectorAll('.GrowthBar').forEach(bar => {
                bar.style.transition = 'width 1.5s cubic-bezier(0.22, 1, 0.36, 1)';
                bar.style.width = bar.getAttribute('data-width');
            });
        }, 600);
    });

    // --- Support Chat Logic (Polling) ---
    const botAvatarUrl = '{{ asset("images/logo.webp") }}';
    let chatPolling = null;
    let chatOpen = false;

    function toggleSupportChat() {
        const modal = document.getElementById('supportChatModal');
        chatOpen = !chatOpen;
        if(chatOpen) {
            modal.classList.remove('hidden');
            // small animation delay
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
                chatBody.innerHTML = '';
                
                // Add the chat started indicator
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

    function sendChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
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
            <span class="text-[9px] text-slate-400/70 font-bold uppercase tracking-wider">Powered by AliDebo AI Routing</span>
        </div>
    </div>
</div>

@endsection