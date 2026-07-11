@extends('layouts.app')

@section('title', __('landing.nav_about') ?? 'About Us')

@section('content')

<div class="relative overflow-hidden bg-white dark:bg-[#0a0a0c]">
    <!-- Ambient Corner Glows -->
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
        <div class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full"></div>
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-primary/10 dark:bg-primary/10 blur-[140px] rounded-full"></div>
    </div>

    <!-- Floating Hero Section (Matches CTA-Ads Style from Welcome Page) -->
    <div class="relative z-10 hero-font max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 pt-32 sm:pt-40">
    <section class="reveal relative w-full bg-white dark:bg-[#0a0a0c] rounded-[2rem] lg:rounded-[3rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] flex flex-col lg:flex-row items-center justify-between">
        
        {{-- Text Content (Left) --}}
        <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-20 relative z-10 flex flex-col items-center lg:items-start text-center lg:text-start lg:rtl:text-right">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-[900] tracking-tight text-slate-900 dark:text-white leading-tight mb-6">
                {!! __('about.hero_title') !!}
            </h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">
                {{ __('about.hero_subtitle') }}
            </p>
        </div>

        {{-- Minimalist Visual (Right) --}}
        <div class="w-full lg:w-1/2 h-[350px] sm:h-[450px] lg:h-auto lg:self-stretch relative overflow-hidden bg-slate-100/50 dark:bg-zinc-900/20 border-t lg:border-t-0 lg:border-l border-slate-200/60 dark:border-zinc-800/60 flex items-center justify-center rtl:lg:border-l-0 rtl:lg:border-r">
            
            {{-- Decorative Gradients --}}
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-primary/20 dark:bg-primary/10 rounded-full blur-[80px]"></div>
            </div>

            <!-- Floating Minimal Card -->
            <div class="relative z-10 w-[280px] sm:w-[320px] bg-white/70 dark:bg-zinc-900/70 backdrop-blur-2xl border border-white dark:border-zinc-700/50 rounded-[2rem] shadow-2xl shadow-slate-200/50 dark:shadow-black/50 p-8 sm:p-10 transform transition-transform duration-700 hover:-translate-y-2 hover:scale-[1.02] flex flex-col items-center justify-center">
                
                <!-- Icon -->
                <div class="w-20 h-20 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 relative z-10 border border-primary/20">
                    <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                
                <div class="text-center relative z-10">
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ __('about.hero_badge_network') }}</div>
                    <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-zinc-400 tracking-wide uppercase">{{ __('about.hero_badge_connecting') }}</div>
                </div>
            </div>
            
        </div>

    </section>
</div>

<!-- Content Grid (Matches Featured Companies styling) -->
<section class="pt-8 pb-24 relative bg-slate-50 dark:bg-[#0a0a0c]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Values Grid (Premium Design) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 mb-24 mt-8">
            <!-- Value 1 -->
            <div class="reveal group relative flex flex-col h-full bg-white dark:bg-[#09090b] rounded-[2rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 p-8 md:p-10 transition-all duration-300 ease-out will-change-transform hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-1.5 z-10">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-out pointer-events-none"></div>
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30 flex items-center justify-center mb-8 transform transition-transform duration-300 ease-out group-hover:scale-110 group-hover:rotate-3">
                    <i class="fa-solid fa-shield-halved text-white text-3xl"></i>
                </div>
                <h4 class="text-xl sm:text-2xl font-[900] text-slate-900 dark:text-white mb-4 relative z-10">{{ __('about.value1_title') }}</h4>
                <p class="text-sm sm:text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed relative z-10">
                    {{ __('about.value1_desc') }}
                </p>
            </div>
            
            <!-- Value 2 -->
            <div class="reveal group relative flex flex-col h-full bg-white dark:bg-[#09090b] rounded-[2rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 p-8 md:p-10 transition-all duration-300 ease-out will-change-transform hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-1.5 z-10">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-out pointer-events-none"></div>
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-orange-500 shadow-lg shadow-primary/30 flex items-center justify-center mb-8 transform transition-transform duration-300 ease-out group-hover:scale-110 group-hover:-rotate-3">
                    <i class="fa-solid fa-users-gear text-white text-3xl"></i>
                </div>
                <h4 class="text-xl sm:text-2xl font-[900] text-slate-900 dark:text-white mb-4 relative z-10">{{ __('about.value2_title') }}</h4>
                <p class="text-sm sm:text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed relative z-10">
                    {!! __('about.value2_desc') !!}
                </p>
            </div>

            <!-- Value 3 -->
            <div class="reveal group relative flex flex-col h-full bg-white dark:bg-[#09090b] rounded-[2rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 p-8 md:p-10 transition-all duration-300 ease-out will-change-transform hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-1.5 z-10">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-out pointer-events-none"></div>
                <div class="w-16 h-16 rounded-2xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 shadow-lg shadow-green-500/10 flex items-center justify-center mb-8 transform transition-transform duration-300 ease-out group-hover:scale-110 group-hover:rotate-3">
                    <i class="fa-solid fa-chart-line text-green-600 dark:text-green-500 text-3xl"></i>
                </div>
                <h4 class="text-xl sm:text-2xl font-[900] text-slate-900 dark:text-white mb-4 relative z-10">{{ __('about.value3_title') }}</h4>
                <p class="text-sm sm:text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed relative z-10">
                    {{ __('about.value3_desc') }}
                </p>
            </div>
        </div>
        
    </div>
</section>
</div>
@endsection
