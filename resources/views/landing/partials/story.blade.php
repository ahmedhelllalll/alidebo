@php
    $steps = [
        ['kicker' => __('landing.story_step_1_kicker'), 'title' => __('landing.story_step_1_title'), 'body' => __('landing.story_step_1_desc')],
        ['kicker' => __('landing.story_step_2_kicker'), 'title' => __('landing.story_step_2_title'), 'body' => __('landing.story_step_2_desc')],
        ['kicker' => __('landing.story_step_3_kicker'), 'title' => __('landing.story_step_3_title'), 'body' => __('landing.story_step_3_desc')],
        ['kicker' => __('landing.story_step_4_kicker'), 'title' => __('landing.story_step_4_title'), 'body' => __('landing.story_step_4_desc')],
    ];
@endphp

<section id="how-it-works" class="py-24 lg:py-32 px-4 sm:px-6 scroll-mt-24">
    <div class="max-w-7xl mx-auto">
        <header class="max-w-3xl mb-16 lg:mb-20">
            <p class="reveal text-xs font-bold uppercase tracking-widest text-primary mb-4">{{ __('landing.story_badge') }}</p>
            <h2 class="reveal text-3xl sm:text-4xl lg:text-5xl font-[900] tracking-tight text-slate-950 dark:text-white mb-5">
                {{ __('landing.story_heading') }}
            </h2>
            <p class="reveal text-base sm:text-lg text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">
                {{ __('landing.story_subheading') }}
            </p>
        </header>

        <div class="lg:grid lg:grid-cols-12 lg:gap-16 items-start">
            <aside class="lg:col-span-5 mb-14 lg:mb-0">
                <div class="lg:sticky lg:top-28">
                    <div class="luxury-card p-6 sm:p-8 mb-8" id="story-visual-card">
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-500">{{ __('landing.story_badge') }}</p>
                            <span class="text-[10px] font-bold px-2 py-1 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">AliDebo</span>
                        </div>
                        <div class="space-y-3" id="story-rail" role="list">
                            @foreach ($steps as $idx => $step)
                                <div class="story-rail-item flex gap-3 items-start rounded-xl px-3 py-2.5 transition-colors duration-200 {{ $idx === 0 ? 'is-active' : '' }}"
                                    data-story-step="{{ $idx }}" role="listitem">
                                    <span class="story-rail-dot mt-1.5 w-2 h-2 rounded-full bg-slate-300 dark:bg-zinc-600 shrink-0 transition-all duration-200" aria-hidden="true"></span>
                                    <div class="min-w-0">
                                        <p class="story-rail-label text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-500 opacity-80 transition-opacity">
                                            {{ $step['kicker'] }}
                                        </p>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white leading-snug">{{ $step['title'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-zinc-800/80">
                            <div class="h-2 rounded-full bg-slate-100 dark:bg-zinc-800 overflow-hidden">
                                <div id="story-progress-bar" class="h-full w-1/4 bg-primary rounded-full transition-[width] duration-500 ease-out" style="width: 25%"></div>
                            </div>
                        </div>
                    </div>
                    <p class="reveal text-sm text-slate-500 dark:text-zinc-500 font-medium leading-relaxed px-1 hidden lg:block">
                        {{ __('landing.demo_cta_hint') }}
                    </p>
                </div>
            </aside>

            <div class="lg:col-span-7 space-y-20 lg:space-y-28" id="story-steps">
                @foreach ($steps as $idx => $step)
                    <article class="story-step reveal scroll-mt-32" data-story-index="{{ $idx }}">
                        <p class="text-primary text-xs font-bold uppercase tracking-widest mb-3">{{ $step['kicker'] }}</p>
                        <h3 class="text-2xl sm:text-3xl font-[900] tracking-tight text-slate-950 dark:text-white mb-4">
                            {{ $step['title'] }}
                        </h3>
                        <p class="text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed max-w-xl">
                            {{ $step['body'] }}
                        </p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
