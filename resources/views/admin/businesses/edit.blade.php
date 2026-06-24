@extends('admin.layouts.admin')
@section('title', __('admin.edit_business'))

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 pb-32 mt-4">
    <style>
        .custom-smart-dropdown.is-active { z-index: 9999 !important; }
        .group\/card.has-active-dropdown { z-index: 9998 !important; overflow: visible !important; }
        .input-group.has-active-dropdown { z-index: 9999 !important; position: relative !important; }
        #businesses-form-section { overflow: visible !important; }
        #form-content-placeholder { overflow: visible !important; }
        /* ── RTL/LTR Enhanced Alignment ── */
        [dir="rtl"] #businessForm input,
        [dir="rtl"] #businessForm textarea,
        [dir="rtl"] #businessForm .dropdown-trigger,
        [dir="rtl"] #businessForm .dropdown-label {
            text-align: right !important;
        }
        [dir="ltr"] #businessForm input,
        [dir="ltr"] #businessForm textarea,
        [dir="ltr"] #businessForm .dropdown-trigger,
        [dir="ltr"] #businessForm .dropdown-label {
            text-align: left !important;
        }
        /* Standardize placeholders & alignment */
        #businessForm input::placeholder,
        #businessForm textarea::placeholder {
            text-align: inherit;
            opacity: 0.5;
            transition: opacity 0.3s;
        }
        #businessForm input:focus::placeholder,
        #businessForm textarea:focus::placeholder {
            opacity: 0.3;
        }
    </style>
    {{-- ═══════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 reveal-item">
        <div class="space-y-2 text-start">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-none">
                {{ __('admin.edit_business') }}
            </h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.25em] opacity-70">
                {{ __('admin.identity_subtitle') }}
            </p>
        </div>
        <a href="{{ route('admin.businesses.index') }}"
            class="group flex items-center gap-3 px-7 py-3.5 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-white/5 text-slate-500 dark:text-zinc-400 hover:text-primary hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 active:scale-95">
            <i aria-hidden="true" class="fa-solid fa-arrow-left text-[10px] ltr:group-hover:-translate-x-1 rtl:group-hover:translate-x-1 transition-transform"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">{{ __('admin.back_to_list') }}</span>
        </a>
    </div>
    <form id="businessForm" onsubmit="saveBusiness(event)" class="relative space-y-12" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="businessId" value="{{ $business->id }}">
        {{-- ═══════════════════════════════════════════════
             SECTION 1 — IDENTITY
        ═══════════════════════════════════════════════ --}}
        <div class="relative bg-white dark:bg-zinc-900/40 border border-slate-200/60 dark:border-white/5  shadow-sm reveal-item group/card hover:shadow-md transition-shadow duration-500">
            {{-- Gradient accent line --}}
            <div class="absolute top-0 inset-x-0 h-[3px] ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-primary via-primary/60 to-transparent rounded-t-3xl"></div>
            <div class="p-8 pt-10">
                {{-- Section Header --}}
                <div class="flex items-center gap-5 mb-10 border-b border-slate-100 dark:border-white/5 pb-7">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary/15 to-primary/5 text-primary flex items-center justify-center shrink-0 shadow-inner ring-1 ring-primary/10">
                            <i aria-hidden="true" class="fa-solid fa-fingerprint text-lg"></i>
                        </div>
                        <span class="absolute -top-2 ltr:-right-2 rtl:-left-2 w-6 h-6 rounded-full bg-primary text-white text-[9px] font-black flex items-center justify-center shadow-lg shadow-primary/30">01</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest">{{ __('admin.identity_title') }}</h3>
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">{{ __('admin.basic_info_desc') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-7">
                    {{-- Business Name --}}
                    <div class="input-group">
                        <label for="field_name" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                            {{ __('admin.business_name') }} <span class="text-red-400" aria-hidden="true">*</span>
                        </label>
                        <div class="relative flex items-center group">
                            <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 pointer-events-none transition-colors group-focus-within:text-primary z-10">
                                <i class="fa-solid fa-building text-sm"></i>
                            </div>
                            <input type="text" name="name" id="field_name" required aria-required="true"
                                   oninput="updateLiveUrl(this.value); validateFieldRealtime(this)"
                                   placeholder="{{ __('admin.business_name_placeholder') }}"
                                   class="w-full bg-slate-50/80 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 focus:border-primary focus:ring-4 focus:ring-primary/15 rounded-2xl text-[15px] font-semibold text-slate-800 dark:text-white py-4 ps-14 pe-5 transition-all duration-300 outline-none validate-target placeholder:text-slate-400 dark:placeholder:text-zinc-500 hover:border-slate-300 dark:hover:border-white/15 hover:shadow-sm focus:shadow-md focus:shadow-primary/5">
                            <div class="validation-icon absolute end-4 top-1/2 -translate-y-1/2 opacity-0 text-base pointer-events-none transition-all"></div>
                        </div>
                        <p class="field-error text-[10px] font-bold text-red-500 mt-1.5 hidden" id="error_name"></p>
                    </div>
                    {{-- Category --}}
                    <div class="input-group">
                        <label id="category_label" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                            {{ __('admin.category') }} <span class="text-red-400" aria-hidden="true">*</span>
                        </label>
                        <div class="relative custom-smart-dropdown" id="dropdown_category">
                            <div class="relative flex items-center group">
                                <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 z-10 pointer-events-none transition-colors group-focus-within:text-primary">
                                    <i class="fa-solid fa-tags text-sm"></i>
                                </div>
                                <input type="hidden" name="category_id" id="field_category_id" class="validate-target" value="{{ $business->category_id }}">
                                <button type="button" class="dropdown-trigger w-full bg-slate-50/80 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 hover:border-primary/40 hover:shadow-sm focus:border-primary focus:ring-4 focus:ring-primary/15 rounded-2xl text-[15px] font-semibold py-4 ps-14 pe-5 flex items-center justify-between transition-all duration-300 outline-none cursor-pointer">
                                    <span class="dropdown-label text-slate-400 dark:text-zinc-600 truncate">{{ __('admin.choose_category') }}</span>
                                    <i aria-hidden="true" class="fa-solid fa-chevron-down text-[10px] text-slate-300 dark:text-zinc-600 transition-transform duration-300"></i>
                                </button>
                            </div>
                            {{-- Dropdown Menu --}}
                            <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden opacity-0 translate-y-2 transition-all duration-300">
                                <div class="p-3 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]">
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute start-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" class="dropdown-search w-full bg-white dark:bg-zinc-900 border-none rounded-xl py-2 ps-9 ltr:pr-4 rtl:pl-4 text-sm font-bold outline-none focus:ring-2 focus:ring-primary/20 text-slate-700 dark:text-zinc-200" placeholder="{{ __('admin.search') }}">
                                    </div>
                                </div>
                                <div class="dropdown-list max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                    @foreach($categories as $category)
                                        <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" 
                                             data-id="{{ $category->id }}" data-label="{{ $category->name_en }} / {{ $category->name_ar }}">
                                            {{ $category->name_en }} / {{ $category->name_ar }}
                                        </div>
                                    @endforeach
                                    <div class="no-results-msg hidden p-6 text-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-white/[0.02] flex items-center justify-center mx-auto mb-3">
                                            <i class="fa-solid fa-magnifying-glass text-slate-300 dark:text-zinc-600"></i>
                                        </div>
                                        <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.no_results_found') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Description --}}
                    <div class="input-group lg:col-span-2">
                        <label for="field_description" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                            {{ __('admin.description') }}
                        </label>
                        <div class="relative group">
                            <textarea name="description" id="field_description" rows="4" placeholder="{{ __('admin.description_placeholder') }}"
                                      class="w-full bg-slate-50/80 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 focus:border-primary focus:ring-4 focus:ring-primary/15 focus:shadow-md focus:shadow-primary/5 rounded-2xl text-[15px] font-semibold text-slate-800 dark:text-white py-4 px-5 transition-all duration-300 outline-none resize-none placeholder:text-slate-400 dark:placeholder:text-zinc-500 validate-target hover:border-slate-300 dark:hover:border-white/15 hover:shadow-sm"
                                      oninput="validateFieldRealtime(this)"></textarea>
                            {{-- Character Counter --}}
                            <div class="absolute bottom-4 end-5 flex items-center gap-2 pointer-events-none">
                                <span id="description_counter" class="text-[10px] font-black tracking-widest text-slate-400 transition-colors">0 / 500</span>
                            </div>
                        </div>
                        <p class="field-error text-[10px] font-bold text-red-500 mt-1.5 hidden" id="error_description"></p>
                    </div>
                    <div class="lg:col-span-2 flex items-center">
                        {{-- Live URL Preview Box --}}
                        <div id="live_url_preview" class="hidden w-full px-5 py-4 bg-gradient-to-br from-slate-50 to-slate-100/50 dark:from-white/[0.03] dark:to-white/[0.01] rounded-2xl border border-slate-100 dark:border-white/5 shadow-inner">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2 text-primary/60">
                                    <i aria-hidden="true" class="fa-solid fa-link text-[10px]"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('admin.live_custom_url') }}</span>
                                </div>
                                <span class="text-[13px] font-medium text-slate-400 dark:text-zinc-500 truncate block w-full overflow-hidden">
                                    {{ url('/') }}/<span id="live_slug" class="text-primary font-bold"></span>
                                </span>
                            </div>
                            <p id="slug_status_text" class="text-[11px] font-bold mt-2 hidden"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- ═══════════════════════════════════════════════
             SECTION 2 — LOCATION & CONTACT
        ═══════════════════════════════════════════════ --}}
        <div class="relative bg-white dark:bg-zinc-900/40 border border-slate-200/60 dark:border-white/5  shadow-sm reveal-item group/card hover:shadow-md transition-shadow duration-500">
            {{-- Gradient accent line --}}
            <div class="absolute top-0 inset-x-0 h-[3px] ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-emerald-500 via-emerald-400/60 to-transparent rounded-t-3xl"></div>
            <div class="p-8 pt-10">
                <div class="flex items-center gap-5 mb-10 border-b border-slate-100 dark:border-white/5 pb-7">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500/15 to-emerald-500/5 text-emerald-500 flex items-center justify-center shrink-0 shadow-inner ring-1 ring-emerald-500/10">
                            <i aria-hidden="true" class="fa-solid fa-map-location-dot text-lg"></i>
                        </div>
                        <span class="absolute -top-2 ltr:-right-2 rtl:-left-2 w-6 h-6 rounded-full bg-emerald-500 text-white text-[9px] font-black flex items-center justify-center shadow-lg shadow-emerald-500/30">02</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest">{{ __('admin.location') }} & {{ __('admin.contact') }}</h3>
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">{{ __('admin.regional_data_desc') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-7">
                    {{-- Country Picker --}}
                    <div class="input-group">
                        <label id="country_label" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                            {{ __('admin.country') }} <span class="text-red-400" aria-hidden="true">*</span>
                        </label>
                        <div class="relative custom-smart-dropdown" id="dropdown_country">
                            <div class="relative flex items-center group">
                                <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 z-10 pointer-events-none transition-colors group-focus-within:text-emerald-500">
                                    <i class="fa-solid fa-globe text-sm"></i>
                                </div>
                                <input type="hidden" name="country_id" id="field_country_id" class="validate-target" value="{{ $business->country_id }}">
                                <button type="button" class="dropdown-trigger w-full bg-slate-50/80 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 hover:border-emerald-500/40 hover:shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 rounded-2xl text-[15px] font-semibold py-4 ps-14 pe-5 flex items-center justify-between transition-all duration-300 outline-none cursor-pointer">
                                    <span class="dropdown-label text-slate-400 dark:text-zinc-600 truncate">{{ __('admin.select_country') }}</span>
                                    <i aria-hidden="true" class="fa-solid fa-chevron-down text-[10px] text-slate-300 dark:text-zinc-600 transition-transform duration-300"></i>
                                </button>
                            </div>
                            {{-- Dropdown Menu --}}
                            <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden opacity-0 translate-y-2 transition-all duration-300">
                                <div class="p-3 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]">
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute start-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" class="dropdown-search w-full bg-white dark:bg-zinc-900 border-none rounded-xl py-2 ps-9 ltr:pr-4 rtl:pl-4 text-sm font-bold outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-700 dark:text-zinc-200" placeholder="{{ __('admin.search') }}">
                                    </div>
                                </div>
                                <div class="dropdown-list max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                    @foreach($countries as $country)
                                        <div class="dropdown-item p-3 rounded-xl hover:bg-emerald-500/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" 
                                             data-id="{{ $country->id }}" data-label="{{ $country->name_en }} / {{ $country->name_ar }}">
                                            {{ $country->name_en }} / {{ $country->name_ar }}
                                        </div>
                                    @endforeach
                                    <div class="no-results-msg hidden p-6 text-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-white/[0.02] flex items-center justify-center mx-auto mb-3">
                                            <i class="fa-solid fa-magnifying-glass text-slate-300 dark:text-zinc-600"></i>
                                        </div>
                                        <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.no_results_found') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- City Picker --}}
                    <div class="input-group">
                        <label id="city_label" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                            {{ __('admin.city') }} <span class="text-red-400" aria-hidden="true">*</span>
                        </label>
                        <div class="relative custom-smart-dropdown" id="dropdown_city">
                            <div class="relative flex items-center group">
                                <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 z-10 pointer-events-none transition-colors group-focus-within:text-emerald-500">
                                    <i class="fa-solid fa-city text-sm"></i>
                                </div>
                                <input type="hidden" name="city_id" id="field_city_id" class="validate-target" value="{{ $business->city_id }}">
                                <button type="button" class="dropdown-trigger w-full bg-slate-50/80 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 hover:border-emerald-500/40 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 rounded-2xl text-[15px] font-semibold py-4 ps-14 pe-5 flex items-center justify-between transition-all duration-300 outline-none opacity-40 pointer-events-none text-start cursor-pointer">
                                    <span class="dropdown-label text-slate-400 dark:text-zinc-600 truncate">{{ __('admin.select_country_first') }}</span>
                                    <i aria-hidden="true" class="fa-solid fa-chevron-down text-[10px] text-slate-300 dark:text-zinc-600 animate-pulse"></i>
                                </button>
                            </div>
                            {{-- Dropdown Menu (Populated dynamically) --}}
                            <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden opacity-0 translate-y-2 transition-all duration-300">
                                <div class="p-3 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]">
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute start-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" class="dropdown-search w-full bg-white dark:bg-zinc-900 border-none rounded-xl py-2 ps-9 ltr:pr-4 rtl:pl-4 text-sm font-bold outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-700 dark:text-zinc-200" placeholder="{{ __('admin.search') }}">
                                    </div>
                                </div>
                                <div class="dropdown-list max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                    <div class="p-4 text-center text-slate-400 dark:text-zinc-600 text-[10px] font-bold uppercase tracking-widest">
                                        {{ __('admin.select_country_first') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Address --}}
                    <div class="input-group lg:col-span-2">
                        <label for="field_address" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">{{ __('admin.address') }}</label>
                        <div class="relative flex items-center group">
                            <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 pointer-events-none transition-colors group-focus-within:text-emerald-500">
                                <i class="fa-solid fa-location-dot text-sm"></i>
                            </div>
                            <input type="text" name="address" id="field_address" placeholder="{{ __('admin.address_placeholder') }}"
                                   class="w-full bg-slate-50/80 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 focus:shadow-md focus:shadow-emerald-500/5 rounded-2xl text-[15px] font-semibold text-slate-800 dark:text-white py-4 ps-14 pe-5 transition-all duration-300 outline-none placeholder:text-slate-400 dark:placeholder:text-zinc-500 hover:border-slate-300 dark:hover:border-white/15 hover:shadow-sm text-start">
                        </div>
                        <p class="field-error text-[10px] font-bold text-red-500 mt-1.5 hidden" id="error_address"></p>
                    </div>
                    {{-- Divider --}}
                    <div class="lg:col-span-2 flex items-center gap-4 my-1">
                        <div class="flex-1 h-px ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-transparent via-slate-200 dark:via-white/5 to-transparent"></div>
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-300 dark:text-zinc-600">{{ __('admin.contact') }}</span>
                        <div class="flex-1 h-px ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-transparent via-slate-200 dark:via-white/5 to-transparent"></div>
                    </div>
                    {{-- Phone --}}
                    <div class="input-group">
                        <label for="field_phone" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                            {{ __('admin.phone') }} <span class="text-red-400" aria-hidden="true">*</span>
                        </label>
                        <div class="flex items-center group w-full bg-slate-50/80 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/15 focus-within:shadow-md focus-within:shadow-emerald-500/5 rounded-2xl transition-all duration-300 hover:border-slate-300 dark:hover:border-white/15 hover:shadow-sm overflow-hidden">
                            <div class="w-14 h-14 flex items-center justify-center text-slate-400 pointer-events-none z-10 transition-colors group-focus-within:text-emerald-500 shrink-0 border-e border-slate-100 dark:border-white/5 bg-slate-100/30 dark:bg-white/[0.02]">
                                <i class="fa-solid fa-phone text-sm"></i>
                            </div>
                            <input type="tel" name="phone" id="field_phone" required aria-required="true" inputmode="tel" placeholder="{{ __('admin.phone_placeholder', ['default' => '+1 234 567 8900']) }}"
                                   oninput="validateFieldRealtime(this)"
                                   class="validate-target flex-1 bg-transparent border-none py-4 px-5 outline-none text-[15px] font-semibold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 focus:ring-0">
                            <div class="validation-icon opacity-0 text-base pointer-events-none transition-all me-4 shrink-0"></div>
                        </div>
                        <p class="field-error text-[10px] font-bold text-red-500 mt-1.5 hidden" id="error_phone"></p>
                    </div>
                    {{-- Whatsapp --}}
                    <div class="input-group">
                        <label for="field_whatsapp" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">{{ __('admin.whatsapp') }}</label>
                        <div class="flex items-center group w-full bg-slate-50/80 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 focus-within:border-green-500 focus-within:ring-4 focus-within:ring-green-500/15 focus-within:shadow-md focus-within:shadow-green-500/5 rounded-2xl transition-all duration-300 hover:border-slate-300 dark:hover:border-white/15 hover:shadow-sm overflow-hidden">
                            <div class="w-14 h-14 flex items-center justify-center text-green-500 pointer-events-none z-10 transition-colors shrink-0 border-e border-green-500/10 bg-green-500/[0.02]">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </div>
                            <input type="tel" name="whatsapp" id="field_whatsapp" inputmode="tel" placeholder="{{ __('admin.phone_placeholder', ['default' => '+1 234 567 8900']) }}"
                                   oninput="validateFieldRealtime(this)"
                                   class="validate-target flex-1 bg-transparent border-none py-4 px-5 outline-none text-[15px] font-semibold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 focus:ring-0">
                        </div>
                        <p class="field-error text-[10px] font-bold text-red-500 mt-1.5 hidden" id="error_whatsapp"></p>
                    </div>
                    {{-- Website --}}
                    <div class="input-group lg:col-span-2">
                        <label for="field_website" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">{{ __('admin.website_url') }}</label>
                        <div class="flex items-center group lg:w-1/2 bg-slate-50/80 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/15 focus-within:shadow-md focus-within:shadow-emerald-500/5 rounded-2xl transition-all duration-300 hover:border-slate-300 dark:hover:border-white/15 hover:shadow-sm overflow-hidden">
                            <div class="w-14 h-14 flex items-center justify-center text-slate-400 pointer-events-none z-10 transition-colors group-focus-within:text-emerald-500 shrink-0 border-e border-slate-100 dark:border-white/5 bg-slate-100/30 dark:bg-white/[0.02]">
                                <i class="fa-solid fa-link text-sm"></i>
                            </div>
                            <input type="url" name="website" id="field_website" inputmode="url" placeholder="{{ __('admin.website_placeholder', ['default' => 'https://example.com']) }}"
                                   class="validate-target flex-1 bg-transparent border-none py-4 px-5 outline-none text-[15px] font-semibold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 focus:ring-0">
                        </div>
                        <p class="field-error text-[10px] font-bold text-red-500 mt-1.5 hidden" id="error_website"></p>
                    </div>
                </div>
            </div>
        </div>
        {{-- ═══════════════════════════════════════════════
             SECTION 3 — SOCIAL MEDIA
        ═══════════════════════════════════════════════ --}}
        <div class="relative bg-white dark:bg-zinc-900/40 border border-slate-200/60 dark:border-white/5  shadow-sm reveal-item group/card hover:shadow-md transition-shadow duration-500">
            {{-- Gradient accent line --}}
            <div class="absolute top-0 inset-x-0 h-[3px] ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-violet-500 via-violet-400/60 to-transparent rounded-t-3xl"></div>
            <div class="p-8 pt-10">
                <div class="flex items-center justify-between mb-10 border-b border-slate-100 dark:border-white/5 pb-7">
                    <div class="flex items-center gap-5">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500/15 to-violet-500/5 text-violet-500 flex items-center justify-center shrink-0 shadow-inner ring-1 ring-violet-500/10">
                                <i aria-hidden="true" class="fa-solid fa-share-nodes text-lg"></i>
                            </div>
                            <span class="absolute -top-2 ltr:-right-2 rtl:-left-2 w-6 h-6 rounded-full bg-violet-500 text-white text-[9px] font-black flex items-center justify-center shadow-lg shadow-violet-500/30">03</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest">{{ __('admin.social_media') }}</h3>
                            <p class="text-[11px] text-slate-400 mt-1 font-medium">{{ __('admin.optional') }}</p>
                        </div>
                    </div>
                </div>
                <!-- Active Social Media Fields -->
                <div id="dynamic-socials-wrapper" class="space-y-4 mb-8 empty:mb-0">
                    <!-- JS injects active social fields here -->
                </div>
                <!-- Platform Picker — Always Visible Chip Grid -->
                <div id="social-platforms-grid">
                    <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-4">{{ __('admin.add_social_account') }}</p>
                    <div class="flex flex-wrap gap-2.5" id="social-picker-chips">
                        <!-- JS renders clickable platform chips here -->
                    </div>
                </div>
                <!-- All platforms used message -->
                <div id="all-socials-used" class="hidden mt-4">
                    <div class="flex items-center gap-3 px-5 py-4 bg-violet-50/50 dark:bg-violet-500/5 rounded-2xl border border-violet-100 dark:border-violet-500/10">
                        <i class="fa-solid fa-check-double text-violet-500 text-sm"></i>
                        <span class="text-[11px] font-bold text-violet-600 dark:text-violet-400">{{ __('admin.all_platforms_in_use') }}</span>
                    </div>
                </div>
            </div>
        </div>
        {{-- ═══════════════════════════════════════════════
             SECTION 4 — VISUALS & GALLERY
        ═══════════════════════════════════════════════ --}}
        <div class="relative bg-white dark:bg-zinc-900/40 border border-slate-200/60 dark:border-white/5  shadow-sm reveal-item group/card hover:shadow-md transition-shadow duration-500">
            {{-- Gradient accent line --}}
            <div class="absolute top-0 inset-x-0 h-[3px] ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-purple-500 via-purple-400/60 to-transparent rounded-t-3xl"></div>
            <div class="p-8 pt-10">
                <div class="flex items-center gap-5 mb-10 border-b border-slate-100 dark:border-white/5 pb-7">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500/15 to-purple-500/5 text-purple-500 flex items-center justify-center shrink-0 shadow-inner ring-1 ring-purple-500/10">
                            <i aria-hidden="true" class="fa-solid fa-palette text-lg"></i>
                        </div>
                        <span class="absolute -top-2 ltr:-right-2 rtl:-left-2 w-6 h-6 rounded-full bg-purple-500 text-white text-[9px] font-black flex items-center justify-center shadow-lg shadow-purple-500/30">04</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest">{{ __('admin.visuals_title') }}</h3>
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">{{ __('admin.visuals_desc') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                    {{-- Logo upload --}}
                    <div class="input-group">
                        <label for="logo_upload" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-3 block text-start">{{ __('admin.business_logo') }}</label>
                        <div class="relative h-[220px]  border-2 border-dashed border-slate-200 dark:border-white/10 hover:border-purple-400/50 flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-50/80 to-slate-100/30 dark:from-white/[0.02] dark:to-white/[0.01] group/drop transition-all duration-500 hover:from-purple-50/50 hover:to-purple-100/20 dark:hover:from-purple-500/[0.03] dark:hover:to-purple-500/[0.01] shrink-0 shadow-inner">
                            <img id="logo-preview-inplace" src="" class="absolute inset-0 w-full h-full object-contain hidden p-6 z-10 transition-transform duration-500 group-hover/drop:scale-105">
                            <div id="logo-placeholder-inplace" class="text-center space-y-3 relative z-0">
                                <div class="w-16 h-16 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center mx-auto transition-all duration-500 group-hover/drop:-translate-y-2 group-hover/drop:scale-110 shadow-sm ring-1 ring-purple-500/5">
                                    <i aria-hidden="true" class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest block text-slate-400 group-hover/drop:text-purple-400 transition-colors">{{ __('admin.click_to_upload') }}</span>
                                <span class="text-[9px] font-medium text-slate-300 dark:text-zinc-600 block">{{ __('admin.image_formats') }}</span>
                            </div>
                            <input type="file" name="logo" id="logo_upload" accept="image/*" onchange="previewImageInPlace(this, 'logo-preview-inplace', 'logo-placeholder-inplace')" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                        </div>
                        <p class="field-error text-[10px] font-bold text-red-500 mt-1.5 hidden" id="error_logo"></p>
                    </div>
                    {{-- Cover upload --}}
                    <div class="input-group">
                        <label for="cover_upload" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-3 block text-start">{{ __('admin.business_cover') }}</label>
                        <div class="relative h-[220px]  border-2 border-dashed border-slate-200 dark:border-white/10 hover:border-purple-400/50 flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-50/80 to-slate-100/30 dark:from-white/[0.02] dark:to-white/[0.01] group/drop transition-all duration-500 hover:from-purple-50/50 hover:to-purple-100/20 dark:hover:from-purple-500/[0.03] dark:hover:to-purple-500/[0.01] shrink-0 shadow-inner">
                            <img id="cover-preview-inplace" src="" class="absolute inset-0 w-full h-full object-cover hidden z-10 transition-transform duration-500 group-hover/drop:scale-105">
                            <div id="cover-placeholder-inplace" class="text-center space-y-3 relative z-0">
                                <div class="w-16 h-16 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center mx-auto transition-all duration-500 group-hover/drop:-translate-y-2 group-hover/drop:scale-110 shadow-sm ring-1 ring-purple-500/5">
                                    <i aria-hidden="true" class="fa-solid fa-image text-2xl"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest block text-slate-400 group-hover/drop:text-purple-400 transition-colors">{{ __('admin.click_to_upload') }}</span>
                                <span class="text-[9px] font-medium text-slate-300 dark:text-zinc-600 block">{{ __('admin.cover_dimension_desc') }}</span>
                            </div>
                            <input type="file" name="cover" id="cover_upload" accept="image/*" onchange="previewImageInPlace(this, 'cover-preview-inplace', 'cover-placeholder-inplace')" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                        </div>
                        <p class="field-error text-[10px] font-bold text-red-500 mt-1.5 hidden" id="error_cover"></p>
                    </div>
                </div>
                {{-- Gallery --}}
                <div class="space-y-6 lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-5">
                        <div class="flex items-center gap-3 text-purple-500">
                            <div class="w-8 h-8 rounded-xl bg-purple-500/10 flex items-center justify-center">
                                <i aria-hidden="true" class="fa-solid fa-images text-sm"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-start">{{ __('admin.premium_gallery') }}</span>
                        </div>
                        <div class="text-[10px] font-black text-slate-600 dark:text-zinc-300 bg-slate-100/80 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 px-4 py-2 rounded-full shadow-inner" id="gallery-count" data-template="{{ __('admin.gallery_count') }}">
                            {{ __('admin.gallery_count', ['current' => 0]) }}
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-6" id="gallery-preview-grid">
                        <div class="relative aspect-square  border-2 border-dashed border-slate-200 dark:border-white/10 flex items-center justify-center hover:border-purple-400/40 transition-all duration-500 cursor-pointer group/add bg-gradient-to-br from-slate-50/50 to-slate-100/20 dark:from-white/[0.02] dark:to-white/[0.01] hover:from-purple-50/30 hover:to-purple-100/10">
                            <div class="text-center space-y-2 z-0">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 group-hover/add:bg-purple-500/10 flex items-center justify-center mx-auto transition-all duration-300">
                                    <i aria-hidden="true" class="fa-solid fa-plus text-slate-300 dark:text-zinc-600 text-lg group-hover/add:text-purple-400 group-hover/add:scale-110 group-hover/add:rotate-90 transition-all duration-300"></i>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-widest block text-slate-400 group-hover/add:text-purple-400 transition-colors">{{ __('admin.add_new') }}</span>
                            </div>
                            <input type="file" name="gallery[]" multiple accept="image/*" aria-label="{{ __('admin.premium_gallery') }}" onchange="handleGalleryUpload(this)" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        </div>
                        <p class="field-error text-[10px] font-bold text-red-500 mt-1.5 hidden" id="error_gallery"></p>
                    </div>
                </div>
            </div>
        </div>
        {{-- ═══════════════════════════════════════════════
             SUBMIT & PUBLISH
        ═══════════════════════════════════════════════ --}}
        <div class="flex flex-col items-center justify-center gap-10 py-16 reveal-item">
            <div class="flex items-center gap-4 w-full px-8">
                <div class="flex-1 h-px ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-transparent via-slate-200 dark:via-white/5 to-transparent"></div>
                <div class="text-[10px] font-black text-slate-300 dark:text-zinc-600 uppercase ltr:tracking-[0.3em]">{{ __('admin.ready_to_publish') }}</div>
                <div class="flex-1 h-px ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-transparent via-slate-200 dark:via-white/5 to-transparent"></div>
            </div>
            <div class="flex flex-col items-center gap-6">
                <button type="submit" id="formSaveBtn"
                    class="group relative inline-flex items-center justify-center gap-3 px-16 py-5 ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-primary to-orange-600 hover:from-orange-500 hover:to-primary text-white rounded-full font-black text-[13px] uppercase ltr:tracking-[0.2em] shadow-2xl shadow-primary/30 hover:shadow-primary/50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 disabled:opacity-70 disabled:pointer-events-none overflow-hidden">
                    {{-- Shimmer --}}
                    <div class="absolute inset-0 ltr:-translate-x-full rtl:translate-x-full ltr:group-hover:translate-x-full rtl:group-hover:-translate-x-full transition-transform duration-1000 ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-transparent via-white/20 to-transparent"></div>
                    {{-- Content --}}
                    <div class="flex items-center gap-3 relative z-10" id="btn-content-wrapper">
                        <i aria-hidden="true" class="fa-solid fa-cloud-arrow-up text-sm transition-transform group-hover:scale-110"></i>
                        <span class="btn-text">{{ __('admin.update_business') }}</span>
                    </div>
                    {{-- Loader --}}
                    <div class="btn-loader absolute inset-0 flex items-center justify-center z-30 opacity-0 pointer-events-none">
                        <i class="fa-solid fa-circle-notch animate-spin text-xl"></i>
                    </div>
                </button>
                <p class="text-[10px] text-slate-400 font-medium text-center max-w-sm leading-relaxed">
                    {{ __('admin.submit_agreement_desc') }}
                </p>
                <a href="{{ route('admin.businesses.index') }}"
                    class="text-[10px] font-black text-slate-400 dark:text-zinc-600 uppercase ltr:tracking-widest hover:text-primary transition-colors py-2">
                    {{ __('admin.cancel_and_return') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Social Media Dynamic Script --}}
<script>
    /* ── Module-Specific Dropdown Extensions ── */
    if (window.SmartDropdownManager) {
        // Add city population logic specifically for the business form
        SmartDropdownManager.handleCountryChange = async function(countryId) {
            const cityDropdown = document.getElementById('dropdown_city');
            if (!cityDropdown) return;
            const cityList = cityDropdown.querySelector('.dropdown-list');
            const cityLabel = cityDropdown.querySelector('.dropdown-label');
            const cityTrigger = cityDropdown.querySelector('.dropdown-trigger');
            const hiddenCityInput = cityDropdown.querySelector('input[type="hidden"]');
            // Reset city state
            if (hiddenCityInput) hiddenCityInput.value = '';
            cityLabel.textContent = '{{ __("admin.loading") }}...';
            cityTrigger.classList.add('opacity-50', 'pointer-events-none');
            cityList.innerHTML = '<div class="p-4 text-center"><i class="fa-solid fa-spinner animate-spin text-primary/40"></i></div>';
            // Loading Pulse Animation
            gsap.to(cityDropdown, { scale: 1.01, duration: 0.1, repeat: 1, yoyo: true });
            try {
                const res = await fetch(`${window.AppConfig.adminUrl}/countries/${countryId}/cities`, { 
                    headers: { 'X-Requested-With': 'XMLHttpRequest' } 
                });
                const cities = await res.json();
                cityList.innerHTML = '';
                if (cities.length === 0) {
                    cityList.innerHTML = `
                        <div class="p-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-white/[0.02] flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-city text-slate-300 dark:text-zinc-600"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __("admin.no_cities_found") }}</p>
                        </div>
                    `;
                    cityLabel.textContent = '{{ __("admin.no_cities_found") }}';
                } else {
                    cities.forEach((city) => {
                        const item = document.createElement('div');
                        item.className = 'dropdown-item p-3 rounded-xl hover:bg-emerald-500/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors opacity-0';
                        item.dataset.id = city.id;
                        item.dataset.label = `${city.name_en} / ${city.name_ar}`;
                        item.textContent = `${city.name_en} / ${city.name_ar}`;
                        // We rely on delegation for selection now
                        cityList.appendChild(item);
                    });
                    // Add no-results message for search in cities
                    const noResults = document.createElement('div');
                    noResults.className = 'no-results-msg hidden p-6 text-center';
                    noResults.innerHTML = `
                        <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-white/[0.02] flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-magnifying-glass text-slate-300 dark:text-zinc-600"></i>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.no_results_found') }}</p>
                    `;
                    cityList.appendChild(noResults);
                    cityLabel.textContent = '{{ __("admin.choose_city") }}';
                    cityTrigger.classList.remove('opacity-50', 'pointer-events-none');
                    // Success Glow & Staggered fade-in
                    gsap.to(cityTrigger, { 
                        boxShadow: '0 0 15px rgba(16, 185, 129, 0.2)', 
                        borderColor: 'rgba(16, 185, 129, 0.4)',
                        duration: 0.4,
                        clearProps: 'all'
                    });
                    gsap.fromTo(cityList.querySelectorAll('.dropdown-item'), 
                        { opacity: 0, y: 10 }, 
                        { opacity: 1, y: 0, duration: 0.3, stagger: 0.05, ease: 'power2.out' }
                    );
                    const chevron = cityTrigger.querySelector('.fa-chevron-down');
                    if (chevron && typeof gsap !== 'undefined') {
                        chevron.classList.remove('animate-pulse');
                        chevron.classList.add('text-emerald-500');
                        gsap.fromTo(chevron, { scale: 0.5, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.4, ease: 'back.out(2)' });
                    }
                }
            } catch (err) {
                console.error('Fetch cities failed:', err);
                cityLabel.textContent = '{{ __("admin.error_loading") }}';
                gsap.fromTo(cityLabel, { x: -10 }, { x: 0, duration: 0.4, ease: 'elastic.out(1, 0.3)' });
            }
        }
    };
    // Initialize Smart Dropdowns
    SmartDropdownManager.init();
    // ── Bind Country-City Cascade (Required for Business Form) ──
    const dropdownCountryEl = document.getElementById('dropdown_country');
    if (dropdownCountryEl) {
        dropdownCountryEl.addEventListener('smart-select', (e) => {
            if (window.SmartDropdownManager && typeof SmartDropdownManager.handleCountryChange === 'function') {
                SmartDropdownManager.handleCountryChange(e.detail.id);
            }
        });
    }
    const availableSocials = [
        { id: 'facebook',  icon: 'fa-facebook-f',   colorHex: '#2563eb', label: "{{ __('admin.facebook') }}",  placeholder: "{{ __('admin.facebook_placeholder') }}" },
        { id: 'instagram', icon: 'fa-instagram',    colorHex: '#e1306c', label: "{{ __('admin.instagram') }}", placeholder: "{{ __('admin.instagram_placeholder') }}" },
        { id: 'twitter',   icon: 'fa-x-twitter',    colorHex: '#14171a', label: "{{ __('admin.twitter') }}",   placeholder: "{{ __('admin.twitter_placeholder') }}" },
        { id: 'tiktok',    icon: 'fa-tiktok',       colorHex: '#fe2c55', label: "{{ __('admin.tiktok') }}",    placeholder: "{{ __('admin.tiktok_placeholder') }}" },
        { id: 'linkedin',  icon: 'fa-linkedin-in',  colorHex: '#0a66c2', label: "{{ __('admin.linkedin') }}",  placeholder: "{{ __('admin.linkedin_placeholder') }}" },
        { id: 'youtube',   icon: 'fa-youtube',      colorHex: '#ff0000', label: "{{ __('admin.youtube') }}",   placeholder: "{{ __('admin.youtube_placeholder') }}" },
        { id: 'snapchat',  icon: 'fa-snapchat',     colorHex: '#fffc00', label: "{{ __('admin.snapchat') }}",  placeholder: "{{ __('admin.snapchat_placeholder') }}" },
    ];
    let usedSocials = [];
    /* ── Render Platform Chips ── */
    function renderPlatformChips() {
        const container = document.getElementById('social-picker-chips');
        const allUsedMsg = document.getElementById('all-socials-used');
        const gridSection = document.getElementById('social-platforms-grid');
        if (!container) return;
        container.innerHTML = '';
        const unused = availableSocials.filter(s => !usedSocials.includes(s.id));
        if (unused.length === 0) {
            gridSection.classList.add('hidden');
            allUsedMsg.classList.remove('hidden');
        } else {
            gridSection.classList.remove('hidden');
            allUsedMsg.classList.add('hidden');
        }
        unused.forEach(social => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.id = `social-chip-${social.id}`;
            chip.className = 'flex items-center gap-2.5 px-4 py-2.5 rounded-2xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-zinc-800/60 cursor-pointer transition-all duration-300';
            chip.innerHTML = `
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-300" style="background:${social.colorHex}14;">
                    <i class="fa-brands ${social.icon} text-[13px]" style="color:${social.colorHex};"></i>
                </div>
                <span class="text-[11px] font-bold text-slate-600 dark:text-zinc-300 whitespace-nowrap">${social.label}</span>
                <div class="w-5 h-5 rounded-md bg-slate-100 dark:bg-white/5 flex items-center justify-center transition-all duration-300" style="margin-inline-start:2px;">
                    <i class="fa-solid fa-plus text-[8px] text-slate-400 dark:text-zinc-500 transition-transform duration-300"></i>
                </div>
            `;
            chip.onmouseenter = () => {
                chip.style.borderColor = social.colorHex + '50';
                chip.style.boxShadow = `0 4px 20px ${social.colorHex}12`;
                chip.style.transform = 'translateY(-2px)';
                const plusIcon = chip.querySelector('.fa-plus');
                if (plusIcon) { plusIcon.style.transform = 'rotate(90deg)'; plusIcon.style.color = social.colorHex; }
                const plusBg = chip.querySelector('.fa-plus').parentElement;
                if (plusBg) plusBg.style.background = social.colorHex + '18';
            };
            chip.onmouseleave = () => {
                chip.style.borderColor = '';
                chip.style.boxShadow = '';
                chip.style.transform = '';
                const plusIcon = chip.querySelector('.fa-plus');
                if (plusIcon) { plusIcon.style.transform = ''; plusIcon.style.color = ''; }
                const plusBg = chip.querySelector('.fa-plus').parentElement;
                if (plusBg) plusBg.style.background = '';
            };
            chip.onclick = () => addSocialField(social.id);
            container.appendChild(chip);
        });
    }
    
    function renderSocialInput(id, val = '') {
        const social = availableSocials.find(s => s.id === id);
        const wrapper = document.getElementById('dynamic-socials-wrapper');
        const card = document.createElement('div');
        card.className = 'group transition-all duration-500 ease-out';
        card.id = `social-field-wrap-${id}`;
        card.innerHTML = `
            <div class="space-y-1.5">
                <div class="flex items-center gap-5 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-white/[0.06] ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-50/60 to-white dark:from-white/[0.015] dark:to-white/[0.025] hover:shadow-sm transition-all duration-300" style="border-inline-start:3px solid ${social.colorHex};">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background:${social.colorHex}12;">
                        <i class="fa-brands ${social.icon} text-lg" style="color:${social.colorHex};"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="field_${social.id}" class="text-[9px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest block mb-1 text-start">${social.label}</label>
                        <input type="url" name="${social.id}" id="field_${social.id}" inputmode="url" value="${val}" placeholder="${social.placeholder}" 
                               oninput="validateFieldRealtime(this)"
                               class="validate-target w-full bg-transparent border-0 p-0 text-[14px] font-semibold text-slate-800 dark:text-white outline-none placeholder:text-slate-300 dark:placeholder:text-zinc-600 focus:ring-0 focus:outline-none"
                               style="box-shadow:none;">
                    </div>
                    <button type="button" onclick="removeSocialField('${social.id}')" 
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-300 dark:text-zinc-600 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all duration-200 shrink-0 sm:opacity-0 sm:group-hover:opacity-100">
                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                    </button>
                    <div class="validation-icon opacity-0 w-5 h-5 flex items-center justify-center"></div>
                </div>
                <p class="field-error text-[10px] font-bold text-red-500 ps-5 hidden" id="error_${social.id}"></p>
            </div>
        `;
        wrapper.appendChild(card);
    }

    /* ── Add Social Field ── */
    function addSocialField(id) {
        if (usedSocials.includes(id)) return;
        usedSocials.push(id);
        renderSocialInput(id);
        renderPlatformChips();
    }
    /* ── Remove Social Field ── */
    function removeSocialField(id) {
        usedSocials = usedSocials.filter(s => s !== id);
        const card = document.getElementById(`social-field-wrap-${id}`);
        if (card) {
            card.classList.add('opacity-0', 'scale-95', '-translate-y-2');
            setTimeout(() => {
                card.remove();
                renderPlatformChips();
            }, 300);
        }
    }
    // Initialize on load
    renderPlatformChips();

    // --- Added back Javascript helpers from index.blade.php ---
    let slugCheckTimeout = null;
    window.updateLiveUrl = function(value) {
        const preview = document.getElementById('live_url_preview');
        const slugSpan = document.getElementById('live_slug');
        const statusText = document.getElementById('slug_status_text');
        if (!preview || !slugSpan) return;
        if (value.trim().length > 0) {
            preview.classList.remove('hidden');
            const slug = value.trim()
                .toLowerCase()
                .replace(/[^a-z0-9\u0600-\u06FF\s-]/g, '')
                .replace(/[\s-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugSpan.innerText = slug || '...';
            
            clearTimeout(slugCheckTimeout);
            slugCheckTimeout = setTimeout(async () => {
                if (!slug) return;
                try {
                    const res = await fetch(`/admin/businesses/check-slug?slug=${encodeURIComponent(slug)}&id={{ $business->id }}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    if (statusText) {
                        if (data.exists) {
                            statusText.innerText = '{{ __("admin.slug_taken") }}';
                            statusText.className = 'text-[10px] font-bold mt-1 text-red-400';
                            statusText.classList.remove('hidden');
                        } else {
                            statusText.innerText = '{{ __("admin.slug_available") }}';
                            statusText.className = 'text-[10px] font-bold mt-1 text-emerald-500';
                            statusText.classList.remove('hidden');
                        }
                    }
                } catch(e) {}
            }, 400);
        } else {
            preview.classList.add('hidden');
        }
    };

    window.validateFieldRealtime = function(input) {
        const group = input.closest('.input-group');
        const icon = group?.querySelector('.validation-icon');
        const errorText = group?.querySelector('.field-error');
        if (input.name === 'description') {
            const counter = document.getElementById('description_counter');
            if (counter) {
                const len = input.value.length;
                counter.innerText = `${len} / 500`;
                if (len < 75 || len > 500) {
                    counter.classList.add('text-red-500');
                    counter.classList.remove('text-slate-400', 'text-emerald-500');
                } else {
                    counter.classList.add('text-emerald-500');
                    counter.classList.remove('text-slate-400', 'text-red-500');
                }
            }
        }
        if (input.value.trim().length > 0) {
            if (icon) {
                icon.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500"></i>';
                if(typeof gsap !== 'undefined') gsap.to(icon, { opacity: 1, scale: 1, duration: 0.3, ease: 'back.out(2)' });
            }
            input.classList.remove('!border-red-500', 'has-error');
            input.classList.add('!border-emerald-400');
            if (errorText) {
                if(typeof gsap !== 'undefined') {
                    gsap.to(errorText, { opacity: 0, y: -5, duration: 0.2, onComplete: () => {
                        errorText.classList.add('hidden'); errorText.innerText = '';
                    }});
                } else {
                    errorText.classList.add('hidden'); errorText.innerText = '';
                }
            }
        } else {
            if (icon && typeof gsap !== 'undefined') gsap.to(icon, { opacity: 0, scale: 0.5, duration: 0.2 });
            input.classList.remove('!border-emerald-400');
        }
    };

    window.previewImageInPlace = function(input, previewId, placeholderId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                if(typeof gsap !== 'undefined') gsap.from(preview, { scale: 0.9, opacity: 0, duration: 0.4 });
            }
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.handleGalleryUpload = function(input) {
        if (!input.files || !input.files[0]) return;
        const grid = document.getElementById('gallery-preview-grid');
        const addButton = input.closest('.group\\/add');
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                const id = 'new-img-' + Date.now() + '-' + index;
                const div = document.createElement('div');
                div.className = 'group/media relative aspect-square rounded-[2rem] overflow-hidden border border-slate-100 dark:border-white/5 shadow-sm opacity-0 scale-90';
                div.id = id;
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover transition-transform duration-700 group-hover/media:scale-110">
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover/media:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                        <button type="button" aria-label="{{ __('admin.delete') }}" onclick="removeGalleryImage('${id}')" class="w-10 h-10 rounded-full bg-white/20 hover:bg-red-500/80 text-white flex items-center justify-center transition-all">
                            <i class="fa-solid fa-trash-can text-sm" aria-hidden="true"></i>
                        </button>
                    </div>
                `;
                grid.insertBefore(div, addButton);
                if(typeof gsap !== 'undefined') gsap.to(div, { opacity: 1, scale: 1, duration: 0.6, ease: 'expo.out' });
                else div.classList.remove('opacity-0', 'scale-90');
                updateGalleryCount();
            }
            reader.readAsDataURL(file);
        });
    };

    window.removeGalleryImage = function(id) {
        const el = document.getElementById(id);
        if(el) {
            if(typeof gsap !== 'undefined') {
                gsap.to(el, { opacity: 0, scale: 0.8, duration: 0.3, onComplete: () => { el.remove(); updateGalleryCount(); } });
            } else {
                el.remove();
                updateGalleryCount();
            }
        }
    };

    window.updateGalleryCount = function() {
        const grid = document.getElementById('gallery-preview-grid');
        if (!grid) return;
        const count = grid.querySelectorAll('.group\\/media').length || 0;
        const display = document.getElementById('gallery-count');
        if (display) {
            const template = display.getAttribute('data-template') || ':current';
            display.innerText = template.replace(':current', count);
        }
    };

    window.saveBusiness = async function(e) {
        e.preventDefault();
        const form = document.getElementById('businessForm');
        document.querySelectorAll('.field-error').forEach(el => { el.classList.add('hidden'); el.innerText = ''; });
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid', '!border-red-500'));
        
        const btn = document.getElementById('formSaveBtn');
        const loader = btn.querySelector('.btn-loader');
        const textWrapper = btn.querySelector('#btn-content-wrapper');
        
        if (btn) btn.disabled = true;
        if (loader) { loader.classList.remove('opacity-0'); loader.classList.add('opacity-100'); }
        if (textWrapper) textWrapper.classList.add('opacity-0');
        
        try {
            const formData = new FormData(form);
            const businessId = document.getElementById('businessId').value;
            const res = await fetch(`/admin/businesses/${businessId}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await res.json();
            if(res.ok && result.success) {
                if(typeof showToast !== 'undefined') showToast('success', result.message);
                setTimeout(() => window.location.href = '{{ route("admin.businesses.index") }}', 1000);
            } else {
                if(typeof showToast !== 'undefined') showToast('error', result.message || 'Validation Failed');
                if(result.errors) {
                    for(const [key, msg] of Object.entries(result.errors)) {
                        const errKey = key.replace('.', '_');
                        const errorEl = document.getElementById('error_' + errKey);
                        const input = document.getElementById('field_' + errKey) || document.getElementsByName(key)[0];
                        if(errorEl) {
                            errorEl.innerText = msg[0];
                            errorEl.classList.remove('hidden');
                        }
                        if(input) {
                            if (input.type === 'hidden' && input.closest('.custom-smart-dropdown')) {
                                input.closest('.custom-smart-dropdown').querySelector('.dropdown-trigger').classList.add('is-invalid', '!border-red-500');
                            } else if (input.type === 'file' && input.closest('.group\\/drop')) {
                                input.closest('.group\\/drop').classList.add('is-invalid', '!border-red-500');
                            } else {
                                input.classList.add('is-invalid', '!border-red-500');
                                const wrap = input.closest('.group');
                                if(wrap) wrap.classList.add('is-invalid', '!border-red-500');
                            }
                        }
                    }
                }
                if (btn) btn.disabled = false;
                if (loader) { loader.classList.add('opacity-0'); loader.classList.remove('opacity-100'); }
                if (textWrapper) textWrapper.classList.remove('opacity-0');
            }
        } catch(err) {
            console.error('Submit error', err);
            if(typeof showToast !== 'undefined') showToast('error', 'An error occurred during submission.');
            if (btn) btn.disabled = false;
            if (loader) { loader.classList.add('opacity-0'); loader.classList.remove('opacity-100'); }
            if (textWrapper) textWrapper.classList.remove('opacity-0');
        }
    };

    // --- Populate Edit Form Data ---
    document.addEventListener('DOMContentLoaded', () => {
        const business = {!! json_encode($business) !!};
        if (business) {
            document.getElementById('field_name').value = business.name || '';
            document.getElementById('field_description').value = business.description || '';
            document.getElementById('field_address').value = business.address || '';
            
            if (business.category_id) {
                document.getElementById('field_category_id').value = business.category_id;
                // find label from dropdown list
                const catItem = document.querySelector(`#dropdown_category .dropdown-item[data-id="${business.category_id}"]`);
                if (catItem) document.querySelector('#dropdown_category .dropdown-label').textContent = catItem.dataset.label;
            }

            if (business.contact_methods) {
                document.getElementById('field_phone').value = business.contact_methods.phone || '';
                document.getElementById('field_whatsapp').value = business.contact_methods.whatsapp || '';
                document.getElementById('field_website').value = business.contact_methods.website || '';
                
                // Add socials that were used
                ['facebook', 'instagram', 'twitter', 'tiktok', 'linkedin', 'youtube', 'snapchat'].forEach(platform => {
                    if (business.contact_methods[platform]) {
                        usedSocials.push(platform);
                        renderSocialInput(platform, business.contact_methods[platform]);
                    }
                });
                renderPlatformChips();
            }

            // Country/City requires cascading.
            if (business.city) {
                const countryId = business.city.country_id;
                document.getElementById('field_country_id').value = countryId;
                const countryItem = document.querySelector(`#dropdown_country .dropdown-item[data-id="${countryId}"]`);
                if (countryItem) document.querySelector('#dropdown_country .dropdown-label').textContent = countryItem.dataset.label;
                
                if (window.SmartDropdownManager && typeof SmartDropdownManager.handleCountryChange === 'function') {
                    // pre-load cities then select the right one
                    SmartDropdownManager.handleCountryChange(countryId).then(() => {
                        const cityList = document.getElementById('dropdown_city').querySelector('.dropdown-list');
                        const cityItem = cityList.querySelector(`.dropdown-item[data-id="${business.city_id}"]`);
                        if (cityItem) {
                            document.getElementById('field_city_id').value = business.city_id;
                            document.querySelector('#dropdown_city .dropdown-label').textContent = cityItem.dataset.label;
                        }
                    });
                }
            }

            // Preview Logo and Cover
            if (business.logo) {
                const logoPreview = document.getElementById('logo-preview-inplace');
                logoPreview.src = '/storage/' + business.logo;
                logoPreview.classList.remove('hidden');
                document.getElementById('logo-placeholder-inplace').classList.add('hidden');
            }
            if (business.cover) {
                const coverPreview = document.getElementById('cover-preview-inplace');
                coverPreview.src = '/storage/' + business.cover;
                coverPreview.classList.remove('hidden');
                document.getElementById('cover-placeholder-inplace').classList.add('hidden');
            }
            
            // Generate live url preview
            if (business.slug) updateLiveUrl(business.slug);
        }
    });
</script>
@endpush
