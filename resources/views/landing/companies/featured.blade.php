<section id="featured" class="pt-24 pb-12 sm:pb-16 relative bg-slate-50 dark:bg-[#0a0a0c] scroll-mt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="reveal flex flex-col sm:flex-row items-end justify-between mb-12 gap-6">
            <div>
                <h2 class="text-3xl md:text-4xl font-[900] tracking-tight text-slate-900 dark:text-white mb-4">
                    {{ __('landing.featured_companies') ?? 'Featured Companies' }}
                </h2>
                <p class="text-slate-600 dark:text-zinc-400 font-medium max-w-xl text-lg">
                    {{ __('landing.featured_desc') ?? 'Discover the top-rated businesses leading the industry with exceptional services.' }}
                </p>
            </div>
            <a href="#" class="shrink-0 flex items-center gap-2 text-primary font-bold hover:text-primary-light transition-colors group px-4 py-2 rounded-xl hover:bg-primary/5">
                {{ __('landing.view_all') ?? 'View All Featured' }}
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <!-- Premium SaaS Cards Grid Wrapper -->
        <div class="relative mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 pb-32">
                @foreach($featuredCompanies as $company)
                <div class="reveal luxury-card group relative flex flex-col h-full bg-white dark:bg-[#09090b] rounded-[1.5rem] overflow-hidden border border-slate-200/60 dark:border-zinc-800/60 transition-all duration-300 hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-1 z-10">
                    
                    {{-- Cover Image --}}
                    <div class="h-32 sm:h-40 bg-slate-100 dark:bg-zinc-800 relative overflow-hidden shrink-0 border-b border-slate-100 dark:border-zinc-800/50">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent z-10"></div>
                        @if($company->cover)
                            <img src="{{ $company->cover_url }}" alt="{{ $company->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <img src="{{ asset('images/home-background.webp') }}" alt="" loading="lazy" decoding="async" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                        @endif
                    </div>

                    {{-- Card Content --}}
                    <div class="p-6 sm:p-8 relative flex-1 flex flex-col">
                        
                        {{-- SaaS Logo Avatar --}}
                        <div class="absolute -top-10 start-6 sm:start-8 w-16 h-16 rounded-2xl bg-white dark:bg-zinc-900 border-4 border-white dark:border-[#09090b] shadow-sm flex items-center justify-center overflow-hidden z-20 transition-transform duration-300 group-hover:-translate-y-1">
                            @if($company->logo)
                                <img src="{{ $company->logo_url }}" alt="{{ $company->name }} logo" loading="lazy" decoding="async" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-primary/10 text-primary flex items-center justify-center font-[900] text-xl">
                                    {{ mb_substr($company->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-8 flex-1 flex flex-col">
                            <div class="flex items-center justify-between gap-4 mb-2">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white line-clamp-1 group-hover:text-primary transition-colors duration-300">{{ $company->name }}</h3>
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-50 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 opacity-0 -translate-x-2 rtl:translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 rtl:group-hover:translate-x-0 transition-all duration-300 shrink-0">
                                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </div>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-zinc-400 line-clamp-2 mb-6 font-medium leading-relaxed flex-1">
                                {{ $company->description ?? __('landing.featured_desc') }}
                            </p>
                            
                            {{-- SaaS Tags --}}
                            <div class="flex items-center gap-2 flex-wrap pt-5 border-t border-slate-100 dark:border-zinc-800/80 mt-auto">
                                @if($company->category)
                                    <span class="px-3 py-1 rounded-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200/60 dark:border-zinc-800/60 text-xs font-semibold text-slate-600 dark:text-zinc-400 transition-colors group-hover:border-primary/30 group-hover:text-primary dark:group-hover:text-primary-light">
                                        {{ $company->category->name }}
                                    </span>
                                @endif
                                @if($company->city)
                                    <span class="px-3 py-1 rounded-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200/60 dark:border-zinc-800/60 text-xs font-semibold text-slate-600 dark:text-zinc-400 transition-colors group-hover:border-primary/30 group-hover:text-primary dark:group-hover:text-primary-light">
                                        {{ $company->city->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('business.view', $company->slug ?? '') }}" class="absolute inset-0 z-30" aria-label="View Company"></a>
                </div>
                @endforeach
            </div>

            <!-- Fade Overlay & Button -->
            <div class="absolute bottom-0 inset-x-0 h-64 bg-gradient-to-t from-slate-50 via-slate-50/90 to-transparent dark:from-[#0a0a0c] dark:via-[#0a0a0c]/90 pointer-events-none z-40 flex items-end justify-center pb-4 sm:pb-8">
                <div class="reveal">
                    <a href="{{ route('directory.index') }}" class="pointer-events-auto inline-flex items-center gap-2 px-8 py-4 bg-slate-900 dark:bg-white text-white dark:text-zinc-900 rounded-2xl font-bold text-lg shadow-xl shadow-black/10 hover:shadow-black/20 hover:-translate-y-1 transition-all duration-300">
                        {{ __('landing.explore_all_companies') ?? 'Explore All Business Profiles' }}
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
