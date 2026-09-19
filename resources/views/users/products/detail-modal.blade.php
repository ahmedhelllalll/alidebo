{{-- 
    Product Detail Quick-View Modal (Phase 4)
    Displays full product details, specifications, and commercial terms.
    Includes Airbnb-style "Show all photos" grid icon overlay on hero image
    which triggers the smooth animated fullscreen gallery.
--}}
<div x-cloak
     x-show="detailModal.open"
     role="dialog"
     aria-modal="true"
     aria-labelledby="product-detail-title"
     data-lenis-prevent
     class="fixed inset-0 z-[9990] flex items-center justify-center p-3 sm:p-6 overflow-y-auto select-none"
     style="display: none;">

    {{-- Backdrop --}}
    <div x-show="detailModal.open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-md"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 backdrop-blur-md"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         @click="closeDetailModal()"
         class="fixed inset-0 bg-zinc-950/60 dark:bg-black/80 backdrop-blur-md">
    </div>

    {{-- Modal Card --}}
    <div x-show="detailModal.open"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-95 translate-y-3"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-3"
         @click.stop
         class="relative w-full max-w-3xl my-8 bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] z-10">

        {{-- Sticky Modal Header --}}
        <div class="px-5 sm:px-6 py-4 flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md shrink-0">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                <h3 class="text-xs sm:text-sm font-black text-zinc-900 dark:text-zinc-100 uppercase tracking-wider truncate">
                    {{ __('products.product_details') }}
                </h3>
            </div>

            <button type="button" 
                    @click="closeDetailModal()"
                    class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 flex items-center justify-center transition-colors shadow-xs"
                    title="{{ __('products.close') }} (Esc)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Scrollable Modal Body --}}
        <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-5 sm:p-7 space-y-6 overscroll-contain" data-lenis-prevent style="-webkit-overflow-scrolling: touch;">
            <template x-if="detailModal.product">
                <div class="space-y-6">
                    
                    {{-- ── 1. Hero Image & Airbnb-Style Gallery Button ── --}}
                    <div class="relative aspect-[16/10] sm:aspect-[16/9] rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-750 shadow-inner group">
                        
                        {{-- Top Floating Badges --}}
                        <div class="absolute top-3 inset-x-3 z-10 flex items-center justify-between gap-2 pointer-events-none">
                            {{-- Status Badge --}}
                            <div class="pointer-events-auto shrink-0">
                                <template x-if="detailModal.product.status === 'approved'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-500 text-white shadow-md shadow-emerald-500/20 backdrop-blur-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        {{ __('products.status_approved') }}
                                    </span>
                                </template>
                                <template x-if="detailModal.product.status === 'pending'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-500 text-white shadow-md shadow-amber-500/20 backdrop-blur-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        {{ __('products.status_pending') }}
                                    </span>
                                </template>
                                <template x-if="detailModal.product.status === 'rejected'">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-500 text-white shadow-md shadow-rose-500/20 backdrop-blur-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        {{ __('products.status_rejected') }}
                                    </span>
                                </template>
                            </div>

                            {{-- Category Pill --}}
                            <template x-if="detailModal.product.category">
                                <div class="pointer-events-auto">
                                    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black tracking-wider bg-black/70 dark:bg-zinc-900/90 text-white backdrop-blur-md border border-white/10 shadow-sm"
                                          x-text="detailModal.product.category"></span>
                                </div>
                            </template>
                        </div>

                        {{-- Hero Image --}}
                        <template x-if="getActiveHeroImage()">
                            <img :src="getActiveHeroImage()"
                                 @click="launchGalleryFromModal($event)"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 cursor-pointer"
                                 title="{{ __('products.photo_gallery') }}">
                        </template>
                        <template x-if="!getActiveHeroImage()">
                            <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-zinc-400 dark:text-zinc-600">
                                <svg class="w-12 h-12 stroke-[1.2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span class="text-xs font-bold">{{ __('products.no_image') }}</span>
                            </div>
                        </template>

                        {{-- Airbnb-Style "Show all photos" Grid Button Overlay --}}
                        <template x-if="detailModal.product.images && detailModal.product.images.length > 0">
                            <button type="button" 
                                    @click="launchGalleryFromModal($event)"
                                    class="absolute bottom-3.5 end-3.5 z-20 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/95 dark:bg-zinc-900/95 hover:bg-white dark:hover:bg-zinc-800 text-zinc-900 dark:text-white text-xs font-black shadow-xl backdrop-blur-md border border-black/10 dark:border-white/10 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer">
                                {{-- Airbnb 3x3 Grid Dots Icon --}}
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" fill="currentColor">
                                    <circle cx="2.5" cy="2.5" r="1.5"/>
                                    <circle cx="8" cy="2.5" r="1.5"/>
                                    <circle cx="13.5" cy="2.5" r="1.5"/>
                                    <circle cx="2.5" cy="8" r="1.5"/>
                                    <circle cx="8" cy="8" r="1.5"/>
                                    <circle cx="13.5" cy="8" r="1.5"/>
                                    <circle cx="2.5" cy="13.5" r="1.5"/>
                                    <circle cx="8" cy="13.5" r="1.5"/>
                                    <circle cx="13.5" cy="13.5" r="1.5"/>
                                </svg>
                                <span>{{ __('products.show_all_photos') }}</span>
                                <span class="px-1.5 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black" 
                                      x-text="detailModal.product.images.length"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Mini Thumbnails Strip --}}
                    <template x-if="detailModal.product.images && detailModal.product.images.length > 1">
                        <div class="flex items-center gap-2 overflow-x-auto light-scrollbar pb-1">
                            <template x-for="(img, idx) in detailModal.product.images" :key="img.id || idx">
                                <button type="button" 
                                        @click="detailModal.activeImageIdx = idx"
                                        class="relative w-16 h-16 rounded-xl overflow-hidden border-2 shrink-0 transition-all duration-150 cursor-pointer"
                                        :class="detailModal.activeImageIdx === idx ? 'border-primary ring-2 ring-primary/30 scale-105 opacity-100' : 'border-zinc-200 dark:border-zinc-700 opacity-60 hover:opacity-100'">
                                    <img :src="img.url" class="w-full h-full object-cover">
                                    <template x-if="img.is_primary">
                                        <span class="absolute top-1 start-1 w-2 h-2 rounded-full bg-primary ring-1 ring-white"></span>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </template>

                    {{-- ── 2. Product Title & Rejection Reason Alert ── --}}
                    <div>
                        <h2 id="product-detail-title" 
                            class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-zinc-100 text-start tracking-tight leading-snug"
                            x-text="detailModal.product.name"></h2>

                        {{-- Rejection Callout --}}
                        <template x-if="detailModal.product.status === 'rejected' && detailModal.product.rejection_reason">
                            <div class="mt-3 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-900/50 text-start">
                                <div class="flex items-start gap-2.5">
                                    <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <div>
                                        <p class="text-[11px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-400">
                                            {{ __('products.rejection_reason_label') }}
                                        </p>
                                        <p class="text-xs sm:text-sm font-semibold text-rose-800 dark:text-rose-200 mt-1 leading-relaxed" 
                                           x-text="detailModal.product.rejection_reason"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ── 3. Commercial Badges (Price, Packaging, MOQ, SKU) ── --}}
                    <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/80 dark:border-zinc-800 flex flex-wrap items-center gap-3">
                        {{-- Formatted Price --}}
                        <div class="flex items-baseline gap-1">
                            <template x-if="detailModal.product.base_price">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xl sm:text-2xl font-[900] text-primary" x-text="detailModal.product.formatted_price"></span>
                                    <span class="text-xs font-bold text-zinc-400 dark:text-zinc-500">/ <span x-text="detailModal.product.unit"></span></span>
                                </div>
                            </template>
                            <template x-if="!detailModal.product.base_price">
                                <span class="text-sm font-black text-zinc-500 dark:text-zinc-400">
                                    {{ __('products.preview_price_on_request') }}
                                </span>
                            </template>
                        </div>

                        {{-- Package Details Badge --}}
                        <template x-if="detailModal.product.package_details">
                            <div class="px-3 py-1 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-xs font-bold flex items-center gap-1.5 shadow-xs">
                                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span x-text="detailModal.product.package_details"></span>
                            </div>
                        </template>

                        {{-- MOQ --}}
                        <template x-if="detailModal.product.moq">
                            <div class="px-3 py-1 rounded-xl bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 text-xs font-bold">
                                <span class="text-zinc-400 dark:text-zinc-500 font-semibold">{{ __('products.preview_moq') }}</span>
                                <span x-text="detailModal.product.moq + ' ' + detailModal.product.unit"></span>
                            </div>
                        </template>

                        {{-- SKU --}}
                        <template x-if="detailModal.product.sku">
                            <div class="px-3 py-1 rounded-xl bg-white dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 text-xs font-mono font-bold uppercase ms-auto">
                                <span x-text="'SKU: ' + detailModal.product.sku"></span>
                            </div>
                        </template>
                    </div>

                    {{-- ── 4. Descriptions ── --}}
                    <div class="space-y-4 text-start">
                        <template x-if="detailModal.product.short_description">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-1">
                                    {{ __('products.short_description') }}
                                </h4>
                                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 leading-relaxed" 
                                   x-text="detailModal.product.short_description"></p>
                            </div>
                        </template>

                        <template x-if="detailModal.product.full_description">
                            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                <h4 class="text-xs font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-1">
                                    {{ __('products.full_description') }}
                                </h4>
                                <div class="text-xs sm:text-sm font-medium text-zinc-600 dark:text-zinc-300 leading-relaxed whitespace-pre-line"
                                     x-text="detailModal.product.full_description"></div>
                            </div>
                        </template>
                    </div>

                    {{-- ── 5. Technical Specifications Table ── --}}
                    <template x-if="detailModal.product.specifications && detailModal.product.specifications.length > 0">
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 text-start space-y-3">
                            <h4 class="text-xs font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                {{ __('products.specifications') }}
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <template x-for="(spec, i) in detailModal.product.specifications" :key="i">
                                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/70 dark:border-zinc-700/60">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500" x-text="spec.name"></p>
                                        <p class="text-xs sm:text-sm font-black text-zinc-800 dark:text-zinc-100 mt-0.5 truncate" x-text="spec.value"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Modal Footer --}}
        <div class="px-5 sm:px-6 py-4 flex items-center justify-between gap-3 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/90 dark:bg-zinc-900/90 backdrop-blur-md shrink-0">
            <button type="button"
                    @click="closeDetailModal()"
                    class="px-5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                {{ __('products.close') }}
            </button>

            <template x-if="detailModal.product?.edit_url">
                <a :href="detailModal.product.edit_url"
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs sm:text-sm font-extrabold shadow-md shadow-primary/20 transition-all hover:scale-105 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>{{ __('products.edit_product_action') }}</span>
                </a>
            </template>

            <template x-if="!detailModal.product?.edit_url">
                <button type="button"
                        disabled
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary/80 text-white text-xs sm:text-sm font-extrabold shadow-sm opacity-80 cursor-not-allowed"
                        title="{{ __('directory.request_quote_soon') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>{{ __('directory.get_quote') }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-white/20 font-bold uppercase tracking-wider">
                        {{ app()->getLocale() === 'ar' ? 'قريباً' : 'Coming Soon' }}
                    </span>
                </button>
            </template>
        </div>
    </div>
</div>
