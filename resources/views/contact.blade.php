@extends('layouts.app')

@section('title', __('landing.nav_contact') ?? 'Contact Us')

@section('content')
<div class="pt-32 pb-24 relative overflow-hidden bg-white dark:bg-[#09090b]">
    <!-- Background Glow Elements -->
    <div class="absolute top-[20%] end-[-10%] w-[500px] h-[500px] rounded-full bg-primary/10 blur-[120px] opacity-60 dark:opacity-20 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-[900] tracking-tight text-slate-900 dark:text-white mb-6 leading-tight">
                Let's <span class="glow-text">Talk</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">
                Whether you have a question about features, pricing, or anything else, our team is ready to answer all your questions.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8">
            <!-- Contact Info -->
            <div class="lg:col-span-5 reveal">
                <div class="bg-white/80 dark:bg-[#0a0a0c]/80 backdrop-blur-2xl rounded-3xl border border-slate-200/50 dark:border-zinc-800/50 shadow-2xl shadow-slate-200/20 dark:shadow-black/40 p-8 sm:p-12 h-full relative overflow-hidden flex flex-col justify-center">
                    <!-- Subtle ambient glow inside the card -->
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>

                    <h2 class="text-3xl font-[900] tracking-tight text-slate-900 dark:text-white mb-4 relative z-10">{{ __('landing.nav_contact') ?? 'Contact Information' }}</h2>
                    <p class="text-slate-500 dark:text-zinc-400 leading-relaxed mb-12 relative z-10">Fill out the form and our dedicated team will get back to you within 24 hours.</p>

                    <div class="space-y-10 relative z-10">
                        <!-- Email -->
                        <div class="group flex items-start gap-5">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-orange-400 p-[2px] shadow-lg shadow-primary/20 group-hover:shadow-primary/40 transition-shadow duration-500 shrink-0">
                                <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                            <div class="pt-1">
                                <h3 class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-1.5">Email Us</h3>
                                <a href="mailto:hello@alidebo.com" class="text-lg font-bold text-slate-900 dark:text-white hover:text-primary transition-colors">hello@alidebo.com</a>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Our friendly team is here to help.</p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="group flex items-start gap-5">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-400 p-[2px] shadow-lg shadow-emerald-500/20 group-hover:shadow-emerald-500/40 transition-shadow duration-500 shrink-0">
                                <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-500 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                            </div>
                            <div class="pt-1">
                                <h3 class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-1.5">Visit Us</h3>
                                <p class="text-lg font-bold text-slate-900 dark:text-white">Riyadh, Saudi Arabia</p>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Come say hello at our office HQ.</p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="group flex items-start gap-5">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-400 p-[2px] shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/40 transition-shadow duration-500 shrink-0">
                                <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-500 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                            </div>
                            <div class="pt-1">
                                <h3 class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-1.5">Call Us</h3>
                                <a href="tel:+966500000000" class="text-lg font-bold text-slate-900 dark:text-white hover:text-blue-500 transition-colors">+966 50 000 0000</a>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Sun-Thu from 8am to 5pm.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-7 reveal delay-100">
                <div class="bg-white/80 dark:bg-[#0a0a0c]/80 backdrop-blur-2xl rounded-3xl border border-slate-200/50 dark:border-zinc-800/50 shadow-2xl shadow-slate-200/20 dark:shadow-black/40 p-8 sm:p-12 h-full flex flex-col justify-center">
                    <form onsubmit="event.preventDefault(); showToast('Message Sent', 'Thank you for reaching out. We will get back to you shortly.'); this.reset();" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-zinc-300">First Name</label>
                                <input type="text" required class="w-full bg-slate-50/50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl py-3 px-4 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder:text-slate-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-zinc-300">Last Name</label>
                                <input type="text" required class="w-full bg-slate-50/50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl py-3 px-4 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder:text-slate-400">
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-zinc-300">Email Address</label>
                            <input type="email" required class="w-full bg-slate-50/50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl py-3 px-4 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder:text-slate-400">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-zinc-300">Message</label>
                            <textarea required rows="5" class="w-full bg-slate-50/50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl py-3 px-4 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder:text-slate-400"></textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-primary text-white rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
