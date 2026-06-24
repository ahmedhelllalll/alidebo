<section id="vision" class="py-24 lg:py-32 px-4 sm:px-6 scroll-mt-24">
    <div class="max-w-3xl mx-auto">
        <div class="reveal luxury-card p-10 sm:p-14 lg:p-16 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-slate-50/50 dark:bg-zinc-900/20 pointer-events-none" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 border border-primary/15 flex items-center justify-center mx-auto mb-8">
                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h2 class="text-3xl sm:text-4xl font-[900] tracking-tight text-slate-950 dark:text-white mb-6">
                    {{ __('landing.vision_title') }}
                </h2>
                <p class="text-slate-600 dark:text-zinc-400 font-medium leading-relaxed max-w-xl mx-auto mb-10">
                    {{ __('landing.vision_desc') }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md mx-auto">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/8 dark:bg-primary/10 border border-primary/15 text-start">
                        <span class="w-2 h-2 rounded-full bg-primary shrink-0" aria-hidden="true"></span>
                        <span class="font-bold text-sm text-slate-900 dark:text-white">{{ __('landing.roadmap_1') }}</span>
                    </div>
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-100/80 dark:bg-zinc-800/50 border border-slate-200/60 dark:border-zinc-700/40 text-start opacity-70">
                        <span class="w-2 h-2 rounded-full bg-slate-400 dark:bg-zinc-500 shrink-0" aria-hidden="true"></span>
                        <span class="font-bold text-sm text-slate-700 dark:text-zinc-300">{{ __('landing.coming_soon') }} · {{ __('landing.vision_next') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
