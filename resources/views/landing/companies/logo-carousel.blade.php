<div class="w-full bg-transparent py-10 sm:py-16 overflow-hidden relative reveal">
    <!-- Title -->
    <div class="mb-10 text-center px-4">
        <h3 class="text-xs sm:text-sm font-bold tracking-[0.2em] text-slate-400 dark:text-zinc-500 uppercase">
            {{ __('landing.trusted_by') ?? 'Trusted by innovative companies worldwide' }}
        </h3>
    </div>

    <!-- Gradient Masks for seamless fade -->
    <div class="absolute inset-y-0 left-0 w-24 sm:w-48 bg-gradient-to-r from-slate-50 dark:from-[#09090b] to-transparent z-10 pointer-events-none"></div>
    <div class="absolute inset-y-0 right-0 w-24 sm:w-48 bg-gradient-to-l from-slate-50 dark:from-[#09090b] to-transparent z-10 pointer-events-none"></div>

    <!-- Marquee Track -->
    <div class="marquee">
        @php
            // Ensure we have a minimum number of items so the slider is ALWAYS wider than the screen.
            $displayCompanies = $heroMarquee->count() > 0 ? $heroMarquee : collect([]);
            
            if ($displayCompanies->count() > 0) {
                while($displayCompanies->count() < 12) {
                    $displayCompanies = $displayCompanies->concat($displayCompanies);
                }
            }
        @endphp

        @if($displayCompanies->count() > 0)
        <div class="logo-marquee-content items-center">
            {{-- We duplicate the list to make the loop mathematically seamless (-50% translation) --}}
            @for ($i = 0; $i < 2; $i++)
                <div class="flex items-center gap-16 sm:gap-24 pr-16 sm:pr-24 shrink-0">
                    @foreach($displayCompanies as $company)
                        <a href="{{ $company['url'] }}" class="logo-marquee-item flex items-center justify-center opacity-50 hover:opacity-100 transition-all duration-300">
                            <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="h-10 sm:h-12 w-auto object-contain max-w-[140px] grayscale hover:grayscale-0 transition-all duration-300" loading="lazy" onerror="this.style.display='none'">
                        </a>
                    @endforeach
                </div>
            @endfor
        </div>
        @endif
    </div>
</div>
