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
      transform: scale(1.1) translateZ(0);
      will-change: transform, opacity;
      backface-visibility: hidden;
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
      width: 48px; height: 48px; border-radius: 50%;
      border: 1px solid rgba(0,0,0,0.1); background: #fff;
      color: rgba(0,0,0,0.6); cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.25rem; font-weight: bold; transition: all 0.2s ease;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      touch-action: manipulation;
    }
    .carousel-btn:hover { border-color: rgba(0,0,0,0.2); color: #000; background: #f8fafc; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,0.08); }
    .dark .carousel-btn {
      border: 1px solid rgba(255,255,255,0.1); background: #18181b;
      color: rgba(255,255,255,0.65);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .dark .carousel-btn:hover { border-color: rgba(255,255,255,0.2); color: #fff; background: #27272a; }
    .carousel-btn:disabled { opacity: 0.3; pointer-events: none; transform: none; box-shadow: none; }
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

<style>
/* New adjustments for grid layout */
.sticky-sidebar { position: sticky; top: 6rem; z-index: 40; }
.profile-card { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
</style>
@endpush

@section('content')
<div class="profile-page min-h-screen bg-slate-50 dark:bg-[#0a0a0c] relative overflow-hidden" x-data="profilePage()">

    {{-- Ambient Glow --}}
    <div class="profile-glow w-[600px] h-[600px] bg-primary/20 -top-60 -start-40 absolute z-0"></div>
    <div class="profile-glow w-[500px] h-[500px] bg-primary/10 top-[40%] -end-40 absolute z-0"></div>

    {{-- STATUS BANNERS --}}
    @if($business->status === 'pending')
    <div class="relative bg-amber-50/80 dark:bg-amber-950/20 border-b border-amber-200/60 dark:border-amber-900/30 z-20">
        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 py-3 flex items-center gap-3">
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
    <div class="relative bg-red-50/80 dark:bg-red-950/15 border-b border-red-200/60 dark:border-red-900/20 z-20">
        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 py-3 flex items-start gap-3">
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

    <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-32 pb-8 sm:pb-12 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            
            {{-- LEFT COLUMN: Identity & Quick Contacts --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="sticky-sidebar space-y-6">
                    
                    {{-- Profile Card --}}
                    <div class="bg-white dark:bg-[#0e0e11] rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/40 border border-slate-100 dark:border-zinc-800 overflow-hidden profile-fade">
                        
                        {{-- Cover Image --}}
                        <div class="relative h-40 w-full overflow-hidden">
                            @if($business->cover)
                                <img src="{{ str_contains($business->cover, 'categories') ? asset($business->cover) : $business->cover_url }}" alt="{{ $business->name }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            @else
                                <div class="w-full h-full cover-pattern relative">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                </div>
                            @endif
                            
                            {{-- Share Button on Cover --}}
                            <div class="absolute top-4 end-4 z-20 relative">
                                <button @click="shareProfile()" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-white/40 transition-colors shadow-lg" aria-label="{{ __('directory.profile_share') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                </button>
                                <div x-show="showShareTooltip" x-cloak class="absolute top-12 end-0 px-3 py-1 rounded-lg bg-slate-900 text-white text-xs font-semibold whitespace-nowrap shadow-lg">
                                    ✓ Copied
                                </div>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="px-6 pb-8 relative z-20 -mt-12 text-center flex flex-col items-center">
                            {{-- Logo --}}
                            <div class="w-24 h-24 rounded-[1.25rem] logo-ring overflow-hidden bg-white dark:bg-[#0e0e11] mb-4 shadow-lg shrink-0">
                                @if($business->logo)
                                    <img src="{{ $business->logo_url }}" alt="{{ $business->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 to-primary/10 dark:from-primary/10 dark:to-primary/20">
                                        <span class="text-4xl font-black text-primary/70">{{ mb_substr($business->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-center gap-2 mb-2">
                                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                                    {{ $business->name }}
                                </h1>
                                @if($business->status === 'approved')
                                <div title="{{ __('directory.profile_verified') }}">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center mt-1">
                                        <svg class="w-3.5 h-3.5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="flex flex-wrap justify-center gap-2 mb-4 mt-2">
                                @if($business->category)
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-primary/5 dark:bg-primary/10 text-primary border border-primary/10">
                                    {{ $business->category->name }}
                                </span>
                                @endif
                            </div>
                            
                            @if($business->city)
                            <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 flex items-center justify-center gap-1.5 mb-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $business->city->name }}@if($business->city->country), {{ $business->city->country->name }}@endif
                            </p>
                            @endif
                            
                            <p class="text-[13px] font-medium text-slate-400 dark:text-zinc-500 flex items-center justify-center gap-1.5 mt-2">
                                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ __('directory.profile_member_since') }} {{ $business->created_at->format('M Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Quick Contacts --}}
                    @php
                        $contacts = $business->contact_methods ?? [];
                        $hasContacts = !empty($contacts['whatsapp']) || !empty($contacts['phone']) || !empty($contacts['email']) || !empty($contacts['website']) || !empty($contacts['instagram']) || !empty($contacts['facebook']) || !empty($contacts['twitter']) || !empty($contacts['tiktok']) || !empty($contacts['linkedin']);
                    @endphp

                    @if($hasContacts)
                    <div class="bg-white dark:bg-[#0e0e11] rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/40 border border-slate-100 dark:border-zinc-800 p-6 profile-fade profile-fade-delay-1">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500 mb-5 text-center">{{ __('directory.profile_contact') }}</h3>
                        <div class="flex flex-col gap-3">
                            @if(!empty($contacts['whatsapp']))
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contacts['whatsapp']) }}" target="_blank" rel="noopener" class="contact-pill w-full inline-flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl text-sm font-bold bg-emerald-500 text-white shadow-lg shadow-emerald-500/25 hover:bg-emerald-600 hover:-translate-y-0.5 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.012 2c-5.508 0-9.987 4.479-9.987 9.988 0 1.757.455 3.47 1.316 4.972l-1.341 4.922 5.035-1.321c1.442.786 3.064 1.2 4.733 1.2 5.507 0 9.988-4.479 9.988-9.988 0-5.508-4.481-9.988-9.988-9.988zm5.952 14.129c-.255.719-1.488 1.309-2.039 1.391-.497.073-.789.243-2.993-.655-2.203-.898-3.626-3.141-3.737-3.289-.112-.149-.912-1.21-.912-2.308 0-1.097.575-1.637.779-1.859.204-.223.446-.279.595-.279.149 0 .297.001.427.007.137.007.319-.052.499.383.181.437.618 1.503.671 1.614.053.111.088.241.014.39-.074.148-.111.241-.223.371-.111.13-.231.291-.329.39-.111.111-.227.233-.098.455.129.223.576.953 1.238 1.541.852.759 1.569 1.01 1.792 1.121.223.111.353.093.484-.056.13-.149.559-.652.707-.869.149-.223.298-.186.502-.112.204.073 1.298.614 1.52.726.223.111.371.167.426.26.056.093.056.541-.199 1.26z"/></svg>
                                {{ __('directory.profile_whatsapp') }}
                            </a>
                            @endif

                            <div class="grid grid-cols-2 gap-3">
                                @if(!empty($contacts['phone']))
                                <a href="tel:{{ $contacts['phone'] }}" class="contact-pill inline-flex items-center justify-center gap-2 px-3 py-3 rounded-xl text-sm font-semibold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/40 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ __('directory.profile_call') }}
                                </a>
                                @endif
                                @if(!empty($contacts['email']))
                                <a href="mailto:{{ $contacts['email'] }}" class="contact-pill inline-flex items-center justify-center gap-2 px-3 py-3 rounded-xl text-sm font-semibold bg-slate-50 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-800 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ __('directory.profile_email') }}
                                </a>
                                @endif
                            </div>

                            @if(!empty($contacts['website']) || !empty($contacts['instagram']) || !empty($contacts['facebook']) || !empty($contacts['twitter']) || !empty($contacts['tiktok']) || !empty($contacts['linkedin']))
                            <div class="flex flex-wrap items-center justify-center gap-2 mt-3 pt-3 border-t border-slate-100 dark:border-zinc-800/80">
                                @if(!empty($contacts['website']))
                                <a href="{{ $contacts['website'] }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-zinc-900 flex items-center justify-center text-slate-500 dark:text-zinc-400 hover:text-primary hover:bg-primary/10 transition-all hover:scale-110" title="{{ __('directory.profile_website') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </a>
                                @endif
                                @if(!empty($contacts['instagram']))
                                <a href="{{ $contacts['instagram'] }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-zinc-900 flex items-center justify-center text-slate-500 dark:text-zinc-400 hover:text-pink-600 hover:bg-pink-50 transition-all hover:scale-110">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                                @endif
                                @if(!empty($contacts['facebook']))
                                <a href="{{ $contacts['facebook'] }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-zinc-900 flex items-center justify-center text-slate-500 dark:text-zinc-400 hover:text-blue-600 hover:bg-blue-50 transition-all hover:scale-110">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                                @endif
                                @if(!empty($contacts['twitter']))
                                <a href="{{ $contacts['twitter'] }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-zinc-900 flex items-center justify-center text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-zinc-700 transition-all hover:scale-110">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                                @endif
                                @if(!empty($contacts['tiktok']))
                                <a href="{{ $contacts['tiktok'] }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-zinc-900 flex items-center justify-center text-slate-500 dark:text-zinc-400 hover:text-black dark:hover:text-white hover:bg-slate-200 dark:hover:bg-zinc-700 transition-all hover:scale-110">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                </a>
                                @endif
                                @if(!empty($contacts['linkedin']))
                                <a href="{{ $contacts['linkedin'] }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-zinc-900 flex items-center justify-center text-slate-500 dark:text-zinc-400 hover:text-blue-700 hover:bg-blue-50 transition-all hover:scale-110">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT COLUMN: Main Content --}}
            <div class="lg:col-span-8 space-y-8 pb-24">
                
                {{-- About & Address --}}
                @if($business->description || $business->address)
                <div id="about-section" class="bg-white dark:bg-[#0e0e11] rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/40 border border-slate-100 dark:border-zinc-800 p-8 sm:p-10 profile-fade profile-fade-delay-2 scroll-mt-24">
                    @if($business->description)
                    <div class="mb-8">
                        <h2 class="text-sm font-black uppercase tracking-[0.2em] text-primary mb-5 flex items-center gap-3">
                            <span class="w-1.5 h-6 rounded-full bg-primary block"></span>
                            {{ __('directory.profile_about') }}
                        </h2>
                        <p class="text-[15px] sm:text-base text-slate-600 dark:text-zinc-300 leading-loose font-medium whitespace-pre-line">{{ $business->description }}</p>
                    </div>
                    @endif

                    @if($business->address)
                    <div class="flex items-start gap-4 p-5 rounded-2xl bg-slate-50 dark:bg-zinc-900/50 border border-slate-100 dark:border-zinc-800/80 hover:border-primary/30 transition-colors group">
                        <div class="shrink-0 w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary mt-0.5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-zinc-500 mb-1.5">{{ __('directory.profile_address') }}</p>
                            <p class="text-sm sm:text-[15px] font-semibold text-slate-800 dark:text-zinc-200 leading-relaxed">{{ $business->address }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Premium Gallery --}}
                @php
                    $mediaCount = $business->media->count();
                    $mediaItems = $business->media;
                @endphp

                @if($mediaCount > 0)
                <div id="gallery-section" class="bg-white dark:bg-[#0e0e11] rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/40 p-8 sm:p-10 profile-fade scroll-mt-24">
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-primary mb-2 flex items-center gap-3">
                                <span class="w-1.5 h-6 rounded-full bg-primary block"></span>
                                {{ __('directory.profile_gallery') }}
                            </h2>
                            <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">
                                {{ $mediaCount }} {{ $mediaCount === 1 ? __('directory.profile_photo') : __('directory.profile_photos') }}
                            </p>
                        </div>
                    </div>

                    <div id="gallery-container" class="rounded-2xl overflow-hidden">
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
                                <button class="carousel-btn" id="carousel-prev" disabled>
                                    <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <div class="carousel-dots" id="carousel-dots"></div>
                                <button class="carousel-btn" id="carousel-next">
                                    <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </nav>
                        @endif
                    </div>
                </div>


                @endif

                {{-- Contact Form --}}
                <div id="contact-form-section" class="bg-white dark:bg-[#0e0e11] rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/40 border border-slate-100 dark:border-zinc-800 p-8 sm:p-10 profile-fade scroll-mt-24" x-data="{ leadStatus: '', isSubmitting: false }">
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-primary mb-2 flex items-center gap-3">
                        <span class="w-1.5 h-6 rounded-full bg-primary block"></span>
                        {{ __('directory.get_quote') }}
                    </h2>
                    <p class="text-[15px] text-slate-500 dark:text-zinc-400 mb-8">{{ __('directory.lead_message') }}</p>

                    <div x-show="leadStatus === 'success'" x-cloak class="mb-8 p-5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/30 rounded-2xl flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-base font-semibold">{{ __('directory.lead_success') }}</span>
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
                    " class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-400 mb-2">{{ __('directory.lead_name') }} *</label>
                            <input type="text" name="name" required class="w-full bg-slate-50 dark:bg-[#131318] border border-slate-200 dark:border-zinc-800 rounded-xl px-5 py-3.5 text-[15px] focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="{{ __('directory.lead_name') }}">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-400 mb-2">{{ __('directory.lead_email') }}</label>
                                <input type="email" name="email" class="w-full bg-slate-50 dark:bg-[#131318] border border-slate-200 dark:border-zinc-800 rounded-xl px-5 py-3.5 text-[15px] focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="example@domain.com">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-400 mb-2">{{ __('directory.lead_phone') }}</label>
                                <input type="text" name="phone" dir="ltr" class="w-full bg-slate-50 dark:bg-[#131318] border border-slate-200 dark:border-zinc-800 rounded-xl px-5 py-3.5 text-[15px] focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="+123456789">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-zinc-400 mb-2">{{ __('directory.lead_message_label') }} *</label>
                            <textarea name="message" required rows="4" class="w-full bg-slate-50 dark:bg-[#131318] border border-slate-200 dark:border-zinc-800 rounded-xl px-5 py-3.5 text-[15px] focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all resize-y placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="{{ __('directory.lead_message_placeholder') }}"></textarea>
                        </div>
                        <button type="submit" :disabled="isSubmitting" class="w-full sm:w-auto px-10 py-4 bg-primary text-white rounded-xl font-bold text-[15px] hover:bg-primary-dark hover:shadow-xl hover:-translate-y-0.5 hover:shadow-primary/30 transition-all disabled:opacity-70 flex items-center justify-center gap-3">
                            <span x-show="!isSubmitting">{{ __('directory.lead_submit') }}</span>
                            <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('directory.lead_submitting') }}
                            </span>
                        </button>
                    </form>
                </div>

                {{-- Reviews --}}
                <div id="reviews-section" class="bg-white dark:bg-[#0e0e11] rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/40 p-8 sm:p-10 profile-fade scroll-mt-24">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-primary mb-5 flex items-center gap-3">
                                <span class="w-1.5 h-6 rounded-full bg-primary block"></span>
                                {{ __('directory.business_reviews') }}
                            </h2>
                            <div class="flex items-center gap-6">
                                <span class="text-6xl font-black text-slate-900 dark:text-white leading-none tracking-tighter">{{ number_format($business->averageRating(), 1) }}</span>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center text-amber-400 gap-0.5">
                                        @php $avgRating = $business->averageRating(); @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= round($avgRating) ? 'fill-current' : 'text-slate-200 dark:text-zinc-800 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm font-semibold text-slate-500 dark:text-zinc-400">{{ $business->reviews()->where('status', 'approved')->count() }} {{ __('directory.reviews_count') }}</span>
                                </div>
                            </div>
                        </div>
                        @if(!Auth::check() || Auth::id() !== $business->owner_id)
                            <button onclick="document.getElementById('review-form').scrollIntoView({behavior: 'smooth'})" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-slate-900 dark:text-white rounded-xl font-bold text-sm transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                {{ __('directory.write_review') }}
                            </button>
                        @endif
                    </div>

                    @if(!Auth::check() || Auth::id() !== $business->owner_id)
                        <div id="review-form" class="mb-12 p-8 rounded-3xl bg-slate-50/50 dark:bg-[#131318]/50 border border-slate-100 dark:border-zinc-800/80" x-data="{ 
                            step: 1,
                            rating: 0, 
                            isSubmittingReview: false, 
                            reviewStatus: '',
                            setRating(val) { this.rating = val; },
                            isActive(val) { return this.rating === val; },
                            nextStep() {
                                const form = this.$refs.reviewForm;
                                if(form.comment.value.trim() === '') { alert('{{ __('directory.please_enter_comment') }}'); return; }
                                if(form.reviewer_name && form.reviewer_name.value.trim() === '') { alert('{{ __('directory.please_enter_name') }}'); return; }
                                this.step = 2;
                            },
                            prevStep() {
                                this.step = 1;
                            }
                        }">
                            <h3 class="text-lg font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                {{ __('directory.write_review') }}
                            </h3>
                            
                            <div x-show="reviewStatus === 'success'" x-cloak x-transition.duration.400ms class="mb-6 p-5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-[15px]">{{ __('directory.review_success') }}</div>
                                </div>
                            </div>

                            <form x-ref="reviewForm" x-show="reviewStatus !== 'success'" x-transition.duration.400ms @submit.prevent="
                                if(rating === 0) { alert('{{ __('directory.please_select_rating') }}'); return; }
                                isSubmittingReview = true;
                                const formElement = $event.target;
                                fetch('{{ route('directory.business.reviews.store', $business->slug) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        rating: rating,
                                        reviewer_name: formElement.reviewer_name ? formElement.reviewer_name.value : '{{ Auth::user()->name ?? '' }}',
                                        reviewer_email: formElement.reviewer_email ? formElement.reviewer_email.value : '{{ Auth::user()->email ?? '' }}',
                                        comment: formElement.comment.value
                                    })
                                }).then(res => res.json()).then(data => {
                                    if(data.success) {
                                        reviewStatus = 'success';
                                        setTimeout(() => { reviewStatus = ''; formElement.reset(); rating = 0; }, 5000);
                                    } else {
                                        alert(data.message || 'Error occurred');
                                    }
                                }).finally(() => isSubmittingReview = false);
                            ">
                                <!-- STEP 1 -->
                                <div x-show="step === 1" x-transition.opacity x-transition:enter.duration.300ms>
                                    @guest
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                        <div>
                                            <input type="text" name="reviewer_name" required class="w-full bg-white dark:bg-[#0a0a0c] border border-slate-200 dark:border-zinc-800/80 rounded-xl px-5 py-3.5 text-[15px] focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all shadow-sm" placeholder="{{ __('directory.lead_name') }} *">
                                        </div>
                                        <div>
                                            <input type="email" name="reviewer_email" class="w-full bg-white dark:bg-[#0a0a0c] border border-slate-200 dark:border-zinc-800/80 rounded-xl px-5 py-3.5 text-[15px] focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all shadow-sm" placeholder="{{ __('directory.lead_email') }}">
                                        </div>
                                    </div>
                                    @endguest

                                    <div class="mb-6">
                                        <textarea name="comment" required rows="4" class="w-full bg-white dark:bg-[#0a0a0c] border border-slate-200 dark:border-zinc-800/80 rounded-xl px-5 py-4 text-[15px] focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none transition-all resize-y shadow-sm" placeholder="{{ __('directory.share_experience') }}"></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button type="button" @click="nextStep()" class="px-8 py-3.5 bg-slate-900 dark:bg-white hover:bg-slate-800 dark:hover:bg-slate-100 text-white dark:text-slate-900 rounded-xl font-bold text-[15px] shadow-lg shadow-black/10 dark:shadow-white/10 transition-all flex items-center justify-center gap-3 w-full sm:w-auto">
                                            {{ __('directory.continue') }}
                                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 2 -->
                                <div x-show="step === 2" x-cloak x-transition.opacity x-transition:enter.duration.400ms x-transition:enter.delay.100ms>
                                    <h4 class="text-lg font-black text-slate-900 dark:text-white mb-6 text-center">{{ __('directory.how_rate_experience') }}</h4>
                                    
                                    <div class="flex flex-row items-center justify-center gap-2 sm:gap-4 mb-10 w-full" dir="ltr">
                                        <!-- 1: Terrible -->
                                        <button type="button" @click="setRating(1)" class="flex-1 max-w-[120px] flex flex-col items-center justify-center gap-3 py-5 sm:py-6 px-2 rounded-2xl border-2 transition-all duration-200 relative" :class="isActive(1) ? 'bg-primary/5 dark:bg-primary/10 border-primary shadow-md shadow-primary/10 scale-105 z-10' : 'bg-white dark:bg-[#0a0a0c] border-slate-100 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-600'">
                                            <span class="text-[28px] sm:text-4xl transition-transform duration-200" :class="isActive(1) ? 'scale-110 drop-shadow-sm' : 'opacity-70'">😡</span>
                                            <span class="text-[9px] sm:text-[11px] font-bold uppercase tracking-widest transition-colors duration-200" :class="isActive(1) ? 'text-primary' : 'text-slate-400 dark:text-zinc-500'">{{ __('directory.rating_terrible') }}</span>
                                        </button>
                                        
                                        <!-- 2: Bad -->
                                        <button type="button" @click="setRating(2)" class="flex-1 max-w-[120px] flex flex-col items-center justify-center gap-3 py-5 sm:py-6 px-2 rounded-2xl border-2 transition-all duration-200 relative" :class="isActive(2) ? 'bg-primary/5 dark:bg-primary/10 border-primary shadow-md shadow-primary/10 scale-105 z-10' : 'bg-white dark:bg-[#0a0a0c] border-slate-100 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-600'">
                                            <span class="text-[28px] sm:text-4xl transition-transform duration-200" :class="isActive(2) ? 'scale-110 drop-shadow-sm' : 'opacity-70'">😕</span>
                                            <span class="text-[9px] sm:text-[11px] font-bold uppercase tracking-widest transition-colors duration-200" :class="isActive(2) ? 'text-primary' : 'text-slate-400 dark:text-zinc-500'">{{ __('directory.rating_bad') }}</span>
                                        </button>

                                        <!-- 3: Okay -->
                                        <button type="button" @click="setRating(3)" class="flex-1 max-w-[120px] flex flex-col items-center justify-center gap-3 py-5 sm:py-6 px-2 rounded-2xl border-2 transition-all duration-200 relative" :class="isActive(3) ? 'bg-primary/5 dark:bg-primary/10 border-primary shadow-md shadow-primary/10 scale-105 z-10' : 'bg-white dark:bg-[#0a0a0c] border-slate-100 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-600'">
                                            <span class="text-[28px] sm:text-4xl transition-transform duration-200" :class="isActive(3) ? 'scale-110 drop-shadow-sm' : 'opacity-70'">😐</span>
                                            <span class="text-[9px] sm:text-[11px] font-bold uppercase tracking-widest transition-colors duration-200" :class="isActive(3) ? 'text-primary' : 'text-slate-400 dark:text-zinc-500'">{{ __('directory.rating_okay') }}</span>
                                        </button>

                                        <!-- 4: Good -->
                                        <button type="button" @click="setRating(4)" class="flex-1 max-w-[120px] flex flex-col items-center justify-center gap-3 py-5 sm:py-6 px-2 rounded-2xl border-2 transition-all duration-200 relative" :class="isActive(4) ? 'bg-primary/5 dark:bg-primary/10 border-primary shadow-md shadow-primary/10 scale-105 z-10' : 'bg-white dark:bg-[#0a0a0c] border-slate-100 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-600'">
                                            <span class="text-[28px] sm:text-4xl transition-transform duration-200" :class="isActive(4) ? 'scale-110 drop-shadow-sm' : 'opacity-70'">🙂</span>
                                            <span class="text-[9px] sm:text-[11px] font-bold uppercase tracking-widest transition-colors duration-200" :class="isActive(4) ? 'text-primary' : 'text-slate-400 dark:text-zinc-500'">{{ __('directory.rating_good') }}</span>
                                        </button>

                                        <!-- 5: Excellent -->
                                        <button type="button" @click="setRating(5)" class="flex-1 max-w-[120px] flex flex-col items-center justify-center gap-3 py-5 sm:py-6 px-2 rounded-2xl border-2 transition-all duration-200 relative" :class="isActive(5) ? 'bg-primary/5 dark:bg-primary/10 border-primary shadow-md shadow-primary/10 scale-105 z-10' : 'bg-white dark:bg-[#0a0a0c] border-slate-100 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-600'">
                                            <span class="text-[28px] sm:text-4xl transition-transform duration-200" :class="isActive(5) ? 'scale-110 drop-shadow-sm' : 'opacity-70'">🤩</span>
                                            <span class="text-[9px] sm:text-[11px] font-bold uppercase tracking-widest transition-colors duration-200" :class="isActive(5) ? 'text-primary' : 'text-slate-400 dark:text-zinc-500'">{{ __('directory.rating_excellent') }}</span>
                                        </button>
                                    </div>

                                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8 pt-6 border-t border-slate-200/60 dark:border-zinc-800/80">
                                        <button type="button" @click="prevStep()" class="text-[14px] font-bold text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-white transition-colors order-2 sm:order-1 flex items-center gap-2">
                                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                            {{ __('directory.back') }}
                                        </button>
                                        <button type="submit" :disabled="rating === 0 || isSubmittingReview" class="px-8 py-3.5 bg-primary hover:bg-primary/90 text-white rounded-xl font-bold text-[15px] shadow-lg shadow-primary/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3 w-full sm:w-auto order-1 sm:order-2">
                                            <span x-show="!isSubmittingReview">{{ __('directory.submit_review') }}</span>
                                            <span x-show="isSubmittingReview" x-cloak class="flex items-center gap-2">
                                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                {{ __('directory.submitting') }}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="space-y-6">
                        @forelse($business->reviews()->where('status', 'approved')->latest()->get() as $review)
                            <div class="p-6 sm:p-8 rounded-3xl bg-slate-50/50 dark:bg-[#131318]/40 hover:bg-slate-50 dark:hover:bg-[#131318] border border-slate-100 dark:border-zinc-800/60 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 border border-primary/10 flex items-center justify-center text-primary font-black text-xl shadow-inner shrink-0">
                                            {{ mb_substr($review->user->name ?? $review->reviewer_name ?? 'A', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-[16px] font-bold text-slate-900 dark:text-white">{{ $review->user->name ?? $review->reviewer_name ?? 'Anonymous' }}</p>
                                            <div class="flex items-center gap-3 mt-1">
                                                <div class="flex items-center text-amber-400 gap-0.5">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-200 dark:text-zinc-800 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    @endfor
                                                </div>
                                                <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700"></span>
                                                <span class="text-[13px] font-medium text-slate-400 dark:text-zinc-500">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[15px] text-slate-700 dark:text-zinc-300 leading-relaxed">{{ $review->comment }}</p>
                                
                                @if($review->reply)
                                    <div class="mt-6 p-5 sm:p-6 bg-slate-100/50 dark:bg-black/40 rounded-2xl border border-slate-200/50 dark:border-white/5 relative">
                                        <div class="absolute -top-3 left-6 px-3 py-1 bg-primary text-white text-[11px] font-bold uppercase tracking-widest rounded-full shadow-sm">{{ __('directory.business_reply') }}</div>
                                        <p class="text-[15px] text-slate-700 dark:text-zinc-400 leading-relaxed mt-2">{{ $review->reply }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-16 px-4 rounded-3xl bg-slate-50 dark:bg-zinc-900/30 border border-slate-100 dark:border-white/5 border-dashed">
                                <p class="text-[15px] font-medium text-slate-500 dark:text-zinc-400">{{ __('directory.no_reviews_yet') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ── Lightbox ── -->
<div id="lightbox" role="dialog" aria-modal="true" aria-label="Image viewer">
  <img id="lightbox-bg-img" src="" alt="" />
  <div id="lightbox-backdrop"></div>
  <button id="lightbox-prev" aria-label="Previous">
      <svg class="w-8 h-8 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
  </button>
  <div id="lightbox-img-wrap">
    <img id="lightbox-img" src="" alt="" />
  </div>
  <button id="lightbox-next" aria-label="Next">
      <svg class="w-8 h-8 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
  </button>
  <button id="lightbox-close" aria-label="Close">&#x2715;</button>
  <div id="lightbox-counter"></div>
</div>

@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    /* ══════════════════ LENIS ══════════════════ */
    if (window.lenis) window.lenis.destroy();
    const lenis = new Lenis({ lerp: 0.08, smoothWheel: true });
    window.lenis = lenis; // Expose for lightbox

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
        const isRtl = document.documentElement.dir === 'rtl';
        gsap.to(track, { x: (isRtl ? 1 : -1) * carouselIndex * slideWidth(), duration:0.75, ease:'expo.inOut' });
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
        if (Math.abs(dx) > 44) {
            const isRtl = document.documentElement.dir === 'rtl';
            const dragDir = dx < 0 ? 1 : -1;
            goTo(carouselIndex + (isRtl ? -dragDir : dragDir));
        }
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
          const isRtl = document.documentElement.dir === 'rtl';
          gsap.set(track, { x: (isRtl ? 1 : -1) * carouselIndex * slideWidth() });
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
      let downTarget = null;
      container.addEventListener('pointerdown', e => {
        downX = e.clientX; downY = e.clientY;
        downTarget = e.target.closest('[data-index]');
      });

      container.addEventListener('pointerup', e => {
        if (!downTarget) return;
        const dist = Math.hypot(e.clientX - downX, e.clientY - downY);
        if (dist < 10) openLightbox(+downTarget.dataset.index);
        downTarget = null;
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
      lb.classList.add('active');
      setLbImage(false);
      gsap.fromTo(lb, { opacity:0 }, { opacity:1, duration:0.4, ease:'power2.out' });
      gsap.fromTo(lbImg, { scale:0.88, opacity:0, y:18 }, { scale:1, opacity:1, y:0, duration:0.55, ease:'expo.out', delay:0.04 });
    }

    function closeLightbox() {
      isLightboxOpen = false;
      if (typeof window.lenis !== "undefined") window.lenis.start();
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
      
      const isRtl = document.documentElement.dir === 'rtl';
      const dir = isRtl ? -animate : animate; 
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