<section class="relative min-h-[92vh] flex flex-col justify-center pt-28 pb-20 px-4 sm:px-6 border-b border-slate-200/80 dark:border-zinc-800/80">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute inset-0 opacity-[0.35] dark:opacity-[0.2]"
            style="background-image: linear-gradient(to bottom, rgba(244,80,24,0.04) 0%, transparent 42%), linear-gradient(90deg, rgba(15,23,42,0.03) 1px, transparent 1px), linear-gradient(rgba(15,23,42,0.03) 1px, transparent 1px); background-size: 100% 100%, 72px 72px, 72px 72px;">
        </div>
    </div>

    <div class="max-w-5xl mx-auto relative z-10">
        <p class="reveal text-[11px] sm:text-xs font-bold uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500 mb-6">
            {{ __('landing.hero_eyebrow') }}
        </p>

        <h1 class="reveal text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-[900] tracking-tight leading-[1.08] text-slate-950 dark:text-white mb-8">
            {{ __('landing.hero_h1_part1') }}
            <span class="block sm:inline sm:ms-2 ">{{ __('landing.hero_h1_part2') }}</span>
        </h1>

        <p class="reveal text-lg sm:text-xl text-slate-600 dark:text-zinc-400 max-w-2xl font-medium leading-relaxed mb-10">
            {{ __('landing.hero_desc') }}
        </p>

        <div class="reveal flex flex-wrap gap-2 mb-12">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200/80 dark:border-zinc-800">
                {{ __('landing.hero_point_1') }}
            </span>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200/80 dark:border-zinc-800">
                {{ __('landing.hero_point_2') }}
            </span>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200/80 dark:border-zinc-800">
                {{ __('landing.hero_point_3') }}
            </span>
        </div>

        <div class="reveal flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
            <a href="{{ route('register') }}"
                class="inline-flex justify-center items-center px-8 py-4 rounded-2xl bg-primary text-white font-bold text-base shadow-lg shadow-primary/20 hover:bg-primary-light hover:shadow-primary/30 active:scale-[0.98] transition-all duration-200">
                {{ __('landing.cta_main') }}
            </a>
            <a href="#how-it-works"
                class="inline-flex justify-center items-center px-8 py-4 rounded-2xl font-bold text-base border border-slate-200 dark:border-zinc-700 bg-white/80 dark:bg-zinc-900/80 text-slate-900 dark:text-white hover:border-primary/30 dark:hover:border-primary/25 transition-all duration-200">
                {{ __('landing.cta_secondary') }}
                <svg class="w-4 h-4 ms-2 rtl:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <div class="reveal mt-14 flex flex-col sm:flex-row sm:items-center gap-4 text-sm text-slate-500 dark:text-zinc-500">
            <span class="inline-flex items-center gap-2 font-semibold text-slate-600 dark:text-zinc-400">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse shrink-0" aria-hidden="true"></span>
                {{ __('landing.version') }}
            </span>
            <span class="hidden sm:block w-px h-4 bg-slate-200 dark:bg-zinc-800" aria-hidden="true"></span>
            <span class="font-medium">{{ __('landing.stat_1_number') }} {{ __('landing.stat_1_label') }}</span>
        </div>
    </div>
</section>
