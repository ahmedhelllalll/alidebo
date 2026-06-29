@push('styles')
<link rel="preload" href="{{ asset('images/home-background.webp') }}" as="image" type="image/webp" fetchpriority="high">
@endpush
<style>
.hero-font { font-family: 'Plus Jakarta Sans', 'Cairo', system-ui, sans-serif; }

/* Smooth Fade Up Animations */
@keyframes heroFadeUp {
    0% { opacity: 0; transform: translateY(40px); }
    100% { opacity: 1; transform: translateY(0); }
}
.hero-fade-element {
    opacity: 0;
    animation: heroFadeUp 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.hero-fade-element:nth-child(1) { animation-delay: 0.2s; }
.hero-fade-element:nth-child(2) { animation-delay: 0.4s; }
.hero-fade-element:nth-child(3) { animation-delay: 0.6s; }
</style>
<div class="hero-font px-3 sm:px-6 lg:px-8 pb-3 sm:pb-6 lg:pb-8 pt-20 sm:pt-24">
<section id="discover" class="relative w-full z-40 flex flex-col min-h-[85vh] justify-center scroll-mt-24 rounded-[2rem] shadow-[0_20px_50px_-12px_rgba(0,0,0,0.25)] overflow-hidden">

    {{-- Image Background --}}
    <div class="absolute inset-0 pointer-events-none">
        <img src="{{ asset('images/home-background.webp') }}" alt="" fetchpriority="high" decoding="sync" class="absolute inset-0 w-full h-full object-cover">
        {{-- Dark Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-r from-[#09090b]/90 via-[#09090b]/70 to-[#09090b]/30 rtl:from-[#09090b]/30 rtl:via-[#09090b]/70 rtl:to-[#09090b]/90"></div>
        <div class="absolute inset-0 bg-[#09090b]/30 backdrop-blur-[2px]"></div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 max-w-7xl mx-auto w-full px-6 sm:px-10 lg:px-12 relative z-10 flex flex-col justify-center py-16 sm:py-20">
        <div class="flex flex-col items-start text-start w-full lg:w-3/5 gap-8 sm:gap-10">

            {{-- Headline --}}
            <h1 class="hero-fade-element text-3xl sm:text-4xl md:text-5xl lg:text-6xl rtl:lg:text-[3.25rem] font-bold leading-[1.2] text-white mb-4">
                <span>{{ __('landing.hero_unified_headline_1') }}</span>
                <span class="relative inline-block mx-1 md:mx-2 z-10">
                    <span class="relative z-10 bg-clip-text text-transparent bg-gradient-to-r from-primary to-primary-light drop-shadow-sm">{{ __('landing.hero_unified_highlight') }}</span>
                </span>
                <br class="hidden sm:block">
                <span>{{ __('landing.hero_unified_headline_2') }}</span>
            </h1>

            {{-- Description --}}
            <p class="hero-fade-element text-base sm:text-lg text-white font-medium max-w-2xl mb-8 leading-relaxed rtl:leading-loose">
               {{ __('landing.hero_unified_desc') }}
            </p>

            {{-- Action Area --}}
            <div class="hero-fade-element relative z-50 w-full flex flex-col sm:flex-row items-stretch sm:items-center justify-start gap-4 mt-2">
                {{-- Primary: Join Now / Dashboard --}}
                @auth
                    <button type="button" onclick="window.location.href='{{ url('/dashboard') }}'"
                       class="group relative z-[100] pointer-events-auto cursor-pointer h-[3.5rem] sm:h-[3.75rem] px-8 sm:px-10 rounded-[1.25rem] bg-primary hover:bg-primary-light text-white flex items-center justify-center transition-all duration-500 font-bold text-base sm:text-lg shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 hover:-translate-y-0.5 active:scale-[0.98]">
                        <span>{{ __('landing.nav_dashboard') ?? 'Dashboard' }}</span>
                    </button>
                @else
                    <button type="button" onclick="window.location.href='{{ route('register') }}'"
                       class="group relative z-[100] pointer-events-auto cursor-pointer h-[3.5rem] sm:h-[3.75rem] px-8 sm:px-10 rounded-[1.25rem] bg-primary hover:bg-primary-light text-white flex items-center justify-center transition-all duration-500 font-bold text-base sm:text-lg shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 hover:-translate-y-0.5 active:scale-[0.98]">
                        <span>{{ __('landing.hero_join_cta') }}</span>
                    </button>
                @endauth

                {{-- Secondary: Explore Companies --}}
                <button type="button"
                        onclick="event.preventDefault(); document.getElementById('featured').scrollIntoView({ behavior: 'smooth', block: 'start' });"
                        class="group h-[3.5rem] sm:h-[3.75rem] px-8 sm:px-10 rounded-[1.25rem] bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white flex items-center justify-center transition-all duration-500 font-bold text-base sm:text-lg hover:-translate-y-0.5 active:scale-[0.98]">
                    <span>{{ __('landing.hero_unified_btn') }}</span>
                </button>
            </div>

        </div>
    </div>
</section>
</div>
