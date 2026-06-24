<section id="profile-preview" class="py-24 lg:py-32 px-4 sm:px-6 bg-slate-50/50 dark:bg-zinc-950/35 border-y border-slate-100 dark:border-zinc-900/60 scroll-mt-24">
    <div class="max-w-7xl mx-auto">
        <header class="max-w-3xl mb-14 lg:mb-16">
            <p class="reveal text-xs font-bold uppercase tracking-widest text-primary mb-4">{{ __('landing.demo_badge') }}</p>
            <h2 class="reveal text-3xl sm:text-4xl lg:text-5xl font-[900] tracking-tight text-slate-950 dark:text-white mb-5">
                {{ __('landing.demo_heading') }}
            </h2>
            <p class="reveal text-base sm:text-lg text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">
                {{ __('landing.demo_subheading') }}
            </p>
        </header>

        <div class="reveal luxury-card overflow-hidden shadow-sm shadow-black/[0.03] dark:shadow-black/30">
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 sm:px-5 py-3 border-b border-slate-200/80 dark:border-zinc-800 bg-slate-50/80 dark:bg-zinc-900/50">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="flex gap-1.5 shrink-0" aria-hidden="true">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-zinc-600"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-zinc-600"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-zinc-600"></span>
                    </span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-500 truncate">{{ __('landing.demo_browser_chrome') }}</span>
                </div>
                <div class="flex min-w-0 flex-1 sm:flex-initial sm:max-w-md">
                    <div class="flex-1 rounded-lg bg-white dark:bg-zinc-950 border border-slate-200/80 dark:border-zinc-800 px-3 py-1.5 text-xs font-mono text-slate-500 dark:text-zinc-500 truncate text-center sm:text-start">
                        {{ __('landing.demo_url') }}
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-12 gap-0">
                <div class="lg:col-span-4 border-b lg:border-b-0 lg:border-e border-slate-200/80 dark:border-zinc-800 p-6 sm:p-8 bg-white dark:bg-[#0a0a0c]">
                    <div class="flex items-start justify-between gap-4 mb-6">
                        <div class="min-w-0">
                            <p class="text-lg sm:text-xl font-[900] tracking-tight text-slate-950 dark:text-white truncate">{{ __('landing.demo_company') }}</p>
                            <p class="text-sm text-slate-500 dark:text-zinc-500 font-medium mt-1 leading-snug">{{ __('landing.demo_tagline') }}</p>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary/10 text-primary text-[11px] font-bold border border-primary/15">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ __('landing.demo_verified') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-8">
                        <div class="rounded-xl border border-slate-200/80 dark:border-zinc-800 p-4 bg-slate-50/50 dark:bg-zinc-900/40">
                            <p class="text-2xl font-[900] text-slate-950 dark:text-white">{{ __('landing.demo_stat_a_value') }}</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-500 mt-1">{{ __('landing.demo_stat_a_label') }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200/80 dark:border-zinc-800 p-4 bg-slate-50/50 dark:bg-zinc-900/40">
                            <p class="text-2xl font-[900] text-slate-950 dark:text-white">{{ __('landing.demo_stat_b_value') }}</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-500 mt-1">{{ __('landing.demo_stat_b_label') }}</p>
                        </div>
                    </div>

                    <div class="flex rounded-xl bg-slate-100/80 dark:bg-zinc-900/80 p-1 border border-slate-200/60 dark:border-zinc-800" role="tablist" aria-label="{{ __('landing.demo_badge') }}">
                        <button type="button" role="tab" aria-selected="true" data-demo-tab="overview" id="demo-tab-overview"
                            class="demo-tab flex-1 py-2.5 text-xs font-bold rounded-lg transition-colors bg-white dark:bg-zinc-950 text-slate-900 dark:text-white shadow-sm border border-slate-200/50 dark:border-zinc-800">
                            {{ __('landing.demo_tab_profile') }}
                        </button>
                        <button type="button" role="tab" aria-selected="false" data-demo-tab="services" id="demo-tab-services"
                            class="demo-tab flex-1 py-2.5 text-xs font-bold rounded-lg transition-colors text-slate-500 dark:text-zinc-500 hover:text-slate-800 dark:hover:text-zinc-200">
                            {{ __('landing.demo_tab_services') }}
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-8 p-6 sm:p-8 lg:p-10 bg-white dark:bg-[#0e0e11]">
                    <div id="demo-panel-overview" class="demo-panel space-y-8" role="tabpanel" aria-labelledby="demo-tab-overview">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 dark:text-zinc-500 mb-3">{{ __('landing.demo_about_title') }}</h3>
                            <p class="text-base text-slate-700 dark:text-zinc-300 font-medium leading-relaxed max-w-2xl">
                                {{ __('landing.demo_about_body') }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 dark:text-zinc-500 mb-3">{{ __('landing.demo_media_label') }}</h3>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach ([1, 2, 3] as $i)
                                    <div class="aspect-[4/3] rounded-xl bg-slate-100 dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-800 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-br from-slate-200/80 to-slate-100 dark:from-zinc-800 dark:to-zinc-900"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div id="demo-panel-services" class="demo-panel hidden space-y-6" role="tabpanel" aria-labelledby="demo-tab-services" hidden>
                        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 dark:text-zinc-500">{{ __('landing.demo_services_title') }}</h3>
                        <ul class="space-y-3">
                            @foreach ([__('landing.demo_service_a'), __('landing.demo_service_b'), __('landing.demo_service_c')] as $svc)
                                <li class="flex items-center gap-3 rounded-xl border border-slate-200/80 dark:border-zinc-800 px-4 py-3.5 bg-slate-50/50 dark:bg-zinc-900/40">
                                    <span class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0">{{ $loop->iteration }}</span>
                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $svc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <p class="mt-10 text-xs text-slate-500 dark:text-zinc-600 font-medium">{{ __('landing.demo_cta_hint') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
