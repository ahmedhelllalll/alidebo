<section class="py-24 relative bg-slate-50 dark:bg-[#030305] text-slate-900 dark:text-[#f5f5f7] overflow-hidden select-none antialiased transition-colors duration-500">
    
    <div class="max-w-[1400px] mx-auto px-6 sm:px-10 mb-10">
        <h2 class="section-label font-black tracking-[6px] uppercase text-[0.7rem] text-slate-400 dark:text-[#4e4e54] flex items-center gap-5">
            {{ __('landing.recent_companies') ?? 'Recently Added' }}
        </h2>
        @if(__('landing.recent_desc'))
            <p class="mt-4 text-slate-500 dark:text-zinc-500 max-w-xl text-sm leading-relaxed font-medium">
                {{ __('landing.recent_desc') }}
            </p>
        @endif
    </div>
    
    <div class="timeline-container max-w-[1400px] mx-auto px-6 sm:px-10 relative">
        <div class="timeline-viewport flex gap-5 overflow-x-auto py-8 pb-12 relative z-10 scrollbar-none snap-x snap-mandatory cursor-grab active:cursor-grabbing" id="timelineViewport">
            
            @foreach($recentCompanies as $company)
            <div class="timeline-strip snap-start shrink-0 flex-0-0-380 h-[280px] rounded-[24px] relative overflow-hidden border border-slate-200 dark:border-white/5 bg-[#0b0b10] transition-all duration-600 will-change-transform">
                
                @if($company->logo)
                    <div class="strip-bg absolute -inset-5 bg-cover bg-center will-change-transform scale-115 transition-transform duration-600" 
                         style="background-image: url('{{ $company->logo_url }}');">
                    </div>
                @else
                    <div class="strip-bg absolute -inset-5 bg-gradient-to-br from-[#16161f] via-[#0b0b10] to-[#030305] flex items-center justify-center will-change-transform scale-115 transition-transform duration-600">
                        <span class="text-white/10 text-7xl font-black tracking-tighter select-none">
                            {{ mb_substr($company->name, 0, 1) }}
                        </span>
                    </div>
                @endif

                <div class="absolute inset-0 bg-gradient-to-t from-[#030305]/95 via-[#030305]/50 to-transparent z-1 transition-opacity duration-600"></div>
                
                @if($company->logo)
                    <div class="hover-logo-capsule absolute top-1/2 left-1/2 w-24 h-24 bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl p-4 flex items-center justify-center pointer-events-none z-2">
                        <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="max-w-full max-h-full object-contain filter drop-shadow-md">
                    </div>
                @else
                    <div class="hover-logo-capsule absolute top-1/2 left-1/2 w-24 h-24 bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl flex items-center justify-center pointer-events-none z-2">
                        <span class="text-white/40 text-3xl font-bold tracking-tight">
                            {{ mb_substr($company->name, 0, 1) }}
                        </span>
                    </div>
                @endif

                <div class="strip-info absolute bottom-8 left-8 right-8 rtl:left-8 rtl:right-8 z-2 transition-transform duration-600 ease-out text-left rtl:text-right">
                    <em class="not-italic text-[0.65rem] text-rose-400 dark:text-[#ff3b30] font-extrabold tracking-[2px] uppercase block mb-2 drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)]">
                        {{ $company->approved_at ? $company->approved_at->diffForHumans() : 'Recently' }}
                    </em>
                    <h4 class="text-[1.35rem] font-semibold text-white leading-[1.3] tracking-tight truncate drop-shadow-[0_2px_10px_rgba(0,0,0,0.6)]">
                        {{ $company->name }}
                    </h4>
                    @if($company->description)
                        <p class="text-xs text-zinc-400 mt-2 line-clamp-2 font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                            {{ $company->description }}
                        </p>
                    @endif
                </div>

                <a href="{{ route('business.view', $company->slug ?? '') }}" class="absolute inset-0 z-10" aria-label="View {{ $company->name }}"></a>
            </div>
            @endforeach

        </div>

        <div class="interactive-track-wrapper max-w-[280px] mx-auto mt-8 flex flex-col items-center gap-[14px]">
            <div class="track-base w-full h-[2px] bg-slate-200 dark:bg-white/5 rounded-full relative">
                <div class="track-progress absolute top-0 h-full w-[25%] bg-gradient-to-r from-rose-500 to-orange-500 dark:from-[#ff3b30] dark:to-[#ff7b00] rounded-full will-change-all transition-all duration-150 ease-out" id="scrollProgress"></div>
            </div>
            <span class="track-label text-[0.6rem] uppercase tracking-[4px] text-slate-400 dark:text-[#3e3e44] font-bold transition-colors duration-300" id="trackLabel">
                {{ __('landing.scroll_explore') ?? 'Scroll to Explore' }}
            </span>
        </div>
    </div>

    <style>
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
        .flex-0-0-380 { flex: 0 0 380px; }
        .duration-600 { transition-duration: 600ms; }
        .scale-115 { transform: scale(1.15); }

        /* Dynamic Section Header Line Rule favoring direction strings natively */
        .section-label::after {
            content: '';
            flex-grow: 1;
            height: 1px;
        }
        :where([dir="ltr"], html:not([dir="rtl"])) .section-label::after {
            background: linear-gradient(90deg, rgb(226, 232, 240), transparent);
        }
        :where([dir="rtl"]) .section-label::after {
            background: linear-gradient(-90deg, rgb(226, 232, 240), transparent);
        }
        .dark :where([dir="ltr"], html:not([dir="rtl"])) .section-label::after {
            background: linear-gradient(90deg, #16161f, transparent);
        }
        .dark :where([dir="rtl"]) .section-label::after {
            background: linear-gradient(-90deg, #16161f, transparent);
        }

        /* Hover Logo Animation Base Config */
        .hover-logo-capsule {
            transform: translate(-50%, -40%) scale(0.85);
            opacity: 0;
            filter: blur(8px);
            will-change: transform, opacity, filter;
            transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.5s ease, filter 0.5s ease;
        }

        /* --- THE MASTER ACCORDION INTERACTION MATRIX --- */
        @media (min-width: 769px) {
            .timeline-viewport:hover .timeline-strip {
                filter: brightness(0.75) grayscale(15%);
            }
            .dark .timeline-viewport:hover .timeline-strip {
                filter: brightness(0.6) grayscale(20%);
            }

            /* Active Accordion Anchor Sizing Profile */
            .timeline-viewport .timeline-strip:hover {
                flex-basis: 480px !important;
                filter: brightness(1) grayscale(0%);
                border-color: rgb(203, 213, 225);
            }
            .dark .timeline-viewport .timeline-strip:hover {
                border-color: rgba(255, 255, 255, 0.08);
            }

            /* Smoothly Trigger the Floating Logo Reveal */
            .timeline-strip:hover .hover-logo-capsule {
                transform: translate(-50%, -60%) scale(1);
                opacity: 1;
                filter: blur(0);
            }

            /* Right Sibling Compress Vector Rule */
            .timeline-strip:hover + .timeline-strip {
                flex-basis: 310px !important;
                filter: brightness(0.65) grayscale(25%);
            }
            .dark .timeline-strip:hover + .timeline-strip {
                filter: brightness(0.4) grayscale(40%);
            }

            /* Left Sibling Functional Structural Rule */
            .timeline-strip:has(+ .timeline-strip:hover) {
                flex-basis: 310px !important;
                filter: brightness(0.65) grayscale(25%);
            }
            .dark .timeline-strip:has(+ .timeline-strip:hover) {
                filter: brightness(0.4) grayscale(40%);
            }

            /* Inner Content Micro-Movements */
            .timeline-strip:hover .strip-bg {
                transform: scale(1.22);
            }
            .timeline-strip:hover .strip-info {
                transform: translateY(-4px);
            }
            .timeline-container:hover .track-label {
                color: rgb(113, 113, 122);
            }
        }

        /* --- ACCORDION BREAKOUT MOBILE SAFE OVERRIDES --- */
        @media (max-width: 768px) {
            .timeline-container { padding: 0 24px; }
            .section-label { padding: 0 24px; margin-bottom: 15px; }
            .timeline-viewport { gap: 14px; padding-bottom: 30px; }
            
            .timeline-viewport:hover .timeline-strip,
            .timeline-strip:hover + .timeline-strip,
            .timeline-strip:has(+ .timeline-strip:hover) {
                flex-basis: calc(100vw - 48px) !important;
                filter: none;
            }
            .timeline-strip {
                width: calc(100vw - 48px);
                flex: 0 0 calc(100vw - 48px) !important;
                height: 240px;
                border-radius: 20px;
            }
            .timeline-strip:hover {
                flex-basis: calc(100vw - 48px) !important;
                transform: none;
            }
            .timeline-strip:hover .hover-logo-capsule {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
                filter: blur(0);
            }
            .strip-info { bottom: 24px; left: 24px; right: 24px; }
            .interactive-track-wrapper { max-width: 180px; }
        }
    </style>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const slider = document.getElementById('timelineViewport');
        const progress = document.getElementById('scrollProgress');
        const trackLabel = document.getElementById('trackLabel');
        const strips = document.querySelectorAll('.timeline-strip');

        if (!slider || !progress || !trackLabel) return;

        let isDown = false;
        let startX, scrollLeft, velocity = 0, lastX = 0, animationFrameId = null;

        // Establish localized language direction vector rule
        const isRtl = document.documentElement.dir === 'rtl' || document.documentElement.style.direction === 'rtl';

        const renderFrameUpdates = () => {
            const currentScroll = Math.abs(slider.scrollLeft);
            const maxScroll = slider.scrollWidth - slider.clientWidth;
            
            if (maxScroll <= 0) return;

            const scrollPercentage = currentScroll / maxScroll;
            const indicatorWidth = 25; 
            const availableSpace = 100 - indicatorWidth;
            const leftPosition = scrollPercentage * availableSpace;
            
            progress.style.width = `${indicatorWidth}%`;
            
            if (isRtl) {
                progress.style.right = `${leftPosition}%`;
                progress.style.left = 'auto';
            } else {
                progress.style.left = `${leftPosition}%`;
                progress.style.right = 'auto';
            }

            // Active Parallax Execution Loop
            strips.forEach((strip) => {
                const bg = strip.querySelector('.strip-bg');
                if (bg) {
                    const stripOffsetLeft = strip.offsetLeft - slider.offsetLeft;
                    const relativeCenter = (stripOffsetLeft - slider.scrollLeft);
                    const parallaxX = relativeCenter * (isRtl ? 0.10 : -0.10); 
                    bg.style.transform = `translateX(${parallaxX}px) scale(1.15)`;
                }
            });
        };

        const applyInertia = () => {
            if (isDown) return;
            if (Math.abs(velocity) > 0.4) {
                slider.scrollLeft += isRtl ? -velocity : velocity;
                velocity *= 0.93; 
                renderFrameUpdates();
                animationFrameId = requestAnimationFrame(applyInertia);
            } else {
                slider.style.scrollSnapType = 'x mandatory';
                trackLabel.style.transform = 'scale(1)';
            }
        };

        // Vertical Mouse Wheel Translation Interceptor
        slider.addEventListener('wheel', (e) => {
            if (Math.abs(e.deltaY) > 0) {
                e.preventDefault();
                cancelAnimationFrame(animationFrameId);
                slider.style.scrollSnapType = 'none';
                slider.scrollLeft += isRtl ? -e.deltaY * 1.2 : e.deltaY * 1.2; 
                renderFrameUpdates();
                
                clearTimeout(slider.wheelTimeout);
                slider.wheelTimeout = setTimeout(() => {
                    slider.style.scrollSnapType = 'x mandatory';
                }, 150);
            }
        }, { passive: false });

        // Dynamic Interactive Card Alignment Targeting
        strips.forEach(strip => {
            strip.addEventListener('click', (e) => {
                if (velocity !== 0) {
                    e.preventDefault();
                } else {
                    slider.scrollTo({
                        left: strip.offsetLeft - slider.offsetLeft,
                        behavior: 'smooth'
                    });
                }
            });
        });

        /* --- FLUID KINETIC DRAG RUNTIME LISTENERS WITH RTL SUPPORT --- */
        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.style.scrollSnapType = 'none';
            cancelAnimationFrame(animationFrameId);
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            lastX = e.pageX;
            velocity = 0;
            trackLabel.style.transform = 'scale(0.95)';
        });

        slider.addEventListener('mouseleave', () => { if (isDown) { isDown = false; applyInertia(); } });
        slider.addEventListener('mouseup', () => { if (isDown) { isDown = false; applyInertia(); } });

        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.4; 
            
            slider.scrollLeft = isRtl ? (scrollLeft + walk) : (scrollLeft - walk);
            
            velocity = lastX - e.pageX;
            lastX = e.pageX;
            renderFrameUpdates();
        });

        slider.addEventListener('scroll', () => { if (!isDown) renderFrameUpdates(); }, { passive: true });
        window.addEventListener('resize', renderFrameUpdates);
        
        renderFrameUpdates();
    });
</script>
@endpush