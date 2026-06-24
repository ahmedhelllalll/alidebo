@extends('layouts.app')

@section('title', __('legal.cookies_title'))

@section('content')
<style>
    @keyframes blurFadeIn {
        from { opacity: 0; transform: translateY(20px); filter: blur(8px); }
        to { opacity: 1; transform: translateY(0); filter: blur(0); }
    }
    .animate-blur-fade {
        opacity: 0;
        animation: blurFadeIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<div class="pt-32 pb-24 lg:pt-48 lg:pb-32 bg-white dark:bg-[#050507] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-12 lg:gap-20 items-start">
            
            <!-- Left Side: Sticky Header -->
            <div class="lg:col-span-5 lg:sticky lg:top-40 mb-16 lg:mb-0 animate-blur-fade" style="animation-delay: 100ms;">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-[900] tracking-tighter text-slate-900 dark:text-white mb-6">
                    {{ __('legal.cookies_title') }}
                </h1>
                <p class="text-lg sm:text-xl text-slate-500 dark:text-zinc-400 leading-relaxed max-w-md">
                    {{ __('legal.cookies_intro') }}
                </p>
            </div>

            <!-- Right Side: Content -->
            <div class="lg:col-span-7 animate-blur-fade" style="animation-delay: 200ms;">
                <div class="bg-white/80 dark:bg-[#0a0a0c]/80 backdrop-blur-2xl rounded-3xl border border-slate-200/50 dark:border-zinc-800/50 shadow-2xl shadow-slate-200/20 dark:shadow-black/40 p-8 sm:p-12">
                    <div class="space-y-16">
                        
                        <!-- Section 1 -->
                        <section class="group">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-orange-400 p-[2px] shadow-lg shadow-primary/20 group-hover:shadow-primary/40 transition-shadow duration-500 shrink-0">
                                    <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center">
                                        <svg class="w-6 h-6 text-primary group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('legal.cookies_what_title') }}</h2>
                            </div>
                            <p class="text-lg text-slate-600 dark:text-zinc-400 leading-relaxed">{{ __('legal.cookies_what_desc') }}</p>
                        </section>
                        
                        <!-- Section 2 -->
                        <section class="group">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-400 p-[2px] shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/40 transition-shadow duration-500 shrink-0">
                                    <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-500 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                    </div>
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('legal.cookies_how_title') }}</h2>
                            </div>
                            <p class="text-lg text-slate-600 dark:text-zinc-400 leading-relaxed">{{ __('legal.cookies_how_desc') }}</p>
                        </section>

                        <!-- Section 3 -->
                        <section class="group">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-400 p-[2px] shadow-lg shadow-emerald-500/20 group-hover:shadow-emerald-500/40 transition-shadow duration-500 shrink-0">
                                    <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center">
                                        <svg class="w-6 h-6 text-emerald-500 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('legal.cookies_manage_title') }}</h2>
                            </div>
                            <p class="text-lg text-slate-600 dark:text-zinc-400 leading-relaxed">{{ __('legal.cookies_manage_desc') }}</p>
                        </section>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
