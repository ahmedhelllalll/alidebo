<section id="explore" class="py-20 relative bg-white dark:bg-[#0a0a0c] scroll-mt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center mb-14 reveal">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-sm font-semibold mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                {{ __('landing.featured') ?? 'Handpicked for You' }}
            </span>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-slate-900 dark:text-white mb-4">
                {{ __('landing.suggested_companies') ?? 'Suggested For You' }}
            </h2>
            <p class="text-slate-500 dark:text-slate-400 font-medium max-w-2xl mx-auto text-lg leading-relaxed">
                {{ __('landing.suggested_desc') ?? 'Curated businesses tailored to your interests and recent searches.' }}
            </p>
        </div>

        @if(isset($suggestedCompanies) && count($suggestedCompanies) > 0)
            <!-- Featured Carousel -->
            <div class="relative w-full h-[500px] md:h-[520px] rounded-3xl overflow-hidden shadow-2xl shadow-slate-900/20 dark:shadow-black/40 bg-slate-900 group" aria-roledescription="carousel" aria-label="Featured Companies">
                
                <!-- Slides Container -->
                <div class="relative w-full h-full">
                    @foreach($suggestedCompanies as $company)
                        @php
                            $marketingSentences = [
                                'Industry leaders redefining excellence with cutting-edge solutions and reliable professional services.',
                                'Empowering partners and delivering premium, highly trusted marketplace value tailored to your needs.',
                                'An exceptional industry choice committed to premium quality, innovative strategies, and long-term success.'
                            ];
                            $selectedMarketing = $marketingSentences[$loop->index % count($marketingSentences)];
                        @endphp

                        <div class="slide absolute inset-0 w-full h-full opacity-0 invisible transition-all duration-700 ease-out {{ $loop->first ? 'active' : '' }}"
                             role="group" 
                             aria-roledescription="slide" 
                             aria-label="{{ $loop->iteration }} of {{ count($suggestedCompanies) }}">
                            
                            <!-- Background Image -->
                            <div class="absolute inset-0 w-full h-full">
                                @if($company->cover)
                                    <img src="{{ Storage::url($company->cover) }}" alt="" class="w-full h-full object-cover transition-transform duration-[2000ms] ease-out group-hover:scale-105">
                                @else
                                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1200&h=600" alt="" class="w-full h-full object-cover transition-transform duration-[2000ms] ease-out group-hover:scale-105">
                                @endif
                            </div>

                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/50 to-slate-950/20 z-[1]"></div>
                            
                            <!-- Clickable Link Overlay -->
                            <a href="{{ route('business.view', $company->slug ?? '') }}" class="absolute inset-0 z-10" aria-label="View {{ $company->name }}"></a>

                            <!-- Content -->
                            <div class="slide-content absolute inset-0 z-20 flex flex-col items-center justify-end pb-20 px-6 text-center">
                                
                                <!-- Logo -->
                                <div class="relative mb-5 group-hover:scale-105 transition-transform duration-500">
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 flex items-center justify-center overflow-hidden shadow-2xl shadow-black/20">
                                        @if($company->logo)
                                            <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-indigo-500/40 to-slate-900 text-white flex items-center justify-center font-black text-3xl">
                                                {{ mb_substr($company->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="absolute -bottom-1 -right-1 inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-500 text-white shadow-lg border-2 border-slate-900 z-10" title="Verified">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    </span>
                                </div>

                                <!-- Company Name -->
                                <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white tracking-tight mb-3 drop-shadow-lg">
                                    {{ $company->name }}
                                </h3>

                                <!-- Tags -->
                                <div class="flex items-center gap-2 mb-4 flex-wrap justify-center">
                                    @if($company->category)
                                        <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/10 text-xs font-semibold tracking-wider text-white/90 uppercase">
                                            {{ $company->category->name }}
                                        </span>
                                    @endif

                                    @if($company->city)
                                        <span class="px-3 py-1 rounded-full bg-white/5 backdrop-blur-md border border-white/10 text-xs font-semibold tracking-wider text-white/70">
                                            {{ $company->city->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Description -->
                                <p class="text-sm sm:text-base text-white/70 max-w-lg leading-relaxed line-clamp-2 font-light">
                                    {{ $company->description ?? $selectedMarketing }}
                                </p>

                                <!-- CTA -->
                                <div class="mt-6">
                                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-white/90 group-hover:text-white transition-colors">
                                        Explore Profile
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Navigation Arrows -->
                <button class="carousel-prev absolute left-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 text-white flex items-center justify-center hover:bg-white/20 hover:scale-110 transition-all duration-300 opacity-0 group-hover:opacity-100" aria-label="Previous slide">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button class="carousel-next absolute right-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 text-white flex items-center justify-center hover:bg-white/20 hover:scale-110 transition-all duration-300 opacity-0 group-hover:opacity-100" aria-label="Next slide">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>

                <!-- Progress Dots -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-30">
                    @foreach($suggestedCompanies as $company)
                        <button class="carousel-dot h-2 rounded-full transition-all duration-500 cursor-pointer border-none {{ $loop->first ? 'w-8 bg-white' : 'w-2 bg-white/40 hover:bg-white/60' }}" 
                                aria-label="Go to slide {{ $loop->iteration }}"
                                data-index="{{ $loop->index }}"></button>
                    @endforeach
                </div>
            </div>

            <!-- Quick Grid Preview (Below Carousel) -->
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($suggestedCompanies as $company)
                    <a href="{{ route('business.view', $company->slug ?? '') }}" 
                       class="group/card relative flex items-center gap-3 p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-indigo-200 dark:hover:border-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden shrink-0 group-hover/card:scale-110 transition-transform duration-300">
                            @if($company->logo)
                                <img src="{{ Storage::url($company->logo) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="font-bold text-slate-600 dark:text-slate-400 text-sm">{{ mb_substr($company->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate group-hover/card:text-indigo-600 dark:group-hover/card:text-indigo-400 transition-colors">{{ $company->name }}</p>
                            @if($company->category)
                                <p class="text-xs text-slate-500 dark:text-slate-500 truncate">{{ $company->category->name }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-16 px-6 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-3xl">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">No recommendations yet</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto">Start exploring businesses to get personalized suggestions tailored to your interests.</p>
            </div>
        @endif
    </div>
</section>

<style>
    /* Carousel Slide Transitions */
    .slide {
        transform: scale(1.02);
    }
    .slide.active {
        opacity: 1 !important;
        visibility: visible !important;
        transform: scale(1) !important;
    }
    .slide .slide-content {
        transform: translateY(20px);
        opacity: 0;
        transition: transform 600ms cubic-bezier(0.4, 0, 0.2, 1) 200ms, opacity 600ms ease 200ms;
    }
    .slide.active .slide-content {
        transform: translateY(0);
        opacity: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.querySelector('#explore .relative.w-full.h-\\[500px\\]');
        if (!carousel) return;

        const slides = carousel.querySelectorAll('.slide');
        const dots = carousel.querySelectorAll('.carousel-dot');
        const prevBtn = carousel.querySelector('.carousel-prev');
        const nextBtn = carousel.querySelector('.carousel-next');
        
        let currentIndex = 0;
        const totalSlides = slides.length;
        let autoPlayTimer;
        const intervalTime = 6000;

        function updateCarousel(index) {
            if (index === currentIndex || index < 0 || index >= totalSlides) return;

            // Remove active from current
            slides[currentIndex].classList.remove('active');
            dots[currentIndex].classList.remove('w-8', 'bg-white');
            dots[currentIndex].classList.add('w-2', 'bg-white/40');

            currentIndex = index;

            // Add active to new
            slides[currentIndex].classList.add('active');
            dots[currentIndex].classList.remove('w-2', 'bg-white/40');
            dots[currentIndex].classList.add('w-8', 'bg-white');
        }

        function nextSlide() {
            const nextIndex = (currentIndex + 1) % totalSlides;
            updateCarousel(nextIndex);
        }

        function prevSlide() {
            const prevIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateCarousel(prevIndex);
        }

        // Dot clicks
        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                const targetIndex = parseInt(dot.getAttribute('data-index'), 10);
                updateCarousel(targetIndex);
                resetAutoPlay();
            });
        });

        // Arrow navigation
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                resetAutoPlay();
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                resetAutoPlay();
            });
        }

        // Touch/swipe support
        let touchStartX = 0;
        let touchEndX = 0;
        
        carousel.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        carousel.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) nextSlide();
                else prevSlide();
                resetAutoPlay();
            }
        }, { passive: true });

        // Auto-play
        function startAutoPlay() {
            if (totalSlides > 1) {
                autoPlayTimer = setInterval(nextSlide, intervalTime);
            }
        }

        function resetAutoPlay() {
            clearInterval(autoPlayTimer);
            startAutoPlay();
        }

        // Pause on hover
        carousel.addEventListener('mouseenter', () => clearInterval(autoPlayTimer));
        carousel.addEventListener('mouseleave', startAutoPlay);

        startAutoPlay();
    });
</script>