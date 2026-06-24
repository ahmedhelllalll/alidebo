<section id="capabilities" class="py-24 lg:py-32 px-4 sm:px-6 scroll-mt-24">
    <div class="max-w-7xl mx-auto">
        <header class="text-center max-w-3xl mx-auto mb-16 lg:mb-20">
            <p class="reveal text-xs font-bold uppercase tracking-widest text-primary mb-4">{{ __('landing.features_badge') }}</p>
            <h2 class="reveal text-3xl sm:text-4xl lg:text-5xl font-[900] tracking-tight text-slate-950 dark:text-white mb-5">
                {{ __('landing.features_heading') }}
            </h2>
            <p class="reveal text-base sm:text-lg text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">
                {{ __('landing.features_subheading') }}
            </p>
        </header>

        <div class="bento-grid">
            <div class="reveal col-span-12 lg:col-span-8 luxury-card p-8 sm:p-10 lg:p-12 flex flex-col justify-between min-h-[320px] lg:min-h-[360px] group">
                <div class="max-w-xl relative z-10">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center mb-6 border border-primary/10">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-[900] tracking-tight text-slate-950 dark:text-white mb-4">{{ __('landing.feature_1_title') }}</h3>
                    <p class="text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">{{ __('landing.feature_1_desc') }}</p>
                </div>
                <div class="absolute -bottom-10 -end-10 w-48 h-48 rounded-3xl border border-slate-200/60 dark:border-zinc-800/80 bg-slate-50/50 dark:bg-zinc-900/30 pointer-events-none" aria-hidden="true"></div>
            </div>

            <div class="reveal col-span-12 lg:col-span-4 luxury-card p-8 sm:p-10 flex flex-col justify-between min-h-[320px] lg:min-h-[360px] bg-slate-950 dark:bg-zinc-950 text-white border-slate-900 dark:border-zinc-800">
                <div>
                    <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-[900] tracking-tight mb-4">{{ __('landing.feature_2_title') }}</h3>
                    <p class="text-white/75 font-medium leading-relaxed">{{ __('landing.feature_2_desc') }}</p>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-white/40 mt-8 font-mono">{{ __('landing.public_url_hint') }}</p>
            </div>

            <div class="reveal col-span-12 sm:col-span-6 lg:col-span-5 luxury-card p-8 sm:p-10 min-h-[260px]">
                <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center mb-6 border border-primary/10">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-[900] tracking-tight text-slate-950 dark:text-white mb-3">{{ __('landing.feature_3_title') }}</h3>
                <p class="text-sm sm:text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">{{ __('landing.feature_3_desc') }}</p>
            </div>

            <div class="reveal col-span-12 sm:col-span-6 lg:col-span-7 luxury-card p-8 sm:p-10 min-h-[260px] flex flex-col sm:flex-row sm:items-center gap-8">
                <div class="flex-1 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center mb-6 border border-primary/10">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-[900] tracking-tight text-slate-950 dark:text-white mb-3">{{ __('landing.feature_4_title') }}</h3>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">{{ __('landing.feature_4_desc') }}</p>
                </div>
                <div class="flex gap-2 shrink-0" aria-hidden="true">
                    <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">{{ __('landing.demo_lang_en') }}</span>
                    <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-primary/10 text-primary border border-primary/15">{{ __('landing.demo_lang_ar') }}</span>
                    <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">{{ __('landing.demo_lang_more') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>
