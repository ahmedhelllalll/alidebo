{{-- 
    Reusable Airbnb-Style Animated Photo Gallery Lightbox Component
    Features:
    - Adaptive layout: Masonry/editorial grid for 4+ images, centered carousel for < 4 images
    - Click any photo in grid to enter focused high-res zoom carousel with prev/next navigation
    - Shared-element morph animation using origin bounding rect + CSS transitions
    - Keyboard navigation (Escape, Left/Right arrows with RTL mirroring)
    - Touch swipe gesture support on mobile
    - Full Dark Mode, RTL/LTR support, and responsive design
--}}
@props([
    'id' => 'gallery-lightbox',
])

@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div x-data="photoGalleryLightbox()"
     x-cloak
     x-show="isOpen"
     id="{{ $id }}"
     role="dialog"
     aria-modal="true"
     aria-label="{{ __('products.photo_gallery') }}"
     data-lenis-prevent
     @open-photo-gallery.window="openGallery($event.detail)"
     @keydown.escape.window="handleEscape()"
     @keydown.arrow-right.window="handleArrowRight()"
     @keydown.arrow-left.window="handleArrowLeft()"
     class="fixed inset-0 z-[100000] select-none"
     style="display: none;">

    {{-- Backdrop with Ambient Blur --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-350"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-2xl"
         x-transition:leave="transition ease-in duration-250"
         x-transition:leave-start="opacity-100 backdrop-blur-2xl"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         @click="closeGallery()"
         class="absolute inset-0 bg-zinc-950/90 dark:bg-black/95 backdrop-blur-2xl">
    </div>

    {{-- Shared-Element Morph Layer (Active during opening transition) --}}
    <div x-show="isMorphing && morphStyles.width"
         class="fixed pointer-events-none z-[100010] rounded-2xl overflow-hidden shadow-2xl transition-all duration-350 ease-[cubic-bezier(0.16,1,0.3,1)]"
         :style="morphStyles">
        <img :src="currentImage?.url" class="w-full h-full object-cover">
    </div>

    {{-- Main Gallery Container --}}
    <div x-show="isOpen && !isMorphing"
         x-transition:enter="transition ease-out duration-350 transform"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         data-lenis-prevent
         class="relative z-[100005] w-full h-full max-h-screen flex flex-col overflow-hidden text-zinc-100"
         :dir="isRtl ? 'rtl' : 'ltr'">

        {{-- ── Top Navigation Bar ── --}}
        <header class="w-full shrink-0 px-4 sm:px-8 py-3.5 flex items-center justify-between border-b border-white/10 bg-zinc-950/60 dark:bg-black/70 backdrop-blur-xl z-20">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Back to Grid Button (When inside focused zoom view in 4+ mode) --}}
                <template x-if="images.length >= 4 && currentView === 'focus'">
                    <button type="button"
                            @click="switchView('grid')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/15 transition-all hover:scale-105 active:scale-95 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span>{{ __('products.all_photos') }}</span>
                    </button>
                </template>

                {{-- Counter or Product Title --}}
                <div class="flex items-center gap-2 truncate">
                    <span class="text-xs sm:text-sm font-black text-white/90 tracking-wider whitespace-nowrap">
                        <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                    </span>
                    <span class="text-white/40 font-light hidden sm:inline">|</span>
                    <h4 class="text-xs sm:text-sm font-bold text-white/80 truncate hidden sm:inline" x-text="productTitle"></h4>
                </div>

                {{-- Cover Image Badge --}}
                <template x-if="currentImage?.is_primary">
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-primary text-white shadow-sm">
                        {{ __('products.cover_image') }}
                    </span>
                </template>
            </div>

            {{-- Close (X) Button --}}
            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="closeGallery()"
                        aria-label="{{ __('products.close_gallery') }}"
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white/90 hover:text-white border border-white/15 flex items-center justify-center transition-all hover:scale-105 active:scale-95 shadow-lg backdrop-blur-md cursor-pointer"
                        title="{{ __('products.close_gallery') }} (Esc)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </header>

        {{-- ── CONTENT AREA: Adaptive Layouts ── --}}
        
        {{-- 1. MASONRY / EDITORIAL GRID VIEW (Active when images >= 4 and not in focus mode) --}}
        <template x-if="images.length >= 4 && currentView === 'grid'">
            <div class="flex-1 min-h-0 w-full overflow-y-auto overscroll-contain px-4 sm:px-8 lg:px-16 py-6 sm:py-10 custom-scrollbar"
                 data-lenis-prevent
                 style="-webkit-overflow-scrolling: touch; touch-action: pan-y; overflow-y: auto !important;"
                 @click.self="closeGallery()">
                <div class="max-w-6xl mx-auto space-y-6 pb-12">
                    {{-- Section Title & Subtitle --}}
                    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 pb-4 border-b border-white/10">
                        <div>
                            <h3 class="text-lg sm:text-2xl font-black text-white tracking-tight" x-text="productTitle || '{{ __('products.photo_gallery') }}'"></h3>
                            <p class="text-xs text-white/60 font-medium mt-1">
                                {{ __('products.show_all_photos') }}
                                <span class="font-bold text-white/90">(<span x-text="images.length"></span>)</span>
                            </p>
                        </div>
                        <span class="text-[11px] font-bold text-white/50">
                            {{ __('products.view_mode_preview') }}
                        </span>
                    </div>

                    {{-- Airbnb-Style Editorial / Masonry Layout --}}
                    {{-- Hero Top Featured Photo (First Image) --}}
                    <template x-if="images[0]">
                        <div class="group relative rounded-3xl overflow-hidden border border-white/10 bg-zinc-900 aspect-[16/9] sm:aspect-[21/9] cursor-pointer shadow-2xl transition-all duration-300 hover:border-primary/50"
                             @click="openFocusedImage(0, $event)">
                            <img :src="images[0].url" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-between p-4 sm:p-6">
                                <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-white/20 backdrop-blur-md text-white border border-white/20">
                                    {{ __('products.cover_image') }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-primary text-white shadow-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    1 / <span x-text="images.length"></span>
                                </span>
                            </div>
                        </div>
                    </template>

                    {{-- 2-Column Balanced Masonry Grid for Remaining Images --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <template x-for="(img, idx) in images.slice(1)" :key="img.id || idx">
                            <div class="group relative rounded-2xl sm:rounded-3xl overflow-hidden border border-white/10 bg-zinc-900 aspect-[4/3] cursor-pointer shadow-xl transition-all duration-300 hover:border-primary/50"
                                 @click="openFocusedImage(idx + 1, $event)">
                                <img :src="img.url" 
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-between p-4">
                                    <span class="text-xs font-bold text-white/90">
                                        <span x-text="idx + 2"></span> / <span x-text="images.length"></span>
                                    </span>
                                    <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center border border-white/20 shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        {{-- 2. CENTERED CAROUSEL VIEW (Active when images < 4 OR user zoomed into photo in 4+ mode) --}}
        <template x-if="images.length < 4 || currentView === 'focus'">
            <div class="relative flex-1 w-full flex items-center justify-center px-4 sm:px-12 py-4 min-h-0"
                 @touchstart="handleTouchStart($event)"
                 @touchend="handleTouchEnd($event)">
                
                {{-- Previous Button --}}
                <template x-if="images.length > 1">
                    <button type="button" 
                            @click="prevImage()"
                            aria-label="Previous image"
                            class="absolute start-3 sm:start-8 z-30 w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-zinc-900/80 hover:bg-zinc-800 text-white/90 hover:text-white border border-white/15 shadow-2xl flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 backdrop-blur-xl">
                        <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                </template>

                {{-- Centered High-Resolution Image Display --}}
                <div class="relative w-full h-full flex items-center justify-center">
                    <template x-if="currentImage">
                        <img :src="currentImage.url" 
                             :key="currentIndex"
                             class="max-h-[75vh] sm:max-h-[82vh] max-w-[90vw] object-contain rounded-2xl sm:rounded-3xl shadow-[0_25px_60px_rgba(0,0,0,0.7)] border border-white/10 transition-all duration-300 ease-out">
                    </template>
                </div>

                {{-- Next Button --}}
                <template x-if="images.length > 1">
                    <button type="button" 
                            @click="nextImage()"
                            aria-label="Next image"
                            class="absolute end-3 sm:end-8 z-30 w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-zinc-900/80 hover:bg-zinc-800 text-white/90 hover:text-white border border-white/15 shadow-2xl flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 backdrop-blur-xl">
                        <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </template>
            </div>
        </template>

        {{-- ── Bottom Thumbnails Strip (When in carousel / focus mode) ── --}}
        <template x-if="(images.length < 4 || currentView === 'focus') && images.length > 1">
            <footer class="w-full shrink-0 px-4 py-3 border-t border-white/10 bg-zinc-950/60 dark:bg-black/70 backdrop-blur-xl z-20">
                <div class="max-w-4xl mx-auto flex items-center justify-center gap-2.5 overflow-x-auto light-scrollbar pb-1">
                    <template x-for="(img, idx) in images" :key="img.id || idx">
                        <button type="button" 
                                @click="currentIndex = idx"
                                class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden border-2 shrink-0 transition-all duration-200 cursor-pointer shadow-md"
                                :class="currentIndex === idx ? 'border-primary ring-2 ring-primary/40 scale-105 opacity-100' : 'border-white/20 opacity-50 hover:opacity-100'">
                            <img :src="img.url" class="w-full h-full object-cover">
                            <template x-if="img.is_primary">
                                <span class="absolute top-1 start-1 w-2 h-2 rounded-full bg-primary ring-1 ring-white"></span>
                            </template>
                        </button>
                    </template>
                </div>
            </footer>
        </template>
    </div>
</div>

<script>
function photoGalleryLightbox() {
    return {
        isOpen: false,
        isMorphing: false,
        isRtl: {{ $isRtl ? 'true' : 'false' }},
        productTitle: '',
        images: [],
        currentIndex: 0,
        currentView: 'grid', // 'grid' | 'focus'
        morphStyles: {},
        touchStartX: 0,
        touchEndX: 0,

        get currentImage() {
            return this.images[this.currentIndex] || null;
        },

        openGallery(detail) {
            if (!detail || !detail.images || detail.images.length === 0) return;

            this.images = detail.images;
            this.currentIndex = detail.startIndex || 0;
            this.productTitle = detail.title || '';
            this.isRtl = detail.isRtl !== undefined ? detail.isRtl : this.isRtl;

            // Layout rule: 4+ images start in 'grid' layout (unless explicitly requested focus), < 4 start in 'focus' carousel
            this.currentView = this.images.length >= 4 ? (detail.view || 'grid') : 'focus';

            // Lock body scroll and pause Lenis smooth scroll if present
            document.body.style.overflow = 'hidden';
            if (typeof window.lenis !== 'undefined' && window.lenis && typeof window.lenis.stop === 'function') {
                window.lenis.stop();
            }

            // Shared-Element Morph Animation: if originRect is passed
            if (detail.originRect) {
                const r = detail.originRect;
                this.morphStyles = {
                    top: `${r.top}px`,
                    left: `${r.left}px`,
                    width: `${r.width}px`,
                    height: `${r.height}px`,
                    opacity: '1'
                };
                this.isMorphing = true;
                this.isOpen = true;

                // Fluid expansion to center screen
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        const targetW = Math.min(window.innerWidth * 0.9, 1000);
                        const targetH = Math.min(window.innerHeight * 0.75, 700);
                        const targetL = (window.innerWidth - targetW) / 2;
                        const targetT = (window.innerHeight - targetH) / 2;

                        this.morphStyles = {
                            top: `${targetT}px`,
                            left: `${targetL}px`,
                            width: `${targetW}px`,
                            height: `${targetH}px`,
                            opacity: '1'
                        };

                        setTimeout(() => {
                            this.isMorphing = false;
                        }, 350);
                    }, 20);
                });
            } else {
                this.isMorphing = false;
                this.isOpen = true;
            }
        },

        closeGallery() {
            this.isOpen = false;
            this.isMorphing = false;

            // Only restore body overflow and restart Lenis if detailModal is not open
            const isDetailModalOpen = document.querySelector('[x-show*="detailModal.open"]:not([style*="display: none"])');
            if (!isDetailModalOpen) {
                document.body.style.overflow = '';
                if (typeof window.lenis !== 'undefined' && window.lenis && typeof window.lenis.start === 'function') {
                    window.lenis.start();
                }
            }
        },

        handleEscape() {
            if (this.isOpen) {
                if (this.images.length >= 4 && this.currentView === 'focus') {
                    this.switchView('grid');
                } else {
                    this.closeGallery();
                }
            }
        },

        switchView(view) {
            this.currentView = view;
        },

        openFocusedImage(index, event) {
            this.currentIndex = index;
            this.currentView = 'focus';
        },

        nextImage() {
            if (this.images.length <= 1) return;
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },

        prevImage() {
            if (this.images.length <= 1) return;
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        },

        handleArrowRight() {
            if (!this.isOpen) return;
            if (this.currentView === 'focus' || this.images.length < 4) {
                if (this.isRtl) {
                    this.prevImage();
                } else {
                    this.nextImage();
                }
            }
        },

        handleArrowLeft() {
            if (!this.isOpen) return;
            if (this.currentView === 'focus' || this.images.length < 4) {
                if (this.isRtl) {
                    this.nextImage();
                } else {
                    this.prevImage();
                }
            }
        },

        handleTouchStart(e) {
            if (e.touches && e.touches[0]) {
                this.touchStartX = e.touches[0].clientX;
            }
        },

        handleTouchEnd(e) {
            if (e.changedTouches && e.changedTouches[0]) {
                this.touchEndX = e.changedTouches[0].clientX;
                const diff = this.touchEndX - this.touchStartX;
                if (Math.abs(diff) > 40) {
                    if (diff > 0) {
                        // Swiped right
                        this.isRtl ? this.nextImage() : this.prevImage();
                    } else {
                        // Swiped left
                        this.isRtl ? this.prevImage() : this.nextImage();
                    }
                }
            }
        }
    };
}
</script>
