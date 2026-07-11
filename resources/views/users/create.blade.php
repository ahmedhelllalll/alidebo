@extends('users.layout')

@section('title', __('forms.business.setup_title') . ' | ' . (__('common.alidebo') ?? 'alidebo'))
@section('page_title', __('forms.business.setup_title'))

@section('content')
{{-- Premium Custom Styles --}}
@push('styles')
<style>
    .glass-morphism {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    .dark .glass-morphism {
        background: rgba(15, 15, 18, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.05);
    }
    .premium-shadow {
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.08);
    }
    .dark .premium-shadow {
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.8);
    }
    .step-line-active {
        background: linear-gradient(90deg, var(--color-primary) 0%, #fb923c 100%);
    }
    .input-premium {
        @apply bg-slate-50/70 dark:bg-zinc-900/60 border-slate-200/80 dark:border-white/[0.08] dark:text-zinc-100 transition-all duration-300 shadow-inner;
    }
    .input-premium:focus, .input-premium:focus-visible {
        @apply ring-2 ring-primary/40 border-primary bg-white dark:bg-zinc-900 outline-none shadow-md;
    }
    .social-btn {
        @apply w-full px-5 py-4 rounded-2xl border-2 border-transparent transition-all duration-300 flex items-center justify-between cursor-pointer font-bold text-sm bg-slate-50/50 dark:bg-zinc-900/40 hover:bg-slate-100 dark:hover:bg-zinc-800 focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none;
    }
    .social-btn.active {
        @apply shadow-md transform scale-[1.02] dark:bg-zinc-800/80;
    }
    .social-input-wrapper {
        @apply transition-all duration-500;
    }
    [x-cloak] { display: none !important; }
    .light-scrollbar::-webkit-scrollbar, .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    .light-scrollbar::-webkit-scrollbar-track, .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .light-scrollbar::-webkit-scrollbar-thumb, .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(244, 80, 24, 0.25);
        border-radius: 99px;
    }
    .dark .light-scrollbar::-webkit-scrollbar-thumb, .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(244, 80, 24, 0.15);
    }
    .light-scrollbar::-webkit-scrollbar-thumb:hover, .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(244, 80, 24, 0.5);
    }
    .scroll-snap-y {
        scroll-snap-type: y mandatory;
    }
    .scroll-snap-align-start {
        scroll-snap-align: start;
        scroll-margin-top: 8px;
    }

    /* ── Social Media Premium Identity & Spacing ── */
    .social-picker-chip {
        position: relative;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
        --brand-color: var(--hover-color);
    }
    .social-picker-chip:hover {
        border-color: var(--hover-color) !important;
        background-color: color-mix(in srgb, var(--hover-color) 8%, #ffffff) !important;
        box-shadow: 0 10px 25px -5px color-mix(in srgb, var(--hover-color) 20%, transparent),
                    0 8px 10px -6px color-mix(in srgb, var(--hover-color) 15%, transparent) !important;
        transform: translateY(-3px) scale(1.02);
    }
    .dark .social-picker-chip:hover {
        background-color: color-mix(in srgb, var(--hover-color) 12%, #18181b) !important;
    }
    
    /* Plus Button Hover State */
    .social-picker-chip .plus-btn {
        transition: all 0.3s ease;
    }
    .social-picker-chip:hover .plus-btn {
        background-color: var(--hover-color) !important;
    }
    .social-picker-chip .plus-icon {
        color: var(--brand-color);
        transition: all 0.3s ease;
    }
    .social-picker-chip:hover .plus-icon {
        color: #ffffff !important;
        transform: rotate(90deg);
    }

    /* Branded Active Input Cards */
    .social-active-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .social-active-card:focus, .social-active-card:focus-visible, .social-active-card:focus-within {
        border-color: var(--brand-color) !important;
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--brand-color) 12%, transparent) !important;
        background-color: #ffffff !important;
        outline: none !important;
    }
    .dark .social-active-card:focus, .dark .social-active-card:focus-visible, .dark .social-active-card:focus-within {
        background-color: #18181b !important;
    }
    .group\/card:focus-within .w-8.h-8 {
        transform: scale(1.05);
    }

    /* Class-based Brand Configurations */
    .brand-facebook {
        --hover-color: #1877F2 !important;
        --brand-color: #1877F2 !important;
    }
    .brand-instagram {
        --hover-color: #E1306C !important;
        --brand-color: #E1306C !important;
    }
    .brand-tiktok {
        --hover-color: #0f1419 !important;
        --brand-color: #0f1419 !important;
    }
    .dark .brand-tiktok {
        --hover-color: #ffffff !important;
        --brand-color: #ffffff !important;
    }
    .brand-linkedin {
        --hover-color: #0A66C2 !important;
        --brand-color: #0A66C2 !important;
    }
    .brand-twitter {
        --hover-color: #0f1419 !important;
        --brand-color: #0f1419 !important;
    }
    .dark .brand-twitter {
        --hover-color: #ffffff !important;
        --brand-color: #ffffff !important;
    }
    .brand-youtube {
        --hover-color: #FF0000 !important;
        --brand-color: #FF0000 !important;
    }
    .brand-telegram {
        --hover-color: #229ED9 !important;
        --brand-color: #229ED9 !important;
    }
    .brand-snapchat {
        --hover-color: #eab308 !important;
        --brand-color: #eab308 !important;
    }
    .dark .brand-snapchat {
        --hover-color: #FFFC00 !important;
        --brand-color: #FFFC00 !important;
    }

    .brand-tiktok:hover .plus-icon, .brand-twitter:hover .plus-icon {
        color: #ffffff !important;
    }
    .dark .brand-tiktok:hover .plus-icon, .dark .brand-twitter:hover .plus-icon {
        color: #000000 !important;
    }

    /* Fixed Contact Brand Configurations */
    .brand-phone {
        --hover-color: #10b981 !important;
        --brand-color: #10b981 !important;
    }
    .brand-whatsapp {
        --hover-color: #25D366 !important;
        --brand-color: #25D366 !important;
    }
    .brand-website {
        --hover-color: #0ea5e9 !important;
        --brand-color: #0ea5e9 !important;
    }
</style>
@endpush

<div class="relative w-full py-2 px-2 sm:px-4 h-full sm:h-[calc(100vh-170px)] min-h-[600px] flex flex-col overflow-hidden" x-data="setupWizard()" x-init="initGsap()">
    {{-- Global definitions for SVGs (like brand gradients) --}}
    <svg class="absolute w-0 h-0 opacity-0 pointer-events-none" aria-hidden="true">
        <defs>
            <!-- Instagram Gradient -->
            <radialGradient id="instagram-grad" cx="30%" cy="107%" r="130%" fx="30%" fy="107%">
                <stop offset="0%" stop-color="#fdf497"/>
                <stop offset="5%" stop-color="#fdf497"/>
                <stop offset="45%" stop-color="#fd5949"/>
                <stop offset="60%" stop-color="#d6249f"/>
                <stop offset="90%" stop-color="#285aeB"/>
            </radialGradient>
        </defs>
    </svg>

    {{-- Stepper UI --}}
    <div class="mb-6 mt-2 relative flex items-center justify-between px-2 gsap-stepper max-w-4xl mx-auto w-full shrink-0">
        <div class="absolute top-[20px] left-0 right-0 h-[2px] bg-slate-100 dark:bg-zinc-800/50 z-0 mx-8"></div>

        <template x-for="i in 5" :key="i">
            <div class="relative z-10 flex flex-col items-center group cursor-pointer" @click="goToStep(i)">
                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500 font-bold border-2 shadow-sm relative overflow-hidden"
                    :class="step > i ? 'bg-primary border-primary text-white scale-100' : (step === i ? 'bg-white border-primary text-primary dark:bg-zinc-900 scale-110 ring-8 ring-primary/10' : 'bg-white dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 text-slate-400 dark:text-zinc-600 scale-100')"
                >
                    <template x-if="step > i">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="step <= i">
                        <span class="text-sm" x-text="i"></span>
                    </template>
                </div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap transition-colors duration-300 animate-fade-in"
                     :class="step >= i ? 'text-primary' : 'text-slate-400 dark:text-zinc-600'">
                    {{ __('forms.business.step') }} <span x-text="i"></span>
                </div>
            </div>
        </template>
    </div>

    {{-- Unified Card Container --}}
    <div class="flex flex-col lg:flex-row lg:rtl:flex-row-reverse flex-1 overflow-hidden w-full max-w-6xl mx-auto glass-morphism premium-shadow rounded-[2rem] border border-white/20 dark:border-white/5 h-full">
        {{-- Visual Feedback (Left side / Left in RTL) --}}
        <div class="hidden lg:flex w-1/3 xl:w-[350px] shrink-0 bg-slate-50/20 dark:bg-zinc-900/30 border-r border-slate-100/50 dark:border-zinc-800/50 flex-col items-center justify-center overflow-hidden p-6 lg:rounded-l-[2rem]">
            <div class="w-full h-full flex items-center justify-center">
                <img x-show="step === 1" src="{{ asset('images/onboarding/welcome.svg') }}" class="w-full h-full object-contain filter drop-shadow-2xl" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <img x-show="step === 2 || step === 3" src="{{ asset('images/onboarding/category.svg') }}" class="w-full h-full object-contain filter drop-shadow-2xl" style="display: none;" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <img x-show="step === 4" src="{{ asset('images/onboarding/location.svg') }}" class="w-full h-full object-contain filter drop-shadow-2xl" style="display: none;" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <img x-show="step === 5" src="{{ asset('images/onboarding/contact.svg') }}" class="w-full h-full object-contain filter drop-shadow-2xl" style="display: none;" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            </div>
        </div>

        {{-- Main Form Container (Right side) --}}
        <div class="flex-1 p-4 sm:p-6 overflow-hidden relative flex flex-col h-full">
            <form action="{{ route('business.store') }}" method="POST" enctype="multipart/form-data" id="setupForm" class="relative flex-1 flex flex-col overflow-hidden">
                @csrf

                @if($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200/50 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 flex flex-col gap-2 font-medium text-sm shadow-sm relative z-20">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <strong>{{ __('forms.business.validation_error') ?? 'Please check the errors below:' }}</strong>
                    </div>
                    <ul class="list-disc list-inside px-8 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

        {{-- Step 1: Core Identity --}}
        <div x-show="step === 1" class="space-y-4 flex-1 flex flex-col justify-center gsap-step-1">

            <div class="flex flex-col gap-5 w-full px-2">
                {{-- Name Field --}}
                <div class="relative group block w-full">
                    <label for="business_name" class="block text-xs font-extrabold text-slate-700 dark:text-zinc-300 uppercase tracking-wider mb-2">{{ __('forms.business.biz_name') }} <span class="text-primary" aria-hidden="true">*</span></label>
                    <div class="relative flex items-stretch rounded-xl border bg-white dark:bg-zinc-900/50 overflow-hidden shadow-sm transition-all duration-300"
                         :class="fields.name.error && fields.name.touched ? 'border-rose-500 ring-2 ring-rose-500/20' : (fields.name.valid && fields.name.touched ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-white/[0.08] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30')">
                        <div class="flex items-center justify-center px-4 bg-primary text-white shrink-0" aria-hidden="true">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.015a2.993 2.993 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72M6.75 18h.008v.008H6.75V18zm0-3h.008v.008H6.75V15zm0-3h.008v.008H6.75V12zm0-3h.008v.008H6.75V9z" />
                            </svg>
                        </div>
                        <input type="text" id="business_name" name="name" x-model="fields.name.value" @input="validateField('name'); generateSlug()" @blur="fields.name.touched = true; validateField('name')" :aria-invalid="fields.name.error && fields.name.touched ? 'true' : 'false'" placeholder="{{ __('forms.business.biz_name_placeholder') }}" class="flex-1 bg-transparent px-4 py-3 text-sm font-bold outline-none border-0 focus:ring-0 text-slate-900 dark:text-zinc-100 placeholder:text-slate-400">
                    </div>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-400 mt-1.5 pl-1 font-semibold leading-relaxed" id="name_help">
                        {{ __('forms.business.biz_name_help') ?? (app()->getLocale() == 'ar' ? 'استخدم اسماً مميزاً وسهل التذكر لعملائك (3 أحرف على الأقل).' : 'Use a memorable name that represents your brand (min 3 chars).') }}
                    </p>
                    <div aria-live="polite">
                        <p class="text-[11px] font-bold text-rose-500 mt-1 min-h-[16px] pl-1 transition-all" x-show="fields.name.error && fields.name.touched" x-transition id="name_error">{{ __('forms.business.biz_name_error') }}</p>
                    </div>
                </div>

                {{-- Real-Time Slug Field --}}
                <div class="relative group block w-full">
                    <label for="business_slug" class="block text-xs font-extrabold text-slate-700 dark:text-zinc-300 uppercase tracking-wider mb-2">{{ __('forms.business.slug') ?? 'Business Slug URL' }} <span class="text-primary" aria-hidden="true">*</span></label>
                    <div class="relative flex items-stretch rounded-xl border bg-white dark:bg-zinc-900/50 overflow-hidden shadow-sm transition-all duration-300"
                         :class="fields.slug.error && fields.slug.touched ? 'border-rose-500 ring-2 ring-rose-500/20' : (fields.slug.valid && fields.slug.touched ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-white/[0.08] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/30')">
                        <div class="hidden sm:flex items-center justify-center px-4 bg-primary text-white shrink-0" aria-hidden="true">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253m0 0A17.919 17.919 0 0012 10.5c3.162 0 6.133.815 8.716 2.247m0 0c.185-.72.284-1.475.284-2.253z" />
                            </svg>
                        </div>
                        <div class="flex-1 flex items-center relative min-w-0" dir="ltr">
                            <span class="flex items-center px-2 sm:px-3 bg-slate-50 dark:bg-zinc-800/40 border-r border-slate-100 dark:border-white/[0.08] text-slate-400 dark:text-zinc-400 font-bold text-xs sm:text-sm select-none h-full whitespace-nowrap shrink-0">{{ str_replace(['http://', 'https://'], '', config('app.url')) }}/</span>
                            <input type="text" id="business_slug" name="slug" x-model="fields.slug.value" @input="fields.slug.value = fields.slug.value.toLowerCase().replace(/[^a-z0-9\u0600-\u06FF\-]+/g, '').replace(/-+/g, '-'); slugManuallyEdited = true; checkSlug();" :aria-invalid="fields.slug.error && fields.slug.touched ? 'true' : 'false'" placeholder="{{ __('forms.business.slug_placeholder') ?? 'elegance-studio' }}" class="flex-1 min-w-0 bg-transparent pl-2 sm:pl-4 pr-10 sm:pr-12 py-3 text-xs sm:text-sm font-bold outline-none border-0 focus:ring-0 text-slate-900 dark:text-zinc-100 placeholder:text-slate-400 truncate">
                            <div class="absolute right-3 sm:right-4 top-1/2 -translate-y-1/2 flex items-center gap-1.5 z-10 bg-white/80 dark:bg-zinc-900/80 px-1" aria-live="polite">
                                <svg x-show="fields.slug.checking" class="w-4 h-4 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <svg x-show="fields.slug.valid && !fields.slug.checking && fields.slug.touched" class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <svg x-show="fields.slug.error && !fields.slug.checking && fields.slug.touched" class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-400 mt-1.5 pl-1 font-semibold leading-relaxed" id="slug_help">
                        {{ __('forms.business.slug_help') ?? (app()->getLocale() == 'ar' ? 'هذا هو رابط ملفك التجاري الفريد الذي ستشاركه مع عملائك. يتم توليده تلقائياً.' : 'This is your unique URL that clients will visit. It is auto-generated.') }}
                    </p>
                    <div aria-live="polite">
                        <div x-show="fields.slug.error && !fields.slug.checking && fields.slug.touched" x-collapse>
                            <p class="text-[11px] font-bold text-rose-500 mt-1 pl-1" id="slug_error">{{ __('forms.business.slug_taken') ?? 'This URL is already taken.' }}</p>
                            <template x-if="fields.slug.suggestions.length > 0">
                                <div class="mt-2 bg-slate-50 dark:bg-zinc-900/50 p-2.5 rounded-xl border border-slate-100 dark:border-white/[0.05]">
                                    <p class="text-[10px] uppercase tracking-widest font-black text-slate-500 mb-1.5">{{ __('forms.business.slug_suggestions') ?? 'Available Suggestions:' }}</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="sugg in fields.slug.suggestions" :key="sugg">
                                            <button type="button" @click="fields.slug.value = sugg; slugManuallyEdited = true; checkSlug()" class="px-2.5 py-1 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg text-[11px] font-bold hover:border-primary hover:text-primary transition-all shadow-sm focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none" x-text="sugg"></button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2: Category Selection --}}
        <div x-show="step === 2" class="space-y-4 flex-1 flex flex-col justify-center gsap-step-2" style="display: none;">
            <div class="text-start px-2">
                <h2 class="text-2xl font-[800] text-slate-900 dark:text-white tracking-tight mb-1">{{ __('forms.business.category') }}</h2>
                <p class="text-slate-500 dark:text-zinc-500 text-sm font-medium">{{ __('forms.business.category_desc') }}</p>
            </div>

            <div class="space-y-4 px-2 w-full">
                {{-- Custom Category Active State Indicator / Input --}}
                <div x-show="showCustomCategory" x-collapse class="relative w-full">
                    <label for="custom_category_name" class="sr-only">{{ __('forms.business.custom_category_placeholder') ?? 'Enter your category name' }}</label>
                    <div class="flex items-center gap-3">
                        <input type="text" id="custom_category_name" name="custom_category_name" x-model="fields.custom_category_name.value" @input="validateField('category')" :aria-invalid="fields.category.error && fields.category.touched ? 'true' : 'false'" placeholder="{{ __('forms.business.custom_category_placeholder') ?? 'Enter your category name' }}" class="w-full input-premium rounded-2xl px-4 py-3.5 sm:px-6 sm:py-5 text-sm sm:text-base font-bold outline-none shadow-sm" :class="fields.category.error && fields.category.touched ? 'border-rose-500 ring-2 ring-rose-500/20 text-rose-600' : ''">
                        <button type="button" aria-label="Cancel Custom Category" @click="showCustomCategory = false; fields.custom_category_name.value = ''; searchCat = ''; validateField('category');" class="w-14 h-14 shrink-0 bg-slate-100 dark:bg-zinc-800 text-slate-500 hover:text-rose-500 rounded-2xl flex items-center justify-center transition-colors shadow-sm focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-400 mt-2 pl-1 font-semibold leading-relaxed">
                        {{ __('forms.business.custom_category_review_note') ?? 'Note: We will review the custom category name and add it to our system.' }}
                    </p>
                    <div aria-live="polite">
                        <p class="text-[11px] font-bold text-rose-500 mt-2 min-h-[16px] pl-1" x-show="fields.category.error && fields.category.touched" x-transition>{{ __('forms.business.category_error') }}</p>
                    </div>
                </div>

                {{-- Standard Categories Selector --}}
                <div class="w-full relative" x-show="!showCustomCategory" x-collapse>
                    {{-- Search Bar --}}
                    <div class="relative mb-4 w-full">
                        <label for="search_category" class="sr-only">{{ __('forms.business.search_category') }}</label>
                        <input type="text" id="search_category" x-model="searchCat" placeholder="{{ __('forms.business.search_category') }}" class="w-full input-premium rounded-2xl py-3.5 sm:py-4 pr-4 sm:pr-6 pl-10 sm:pl-12 rtl:pl-4 sm:rtl:pl-6 rtl:pr-10 sm:rtl:pr-12 text-sm sm:text-base font-bold outline-none shadow-sm" :class="fields.category.error && fields.category.touched ? 'border-rose-500 ring-2 ring-rose-500/20' : ''">
                        <svg class="absolute left-4 rtl:left-auto rtl:right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    {{-- Grid Scroll Container --}}
                    <div class="max-h-[226px] overflow-y-auto light-scrollbar scroll-snap-y px-2 pt-2 pb-6 relative z-10" role="group" aria-label="Categories">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                            @foreach($categories->unique('name') as $category)
                            <button type="button" 
                                    role="switch"
                                    :aria-checked="fields.category.value == {{ $category->id }} ? 'true' : 'false'"
                                    @click="selectCategory({{ $category->id }}, '{{ addslashes($category->name) }}', $event)" 
                                    x-show="searchCat === '' || '{{ mb_strtolower($category->name) }}'.includes(searchCat.toLowerCase())"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-90 translate-y-2"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-90 translate-y-2"
                                    data-name="{{ mb_strtolower($category->name) }}"
                                    class="cat-btn scroll-snap-align-start p-4 text-center text-xs font-bold rounded-2xl border transition-all flex flex-col items-center justify-center h-[90px] gap-2 cursor-pointer shadow-sm animate-fade-in focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none"
                                    :class="fields.category.value == {{ $category->id }} ? 'bg-primary/10 border-primary text-primary dark:bg-primary/20 ring-2 ring-primary/20 scale-[1.02]' : 'bg-slate-50/50 hover:bg-slate-100/70 border-slate-200/60 dark:bg-zinc-900/30 dark:border-zinc-800/80 dark:hover:bg-zinc-800/50 text-slate-700 dark:text-zinc-300 hover:border-primary/40 hover:text-primary'">
                                <span>{{ $category->name }}</span>
                            </button>
                            @endforeach

                            {{-- Category Not Found Empty State --}}
                            <div x-show="!hasCategoryMatches()" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="col-span-3 text-center py-8 px-4 bg-slate-50/50 dark:bg-zinc-900/30 rounded-2xl border border-slate-200/50 dark:border-zinc-800/80 shadow-inner">
                                <div class="w-10 h-10 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <p class="text-xs font-extrabold text-slate-800 dark:text-zinc-200 uppercase tracking-wider mb-1">{{ __('forms.business.category_not_found') }}</p>
                                <p class="text-[11px] text-slate-400 dark:text-zinc-500 font-semibold mb-4 leading-relaxed max-w-sm mx-auto">{{ __('forms.business.category_not_found_desc') }}</p>
                                <button type="button" @click="showCustomCategory = true; fields.category.value = null; searchCat = ''; validateField('category');" class="px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-[800] hover:bg-primary-light shadow-md shadow-primary/10 active:scale-95 transition-all">
                                    {{ __('forms.business.add_your_category') ?? 'Add your category' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Cannot Find Category Link --}}
                    <div class="text-center mt-4" x-show="hasCategoryMatches() && fields.category.value === null">
                        <button type="button" 
                                @click="showCustomCategory = true; fields.category.value = null; searchCat = ''; validateField('category');" 
                                class="text-xs font-bold text-primary hover:text-primary-light hover:underline transition-colors cursor-pointer">
                            {{ __('forms.business.cant_find_category') ?? 'I cannot find my category' }}
                        </button>
                    </div>
                    <input type="hidden" name="category_id" :value="fields.category.value">
                    <p class="text-[11px] font-bold text-rose-500 mt-2 min-h-[16px] pl-1" x-show="fields.category.error && fields.category.touched" x-transition>{{ __('forms.business.category_error') }}</p>
                </div>
            </div>
        </div>

        {{-- Step 3 - Sub-step 1: Description --}}
        <div x-show="step === 3 && mediaSubStep === 1" class="space-y-4 overflow-y-auto custom-scrollbar pr-2 flex-1 gsap-step-3-1" style="display: none;">
            <div class="text-start px-2">
                <h2 class="text-2xl font-[800] text-slate-900 dark:text-white tracking-tight mb-1">{{ __('forms.business.biz_desc') }}</h2>
                <p class="text-slate-500 dark:text-zinc-500 text-sm font-medium">{{ __('forms.business.biz_desc_placeholder') }}</p>
            </div>

            <div class="space-y-8 px-2">
                {{-- Description Field --}}
                <div class="relative w-full">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <label for="business_description" class="block text-xs font-extrabold text-slate-700 dark:text-zinc-300 uppercase tracking-wider">{{ __('forms.business.biz_desc') }} <span class="text-primary" aria-hidden="true">*</span></label>
                        </div>
                        <span class="text-[10px] font-black px-2.5 py-1 rounded-lg transition-all duration-300 border shadow-sm" 
                              aria-live="polite"
                              :class="{
                                  'text-slate-400 bg-slate-50 border-slate-100 dark:bg-zinc-800/50 dark:border-zinc-700/50': fields.description.value.length === 0,
                                  'text-rose-500 bg-rose-50 border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20': fields.description.value.length > 0 && fields.description.value.length < 75,
                                  'text-emerald-500 bg-emerald-50 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20': fields.description.value.length >= 75 && fields.description.value.length <= 400,
                                  'text-amber-500 bg-amber-50 border-amber-100 dark:bg-amber-500/10 dark:border-amber-500/20': fields.description.value.length > 400 && fields.description.value.length < 500,
                                  'text-rose-500 bg-rose-50 border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20 scale-105 animate-pulse': fields.description.value.length === 500
                              }">
                            <span x-text="fields.description.value.length"></span> / 500
                        </span>
                    </div>
                    
                    {{-- Textarea Wrapper --}}
                    <div class="relative rounded-2xl border bg-white dark:bg-zinc-900/50 shadow-sm transition-all duration-300 focus-within:ring-2 focus-within:ring-primary/30"
                         :class="fields.description.error && fields.description.touched ? 'border-rose-500 ring-2 ring-rose-500/20' : (fields.description.valid && fields.description.touched ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-white/[0.08] focus-within:border-primary')">
                        <textarea id="business_description" name="description" x-model="fields.description.value" @input="validateField('description')" :aria-invalid="fields.description.error && fields.description.touched ? 'true' : 'false'" rows="5" maxlength="500" placeholder="{{ __('forms.business.biz_desc_placeholder') }}" class="w-full bg-transparent px-5 py-4 text-sm font-bold outline-none resize-none border-0 focus:ring-0 text-slate-900 dark:text-zinc-100 leading-relaxed placeholder:text-slate-400/50 dark:placeholder:text-zinc-500"></textarea>
                    </div>

                    {{-- Tips & Info Card --}}
                    <div class="mt-3 bg-slate-50/50 dark:bg-zinc-900/30 border border-slate-200/50 dark:border-white/[0.05] rounded-2xl p-4 flex gap-3 items-start">
                        <div class="w-6 h-6 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0 mt-0.5" aria-hidden="true">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-12 9 9 9 0 0112-9z"/></svg>
                        </div>
                        <div class="flex-1 text-slate-500 dark:text-zinc-400 text-[11px] font-medium leading-relaxed" id="desc_tips">
                            <p class="font-extrabold text-slate-800 dark:text-zinc-200 mb-1 uppercase tracking-wider">{{ __('forms.business.desc_tips_title') ?? 'Tips for a perfect description:' }}</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>{{ __('forms.business.desc_tips_min_length') ?? 'Must write at least 75 characters (letters) so clients know your services.' }}</li>
                                <li>{{ __('forms.business.desc_tips_info') ?? 'Explain your main activities, expertise, and what sets you apart.' }}</li>
                            </ul>
                        </div>
                    </div>
                    <div aria-live="polite">
                        <p class="text-[11px] font-bold text-rose-500 mt-2 min-h-[16px] pl-1 transition-all" x-show="fields.description.error && fields.description.touched" x-transition id="desc_error">{{ __('forms.business.biz_desc_min') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3 - Sub-step 2: Visuals/Media --}}
        <div x-show="step === 3 && mediaSubStep === 2" class="space-y-4 overflow-y-auto custom-scrollbar pr-2 flex-1 gsap-step-3-2" style="display: none;">
            <div class="text-start px-2">
                <h2 class="text-2xl font-[800] text-slate-900 dark:text-white tracking-tight mb-1">{{ __('forms.business.visual_identity') }}</h2>
                <p class="text-slate-500 dark:text-zinc-500 text-sm font-medium">{{ __('forms.business.visual_identity_desc') }}</p>
            </div>

            <div class="space-y-8 px-2">
                {{-- Visual Image Pair --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full">
                    <div class="relative group block w-full">
                        <label for="logoInput" class="block text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-3">{{ __('forms.business.visual_identity') }} ({{ __('forms.business.logo') }}) <span class="text-primary" aria-hidden="true">*</span></label>
                        <div role="button" tabindex="0" class="w-full cursor-pointer group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-[2.5rem]" onclick="document.getElementById('logoInput').click()" onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); }">
                            <div class="h-44 w-full bg-slate-50/50 dark:bg-zinc-900/50 border-2 border-dashed border-slate-200 dark:border-white/[0.08] rounded-[2.5rem] flex flex-col items-center justify-center gap-3 hover:border-primary/50 hover:bg-primary/5 transition-all duration-500 overflow-hidden relative shadow-inner"
                                 :class="fields.logo.error && fields.logo.touched ? 'border-rose-500 ring-2 ring-rose-500/20' : ''">
                                {{-- Loading Spinner Overlay --}}
                                <div x-show="logoLoading" x-transition class="absolute inset-0 bg-white/70 dark:bg-zinc-900/70 z-20 flex flex-col items-center justify-center backdrop-blur-sm gap-2" aria-live="polite">
                                    <svg class="w-8 h-8 animate-spin text-primary" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-xs font-bold text-slate-700 dark:text-zinc-300 animate-pulse">{{ __('forms.business.uploading') ?? 'Uploading...' }}</span>
                                </div>
                                <template x-if="logoPrev">
                                    <div class="w-full h-full relative p-4 bg-white dark:bg-zinc-900 flex items-center justify-center">
                                        <img :src="logoPrev" class="max-w-full max-h-full object-contain filter drop-shadow-xl animate-in zoom-in-95 duration-300" decoding="async" alt="Logo preview">
                                        <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                            <span class="text-white text-[10px] font-black uppercase tracking-widest bg-black/40 px-3 py-1.5 rounded-full">{{ __('forms.business.click_to_upload') }}</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!logoPrev">
                                    <div class="text-center group-hover:scale-105 transition-transform duration-300">
                                        <div class="w-12 h-12 bg-white dark:bg-zinc-900 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4 text-slate-400 group-hover:text-primary transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        </div>
                                        <span class="text-[11px] font-black uppercase tracking-widest text-slate-400 dark:text-zinc-400">{{ __('forms.business.click_to_upload') }}</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="file" name="logo" id="logoInput" class="hidden" accept="image/jpeg,image/png,image/webp,image/jpg" @change="handleImageUpload($el, 'logo')">
                        <div aria-live="polite">
                            <p class="text-[11px] font-bold text-rose-500 mt-2 min-h-[16px] pl-1 transition-all" x-show="fields.logo.error && fields.logo.touched" x-transition x-text="fields.logo.errorMessage"></p>
                        </div>
                    </div>

                    <div class="relative group block w-full">
                        <label for="coverInput" class="block text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-3">{{ __('forms.business.cover_photo') }} ({{ __('forms.business.optional') }})</label>
                        <div role="button" tabindex="0" class="w-full cursor-pointer group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-[2.5rem]" onclick="document.getElementById('coverInput').click()" onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); }">
                            <div class="h-44 w-full bg-slate-50/50 dark:bg-zinc-900/50 border-2 border-dashed border-slate-200 dark:border-white/[0.08] rounded-[2.5rem] flex flex-col items-center justify-center gap-3 hover:border-primary/50 hover:bg-primary/5 transition-all duration-500 overflow-hidden relative shadow-inner"
                                 :class="fields.cover.error && fields.cover.touched ? 'border-rose-500 ring-2 ring-rose-500/20' : ''">
                                {{-- Loading Spinner Overlay --}}
                                <div x-show="coverLoading" x-transition class="absolute inset-0 bg-white/70 dark:bg-zinc-900/70 z-20 flex flex-col items-center justify-center backdrop-blur-sm gap-2" aria-live="polite">
                                    <svg class="w-8 h-8 animate-spin text-primary" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-xs font-bold text-slate-700 dark:text-zinc-300 animate-pulse">{{ __('forms.business.uploading') ?? 'Uploading...' }}</span>
                                </div>
                                <template x-if="coverPrev">
                                    <div class="w-full h-full relative p-2 bg-white dark:bg-zinc-900 flex items-center justify-center">
                                        <img :src="coverPrev" class="w-full h-full object-cover rounded-[1.5rem] filter drop-shadow-lg animate-in zoom-in-95 duration-300" decoding="async" alt="Cover preview">
                                        <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm rounded-[1.5rem]">
                                            <span class="text-white text-[10px] font-black uppercase tracking-widest bg-black/40 px-3 py-1.5 rounded-full">{{ __('forms.business.change_cover') }}</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!coverPrev">
                                    <div class="text-center group-hover:scale-105 transition-transform duration-300">
                                        <div class="w-12 h-12 bg-white dark:bg-zinc-900 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4 text-slate-400 group-hover:text-primary transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </div>
                                        <span class="text-[11px] font-black uppercase tracking-widest text-slate-400 dark:text-zinc-400">{{ __('forms.business.pick_cover') }}</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="file" name="cover" id="coverInput" class="hidden" accept="image/jpeg,image/png,image/webp,image/jpg" @change="handleImageUpload($el, 'cover')">
                        <div aria-live="polite">
                            <p class="text-[11px] font-bold text-rose-500 mt-2 min-h-[16px] pl-1 transition-all" x-show="fields.cover.error && fields.cover.touched" x-transition x-text="fields.cover.errorMessage"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4 - Sub-step 1: Country Selection --}}
        <div x-show="step === 4 && locationSubStep === 1" class="space-y-4 overflow-y-auto custom-scrollbar pr-2 flex-1 gsap-step-4-1" style="display: none;">
            <div class="text-start px-2">
                <h2 class="text-2xl font-[800] text-slate-900 dark:text-white tracking-tight mb-1">{{ __('forms.business.country') }}</h2>
                <p class="text-slate-500 dark:text-zinc-500 text-sm font-medium">{{ __('forms.business.pick_country') }}</p>
            </div>

            <div class="space-y-4 px-2 w-full">
                {{-- Custom Country Input --}}
                <div x-show="showCustomCountry" x-collapse class="relative w-full">
                    <label for="custom_country_name" class="sr-only">{{ __('forms.business.custom_country_placeholder') }}</label>
                    <div class="flex items-center gap-3">
                        <input type="text" id="custom_country_name" name="custom_country_name" x-model="fields.custom_country_name.value" @input="validateField('country')" :aria-invalid="fields.country.error && fields.country.touched ? 'true' : 'false'" placeholder="{{ __('forms.business.custom_country_placeholder') }}" class="w-full input-premium rounded-2xl px-4 py-3.5 sm:px-6 sm:py-5 text-sm sm:text-base font-bold outline-none shadow-sm" :class="fields.country.error && fields.country.touched ? 'border-rose-500 ring-2 ring-rose-500/20 text-rose-600' : ''">
                        <button type="button" aria-label="Cancel Custom Country" @click="showCustomCountry = false; showCustomCity = false; fields.custom_country_name.value = ''; fields.custom_city_name.value = ''; searchCountry = ''; validateField('country');" class="w-14 h-14 shrink-0 bg-slate-100 dark:bg-zinc-800 text-slate-500 hover:text-rose-500 rounded-2xl flex items-center justify-center transition-colors shadow-sm focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div aria-live="polite">
                        <p class="text-[11px] font-bold text-rose-500 mt-2 min-h-[16px] pl-1" x-show="fields.country.error && fields.country.touched" x-transition>{{ __('forms.business.country_error') }}</p>
                    </div>
                </div>

                {{-- Standard Countries Selector --}}
                <div class="w-full relative" x-show="!showCustomCountry" x-collapse>
                    {{-- Search Bar --}}
                    <div class="relative mb-2 w-full">
                        <label for="search_country" class="sr-only">{{ __('forms.business.search_country') }}</label>
                        <input type="text" id="search_country" x-model="searchCountry" placeholder="{{ __('forms.business.search_country') }}" class="w-full input-premium rounded-2xl py-3.5 sm:py-4 pr-4 sm:pr-6 pl-10 sm:pl-12 rtl:pl-4 sm:rtl:pl-6 rtl:pr-10 sm:rtl:pr-12 text-sm sm:text-base font-bold outline-none shadow-sm" :class="fields.country.error && fields.country.touched ? 'border-rose-500 ring-2 ring-rose-500/20' : ''">
                        <svg class="absolute left-4 rtl:left-auto rtl:right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    {{-- Cannot Find Country Link --}}
                    <div class="text-start px-2 mb-4" x-show="selectedCountry === null">
                        <button type="button" 
                                @click="showCustomCountry = true; showCustomCity = true; selectedCountry = null; fields.city.value = null; searchCountry = ''; validateField('country');" 
                                class="text-xs font-bold text-primary hover:text-primary-light hover:underline transition-colors cursor-pointer">
                            {{ __('forms.business.cant_find_country') }}
                        </button>
                    </div>

                    {{-- Grid Scroll Container --}}
                    <div class="max-h-[226px] overflow-y-auto light-scrollbar scroll-snap-y px-2 pt-2 pb-6 relative z-10" role="group" aria-label="Countries">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                            @foreach($countries->unique('id') as $country)
                            <button type="button" 
                                    role="switch"
                                    :aria-checked="selectedCountry == {{ $country->id }} ? 'true' : 'false'"
                                    @click="selectCountry({{ $country->id }}, '{{ addslashes($country->name) }}', $event)" 
                                    x-show="searchCountry === '' || '{{ mb_strtolower($country->name) }}'.includes(searchCountry.toLowerCase())"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-90 translate-y-2"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-90 translate-y-2"
                                    data-name="{{ mb_strtolower($country->name) }}"
                                    class="country-btn scroll-snap-align-start p-4 text-center text-xs font-bold rounded-2xl border transition-all flex flex-col items-center justify-center h-[90px] gap-2 cursor-pointer shadow-sm animate-fade-in focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none"
                                    :class="selectedCountry == {{ $country->id }} ? 'bg-primary/10 border-primary text-primary dark:bg-primary/20 ring-2 ring-primary/20 scale-[1.02]' : 'bg-slate-50/50 hover:bg-slate-100/70 border-slate-200/60 dark:bg-zinc-900/30 dark:border-zinc-800/80 dark:hover:bg-zinc-800/50 text-slate-700 dark:text-zinc-300 hover:border-primary/40 hover:text-primary'">
                                <span>{{ $country->name }}</span>
                            </button>
                            @endforeach

                            {{-- Country Not Found Empty State --}}
                            <div x-show="!hasCountryMatches()" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="col-span-3 text-center py-8 px-4 bg-slate-50/50 dark:bg-zinc-900/30 rounded-2xl border border-slate-200/50 dark:border-zinc-800/80 shadow-inner">
                                <div class="w-10 h-10 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <p class="text-xs font-extrabold text-slate-800 dark:text-zinc-200 uppercase tracking-wider mb-1">{{ __('forms.business.country_not_found') }}</p>
                                <button type="button" @click="showCustomCountry = true; showCustomCity = true; selectedCountry = null; fields.city.value = null; searchCountry = ''; validateField('country');" class="px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-[800] hover:bg-primary-light shadow-md shadow-primary/10 active:scale-95 transition-all">
                                    {{ __('forms.business.add_your_country') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="country_id" :value="selectedCountry">
                    <p class="text-[11px] font-bold text-rose-500 mt-2 min-h-[16px] pl-1" x-show="fields.country.error && fields.country.touched && !showCustomCountry" x-transition>{{ __('forms.business.country_error') }}</p>
                </div>
            </div>
        </div>

        {{-- Step 4 - Sub-step 2: City Selection --}}
        <div x-show="step === 4 && locationSubStep === 2" class="space-y-4 overflow-y-auto custom-scrollbar pr-2 flex-1 gsap-step-4-2" style="display: none;">
            <div class="text-start px-2">
                <h2 class="text-2xl font-[800] text-slate-900 dark:text-white tracking-tight mb-1">{{ __('forms.business.city') }}</h2>
                <p class="text-slate-500 dark:text-zinc-500 text-sm font-medium">{{ __('forms.business.pick_city') }}</p>
            </div>

            <div class="space-y-4 px-2 w-full">
                {{-- Custom City Input --}}
                <div x-show="showCustomCity" x-collapse class="relative w-full">
                    <label for="custom_city_name" class="sr-only">{{ __('forms.business.custom_city_placeholder') }}</label>
                    <div class="flex items-center gap-3">
                        <input type="text" id="custom_city_name" name="custom_city_name" x-model="fields.custom_city_name.value" @input="validateField('city')" :aria-invalid="fields.city.error && fields.city.touched ? 'true' : 'false'" placeholder="{{ __('forms.business.custom_city_placeholder') }}" class="w-full input-premium rounded-2xl px-4 py-3.5 sm:px-6 sm:py-5 text-sm sm:text-base font-bold outline-none shadow-sm" :class="fields.city.error && fields.city.touched ? 'border-rose-500 ring-2 ring-rose-500/20 text-rose-600' : ''">
                        <button type="button" aria-label="Cancel Custom City" x-show="!showCustomCountry" @click="showCustomCity = false; fields.custom_city_name.value = ''; searchCity = ''; validateField('city');" class="w-14 h-14 shrink-0 bg-slate-100 dark:bg-zinc-800 text-slate-500 hover:text-rose-500 rounded-2xl flex items-center justify-center transition-colors shadow-sm focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div aria-live="polite">
                        <p class="text-[11px] font-bold text-rose-500 mt-2 min-h-[16px] pl-1" x-show="fields.city.error && fields.city.touched" x-transition>{{ __('forms.business.city_error') }}</p>
                    </div>
                </div>

                {{-- Standard Cities Selector --}}
                <div class="w-full relative" x-show="!showCustomCity" x-collapse>
                    {{-- Search Bar --}}
                    <div class="relative mb-2 w-full">
                        <label for="search_city" class="sr-only">{{ __('forms.business.search_city') }}</label>
                        <input type="text" id="search_city" x-model="searchCity" placeholder="{{ __('forms.business.search_city') }}" class="w-full input-premium rounded-2xl py-3.5 sm:py-4 pr-4 sm:pr-6 pl-10 sm:pl-12 rtl:pl-4 sm:rtl:pl-6 rtl:pr-10 sm:rtl:pr-12 text-sm sm:text-base font-bold outline-none shadow-sm" :class="fields.city.error && fields.city.touched ? 'border-rose-500 ring-2 ring-rose-500/20' : ''">
                        <svg class="absolute left-4 rtl:left-auto rtl:right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    {{-- Cannot Find City Link --}}
                    <div class="text-start px-2 mb-4" x-show="fields.city.value === null">
                        <button type="button" 
                                @click="showCustomCity = true; fields.city.value = null; searchCity = ''; validateField('city');" 
                                class="text-xs font-bold text-primary hover:text-primary-light hover:underline transition-colors cursor-pointer">
                            {{ __('forms.business.cant_find_city') }}
                        </button>
                    </div>

                    {{-- Grid Scroll Container --}}
                    <div class="max-h-[226px] overflow-y-auto light-scrollbar scroll-snap-y px-2 pt-2 pb-6 relative z-10" role="group" aria-label="Cities">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                            <template x-for="city in getFilteredCities()" :key="city.id">
                                <button type="button" 
                                        role="switch"
                                        :aria-checked="fields.city.value == city.id ? 'true' : 'false'"
                                        @click="selectCity(city.id, city.name, $event)" 
                                        class="city-btn scroll-snap-align-start p-4 text-center text-xs font-bold rounded-2xl border transition-all flex flex-col items-center justify-center h-[90px] gap-2 cursor-pointer shadow-sm animate-fade-in focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none"
                                        :class="fields.city.value == city.id ? 'bg-primary/10 border-primary text-primary dark:bg-primary/20 ring-2 ring-primary/20 scale-[1.02]' : 'bg-slate-50/50 hover:bg-slate-100/70 border-slate-200/60 dark:bg-zinc-900/30 dark:border-zinc-800/80 dark:hover:bg-zinc-800/50 text-slate-700 dark:text-zinc-300 hover:border-primary/40 hover:text-primary'">
                                    <span x-text="city.name"></span>
                                </button>
                            </template>

                            {{-- City Not Found Empty State --}}
                            <div x-show="getFilteredCities().length === 0" 
                                 x-transition class="col-span-3 text-center py-8 px-4 bg-slate-50/50 dark:bg-zinc-900/30 rounded-2xl border border-slate-200/50 dark:border-zinc-800/80 shadow-inner">
                                <div class="w-10 h-10 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <p class="text-xs font-extrabold text-slate-800 dark:text-zinc-200 uppercase tracking-wider mb-1">{{ __('forms.business.city_not_found') }}</p>
                                <button type="button" @click="showCustomCity = true; fields.city.value = null; searchCity = ''; validateField('city');" class="px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-[800] hover:bg-primary-light shadow-md shadow-primary/10 active:scale-95 transition-all">
                                    {{ __('forms.business.add_your_city') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="city_id" :value="fields.city.value">
                    <p class="text-[11px] font-bold text-rose-500 mt-2 min-h-[16px] pl-1" x-show="fields.city.error && fields.city.touched && !showCustomCity" x-transition>{{ __('forms.business.city_error') }}</p>
                </div>
            </div>
        </div>

        {{-- Step 4 - Sub-step 3: Physical Address details --}}
        <div x-show="step === 4 && locationSubStep === 3" class="space-y-4 overflow-y-auto custom-scrollbar pr-2 flex-1 gsap-step-4-3" style="display: none;">
            <div class="text-start px-2">
                <h2 class="text-2xl font-[800] text-slate-900 dark:text-white tracking-tight mb-1">{{ __('forms.business.address') }}</h2>
                <p class="text-slate-500 dark:text-zinc-500 text-sm font-medium">{{ __('forms.business.select_location') }}</p>
            </div>

            <div class="space-y-6 px-2 w-full">
                {{-- Address Field --}}
                <div class="relative group block w-full">
                    <label for="business_address" class="block text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-3 text-start">{{ __('forms.business.address') }} ({{ __('forms.business.optional') }})</label>
                    <div class="relative">
                        <input type="text" id="business_address" name="address" placeholder="{{ __('forms.business.address_placeholder') }}" class="w-full input-premium rounded-2xl px-4 py-3.5 sm:px-6 sm:py-5 text-sm sm:text-base font-bold outline-none shadow-sm">
                        <svg class="absolute right-6 rtl:right-auto rtl:left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400/60" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>

                {{-- Guided Address Help Card --}}
                <div class="bg-slate-50/50 dark:bg-zinc-900/30 border border-slate-200/50 dark:border-zinc-800/60 rounded-2xl p-5 flex gap-4 items-start shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-12 9 9 9 0 0112-9z"/></svg>
                    </div>
                    <div class="flex-1 text-slate-500 dark:text-zinc-400 text-xs font-medium leading-relaxed text-start">
                        <p class="font-extrabold text-slate-800 dark:text-zinc-200 mb-1.5 uppercase tracking-wider text-[11px]">{{ __('forms.business.location_tips_title') }}</p>
                        <ul class="list-disc list-inside space-y-1.5 pl-1">
                            <li>{{ __('forms.business.location_tips_street') }}</li>
                            <li>{{ __('forms.business.location_tips_landmark') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 5: Digital Connectivity --}}
        <div x-show="step === 5" class="space-y-6 overflow-y-auto custom-scrollbar pr-2 flex-1 gsap-step-5" style="display: none;">
            <div class="text-start">
                <h2 class="text-2xl font-[800] text-slate-900 dark:text-white tracking-tight mb-1">{{ __('forms.business.social_links') }}</h2>
                <p class="text-slate-500 dark:text-zinc-500 text-sm font-medium">{{ __('forms.business.social_links_desc') ?? 'Connect your audience with your digital footprint.' }}</p>
            </div>

            <div class="w-full flex flex-col items-start">
                
                @php
                    $contacts = [
                        'phone' => [
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>',
                            'color' => 'text-emerald-600 dark:text-emerald-400',
                            'bg' => 'bg-emerald-500/10 dark:bg-emerald-500/20',
                            'type' => 'tel',
                            'label' => __('forms.business.direct_line') ?? 'Phone',
                            'hex' => '#10b981'
                        ],
                        'whatsapp' => [
                            'icon' => '<path fill="currentColor" d="M19.077 4.928A9.944 9.944 0 0 0 12.004 2c-5.51 0-9.993 4.483-9.993 9.993 0 1.763.461 3.486 1.336 5.006L2 22l5.127-1.345a9.92 9.92 0 0 0 4.877 1.28h.005c5.51 0 9.993-4.484 9.993-9.993 0-2.67-1.04-5.18-2.929-7.069z"/><path fill="#ffffff" d="M12.004 3.7c-4.573 0-8.293 3.72-8.293 8.293 0 1.463.38 2.89 1.11 4.148l-.12-.22-.73 2.66 2.72-.71.21.12c1.21.72 2.59 1.1 4.02 1.1 4.57 0 8.29-3.72 8.29-8.29 0-2.22-.86-4.3-2.43-5.87S14.22 3.7 12 3.7zm4.72 9.87c-.26.73-1.25 1.34-2.03 1.42-.54.06-1.24.08-3.6-.9-3.02-1.25-4.97-4.33-5.12-4.54-.15-.2-1.24-1.65-1.24-3.15 0-1.5.78-2.24 1.06-2.54.28-.31.6-.38.81-.38.2 0 .41.01.58.02.18.01.42-.07.66.51.25.6.85 2.06.92 2.21.08.15.13.34.03.54-.1.21-.21.34-.36.52-.15.18-.32.41-.46.55-.15.15-.31.33-.13.64.18.31.82 1.34 1.76 2.18.94.84 1.74 1.08 1.98 1.18.24.1.38.08.52-.07.14-.15.6-.71.77-.95.16-.24.33-.2.55-.12.22.08 1.4.66 1.64.78.24.12.4.18.46.28.06.1.06 1.44-.2 2.17z"/>',
                            'color' => 'text-[#25D366]',
                            'bg' => 'bg-[#25D366]/10 dark:bg-[#25D366]/20',
                            'type' => 'tel',
                            'label' => __('forms.business.whatsapp') ?? 'WhatsApp',
                            'fill' => true,
                            'hex' => '#25D366'
                        ],
                        'website' => [
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a11.83 11.83 0 00-5.656 0l-4 4a11.83 11.83 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>',
                            'color' => 'text-sky-600',
                            'bg' => 'bg-sky-500/10 dark:bg-sky-500/20',
                            'type' => 'url',
                            'label' => __('forms.business.website') ?? 'Website',
                            'hex' => '#0ea5e9'
                        ],
                    ];
                    
                    $socials = [
                        'facebook' => ['icon' => '<circle cx="12" cy="12" r="12" fill="currentColor"/><path fill="#ffffff" d="M14.5 12h-2v7h-3v-7h-1.5v-2.5h1.5v-1.6c0-2 1.2-3.4 3.3-3.4 1 0 1.8.1 2 .1v2.4h-1.4c-1 0-1.2.5-1.2 1.2v1.3h2.6l-.3 2.5z"/>', 'color' => 'text-[#1877F2]', 'hex' => '#1877F2', 'type' => 'url', 'label' => __('forms.business.facebook') ?? 'Facebook', 'fill' => true],
                        'instagram' => ['icon' => '<path fill="url(#instagram-grad)" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>', 'color' => 'text-[#E1306C]', 'hex' => '#E1306C', 'type' => 'url', 'label' => __('forms.business.instagram') ?? 'Instagram', 'fill' => true],
                        'tiktok' => ['icon' => '<g><path fill="#fe0979" d="M19.19 6.29a4.83 4.83 0 0 1-3.77-4.25V1.6h-3.45v13.67a2.89 2.89 0 0 1-5.77 0 2.89 2.89 0 0 1 2.89-2.89h.6V8.25h-.6a6.38 6.38 0 1 0 6.38 6.38V8.25c1.46.25 2.8.84 3.9 1.69v-3.65z"/><path fill="#25f4ee" d="M19.99 7.09a4.83 4.83 0 0 1-3.77-4.25V2.4h-3.45v13.67a2.89 2.89 0 0 1-5.77 0 2.89 2.89 0 0 1 2.89-2.89h.6V9.05h-.6a6.38 6.38 0 1 0 6.38 6.38V9.05c1.46.25 2.8.84 3.9 1.69v-3.65z"/><path fill="currentColor" d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.77 0 2.89 2.89 0 0 1 2.89-2.89h.6V8.65h-.6a6.38 6.38 0 1 0 6.38 6.38V8.65c1.46.25 2.8.84 3.9 1.69v-3.65z"/></g>', 'color' => 'text-[#0f1419] dark:text-white', 'hex' => '#0f1419', 'type' => 'url', 'label' => __('forms.business.tiktok') ?? 'TikTok', 'fill' => true],
                        'linkedin' => ['icon' => '<rect width="24" height="24" rx="4" fill="currentColor"/><path fill="#ffffff" d="M19 19h-3v-5.604c0-3.368-4-3.113-4 0V19h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476V19zm-11.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zM8 19H5V8h3v11z"/>', 'color' => 'text-[#0A66C2]', 'hex' => '#0A66C2', 'type' => 'url', 'label' => __('forms.business.linkedin') ?? 'LinkedIn', 'fill' => true],
                        'twitter' => ['icon' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>', 'color' => 'text-[#0f1419] dark:text-white', 'hex' => '#0f1419', 'type' => 'url', 'label' => __('forms.business.twitter') ?? 'X (Twitter)', 'fill' => true],
                        'youtube' => ['icon' => '<path fill="currentColor" d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.11C19.518 3.545 12 3.545 12 3.545s-7.516 0-9.388.508a3.003 3.003 0 0 0-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 0 0 2.11 2.11c1.872.508 9.388.508 9.388.508s7.517 0 9.388-.508a3.002 3.002 0 0 0 2.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837z"/><polygon fill="#ffffff" points="9.545 15.568 15.818 12 9.545 8.432"/>', 'color' => 'text-[#FF0000]', 'hex' => '#FF0000', 'type' => 'url', 'label' => __('forms.business.youtube') ?? 'YouTube', 'fill' => true],
                        'telegram' => ['icon' => '<circle cx="12" cy="12" r="12" fill="currentColor"/><path fill="#ffffff" d="M9.8 14.5l-.3 4.2c.4 0 .6-.2.8-.4l2-1.9 4.1 3c.7.4 1.3.2 1.5-.6l2.7-12.7c.3-1.1-.4-1.6-1.1-1.3L3.5 9.7c-1.1.4-1.1 1.1-.2 1.4l4.1 1.3 9.6-6c.5-.3.9-.1.5.2l-7.7 7z"/>', 'color' => 'text-[#229ED9]', 'hex' => '#229ED9', 'type' => 'url', 'label' => __('forms.business.telegram') ?? 'Telegram', 'fill' => true],
                        'snapchat' => ['icon' => '<rect width="24" height="24" rx="5.5" fill="currentColor"/><g transform="translate(4.2, 4.2) scale(0.65)"><path fill="#ffffff" d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.475 4.791 4.014.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.18-.015h-.149c-1.468 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.243.165-.465.42-.509 3.264-.54 4.73-3.879 4.791-4.02l.016-.029c.18-.345.224-.645.119-.869-.195-.434-.884-.658-1.332-.809-.121-.029-.24-.074-.346-.119-1.107-.435-1.257-.93-1.197-1.273.09-.479.674-.793 1.168-.793.146 0 .27.029.383.074.42.194.789.3 1.104.3.234 0 .384-.06.465-.105l-.046-.569c-.098-1.626-.225-3.651.307-4.837C7.392 1.077 10.739.807 11.727.807l.419-.015h.06z"/></g>', 'color' => 'text-[#eab308]', 'hex' => '#eab308', 'type' => 'url', 'label' => __('forms.business.snapchat') ?? 'Snapchat', 'fill' => true],
                    ];
                @endphp

                <!-- Fixed Contact Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full mb-8">
                    @foreach($contacts as $key => $data)
                    <div class="flex flex-col gap-2 {{ $key === 'website' ? 'md:col-span-2' : '' }}">
                        <label for="social_{{$key}}" class="block text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-1 text-start">{{ $data['label'] }} {!! $key === 'phone' ? '<span class="text-rose-500">*</span>' : '' !!}</label>
                        <div class="relative w-full">
                            <div class="absolute left-4.5 rtl:left-auto rtl:right-4.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center pointer-events-none transition-transform duration-300" style="color: {{ $data['hex'] }};">
                                <svg class="w-5 h-5" {{ isset($data['fill']) ? 'fill="currentColor"' : 'fill="none" stroke="currentColor" stroke-width="2.5"' }} viewBox="0 0 24 24">{!! $data['icon'] !!}</svg>
                            </div>
                            <input type="{{$data['type']}}" id="social_{{$key}}" name="social_links[{{$key}}]" {{ $key === 'phone' ? 'required aria-required="true"' : '' }} x-model="fields.links.{{$key}}.value" @input="validateLink('{{$key}}')" placeholder="{{ __('forms.business.' . $key . '_placeholder') ?? 'Enter details' }}" class="w-full input-premium rounded-2xl pl-13 pr-12 rtl:pr-13 rtl:pl-12 py-3 text-sm font-bold outline-none social-active-card brand-{{$key}}" style="--brand-color: {{ $data['hex'] }}" :class="fields.links.{{$key}}.error && fields.links.{{$key}}.touched ? 'border-rose-500 ring-4 ring-rose-500/20' : ''">
                            
                            <div class="absolute right-4 rtl:right-auto rtl:left-4 top-1/2 -translate-y-1/2 validation-icon flex items-center justify-center shrink-0">
                                <svg x-show="fields.links.{{$key}}.valid && fields.links.{{$key}}.value.length > 0 && !fields.links.{{$key}}.error" class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <svg x-show="fields.links.{{$key}}.error && fields.links.{{$key}}.touched" class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                        </div>
                        <p class="text-[10px] font-bold text-rose-500 mt-1 pl-1" x-show="fields.links.{{$key}}.error && fields.links.{{$key}}.touched" style="display: none;">{{ __('forms.business.invalid_link') ?? 'Invalid format' }}</p>
                    </div>
                    @endforeach
                </div>

                <!-- Dynamic Social Media Section -->
                <div class="w-full">
                    <div class="flex items-center justify-between mb-6 border-b border-slate-100 dark:border-white/5 pb-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500/15 to-violet-500/5 text-violet-500 flex items-center justify-center shrink-0 shadow-inner ring-1 ring-violet-500/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            </div>
                            <div class="text-start">
                                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest">{{ __('forms.business.social_media') ?? 'Social Media' }}</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5 font-medium">{{ __('forms.business.optional') ?? 'Optional' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Social Media Fields -->
                    <div class="space-y-5 mb-8" x-show="activeSocials.length > 0" style="display: none;">
                        <template x-for="socialId in activeSocials" :key="socialId">
                            <div class="space-y-1.5 animate-fade-in group/card">
                                @foreach($socials as $key => $data)
                                <template x-if="socialId === '{{$key}}'">
                                    <div class="flex flex-col gap-2">
                                        <label :for="'social_'+socialId" class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1 text-start">{{ $data['label'] }}</label>
                                        <div class="relative w-full">
                                            <div class="absolute left-4.5 rtl:left-auto rtl:right-4.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center pointer-events-none transition-transform duration-300" style="color: {{ $data['hex'] }};">
                                                <svg class="w-5 h-5" {{ isset($data['fill']) ? 'fill="currentColor"' : 'fill="none" stroke="currentColor" stroke-width="2.5"' }} viewBox="0 0 24 24">{!! $data['icon'] !!}</svg>
                                            </div>
                                            <input type="{{$data['type']}}" :id="'social_'+socialId" name="social_links[{{$key}}]" x-ref="input_{{$key}}" x-model="fields.links.{{$key}}.value" @input="validateLink('{{$key}}')" placeholder="{{ __('forms.business.' . $key . '_placeholder') ?? 'Enter handle or URL' }}" class="w-full input-premium rounded-2xl pl-13 pr-14 rtl:pr-13 rtl:pl-14 py-3 text-sm font-bold outline-none social-active-card brand-{{$key}}" style="--brand-color: {{ $data['hex'] }}" :class="fields.links.{{$key}}.error ? 'border-rose-500 ring-4 ring-rose-500/20' : ''">
                                            
                                            <div class="absolute right-4 rtl:right-auto rtl:left-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                                <button type="button" @click="removeSocialField('{{$key}}')" class="w-6 h-6 rounded-lg flex items-center justify-center text-slate-300 dark:text-zinc-600 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-all duration-200 shrink-0 sm:opacity-0 sm:group-hover/card:opacity-100" title="{{ __('forms.business.remove') ?? 'Remove' }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                @endforeach
                                <p class="text-[10px] font-bold text-rose-500 ps-5" x-show="fields.links[socialId].error && fields.links[socialId].touched" style="display: none;">{{ __('forms.business.invalid_link') ?? 'Invalid format' }}</p>
                            </div>
                        </template>
                    </div>

                    <!-- Platform Picker — Always Visible Chip Grid -->
                    <div x-show="activeSocials.length < {{ count($socials) }}">
                        <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-4.5 text-start">{{ __('forms.business.add_social_account') ?? 'Add Social Account' }}</p>
                        <div class="flex flex-wrap gap-3.5">
                            @foreach($socials as $key => $data)
                            <button type="button" x-show="!activeSocials.includes('{{$key}}')" @click="addSocialField('{{$key}}')" class="social-picker-chip brand-{{$key}} group flex items-center gap-3 px-5 py-3 rounded-2xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-zinc-800/60 cursor-pointer" style="--hover-color: {{ $data['hex'] }}">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:scale-110" style="background: color-mix(in srgb, var(--brand-color, {{ $data['hex'] }}) 10%, transparent);">
                                    <svg class="w-4 h-4" style="color: var(--brand-color, {{ $data['hex'] }});" {{ isset($data['fill']) ? 'fill="currentColor"' : 'fill="none" stroke="currentColor" stroke-width="2"' }} viewBox="0 0 24 24">{!! $data['icon'] !!}</svg>
                                </div>
                                <span class="text-[11px] font-bold text-slate-600 dark:text-zinc-300 whitespace-nowrap">{{ $data['label'] }}</span>
                                <div class="w-5 h-5 rounded-md bg-slate-100 dark:bg-white/5 flex items-center justify-center transition-all duration-300 ml-1 plus-btn">
                                    <svg class="w-3 h-3 text-slate-400 dark:text-zinc-500 transition-transform duration-300 plus-icon" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- All platforms used message -->
                    <div x-show="activeSocials.length === {{ count($socials) }}" style="display: none;" class="mt-4">
                        <div class="flex items-center gap-3 px-5 py-4 bg-violet-50/50 dark:bg-violet-500/5 rounded-2xl border border-violet-100 dark:border-violet-500/10">
                            <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-[11px] font-bold text-violet-600 dark:text-violet-400">{{ __('forms.business.all_platforms_in_use') ?? 'All platforms are in use' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wizard Navigation Controls --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 mt-auto pt-4 border-t border-slate-200/50 dark:border-zinc-800/50 w-full gsap-controls shrink-0 px-2">
            <button type="button" @click="prevStep()" :class="step > 1 ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'" class="w-full sm:w-auto px-8 py-4 bg-white/50 dark:bg-zinc-800/30 border border-slate-200 dark:border-zinc-700/50 rounded-2xl text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-white dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white transition-all duration-300 flex items-center justify-center gap-2 group active:scale-95">
                <svg class="w-4 h-4 rtl:rotate-180 group-hover:-translate-x-1 rtl:group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                {{ __('forms.business.prev') }}
            </button>

            <button type="button" @click="nextStep($event)" x-show="isNextButtonVisible()" class="w-full sm:w-auto px-10 py-4 bg-primary text-white rounded-2xl text-sm font-[800] flex items-center justify-center gap-3 hover:bg-primary-light transition-all duration-300 active:scale-[0.98] group relative overflow-hidden">
                <span class="relative z-10" x-text="step === 5 ? '{{ __('forms.business.launch_profile') }}' : '{{ __('forms.business.next') }}'"></span>
                <svg x-show="step < 5" class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
            </button>
        </div>
    </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setupWizard() {
        return {
            step: 1,
            mediaSubStep: 1,
            locationSubStep: 1,
            isAnimating: false,
            getStepSelector(step, subStep) {
                if (step === 3) return `.gsap-step-3-${subStep}`;
                if (step === 4) return `.gsap-step-4-${subStep}`;
                return `.gsap-step-${step}`;
            },
            fields: {
                name: { value: '', error: false, valid: false, touched: false },
                slug: { value: '', error: false, valid: false, touched: false, checking: false, exists: false, suggestions: [] },
                description: { value: '', error: false, valid: false, touched: false },
                logo: { value: null, error: false, valid: false, touched: false, errorMessage: '' },
                cover: { value: null, error: false, valid: true, touched: false, errorMessage: '' },
                category: { value: null, error: false, valid: false, touched: false },
                custom_category_name: { value: '', error: false, valid: false, touched: false },
                country: { value: null, error: false, valid: false, touched: false },
                city: { value: null, error: false, valid: false, touched: false },
                custom_city_name: { value: '', error: false, valid: false, touched: false },
                custom_country_name: { value: '', error: false, valid: false, touched: false },
                links: {
                    phone: { value: '', error: false, valid: true, touched: false },
                    whatsapp: { value: '', error: false, valid: true, touched: false },
                    website: { value: '', error: false, valid: true, touched: false },
                    facebook: { value: '', error: false, valid: true, touched: false },
                    instagram: { value: '', error: false, valid: true, touched: false },
                    tiktok: { value: '', error: false, valid: true, touched: false },
                    linkedin: { value: '', error: false, valid: true, touched: false },
                    twitter: { value: '', error: false, valid: true, touched: false },
                    youtube: { value: '', error: false, valid: true, touched: false },
                    telegram: { value: '', error: false, valid: true, touched: false },
                    snapchat: { value: '', error: false, valid: true, touched: false },
                }
            },

            searchCat: '',
            searchCountry: '',
            searchCity: '',
            logoPrev: null,
            coverPrev: null,
            logoLoading: false,
            coverLoading: false,
            selectedCountry: null,
            selectedCountryName: '',
            cities: [],
            cityName: '',
            showCustomCategory: false,
            showCustomCountry: false,
            showCustomCity: false,
            slugManuallyEdited: false,
            checkSlugDebounce: null,
            activeSocials: [],

            addSocialField(id) {
                if (!this.activeSocials.includes(id)) {
                    this.activeSocials.push(id);
                    setTimeout(() => {
                        const input = this.$refs['input_' + id];
                        if (input) input.focus();
                    }, 100);
                }
            },
            
            removeSocialField(id) {
                this.activeSocials = this.activeSocials.filter(s => s !== id);
                this.fields.links[id].value = '';
                this.fields.links[id].error = false;
                this.fields.links[id].valid = true;
            },

            getFilteredCities() {
                if (!this.searchCity) return this.cities;
                const q = this.searchCity.toLowerCase();
                return this.cities.filter(c => c.name.toLowerCase().includes(q));
            },

            isNextButtonVisible() {
                if (this.step === 2) {
                    return this.fields.category.valid;
                }
                if (this.step === 4) {
                    if (this.locationSubStep === 1) {
                        return this.showCustomCountry && this.fields.country.valid;
                    }
                    if (this.locationSubStep === 2) {
                        return this.showCustomCity && this.fields.city.valid;
                    }
                    if (this.locationSubStep === 3) {
                        return true;
                    }
                }
                return true;
            },

            hasCountryMatches() {
                if (this.searchCountry === '') return true;
                const val = this.searchCountry.toLowerCase();
                const buttons = document.querySelectorAll('.country-btn');
                if (buttons.length === 0) return true;
                for (let btn of buttons) {
                    const name = btn.getAttribute('data-name');
                    if (name && name.includes(val)) {
                        return true;
                    }
                }
                return false;
            },

            initGsap() {
                if (typeof gsap === 'undefined') return;
                this.$nextTick(() => {
                    const tl = gsap.timeline({ defaults: { ease: 'power4.out', duration: 1 } });
                    tl.from('.gsap-stepper', { y: -20, opacity: 0, delay: 0.2 })
                      .from('.glass-morphism', { y: 40, opacity: 0, stagger: 0.1, scale: 0.98 }, '-=0.6')
                      .from('.gsap-step-1 > *', { y: 20, opacity: 0, stagger: 0.1 }, '-=0.4');
                });
            },

            stepDesc() {
                const descs = {
                    1: '{{ __('forms.business.basic_info_desc') }}',
                    2: '{{ __('forms.business.category_desc') }}',
                    3: '{{ __('forms.business.visual_identity_desc') }}',
                    4: '{{ __('forms.business.location_desc') }}',
                    5: '{{ __('forms.business.social_links_desc') ?? 'Connect your digital presence.' }}'
                };
                return descs[this.step] || '';
            },

            generateSlug() {
                if(this.slugManuallyEdited) return;
                if(this.fields.name.value) {
                    let base = this.fields.name.value.toLowerCase().replace(/[^a-z0-9\u0600-\u06FF]+/g, '-').replace(/(^-|-$)+/g, '');
                    if(base.length >= 3) {
                        this.fields.slug.value = base;
                        this.checkSlug();
                    } else {
                        this.fields.slug.value = '';
                    }
                } else {
                    this.fields.slug.value = '';
                }
            },

            checkSlug() {
                clearTimeout(this.checkSlugDebounce);
                const val = this.fields.slug.value.trim();
                this.fields.slug.touched = true;
                
                if(val.length < 3) {
                    this.fields.slug.error = true;
                    this.fields.slug.valid = false;
                    this.fields.slug.suggestions = [];
                    return;
                }
                
                this.fields.slug.checking = true;
                this.fields.slug.error = false;
                
                this.checkSlugDebounce = setTimeout(async () => {
                    try {
                        const res = await fetch(`/dashboard/business/check-slug?slug=${encodeURIComponent(val)}`);
                        const data = await res.json();
                        this.fields.slug.checking = false;
                        this.fields.slug.exists = data.exists;
                        this.fields.slug.suggestions = data.suggestions || [];
                        this.fields.slug.valid = !data.exists;
                        this.fields.slug.error = data.exists;
                    } catch(e) {
                        this.fields.slug.checking = false;
                    }
                }, 500);
            },

            validateField(field) {
                if (field === 'name') {
                    const val = this.fields.name.value.trim();
                    this.fields.name.valid = val.length >= 3;
                    this.fields.name.error = !this.fields.name.valid;
                }
                if (field === 'description') {
                    const val = this.fields.description.value.trim();
                    this.fields.description.valid = val.length >= 75 && val.length <= 500;
                    this.fields.description.error = !this.fields.description.valid;
                }
                if (field === 'category') {
                    if (this.showCustomCategory) {
                        this.fields.category.valid = this.fields.custom_category_name.value.trim().length >= 2;
                    } else {
                        this.fields.category.valid = this.fields.category.value !== null;
                    }
                    this.fields.category.error = !this.fields.category.valid;
                }
                if (field === 'country') {
                    if (this.showCustomCountry) {
                        this.fields.country.valid = this.fields.custom_country_name.value.trim().length >= 2;
                    } else {
                        this.fields.country.valid = this.selectedCountry !== null;
                    }
                    this.fields.country.error = !this.fields.country.valid;
                }
                if (field === 'city') {
                    if (this.showCustomCity) {
                        this.fields.city.valid = this.fields.custom_city_name.value.trim().length >= 2;
                        if(this.showCustomCountry && this.fields.custom_country_name.value.trim().length < 2) {
                            this.fields.city.valid = false;
                        }
                    } else {
                        this.fields.city.valid = this.fields.city.value !== null;
                    }
                    this.fields.city.error = !this.fields.city.valid;
                }
                if (field === 'logo') {
                    const inputEl = document.getElementById('logoInput');
                    const file = inputEl && inputEl.files ? inputEl.files[0] : null;
                    if (!file) {
                        this.fields.logo.valid = false;
                        this.fields.logo.errorMessage = '{{ __('forms.business.logo_required_error') }}';
                    } else {
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                        if (!allowedTypes.includes(file.type)) {
                            this.fields.logo.valid = false;
                            this.fields.logo.errorMessage = '{{ __('forms.business.logo_type_error') }}';
                        } else if (file.size > 5242880) {
                            this.fields.logo.valid = false;
                            this.fields.logo.errorMessage = '{{ __('forms.business.logo_size_error') }}';
                        } else {
                            this.fields.logo.valid = true;
                            this.fields.logo.errorMessage = '';
                        }
                    }
                    this.fields.logo.error = !this.fields.logo.valid;
                }
                if (field === 'cover') {
                    const inputEl = document.getElementById('coverInput');
                    const file = inputEl && inputEl.files ? inputEl.files[0] : null;
                    if (!file) {
                        this.fields.cover.valid = true;
                        this.fields.cover.errorMessage = '';
                    } else {
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                        if (!allowedTypes.includes(file.type)) {
                            this.fields.cover.valid = false;
                            this.fields.cover.errorMessage = '{{ __('forms.business.logo_type_error') }}';
                        } else if (file.size > 5242880) {
                            this.fields.cover.valid = false;
                            this.fields.cover.errorMessage = '{{ __('forms.business.logo_size_error') }}';
                        } else {
                            this.fields.cover.valid = true;
                            this.fields.cover.errorMessage = '';
                        }
                    }
                    this.fields.cover.error = !this.fields.cover.valid;
                }
            },

            validateLink(field) {
                const val = this.fields.links[field].value.trim();
                if (!val) {
                    this.fields.links[field].error = false;
                    this.fields.links[field].valid = true;
                    return;
                }
                this.fields.links[field].touched = true;
                let isValid = true;
                const urlPattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
                const phonePattern = /^[+\-\(\)\s0-9]{7,20}$/;
                const handlePattern = /^[a-zA-Z0-9_.\-]+$/;
                
                const handlePlatforms = ['instagram', 'tiktok', 'twitter', 'snapchat', 'telegram'];

                if (['phone', 'whatsapp'].includes(field)) {
                    isValid = phonePattern.test(val);
                } else if (handlePlatforms.includes(field)) {
                    isValid = handlePattern.test(val.replace(/^@/, '')) || urlPattern.test(val);
                } else {
                    isValid = urlPattern.test(val);
                }
                
                this.fields.links[field].valid = isValid;
                this.fields.links[field].error = !isValid;
            },

            validateCurrentStep() {
                let isValid = true;
                if (this.step === 1) {
                    this.fields.name.touched = true;
                    this.fields.slug.touched = true;
                    this.validateField('name');
                    if(!this.fields.slug.value) {
                        this.fields.slug.error = true;
                        this.fields.slug.valid = false;
                    }
                    if(this.fields.name.error || this.fields.slug.error || this.fields.slug.checking) isValid = false;
                } else if (this.step === 2) {
                    this.fields.category.touched = true;
                    this.validateField('category');
                    if(this.fields.category.error) isValid = false;
                } else if (this.step === 3) {
                    if (this.mediaSubStep === 2) {
                        this.fields.logo.touched = true;
                        this.fields.cover.touched = true;
                        this.validateField('logo');
                        this.validateField('cover');
                        if (this.fields.logo.error || this.fields.cover.error) isValid = false;
                    }
                    this.fields.description.touched = true;
                    this.validateField('description');
                    if(this.fields.description.error) isValid = false;
                } else if (this.step === 4) {
                    if (this.locationSubStep === 1) {
                        this.fields.country.touched = true;
                        this.validateField('country');
                        if(this.fields.country.error) isValid = false;
                    } else if (this.locationSubStep === 2) {
                        this.fields.city.touched = true;
                        this.validateField('city');
                        if(this.fields.city.error) isValid = false;
                    } else if (this.locationSubStep === 3) {
                        // Address is optional
                    }
                } else if (this.step === 5) {
                    Object.keys(this.fields.links).forEach(f => {
                        if(this.fields.links[f].value) {
                            this.validateLink(f);
                            if(this.fields.links[f].error) isValid = false;
                        }
                    });
                }
                if(!isValid) this.shakeForm();
                return isValid;
            },

            shakeForm() {
                gsap.to('.glass-morphism', { x: 10, duration: 0.1, repeat: 5, yoyo: true, ease: "power1.inOut" });
            },

            animateStep(nextStep, direction = 1, targetSubStep = 1) {
                if (this.isAnimating) return;
                this.isAnimating = true;

                const currentSubStep = this.step === 3 ? this.mediaSubStep : (this.step === 4 ? this.locationSubStep : 1);
                const currentEl = this.getStepSelector(this.step, currentSubStep);
                const nextEl = this.getStepSelector(nextStep, targetSubStep);
                const xOff = direction * 40;

                const tl = gsap.timeline({
                    onComplete: () => {
                        this.step = nextStep;
                        if (nextStep === 3) {
                            this.mediaSubStep = targetSubStep;
                        } else if (nextStep === 4) {
                            this.locationSubStep = targetSubStep;
                        }
                        this.isAnimating = false;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });

                tl.to(currentEl, { opacity: 0, x: -xOff/2, duration: 0.4, ease: 'power2.in' })
                  .set(nextEl, { opacity: 0, x: xOff })
                  .to(nextEl, { 
                      opacity: 1, 
                      x: 0, 
                      duration: 0.6, 
                      ease: 'power3.out',
                      onStart: () => { 
                          this.step = nextStep;
                          if (nextStep === 3) {
                              this.mediaSubStep = targetSubStep;
                          } else if (nextStep === 4) {
                              this.locationSubStep = targetSubStep;
                          }
                      }
                  }, '+=0.1')
                  .from(`${nextEl} > *`, { y: 15, opacity: 0, stagger: 0.08, duration: 0.5 }, '-=0.3');
            },

            goToStep(target) {
                if (this.isAnimating) return;
                if (target === this.step) {
                    if (target === 3 && this.mediaSubStep === 2) {
                        this.animateStep(3, -1, 1);
                    } else if (target === 4 && this.locationSubStep > 1) {
                        this.animateStep(4, -1, 1);
                    }
                    return;
                }
                if (target < this.step) {
                    const targetSub = (target === 3) ? 2 : ((target === 4) ? 3 : 1);
                    this.animateStep(target, -1, targetSub);
                    return;
                }
                
                // Going forward
                let can = true;
                const oldStep = this.step;
                const oldSub = this.step === 3 ? this.mediaSubStep : (this.step === 4 ? this.locationSubStep : 1);
                
                // Validate intermediate steps
                for (let i = this.step; i < target; i++) {
                    this.step = i;
                    if (i === 3) {
                        this.mediaSubStep = 1;
                        if (!this.validateCurrentStep()) {
                            can = false;
                            this.step = oldStep;
                            if (oldStep === 3) this.mediaSubStep = oldSub;
                            if (oldStep === 4) this.locationSubStep = oldSub;
                            break;
                        }
                        this.mediaSubStep = 2;
                        if (!this.validateCurrentStep()) {
                            can = false;
                            this.step = oldStep;
                            if (oldStep === 3) this.mediaSubStep = oldSub;
                            if (oldStep === 4) this.locationSubStep = oldSub;
                            break;
                        }
                    } else if (i === 4) {
                        this.locationSubStep = 1;
                        if (!this.validateCurrentStep()) {
                            can = false;
                            this.step = oldStep;
                            if (oldStep === 3) this.mediaSubStep = oldSub;
                            if (oldStep === 4) this.locationSubStep = oldSub;
                            break;
                        }
                        this.locationSubStep = 2;
                        if (!this.validateCurrentStep()) {
                            can = false;
                            this.step = oldStep;
                            if (oldStep === 3) this.mediaSubStep = oldSub;
                            if (oldStep === 4) this.locationSubStep = oldSub;
                            break;
                        }
                        this.locationSubStep = 3;
                        if (!this.validateCurrentStep()) {
                            can = false;
                            this.step = oldStep;
                            if (oldStep === 3) this.mediaSubStep = oldSub;
                            if (oldStep === 4) this.locationSubStep = oldSub;
                            break;
                        }
                    } else {
                        if (!this.validateCurrentStep()) {
                            can = false;
                            this.step = oldStep;
                            if (oldStep === 3) this.mediaSubStep = oldSub;
                            if (oldStep === 4) this.locationSubStep = oldSub;
                            break;
                        }
                    }
                }
                
                this.step = oldStep;
                if (oldStep === 3) this.mediaSubStep = oldSub;
                if (oldStep === 4) this.locationSubStep = oldSub;
                
                if (can) {
                    const targetSub = 1;
                    this.animateStep(target, 1, targetSub);
                }
            },

            nextStep(e) {
                if (this.isAnimating) return;
                
                if (this.step === 3 && this.mediaSubStep === 1) {
                    if (this.validateCurrentStep()) {
                        this.animateStep(3, 1, 2);
                    }
                    return;
                }

                if (this.step === 4) {
                    if (this.locationSubStep === 1) {
                        if (this.validateCurrentStep()) {
                            this.animateStep(4, 1, 2);
                        }
                        return;
                    }
                    if (this.locationSubStep === 2) {
                        if (this.validateCurrentStep()) {
                            this.animateStep(4, 1, 3);
                        }
                        return;
                    }
                }

                if (this.validateCurrentStep()) {
                    if (this.step < 5) {
                        const targetSub = 1;
                        this.animateStep(this.step + 1, 1, targetSub);
                    } else {
                        const btn = e.currentTarget;
                        btn.innerHTML = '<svg class="w-5 h-5 animate-spin mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                        btn.disabled = true;
                        gsap.to('.glass-morphism', { opacity: 0.6, scale: 0.98, duration: 0.5 });
                        document.getElementById('setupForm').submit();
                    }
                }
            },

            prevStep() {
                if (this.isAnimating) return;

                if (this.step === 3 && this.mediaSubStep === 2) {
                    this.animateStep(3, -1, 1);
                    return;
                }

                if (this.step === 4) {
                    if (this.locationSubStep === 3) {
                        this.animateStep(4, -1, 2);
                        return;
                    }
                    if (this.locationSubStep === 2) {
                        this.animateStep(4, -1, 1);
                        return;
                    }
                }

                if (this.step > 1) {
                    const targetSub = (this.step - 1 === 3) ? 2 : ((this.step - 1 === 4) ? 3 : 1);
                    this.animateStep(this.step - 1, -1, targetSub);
                }
            },

            selectCategory(id, name, event) {
                this.fields.category.value = id;
                this.searchCat = name;
                this.fields.category.touched = true;
                this.validateField('category');
                if (event && event.currentTarget) {
                    gsap.fromTo(event.currentTarget, 
                        { scale: 0.96 }, 
                        { scale: 1.02, duration: 0.2, yoyo: true, repeat: 1, ease: "power2.out" }
                    );
                }
                gsap.fromTo('.input-premium', { scale: 1 }, { scale: 1.01, duration: 0.1, yoyo: true, repeat: 1 });
            },

            async selectCountry(id, name, event) {
                this.selectedCountry = id;
                this.selectedCountryName = name;
                this.fields.city.value = null;
                this.cityName = '';
                this.fields.country.value = id;
                this.fields.country.touched = true;
                this.validateField('country');
                
                if (event && event.currentTarget) {
                    gsap.fromTo(event.currentTarget, 
                        { scale: 0.96 }, 
                        { scale: 1.02, duration: 0.2, yoyo: true, repeat: 1, ease: "power2.out" }
                    );
                }
                
                try {
                    const res = await fetch(`/api/countries/${id}/cities`);
                    this.cities = await res.json();
                } catch(e) {
                    this.cities = [];
                }
                
                setTimeout(() => {
                    this.nextStep();
                }, 300);
            },

            selectCity(id, name, event) {
                this.fields.city.value = id;
                this.cityName = name;
                this.fields.city.touched = true;
                this.validateField('city');
                
                if (event && event.currentTarget) {
                    gsap.fromTo(event.currentTarget, 
                        { scale: 0.96 }, 
                        { scale: 1.02, duration: 0.2, yoyo: true, repeat: 1, ease: "power2.out" }
                    );
                }
                
                setTimeout(() => {
                    this.nextStep();
                }, 300);
            },

            hasCategoryMatches() {
                if (this.searchCat === '') return true;
                const val = this.searchCat.toLowerCase();
                const buttons = document.querySelectorAll('.cat-btn');
                if (buttons.length === 0) return true;
                for (let btn of buttons) {
                    const name = btn.getAttribute('data-name');
                    if (name && name.includes(val)) {
                        return true;
                    }
                }
                return false;
            },

            handleImageUpload(inputEl, field) {
                const file = inputEl.files[0];
                if (!file) return;

                this.fields[field].touched = true;
                this.validateField(field);

                if (field === 'logo') {
                    this.logoLoading = true;
                } else {
                    this.coverLoading = true;
                }

                // If validation failed, clear everything and do not draw preview
                if (this.fields[field].error) {
                    inputEl.value = '';
                    if (field === 'logo') {
                        this.logoPrev = null;
                        this.logoLoading = false;
                    } else {
                        this.coverPrev = null;
                        this.coverLoading = false;
                    }
                    return;
                }

                // Completely eliminate UI freeze by reading & rendering off-screen asynchronously via Canvas
                requestAnimationFrame(() => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            
                            // Keep preview canvas small (300px) so rendering is instant and doesn't block the main thread
                            const max_size = 300;
                            let width = img.width;
                            let height = img.height;
                            
                            if (width > height) {
                                if (width > max_size) {
                                    height *= max_size / width;
                                    width = max_size;
                                }
                            } else {
                                if (height > max_size) {
                                    width *= max_size / height;
                                    height = max_size;
                                }
                            }
                            
                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(img, 0, 0, width, height);
                            
                            const dataUrl = canvas.toDataURL('image/jpeg', 0.75);
                            
                            if (field === 'logo') {
                                this.logoPrev = dataUrl;
                                setTimeout(() => { this.logoLoading = false; }, 300);
                            } else {
                                this.coverPrev = dataUrl;
                                setTimeout(() => { this.coverLoading = false; }, 300);
                            }
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }
        }
    }
</script>
@endpush
@endsection
