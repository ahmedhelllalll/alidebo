@extends('layouts.app')

@section('title', __('landing.nav_contact') ?? 'Contact Us')

@section('content')
<style>
    .contact-font { font-family: 'Plus Jakarta Sans', 'Cairo', system-ui, sans-serif; }

    /* Input focus glow */
    .contact-input:focus {
        box-shadow: 0 0 0 3px rgba(244, 80, 24, 0.12), 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    /* Glassmorphism card */
    .glass-form {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.72) 0%, rgba(255, 255, 255, 0.45) 100%);
        backdrop-filter: blur(32px);
        -webkit-backdrop-filter: blur(32px);
    }
    .dark .glass-form {
        background: linear-gradient(135deg, rgba(20, 20, 23, 0.6) 0%, rgba(9, 9, 11, 0.4) 100%);
    }

    /* Contact info card hover */
    .info-card {
        transition-property: transform, box-shadow, background-color, border-color;
        transition-duration: 0.5s;
        transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform, box-shadow;
    }
    .info-card:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 20px 40px -12px rgba(244, 80, 24, 0.12);
    }
    .dark .info-card:hover {
        box-shadow: 0 20px 40px -12px rgba(244, 80, 24, 0.08);
    }

    /* Social icon pulse on hover */
    .social-icon {
        transition-property: transform, box-shadow, background-color, border-color;
        transition-duration: 0.4s;
        transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform;
    }
    .social-icon:hover {
        transform: translateY(-4px) scale(1.08);
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
</style>

<div class="contact-font relative overflow-hidden bg-white dark:bg-[#09090b] min-h-screen">

    <!-- Ambient Corner Glows -->
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
        <div class="absolute -top-40 -start-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full"></div>
        <div class="absolute -top-40 -end-40 w-[500px] h-[500px] bg-primary/10 dark:bg-primary/10 blur-[140px] rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-32 sm:pt-36 pb-24 sm:pb-32">

        {{-- ─── Hero Section ─── --}}
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20 opacity-0 animate-slide-up" style="animation-delay: 50ms;">


            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-[900] tracking-tight text-slate-900 dark:text-white mb-6 leading-[1.1]">
                {{ __('landing.contact_hero_title_1') ?? 'How can we ' }}<span class="glow-text">{{ __('landing.contact_hero_title_highlight') ?? 'help you?' }}</span>
            </h1>
            <p class="text-lg sm:text-xl text-slate-500 dark:text-zinc-400 font-medium leading-relaxed max-w-2xl mx-auto">
                {{ __('landing.contact_hero_subtitle') ?? 'Whether you have a question about our platform, need technical support, or simply want to share your thoughts—our friendly team is always ready to listen.' }}
            </p>
        </div>

        {{-- ─── Main Content Grid ─── --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-10 items-start">

            {{-- ── Left: Contact Form ── --}}
            <div class="md:col-span-7 opacity-0 animate-slide-up" style="animation-delay: 150ms;">
                <div class="glass-form rounded-[2rem] border border-white/60 dark:border-zinc-800/50 p-5 sm:p-10 lg:p-12 relative overflow-hidden shadow-[0_8px_40px_-12px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_40px_-12px_rgba(0,0,0,0.3)]">

                    {{-- Smooth Ambient background glows inside the card --}}
                    <div class="absolute -top-32 -end-32 w-64 h-64 bg-primary/10 dark:bg-primary/15 blur-3xl rounded-full pointer-events-none z-0"></div>
                    <div class="absolute -bottom-32 -start-32 w-64 h-64 bg-primary/5 dark:bg-primary/8 blur-3xl rounded-full pointer-events-none z-0"></div>

                    <div class="relative z-10" x-data="contactForm()">
                        <!-- Form Section -->
                        <div x-show="!submitted" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
                            <h2 class="text-2xl sm:text-3xl font-[800] text-slate-900 dark:text-white mb-2">
                                {{ __('landing.send_message') ?? 'Drop us a quick message' }}
                            </h2>
                            <p class="text-slate-400 dark:text-zinc-500 text-sm font-medium mb-8">{{ __('landing.contact_hero_subtitle_form') ?? 'We usually respond within a few hours.' }}</p>

                            <form @submit.prevent="submit" class="space-y-5">
                                @csrf
                                {{-- Name row --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label class="text-[13px] font-bold text-slate-600 dark:text-zinc-400 tracking-wide">{{ __('landing.first_name') ?? 'First name' }}</label>
                                        <input type="text" x-model="form.first_name" @blur="validateField('first_name', form.first_name)" placeholder="{{ __('landing.contact_placeholder_fname') ?? 'e.g. John' }}" required
                                            class="contact-input w-full bg-white/80 dark:bg-zinc-900/60 backdrop-blur-sm border rounded-xl py-3.5 px-4 text-slate-900 dark:text-white text-[15px] focus:outline-none transition-all duration-300 placeholder:text-slate-300 dark:placeholder:text-zinc-600"
                                            :class="errors.first_name ? 'border-red-500 focus:border-red-500 bg-red-50/50 dark:bg-red-900/10' : (form.first_name.length >= 2 ? 'border-emerald-500/50 focus:border-emerald-500' : 'border-slate-200/80 dark:border-zinc-700/50 focus:border-primary/40')">
                                        <p x-show="errors.first_name" x-text="errors.first_name[0]" class="text-red-500 text-[11px] font-bold mt-1.5 ms-1" x-cloak></p>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[13px] font-bold text-slate-600 dark:text-zinc-400 tracking-wide">{{ __('landing.last_name') ?? 'Last name' }}</label>
                                        <input type="text" x-model="form.last_name" @blur="validateField('last_name', form.last_name)" placeholder="{{ __('landing.contact_placeholder_lname') ?? 'e.g. Doe' }}" required
                                            class="contact-input w-full bg-white/80 dark:bg-zinc-900/60 backdrop-blur-sm border rounded-xl py-3.5 px-4 text-slate-900 dark:text-white text-[15px] focus:outline-none transition-all duration-300 placeholder:text-slate-300 dark:placeholder:text-zinc-600"
                                            :class="errors.last_name ? 'border-red-500 focus:border-red-500 bg-red-50/50 dark:bg-red-900/10' : (form.last_name.length >= 2 ? 'border-emerald-500/50 focus:border-emerald-500' : 'border-slate-200/80 dark:border-zinc-700/50 focus:border-primary/40')">
                                        <p x-show="errors.last_name" x-text="errors.last_name[0]" class="text-red-500 text-[11px] font-bold mt-1.5 ms-1" x-cloak></p>
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="space-y-1.5">
                                    <label class="text-[13px] font-bold text-slate-600 dark:text-zinc-400 tracking-wide">{{ __('landing.email_address') ?? 'Email address' }}</label>
                                    <input type="email" x-model="form.email" @blur="validateField('email', form.email)" placeholder="{{ __('landing.contact_placeholder_email') ?? 'john@example.com' }}" required
                                        class="contact-input w-full bg-white/80 dark:bg-zinc-900/60 backdrop-blur-sm border rounded-xl py-3.5 px-4 text-slate-900 dark:text-white text-[15px] focus:outline-none transition-all duration-300 placeholder:text-slate-300 dark:placeholder:text-zinc-600"
                                        :class="errors.email ? 'border-red-500 focus:border-red-500 bg-red-50/50 dark:bg-red-900/10' : (form.email.length > 5 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email) ? 'border-emerald-500/50 focus:border-emerald-500' : 'border-slate-200/80 dark:border-zinc-700/50 focus:border-primary/40')">
                                    <p x-show="errors.email" x-text="errors.email[0]" class="text-red-500 text-[11px] font-bold mt-1.5 ms-1" x-cloak></p>
                                </div>

                                {{-- Message --}}
                                <div class="space-y-1.5">
                                    <label class="text-[13px] font-bold text-slate-600 dark:text-zinc-400 tracking-wide">{{ __('landing.message') ?? 'How can we help you today?' }}</label>
                                    <textarea x-model="form.message" @blur="validateField('message', form.message)" placeholder="{{ __('landing.contact_placeholder_msg') ?? 'Tell us a little bit about what you need...' }}" required rows="5"
                                        class="contact-input w-full bg-white/80 dark:bg-zinc-900/60 backdrop-blur-sm border rounded-xl py-3.5 px-4 text-slate-900 dark:text-white text-[15px] focus:outline-none transition-all duration-300 placeholder:text-slate-300 dark:placeholder:text-zinc-600 resize-none"
                                        :class="errors.message ? 'border-red-500 focus:border-red-500 bg-red-50/50 dark:bg-red-900/10' : (form.message.length >= 10 && form.message.length <= maxLength ? 'border-emerald-500/50 focus:border-emerald-500' : 'border-slate-200/80 dark:border-zinc-700/50 focus:border-primary/40')"></textarea>
                                    
                                    <div class="flex justify-between items-start mt-1.5 ms-1 px-1">
                                        <p x-show="errors.message" x-text="errors.message[0]" class="text-red-500 text-[11px] font-bold" x-cloak></p>
                                        <p x-show="!errors.message" class="text-[11px]"></p>
                                        <p class="text-[11px] font-bold transition-colors" 
                                           :class="form.message.length > maxLength ? 'text-red-500' : (form.message.length >= 10 ? 'text-emerald-500' : 'text-slate-400 dark:text-zinc-500')"
                                           x-text="form.message.length + ' / ' + maxLength"></p>
                                    </div>
                                </div>

                                {{-- Submit --}}
                                <div class="pt-2">
                                    <button type="submit" :disabled="loading" class="group w-full sm:w-auto py-4 px-10 bg-primary hover:bg-primary-light text-white rounded-2xl font-bold text-[15px] shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-300 inline-flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:-translate-y-0">
                                        <span x-show="!loading" class="flex items-center gap-3">
                                            {{ __('landing.send_message_btn') ?? 'Send message' }}
                                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </span>
                                        <span x-show="loading" class="flex items-center gap-3" x-cloak>
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            {{ __('landing.sending') ?? 'Sending...' }}
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Success Section -->
                        <div x-show="submitted" x-cloak x-transition:enter="transition ease-out duration-500 delay-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="text-center py-14 px-4 flex flex-col items-center justify-center">
                            {{-- Premium Success Checkmark with Glow and Draw Effect --}}
                            <div class="relative mb-8">
                                <div class="absolute inset-0 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 blur-2xl animate-pulse"></div>
                                <div class="relative w-24 h-24 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30 dark:shadow-emerald-500/20">
                                    <svg class="w-12 h-12 stroke-white animate-draw-check" fill="none" viewBox="0 0 24 24" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>

                            <h3 class="text-3xl font-[900] tracking-tight text-slate-900 dark:text-white mb-4">
                                {{ __('landing.success') ?? 'Success!' }}
                            </h3>
                            <p x-text="successMessage" class="text-slate-500 dark:text-zinc-400 text-[16px] sm:text-lg font-medium max-w-md mx-auto mb-10 leading-relaxed"></p>

                            <button @click="submitted = false" class="py-4 px-10 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-slate-900 rounded-2xl font-bold text-[15px] shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-300">
                                {{ app()->getLocale() == 'ar' ? 'إرسال رسالة أخرى' : 'Send another message' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Right: Contact Info ── --}}
            <div class="md:col-span-5 space-y-6 opacity-0 animate-slide-up" style="animation-delay: 250ms;">

                {{-- Heading --}}
                <div class="ps-1 mb-2">
                    <h3 class="text-2xl sm:text-3xl font-[900] text-slate-900 dark:text-white mb-3 leading-tight">
                        {{ __('landing.reach_out') ?? 'Prefer to connect directly?' }}
                    </h3>
                    <p class="text-slate-500 dark:text-zinc-400 leading-relaxed text-[15px]">
                        {{ __('landing.reach_out_subtitle') ?? 'If you need immediate assistance or just prefer a direct conversation, feel free to reach out using the details below.' }}
                    </p>
                </div>

                {{-- Phone Card --}}
                <a href="tel:+85246196281" class="info-card group block bg-white/70 dark:bg-zinc-900/40 backdrop-blur-xl border border-slate-200/60 dark:border-zinc-800/50 rounded-2xl p-4 sm:p-6 cursor-pointer">
                    <div class="flex items-center gap-3 sm:gap-5">
                        <div class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary/8 dark:bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 group-hover:shadow-lg group-hover:shadow-primary/20">
                            <svg class="w-5.5 h-5.5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest block mb-1">{{ __('landing.call_us') ?? 'Give us a call' }}</span>
                            <span class="text-[16px] sm:text-xl font-[800] text-slate-900 dark:text-white group-hover:text-primary transition-colors duration-300 block">+852 4619 6281</span>
                        </div>
                        <svg class="w-5 h-5 text-slate-300 dark:text-zinc-600 group-hover:text-primary transition-all duration-300 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>

                {{-- Email Card --}}
                <a href="mailto:contact@alidebo.com" class="info-card group block bg-white/70 dark:bg-zinc-900/40 backdrop-blur-xl border border-slate-200/60 dark:border-zinc-800/50 rounded-2xl p-4 sm:p-6 cursor-pointer">
                    <div class="flex items-center gap-3 sm:gap-5">
                        <div class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary/8 dark:bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300 group-hover:shadow-lg group-hover:shadow-primary/20">
                            <svg class="w-5.5 h-5.5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest block mb-1">{{ __('landing.email_us') ?? 'Send an email' }}</span>
                            <span class="text-[16px] sm:text-xl font-[800] text-slate-900 dark:text-white group-hover:text-primary transition-colors duration-300 block truncate">contact@alidebo.com</span>
                        </div>
                        <svg class="w-5 h-5 text-slate-300 dark:text-zinc-600 group-hover:text-primary transition-all duration-300 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>

                {{-- Social Media Card --}}
                <div class="bg-white/70 dark:bg-zinc-900/40 backdrop-blur-xl border border-slate-200/60 dark:border-zinc-800/50 rounded-2xl p-4 sm:p-6">
                    <span class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest block mb-5">{{ __('landing.follow_us') ?? 'Connect with us' }}</span>
                    <div class="grid grid-cols-4 sm:grid-cols-2 gap-3 sm:gap-4">
                        
                        {{-- Twitter/X --}}
                        <a href="#" class="social-icon group flex items-center justify-center sm:justify-start gap-3 p-3 sm:p-4 rounded-xl bg-slate-50/80 dark:bg-zinc-800/50 border border-slate-200/50 dark:border-zinc-700/50 hover:bg-slate-900 dark:hover:bg-white hover:border-slate-900 dark:hover:border-white hover:shadow-lg hover:shadow-slate-900/20 dark:hover:shadow-white/20 transition-all duration-300">
                            <svg class="w-5 h-5 fill-slate-500 dark:fill-zinc-400 group-hover:fill-white dark:group-hover:fill-zinc-900 transition-colors duration-300" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.008 5.961H5.078z"/></svg>
                            <span class="hidden sm:block text-sm font-bold text-slate-700 dark:text-zinc-300 group-hover:text-white dark:group-hover:text-zinc-900 transition-colors duration-300">Twitter</span>
                        </a>

                        {{-- LinkedIn --}}
                        <a href="#" class="social-icon group flex items-center justify-center sm:justify-start gap-3 p-3 sm:p-4 rounded-xl bg-slate-50/80 dark:bg-zinc-800/50 border border-slate-200/50 dark:border-zinc-700/50 hover:bg-[#0A66C2] hover:border-[#0A66C2] hover:shadow-lg hover:shadow-[#0A66C2]/20 transition-all duration-300">
                            <svg class="w-5 h-5 fill-slate-500 dark:fill-zinc-400 group-hover:fill-white transition-colors duration-300" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            <span class="hidden sm:block text-sm font-bold text-slate-700 dark:text-zinc-300 group-hover:text-white transition-colors duration-300">LinkedIn</span>
                        </a>

                        {{-- Facebook --}}
                        <a href="#" class="social-icon group flex items-center justify-center sm:justify-start gap-3 p-3 sm:p-4 rounded-xl bg-slate-50/80 dark:bg-zinc-800/50 border border-slate-200/50 dark:border-zinc-700/50 hover:bg-[#1877F2] hover:border-[#1877F2] hover:shadow-lg hover:shadow-[#1877F2]/20 transition-all duration-300">
                            <svg class="w-5 h-5 fill-slate-500 dark:fill-zinc-400 group-hover:fill-white transition-colors duration-300" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            <span class="hidden sm:block text-sm font-bold text-slate-700 dark:text-zinc-300 group-hover:text-white transition-colors duration-300">Facebook</span>
                        </a>

                        {{-- Instagram --}}
                        <a href="#" class="social-icon group flex items-center justify-center sm:justify-start gap-3 p-3 sm:p-4 rounded-xl bg-slate-50/80 dark:bg-zinc-800/50 border border-slate-200/50 dark:border-zinc-700/50 hover:bg-gradient-to-tr hover:from-yellow-400 hover:via-red-500 hover:to-purple-500 hover:border-transparent hover:shadow-lg hover:shadow-red-500/20 transition-all duration-300">
                            <svg class="w-5 h-5 fill-slate-500 dark:fill-zinc-400 group-hover:fill-white transition-colors duration-300" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.20 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.88z"/></svg>
                            <span class="hidden sm:block text-sm font-bold text-slate-700 dark:text-zinc-300 group-hover:text-white transition-colors duration-300">Instagram</span>
                        </a>

                    </div>
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
            maxLength: 2000,
            submitted: false,
            successMessage: '',
            
            init() {
                this.$watch('form.first_name', (value) => { if (this.errors.first_name) this.validateField('first_name', value) });
                this.$watch('form.last_name', (value) => { if (this.errors.last_name) this.validateField('last_name', value) });
                this.$watch('form.email', (value) => { if (this.errors.email) this.validateField('email', value) });
                this.$watch('form.message', (value) => { if (this.errors.message) this.validateField('message', value) });
            },

            validateField(field, value) {
                if (field === 'first_name' || field === 'last_name') {
                    if (value.length > 0 && value.length < 2) {
                        this.errors[field] = ['{{ __("landing.contact_error_name_min") ?? "Must be at least 2 characters." }}'];
                    } else {
                        delete this.errors[field];
                    }
                }
                if (field === 'email') {
                    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (value.length > 0 && !regex.test(value)) {
                        this.errors[field] = ['{{ __("landing.contact_error_email_invalid") ?? "Please enter a valid email address." }}'];
                    } else {
                        delete this.errors[field];
                    }
                }
                if (field === 'message') {
                    if (value.length > 0 && value.length < 10) {
                        this.errors[field] = ['{{ __("landing.contact_error_msg_min") ?? "Message must be at least 10 characters." }}'];
                    } else if (value.length > this.maxLength) {
                         this.errors[field] = ['{{ __("landing.contact_error_msg_max") ?? "Message is too long." }}'];
                    } else {
                        delete this.errors[field];
                    }
                }
            },

            submit() {
                this.loading = true;
                this.errors = {};
                
                fetch('{{ route("contact.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        if (response.status === 422) {
                            this.errors = data.errors;
                        } else if (response.status === 429) {
                            showToast('{{ __("landing.error") ?? "Error" }}', '{{ __("landing.contact_rate_limit") ?? "Too many requests. Please try again later." }}');
                        } else {
                            showToast('{{ __("landing.error") ?? "Error" }}', data.message || '{{ __("landing.contact_error_general") ?? "Something went wrong." }}');
                        }
                    } else {
                        showToast('{{ __("landing.success") ?? "Success" }}', data.message, 'hide');
                        this.successMessage = data.message;
                        this.submitted = true;
                        this.form = { first_name: '', last_name: '', email: '', message: '' };
                    }
                })
                .catch(error => {
                    showToast('{{ __("landing.error") ?? "Error" }}', '{{ __("landing.contact_network_error") ?? "Network error. Please try again." }}');
                })
                .finally(() => {
                    this.loading = false;
                });
            }
        }));
    });
</script>
@endpush
@endsection
