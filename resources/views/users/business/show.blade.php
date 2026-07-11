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

        /* ── Lenis ── */
    html.lenis { height: auto; }
    .lenis.lenis-smooth { scroll-behavior: auto; }

    /* ─────────────────────────────────────────────────
       GRID  (1–6 images)
    ───────────────────────────────────────────────── */
    .gallery-grid { display: grid; gap: 2px; }

    .gallery-grid.count-1 { grid-template-columns: 1fr; }
    .gallery-grid.count-1 .gallery-item { aspect-ratio: 16/7; }
    @media (max-width: 640px) { .gallery-grid.count-1 .gallery-item { aspect-ratio: 4/3; } }

    .gallery-grid.count-2 { grid-template-columns: repeat(2, 1fr); }
    .gallery-grid.count-2 .gallery-item { aspect-ratio: 3/4; }
    @media (max-width: 480px) {
      .gallery-grid.count-2 { grid-template-columns: 1fr; }
      .gallery-grid.count-2 .gallery-item { aspect-ratio: 4/3; }
    }

    .gallery-grid.count-3 { grid-template-columns: repeat(3, 1fr); }
    .gallery-grid.count-3 .gallery-item { aspect-ratio: 3/4; }
    @media (max-width: 640px) {
      .gallery-grid.count-3 { grid-template-columns: repeat(2, 1fr); }
      .gallery-grid.count-3 .gallery-item:last-child { grid-column: 1 / -1; aspect-ratio: 16/7; }
    }
    @media (max-width: 380px) {
      .gallery-grid.count-3 { grid-template-columns: 1fr; }
      .gallery-grid.count-3 .gallery-item { aspect-ratio: 4/3; }
      .gallery-grid.count-3 .gallery-item:last-child { grid-column: auto; aspect-ratio: 4/3; }
    }

    .gallery-grid.count-4 { grid-template-columns: repeat(2, 1fr); }
    .gallery-grid.count-4 .gallery-item { aspect-ratio: 4/3; }
    @media (max-width: 480px) { .gallery-grid.count-4 { grid-template-columns: 1fr; } }

    .gallery-grid.count-5 {
      grid-template-columns: repeat(6, 1fr);
      grid-template-rows: auto auto;
    }
    .gallery-grid.count-5 .gallery-item:nth-child(1),
    .gallery-grid.count-5 .gallery-item:nth-child(2),
    .gallery-grid.count-5 .gallery-item:nth-child(3) { grid-column: span 2; aspect-ratio: 4/3; }
    .gallery-grid.count-5 .gallery-item:nth-child(4),
    .gallery-grid.count-5 .gallery-item:nth-child(5) { grid-column: span 3; aspect-ratio: 16/9; }
    @media (max-width: 640px) {
      .gallery-grid.count-5 { grid-template-columns: repeat(2, 1fr); }
      .gallery-grid.count-5 .gallery-item { grid-column: auto !important; aspect-ratio: 4/3 !important; }
    }
    @media (max-width: 380px) { .gallery-grid.count-5 { grid-template-columns: 1fr; } }

    .gallery-grid.count-6 { grid-template-columns: repeat(3, 1fr); }
    .gallery-grid.count-6 .gallery-item { aspect-ratio: 4/3; }
    @media (max-width: 640px) { .gallery-grid.count-6 { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 380px) { .gallery-grid.count-6 { grid-template-columns: 1fr; } }

    /* ─────────────────────────────────────────────────
       GALLERY ITEM
    ───────────────────────────────────────────────── */
    .gallery-item {
      position: relative;
      overflow: hidden;
      cursor: pointer;
      background: #111;
    }
    .gallery-item img {
      width: 100%; height: 100%;
      object-fit: cover; display: block;
      transform-origin: center;
      will-change: transform;
    }
    .gallery-item-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.1) 45%, transparent 100%);
      opacity: 0;
      transition: opacity 0.4s ease;
      display: flex; align-items: flex-end;
      padding: clamp(0.75rem, 2vw, 1.5rem);
    }
    @media (hover: hover) {
      .gallery-item:hover .gallery-item-overlay { opacity: 1; }
      .gallery-item:hover .gallery-item-label { transform: translateY(0); }
    }
    @media (hover: none) {
      .gallery-item-overlay { opacity: 1; background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 40%); }
      .gallery-item-label { transform: translateY(0) !important; }
    }
    .gallery-item-label {
      font-size: clamp(0.6rem, 1.2vw, 0.72rem);
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.75);
      transform: translateY(8px);
      transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    }

    /* ─────────────────────────────────────────────────
       LIGHTBOX
    ───────────────────────────────────────────────── */
    #lightbox {
      position: fixed; inset: 0; z-index: 100000;
      display: flex; align-items: center; justify-content: center;
      pointer-events: none; opacity: 0;
    }
    #lightbox.active { pointer-events: all; }
    #lightbox-bg-img {
      position: absolute; inset: 0;
      width: 100%; height: 100%;
      object-fit: cover;
      filter: blur(40px) brightness(0.35);
      transform: scale(1.1);
      z-index: 0;
      transition: opacity 0.3s ease;
    }
    #lightbox-backdrop {
      position: absolute; inset: 0;
      background: rgba(0,0,0,0.65);
      z-index: 1;
    }
    #lightbox-img-wrap {
      position: relative; z-index: 1;
      width: 90vw; max-width: 1100px;
      max-height: 80vh;
      display: flex; align-items: center; justify-content: center;
    }
    #lightbox-img {
      max-width: 100%; max-height: 80vh;
      object-fit: contain; display: block;
      border: 1px solid rgba(255,255,255,0.05);
      user-select: none; -webkit-user-drag: none;
    }
    #lightbox-close {
      position: absolute; top: clamp(0.75rem, 3vw, 1.5rem); right: clamp(0.75rem, 3vw, 1.5rem); z-index: 2;
      width: 44px; height: 44px; border-radius: 50%;
      background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12);
      color: #fff; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem; transition: background 0.2s;
      touch-action: manipulation;
    }
    #lightbox-close:hover { background: rgba(255,255,255,0.14); }
    #lightbox-counter {
      position: absolute; bottom: clamp(0.75rem, 3vw, 1.5rem); left: 50%; transform: translateX(-50%); z-index: 2;
      font-size: 0.65rem; letter-spacing: 0.22em; color: rgba(255,255,255,0.3); text-transform: uppercase;
      white-space: nowrap;
    }
    #lightbox-prev, #lightbox-next {
      position: absolute; top: 50%; transform: translateY(-50%); z-index: 2;
      width: 48px; height: 48px; border-radius: 50%;
      background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12);
      color: #fff; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem; transition: background 0.2s;
      touch-action: manipulation;
    }
    #lightbox-prev { left: clamp(0.5rem, 2vw, 1.25rem); }
    #lightbox-next { right: clamp(0.5rem, 2vw, 1.25rem); }
    #lightbox-prev:hover, #lightbox-next:hover { background: rgba(255,255,255,0.14); }
    #lightbox-prev:disabled, #lightbox-next:disabled { opacity: 0.2; pointer-events: none; }

    @media (max-width: 480px) {
      #lightbox-prev, #lightbox-next { top: auto; bottom: 3rem; transform: none; }
      #lightbox-prev { left: 25%; transform: translateX(-50%); }
      #lightbox-next { right: 25%; transform: translateX(50%); }
    }

    /* ─────────────────────────────────────────────────
       CAROUSEL  (7-10 images)
    ───────────────────────────────────────────────── */
    .carousel-wrap { position: relative; overflow: hidden; }
    .carousel-track { display: flex; gap: 2px; will-change: transform; }
    .carousel-slide {
      flex: 0 0 var(--slide-w, 33.333%);
      position: relative; aspect-ratio: 4/3;
      overflow: hidden; cursor: pointer; background: #111;
    }
    .carousel-slide img { width: 100%; height: 100%; object-fit: cover; display: block; will-change: transform; }
    .carousel-slide .gallery-item-overlay { opacity: 0; }
    @media (hover: hover) {
      .carousel-slide:hover .gallery-item-overlay { opacity: 1; }
      .carousel-slide:hover .gallery-item-label { transform: translateY(0); }
    }
    @media (hover: none) {
      .carousel-slide .gallery-item-overlay { opacity: 1; background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 40%); }
      .carousel-slide .gallery-item-label { transform: translateY(0) !important; }
    }
    .carousel-nav {
      display: flex; align-items: center; justify-content: space-between;
      padding: 1rem 0 0;
    }
    .carousel-btn {
      width: 42px; height: 42px; border-radius: 50%;
      border: 1px solid rgba(100,100,100,0.2); background: transparent;
      color: rgba(100,100,100,0.65); cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem; transition: border-color 0.2s, color 0.2s, background 0.2s;
      touch-action: manipulation;
    }
    .dark .carousel-btn {
      border: 1px solid rgba(255,255,255,0.12);
      color: rgba(255,255,255,0.65);
    }
    .carousel-btn:hover { border-color: rgba(255,255,255,0.3); color: #fff; background: rgba(255,255,255,0.06); }
    .carousel-btn:disabled { opacity: 0.2; pointer-events: none; }
    .carousel-dots { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; justify-content: center; }
    .dot {
      width: 5px; height: 5px; border-radius: 50%;
      background: rgba(100,100,100,0.18);
      transition: background 0.3s, transform 0.3s; cursor: pointer;
    }
    .dark .dot { background: rgba(255,255,255,0.18); }
    .dot.active { background: rgba(100,100,100,0.72); transform: scale(1.45); }
    .dark .dot.active { background: rgba(255,255,255,0.72); }

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
    <div class="max-w-4xl mx-auto px-4 sm:px-6 mt-8 profile-fade profile-fade-delay-1" x-data="{ leadStatus: '', isSubmitting: false }">
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
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500 mb-1">
                    {{ __('directory.profile_gallery') }}
                </h2>
                <p class="text-sm font-medium text-slate-400 dark:text-zinc-500">
                    {{ $mediaCount }} {{ $mediaCount === 1 ? __('directory.profile_photo') : __('directory.profile_photos') }}
                </p>
            </div>
        </div>

        <div id="gallery-container">
            @if($mediaCount <= 6)
                <div class="gallery-grid count-{{ $mediaCount }}">
                    @foreach($mediaItems->take(6) as $idx => $item)
                    <div class="gallery-item" data-index="{{ $idx }}" tabindex="0" role="button" aria-label="{{ $item->caption ?? $business->name }}">
                        <img src="{{ $item->file_url }}" alt="{{ $item->caption ?? $business->name }}" loading="lazy" />
                        <div class="gallery-item-overlay">
                            <span class="gallery-item-label">{{ $item->caption ?? $business->name }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="carousel-wrap">
                    <div class="carousel-track" id="carousel-track">
                        @foreach($mediaItems as $idx => $item)
                        <div class="carousel-slide gallery-item" data-index="{{ $idx }}" tabindex="0" role="button" aria-label="{{ $item->caption ?? $business->name }}">
                            <img src="{{ $item->file_url }}" alt="{{ $item->caption ?? $business->name }}" loading="lazy"/>
                            <div class="gallery-item-overlay">
                                <span class="gallery-item-label">{{ $item->caption ?? $business->name }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <nav class="carousel-nav" aria-label="Carousel navigation">
                    <button class="carousel-btn" id="carousel-prev" disabled>&#8592;</button>
                    <div class="carousel-dots" id="carousel-dots">
                        {{-- Dots generated by JS --}}
                    </div>
                    <button class="carousel-btn" id="carousel-next">&#8594;</button>
                </nav>
            @endif
        </div>
    </div>

    <!-- ── Lightbox ── -->
    <div id="lightbox" role="dialog" aria-modal="true" aria-label="Image viewer">
      <img id="lightbox-bg-img" src="" alt="" />
      <div id="lightbox-backdrop"></div>
      <button id="lightbox-prev" aria-label="Previous">&#8592;</button>
      <div id="lightbox-img-wrap">
        <img id="lightbox-img" src="" alt="" />
      </div>
      <button id="lightbox-next" aria-label="Next">&#8594;</button>
      <button id="lightbox-close" aria-label="Close">&#x2715;</button>
      <div id="lightbox-counter"></div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- FOOTER --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($mediaCount === 0 && !$business->description && !$business->address)
    <div class="h-32"></div>
    @endif
    </div>


    {{-- ═══════════════════════════════════════════════ --}}
    {{-- CONTACT FORM --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 mt-16 profile-fade profile-fade-delay-3" id="contact-form-section" x-data="{ leadStatus: '', isSubmitting: false }">
        <div class="section-line mb-8"></div>
        <div class="bg-white dark:bg-[#0e0e11] shadow-xl rounded-3xl border border-slate-100 dark:border-white/5 p-6 sm:p-10">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('directory.get_quote') }}</h3>
            <p class="text-sm text-slate-500 dark:text-zinc-400 mb-8">{{ __('directory.lead_message') }}</p>

            <div x-show="leadStatus === 'success'" x-cloak class="mb-8 p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/30 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-semibold">{{ __('directory.lead_success') }}</span>
            </div>

            <form x-show="leadStatus !== 'success'" @submit.prevent="
                isSubmitting = true;
                const formElement = $event.target;
                fetch('{{ route('directory.business.contact', $business->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: formElement.name.value,
                        email: formElement.email.value,
                        phone: formElement.phone.value,
                        message: formElement.message.value
                    })
                }).then(res => res.json()).then(data => {
                    if(data.success) {
                        leadStatus = 'success';
                        setTimeout(() => { leadStatus = ''; formElement.reset(); }, 5000);
                    }
                }).finally(() => isSubmitting = false);
            " class="space-y-5">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-2">{{ __('directory.lead_name') }} *</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-2">{{ __('directory.lead_email') }}</label>
                        <input type="email" name="email" class="w-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-2">{{ __('directory.lead_phone') }}</label>
                        <input type="text" name="phone" dir="ltr" class="w-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-2">{{ __('directory.lead_message') }} *</label>
                    <textarea name="message" required rows="5" class="w-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none resize-none transition-all"></textarea>
                </div>
                <button type="submit" :disabled="isSubmitting" class="w-full sm:w-auto px-8 flex items-center justify-center gap-2 bg-primary text-white font-bold text-sm py-3.5 rounded-xl hover:bg-primary-dark transition-colors active:scale-[0.98] disabled:opacity-70">
                    <span x-show="!isSubmitting">{{ __('directory.lead_send') }}</span>
                    <span x-show="isSubmitting" x-cloak>
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </span>
                </button>
            </form>
        </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- REVIEWS SECTION --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div id="reviews-section" class="max-w-4xl mx-auto px-4 sm:px-6 mt-16 pb-20 profile-fade profile-fade-delay-3 scroll-mt-24">
        <div class="section-line mb-8"></div>
        <div class="mb-10 flex items-center justify-between">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ __('directory.profile_reviews') }}</h2>
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $business->averageRating() }}</span>
                <div class="flex text-amber-400">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <span class="text-slate-500 dark:text-zinc-400 font-medium">({{ $business->reviews()->where('status', 'approved')->count() }})</span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 mb-6 text-sm text-emerald-800 dark:text-emerald-300 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/30">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-6 text-sm text-red-800 dark:text-red-300 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30">
                {{ session('error') }}
            </div>
        @endif

        {{-- Write Review Form --}}
        <div class="bg-white dark:bg-[#0e0e11] shadow-sm rounded-3xl border border-slate-100 dark:border-white/5 p-6 sm:p-8 mb-10">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">{{ __('directory.write_review') }}</h3>
            @if(!$business->reviews()->where('ip_address', request()->ip())->exists())
                <form action="{{ route('directory.business.reviews.store', $business->slug) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-2">{{ __('directory.review_name') }} *</label>
                            <input type="text" name="reviewer_name" required class="w-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-2">{{ __('directory.review_email') }}</label>
                            <input type="email" name="reviewer_email" class="w-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all">
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-2">{{ __('directory.rate_experience') }} *</label>
                        <select name="rating" required class="w-full sm:w-auto bg-slate-50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all">
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Good</option>
                            <option value="3">3 - Average</option>
                            <option value="2">2 - Poor</option>
                            <option value="1">1 - Terrible</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-2">{{ __('directory.review_comment') }} *</label>
                        <textarea name="comment" required rows="3" class="w-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none resize-none transition-all"></textarea>
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-8 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm py-3.5 rounded-xl hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors active:scale-[0.98]">
                        {{ __('directory.submit_review') }}
                    </button>
                </form>
            @else
                <p class="text-sm text-slate-600 dark:text-zinc-400 font-medium">{{ __('directory.already_reviewed') }}</p>
            @endif
        </div>

        {{-- Reviews List --}}
        <div class="space-y-6">
            @forelse($business->reviews()->where('status', 'approved')->latest()->get() as $review)
                <div class="bg-white dark:bg-zinc-900/50 rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-white/5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $review->reviewer_name }}</div>
                        <div class="text-xs font-medium text-slate-400 dark:text-zinc-500">{{ $review->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="flex text-amber-400 mb-4">
                        @for($i=1; $i<=5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-200 dark:text-zinc-700' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-zinc-300 leading-relaxed">{{ $review->comment }}</p>
                    
                    @if($review->reply)
                        <div class="mt-5 p-5 bg-slate-50 dark:bg-black/30 rounded-2xl border-s-2 border-primary">
                            <div class="font-bold text-xs uppercase tracking-wider text-primary mb-2">{{ __('directory.business_reply') }}</div>
                            <p class="text-sm text-slate-700 dark:text-zinc-400 leading-relaxed">{{ $review->reply }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 px-4 rounded-3xl bg-slate-50/50 dark:bg-zinc-900/30 border border-slate-100 dark:border-white/5 border-dashed">
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">{{ __('directory.no_reviews_yet') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    /* ══════════════════ LENIS ══════════════════ */
    const lenis = new Lenis({ lerp: 0.08, smoothWheel: true });
    window.lenis = lenis; // Expose for lightbox
    function raf(t) { lenis.raf(t); requestAnimationFrame(raf); }
    requestAnimationFrame(raf);

    /* ══════════════════ GSAP ══════════════════ */
    gsap.registerPlugin(ScrollTrigger);
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add(t => lenis.raf(t * 1000));
    gsap.ticker.lagSmoothing(0);

    /* ══════════════════ GALLERY DATA ══════════════════ */
    const IMAGES = @json($business->media->map(fn($m) => ['src' => $m->file_url, 'label' => $m->caption ?? $business->name])->values());
    if (IMAGES.length === 0) return;
    
    let lightboxIndex = 0;
    let isLightboxOpen = false;
    let carouselIndex = 0;
    let carouselSlidesVisible = 3;

    /* ══════════════════ HELPERS ══════════════════ */
    function slidesVisible() {
      const w = window.innerWidth;
      return w <= 480 ? 1 : w <= 768 ? 2 : 3;
    }

    function addHoverZoom(el) {
      if (window.matchMedia('(hover: none)').matches) return;
      const img = el.querySelector('img');
      el.addEventListener('mouseenter', () => gsap.to(img, { scale:1.07, duration:0.65, ease:'power2.out' }));
      el.addEventListener('mouseleave', () => gsap.to(img, { scale:1,    duration:0.55, ease:'power2.out' }));
    }

    /* ══════════════════ INIT GALLERY ══════════════════ */
    if (IMAGES.length <= 6) {
      const items = document.querySelectorAll('.gallery-grid .gallery-item');
      gsap.set(items, { opacity:0, y:36, scale:0.97 });
      items.forEach((item, i) => {
        ScrollTrigger.create({
          trigger: item, start:'top 92%', once:true,
          onEnter: () => gsap.to(item,{ opacity:1, y:0, scale:1, duration:0.8, delay:(i%3)*0.07, ease:'expo.out' })
        });
        addHoverZoom(item);
      });
    } else {
      initCarousel();
    }
    bindClicks();

    /* ── Carousel ── */
    function initCarousel() {
      const track   = document.getElementById('carousel-track');
      const prevBtn = document.getElementById('carousel-prev');
      const nextBtn = document.getElementById('carousel-next');
      const dotsWrap= document.getElementById('carousel-dots');
      if (!track) return;

      carouselSlidesVisible = slidesVisible();
      const slides = track.querySelectorAll('.carousel-slide');
      
      function buildDots() {
         const maxI = Math.max(0, IMAGES.length - carouselSlidesVisible);
         let dotsHTML = '';
         for(let i=0; i<=maxI; i++) {
             dotsHTML += `<div class="dot${i===0?' active':''}" data-dot="${i}"></div>`;
         }
         dotsWrap.innerHTML = dotsHTML;
      }
      buildDots();

      gsap.set(slides, { opacity:0, y:28, scale:0.97 });
      ScrollTrigger.create({
        trigger: track, start:'top 92%', once:true,
        onEnter: () => gsap.to(slides,{ opacity:1, y:0, scale:1, stagger:0.06, duration:0.75, ease:'expo.out' })
      });
      slides.forEach(s => addHoverZoom(s));

      function maxIdx() { return Math.max(0, IMAGES.length - carouselSlidesVisible); }

      function updateDots() {
        dotsWrap.querySelectorAll('.dot').forEach((d,i) => d.classList.toggle('active', i===carouselIndex));
      }
      function updateBtns() {
        prevBtn.disabled = carouselIndex === 0;
        nextBtn.disabled = carouselIndex >= maxIdx();
      }

      function slideWidth() {
        const s = track.querySelector('.carousel-slide');
        return s ? s.offsetWidth + 2 : 0;
      }

      function goTo(idx) {
        carouselIndex = Math.max(0, Math.min(idx, maxIdx()));
        gsap.to(track, { x: -carouselIndex * slideWidth(), duration:0.75, ease:'expo.inOut' });
        updateDots(); updateBtns();
      }

      prevBtn.addEventListener('click', () => goTo(carouselIndex - 1));
      nextBtn.addEventListener('click', () => goTo(carouselIndex + 1));
      dotsWrap.addEventListener('click', e => {
        const d = e.target.closest('.dot');
        if (d) goTo(+d.dataset.dot);
      });

      let startX = 0;
      track.addEventListener('pointerdown', e => { startX = e.clientX; track.setPointerCapture(e.pointerId); });
      track.addEventListener('pointerup',   e => {
        const dx = e.clientX - startX;
        if (Math.abs(dx) > 44) goTo(dx < 0 ? carouselIndex + 1 : carouselIndex - 1);
      });

      let resizeTO;
      const resizeObs = new ResizeObserver(() => {
        clearTimeout(resizeTO);
        resizeTO = setTimeout(() => {
          const nv = slidesVisible();
          if (nv !== carouselSlidesVisible) {
            carouselSlidesVisible = nv;
            const pct = 100 / nv;
            slides.forEach(s => { s.style.flexBasis = `calc(${pct}% - 2px)`; });
            buildDots();
            carouselIndex = Math.min(carouselIndex, maxIdx());
          }
          gsap.set(track, { x: -carouselIndex * slideWidth() });
          updateDots(); updateBtns();
        }, 80);
      });
      resizeObs.observe(track.parentElement);

      // Initial basis setup
      const pct = 100 / carouselSlidesVisible;
      slides.forEach(s => { s.style.flexBasis = `calc(${pct}% - 2px)`; });

      updateBtns();
    }

    /* ══════════════════ CLICK → LIGHTBOX ══════════════════ */
    function bindClicks() {
      const container = document.getElementById('gallery-section');
      if(!container) return;
      
      let downX = 0, downY = 0;
      container.addEventListener('pointerdown', e => {
        downX = e.clientX; downY = e.clientY;
      });

      container.addEventListener('click', e => {
        const item = e.target.closest('[data-index]');
        if (item) {
          const dist = Math.hypot(e.clientX - downX, e.clientY - downY);
          if (dist < 8) openLightbox(+item.dataset.index);
        }
      });
      container.addEventListener('keydown', e => {
        if (e.key==='Enter'||e.key===' ') {
          const item = e.target.closest('[data-index]');
          if (item) openLightbox(+item.dataset.index);
        }
      });
    }

    /* ══════════════════ LIGHTBOX ══════════════════ */
    const lb      = document.getElementById('lightbox');
    const lbImg   = document.getElementById('lightbox-img');
    const lbBgImg = document.getElementById('lightbox-bg-img');
    const lbCount = document.getElementById('lightbox-counter');
    const lbClose = document.getElementById('lightbox-close');
    const lbPrev  = document.getElementById('lightbox-prev');
    const lbNext  = document.getElementById('lightbox-next');

    function openLightbox(index) {
      lightboxIndex = index;
      isLightboxOpen = true;
      if (typeof window.lenis !== "undefined") window.lenis.stop();
      document.body.style.overflow = 'hidden';
      lb.classList.add('active');
      setLbImage(false);
      gsap.fromTo(lb, { opacity:0 }, { opacity:1, duration:0.4, ease:'power2.out' });
      gsap.fromTo(lbImg, { scale:0.88, opacity:0, y:18 }, { scale:1, opacity:1, y:0, duration:0.55, ease:'expo.out', delay:0.04 });
    }

    function closeLightbox() {
      isLightboxOpen = false;
      if (typeof window.lenis !== "undefined") window.lenis.start();
      document.body.style.overflow = '';
      gsap.to(lbImg, { scale:0.9, opacity:0, y:14, duration:0.3, ease:'power2.in' });
      gsap.to(lb, { opacity:0, duration:0.35, ease:'power2.in', delay:0.04,
        onComplete: () => lb.classList.remove('active') });
    }

    function setLbImage(animate) {
      const img = IMAGES[lightboxIndex];
      lbCount.textContent = `${lightboxIndex+1} \u2014 ${IMAGES.length}`;
      lbPrev.disabled = lightboxIndex === 0;
      lbNext.disabled = lightboxIndex === IMAGES.length - 1;
      
      // Update the blurred background image too
      lbBgImg.src = img.src;

      if (!animate) { 
        lbImg.src = img.src; 
        lbImg.alt = img.label; 
        return; 
      }
      
      const dir = animate; 
      gsap.to(lbImg, { opacity:0, x: dir*-24, duration:0.18, ease:'power2.in', onComplete: () => {
        lbImg.src = img.src; lbImg.alt = img.label;
        gsap.fromTo(lbImg, { opacity:0, x: dir*24 }, { opacity:1, x:0, duration:0.32, ease:'power2.out' });
      }});
    }

    lbClose.addEventListener('click', closeLightbox);
    document.getElementById('lightbox-backdrop').addEventListener('click', closeLightbox);
    lbPrev.addEventListener('click', () => { if(lightboxIndex>0){ lightboxIndex--; setLbImage(-1); } });
    lbNext.addEventListener('click', () => { if(lightboxIndex<IMAGES.length-1){ lightboxIndex++; setLbImage(1); } });

    document.addEventListener('keydown', e => {
      if (!isLightboxOpen) return;
      if (e.key==='Escape') closeLightbox();
      if (e.key==='ArrowLeft'  && lightboxIndex>0) { lightboxIndex--; setLbImage(-1); }
      if (e.key==='ArrowRight' && lightboxIndex<IMAGES.length-1) { lightboxIndex++; setLbImage(1); }
    });

    let lbTx = 0;
    lb.addEventListener('touchstart', e => { lbTx = e.touches[0].clientX; }, { passive:true });
    lb.addEventListener('touchend',   e => {
      const dx = e.changedTouches[0].clientX - lbTx;
      if (Math.abs(dx) > 48) {
        if (dx < 0 && lightboxIndex < IMAGES.length-1) { lightboxIndex++; setLbImage(1); }
        if (dx > 0 && lightboxIndex > 0) { lightboxIndex--; setLbImage(-1); }
      }
    });

});
</script>
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