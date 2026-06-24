<section id="metrics" class="py-20 lg:py-24 px-4 sm:px-6 border-y border-slate-100 dark:border-zinc-900/70 bg-white dark:bg-[#09090b] scroll-mt-24">
    <div class="max-w-7xl mx-auto">
        <header class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12 lg:mb-14">
            <div class="max-w-2xl">
                <p class="reveal text-xs font-bold uppercase tracking-widest text-primary mb-3">{{ __('landing.stats_badge') }}</p>
                <h2 class="reveal text-2xl sm:text-3xl lg:text-4xl font-[900] tracking-tight text-slate-950 dark:text-white mb-3">
                    {{ __('landing.stats_heading') }}
                </h2>
                <p class="reveal text-sm sm:text-base text-slate-600 dark:text-zinc-400 font-medium">{{ __('landing.stats_subheading') }}</p>
            </div>
        </header>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach ([
                ['n' => __('landing.stat_1_number'), 'l' => __('landing.stat_1_label')],
                ['n' => __('landing.stat_2_number'), 'l' => __('landing.stat_2_label')],
                ['n' => __('landing.stat_3_number'), 'l' => __('landing.stat_3_label')],
                ['n' => __('landing.stat_4_number'), 'l' => __('landing.stat_4_label')],
            ] as $stat)
                <div class="reveal luxury-card p-6 sm:p-8 text-center lg:text-start">
                    <p class="text-3xl sm:text-4xl font-[900] tracking-tight text-slate-950 dark:text-white mb-2">{{ $stat['n'] }}</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 dark:text-zinc-500 uppercase tracking-wider">{{ $stat['l'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
