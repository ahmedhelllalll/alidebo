<section id="trust" class="py-24 lg:py-32 px-4 sm:px-6 scroll-mt-24">
    <div class="max-w-7xl mx-auto">
        <header class="text-center max-w-3xl mx-auto mb-16 lg:mb-20">
            <p class="reveal text-xs font-bold uppercase tracking-widest text-primary mb-4">{{ __('landing.trust_badge') }}</p>
            <h2 class="reveal text-3xl sm:text-4xl lg:text-5xl font-[900] tracking-tight text-slate-950 dark:text-white">
                {{ __('landing.trust_heading') }}
            </h2>
        </header>

        @php
            $trustItems = [
                ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => __('landing.trust_item_1_title'), 'desc' => __('landing.trust_item_1_desc')],
                ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => __('landing.trust_item_2_title'), 'desc' => __('landing.trust_item_2_desc')],
                ['icon' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129', 'title' => __('landing.trust_item_3_title'), 'desc' => __('landing.trust_item_3_desc')],
                ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'title' => __('landing.trust_item_4_title'), 'desc' => __('landing.trust_item_4_desc')],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 lg:gap-6">
            @foreach ($trustItems as $item)
                <div class="reveal luxury-card p-8 sm:p-9 hover:border-primary/20 transition-colors duration-300">
                    <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center mb-6 border border-primary/10">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-950 dark:text-white mb-2">{{ $item['title'] }}</h3>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">{{ $item['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
