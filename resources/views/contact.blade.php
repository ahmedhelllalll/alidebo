@extends('layouts.app')

@section('title', __('landing.nav_contact') ?? 'Contact Us')

@section('content')
<style>
    .contact-font { font-family: 'Plus Jakarta Sans', 'Cairo', system-ui, sans-serif; }

    /* Social icon hover */
    .social-icon {
        transition-property: box-shadow, background-color, border-color, color;
        transition-duration: 0.3s;
        transition-timing-function: ease;
    }

    /* Info card hover */
    .info-card {
        transition: all 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-2px);
    }

    /* Success checkmark drawing animation */
    @keyframes drawCheck {
        0% { stroke-dashoffset: 24; }
        100% { stroke-dashoffset: 0; }
    }
    .animate-draw-check {
        stroke-dasharray: 24;
        stroke-dashoffset: 24;
        animation: drawCheck 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards 0.2s;
    }

    /* Smooth Fade Up Animations */
    @keyframes contactFadeUp {
        0% { opacity: 0; transform: translateY(40px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .contact-fade-element {
        opacity: 0;
        animation: contactFadeUp 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .contact-fade-delay-1 { animation-delay: 0.1s; }
    .contact-fade-delay-2 { animation-delay: 0.3s; }
    .contact-fade-delay-3 { animation-delay: 0.5s; }
    .contact-fade-delay-4 { animation-delay: 0.7s; }
    .contact-fade-delay-5 { animation-delay: 0.9s; }

    /* Social Icon Tooltips */
    .social-tooltip-wrap {
        position: relative;
    }
    .social-tooltip-wrap .social-tip {
        position: absolute;
        top: calc(100% + 12px);
        left: 50%;
        transform: translateX(-50%) translateY(-6px);
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.02em;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 50;
    }
    .social-tooltip-wrap .social-tip::after {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
    }
    .social-tooltip-wrap:hover .social-tip {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    /* Brand-specific tooltip colors */
    .tip-facebook  { background: #1877F2; color: #fff; }
    .tip-facebook::after  { border-bottom-color: #1877F2; }
    .tip-x         { background: #000000; color: #fff; }
    .tip-x::after         { border-bottom-color: #000000; }
    .tip-linkedin  { background: #0A66C2; color: #fff; }
    .tip-linkedin::after  { border-bottom-color: #0A66C2; }
    .tip-instagram { background: linear-gradient(135deg, #F58529, #DD2A7B, #8134AF); color: #fff; }
    .tip-instagram::after { border-bottom-color: #DD2A7B; }

    /* Social icon brand hover colors */
    .social-btn-facebook:hover  { background: #1877F2 !important; color: #fff !important; border-color: #1877F2 !important; }
    .social-btn-x:hover         { background: #000000 !important; color: #fff !important; border-color: #000000 !important; }
    .social-btn-linkedin:hover  { background: #0A66C2 !important; color: #fff !important; border-color: #0A66C2 !important; }
    .social-btn-instagram:hover { background: linear-gradient(135deg, #F58529, #DD2A7B, #8134AF) !important; color: #fff !important; border-color: #DD2A7B !important; }

    /* Mobile: disable fade animations, hide tooltips, fix form layout */
    @media (max-width: 768px) {
        .contact-fade-element {
            animation: none !important;
            opacity: 1 !important;
            transform: none !important;
        }
        .social-tooltip-wrap .social-tip {
            display: none !important;
        }
        .social-icon:hover {
            transform: none;
        }
    }
</style>

<div class="contact-font relative overflow-hidden bg-white dark:bg-[#09090b] min-h-screen">
    
    <!-- Ambient Corner Glows -->
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
        <div class="absolute -top-40 -start-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full"></div>
        <div class="absolute -top-40 -end-40 w-[500px] h-[500px] bg-primary/10 dark:bg-primary/10 blur-[140px] rounded-full"></div>
    </div>

    <div class="relative z-10 px-3 sm:px-6 lg:px-8 pb-3 sm:pb-6 lg:pb-8 pt-20 sm:pt-24">
        
        {{-- Card Container (matches hero-search dimensions) --}}
        <section class="relative w-full z-40 flex flex-col lg:flex-row min-h-0 lg:min-h-[85vh] rounded-2xl sm:rounded-[2rem] shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_-12px_rgba(0,0,0,0.3)] overflow-hidden bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-800/60 contact-fade-element">
            
            {{-- Background Gradient Effect --}}
            <div class="absolute inset-0 bg-gradient-to-br from-white via-white to-slate-50 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-950 opacity-90 pointer-events-none"></div>

            {{-- ── Left Part: Text Only ── --}}
            <div class="w-full lg:w-5/12 p-5 sm:p-10 lg:p-20 flex flex-col justify-center relative z-10 lg:border-e border-b lg:border-b-0 border-slate-200/60 dark:border-zinc-700/50">
                
                {{-- Decorative glow --}}
                <div class="absolute -top-32 -start-32 w-64 h-64 bg-primary/5 dark:bg-primary/10 blur-3xl rounded-full pointer-events-none z-0"></div>

                <div class="max-w-md w-full mx-auto relative z-10 contact-fade-element contact-fade-delay-1">
                    <div>
                        <h1 class="text-3xl sm:text-4xl lg:text-6xl font-[900] tracking-tight text-slate-900 dark:text-white mb-4 sm:mb-6 leading-tight">
                            {{ __('landing.contact_hero_title_1') ?? 'Let\'s start a ' }}<br class="hidden sm:block"><span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-primary-light">{{ __('landing.contact_hero_title_highlight') ?? 'conversation' }}</span>
                        </h1>
                        <p class="text-base sm:text-lg text-slate-500 dark:text-zinc-400 font-medium leading-relaxed">
                            {{ __('landing.contact_hero_subtitle') ?? 'Have a question or need help? Our friendly team is always ready to listen and will get back to you shortly.' }}
                        </p>
                    </div>

                    {{-- Alidebo Market Features --}}
                    <div class="mt-8 sm:mt-12 space-y-6 sm:space-y-8 border-t border-slate-200/60 dark:border-zinc-700/50 pt-8 sm:pt-10">
                        <div class="flex items-start gap-4 sm:gap-5">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0 shadow-sm border border-primary/20">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1.5">{{ __('landing.market_feature_1_title') ?? 'Global Trade Network' }}</h4>
                                <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 leading-relaxed">{{ __('landing.market_feature_1_desc') ?? 'Connect with verified buyers and sellers from around the world in one unified, seamless marketplace.' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 sm:gap-5">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0 shadow-sm border border-primary/20">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1.5">{{ __('landing.market_feature_2_title') ?? 'Secure B2B Platform' }}</h4>
                                <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 leading-relaxed">{{ __('landing.market_feature_2_desc') ?? 'Trade with absolute confidence through our vetted company directory and encrypted communication channels.' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 sm:gap-5">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0 shadow-sm border border-primary/20">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1.5">{{ __('landing.market_feature_3_title') ?? 'Accelerate Growth' }}</h4>
                                <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 leading-relaxed">{{ __('landing.market_feature_3_desc') ?? 'Expand your market reach and discover premium business opportunities effortlessly with Alidebo.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Right Part: Contact Form ── --}}
            <div class="w-full lg:w-7/12 p-5 sm:p-10 lg:p-20 flex flex-col justify-center relative z-10 bg-transparent" x-data="contactForm()">
                <div class="relative z-10 max-w-xl mx-auto w-full min-h-0 sm:min-h-[550px] contact-fade-element contact-fade-delay-2">
                    
                    <!-- Form Section -->
                    <div x-show="!submitted" 
                         x-transition:enter="transition ease-out duration-500 delay-300" 
                         x-transition:enter-start="opacity-0 transform translate-y-4" 
                         x-transition:enter-end="opacity-100 transform translate-y-0" 
                         x-transition:leave="transition ease-in duration-300" 
                         x-transition:leave-start="opacity-100 transform translate-y-0" 
                         x-transition:leave-end="opacity-0 transform -translate-y-4" 
                         class="relative sm:absolute inset-0 z-10 w-full flex flex-col justify-center"
                         :class="submitted ? 'pointer-events-none' : ''">
                        
                        <div class="mb-6 sm:mb-10">
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-[800] text-slate-800 dark:text-white mb-2 sm:mb-3 tracking-tight">
                                {{ __('landing.send_message') ?? 'Drop us a message' }}
                            </h2>
                            <p class="text-slate-500 dark:text-zinc-400 text-sm sm:text-base font-medium leading-relaxed">
                                {{ __('landing.contact_hero_subtitle_form') ?? 'Fill out the form below and we\'ll respond within a few hours.' }}
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                {{-- First Name --}}
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-600 dark:text-zinc-400">{{ __('landing.first_name') ?? 'First Name' }}</label>
                                    <input type="text" x-model="form.first_name" @blur="validateField('first_name', form.first_name)" placeholder="{{ __('landing.contact_placeholder_fname') ?? 'John' }}" required
                                        class="w-full bg-white dark:bg-zinc-900/60 border rounded-xl sm:rounded-2xl py-3 sm:py-3.5 px-4 sm:px-5 text-slate-800 dark:text-white text-sm sm:text-[15px] focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300 placeholder-slate-400 dark:placeholder-zinc-500 shadow-sm"
                                        :class="errors.first_name ? 'border-red-500 focus:border-red-500 bg-red-50 dark:bg-red-900/10' : (form.first_name.length >= 2 ? 'border-emerald-500/50 focus:border-emerald-500' : 'border-slate-200 dark:border-zinc-700/80 focus:border-primary/50')">
                                    <p x-show="errors.first_name" x-text="errors.first_name?.[0]" class="text-red-500 text-xs font-bold mt-1" x-cloak></p>
                                </div>
                                {{-- Last Name --}}
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-600 dark:text-zinc-400">{{ __('landing.last_name') ?? 'Last Name' }}</label>
                                    <input type="text" x-model="form.last_name" @blur="validateField('last_name', form.last_name)" placeholder="{{ __('landing.contact_placeholder_lname') ?? 'Doe' }}" required
                                        class="w-full bg-white dark:bg-zinc-900/60 border rounded-xl sm:rounded-2xl py-3 sm:py-3.5 px-4 sm:px-5 text-slate-800 dark:text-white text-sm sm:text-[15px] focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300 placeholder-slate-400 dark:placeholder-zinc-500 shadow-sm"
                                        :class="errors.last_name ? 'border-red-500 focus:border-red-500 bg-red-50 dark:bg-red-900/10' : (form.last_name.length >= 2 ? 'border-emerald-500/50 focus:border-emerald-500' : 'border-slate-200 dark:border-zinc-700/80 focus:border-primary/50')">
                                    <p x-show="errors.last_name" x-text="errors.last_name?.[0]" class="text-red-500 text-xs font-bold mt-1" x-cloak></p>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-600 dark:text-zinc-400">{{ __('landing.email_address') ?? 'Email Address' }}</label>
                                <input type="email" x-model="form.email" @blur="validateField('email', form.email)" placeholder="{{ __('landing.contact_placeholder_email') ?? 'john@example.com' }}" required
                                    class="w-full bg-white dark:bg-zinc-900/60 border rounded-xl sm:rounded-2xl py-3 sm:py-3.5 px-4 sm:px-5 text-slate-800 dark:text-white text-sm sm:text-[15px] focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300 placeholder-slate-400 dark:placeholder-zinc-500 shadow-sm"
                                    :class="errors.email ? 'border-red-500 focus:border-red-500 bg-red-50 dark:bg-red-900/10' : (form.email.length > 5 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email) ? 'border-emerald-500/50 focus:border-emerald-500' : 'border-slate-200 dark:border-zinc-700/80 focus:border-primary/50')">
                                <p x-show="errors.email" x-text="errors.email?.[0]" class="text-red-500 text-xs font-bold mt-1" x-cloak></p>
                            </div>

                            {{-- Message --}}
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-600 dark:text-zinc-400">{{ __('landing.message') ?? 'Message' }}</label>
                                <textarea x-model="form.message" @blur="validateField('message', form.message)" placeholder="{{ __('landing.contact_placeholder_msg') ?? 'How can we help you?' }}" required rows="4"
                                    class="w-full bg-white dark:bg-zinc-900/60 border rounded-xl sm:rounded-2xl py-3 sm:py-4 px-4 sm:px-5 text-slate-800 dark:text-white text-sm sm:text-[15px] focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300 placeholder-slate-400 dark:placeholder-zinc-500 shadow-sm resize-none"
                                    :class="errors.message ? 'border-red-500 focus:border-red-500 bg-red-50 dark:bg-red-900/10' : (form.message.length >= 10 && form.message.length <= maxLength ? 'border-emerald-500/50 focus:border-emerald-500' : 'border-slate-200 dark:border-zinc-700/80 focus:border-primary/50')"></textarea>
                                
                                <div class="flex justify-between items-start mt-1">
                                    <p x-show="errors.message" x-text="errors.message?.[0]" class="text-red-500 text-xs font-bold" x-cloak></p>
                                    <p x-show="!errors.message" class="text-xs"></p>
                                    <p class="text-[11px] font-bold transition-colors" 
                                        :class="form.message.length > maxLength ? 'text-red-500' : (form.message.length >= 10 ? 'text-emerald-500' : 'text-slate-400 dark:text-zinc-500')"
                                        x-text="form.message.length + ' / ' + maxLength"></p>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="pt-2">
                                <button type="submit" :disabled="loading" class="group w-full py-3.5 sm:py-4 px-6 sm:px-8 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl sm:rounded-2xl font-bold text-sm sm:text-[15px] shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-300 inline-flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
                                    <span x-show="!loading" class="flex items-center gap-3">
                                        {{ __('landing.send_message_btn') ?? 'Send Message' }}
                                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                    <span x-show="loading" class="flex items-center gap-3" x-cloak>
                                        <svg class="animate-spin h-4 w-4 text-white dark:text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        {{ __('landing.sending') ?? 'Sending...' }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Success Section -->
                    <div x-show="submitted" 
                         x-transition:enter="transition ease-out duration-500 delay-300" 
                         x-transition:enter-start="opacity-0 transform scale-95" 
                         x-transition:enter-end="opacity-100 transform scale-100" 
                         x-transition:leave="transition ease-in duration-300" 
                         x-transition:leave-start="opacity-100 transform scale-100" 
                         x-transition:leave-end="opacity-0 transform scale-95" 
                         class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center p-5 sm:p-10" 
                         x-cloak>
                        <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 rounded-full flex items-center justify-center mb-6 border-8 border-emerald-50/50 dark:border-emerald-900/10">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-[900] text-slate-900 dark:text-white mb-3">{{ __('landing.success') ?? 'Success!' }}</h3>
                        <p class="text-slate-500 dark:text-zinc-400 font-medium text-base sm:text-lg">{{ __('landing.contact_success') ?? 'Thank you for reaching out. We will get back to you shortly.' }}</p>
                    </div>

                </div>
            </div>

        </section>

        {{-- ── Contact Info & Social Media – Two Cards ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-6 mt-3 sm:mt-6 relative z-40">

            {{-- Card 1: Contact Methods --}}
            <div class="contact-fade-element contact-fade-delay-3 rounded-2xl sm:rounded-[2rem] bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-800/60 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.08)] dark:shadow-[0_8px_30px_-12px_rgba(0,0,0,0.25)] p-5 sm:p-10 flex flex-col justify-between">
                <div class="mb-6">
                    <h3 class="text-lg font-[800] text-slate-800 dark:text-white tracking-tight mb-1">{{ __('landing.contact_reach_title') ?? 'Reach us directly' }}</h3>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">{{ __('landing.contact_reach_desc') ?? 'Get in touch through any of these channels.' }}</p>
                </div>
                <div class="flex flex-col gap-4">
                    {{-- Phone --}}
                    <a href="tel:+85246196281" class="group flex items-center gap-3 sm:gap-5 p-3.5 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-50/50 dark:bg-zinc-800 border border-slate-100 dark:border-zinc-700 hover:bg-slate-100 dark:hover:bg-zinc-700 transition-colors duration-300">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-gradient-to-br from-primary/15 to-primary/5 dark:from-primary/20 dark:to-primary/5 flex items-center justify-center text-primary shrink-0 group-hover:scale-105 transition-transform duration-300 border border-primary/10">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">{{ __('landing.contact_phone_label') ?? 'Phone' }}</p>
                            <p class="text-sm sm:text-base font-[800] text-slate-800 dark:text-zinc-100 tracking-wide" dir="ltr">+852 4619 6281</p>
                            <p class="text-[11px] sm:text-[12px] font-medium text-slate-400 dark:text-zinc-500 mt-0.5 hidden sm:block">{{ __('landing.contact_phone_hint') ?? 'Available during business hours' }}</p>
                        </div>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-slate-100 dark:bg-white dark:shadow-[0_0_12px_rgba(255,255,255,0.4)] flex items-center justify-center group-hover:bg-primary/10 group-hover:text-primary transition-all duration-300 shrink-0">
                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-900 group-hover:text-primary group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 rtl:rotate-180 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                    {{-- Email --}}
                    <a href="mailto:contact@alidebo.com" class="group flex items-center gap-3 sm:gap-5 p-3.5 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-50/50 dark:bg-zinc-800 border border-slate-100 dark:border-zinc-700 hover:bg-slate-100 dark:hover:bg-zinc-700 transition-colors duration-300">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-gradient-to-br from-primary/15 to-primary/5 dark:from-primary/20 dark:to-primary/5 flex items-center justify-center text-primary shrink-0 group-hover:scale-105 transition-transform duration-300 border border-primary/10">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">{{ __('landing.contact_email_label') ?? 'Email' }}</p>
                            <p class="text-sm sm:text-base font-[800] text-slate-800 dark:text-zinc-100" dir="ltr">contact@alidebo.com</p>
                            <p class="text-[11px] sm:text-[12px] font-medium text-slate-400 dark:text-zinc-500 mt-0.5 hidden sm:block">{{ __('landing.contact_email_hint') ?? 'We reply within a few hours' }}</p>
                        </div>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-slate-100 dark:bg-white dark:shadow-[0_0_12px_rgba(255,255,255,0.4)] flex items-center justify-center group-hover:bg-primary/10 group-hover:text-primary transition-all duration-300 shrink-0">
                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-900 group-hover:text-primary group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 rtl:rotate-180 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Card 2: Social Media --}}
            <div class="contact-fade-element contact-fade-delay-4 rounded-2xl sm:rounded-[2rem] bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-800/60 shadow-[0_8px_30px_-12px_rgba(0,0,0,0.08)] dark:shadow-[0_8px_30px_-12px_rgba(0,0,0,0.25)] p-5 sm:p-10 flex flex-col items-center text-center justify-center gap-5 sm:gap-6">
                <div>
                    <h3 class="text-lg font-[800] text-slate-800 dark:text-white tracking-tight mb-1">{{ __('landing.contact_follow_title') ?? 'Follow us for early access' }}</h3>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">{{ __('landing.contact_follow_desc') ?? 'Be the first to know about new features, updates, and exclusive offers.' }}</p>
                </div>
                <div class="flex items-center justify-center gap-2.5 sm:gap-3 flex-wrap">
                    {{-- Facebook --}}
                    <a href="https://www.facebook.com/share/1DVyDHa1Lo/" target="_blank" rel="noopener" aria-label="Facebook"
                       class="social-tooltip-wrap social-icon social-btn-facebook w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-white border border-slate-200/60 dark:border-white/50 dark:shadow-[0_0_12px_rgba(255,255,255,0.4)] flex items-center justify-center text-slate-500 dark:text-slate-900 transition-all duration-300">
                        <span class="social-tip tip-facebook">Facebook</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
                    </a>
                    {{-- X (Twitter) --}}
                    <a href="https://x.com/co_alidebo" target="_blank" rel="noopener" aria-label="X"
                       class="social-tooltip-wrap social-icon social-btn-x w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-white border border-slate-200/60 dark:border-white/50 dark:shadow-[0_0_12px_rgba(255,255,255,0.4)] flex items-center justify-center text-slate-500 dark:text-slate-900 transition-all duration-300">
                        <span class="social-tip tip-x">X (Twitter)</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    {{-- LinkedIn --}}
                    <a href="https://www.linkedin.com/company/alidebo/" target="_blank" rel="noopener" aria-label="LinkedIn"
                       class="social-tooltip-wrap social-icon social-btn-linkedin w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-white border border-slate-200/60 dark:border-white/50 dark:shadow-[0_0_12px_rgba(255,255,255,0.4)] flex items-center justify-center text-slate-500 dark:text-slate-900 transition-all duration-300">
                        <span class="social-tip tip-linkedin">LinkedIn</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    {{-- Instagram --}}
                    <a href="https://www.instagram.com/co.alidebo?igsh=MW81ZDNtcGJzc2Vjcw==" target="_blank" rel="noopener" aria-label="Instagram"
                       class="social-tooltip-wrap social-icon social-btn-instagram w-12 h-12 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-white border border-slate-200/60 dark:border-white/50 dark:shadow-[0_0_12px_rgba(255,255,255,0.4)] flex items-center justify-center text-slate-500 dark:text-slate-900 transition-all duration-300">
                        <span class="social-tip tip-instagram">Instagram</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('contactForm', () => ({
            form: {
                first_name: '',
                last_name: '',
                email: '',
                message: ''
            },
            errors: {},
            loading: false,
            maxLength: 1000,
            submitted: false,
            
            resetForm() {
                this.form = { first_name: '', last_name: '', email: '', message: '' };
                this.submitted = false;
            },

            validateField(field, value) {
                this.errors[field] = null;
                
                if (!value || value.trim() === '') {
                    this.errors[field] = ['This field is required.'];
                    return;
                }

                if (field === 'first_name' || field === 'last_name') {
                    if (value.length < 2) {
                        this.errors[field] = ['{{ __("landing.contact_error_name_min") ?? "Must be at least 2 characters." }}'];
                    }
                }

                if (field === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        this.errors[field] = ['{{ __("landing.contact_error_email_invalid") ?? "Please enter a valid email address." }}'];
                    }
                }

                if (field === 'message') {
                    if (value.length < 10) {
                        this.errors[field] = ['{{ __("landing.contact_error_msg_min") ?? "Message must be at least 10 characters." }}'];
                    } else if (value.length > this.maxLength) {
                        this.errors[field] = ['{{ __("landing.contact_error_msg_max") ?? "Message is too long." }}'];
                    }
                }
            },

            validateAll() {
                this.validateField('first_name', this.form.first_name);
                this.validateField('last_name', this.form.last_name);
                this.validateField('email', this.form.email);
                this.validateField('message', this.form.message);
            },

            async submit() {
                this.validateAll();
                
                // Check if any errors exist
                const hasErrors = Object.values(this.errors).some(error => error !== null);
                if (hasErrors) return;
                
                this.loading = true;
                
                try {
                    const response = await fetch('{{ route("contact.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.submitted = true;
                        this.errors = {};
                        
                        // Automatically fade out success message and reset form after 4 seconds
                        setTimeout(() => {
                            this.resetForm();
                        }, 4000);
                    } else {
                        if (response.status === 422) {
                            this.errors = data.errors;
                        } else if (response.status === 429) {
                            alert('{{ __("landing.contact_rate_limit") ?? "Too many requests. Please try again later." }}');
                        } else {
                            alert(data.message || '{{ __("landing.contact_error_general") ?? "Something went wrong." }}');
                        }
                    }
                } catch (error) {
                    showToast('{{ __("landing.error") ?? "Error" }}', '{{ __("landing.contact_network_error") ?? "Network error. Please try again." }}');
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endpush
@endsection
