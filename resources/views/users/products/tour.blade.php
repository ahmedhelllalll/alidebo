@php
    $tourLocale = app()->getLocale();
    $isTourRtl = $tourLocale === 'ar';
    $userId = auth()->id() ?? 0;
    $tourPage = $page ?? 'create';

    $steps = [];
    if ($tourPage === 'create') {
        $steps = [
            [
                'targetId' => '#tour-category-field',
                'title' => __('products.tour_create_step1_title'),
                'desc' => __('products.tour_create_step1_desc'),
                'icon' => 'tag',
                'placement' => 'bottom',
            ],
            [
                'targetId' => '#tour-language-tabs',
                'title' => __('products.tour_create_step2_title'),
                'desc' => __('products.tour_create_step2_desc'),
                'icon' => 'translate',
                'placement' => 'bottom',
            ],
            [
                'targetId' => '#tour-image-upload',
                'title' => __('products.tour_create_step3_title'),
                'desc' => __('products.tour_create_step3_desc'),
                'icon' => 'image',
                'placement' => 'bottom',
            ],
            [
                'targetId' => '#tour-pricing-grid',
                'title' => __('products.tour_create_step4_title'),
                'desc' => __('products.tour_create_step4_desc'),
                'icon' => 'badge-dollar-sign',
                'placement' => 'top',
            ],
            [
                'targetId' => '#tour-submit-section',
                'title' => __('products.tour_create_step5_title'),
                'desc' => __('products.tour_create_step5_desc'),
                'icon' => 'shield-check',
                'placement' => 'top',
            ],
        ];
    } else {
        $steps = [
            [
                'targetId' => '#tour-add-product-btn',
                'title' => __('products.tour_index_step1_title'),
                'desc' => __('products.tour_index_step1_desc'),
                'icon' => 'plus-circle',
                'placement' => 'bottom',
            ],
            [
                'targetId' => '#tour-view-switcher',
                'title' => __('products.tour_index_step2_title'),
                'desc' => __('products.tour_index_step2_desc'),
                'icon' => 'layout-grid',
                'placement' => 'bottom',
            ],
            [
                'targetId' => '#tour-category-filter',
                'title' => __('products.tour_index_step3_title'),
                'desc' => __('products.tour_index_step3_desc'),
                'icon' => 'filter',
                'placement' => 'side',
            ],
            [
                'targetId' => '#tour-status-badges',
                'title' => __('products.tour_index_step4_title'),
                'desc' => __('products.tour_index_step4_desc'),
                'icon' => 'badge-check',
                'placement' => 'side',
            ],
            [
                'targetId' => '#tour-quick-actions',
                'title' => __('products.tour_index_step5_title'),
                'desc' => __('products.tour_index_step5_desc'),
                'icon' => 'sliders',
                'placement' => 'auto',
            ],
        ];
    }

    $user = auth()->user();
    $hasSeenTour = (bool) ($user && $user->products_tour_seen_at !== null);

    $tourConfig = [
        'page' => $tourPage,
        'userId' => (int) $userId,
        'isRtl' => (bool) $isTourRtl,
        'locale' => $tourLocale,
        'hasSeenServer' => $hasSeenTour,
        'seenUrl' => route('products.tour.seen', [], false),
        'labels' => [
            'finish' => __('products.tour_finish'),
            'next' => __('products.tour_next'),
            'back' => __('products.tour_back'),
            'skip' => __('products.tour_skip'),
        ],
        'steps' => $steps,
    ];
@endphp

<div x-data="productsOnboardingTour({{ \Illuminate\Support\Js::from($tourConfig) }})"
    x-cloak
    @keydown.escape.window="if (isOpen) skipTour()"
    @keydown.arrow-right.window="if (isOpen) { isRtl ? prevStep() : nextStep() }"
    @keydown.arrow-left.window="if (isOpen) { isRtl ? nextStep() : prevStep() }"
    class="tour-root">

    {{-- Tour Overlay Backdrop with Cutout Mask for Spotlight Target (Inert on click, outside clicks do not close tour) --}}
    <svg x-show="isOpen"
         x-transition:enter="transition ease-out duration-[400ms]"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 w-full h-full z-[9990] select-none pointer-events-auto"
         style="display: none;">
        <defs>
            <mask id="tour-spotlight-mask" maskUnits="userSpaceOnUse" x="0" y="0" width="100%" height="100%">
                {{-- White fills entire screen to maintain the dimming backdrop --}}
                <rect x="0" y="0" width="100%" height="100%" fill="#ffffff" />
                {{-- Black rectangle punches an exact transparent hole for the spotlight target --}}
                <rect :x="spotlight.visible ? spotlight.left : -9999"
                      :y="spotlight.visible ? spotlight.top : -9999"
                      :width="spotlight.visible ? spotlight.width : 0"
                      :height="spotlight.visible ? spotlight.height : 0"
                      rx="16" ry="16"
                      fill="#000000"
                      class="tour-spotlight-mask-rect" />
            </mask>
        </defs>
        {{-- Background overlay masked by the cutout --}}
        <rect x="0" y="0" width="100%" height="100%"
              fill="rgba(9, 9, 11, 0.78)"
              class="dark:fill-[rgba(0,0,0,0.85)]"
              mask="url(#tour-spotlight-mask)" />
    </svg>

    {{-- Spotlight Frame Highlight --}}
    <div x-show="isOpen && spotlight.visible"
         x-transition:enter="transition ease-out duration-[400ms]"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed z-[9993] pointer-events-none rounded-2xl border-2 border-primary tour-spotlight-frame"
         :style="`top: ${spotlight.top}px; left: ${spotlight.left}px; width: ${spotlight.width}px; height: ${spotlight.height}px;`"
         style="display: none;">
        {{-- Pulsing Corner Accents --}}
        <div class="absolute -inset-1 rounded-2xl border-2 border-primary/40 animate-pulse pointer-events-none"></div>
    </div>

    {{-- Floating Smart Popover Tooltip (Responsive, intelligent above/below/side positioning across mobile and desktop) --}}
    <div x-show="isOpen && spotlight.visible"
         x-transition:enter="transition ease-out duration-[400ms] transform"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed z-[9995] w-[calc(100vw-32px)] sm:w-[380px] max-w-[380px] bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white rounded-2xl border border-zinc-200/90 dark:border-zinc-700/80 shadow-[0_20px_50px_rgba(0,0,0,0.3)] p-5 select-text tour-popover-card"
         :style="popoverStyle"
         style="display: none;">
        
        {{-- Header: Solid Primary Circular Step Number Badge + Close Button --}}
        <div class="flex items-center justify-between gap-3 mb-3.5">
            <span class="w-7 h-7 rounded-full bg-primary text-white text-xs font-black shadow-md shadow-primary/30 ring-2 ring-primary/20 flex items-center justify-center shrink-0">
                <span x-text="stepIndex + 1"></span>
            </span>

            <button type="button" 
                    @click="skipTour()"
                    class="w-7 h-7 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors flex items-center justify-center text-xs"
                    :title="labels.skip || 'Skip'"
                    :aria-label="labels.skip || 'Skip'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Content --}}
        <div class="space-y-1.5 mb-5 text-start">
            <h4 class="text-sm sm:text-base font-[900] text-zinc-900 dark:text-zinc-50 leading-snug tracking-tight"
                x-text="currentStep ? currentStep.title : ''"></h4>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium leading-relaxed"
               x-text="currentStep ? currentStep.desc : ''"></p>
        </div>

        {{-- Footer Controls --}}
        <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
            <button type="button"
                    @click="skipTour()"
                    class="text-xs font-bold text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                    x-text="labels.skip || 'Skip'">
            </button>

            <div class="flex items-center gap-2">
                <template x-if="stepIndex > 0">
                    <button type="button"
                            @click="prevStep()"
                            class="px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-xs font-bold transition-colors"
                            x-text="labels.back || 'Back'">
                    </button>
                </template>

                <button type="button"
                        @click="nextStep()"
                        class="px-4 py-1.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-black shadow-md shadow-primary/20 transition-all flex items-center gap-1.5 active:scale-95">
                    <span x-text="stepIndex === steps.length - 1 ? (labels.finish || 'Finish') : (labels.next || 'Next')"></span>
                    <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.tour-spotlight-active {
    position: relative !important;
    z-index: 9992 !important;
}
[id^="tour-"], .tour-spotlight-active {
    scroll-margin-top: 100px;
    scroll-margin-bottom: 60px;
}
.tour-popover-card {
    transition: top 400ms cubic-bezier(0.16, 1, 0.3, 1),
                left 400ms cubic-bezier(0.16, 1, 0.3, 1),
                opacity 400ms cubic-bezier(0.16, 1, 0.3, 1),
                transform 400ms cubic-bezier(0.16, 1, 0.3, 1);
    will-change: top, left, opacity, transform;
}
.tour-spotlight-frame {
    transition: top 480ms cubic-bezier(0.16, 1, 0.3, 1),
                left 480ms cubic-bezier(0.16, 1, 0.3, 1),
                width 480ms cubic-bezier(0.16, 1, 0.3, 1),
                height 480ms cubic-bezier(0.16, 1, 0.3, 1),
                opacity 400ms cubic-bezier(0.16, 1, 0.3, 1);
    will-change: top, left, width, height;
}
.tour-spotlight-mask-rect {
    transition: x 480ms cubic-bezier(0.16, 1, 0.3, 1),
                y 480ms cubic-bezier(0.16, 1, 0.3, 1),
                width 480ms cubic-bezier(0.16, 1, 0.3, 1),
                height 480ms cubic-bezier(0.16, 1, 0.3, 1);
}
</style>

<script>
function productsOnboardingTour(config) {
    return {
        page: config.page || 'create',
        userId: config.userId || 0,
        isRtl: !!config.isRtl,
        locale: config.locale || 'ar',
        hasSeenServer: !!config.hasSeenServer,
        seenUrl: config.seenUrl || '',
        labels: config.labels || {},
        steps: config.steps || [],
        isOpen: false,
        stepIndex: 0,
        isMobile: window.innerWidth < 768,
        openedDrawerForTour: false,
        popoverStyle: '',
        activeTargetEl: null,
        spotlight: {
            visible: false,
            top: 0,
            left: 0,
            width: 0,
            height: 0
        },

        isScrolling: false,

        get storageKey() {
            return `alidebo_products_tour_seen_${this.userId}`;
        },

        get currentStep() {
            return this.steps[this.stepIndex] || null;
        },

        isMobileFilterDrawerOpen() {
            const indexEl = document.querySelector('[x-data*="productsIndex"]');
            if (indexEl && window.Alpine) {
                const data = window.Alpine.$data(indexEl);
                return !!(data && data.mobileFiltersOpen);
            }
            return false;
        },

        openMobileFilterDrawer() {
            const indexEl = document.querySelector('[x-data*="productsIndex"]');
            if (indexEl && window.Alpine) {
                const data = window.Alpine.$data(indexEl);
                if (data && !data.mobileFiltersOpen) {
                    data.mobileFiltersOpen = true;
                    this.openedDrawerForTour = true;
                    return true;
                }
            }
            const btn = document.querySelector('button[\\@click*="mobileFiltersOpen = true"]');
            if (btn) {
                btn.click();
                this.openedDrawerForTour = true;
                return true;
            }
            return false;
        },

        closeMobileFilterDrawer() {
            const indexEl = document.querySelector('[x-data*="productsIndex"]');
            if (indexEl && window.Alpine) {
                const data = window.Alpine.$data(indexEl);
                if (data && data.mobileFiltersOpen) {
                    data.mobileFiltersOpen = false;
                }
            }
            this.openedDrawerForTour = false;
        },

        init() {
            // Check viewport on resize & scroll
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 768;
                if (this.isOpen && this.activeTargetEl && !this.isScrolling) {
                    this.updateTargetPosition(this.activeTargetEl);
                }
            });

            const onScrollHandler = () => {
                if (this.isOpen && this.activeTargetEl && !this.isScrolling) {
                    this.updateTargetPosition(this.activeTargetEl);
                }
            };

            // Capture scroll on window, document, and main container
            window.addEventListener('scroll', onScrollHandler, { capture: true, passive: true });
            document.addEventListener('scroll', onScrollHandler, { capture: true, passive: true });
            const mainContainer = document.querySelector('main');
            if (mainContainer) {
                mainContainer.addEventListener('scroll', onScrollHandler, { passive: true });
            }

            // Listen for manual re-trigger event
            window.addEventListener('open-products-tour', () => {
                this.startTour();
            });

            // Auto-trigger on first visit (only once per vendor account, verified against database)
            const hasSeen = this.hasSeenServer || (localStorage.getItem(this.storageKey) === 'true');
            if (!hasSeen) {
                // Wait slightly for page layout to finish rendering
                setTimeout(() => {
                    this.startTour();
                }, 800);
            }
        },

        resolveTargetElement(selector) {
            if (selector === '#tour-quick-actions') {
                // Check visible quick actions target across Row View and Card View
                const rowTarget = document.getElementById('tour-quick-actions-row');
                if (rowTarget && rowTarget.offsetParent !== null) return rowTarget;
                const cardTarget = document.getElementById('tour-quick-actions');
                if (cardTarget && cardTarget.offsetParent !== null) return cardTarget;
                return rowTarget || cardTarget || document.querySelector('.tour-quick-actions-target');
            }
            if (selector === '#tour-category-filter' || selector === '#tour-filters-sidebar') {
                const isMobileScreen = window.innerWidth < 1024 || !document.getElementById('tour-filters-sidebar')?.offsetParent;
                if (isMobileScreen) {
                    const mobileCat = document.getElementById('tour-category-filter-mobile');
                    if (mobileCat && mobileCat.offsetParent !== null) return mobileCat;
                    const drawer = document.getElementById('tour-filters-drawer');
                    if (drawer && drawer.offsetParent !== null) return drawer;
                    return mobileCat || drawer;
                }
                const desktopCat = document.getElementById('tour-category-filter');
                if (desktopCat && desktopCat.offsetParent !== null) return desktopCat;
                const desktopSidebar = document.getElementById('tour-filters-sidebar');
                if (desktopSidebar && desktopSidebar.offsetParent !== null) return desktopSidebar;
                return desktopCat || desktopSidebar;
            }
            if (selector === '#tour-status-badges') {
                const isMobileScreen = window.innerWidth < 1024 || !document.getElementById('tour-status-badges')?.offsetParent;
                if (isMobileScreen) {
                    const mobileStatus = document.getElementById('tour-status-badges-mobile');
                    if (mobileStatus && mobileStatus.offsetParent !== null) return mobileStatus;
                }
                return document.getElementById('tour-status-badges');
            }
            return document.querySelector(selector);
        },

        startTour() {
            if (!this.steps || this.steps.length === 0) return;
            this.stepIndex = 0;
            this.isOpen = true;
            window.__alideboTourActive = true;
            this.$nextTick(() => {
                this.positionStep();
            });
        },

        nextStep() {
            if (this.stepIndex < this.steps.length - 1) {
                this.stepIndex++;
                this.positionStep();
            } else {
                this.finishTour();
            }
        },

        prevStep() {
            if (this.stepIndex > 0) {
                this.stepIndex--;
                this.positionStep();
            }
        },

        markAsSeen() {
            try {
                localStorage.setItem(this.storageKey, 'true');
            } catch (e) {}

            this.hasSeenServer = true;

            if (this.seenUrl) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch(this.seenUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }
                }).catch(() => {});
            }
        },

        finishTour() {
            this.isOpen = false;
            window.__alideboTourActive = false;
            this.cleanupActiveTarget();
            if (this.openedDrawerForTour) {
                this.closeMobileFilterDrawer();
            }
            this.markAsSeen();
        },

        skipTour() {
            this.isOpen = false;
            window.__alideboTourActive = false;
            this.cleanupActiveTarget();
            if (this.openedDrawerForTour) {
                this.closeMobileFilterDrawer();
            }
            this.markAsSeen();
        },

        cleanupActiveTarget() {
            if (this.activeTargetEl) {
                this.activeTargetEl.classList.remove('tour-spotlight-active');
                this.activeTargetEl = null;
            }
            this.spotlight.visible = false;
        },

        getScrollContainer(targetEl = null) { // getScrollContainer()
            if (targetEl) {
                const drawer = targetEl.closest('#tour-filters-drawer');
                if (drawer) {
                    return drawer;
                }
            }
            const main = document.querySelector('main');
            if (main && (main.scrollHeight > main.clientHeight)) {
                return main;
            }
            return window;
        },

        getHeaderHeight() {
            const header = document.querySelector('header');
            if (header) {
                const rect = header.getBoundingClientRect();
                return Math.max(64, rect.bottom);
            }
            return 64;
        },

        getCombinedBoundingRect(targetEl) {
            if (!targetEl) return { left: 0, right: 0, top: 0, bottom: 0, width: 0, height: 0 };
            
            const baseRect = targetEl.getBoundingClientRect();
            let minLeft = baseRect.left;
            let maxRight = baseRect.right;
            let minTop = baseRect.top;
            let maxBottom = baseRect.bottom;

            // Direct children and interactive elements (buttons, links, inputs, selects, etc.)
            const candidates = [
                ...(targetEl.children ? Array.from(targetEl.children) : []),
                ...(targetEl.querySelectorAll ? Array.from(targetEl.querySelectorAll('button, a, input, select, [role="button"]')) : [])
            ];

            candidates.forEach(child => {
                const cr = child.getBoundingClientRect();
                if (cr.width > 0 && cr.height > 0) {
                    minLeft = Math.min(minLeft, cr.left);
                    maxRight = Math.max(maxRight, cr.right);
                    minTop = Math.min(minTop, cr.top);
                    maxBottom = Math.max(maxBottom, cr.bottom);
                }
            });

            return {
                left: minLeft,
                right: maxRight,
                top: minTop,
                bottom: maxBottom,
                width: maxRight - minLeft,
                height: maxBottom - minTop
            };
        },

        scrollToTarget(targetEl, onComplete) {
            const container = this.getScrollContainer(targetEl);
            const headerHeight = this.getHeaderHeight();
            const targetRect = this.getCombinedBoundingRect(targetEl);

            // Handle scrolling inside the mobile slide-over filter drawer
            const isInsideDrawer = container && container !== window && (container.id === 'tour-filters-drawer' || container.closest('#tour-filters-drawer'));
            if (isInsideDrawer) {
                const containerRect = container.getBoundingClientRect();
                const relativeTop = targetRect.top - containerRect.top;
                const idealTop = (container.clientHeight / 2) - (targetRect.height / 2);
                const delta = relativeTop - idealTop;
                if (Math.abs(delta) > 15) {
                    container.scrollBy({ top: delta, behavior: 'smooth' });
                    setTimeout(() => {
                        if (onComplete) onComplete();
                    }, 300);
                } else {
                    if (onComplete) onComplete();
                }
                return;
            }

            // Usable vertical space below the sticky header
            const usableHeight = window.innerHeight - headerHeight;
            const safeTop = headerHeight + 28;
            const safeBottom = window.innerHeight - 24;

            const isMobile = this.isMobile || window.innerWidth < 768;
            const isQuickActions = targetEl.id === 'tour-quick-actions-row' || 
                                   targetEl.id === 'tour-quick-actions' || 
                                   !!targetEl.closest('#tour-quick-actions, #tour-quick-actions-row');

            let deltaY = 0;
            if (isMobile && isQuickActions && !isInsideDrawer) {
                // On mobile, Quick Actions card sits at the bottom like a bottom-sheet.
                // Auto-scroll the page so the spotlighted action buttons sit in the visible area ABOVE the bottom-sheet card.
                const cardEl = document.querySelector('.tour-popover-card');
                const popoverHeight = cardEl && cardEl.offsetHeight > 0 ? cardEl.offsetHeight : 220;
                const visibleTop = headerHeight + 16;
                const visibleBottom = window.innerHeight - popoverHeight - 20;
                const idealTargetCenter = visibleTop + Math.max(40, (visibleBottom - visibleTop) / 2);
                const currentCenterY = targetRect.top + (targetRect.height / 2);
                deltaY = currentCenterY - idealTargetCenter;
            } else if (targetRect.height > usableHeight - 40) {
                // Tall targets (like filter sidebar): align top to safeTop with clean clearance
                deltaY = targetRect.top - safeTop;
            } else {
                // Standard targets: center within usable viewport area below sticky header
                const idealCenterY = headerHeight + (usableHeight / 2);
                const currentCenterY = targetRect.top + (targetRect.height / 2);
                deltaY = currentCenterY - idealCenterY;
            }

            // Check if already comfortably visible
            const targetSafeBottom = (isMobile && isQuickActions && !isInsideDrawer) 
                ? (window.innerHeight - 240) 
                : safeBottom;
            const isComfortablyVisible = (targetRect.top >= safeTop && targetRect.bottom <= targetSafeBottom);
            if (isComfortablyVisible && Math.abs(deltaY) < 20) {
                if (onComplete) onComplete();
                return;
            }

            this.isScrolling = true;
            if (container === window) {
                window.scrollBy({ top: deltaY, behavior: 'smooth' });
            } else {
                container.scrollBy({ top: deltaY, behavior: 'smooth' });
            }

            let prevTop = targetEl.getBoundingClientRect().top;
            let stableFrames = 0;
            const startTime = performance.now();
            let rafId = null;
            let fallbackTimer = null;
            let finished = false;

            const finish = () => {
                if (finished) return;
                finished = true;
                if (rafId) cancelAnimationFrame(rafId);
                if (fallbackTimer) clearTimeout(fallbackTimer);

                // Verification check: ensure target is not still clipped or too close to header
                const finalRect = this.getCombinedBoundingRect(targetEl);
                if (finalRect.top < safeTop) {
                    const adjust = safeTop - finalRect.top + 16;
                    if (container === window) {
                        window.scrollBy({ top: -adjust, behavior: 'instant' });
                    } else {
                        container.scrollTop -= adjust;
                    }
                }

                if (this.isOpen && this.activeTargetEl === targetEl) {
                    this.updateTargetPosition(targetEl);
                }
                this.isScrolling = false;
                if (onComplete) onComplete();
            };

            const pollScroll = (currentTime) => {
                if (finished) return;
                const currentTop = targetEl.getBoundingClientRect().top;
                const elapsed = currentTime - startTime;

                if (this.isOpen && this.activeTargetEl === targetEl) {
                    this.updateTargetPosition(targetEl);
                }

                // Wait at least 120ms to allow smooth scroll momentum to start
                if (elapsed >= 120) {
                    if (Math.abs(currentTop - prevTop) < 0.5) {
                        stableFrames++;
                    } else {
                        stableFrames = 0;
                    }

                    // Stabilized across 3 consecutive frames
                    if (stableFrames >= 3) {
                        finish();
                        return;
                    }
                }

                prevTop = currentTop;
                rafId = requestAnimationFrame(pollScroll);
            };

            rafId = requestAnimationFrame(pollScroll);
            fallbackTimer = setTimeout(finish, 650);
        },

        updateTargetPosition(targetEl) {
            const rect = this.getCombinedBoundingRect(targetEl);
            const pad = 10;
            const headerHeight = this.getHeaderHeight();
            const minSafeTop = headerHeight + 8;

            if (rect.bottom <= headerHeight) {
                this.spotlight.visible = false;
                return;
            }

            // Update spotlight cutout coordinates with header clamping
            this.spotlight = {
                visible: true,
                top: Math.max(minSafeTop, Math.round(rect.top - pad)),
                left: Math.max(8, Math.round(rect.left - pad)),
                width: Math.round(rect.width + (pad * 2)),
                height: Math.round(rect.height + (pad * 2))
            };

            const cardEl = document.querySelector('.tour-popover-card');
            const popoverWidth = cardEl && cardEl.offsetWidth > 0 ? cardEl.offsetWidth : Math.min(380, window.innerWidth - 32);
            const popoverHeight = cardEl && cardEl.offsetHeight > 0 ? cardEl.offsetHeight : 220;

            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top - headerHeight;
            const spaceLeft = rect.left;
            const spaceRight = window.innerWidth - rect.right;

            // Check if target is inside mobile filter drawer
            const isInsideDrawer = !!targetEl.closest('#tour-filters-drawer, [x-show="mobileFiltersOpen"]');

            // Check if target is tall (e.g. sidebar with height > 240px)
            const isTallTarget = rect.height > 240;

            const isMobile = this.isMobile || window.innerWidth < 768;
            // Check if target is row action buttons (small button cluster at row edge)
            const isRowActions = targetEl.id === 'tour-quick-actions-row' || !!targetEl.closest('#tour-quick-actions-row');
            const isQuickActions = isRowActions || targetEl.id === 'tour-quick-actions' || !!targetEl.closest('#tour-quick-actions');

            // Check if target has dense content / sibling rows directly below
            const parentRow = targetEl.closest('.group, tr, [class*="product-row"], [class*="product-card"], .space-y-3\\.5 > div');
            const hasDenseContentBelow = !!(parentRow && parentRow.nextElementSibling);

            // Determine step preferred placement
            const step = this.currentStep;
            let preferred = (step && step.placement) ? step.placement : 'auto';

            let topPos = 0;
            let leftPos = 0;

            if (isMobile && isQuickActions && !isInsideDrawer) {
                // On mobile, Quick Actions uses a dedicated mobile bottom-sheet card position,
                // while the target is auto-scrolled into the clear area above it.
                topPos = window.innerHeight - popoverHeight - 16;
                leftPos = Math.max(16, Math.round((window.innerWidth - popoverWidth) / 2));
            } else if (isRowActions && !isMobile) {
                // In Row View on Desktop, action buttons sit on the outer edge of the row.
                // Placing the card to the inner side floats it cleanly over the spacious row.
                if (this.isRtl) {
                    leftPos = rect.right + 20;
                } else {
                    leftPos = rect.left - popoverWidth - 20;
                }
                topPos = rect.top + (rect.height / 2) - (popoverHeight / 2);
            } else if (isInsideDrawer) {
                // Inside mobile drawer: drawer width is ~320px, horizontal side placement is impossible.
                // Intelligently flip ABOVE or BELOW based on available space!
                const fitsAbove = spaceAbove >= popoverHeight + 16;
                const fitsBelow = spaceBelow >= popoverHeight + 16;

                if (fitsBelow && (spaceBelow >= spaceAbove || !fitsAbove)) {
                    topPos = rect.bottom + 16;
                } else if (fitsAbove) {
                    topPos = rect.top - popoverHeight - 16;
                } else if (spaceAbove >= spaceBelow) {
                    topPos = rect.top - popoverHeight - 16;
                } else {
                    topPos = rect.bottom + 16;
                }
                leftPos = rect.left + (rect.width / 2) - (popoverWidth / 2);
            } else if (preferred === 'side' && !isInsideDrawer) {
                // Beside placement: check if side clearance genuinely exists
                const canFitSideRight = spaceRight >= popoverWidth + 24;
                const canFitSideLeft = spaceLeft >= popoverWidth + 24;

                if (this.isRtl && canFitSideLeft) {
                    leftPos = rect.left - popoverWidth - 24;
                    topPos = Math.max(headerHeight + 16, Math.min(window.innerHeight - popoverHeight - 20, rect.top));
                } else if (!this.isRtl && canFitSideRight) {
                    leftPos = rect.right + 24;
                    topPos = Math.max(headerHeight + 16, Math.min(window.innerHeight - popoverHeight - 20, rect.top));
                } else if (canFitSideRight) {
                    leftPos = rect.right + 24;
                    topPos = Math.max(headerHeight + 16, Math.min(window.innerHeight - popoverHeight - 20, rect.top));
                } else if (canFitSideLeft) {
                    leftPos = rect.left - popoverWidth - 24;
                    topPos = Math.max(headerHeight + 16, Math.min(window.innerHeight - popoverHeight - 20, rect.top));
                } else {
                    // No side space: intelligently flip ABOVE or BELOW
                    const fitsAbove = spaceAbove >= popoverHeight + 16;
                    const fitsBelow = spaceBelow >= popoverHeight + 16;
                    if (fitsBelow && (spaceBelow >= spaceAbove || !fitsAbove)) {
                        topPos = rect.bottom + 16;
                    } else {
                        topPos = rect.top - popoverHeight - 16;
                    }
                    leftPos = rect.left + (rect.width / 2) - (popoverWidth / 2);
                }
            } else if (preferred === 'top') {
                // Preferred TOP: place above unless space is cramped above and fits below
                const fitsAbove = spaceAbove >= popoverHeight + 16;
                const fitsBelow = spaceBelow >= popoverHeight + 16;
                if (fitsAbove && (spaceAbove >= spaceBelow || !fitsBelow)) {
                    topPos = rect.top - popoverHeight - 16;
                } else if (fitsBelow) {
                    topPos = rect.bottom + 16;
                } else if (spaceAbove >= spaceBelow) {
                    topPos = rect.top - popoverHeight - 16;
                } else {
                    topPos = rect.bottom + 16;
                }
                leftPos = rect.left + (rect.width / 2) - (popoverWidth / 2);
            } else if (preferred === 'bottom') {
                // Preferred BOTTOM: place below unless space is cramped below and fits above
                const fitsAbove = spaceAbove >= popoverHeight + 16;
                const fitsBelow = spaceBelow >= popoverHeight + 16;
                if (fitsBelow && (spaceBelow >= spaceAbove || !fitsAbove)) {
                    topPos = rect.bottom + 16;
                } else if (fitsAbove) {
                    topPos = rect.top - popoverHeight - 16;
                } else if (spaceBelow >= spaceAbove) {
                    topPos = rect.bottom + 16;
                } else {
                    topPos = rect.top - popoverHeight - 16;
                }
                leftPos = rect.left + (rect.width / 2) - (popoverWidth / 2);
            } else {
                // AUTO placement: intelligently pick whichever side has more comfortable clearance
                const fitsAbove = spaceAbove >= popoverHeight + 16;
                const fitsBelow = spaceBelow >= popoverHeight + 16;
                if (fitsBelow && !hasDenseContentBelow && (spaceBelow >= spaceAbove || !fitsAbove)) {
                    topPos = rect.bottom + 16;
                } else if (fitsAbove) {
                    topPos = rect.top - popoverHeight - 16;
                } else if (spaceBelow > spaceAbove) {
                    topPos = rect.bottom + 16;
                } else {
                    topPos = rect.top - popoverHeight - 16;
                }
                leftPos = rect.left + (rect.width / 2) - (popoverWidth / 2);
            }

            // Viewport boundary clamping with safe margins (never behind sticky header or off-screen)
            leftPos = Math.max(16, Math.min(window.innerWidth - popoverWidth - 16, leftPos));
            topPos = Math.max(headerHeight + 12, Math.min(window.innerHeight - popoverHeight - 16, topPos));

            // Collision safeguard against the target itself
            const cardRight = leftPos + popoverWidth;
            const cardBottom = topPos + popoverHeight;
            const overlapsTarget = !(cardRight < rect.left || leftPos > rect.right || cardBottom < rect.top || topPos > rect.bottom);

            if (overlapsTarget && (!isRowActions || isMobile)) {
                if (spaceAbove >= popoverHeight + 16 || spaceAbove > spaceBelow) {
                    topPos = rect.top - popoverHeight - 16;
                } else {
                    topPos = rect.bottom + 16;
                }
                leftPos = Math.max(16, Math.min(window.innerWidth - popoverWidth - 16, leftPos));
                topPos = Math.max(headerHeight + 12, Math.min(window.innerHeight - popoverHeight - 16, topPos));
            }

            this.popoverStyle = `top: ${Math.round(topPos)}px; left: ${Math.round(leftPos)}px;`;
        },

        async positionStep() {
            this.isMobile = window.innerWidth < 768;
            const step = this.currentStep;
            if (!step || !step.targetId) return;

            // Check if this step targets the filter sidebar / category filter on mobile/tablet
            const isFilterTarget = step.targetId === '#tour-category-filter' || 
                                   step.targetId === '#tour-filters-sidebar' || 
                                   step.targetId === '#tour-status-badges';

            const isMobileScreen = window.innerWidth < 1024 || !document.getElementById('tour-filters-sidebar')?.offsetParent;

            if (isFilterTarget && isMobileScreen) {
                // Ensure the mobile filter drawer is open
                if (!this.isMobileFilterDrawerOpen()) {
                    this.openMobileFilterDrawer();
                    // Wait for the slide-over transition to complete (380ms)
                    await new Promise(resolve => setTimeout(resolve, 420));
                }
            } else if (this.openedDrawerForTour && !isFilterTarget) {
                // If drawer was opened specifically for the tour, close it when leaving filter steps
                this.closeMobileFilterDrawer();
                await new Promise(resolve => setTimeout(resolve, 420));
            }

            const targetEl = this.resolveTargetElement(step.targetId);
            if (!targetEl) {
                this.cleanupActiveTarget();
                return;
            }

            // Manage active target element styling
            if (this.activeTargetEl && this.activeTargetEl !== targetEl) {
                this.activeTargetEl.classList.remove('tour-spotlight-active');
            }
            targetEl.classList.add('tour-spotlight-active');
            this.activeTargetEl = targetEl;

            // Smoothly update position without jarringly hiding the spotlight
            this.updateTargetPosition(targetEl);
            this.spotlight.visible = true;

            this.scrollToTarget(targetEl, () => {
                if (this.isOpen && this.activeTargetEl === targetEl) {
                    this.updateTargetPosition(targetEl);
                    this.spotlight.visible = true;
                }
            });
        }
    };
}
</script>
