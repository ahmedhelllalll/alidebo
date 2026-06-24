<section class="py-24 lg:py-32 px-4 sm:px-6" aria-labelledby="final-cta-heading">
    <div class="max-w-4xl mx-auto text-center">
        <div class="reveal rounded-3xl border border-slate-200 dark:border-zinc-800 bg-slate-950 text-white px-10 py-14 sm:px-14 sm:py-16 relative overflow-hidden">
            <div class="absolute inset-0 opacity-30 pointer-events-none bg-[radial-gradient(ellipse_at_top,rgba(244,80,24,0.35),transparent_55%)]" aria-hidden="true"></div>
            <div class="relative z-10">
                <h2 id="final-cta-heading" class="text-3xl sm:text-4xl lg:text-5xl font-[900] tracking-tight text-white mb-6">
                    {{ __('landing.final_cta_heading') }}
                </h2>
                <p class="text-base sm:text-lg text-zinc-400 max-w-xl mx-auto mb-10 font-medium leading-relaxed">
                    {{ __('landing.final_cta_desc') }}
                </p>
                <a href="{{ route('register') }}"
                    class="inline-flex items-center justify-center gap-2 px-8 sm:px-10 py-4 rounded-2xl bg-primary text-white font-bold text-base shadow-lg shadow-primary/25 hover:bg-primary-light active:scale-[0.98] transition-all duration-200">
                    {{ __('landing.final_cta_button') }}
                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
