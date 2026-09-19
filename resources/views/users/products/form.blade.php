{{-- 
    Single Scrollable Product Form with Live Preview
    Adheres strictly to the Alidebo design system:
    - Primary Color: #f45018 (primary-light: #ff6b35)
    - Dark mode: darkBg (#09090b), zincLight (#18181b), glass-panel
    - Fonts: Cairo (RTL) / Plus Jakarta Sans (LTR)
    - Alpine.js Reactive Form & Live Preview with Smart Positioning & Dynamic Placeholders
--}}

@php
    $currentAppLocale = app()->getLocale();
    $locales = [
        'ar' => ['name' => __('products.arabic'), 'dir' => 'rtl', 'flag' => '🇸🇦'],
        'en' => ['name' => __('products.english'), 'dir' => 'ltr', 'flag' => '🇬🇧'],
        'es' => ['name' => __('products.spanish'), 'dir' => 'ltr', 'flag' => '🇪🇸'],
        'de' => ['name' => __('products.german'), 'dir' => 'ltr', 'flag' => '🇩🇪'],
        'tr' => ['name' => __('products.turkish'), 'dir' => 'ltr', 'flag' => '🇹🇷'],
        'zh' => ['name' => __('products.chinese'), 'dir' => 'ltr', 'flag' => '🇨🇳'],
    ];

    // Pre-populate initial state for edit mode
    $initialNames = [];
    $initialShortDesc = [];
    $initialFullDesc = [];
    $initialSpecs = [];
    $initialImages = [];

    foreach (array_keys($locales) as $loc) {
        $initialNames[$loc] = '';
        $initialShortDesc[$loc] = '';
        $initialFullDesc[$loc] = '';
    }

    if ($isEdit && isset($product)) {
        foreach ($product->translations as $t) {
            $initialNames[$t->locale] = $t->name ?? '';
            $initialShortDesc[$t->locale] = $t->short_description ?? '';
            $initialFullDesc[$t->locale] = $t->full_description ?? '';
        }

        foreach ($product->specifications as $spec) {
            $nameArr = is_array($spec->name) ? $spec->name : ['en' => (string)$spec->name];
            $valArr = is_array($spec->value) ? $spec->value : ['en' => (string)$spec->value];
            
            $specItem = ['name' => [], 'value' => []];
            foreach (array_keys($locales) as $loc) {
                $specItem['name'][$loc] = $nameArr[$loc] ?? ($nameArr['en'] ?? '');
                $specItem['value'][$loc] = $valArr[$loc] ?? ($valArr['en'] ?? '');
            }
            $initialSpecs[] = $specItem;
        }

        foreach ($product->images as $img) {
            $initialImages[] = [
                'id' => $img->id,
                'url' => $img->file_url ?? (Storage::disk($img->disk ?? 'r2')->url($img->file_path)),
                'isPrimary' => (bool)$img->is_primary,
                'isExisting' => true,
            ];
        }
    }

    // Dynamic localized placeholders across all 6 locales
    $placeholders = [];
    $previewI18n = [];
    foreach (array_keys($locales) as $loc) {
        $placeholders[$loc] = [
            'product_name' => __('products.product_name_placeholder', [], $loc),
            'short_description' => __('products.short_description_placeholder', [], $loc),
            'full_description' => __('products.full_description_placeholder', [], $loc),
            'spec_key' => __('products.spec_key_placeholder', [], $loc),
            'spec_value' => __('products.spec_value_placeholder', [], $loc),
            'unit' => __('products.unit_placeholder', [], $loc),
            'units_per_package' => __('products.units_per_package_placeholder', [], $loc),
            'base_price' => __('products.base_price_placeholder', [], $loc),
            'moq' => __('products.moq_placeholder', [], $loc),
            'sku' => __('products.sku_placeholder', [], $loc),
            'search_categories' => __('products.search_categories_placeholder', [], $loc),
            'preview_untitled' => __('products.preview_untitled', [], $loc),
            'preview_no_desc' => __('products.preview_no_desc', [], $loc),
        ];

        $previewI18n[$loc] = [
            'live_preview' => __('products.live_preview', [], $loc),
            'preview_buyer_view' => __('products.preview_buyer_view', [], $loc),
            'no_images' => __('products.no_images', [], $loc),
            'preview_uncategorized' => __('products.preview_uncategorized', [], $loc),
            'preview_untitled' => __('products.preview_untitled', [], $loc),
            'preview_no_desc' => __('products.preview_no_desc', [], $loc),
            'preview_price_on_request' => __('products.preview_price_on_request', [], $loc),
            'preview_request_quote' => __('products.preview_request_quote', [], $loc),
            'preview_contact_supplier' => __('products.preview_contact_supplier', [], $loc),
            'preview_moq' => __('products.preview_moq', [], $loc),
            'preview_sku' => __('products.preview_sku', [], $loc),
            'preview_specs_title' => __('products.preview_specs_title', [], $loc),
            'status_pending_notice' => __('products.status_pending_notice', [], $loc),
            'unit_piece' => __('products.unit_piece', [], $loc),
            'units_per_package' => __('products.units_per_package', [], $loc),
            'package_pieces_format' => __('products.package_pieces_format', [], $loc),
        ];
    }

    $categoriesData = $categories->map(function ($cat) {
        return [
            'id' => $cat->id,
            'name' => [
                'ar' => $cat->name_ar,
                'en' => $cat->name_en,
                'es' => $cat->name_es ?? $cat->name_en,
                'de' => $cat->name_de ?? $cat->name_en,
                'tr' => $cat->name_tr ?? $cat->name_en,
                'zh' => $cat->name_zh ?? $cat->name_en,
            ],
        ];
    })->values();

    $canonicalUnits = ['piece', 'kg', 'carton', 'box', 'meter', 'set', 'ton', 'liter'];
    $unitsData = [];
    foreach ($canonicalUnits as $uKey) {
        $labels = [];
        foreach (array_keys($locales) as $loc) {
            $labels[$loc] = __('products.unit_' . $uKey, [], $loc);
        }
        $unitsData[] = [
            'key' => $uKey,
            'label' => $labels,
        ];
    }
@endphp

<div x-data="productForm({
    isEdit: {{ $isEdit ? 'true' : 'false' }},
    productId: {{ $isEdit && isset($product) ? $product->id : 'null' }},
    submitUrl: '{{ $isEdit ? route('products.update', $product->id) : route('products.store') }}',
    defaultLocale: '{{ $currentAppLocale }}',
    initialNames: {{ json_encode($initialNames) }},
    initialShortDesc: {{ json_encode($initialShortDesc) }},
    initialFullDesc: {{ json_encode($initialFullDesc) }},
    initialSpecs: {{ json_encode($initialSpecs) }},
    initialImages: {{ json_encode($initialImages) }},
    initialCategory: '{{ $isEdit && isset($product) ? $product->category_id : '' }}',
    initialUnit: '{{ $isEdit && isset($product) ? $product->unit : 'piece' }}',
    initialUnitsPerPackage: '{{ $isEdit && isset($product) ? $product->units_per_package : '' }}',
    initialSku: '{{ $isEdit && isset($product) ? $product->sku : '' }}',
    initialMoq: '{{ $isEdit && isset($product) ? $product->moq : '' }}',
    initialBasePrice: '{{ $isEdit && isset($product) ? $product->base_price : '' }}',
    initialCurrency: '{{ $isEdit && isset($product) ? ($product->currency ?? 'EGP') : 'EGP' }}',
    placeholders: {{ json_encode($placeholders, JSON_UNESCAPED_UNICODE) }},
    previewI18n: {{ json_encode($previewI18n, JSON_UNESCAPED_UNICODE) }},
    categories: {{ json_encode($categoriesData, JSON_UNESCAPED_UNICODE) }},
    units: {{ json_encode($unitsData, JSON_UNESCAPED_UNICODE) }}
})" 
dir="{{ $currentAppLocale == 'ar' ? 'rtl' : 'ltr' }}"
class="relative w-full max-w-7xl mx-auto pb-24">

    {{-- Toast Notification Box (Item 9: Redesigned with proper light/dark card styling, high contrast, clean icons) --}}
    <div x-show="toast.visible" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed top-5 end-5 z-[9999] flex items-center gap-3.5 p-4 rounded-2xl shadow-2xl max-w-md w-full border backdrop-blur-xl bg-white/95 dark:bg-zinc-900/95"
         :class="toast.type === 'success' 
            ? 'border-emerald-500/30 text-zinc-900 dark:text-white shadow-emerald-500/10' 
            : 'border-rose-500/30 text-zinc-900 dark:text-white shadow-rose-500/10'"
         style="display: none;">
        
        {{-- Status Icon Container --}}
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
             :class="toast.type === 'success' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'">
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </template>
            <template x-if="toast.type !== 'success'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </template>
        </div>

        {{-- Message --}}
        <div class="flex-1 min-w-0">
            <p class="text-xs font-black uppercase tracking-wider mb-0.5 text-start"
               :class="toast.type === 'success' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
               x-text="toast.type === 'success' ? '{{ __('products.toast_success_title') }}' : '{{ __('products.toast_error_title') }}'"></p>
            <p class="text-xs sm:text-sm font-bold text-zinc-800 dark:text-zinc-200 leading-snug text-start" x-text="toast.message"></p>
        </div>

        {{-- Close Button --}}
        <button type="button" 
                @click="toast.visible = false" 
                class="w-7 h-7 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center transition-colors"
                title="{{ __('products.toast_dismiss') }}"
                aria-label="{{ __('products.toast_dismiss') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-zinc-400 hover:text-primary transition-colors">
                    {{ __('nav.home') }}
                </a>
                <span class="text-zinc-400 text-xs">/</span>
                <span class="text-xs font-semibold text-primary">
                    {{ $isEdit ? __('products.edit_product') : __('products.add_product') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-[900] text-zinc-900 dark:text-white tracking-tight text-start">
                {{ $isEdit ? __('products.edit_product') : __('products.add_product') }}
            </h1>
            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 font-medium mt-1 text-start">
                {{ $isEdit ? __('products.subtitle_edit') : __('products.subtitle_create') }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Guide Tour Re-trigger Button --}}
            <button type="button" 
                    id="tour-retrigger-btn"
                    @click="$dispatch('open-products-tour')" 
                    class="px-3.5 py-2 rounded-xl bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:text-primary dark:hover:text-primary border border-zinc-200/80 dark:border-zinc-700/80 shadow-xs text-xs font-bold flex items-center gap-1.5 transition-all hover:scale-105 active:scale-95 cursor-pointer shrink-0"
                    title="{{ __('products.tour_guide_button') }}">
                <span class="w-4 h-4 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black text-[11px]">؟</span>
                <span>{{ __('products.tour_guide_button') }}</span>
            </button>

            {{-- Mobile View Mode Switcher (Form / Live Preview) --}}
            <div class="lg:hidden flex items-center bg-zinc-100 dark:bg-zinc-800/80 p-1 rounded-xl border border-zinc-200/60 dark:border-zinc-700/60">
                <button type="button" 
                        @click="mobileTab = 'form'"
                        class="flex-1 sm:flex-initial px-4 py-2 text-xs font-bold rounded-lg transition-all"
                        :class="mobileTab === 'form' ? 'bg-white dark:bg-zinc-900 text-primary shadow-sm' : 'text-zinc-600 dark:text-zinc-400'">
                    {{ __('products.view_mode_form') }}
                </button>
                <button type="button" 
                        @click="mobileTab = 'preview'"
                        class="flex-1 sm:flex-initial px-4 py-2 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5"
                        :class="mobileTab === 'preview' ? 'bg-white dark:bg-zinc-900 text-primary shadow-sm' : 'text-zinc-600 dark:text-zinc-400'">
                    <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ __('products.view_mode_preview') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Error Banner --}}
    <div x-show="errors.length > 0" 
         x-transition 
         class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs font-semibold"
         style="display: none;">
        <div class="flex items-center gap-2 mb-2 font-bold text-sm text-rose-800 dark:text-rose-300">
            <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ __('products.error_title') }}</span>
        </div>
        <ul class="list-disc list-inside space-y-1 ps-2 text-start">
            <template x-for="(err, idx) in errors" :key="idx">
                <li x-text="err"></li>
            </template>
        </ul>
    </div>

    {{-- Main Grid Container: Left Scrollable Form + Right Sticky Live Preview --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ═══ LEFT / MAIN FORM COLUMN ═══ --}}
        <div class="lg:col-span-7 xl:col-span-7 space-y-8" :class="mobileTab === 'preview' ? 'hidden lg:block' : 'block'">
            <form @submit.prevent="submitForm" id="productFormElement">

                {{-- ── GLOBAL MULTI-LANGUAGE SWITCHER BAR ── --}}
                <div id="tour-language-tabs" class="bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl p-5 shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)] mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                            </div>
                            <h3 class="text-xs font-[800] uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                                {{ __('products.languages') }}
                            </h3>
                        </div>
                        <span class="text-[11px] font-semibold text-primary bg-primary/[0.08] px-2.5 py-1 rounded-full border border-primary/20">
                            {{ __('products.base_language_badge') }} (EN)
                        </span>
                    </div>

                    {{-- Language Tabs --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($locales as $code => $loc)
                        <button type="button"
                                @click="activeLocale = '{{ $code }}'"
                                class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold border transition-all duration-200"
                                :class="activeLocale === '{{ $code }}' 
                                    ? 'bg-primary text-white border-primary shadow-md shadow-primary/20 scale-[1.02]' 
                                    : 'bg-zinc-50 dark:bg-zinc-800/60 border-zinc-200/80 dark:border-zinc-700/60 text-zinc-600 dark:text-zinc-300 hover:border-primary/40'">
                            <span>{{ $loc['flag'] }}</span>
                            <span>{{ $loc['name'] }}</span>
                            <span class="w-1.5 h-1.5 rounded-full"
                                  :class="hasContentForLocale('{{ $code }}') ? 'bg-emerald-400' : 'bg-zinc-300 dark:bg-zinc-600'"></span>
                        </button>
                        @endforeach
                    </div>

                    {{-- AI Notice --}}
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-amber-500/[0.08] dark:bg-amber-500/[0.12] border border-amber-500/20 text-amber-800 dark:text-amber-300 text-[11px] leading-relaxed text-start">
                        <svg class="w-4 h-4 shrink-0 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>{{ __('products.ai_translation_notice') }}</span>
                    </div>
                </div>

                {{-- ═══ SECTION 1: GENERAL INFORMATION & MEDIA ═══ --}}
                <div class="bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)] mb-8 space-y-6">
                    <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center shrink-0">1</span>
                            <h2 class="text-base font-[800] text-zinc-900 dark:text-zinc-100 text-start">
                                {{ __('products.section_general') }}
                            </h2>
                        </div>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 text-start">
                            {{ __('products.section_general_desc') }}
                        </p>
                    </div>

                    {{-- Category & Unit Row --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Custom Alpine.js Smart Dropdown Component for Category (Item 7) --}}
                        <div id="tour-category-field" class="space-y-2" x-data="{
                            open: false,
                            search: '',
                            dropUp: false,
                            toggle(triggerEl) {
                                if (!this.open) {
                                    const rect = triggerEl.getBoundingClientRect();
                                    const spaceBelow = window.innerHeight - rect.bottom;
                                    const spaceAbove = rect.top;
                                    this.dropUp = spaceBelow < 340 && spaceAbove > spaceBelow;
                                    this.open = true;
                                    this.$nextTick(() => {
                                        if (this.$refs.catSearchInput) this.$refs.catSearchInput.focus();
                                    });
                                } else {
                                    this.open = false;
                                }
                            },
                            select(id) {
                                form.category_id = id;
                                this.open = false;
                                this.search = '';
                            },
                            get selectedCategoryName() {
                                if (!form.category_id) return '';
                                const found = categories.find(c => c.id == form.category_id);
                                return found ? (typeof found.name === 'object' ? (found.name[activeLocale] || found.name['en'] || Object.values(found.name)[0]) : found.name) : '';
                            },
                            get filteredCategories() {
                                const getCatName = (c) => typeof c.name === 'object' ? (c.name[activeLocale] || c.name['en'] || Object.values(c.name)[0] || '') : (c.name || '');
                                if (!this.search || this.search.trim() === '') return categories;
                                const q = this.search.toLowerCase();
                                return categories.filter(c => getCatName(c).toLowerCase().includes(q));
                            }
                        }" @click.outside="open = false" @keydown.escape.window="open = false">
                            
                            <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.category') }}
                            </label>

                            <div class="relative">
                                {{-- Trigger Button (Consistent Solid Icon Box matching unit and name inputs) --}}
                                <button type="button" 
                                        x-ref="catDropdownBtn"
                                        @click="toggle($refs.catDropdownBtn)"
                                        class="w-full flex items-stretch rounded-xl border bg-white dark:bg-zinc-900/50 overflow-hidden shadow-sm transition-all duration-300 border-zinc-200 dark:border-white/[0.08] hover:border-primary/50 text-start cursor-pointer select-none"
                                        :class="open ? 'border-primary ring-2 ring-primary/30' : 'focus:border-primary focus:ring-2 focus:ring-primary/30'">
                                    
                                    {{-- Flush Solid Primary Box matching unit and other form inputs --}}
                                    <div class="flex items-center justify-center px-4 bg-primary text-white shrink-0" aria-hidden="true">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </div>

                                    {{-- Value display + Chevron --}}
                                    <div class="flex-1 flex items-center justify-between min-w-0 ps-4 pe-4 py-3">
                                        <span class="text-xs sm:text-sm font-bold truncate"
                                              :class="form.category_id ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500'"
                                              x-text="selectedCategoryName || '{{ __('products.select_category') }}'">
                                        </span>
                                        <svg class="w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-200 ms-2" 
                                             :class="open ? 'rotate-180 text-primary' : ''" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>

                                {{-- Dropdown Popover (Smart Upward / Downward positioning & Pinned Search) --}}
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200 transform"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150 transform"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute start-0 end-0 z-50 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl p-2 flex flex-col"
                                     :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                                     style="max-height: 340px; display: none;">
                                    
                                    {{-- Search Input inside Dropdown (Pinned at top, doesn't scroll) --}}
                                    <div class="relative mb-2 shrink-0">
                                        <input type="text" 
                                               x-ref="catSearchInput"
                                               x-model="search"
                                               :placeholder="getPlaceholder('search_categories')"
                                               class="w-full bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/80 rounded-xl ps-9 pe-3 py-2 text-xs font-semibold text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 outline-none focus:border-primary text-start">
                                        <svg class="w-4 h-4 text-zinc-400 absolute start-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>

                                    {{-- Categories List (Internal scroll, max-height 280px) --}}
                                    <div class="overflow-y-auto custom-scrollbar space-y-1 flex-1 min-h-0" style="max-height: 280px; overflow-y: auto;">
                                        <template x-for="cat in filteredCategories" :key="cat.id">
                                            <button type="button" 
                                                    @click="select(cat.id)"
                                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-colors text-start"
                                                    :class="form.category_id == cat.id 
                                                        ? 'bg-primary/10 text-primary dark:bg-primary/20' 
                                                        : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60'">
                                                <span x-text="typeof cat.name === 'object' ? (cat.name[activeLocale] || cat.name['en'] || Object.values(cat.name)[0]) : cat.name"></span>
                                                <template x-if="form.category_id == cat.id">
                                                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </template>
                                            </button>
                                        </template>
                                        <template x-if="filteredCategories.length === 0">
                                            <p class="text-xs text-zinc-400 text-center py-4">
                                                {{ __('forms.business.category_not_found') ?? 'No categories found' }}
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 ps-1 font-semibold text-start">
                                {{ __('products.category_help') }}
                            </p>
                        </div>

                        {{-- Custom Alpine.js Smart Dropdown Component for Unit (Reuses Category Dropdown Pattern) --}}
                        <div id="tour-unit-field" class="space-y-2" x-data="{
                            open: false,
                            dropUp: false,
                            toggle(triggerEl) {
                                if (!this.open) {
                                    const rect = triggerEl.getBoundingClientRect();
                                    const spaceBelow = window.innerHeight - rect.bottom;
                                    const spaceAbove = rect.top;
                                    this.dropUp = spaceBelow < 300 && spaceAbove > spaceBelow;
                                    this.open = true;
                                } else {
                                    this.open = false;
                                }
                            },
                            select(key) {
                                form.unit = key;
                                this.open = false;
                            },
                            get selectedUnitName() {
                                if (!form.unit) return '';
                                const found = units.find(u => u.key === form.unit);
                                return found ? (found.label[activeLocale] || found.label['en'] || found.key) : form.unit;
                            }
                        }" @click.outside="open = false" @keydown.escape.window="open = false">
                            
                            <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.unit') }} <span class="text-primary">*</span>
                            </label>

                            <div class="relative">
                                {{-- Hidden Input for form submission --}}
                                <input type="hidden" name="unit" :value="form.unit">

                                {{-- Trigger Button (Exact Category Dropdown Pattern: Solid Primary Icon Box + Value + Chevron) --}}
                                <button type="button" 
                                        x-ref="unitDropdownBtn"
                                        @click="toggle($refs.unitDropdownBtn)"
                                        class="w-full flex items-stretch rounded-xl border bg-white dark:bg-zinc-900/50 overflow-hidden shadow-sm transition-all duration-300 border-zinc-200 dark:border-white/[0.08] hover:border-primary/50 text-start cursor-pointer select-none"
                                        :class="open ? 'border-primary ring-2 ring-primary/30' : 'focus:border-primary focus:ring-2 focus:ring-primary/30'"
                                        aria-haspopup="listbox"
                                        :aria-expanded="open">
                                    
                                    {{-- Flush Solid Primary Box matching category and form inputs --}}
                                    <div class="flex items-center justify-center px-4 bg-primary text-white shrink-0" aria-hidden="true">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                    </div>

                                    {{-- Value display + Chevron --}}
                                    <div class="flex-1 flex items-center justify-between min-w-0 ps-4 pe-4 py-3">
                                        <span class="text-xs sm:text-sm font-bold truncate text-zinc-900 dark:text-zinc-100"
                                              x-text="selectedUnitName || '{{ __('products.unit_piece') }}'">
                                        </span>
                                        <svg class="w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-200 ms-2" 
                                             :class="open ? 'rotate-180 text-primary' : ''" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>

                                {{-- Dropdown Popover (Smart Upward / Downward positioning matching Category Dropdown) --}}
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200 transform"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150 transform"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute start-0 end-0 z-50 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl p-2 flex flex-col"
                                     :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                                     style="max-height: 280px; display: none;">
                                    
                                    {{-- Units List (Internal scroll, max-height 260px) --}}
                                    <div class="overflow-y-auto custom-scrollbar space-y-1 flex-1 min-h-0" style="max-height: 260px; overflow-y: auto;">
                                        <template x-for="u in units" :key="u.key">
                                            <button type="button" 
                                                    @click="select(u.key)"
                                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-colors text-start cursor-pointer"
                                                    :class="form.unit === u.key 
                                                        ? 'bg-primary/10 text-primary dark:bg-primary/20' 
                                                        : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60'">
                                                <span x-text="u.label[activeLocale] || u.label['en'] || u.key"></span>
                                                <template x-if="form.unit === u.key">
                                                    <svg class="w-4 h-4 text-primary shrink-0 ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </template>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 ps-1 font-semibold text-start">
                                {{ __('products.unit_help') }}
                            </p>
                        </div>
                    </div>

                    {{-- Conditional Pieces per Package for Bulk Units (Carton, Box, Set) --}}
                    <div x-show="['carton', 'box', 'set'].includes(form.unit)" 
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200 transform"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="space-y-2"
                         style="display: none;">
                        <div class="flex items-center justify-between">
                            <label for="units_per_package" class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.units_per_package') }}
                            </label>
                        </div>
                        <div class="relative flex items-stretch rounded-xl border bg-white dark:bg-zinc-900/50 overflow-hidden shadow-sm transition-all duration-300 border-zinc-200 dark:border-white/[0.08] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30">
                            <div class="flex items-center justify-center px-4 bg-primary text-white shrink-0" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <input type="number" 
                                   step="1" 
                                   min="1" 
                                   id="units_per_package" 
                                   dir="ltr"
                                   x-model="form.units_per_package" 
                                   :placeholder="getPlaceholder('units_per_package')" 
                                   class="w-full bg-transparent px-3.5 py-3 text-xs sm:text-sm font-bold outline-none border-0 focus:ring-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 text-start">
                        </div>
                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500 ps-1 font-semibold text-start">
                            {{ __('products.units_per_package_help') }}
                        </p>
                    </div>

                    {{-- Product Name Field (Per Active Locale) --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="product_name" class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.product_name') }} (<span x-text="activeLocale.toUpperCase()"></span>)
                                <template x-if="activeLocale === 'en'">
                                    <span class="text-primary">*</span>
                                </template>
                            </label>
                            <span class="text-[10px] uppercase font-black tracking-widest text-zinc-400" x-text="getLocaleDir(activeLocale)"></span>
                        </div>
                        <div class="relative flex items-stretch rounded-xl border bg-white dark:bg-zinc-900/50 overflow-hidden shadow-sm transition-all duration-300 border-zinc-200 dark:border-white/[0.08] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30">
                            <div class="flex items-center justify-center px-4 bg-primary text-white shrink-0" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <input type="text" 
                                   id="product_name" 
                                   x-model="form.name[activeLocale]" 
                                   :dir="getLocaleDir(activeLocale)"
                                   :placeholder="getPlaceholder('product_name')" 
                                   class="flex-1 bg-transparent ps-4 pe-4 py-3 text-xs sm:text-sm font-bold outline-none border-0 focus:ring-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 text-start">
                        </div>
                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500 ps-1 font-semibold text-start">
                            {{ __('products.product_name_help') }}
                        </p>
                    </div>

                    {{-- Short Description Field (Per Active Locale) --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="short_description" class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.short_description') }} (<span x-text="activeLocale.toUpperCase()"></span>)
                                <template x-if="activeLocale === 'en'">
                                    <span class="text-primary">*</span>
                                </template>
                            </label>
                            <span class="text-[10px] uppercase font-black tracking-widest text-zinc-400" x-text="getLocaleDir(activeLocale)"></span>
                        </div>
                        <div class="relative rounded-xl border bg-white dark:bg-zinc-900/50 overflow-hidden shadow-sm transition-all duration-300 border-zinc-200 dark:border-white/[0.08] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30">
                            <textarea id="short_description" 
                                      rows="2" 
                                      x-model="form.short_description[activeLocale]"
                                      :dir="getLocaleDir(activeLocale)"
                                      :placeholder="getPlaceholder('short_description')" 
                                      class="w-full bg-transparent ps-4 pe-4 py-3 text-xs sm:text-sm font-medium outline-none border-0 focus:ring-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 resize-y text-start"></textarea>
                        </div>
                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500 ps-1 font-semibold text-start">
                            {{ __('products.short_description_help') }}
                        </p>
                    </div>

                    {{-- Image Gallery Upload UI --}}
                    <div id="tour-image-upload" class="space-y-4 pt-2">
                        <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                            {{ __('products.product_images') }}
                        </label>

                        {{-- Drag and Drop Area --}}
                        <div @dragover.prevent="isDragging = true" 
                             @dragleave.prevent="isDragging = false" 
                             @drop.prevent="handleFileDrop($event)" 
                             @click="$refs.fileInput.click()"
                             class="border-2 border-dashed rounded-2xl p-6 sm:p-8 text-center cursor-pointer transition-all duration-200 relative overflow-hidden"
                             :class="isDragging ? 'border-primary bg-primary/[0.05]' : 'border-zinc-300 dark:border-zinc-700 hover:border-primary/60 bg-zinc-50/50 dark:bg-zinc-800/30'">
                            
                            <input type="file" 
                                   x-ref="fileInput" 
                                   @change="handleFileSelect($event)" 
                                   multiple 
                                   accept="image/jpeg,image/png,image/webp,image/jpg" 
                                   class="hidden">

                            <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-xs sm:text-sm font-bold text-zinc-800 dark:text-zinc-200">
                                {{ __('products.upload_title') }}
                            </p>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 font-medium mt-1">
                                {{ __('products.upload_subtitle') }}
                            </p>
                        </div>

                        {{-- Thumbnails Gallery & Cover Designator --}}
                        <template x-if="images.length > 0">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-[11px] font-bold text-zinc-500">
                                    <span>{{ __('products.images_count') }} (<span x-text="images.length"></span>/10)</span>
                                </div>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                    <template x-for="(img, idx) in images" :key="img.id || idx">
                                        <div class="group relative aspect-square rounded-xl overflow-hidden border transition-all duration-200 bg-zinc-100 dark:bg-zinc-800 shadow-sm cursor-pointer"
                                             @click="openLightbox(idx)"
                                             :class="img.isPrimary ? 'ring-2 ring-primary border-primary' : 'border-zinc-200 dark:border-zinc-700'">
                                            
                                            <img :src="img.url" class="w-full h-full object-cover">

                                            {{-- Primary / Cover Badge --}}
                                            <template x-if="img.isPrimary">
                                                <div class="absolute top-1.5 start-1.5 bg-primary text-white text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md shadow-md flex items-center gap-1">
                                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    {{ __('products.cover_image') }}
                                                </div>
                                            </template>

                                            {{-- Hover Overlay with Actions --}}
                                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1.5 p-1">
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button" 
                                                            @click.stop="openLightbox(idx)"
                                                            class="p-1.5 bg-white/20 hover:bg-white/40 text-white rounded-lg shadow-sm transition-all"
                                                            title="{{ __('products.view_mode_preview') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </button>
                                                    <button type="button" 
                                                            @click.stop="removeImage(idx)"
                                                            class="p-1.5 bg-rose-500/90 hover:bg-rose-600 text-white rounded-lg shadow-sm transition-all">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </div>
                                                <template x-if="!img.isPrimary">
                                                    <button type="button" 
                                                            @click.stop="setPrimaryImage(idx)"
                                                            class="px-2 py-1 bg-primary hover:bg-primary-light text-white text-[10px] font-bold rounded-lg shadow-sm transition-all">
                                                        {{ __('products.set_as_cover') }}
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ═══ SECTION 2: FULL DESCRIPTION & SPECIFICATIONS ═══ --}}
                <div class="bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)] mb-8 space-y-6">
                    <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center shrink-0">2</span>
                            <h2 class="text-base font-[800] text-zinc-900 dark:text-zinc-100 text-start">
                                {{ __('products.section_details') }}
                            </h2>
                        </div>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 text-start">
                            {{ __('products.section_details_desc') }}
                        </p>
                    </div>

                    {{-- Full Description Field --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="full_description" class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.full_description') }} (<span x-text="activeLocale.toUpperCase()"></span>)
                            </label>
                            <span class="text-[10px] uppercase font-black tracking-widest text-zinc-400" x-text="getLocaleDir(activeLocale)"></span>
                        </div>
                        <div class="relative rounded-xl border bg-white dark:bg-zinc-900/50 overflow-hidden shadow-sm transition-all duration-300 border-zinc-200 dark:border-white/[0.08] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30">
                            <textarea id="full_description" 
                                      rows="5" 
                                      x-model="form.full_description[activeLocale]"
                                      :dir="getLocaleDir(activeLocale)"
                                      :placeholder="getPlaceholder('full_description')" 
                                      class="w-full bg-transparent ps-4 pe-4 py-3 text-xs sm:text-sm font-medium outline-none border-0 focus:ring-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 resize-y text-start"></textarea>
                        </div>
                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500 ps-1 font-semibold text-start">
                            {{ __('products.full_description_help') }}
                        </p>
                    </div>

                    {{-- Repeatable Technical Specifications (Item 1 & 6) --}}
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                    {{ __('products.specifications') }}
                                </label>
                                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 font-medium text-start">
                                    {{ __('products.specifications_help') }}
                                </p>
                            </div>
                            <button type="button" 
                                    @click="addSpecification()"
                                    class="px-3 py-1.5 bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold rounded-xl flex items-center gap-1.5 transition-colors shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>{{ __('products.add_specification') }}</span>
                            </button>
                        </div>

                        {{-- Specification Rows --}}
                        <div class="space-y-2.5">
                            <template x-for="(spec, sIdx) in specifications" :key="sIdx">
                                <div class="flex items-center gap-2 p-2 bg-zinc-50 dark:bg-zinc-800/40 rounded-xl border border-zinc-200/60 dark:border-zinc-800 transition-all">
                                    {{-- Key Input --}}
                                    <div class="flex-1 min-w-0">
                                        <input type="text" 
                                               x-model="spec.name[activeLocale]" 
                                               :dir="getLocaleDir(activeLocale)"
                                               :placeholder="getPlaceholder('spec_key')" 
                                               class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg ps-3 pe-3 py-2 text-xs font-bold outline-none focus:border-primary text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 text-start">
                                    </div>
                                    
                                    {{-- Value Input --}}
                                    <div class="flex-1 min-w-0">
                                        <input type="text" 
                                               x-model="spec.value[activeLocale]" 
                                               :dir="getLocaleDir(activeLocale)"
                                               :placeholder="getPlaceholder('spec_value')" 
                                               class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg ps-3 pe-3 py-2 text-xs font-medium outline-none focus:border-primary text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 text-start">
                                    </div>

                                    {{-- Remove Row Button (Mirrors cleanly at the end in LTR/RTL) --}}
                                    <button type="button" 
                                            @click="removeSpecification(sIdx)"
                                            class="w-8 h-8 rounded-lg text-zinc-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 flex items-center justify-center shrink-0 transition-colors"
                                            title="{{ __('products.remove_image') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>

                            <template x-if="specifications.length === 0">
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 italic py-2 ps-1 text-start">
                                    {{ __('products.no_specifications') }}
                                </p>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- ═══ SECTION 3: PRICING & MOQ (Redesigned with Option B inline icons) ═══ --}}
                <div id="tour-pricing-grid" class="bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)] mb-8 space-y-6">
                    <div class="border-b border-zinc-100 dark:border-zinc-800 pb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-black flex items-center justify-center shrink-0">3</span>
                            <h2 class="text-base font-[800] text-zinc-900 dark:text-zinc-100 text-start">
                                {{ __('products.section_pricing') }}
                            </h2>
                        </div>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 text-start">
                            {{ __('products.section_pricing_desc') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                        {{-- Base Price (Clean Inline Currency Selector using Custom Alpine Dropdown) --}}
                        <div class="space-y-1.5" id="tour-base-price-field">
                            <label for="base_price" class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.base_price') }}
                            </label>
                            <div class="relative flex items-stretch rounded-xl border bg-white dark:bg-zinc-900/50 shadow-sm transition-all duration-200 border-zinc-200 dark:border-white/[0.08] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30">
                                {{-- Custom Alpine.js Dropdown Component for Currency (Reuses Category Dropdown Pattern) --}}
                                <div class="relative shrink-0 flex items-stretch" x-data="{
                                    open: false,
                                    dropUp: false,
                                    currencies: [
                                        { code: 'EGP', name_ar: 'جنيه مصري (EGP)', name_en: 'Egyptian Pound (EGP)' },
                                        { code: 'USD', name_ar: 'دولار أمريكي (USD)', name_en: 'US Dollar (USD)' }
                                    ],
                                    toggle(triggerEl) {
                                        if (!this.open) {
                                            const rect = triggerEl.getBoundingClientRect();
                                            const spaceBelow = window.innerHeight - rect.bottom;
                                            const spaceAbove = rect.top;
                                            this.dropUp = spaceBelow < 180 && spaceAbove > spaceBelow;
                                            this.open = true;
                                        } else {
                                            this.open = false;
                                        }
                                    },
                                    select(code) {
                                        form.currency = code;
                                        this.open = false;
                                    }
                                }" @click.outside="open = false" @keydown.escape.window="open = false">
                                    
                                    {{-- Hidden Input for form submission --}}
                                    <input type="hidden" name="currency" :value="form.currency">

                                    {{-- Trigger Button (Reusing Category Dropdown Trigger Pattern) --}}
                                    <button type="button" 
                                            x-ref="currencyDropdownBtn"
                                            @click="toggle($refs.currencyDropdownBtn)"
                                            class="flex items-center gap-1 px-2.5 py-2.5 sm:px-3 sm:py-3 bg-zinc-50 dark:bg-zinc-800/70 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-black text-xs border-e border-zinc-200 dark:border-zinc-700/80 transition-colors select-none cursor-pointer rounded-s-xl focus:outline-none shrink-0"
                                            :class="open ? 'text-primary' : ''"
                                            aria-label="{{ __('products.currency') }}"
                                            aria-haspopup="listbox"
                                            :aria-expanded="open">
                                        <span x-text="form.currency"></span>
                                        <svg class="w-3.5 h-3.5 text-zinc-400 shrink-0 transition-transform duration-200" 
                                             :class="open ? 'rotate-180 text-primary' : ''" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    {{-- Dropdown Popover (Smart Upward / Downward positioning) --}}
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-200 transform"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-150 transform"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute start-0 z-50 min-w-[190px] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl p-1.5 space-y-1"
                                         :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                                         style="display: none;">
                                        <template x-for="curr in currencies" :key="curr.code">
                                            <button type="button" 
                                                    @click="select(curr.code)"
                                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-bold transition-colors text-start cursor-pointer"
                                                    :class="form.currency === curr.code 
                                                        ? 'bg-primary/10 text-primary dark:bg-primary/20' 
                                                        : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60'">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-extrabold" x-text="curr.code"></span>
                                                    <span class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500" x-text="activeLocale === 'ar' ? curr.name_ar : curr.name_en"></span>
                                                </div>
                                                <template x-if="form.currency === curr.code">
                                                    <svg class="w-3.5 h-3.5 text-primary shrink-0 ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </template>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       id="base_price" 
                                       dir="ltr"
                                       x-model="form.base_price" 
                                       :placeholder="getPlaceholder('base_price')" 
                                       class="w-full min-w-0 bg-transparent px-2.5 py-2.5 sm:px-3 sm:py-3 text-xs sm:text-xs xl:text-sm font-bold outline-none border-0 focus:ring-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 text-start [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            </div>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 ps-1 font-semibold text-start">
                                {{ __('products.base_price_help') }}
                            </p>
                        </div>

                        {{-- MOQ (Clean Inline Icon) --}}
                        <div class="space-y-1.5">
                            <label for="moq" class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.moq') }}
                            </label>
                            <div class="relative rounded-xl border bg-white dark:bg-zinc-900/50 shadow-sm transition-all duration-200 border-zinc-200 dark:border-white/[0.08] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30">
                                <svg class="w-4 h-4 text-zinc-400 dark:text-zinc-500 absolute start-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <input type="number" 
                                       step="1" 
                                       min="1" 
                                       id="moq" 
                                       x-model="form.moq" 
                                       :placeholder="getPlaceholder('moq')" 
                                       class="w-full bg-transparent ps-10 pe-3.5 py-3 text-xs sm:text-sm font-bold outline-none border-0 focus:ring-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 text-start">
                            </div>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 ps-1 font-semibold text-start">
                                {{ __('products.moq_help') }}
                            </p>
                        </div>

                        {{-- SKU (Clean Inline Barcode Icon) --}}
                        <div class="space-y-1.5">
                            <label for="sku" class="block text-xs font-extrabold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-start">
                                {{ __('products.sku') }}
                            </label>
                            <div class="relative rounded-xl border bg-white dark:bg-zinc-900/50 shadow-sm transition-all duration-200 border-zinc-200 dark:border-white/[0.08] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30">
                                <svg class="w-4 h-4 text-zinc-400 dark:text-zinc-500 absolute start-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                <input type="text" 
                                       id="sku" 
                                       x-model="form.sku" 
                                       :placeholder="getPlaceholder('sku')" 
                                       class="w-full bg-transparent ps-10 pe-3.5 py-3 text-xs sm:text-sm font-bold outline-none border-0 focus:ring-0 text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 uppercase text-start">
                            </div>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 ps-1 font-semibold text-start">
                                {{ __('products.sku_help') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Form Actions (Submit & Discard) --}}
                <div id="tour-submit-section" class="flex items-center justify-between gap-4 pt-4 border-t border-zinc-200/60 dark:border-zinc-800">
                    <a href="{{ route('dashboard') }}" 
                       class="px-6 py-3 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        {{ __('products.cancel') }}
                    </a>

                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="px-8 py-3.5 rounded-xl bg-primary hover:bg-primary-light text-white text-xs sm:text-sm font-[800] shadow-lg shadow-primary/20 active:scale-95 transition-all flex items-center justify-center gap-2.5 disabled:opacity-60 disabled:pointer-events-none">
                        <template x-if="isSubmitting">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span x-text="isSubmitting ? '{{ __('products.saving') }}' : (isEdit ? '{{ __('products.update_product') }}' : '{{ __('products.save_product') }}')"></span>
                    </button>
                </div>
            </form>
        </div>

        {{-- ═══ RIGHT COLUMN: LIVE PREVIEW PANEL (Items 1, 4, 5, 6, 7, 10) ═══ --}}
        <div class="lg:col-span-5 xl:col-span-5 lg:sticky lg:top-6" 
             style="position: -webkit-sticky; position: sticky; top: 1.5rem;"
             :class="mobileTab === 'form' ? 'hidden lg:block' : 'block'">
            
            <div class="sticky top-6 z-20" style="position: -webkit-sticky; position: sticky; top: 1.5rem;">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-3xl p-6 shadow-xl overflow-y-auto custom-scrollbar relative" style="max-height: calc(100vh - 5rem); overflow-y: auto;">
                
                {{-- Preview Header: Informative Active Locale Badge (Item 4: Fully Dynamic Localization) --}}
                <div class="flex items-center justify-between mb-5 border-b border-zinc-100 dark:border-zinc-800/80 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-[900] uppercase tracking-wider text-zinc-900 dark:text-zinc-100 text-start"
                                x-text="getPreviewString('live_preview')">
                            </h3>
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 font-semibold text-start"
                               x-text="getPreviewString('preview_buyer_view')">
                            </p>
                        </div>
                    </div>

                    {{-- Active Locale Badge Pill --}}
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/80 text-zinc-800 dark:text-zinc-200 shadow-sm transition-all">
                        <span class="text-xs" x-text="getLocaleFlag(activeLocale)"></span>
                        <span class="text-xs font-bold" x-text="getLocaleName(activeLocale)"></span>
                        <span class="text-[9px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500 ps-1" x-text="getLocaleDir(activeLocale).toUpperCase()"></span>
                    </div>
                </div>

                {{-- Product Preview Card (Mirrors Public Directory Card with Full Dark Mode & Dynamic I18n) --}}
                <div class="space-y-4">
                    {{-- Hero Media Display --}}
                    <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800/60 border border-zinc-200/60 dark:border-zinc-700/60">
                        <template x-if="getCoverImageUrl()">
                            <img :src="getCoverImageUrl()" 
                                 @click="openLightbox(getCoverImageIndex())"
                                 title="{{ __('products.view_mode_preview') }}"
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105 cursor-zoom-in">
                        </template>
                        <template x-if="!getCoverImageUrl()">
                            <div class="w-full h-full flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 gap-2 p-4 text-center">
                                <svg class="w-12 h-12 stroke-[1.25] text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-semibold" x-text="getPreviewString('no_images')"></span>
                            </div>
                        </template>

                        {{-- Floating Category Pill (Item 4: Dynamic Localization) --}}
                        <div class="absolute top-3 start-3">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md text-primary border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm"
                                  x-text="getSelectedCategoryName() || getPreviewString('preview_uncategorized')"></span>
                        </div>
                    </div>

                    {{-- Mini Thumbnail Selector Strip --}}
                    <template x-if="images.length > 1">
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 light-scrollbar">
                            <template x-for="(img, idx) in images" :key="idx">
                                <button type="button" 
                                        @click="previewCoverOverride = img.url"
                                        class="w-14 h-14 rounded-xl overflow-hidden border-2 shrink-0 transition-all cursor-pointer"
                                        :class="(previewCoverOverride === img.url || (!previewCoverOverride && img.isPrimary)) ? 'border-primary ring-2 ring-primary/20 scale-105' : 'border-zinc-200 dark:border-zinc-700 opacity-70 hover:opacity-100'">
                                    <img :src="img.url" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </template>

                    {{-- Product Title & Pricing Header --}}
                    <div>
                        <div class="flex items-baseline justify-between gap-2 mb-1">
                            {{-- Item 6: Non-italic title placeholder styling --}}
                            <h4 class="text-lg font-[900] tracking-tight leading-snug text-start transition-colors"
                                :class="form.name[activeLocale]?.trim() ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500'"
                                :dir="getLocaleDir(activeLocale)"
                                x-text="getPreviewTitle()"></h4>
                        </div>

                        {{-- Commercial Badges (Price, MOQ, SKU) --}}
                        <div class="flex flex-wrap items-center gap-2 pt-2">
                            {{-- Price Badge (Item 7: Distinct 'Price on Request' when empty, dynamic currency) --}}
                            <div class="px-3 py-1.5 rounded-xl bg-primary/[0.08] dark:bg-primary/[0.15] text-primary border border-primary/20 dark:border-primary/30 flex items-baseline gap-1 whitespace-nowrap">
                                <span class="text-base font-black whitespace-nowrap" x-text="getPreviewPrice()"></span>
                                <template x-if="form.base_price">
                                    <span class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 whitespace-nowrap">/ <span x-text="getPreviewUnit()"></span></span>
                                </template>
                            </div>

                            {{-- Pieces per package Badge (Packaging units: carton, box, set) --}}
                            <template x-if="['carton', 'box', 'set'].includes(form.unit) && form.units_per_package">
                                <div class="px-2.5 py-1.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-xs font-bold flex items-center gap-1.5 whitespace-nowrap shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span x-text="getPreviewPackageDetails()"></span>
                                </div>
                            </template>

                            {{-- MOQ Badge (Item 4: Dynamic I18n) --}}
                            <template x-if="form.moq">
                                <div class="px-3 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60 text-xs font-bold flex items-center gap-1.5">
                                    <span class="text-zinc-400 dark:text-zinc-500 font-semibold" x-text="getPreviewString('preview_moq')"></span>
                                    <span x-text="form.moq + ' ' + getPreviewUnit()"></span>
                                </div>
                            </template>

                            {{-- SKU Badge (Item 4: Dynamic I18n) --}}
                            <template x-if="form.sku">
                                <div class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-700/60 text-[10px] font-mono font-bold uppercase">
                                    <span x-text="getPreviewString('preview_sku') + ' ' + form.sku"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Short Description --}}
                    <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <p class="text-xs leading-relaxed font-medium text-start transition-colors"
                           :class="form.short_description[activeLocale]?.trim() ? 'text-zinc-600 dark:text-zinc-300' : 'text-zinc-400 dark:text-zinc-500'"
                           :dir="getLocaleDir(activeLocale)"
                           x-text="getPreviewShortDesc()"></p>
                    </div>

                    {{-- Technical Specifications Table / Chips (Item 4: Dynamic I18n) --}}
                    <template x-if="getActiveSpecifications().length > 0">
                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 space-y-2">
                            <h5 class="text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500 text-start"
                                x-text="getPreviewString('preview_specs_title')">
                            </h5>
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="(s, i) in getActiveSpecifications()" :key="i">
                                    <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/60 dark:border-zinc-700/60 text-start">
                                        <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-tight" x-text="s.name"></p>
                                        <p class="text-xs font-black text-zinc-800 dark:text-zinc-100 mt-0.5 truncate" x-text="s.value"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Mock Interactive Actions (Item 4: Dynamic I18n Buttons) --}}
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 grid grid-cols-2 gap-3">
                        <button type="button" 
                                class="w-full py-3 bg-primary text-white text-xs font-[800] rounded-xl shadow-md shadow-primary/20 flex items-center justify-center gap-1.5 cursor-default">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span x-text="getPreviewString('preview_contact_supplier')"></span>
                        </button>
                        <button type="button" 
                                class="w-full py-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 cursor-default transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-text="getPreviewString('preview_request_quote')"></span>
                        </button>
                    </div>

                    {{-- Admin Approval Note (Item 5: SVG Icon instead of emoji, Item 4: Dynamic I18n) --}}
                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
                        <div class="flex items-center justify-center gap-2 p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/60 dark:border-zinc-700/60 text-zinc-500 dark:text-zinc-400 text-center">
                            <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-[11px] font-semibold leading-snug" x-text="getPreviewString('status_pending_notice')"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    {{-- Fullscreen Image Gallery Lightbox (Item 10) --}}
    <div x-show="lightbox.open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closeLightbox()"
         @keydown.arrow-right.window="lightbox.open && (getLocaleDir(activeLocale) === 'rtl' ? prevLightboxImage() : nextLightboxImage())"
         @keydown.arrow-left.window="lightbox.open && (getLocaleDir(activeLocale) === 'rtl' ? nextLightboxImage() : prevLightboxImage())"
         class="fixed inset-0 z-[10000] flex flex-col justify-between p-4 sm:p-6 bg-white dark:bg-zinc-950 select-none"
         style="display: none;">
        
        {{-- Lightbox Top Header Bar --}}
        <div class="w-full max-w-6xl mx-auto flex items-center justify-between py-2 shrink-0 border-b border-zinc-200/80 dark:border-zinc-800">
            <div class="flex items-center gap-3">
                <span class="text-xs sm:text-sm font-black text-zinc-900 dark:text-zinc-100 tracking-wider">
                    <span x-text="lightbox.currentIndex + 1"></span> / <span x-text="images.length"></span>
                </span>
                <template x-if="getCurrentLightboxImage()?.isPrimary">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-primary text-white shadow-sm">
                        {{ __('products.cover_image') }}
                    </span>
                </template>
            </div>

            <button type="button" 
                    @click="closeLightbox()"
                    class="w-10 h-10 rounded-2xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 flex items-center justify-center transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Lightbox Center Display with Navigation Buttons --}}
        <div class="relative flex-1 w-full max-w-6xl mx-auto flex items-center justify-center py-4 min-h-0">
            <template x-if="images.length > 1">
                <button type="button" 
                        @click="prevLightboxImage()"
                        class="absolute start-2 sm:start-4 z-10 w-12 h-12 rounded-2xl bg-white/90 dark:bg-zinc-900/90 hover:bg-white dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 shadow-xl flex items-center justify-center transition-all hover:scale-110 active:scale-95">
                    <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </template>

            <template x-if="getCurrentLightboxImage()">
                <img :src="getCurrentLightboxImage().url" 
                     class="max-h-full max-w-full object-contain rounded-2xl shadow-2xl border border-zinc-200/50 dark:border-zinc-800 transition-all duration-300">
            </template>

            <template x-if="images.length > 1">
                <button type="button" 
                        @click="nextLightboxImage()"
                        class="absolute end-2 sm:end-4 z-10 w-12 h-12 rounded-2xl bg-white/90 dark:bg-zinc-900/90 hover:bg-white dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 shadow-xl flex items-center justify-center transition-all hover:scale-110 active:scale-95">
                    <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </template>
        </div>

        {{-- Lightbox Bottom Thumbnails Strip --}}
        <div class="w-full max-w-6xl mx-auto py-2 shrink-0 border-t border-zinc-200/80 dark:border-zinc-800 overflow-x-auto light-scrollbar flex items-center justify-center gap-2">
            <template x-for="(img, idx) in images" :key="idx">
                <button type="button" 
                        @click="lightbox.currentIndex = idx"
                        class="w-14 h-14 rounded-xl overflow-hidden border-2 shrink-0 transition-all cursor-pointer"
                        :class="lightbox.currentIndex === idx ? 'border-primary ring-2 ring-primary/30 scale-105' : 'border-zinc-200 dark:border-zinc-700 opacity-60 hover:opacity-100'">
                    <img :src="img.url" class="w-full h-full object-cover">
                </button>
            </template>
        </div>
    </div>

    {{-- Onboarding Tour for Product Creation --}}
    @include('users.products.tour', ['page' => 'create'])

@push('scripts')
<script>
function productForm(config) {
    return {
        isEdit: config.isEdit || false,
        productId: config.productId || null,
        submitUrl: config.submitUrl || '',
        activeLocale: config.defaultLocale || 'en',
        mobileTab: 'form',
        isDragging: false,
        isSubmitting: false,
        previewCoverOverride: null,
        errors: [],
        categories: config.categories || [],
        placeholders: config.placeholders || {},
        previewI18n: config.previewI18n || {},
        lightbox: {
            open: false,
            currentIndex: 0
        },
        toast: {
            visible: false,
            message: '',
            type: 'success'
        },

        form: {
            category_id: config.initialCategory || '',
            unit: config.initialUnit || 'piece',
            units_per_package: config.initialUnitsPerPackage || '',
            sku: config.initialSku || '',
            moq: config.initialMoq || '',
            base_price: config.initialBasePrice || '',
            currency: config.initialCurrency || 'EGP',
            name: Object.assign({ en: '', ar: '', es: '', de: '', tr: '', zh: '' }, config.initialNames || {}),
            short_description: Object.assign({ en: '', ar: '', es: '', de: '', tr: '', zh: '' }, config.initialShortDesc || {}),
            full_description: Object.assign({ en: '', ar: '', es: '', de: '', tr: '', zh: '' }, config.initialFullDesc || {}),
        },

        specifications: config.initialSpecs && config.initialSpecs.length > 0 
            ? config.initialSpecs 
            : [{ name: { en: '', ar: '', es: '', de: '', tr: '', zh: '' }, value: { en: '', ar: '', es: '', de: '', tr: '', zh: '' } }],

        images: config.initialImages || [],
        newImageFiles: [],
        units: config.units || [],

        init() {
            // If editing and has images, ensure first has primary set
            if (this.images.length > 0 && !this.images.some(i => i.isPrimary)) {
                this.images[0].isPrimary = true;
            }
        },

        localesInfo: {
            ar: { name: '{{ __('products.arabic') }}', flag: '🇸🇦', dir: 'rtl' },
            en: { name: '{{ __('products.english') }}', flag: '🇬🇧', dir: 'ltr' },
            es: { name: '{{ __('products.spanish') }}', flag: '🇪🇸', dir: 'ltr' },
            de: { name: '{{ __('products.german') }}', flag: '🇩🇪', dir: 'ltr' },
            tr: { name: '{{ __('products.turkish') }}', flag: '🇹🇷', dir: 'ltr' },
            zh: { name: '{{ __('products.chinese') }}', flag: '🇨🇳', dir: 'ltr' },
        },

        getLocaleFlag(locale) {
            return this.localesInfo[locale]?.flag || '🌐';
        },

        getLocaleName(locale) {
            return this.localesInfo[locale]?.name || (locale ? locale.toUpperCase() : '');
        },

        getLocaleDir(locale) {
            return locale === 'ar' ? 'rtl' : 'ltr';
        },

        getPlaceholder(key) {
            if (this.placeholders && this.placeholders[this.activeLocale] && this.placeholders[this.activeLocale][key]) {
                return this.placeholders[this.activeLocale][key];
            }
            if (this.placeholders && this.placeholders['en'] && this.placeholders['en'][key]) {
                return this.placeholders['en'][key];
            }
            return '';
        },

        hasContentForLocale(locale) {
            return Boolean(
                (this.form.name[locale] && this.form.name[locale].trim() !== '') ||
                (this.form.short_description[locale] && this.form.short_description[locale].trim() !== '')
            );
        },

        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.processFiles(files);
            event.target.value = '';
        },

        handleFileDrop(event) {
            this.isDragging = false;
            const files = Array.from(event.dataTransfer.files).filter(f => f.type.startsWith('image/'));
            this.processFiles(files);
        },

        processFiles(files) {
            const maxAllowed = 10 - this.images.length;
            if (maxAllowed <= 0) {
                this.showToast('{{ __('products.max_images_reached') }}', 'error');
                return;
            }

            const accepted = files.slice(0, maxAllowed);
            accepted.forEach((file) => {
                const previewUrl = URL.createObjectURL(file);
                const isPrimary = this.images.length === 0;
                
                this.images.push({
                    id: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
                    file: file,
                    url: previewUrl,
                    isPrimary: isPrimary,
                    isExisting: false
                });

                this.newImageFiles.push(file);
            });
        },

        setPrimaryImage(index) {
            this.images.forEach((img, i) => {
                img.isPrimary = (i === index);
            });
            this.previewCoverOverride = this.images[index].url;
        },

        removeImage(index) {
            const removed = this.images.splice(index, 1)[0];
            if (removed && removed.file) {
                const fIdx = this.newImageFiles.indexOf(removed.file);
                if (fIdx > -1) this.newImageFiles.splice(fIdx, 1);
            }
            if (this.previewCoverOverride === removed?.url) {
                this.previewCoverOverride = null;
            }
            if (removed?.isPrimary && this.images.length > 0) {
                this.images[0].isPrimary = true;
            }
        },

        openLightbox(index) {
            this.lightbox.currentIndex = typeof index === 'number' ? index : 0;
            this.lightbox.open = true;
            document.body.style.overflow = 'hidden';
        },

        closeLightbox() {
            this.lightbox.open = false;
            document.body.style.overflow = '';
        },

        nextLightboxImage() {
            if (this.images.length > 0) {
                this.lightbox.currentIndex = (this.lightbox.currentIndex + 1) % this.images.length;
            }
        },

        prevLightboxImage() {
            if (this.images.length > 0) {
                this.lightbox.currentIndex = (this.lightbox.currentIndex - 1 + this.images.length) % this.images.length;
            }
        },

        getCurrentLightboxImage() {
            return this.images[this.lightbox.currentIndex] || null;
        },

        getCoverImageIndex() {
            if (this.previewCoverOverride) {
                const idx = this.images.findIndex(i => i.url === this.previewCoverOverride);
                if (idx > -1) return idx;
            }
            const pIdx = this.images.findIndex(i => i.isPrimary);
            return pIdx > -1 ? pIdx : 0;
        },

        getPreviewString(key) {
            if (this.previewI18n && this.previewI18n[this.activeLocale] && this.previewI18n[this.activeLocale][key]) {
                return this.previewI18n[this.activeLocale][key];
            }
            if (this.previewI18n && this.previewI18n['en'] && this.previewI18n['en'][key]) {
                return this.previewI18n['en'][key];
            }
            return '';
        },

        getCoverImageUrl() {
            if (this.previewCoverOverride) return this.previewCoverOverride;
            const primary = this.images.find(i => i.isPrimary);
            if (primary) return primary.url;
            if (this.images.length > 0) return this.images[0].url;
            return null;
        },

        getSelectedCategoryName() {
            if (!this.form.category_id) return '';
            const found = this.categories.find(c => c.id == this.form.category_id);
            if (!found) return '';
            if (typeof found.name === 'object' && found.name !== null) {
                return found.name[this.activeLocale] || found.name['en'] || Object.values(found.name)[0] || '';
            }
            return found.name || '';
        },

        getPreviewTitle() {
            const val = this.form.name[this.activeLocale];
            if (val && val.trim() !== '') {
                return val.trim();
            }
            return this.getPreviewString('preview_untitled');
        },

        getPreviewUnit() {
            if (!this.form.unit) return this.getPreviewString('unit_piece');
            const found = this.units.find(u => u.key === this.form.unit);
            if (found && found.label) {
                return found.label[this.activeLocale] || found.label['en'] || this.form.unit;
            }
            return this.form.unit;
        },

        getPreviewPrice() {
            if (!this.form.base_price || this.form.base_price === '') {
                return this.getPreviewString('preview_price_on_request');
            }
            const num = parseFloat(this.form.base_price);
            if (isNaN(num)) return this.getPreviewString('preview_price_on_request');
            const formatted = Number(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (this.form.currency === 'USD') {
                return '$' + formatted;
            }
            const symbol = this.activeLocale === 'ar' ? 'ج.م' : 'EGP';
            return formatted + ' ' + symbol;
        },

        getPreviewPackageDetails() {
            if (!this.form.units_per_package || !['carton', 'box', 'set'].includes(this.form.unit)) {
                return '';
            }
            const pieceWord = this.activeLocale === 'ar' ? 'قطعة' : 'pcs';
            return `${this.form.units_per_package} ${pieceWord} / ${this.getPreviewUnit()}`;
        },

        getPreviewShortDesc() {
            const val = this.form.short_description[this.activeLocale];
            if (val && val.trim() !== '') {
                return val.trim();
            }
            return this.getPreviewString('preview_no_desc');
        },

        addSpecification() {
            this.specifications.push({
                name: { en: '', ar: '', es: '', de: '', tr: '', zh: '' },
                value: { en: '', ar: '', es: '', de: '', tr: '', zh: '' }
            });
        },

        removeSpecification(index) {
            this.specifications.splice(index, 1);
        },

        getActiveSpecifications() {
            return this.specifications
                .filter(s => {
                    const key = s.name[this.activeLocale];
                    return key && key.trim() !== '';
                })
                .map(s => ({
                    name: s.name[this.activeLocale].trim(),
                    value: (s.value[this.activeLocale] && s.value[this.activeLocale].trim() !== '') ? s.value[this.activeLocale].trim() : '-'
                }));
        },

        showToast(message, type = 'success') {
            this.toast.message = message;
            this.toast.type = type;
            this.toast.visible = true;
            setTimeout(() => {
                this.toast.visible = false;
            }, 5000);
        },

        async submitForm() {
            this.errors = [];
            
            // Client-side required checks: English name and short_description are required
            if (!this.form.name.en || this.form.name.en.trim() === '') {
                // If user entered name in another locale, populate en as fallback
                const fallbackLocale = Object.keys(this.form.name).find(l => this.form.name[l] && this.form.name[l].trim() !== '');
                if (fallbackLocale) {
                    this.form.name.en = this.form.name[fallbackLocale];
                } else {
                    this.errors.push('{{ __('products.missing_en_warning') }}');
                    this.activeLocale = 'en';
                    this.showToast('{{ __('products.missing_en_warning') }}', 'error');
                    return;
                }
            }

            if (!this.form.short_description.en || this.form.short_description.en.trim() === '') {
                const fallbackLocale = Object.keys(this.form.short_description).find(l => this.form.short_description[l] && this.form.short_description[l].trim() !== '');
                if (fallbackLocale) {
                    this.form.short_description.en = this.form.short_description[fallbackLocale];
                } else {
                    this.errors.push('{{ __('products.missing_en_warning') }}');
                    this.activeLocale = 'en';
                    this.showToast('{{ __('products.missing_en_warning') }}', 'error');
                    return;
                }
            }

            if (!this.form.unit || this.form.unit.trim() === '') {
                this.errors.push('{{ __('products.unit_required') }}');
                this.showToast('{{ __('products.unit_required') }}', 'error');
                return;
            }

            this.isSubmitting = true;

            try {
                const formData = new FormData();
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) formData.append('_token', csrfToken);

                if (this.isEdit) {
                    formData.append('_method', 'PUT');
                }

                if (this.form.category_id) formData.append('category_id', this.form.category_id);
                formData.append('unit', this.form.unit);
                if (this.form.units_per_package && ['carton', 'box', 'set'].includes(this.form.unit)) {
                    formData.append('units_per_package', this.form.units_per_package);
                }
                if (this.form.sku) formData.append('sku', this.form.sku);
                if (this.form.moq) formData.append('moq', this.form.moq);
                if (this.form.base_price) formData.append('base_price', this.form.base_price);
                formData.append('currency', this.form.currency || 'EGP');

                // Multi-locale translatable arrays
                ['en', 'ar', 'es', 'de', 'tr', 'zh'].forEach(locale => {
                    if (this.form.name[locale] && this.form.name[locale].trim() !== '') {
                        formData.append(`name[${locale}]`, this.form.name[locale]);
                    }
                    if (this.form.short_description[locale] && this.form.short_description[locale].trim() !== '') {
                        formData.append(`short_description[${locale}]`, this.form.short_description[locale]);
                    }
                    if (this.form.full_description[locale] && this.form.full_description[locale].trim() !== '') {
                        formData.append(`full_description[${locale}]`, this.form.full_description[locale]);
                    }
                });

                // Specifications
                let specIndex = 0;
                this.specifications.forEach(spec => {
                    const hasEnName = spec.name.en && spec.name.en.trim() !== '';
                    const anyName = Object.values(spec.name).find(v => v && v.trim() !== '');
                    
                    if (hasEnName || anyName) {
                        const enVal = hasEnName ? spec.name.en : anyName;
                        formData.append(`specifications[${specIndex}][name][en]`, enVal);

                        ['ar', 'es', 'de', 'tr', 'zh'].forEach(locale => {
                            if (spec.name[locale] && spec.name[locale].trim() !== '') {
                                formData.append(`specifications[${specIndex}][name][${locale}]`, spec.name[locale]);
                            }
                        });

                        ['en', 'ar', 'es', 'de', 'tr', 'zh'].forEach(locale => {
                            if (spec.value[locale] && spec.value[locale].trim() !== '') {
                                formData.append(`specifications[${specIndex}][value][${locale}]`, spec.value[locale]);
                            }
                        });

                        specIndex++;
                    }
                });

                // Images: order so that the designated primary image is index 0
                const newImgs = this.images.filter(i => !i.isExisting && i.file);
                const sortedNewImgs = [...newImgs].sort((a, b) => (b.isPrimary ? 1 : 0) - (a.isPrimary ? 1 : 0));
                sortedNewImgs.forEach(img => {
                    formData.append('images[]', img.file);
                });

                const response = await fetch(this.submitUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    const redirectUrl = result.redirect_url || '{{ route('products.index') }}';
                    try {
                        sessionStorage.setItem('alidebo_flash', JSON.stringify({
                            message: result.message || (this.isEdit ? '{{ __('products.success_updated') }}' : '{{ __('products.success_created') }}'),
                            type: 'success'
                        }));
                    } catch (e) {}
                    window.location.href = redirectUrl;
                } else if (response.status === 422) {
                    if (result.errors) {
                        const errList = [];
                        Object.keys(result.errors).forEach(key => {
                            result.errors[key].forEach(msg => errList.push(msg));
                        });
                        this.errors = errList;
                        this.showToast(errList[0] || '{{ __('products.validation_error') }}', 'error');
                    } else {
                        this.errors = [result.message || '{{ __('products.validation_error') }}'];
                        this.showToast(result.message || '{{ __('products.validation_error') }}', 'error');
                    }
                } else {
                    this.showToast(result.message || result.error || '{{ __('products.server_error') }}', 'error');
                }
            } catch (err) {
                console.error(err);
                this.showToast('{{ __('products.network_error') }}', 'error');
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endpush
