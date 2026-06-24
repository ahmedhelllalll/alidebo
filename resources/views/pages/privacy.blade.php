@extends('layouts.app')

@section('title', __('legal.privacy_title'))

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
                    {{ __('legal.privacy_title') }}
                </h1>
                <p class="text-lg sm:text-xl text-slate-500 dark:text-zinc-400 leading-relaxed max-w-md">
                    {{ __('legal.privacy_intro') }}
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
                                        <svg class="w-6 h-6 text-primary group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                                    </div>
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('legal.privacy_collect_title') }}</h2>
                            </div>
                            <p class="text-lg text-slate-600 dark:text-zinc-400 leading-relaxed">{{ __('legal.privacy_collect_desc') }}</p>
                        </section>
                        
                        <!-- Section 2 -->
                        <section class="group">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-400 p-[2px] shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/40 transition-shadow duration-500 shrink-0">
                                    <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-500 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('legal.privacy_use_title') }}</h2>
                            </div>
                            <p class="text-lg text-slate-600 dark:text-zinc-400 leading-relaxed">{{ __('legal.privacy_use_desc') }}</p>
                        </section>

                        <!-- Section 3 -->
                        <section class="group">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-400 p-[2px] shadow-lg shadow-emerald-500/20 group-hover:shadow-emerald-500/40 transition-shadow duration-500 shrink-0">
                                    <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center">
                                        <svg class="w-6 h-6 text-emerald-500 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('legal.privacy_security_title') }}</h2>
                            </div>
                            <p class="text-lg text-slate-600 dark:text-zinc-400 leading-relaxed">{{ __('legal.privacy_security_desc') }}</p>
                        </section>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
