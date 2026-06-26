@extends('layouts.app')

@section('title', $business->name)

@push('styles')
<style>
    /* ── Profile Page Design System ── */
    .profile-page { padding-top: 0; }

    /* Ambient glow */
    .profile-glow {
        position: absolute;
        border-radius: 9999px;
        filter: blur(120px);
        pointer-events: none;
        opacity: 0.15;
    }
    .dark .profile-glow { opacity: 0.08; }

    /* Cover shimmer for missing cover */
    .cover-pattern {
        background-image:
            radial-gradient(circle at 20% 50%, rgba(244, 80, 24, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 80% 50%, rgba(244, 80, 24, 0.05) 0%, transparent 50%),
            linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
    }
    .dark .cover-pattern {
        background-image:
            radial-gradient(circle at 20% 50%, rgba(244, 80, 24, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 50%, rgba(244, 80, 24, 0.06) 0%, transparent 50%),
            linear-gradient(135deg, #0a0a0c 0%, #0e0e11 50%, #18181b 100%);
    }

    /* Logo ring animation */
    .logo-ring {
        box-shadow: 0 0 0 4px white, 0 8px 32px -4px rgba(0,0,0,0.12);
    }
    .dark .logo-ring {
        box-shadow: 0 0 0 4px #0e0e11, 0 8px 32px -4px rgba(0,0,0,0.5);
    }

    /* Contact pill hover */
    .contact-pill {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .contact-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px -8px rgba(0,0,0,0.12);
    }
    .dark .contact-pill:hover {
        box-shadow: 0 8px 24px -8px rgba(0,0,0,0.4);
    }

    /* Gallery image loading */
    .gallery-img {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dark .gallery-img {
        background: linear-gradient(135deg, #18181b, #27272a);
    }

    /* Lightbox */
    .lightbox-overlay {
        backdrop-filter: blur(8px) saturate(0.8);
        -webkit-backdrop-filter: blur(8px) saturate(0.8);
    }
    .lightbox-nav-btn {
        transition: all 0.2s ease;
    }
    .lightbox-nav-btn:hover {
        background: rgba(255,255,255,0.2);
        transform: scale(1.1);
    }

    /* Fade in animation */
    .profile-fade {
        animation: profileFadeIn 0.6s ease-out forwards;
    }
    .profile-fade-delay-1 { animation-delay: 0.1s; opacity: 0; }
    .profile-fade-delay-2 { animation-delay: 0.2s; opacity: 0; }
    .profile-fade-delay-3 { animation-delay: 0.3s; opacity: 0; }

    @keyframes profileFadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Section divider */
    .section-line {
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0 20%, #e2e8f0 80%, transparent);
    }
    .dark .section-line {
        background: linear-gradient(to right, transparent, rgba(63,63,70,0.4) 20%, rgba(63,63,70,0.4) 80%, transparent);
    }

    /* Share tooltip */
    .share-tooltip {
        animation: tooltipFade 2s ease forwards;
    }
    @keyframes tooltipFade {
        0%, 80% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-4px); }
    }
</style>
@endpush

@section('content')
<div class="profile-page min-h-screen bg-white dark:bg-[#0a0a0c] relative overflow-hidden" x-data="profilePage()">

    {{-- Ambient Glow --}}
    <div class="profile-glow w-[500px] h-[500px] bg-primary/30 -top-60 -start-40 absolute z-0"></div>
    <div class="profile-glow w-[400px] h-[400px] bg-primary/20 top-[30%] -end-40 absolute z-0"></div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- STATUS BANNERS --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($business->status === 'pending')
    <div class="relative bg-amber-50/80 dark:bg-amber-950/20 border-b border-amber-200/60 dark:border-amber-900/30 z-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-center gap-3">
            <div class="shrink-0 w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">{{ __('dashboard.index.review_overlay.title') }}</p>
                <p class="text-xs text-amber-600/70 dark:text-amber-400/60">{{ __('dashboard.index.review_overlay.message') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if($business->status === 'rejected')
    <div class="relative bg-red-50/80 dark:bg-red-950/15 border-b border-red-200/60 dark:border-red-900/20 z-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-start gap-3">
            <div class="shrink-0 w-7 h-7 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-red-800 dark:text-red-300">{{ __('dashboard.index.rejected_overlay.title') }}</p>
                @if($business->rejection_reason)
                <p class="text-xs text-red-600/70 dark:text-red-400/60 mt-0.5">{{ $business->rejection_reason }}</p>
                @endif
                <a href="{{ route('business.edit') }}" class="inline-flex items-center gap-1 mt-1.5 text-xs font-semibold text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">
                    {{ __('dashboard.index.rejected_overlay.cta') }}
                    <svg class="w-3 h-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- HERO: COVER + IDENTITY CARD --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div id="discover" class="max-w-4xl mx-auto px-4 sm:px-6 {{ request()->routeIs('business.view') ? 'pt-8 sm:pt-12' : 'pt-24 sm:pt-28' }} relative z-10 profile-fade scroll-mt-24">
        <div class="relative w-full rounded-[2rem] shadow-[0_24px_60px_-15px_rgba(0,0,0,0.08)] dark:shadow-[0_24px_60px_-15px_rgba(0,0,0,0.45)] overflow-hidden bg-white dark:bg-[#0e0e11] border border-slate-100 dark:border-zinc-800/40">
            
            {{-- Grid background pattern overlay --}}
            <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute inset-0 opacity-[0.25] dark:opacity-[0.12]"
                    style="background-image: linear-gradient(to bottom, rgba(244,80,24,0.02) 0%, transparent 40%), linear-gradient(90deg, rgba(244,80,24,0.02) 1px, transparent 1px), linear-gradient(rgba(244,80,24,0.02) 1px, transparent 1px); background-size: 100% 100%, 48px 48px, 48px 48px;">
                </div>
            </div>

            {{-- Cover --}}
            <div class="relative h-48 sm:h-56 md:h-64 w-full overflow-hidden z-10">
                @if($business->cover)
                    <img src="{{ str_contains($business->cover, 'categories') ? asset($business->cover) : $business->cover_url }}"
                         alt="{{ $business->name }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/10 to-transparent dark:from-[#0e0e11] dark:via-[#0e0e11]/10 dark:to-transparent"></div>
                @else
                    <div class="w-full h-full cover-pattern relative">
                        {{-- Subtle brand element when no cover --}}
                        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] dark:opacity-[0.02]">
                            <span class="text-[160px] font-black text-primary select-none">{{ mb_substr($business->name, 0, 1) }}</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent dark:from-[#0e0e11] dark:via-transparent dark:to-transparent"></div>
                    </div>
                @endif
            </div>

            {{-- Identity Details --}}
            <div class="px-6 pb-6 sm:px-8 sm:pb-8 relative z-20 -mt-16 sm:-mt-20">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-5">

                    {{-- Logo --}}
                    <div class="shrink-0">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-[1.25rem] logo-ring overflow-hidden bg-white dark:bg-[#0e0e11]">
                            @if($business->logo)
                                <img src="{{ $business->logo_url }}" alt="{{ $business->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 to-primary/10 dark:from-primary/10 dark:to-primary/20">
                                    <span class="text-3xl font-black text-primary/70">{{ mb_substr($business->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Name + Meta --}}
                    <div class="flex-1 min-w-0 pb-1">
                        <div class="flex items-start gap-3">
                            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                                {{ $business->name }}
                            </h1>
                            @if($business->status === 'approved')
                            <div class="shrink-0 mt-1.5" title="{{ __('directory.profile_verified') }}">
                                <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 mt-2">
                            @if($business->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/5 dark:bg-primary/10 text-primary border border-primary/10 dark:border-primary/20">
                                {{ $business->category->name }}
                            </span>
                            @endif

                            @if($business->city)
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 dark:text-zinc-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $business->city->name }}@if($business->city->country), {{ $business->city->country->name }}@endif
                            </span>
                            @endif

                            <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400 dark:text-zinc-500">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ __('directory.profile_member_since') }} {{ $business->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Share Button --}}
                    <div class="shrink-0 hidden sm:block pb-1 relative">
                        <button @click="shareProfile()" class="contact-pill inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-zinc-400 bg-slate-50 dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 hover:border-primary/30 hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            {{ __('directory.profile_share') }}
                        </button>
                        <div x-show="showShareTooltip" x-cloak class="share-tooltip absolute -top-8 start-1/2 -translate-x-1/2 px-3 py-1 rounded-lg bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-semibold whitespace-nowrap">
                            ✓ Link copied
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- QUICK CONTACT BAR --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @php
        $contacts = $business->contact_methods ?? [];
        $hasContacts = !empty($contacts['whatsapp']) || !empty($contacts['phone']) || !empty($contacts['email']) || !empty($contacts['website']) || !empty($contacts['instagram']) || !empty($contacts['facebook']) || !empty($contacts['twitter']) || !empty($contacts['tiktok']) || !empty($contacts['linkedin']);
    @endphp

    @if($hasContacts)
    <div class="max-w-4xl mx-auto px-4 sm:px-6 mt-8 profile-fade profile-fade-delay-1">
        <div class="flex flex-wrap items-center gap-2">

            @if(!empty($contacts['whatsapp']))
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contacts['whatsapp']) }}" target="_blank" rel="noopener"
               class="contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-950/40">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.012 2c-5.508 0-9.987 4.479-9.987 9.988 0 1.757.455 3.47 1.316 4.972l-1.341 4.922 5.035-1.321c1.442.786 3.064 1.2 4.733 1.2 5.507 0 9.988-4.479 9.988-9.988 0-5.508-4.481-9.988-9.988-9.988zm5.952 14.129c-.255.719-1.488 1.309-2.039 1.391-.497.073-.789.243-2.993-.655-2.203-.898-3.626-3.141-3.737-3.289-.112-.149-.912-1.21-.912-2.308 0-1.097.575-1.637.779-1.859.204-.223.446-.279.595-.279.149 0 .297.001.427.007.137.007.319-.052.499.383.181.437.618 1.503.671 1.614.053.111.088.241.014.39-.074.148-.111.241-.223.371-.111.13-.231.291-.329.39-.111.111-.227.233-.098.455.129.223.576.953 1.238 1.541.852.759 1.569 1.01 1.792 1.121.223.111.353.093.484-.056.13-.149.559-.652.707-.869.149-.223.298-.186.502-.112.204.073 1.298.614 1.52.726.223.111.371.167.426.26.056.093.056.541-.199 1.26z"/></svg>
                {{ __('directory.profile_whatsapp') }}
            </a>
            @endif

            @if(!empty($contacts['phone']))
            <a href="tel:{{ $contacts['phone'] }}"
               class="contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-950/40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                {{ __('directory.profile_call') }}
            </a>
            @endif

            @if(!empty($contacts['email']))
            <a href="mailto:{{ $contacts['email'] }}"
               class="contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-50 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200/80 dark:border-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                {{ __('directory.profile_email') }}
            </a>
            @endif

            @if(!empty($contacts['website']))
            <a href="{{ $contacts['website'] }}" target="_blank" rel="noopener"
               class="contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-50 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200/80 dark:border-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                {{ __('directory.profile_website') }}
            </a>
            @endif

            @if(!empty($contacts['instagram']))
            <a href="{{ $contacts['instagram'] }}" target="_blank" rel="noopener"
               class="contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-pink-50 dark:bg-pink-950/20 text-pink-700 dark:text-pink-400 border border-pink-100 dark:border-pink-900/30 hover:bg-pink-100 dark:hover:bg-pink-950/40">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                {{ __('directory.profile_instagram') }}
            </a>
            @endif

            @if(!empty($contacts['facebook']))
            <a href="{{ $contacts['facebook'] }}" target="_blank" rel="noopener"
               class="contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-950/40">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                {{ __('directory.profile_facebook') }}
            </a>
            @endif

            @if(!empty($contacts['twitter']))
            <a href="{{ $contacts['twitter'] }}" target="_blank" rel="noopener"
               class="contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-50 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200/80 dark:border-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-800">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                {{ __('directory.profile_twitter') }}
            </a>
            @endif

            @if(!empty($contacts['tiktok']))
            <a href="{{ $contacts['tiktok'] }}" target="_blank" rel="noopener"
               class="contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-50 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200/80 dark:border-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-800">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                {{ __('directory.profile_tiktok') }}
            </a>
            @endif

            @if(!empty($contacts['linkedin']))
            <a href="{{ $contacts['linkedin'] }}" target="_blank" rel="noopener"
               class="contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-950/40">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                {{ __('directory.profile_linkedin') }}
            </a>
            @endif

            {{-- Mobile share --}}
            <button @click="shareProfile()" class="sm:hidden contact-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-50 dark:bg-zinc-900 text-slate-600 dark:text-zinc-400 border border-slate-200/80 dark:border-zinc-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                {{ __('directory.profile_share') }}
            </button>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- ABOUT + ADDRESS --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($business->description || $business->address)
    <div id="about-section" class="max-w-4xl mx-auto px-4 sm:px-6 mt-10 profile-fade profile-fade-delay-2 scroll-mt-24">
        <div class="section-line mb-10"></div>

        @if($business->description)
        <div class="mb-8">
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500 mb-4">
                {{ __('directory.profile_about') }}
            </h2>
            <p class="text-base sm:text-lg text-slate-600 dark:text-zinc-300 leading-relaxed font-medium whitespace-pre-line">{{ $business->description }}</p>
        </div>
        @endif

        @if($business->address)
        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50/80 dark:bg-zinc-900/50 border border-slate-100 dark:border-zinc-800/60">
            <div class="shrink-0 w-8 h-8 rounded-lg bg-primary/5 dark:bg-primary/10 flex items-center justify-center text-primary mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-1">{{ __('directory.profile_address') }}</p>
                <p class="text-sm font-medium text-slate-700 dark:text-zinc-300">{{ $business->address }}</p>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- GALLERY --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @php
        $mediaCount = $business->media->count();
        $mediaItems = $business->media;
    @endphp

    @if($mediaCount > 0)
    <div id="gallery-section" class="max-w-4xl mx-auto px-4 sm:px-6 mt-10 pb-16 profile-fade profile-fade-delay-3 scroll-mt-24">
        <div class="section-line mb-10"></div>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500 mb-1">
                    {{ __('directory.profile_gallery') }}
                </h2>
                <p class="text-sm font-medium text-slate-400 dark:text-zinc-500">
                    {{ $mediaCount }} {{ $mediaCount === 1 ? __('directory.profile_photo') : __('directory.profile_photos') }}
                </p>
            </div>
        </div>

        <div class="grid gap-2 sm:gap-3">
            @if($mediaCount === 1)
                {{-- Single: full width --}}
                <div class="group relative aspect-[16/9] rounded-2xl overflow-hidden cursor-pointer"
                     @click="openLightbox(0)">
                    <img src="{{ $mediaItems[0]->file_url }}" alt="{{ $mediaItems[0]->caption ?? $business->name }}"
                         class="gallery-img w-full h-full object-cover group-hover:scale-105" loading="lazy">
                    @if($mediaItems[0]->caption)
                    <div class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <p class="text-white text-sm font-medium">{{ $mediaItems[0]->caption }}</p>
                    </div>
                    @endif
                </div>

            @elseif($mediaCount === 2)
                {{-- Two: side by side --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                    @foreach($mediaItems as $idx => $item)
                    <div class="group relative aspect-[4/3] rounded-2xl overflow-hidden cursor-pointer"
                         @click="openLightbox({{ $idx }})">
                        <img src="{{ $item->file_url }}" alt="{{ $item->caption ?? $business->name }}"
                             class="gallery-img w-full h-full object-cover group-hover:scale-105" loading="lazy">
                        @if($item->caption)
                        <div class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <p class="text-white text-sm font-medium">{{ $item->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

            @elseif($mediaCount === 3)
                {{-- Three: featured + two stacked --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                    <div class="group relative aspect-[4/3] sm:aspect-auto sm:row-span-2 rounded-2xl overflow-hidden cursor-pointer"
                         @click="openLightbox(0)">
                        <img src="{{ $mediaItems[0]->file_url }}" alt="{{ $mediaItems[0]->caption ?? $business->name }}"
                             class="gallery-img w-full h-full object-cover group-hover:scale-105" loading="lazy">
                        @if($mediaItems[0]->caption)
                        <div class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <p class="text-white text-sm font-medium">{{ $mediaItems[0]->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @foreach($mediaItems->slice(1, 2) as $idx => $item)
                    <div class="group relative aspect-[4/3] rounded-2xl overflow-hidden cursor-pointer"
                         @click="openLightbox({{ $loop->index + 1 }})">
                        <img src="{{ $item->file_url }}" alt="{{ $item->caption ?? $business->name }}"
                             class="gallery-img w-full h-full object-cover group-hover:scale-105" loading="lazy">
                        @if($item->caption)
                        <div class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <p class="text-white text-sm font-medium">{{ $item->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

            @elseif($mediaCount === 4)
                {{-- Four: 2x2 grid --}}
                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                    @foreach($mediaItems as $idx => $item)
                    <div class="group relative aspect-[4/3] rounded-2xl overflow-hidden cursor-pointer"
                         @click="openLightbox({{ $idx }})">
                        <img src="{{ $item->file_url }}" alt="{{ $item->caption ?? $business->name }}"
                             class="gallery-img w-full h-full object-cover group-hover:scale-105" loading="lazy">
                        @if($item->caption)
                        <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <p class="text-white text-sm font-medium">{{ $item->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

            @else
                {{-- 5+: masonry-style with "more" overlay --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3">
                    @foreach($mediaItems->take(6) as $idx => $item)
                    <div class="group relative {{ $idx === 0 ? 'col-span-2 sm:col-span-2 sm:row-span-2 aspect-[16/9] sm:aspect-auto' : 'aspect-square' }} rounded-2xl overflow-hidden cursor-pointer"
                         @click="openLightbox({{ $idx }})">
                        <img src="{{ $item->file_url }}" alt="{{ $item->caption ?? $business->name }}"
                             class="gallery-img w-full h-full object-cover group-hover:scale-105" loading="lazy">

                        @if($idx === 5 && $mediaCount > 6)
                        <div class="absolute inset-0 bg-slate-900/60 dark:bg-black/60 backdrop-blur-[2px] flex flex-col items-center justify-center text-white">
                            <span class="text-2xl font-black">+{{ $mediaCount - 6 }}</span>
                            <span class="text-xs font-semibold opacity-60 mt-0.5">{{ __('directory.profile_more') }}</span>
                        </div>
                        @elseif($item->caption && !($idx === 5 && $mediaCount > 6))
                        <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <p class="text-white text-sm font-medium">{{ $item->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- LIGHTBOX WITH NAVIGATION --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($mediaCount > 0)
    <div x-show="lightboxOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="closeLightbox()"
         @keydown.escape.window="closeLightbox()"
         @keydown.right.window="nextImage()"
         @keydown.left.window="prevImage()"
         class="lightbox-overlay fixed inset-0 z-[99999] bg-slate-950/90 dark:bg-black/95 flex items-center justify-center p-4">

        {{-- Close --}}
        <button @click="closeLightbox()" class="lightbox-nav-btn absolute top-4 end-4 sm:top-6 sm:end-6 z-10 w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/70 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Counter --}}
        <div class="absolute top-4 start-4 sm:top-6 sm:start-6 z-10 px-3 py-1.5 rounded-full bg-white/10 text-white/70 text-xs font-semibold">
            <span x-text="currentIndex + 1"></span> {{ __('directory.profile_of') }} {{ $mediaCount }}
        </div>

        {{-- Previous --}}
        <button x-show="mediaItems.length > 1" @click.stop="prevImage()"
                class="lightbox-nav-btn absolute start-2 sm:start-4 z-10 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/10 flex items-center justify-center text-white/70 hover:text-white">
            <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Image --}}
        <div class="max-w-5xl w-full max-h-[85vh] flex flex-col items-center">
            <img :src="currentImage.url" :alt="currentImage.caption || ''"
                 class="max-w-full max-h-[75vh] object-contain rounded-xl"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
            <p x-show="currentImage.caption" x-text="currentImage.caption"
               class="mt-4 text-white/70 text-sm font-medium text-center max-w-lg"></p>
        </div>

        {{-- Next --}}
        <button x-show="mediaItems.length > 1" @click.stop="nextImage()"
                class="lightbox-nav-btn absolute end-2 sm:end-4 z-10 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/10 flex items-center justify-center text-white/70 hover:text-white">
            <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- FOOTER --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($mediaCount === 0 && !$business->description && !$business->address)
    <div class="h-32"></div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function profilePage() {
        return {
            lightboxOpen: false,
            currentIndex: 0,
            showShareTooltip: false,
            mediaItems: @json($business->media->map(fn($m) => ['url' => $m->file_url, 'caption' => $m->caption])->values()),

            get currentImage() {
                return this.mediaItems[this.currentIndex] || { url: '', caption: '' };
            },

            openLightbox(index) {
                this.currentIndex = index;
                this.lightboxOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeLightbox() {
                this.lightboxOpen = false;
                document.body.style.overflow = '';
            },

            nextImage() {
                if (!this.lightboxOpen) return;
                this.currentIndex = (this.currentIndex + 1) % this.mediaItems.length;
            },

            prevImage() {
                if (!this.lightboxOpen) return;
                this.currentIndex = (this.currentIndex - 1 + this.mediaItems.length) % this.mediaItems.length;
            },

            shareProfile() {
                const url = window.location.href;
                if (navigator.share) {
                    navigator.share({ title: document.title, url: url });
                } else if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(() => {
                        this.showShareTooltip = true;
                        setTimeout(() => this.showShareTooltip = false, 2000);
                    });
                }
            }
        };
    }
</script>
@endpush