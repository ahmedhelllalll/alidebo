<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', __('landing.meta_description'))">
    <title>@yield('title') | alidebo</title>

    <!-- Search Engine & Indexing Optimization -->
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title') | alidebo">
    <meta property="og:description" content="@yield('meta_description', __('landing.meta_description'))">
    <meta property="og:image" content="{{ asset('images/logo.webp') }}">
    <meta property="og:site_name" content="alidebo">
    <meta property="og:locale" content="{{ app()->getLocale() == 'ar' ? 'ar_AR' : 'en_US' }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title') | alidebo">
    <meta property="twitter:description" content="@yield('meta_description', __('landing.meta_description'))">
    <meta property="twitter:image" content="{{ asset('images/logo.webp') }}">

    {{-- Anti-FOUC: Theme --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- GSAP --}}

    <style>
        /* ── Lenis Smooth Scroll Base ── */
        html.lenis, html.lenis body { height: auto; }
        .lenis.lenis-smooth { scroll-behavior: auto !important; }
        .lenis.lenis-smooth [data-lenis-prevent] { overscroll-behavior: contain; }
        .lenis.lenis-stopped { overflow: hidden; }
        .lenis.lenis-scrolling iframe { pointer-events: none; }

        /* ── Premium Base ── */
        [dir="rtl"] { font-family: 'Cairo', sans-serif; }
        [dir="ltr"] { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Accent headline (subtle motion; disabled under reduced-motion) */
        .glow-text {
            background: linear-gradient(to left, #f45018, #fb923c, #f45018);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
        }
        [dir="rtl"] .glow-text {
            background: linear-gradient(to right, #f45018, #fb923c, #f45018);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        @keyframes shine { to { background-position: 200% center; } }

        /* Bento grid */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.5rem;
        }

        /* Luxury card */
        .luxury-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            transition: border-color 0.4s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            will-change: transform, opacity;
        }
        .dark .luxury-card {
            background: #0e0e11;
            border-color: rgba(255,255,255,0.06);
        }
        .luxury-card:hover {
            border-color: rgba(244, 80, 24, 0.2);
            box-shadow: 0 0 0 1px rgba(244, 80, 24, 0.05), 0 20px 50px -12px rgba(0, 0, 0, 0.08);
        }
        .dark .luxury-card:hover {
            border-color: rgba(244, 80, 24, 0.15);
            box-shadow: 0 0 0 1px rgba(244, 80, 24, 0.08), 0 20px 50px -12px rgba(0, 0, 0, 0.4);
        }

        /* Marquee */
        .marquee {
            overflow: hidden;
            width: 100%;
        }
        .marquee-content {
            display: flex;
            gap: 4rem;
            width: max-content;
            animation: marquee-scroll 32s linear infinite;
        }
        [dir="rtl"] .marquee-content {
            animation-direction: reverse;
        }
        @keyframes marquee-scroll {
            from { transform: translate3d(0, 0, 0); }
            to { transform: translate3d(calc(-50% - 2rem), 0, 0); }
        }

        /* Story rail */
        .story-rail-item.is-active .story-rail-dot {
            background: #f45018;
            box-shadow: 0 0 0 4px rgba(244, 80, 24, 0.2);
        }
        .story-rail-item.is-active .story-rail-label {
            color: inherit;
            opacity: 1;
        }
        .story-rail-item:not(.is-active) .story-rail-dot {
            background: #cbd5e1;
        }
        .dark .story-rail-item:not(.is-active) .story-rail-dot {
            background: #52525b;
        }
        .story-rail-item:not(.is-active) .story-rail-label {
            opacity: 0.55;
        }

        @media (prefers-reduced-motion: reduce) {
            .marquee-content { animation: none !important; transform: none !important; }
            .logo-marquee-content { animation: none !important; transform: none !important; }
            .glow-text { animation: none !important; background-position: 0 center !important; }
        }

        /* Glass panel */
        .glass-panel {
            backdrop-filter: blur(16px) saturate(1.5);
            -webkit-backdrop-filter: blur(16px) saturate(1.5);
        }

        /* Hero search bar glow on focus */
        .hero-search-bar:focus-within {
            box-shadow: 0 0 0 1px rgba(244, 80, 24, 0.08), 0 20px 40px -12px rgba(244, 80, 24, 0.15);
        }
        .dark .hero-search-bar:focus-within {
            box-shadow: 0 0 0 1px rgba(244, 80, 24, 0.12), 0 20px 40px -12px rgba(0, 0, 0, 0.5);
        }

        /* Logo marquee */
        @keyframes logo-marquee-scroll {
            from { transform: translate3d(0, 0, 0); }
            to { transform: translate3d(-50%, 0, 0); }
        }

        .logo-marquee-content {
            display: flex;
            width: max-content;
            animation: logo-marquee-scroll 35s linear infinite;
        }
        [dir="rtl"] .logo-marquee-content {
            animation-direction: reverse;
        }
        .logo-marquee-item img {
            filter: grayscale(0.2);
            transition: filter 0.3s ease;
        }
        .logo-marquee-item:hover img {
            filter: grayscale(0);
        }

        /* Hero dashboard subtle float */
        .hero-dashboard {
            animation: hero-float 6s ease-in-out infinite;
        }
        @keyframes hero-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        @media (prefers-reduced-motion: reduce) {
            .hero-dashboard { animation: none !important; }
        }

        /* Glass nav - Transparent initially */
        .glass-nav {
            background: transparent;
            border-color: transparent;
        }

        /* Scrollbar minimal */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(161, 161, 170, 0.25); border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(82, 82, 91, 0.35); }

        /* Scroll reveal — initial state set by GSAP, not CSS, to prevent conflicts */
        .reveal { will-change: transform, opacity; }

        /* Nav link hover effect */
        .nav-link-effect {
            position: relative;
        }
        .nav-link-effect::after {
            content: '';
            position: absolute;
            bottom: -2px;
            inset-inline: 0;
            width: 0;
            height: 2px;
            background: #f45018;
            border-radius: 999px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-inline: auto;
        }
        .nav-link-effect:hover::after {
            width: 100%;
        }

        /* Nav — glassy effect on scroll */
        .glass-nav.is-scrolled {
            background: #ffffff;
            border-color: rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.05), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
        }
        .dark .glass-nav.is-scrolled {
            backdrop-filter: blur(20px) saturate(1.8);
            -webkit-backdrop-filter: blur(20px) saturate(1.8);
            background: rgba(9, 9, 11, 0.8);
            border-color: rgba(63, 63, 70, 0.55);
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.2), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        /* ── Mobile nav ── */
        @media (max-width: 1023px) {
            .nav-mobile-shell {
                width: 100%;
            }
        }

        #mobile-menu {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.32s ease, visibility 0.32s ease;
        }
        #mobile-menu.is-open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        #mobile-menu .mobile-menu-panel {
            transform: translateY(-1.5rem) scale(0.98);
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            transform-origin: top center;
            will-change: transform, opacity;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }
        #mobile-menu.is-open .mobile-menu-panel {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        .mobile-nav-link {
            opacity: 0;
            transform: translateY(-8px);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            will-change: transform, opacity;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }
        #mobile-menu.is-open .mobile-nav-link {
            opacity: 1;
            transform: translateY(0);
        }
        #mobile-menu.is-open .mobile-nav-link:nth-child(1) { transition-delay: 0.05s; }
        #mobile-menu.is-open .mobile-nav-link:nth-child(2) { transition-delay: 0.10s; }
        #mobile-menu.is-open .mobile-nav-link:nth-child(3) { transition-delay: 0.15s; }
        #mobile-menu.is-open .mobile-nav-link:nth-child(4) { transition-delay: 0.20s; }
        #mobile-menu.is-open .mobile-nav-link:nth-child(5) { transition-delay: 0.25s; }
        @media (prefers-reduced-motion: reduce) {
            #mobile-menu {
                transition: none !important;
            }
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="rtl:font-cairo ltr:font-jakarta bg-white dark:bg-[#09090b] text-slate-900 dark:text-white transition-colors duration-300 overflow-x-hidden antialiased">

    {{-- ═══════════════════════════════════════ --}}
    {{-- NAVIGATION --}}
    {{-- ═══════════════════════════════════════ --}}
    @if(!request()->routeIs('business.view'))
    <nav id="main-nav" class="fixed top-3 sm:top-5 inset-x-3 sm:inset-x-6 lg:inset-x-8 z-[99999] glass-nav border border-transparent rounded-2xl lg:rounded-[1.5rem] transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]">
        <div class="nav-mobile-shell relative w-full px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-[72px]">

                {{-- Logo --}}
                <div class="flex flex-1 justify-start">
                    @if(request()->routeIs('business.view') && isset($business))
                        <a href="#discover" class="flex items-center gap-2.5 group shrink-0">
                            @if($business->logo)
                                <img src="{{ $business->logo_url }}" alt="{{ $business->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200/50 dark:border-zinc-800/50">
                            @else
                                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                    {{ mb_substr($business->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="text-base sm:text-lg font-black tracking-tight text-slate-900 dark:text-white truncate max-w-[120px] sm:max-w-[200px]">{{ $business->name }}</span>
                        </a>
                    @else
                        <a href="/" class="flex items-center gap-2.5 group shrink-0" id="nav-logo">
                            <img src="{{ asset('images/logo.webp') }}" alt="alidebo" class="w-8 h-8 transition-transform duration-300 group-hover:scale-110">
                            <span class="text-xl font-[900] tracking-tighter">alidebo</span>
                        </a>
                    @endif
                </div>

                {{-- Desktop Navigation --}}
                <div class="hidden lg:flex items-center justify-center gap-1 shrink-0">
                    @if(request()->routeIs('business.view') && isset($business))
                        <a href="#discover" class="nav-link-effect px-4 py-2 text-sm font-semibold text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white">
                            {{ __('landing.nav_home') ?? 'Home' }}
                        </a>
                        @if($business->description)
                        <a href="#about-section" class="nav-link-effect px-4 py-2 text-sm font-semibold text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white">
                            {{ __('directory.profile_about') ?? 'About' }}
                        </a>
                        @endif
                        @if($business->media->count() > 0)
                        <a href="#gallery-section" class="nav-link-effect px-4 py-2 text-sm font-semibold text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white">
                            {{ __('directory.profile_gallery') ?? 'Gallery' }}
                        </a>
                        @endif
                    @else
                        <a href="{{ route('home') }}" class="nav-link-effect px-4 py-2 text-sm transition-colors rounded-lg {{ request()->routeIs('home') ? 'font-bold text-primary bg-primary/5 dark:bg-primary/10' : 'font-semibold text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ __('landing.nav_home') ?? 'Home' }}
                        </a>
                        <a href="{{ route('directory.index') }}" class="nav-link-effect px-4 py-2 text-sm transition-colors rounded-lg {{ request()->routeIs('directory.*') ? 'font-bold text-primary bg-primary/5 dark:bg-primary/10' : 'font-semibold text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ __('landing.nav_companies') ?? 'Companies' }}
                        </a>
                        <a href="{{ route('about') }}" class="nav-link-effect px-4 py-2 text-sm transition-colors rounded-lg {{ request()->routeIs('about') ? 'font-bold text-primary bg-primary/5 dark:bg-primary/10' : 'font-semibold text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ __('landing.nav_about') ?? 'About Us' }}
                        </a>
                        <a href="{{ route('contact') }}" class="nav-link-effect px-4 py-2 text-sm transition-colors rounded-lg {{ request()->routeIs('contact') ? 'font-bold text-primary bg-primary/5 dark:bg-primary/10' : 'font-semibold text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ __('landing.nav_contact') ?? 'Contact' }}
                        </a>
                    @endif
                </div>

                {{-- Right Actions --}}
                <div class="flex flex-1 items-center justify-end gap-2 sm:gap-3">

                    {{-- Language Switcher --}}
                    <div class="relative" id="langDropdownContainer">
                        <button onclick="toggleLangDropdown()" id="lang-toggle-btn" aria-label="Toggle language" class="flex items-center gap-1.5 sm:gap-2 px-2.5 py-1.5 rounded-[0.85rem] text-sm font-bold text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/60 border border-transparent hover:border-slate-200 dark:hover:border-zinc-700 transition-all duration-200">
                            @if(app()->getLocale() == 'en')
                                <img src="https://flagcdn.com/w40/us.png" alt="EN" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="hidden sm:inline">EN</span>
                            @elseif(app()->getLocale() == 'ar')
                                <img src="https://flagcdn.com/w40/eg.png" alt="AR" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="hidden sm:inline">عربي</span>
                            @elseif(app()->getLocale() == 'es')
                                <img src="https://flagcdn.com/w40/es.png" alt="ES" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="hidden sm:inline">ES</span>
                            @elseif(app()->getLocale() == 'de')
                                <img src="https://flagcdn.com/w40/de.png" alt="DE" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="hidden sm:inline">DE</span>
                            @elseif(app()->getLocale() == 'zh')
                                <img src="https://flagcdn.com/w40/cn.png" alt="CN" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="hidden sm:inline">ZH</span>
                            @elseif(app()->getLocale() == 'tr')
                                <img src="https://flagcdn.com/w40/tr.png" alt="TR" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="hidden sm:inline">TR</span>
                            @endif
                            <svg class="w-3 h-3 opacity-50 transition-transform duration-200" id="langChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        {{-- Dropdown --}}
                        <div id="langDropdownMenu" class="absolute top-full mt-2 end-0 w-44 bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800 rounded-2xl shadow-xl shadow-black/5 dark:shadow-black/30 opacity-0 pointer-events-none scale-95 transition-all duration-200 origin-top z-50 p-1.5">
                            <a href="{{ route('lang.switch', 'en') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'en' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                <img src="https://flagcdn.com/w40/us.png" alt="US" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="flex-1">English</span>
                                @if(app()->getLocale() == 'en')
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'ar' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                <img src="https://flagcdn.com/w40/eg.png" alt="EG" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="flex-1 font-cairo">العربية</span>
                                @if(app()->getLocale() == 'ar')
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </a>
                            <a href="{{ route('lang.switch', 'es') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'es' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                <img src="https://flagcdn.com/w40/es.png" alt="ES" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="flex-1">Español</span>
                                @if(app()->getLocale() == 'es')
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </a>
                            <a href="{{ route('lang.switch', 'de') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'de' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                <img src="https://flagcdn.com/w40/de.png" alt="DE" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="flex-1">Deutsch</span>
                                @if(app()->getLocale() == 'de')
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </a>
                            <a href="{{ route('lang.switch', 'zh') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'zh' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                <img src="https://flagcdn.com/w40/cn.png" alt="CN" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="flex-1">中文 (Chinese)</span>
                                @if(app()->getLocale() == 'zh')
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </a>
                            <a href="{{ route('lang.switch', 'tr') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'tr' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                <img src="https://flagcdn.com/w40/tr.png" alt="TR" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                <span class="flex-1">Türkçe (Turkish)</span>
                                @if(app()->getLocale() == 'tr')
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </a>
                        </div>
                    </div>

                    {{-- Theme Toggle --}}
                    <button onclick="toggleTheme()" id="theme-toggle-btn" class="relative p-2 rounded-[0.85rem] text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/60 border border-transparent hover:border-slate-200 dark:hover:border-zinc-700 transition-all duration-200" aria-label="Toggle theme">
                        <svg id="theme-toggle-dark-icon" class="w-[18px] h-[18px] dark:hidden transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="w-[18px] h-[18px] hidden dark:block transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    {{-- Auth Actions / Business CTA --}}
                    <div class="hidden sm:flex items-center gap-1.5">
                        @if(request()->routeIs('business.view') && isset($business))
                            @php
                                $contacts = $business->contact_methods ?? [];
                                $ctaUrl = null;
                                $ctaLabel = __('directory.profile_connect') ?? 'Connect';
                                if (!empty($contacts['whatsapp'])) {
                                    $ctaUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $contacts['whatsapp']);
                                } elseif (!empty($contacts['phone'])) {
                                    $ctaUrl = "tel:" . $contacts['phone'];
                                } elseif (!empty($contacts['email'])) {
                                    $ctaUrl = "mailto:" . $contacts['email'];
                                } elseif (!empty($contacts['website'])) {
                                    $ctaUrl = $contacts['website'];
                                }
                            @endphp

                            @if($ctaUrl)
                                <a href="{{ $ctaUrl }}" target="_blank" rel="noopener" class="px-4 py-2 bg-primary text-white rounded-[0.85rem] font-bold text-sm shadow-lg shadow-primary/20 hover:shadow-primary/30 hover:bg-primary-light active:scale-[0.97] transition-all duration-200 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    {{ $ctaLabel }}
                                </a>
                            @else
                                <button @click="shareProfile()" class="px-4 py-2 bg-primary text-white rounded-[0.85rem] font-bold text-sm shadow-lg shadow-primary/20 hover:shadow-primary/30 hover:bg-primary-light active:scale-[0.97] transition-all duration-200">
                                    {{ __('directory.profile_share') ?? 'Share' }}
                                </button>
                            @endif
                        @else
                            @guest
                                <a href="{{ route('login') }}" class="px-3 py-1.5 text-sm font-bold text-slate-700 dark:text-zinc-300 hover:text-primary transition-colors rounded-[0.85rem]">
                                    {{ __('landing.login') }}
                                </a>
                                <a href="{{ route('register') }}" id="nav-cta-btn" class="px-4 py-2 bg-primary text-white rounded-[0.85rem] font-bold text-sm shadow-lg shadow-primary/20 hover:shadow-primary/30 hover:bg-primary-light active:scale-[0.97] transition-all duration-200">
                                    {{ __('landing.get_started') }}
                                </a>
                            @else
                                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="px-4 py-2 bg-primary text-white rounded-[0.85rem] font-bold text-sm shadow-lg shadow-primary/20 hover:shadow-primary/30 hover:bg-primary-light active:scale-[0.97] transition-all duration-200 flex items-center gap-2 group">
                                    <svg class="w-4 h-4 text-white/80 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    {{ __('nav.dashboard') ?? 'Dashboard' }}
                                </a>
                            @endguest
                        @endif
                    </div>

                    {{-- Mobile Menu Toggle --}}
                    <button onclick="toggleMobileMenu()"
                            id="mobile-menu-btn"
                            class="lg:hidden relative p-2 rounded-[0.85rem] text-slate-600 dark:text-zinc-400 hover:bg-slate-100/80 dark:hover:bg-zinc-800/60 transition-colors"
                            aria-label="Menu"
                            aria-expanded="false"
                            aria-controls="mobile-menu">
                        <svg id="hamburger-icon" class="w-5 h-5 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg id="close-icon" class="w-5 h-5 absolute inset-0 m-auto opacity-0 pointer-events-none transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    {{-- Mobile Menu — perfectly synced with floating pill nav --}}
    <div id="mobile-menu"
         class="lg:hidden fixed inset-0 z-[99998]"
         aria-hidden="true">
        
        {{-- Soft Backdrop --}}
        <div class="mobile-menu-backdrop absolute inset-0 bg-slate-900/10 dark:bg-black/40 backdrop-blur-sm transition-opacity duration-300"
             onclick="closeMobileMenu()"
             aria-hidden="true"></div>

        {{-- Floating Glass Menu Pane --}}
        <div class="mobile-menu-panel absolute top-[5rem] sm:top-[6.5rem] bottom-3 sm:bottom-5 inset-x-3 sm:inset-x-6 overflow-y-auto bg-white/85 dark:bg-[#0a0a0c]/85 backdrop-blur-2xl border border-white/50 dark:border-zinc-800/50 rounded-[1.5rem] shadow-[0_24px_48px_-12px_rgba(0,0,0,0.15)] dark:shadow-[0_24px_48px_-12px_rgba(0,0,0,0.4)] flex flex-col p-2.5">
            
            <nav class="space-y-1" aria-label="Mobile navigation">
                @if(request()->routeIs('business.view') && isset($business))
                    <a href="#discover"
                       onclick="closeMobileMenu()"
                       class="mobile-nav-link group flex items-center justify-between gap-3 px-4 py-3.5 rounded-2xl text-[15px] transition-all duration-200 font-bold text-slate-700 dark:text-zinc-200 hover:bg-white dark:hover:bg-zinc-800/50 hover:shadow-sm hover:text-primary">
                        <span>{{ __('landing.nav_home') ?? 'Home' }}</span>
                        <svg class="w-4 h-4 text-slate-300 dark:text-zinc-600 group-hover:text-primary rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @if($business->description)
                    <a href="#about-section"
                       onclick="closeMobileMenu()"
                       class="mobile-nav-link group flex items-center justify-between gap-3 px-4 py-3.5 rounded-2xl text-[15px] transition-all duration-200 font-bold text-slate-700 dark:text-zinc-200 hover:bg-white dark:hover:bg-zinc-800/50 hover:shadow-sm hover:text-primary">
                        <span>{{ __('directory.profile_about') ?? 'About' }}</span>
                        <svg class="w-4 h-4 text-slate-300 dark:text-zinc-600 group-hover:text-primary rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endif
                    @if($business->media->count() > 0)
                    <a href="#gallery-section"
                       onclick="closeMobileMenu()"
                       class="mobile-nav-link group flex items-center justify-between gap-3 px-4 py-3.5 rounded-2xl text-[15px] transition-all duration-200 font-bold text-slate-700 dark:text-zinc-200 hover:bg-white dark:hover:bg-zinc-800/50 hover:shadow-sm hover:text-primary">
                        <span>{{ __('directory.profile_gallery') ?? 'Gallery' }}</span>
                        <svg class="w-4 h-4 text-slate-300 dark:text-zinc-600 group-hover:text-primary rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endif
                @else
                    @foreach([
                        ['href' => route('home'), 'route' => 'home', 'label' => __('landing.nav_home') ?? 'Home'],
                        ['href' => route('directory.index'), 'route' => 'directory.*', 'label' => __('landing.nav_companies') ?? 'Companies'],
                        ['href' => route('about'), 'route' => 'about', 'label' => __('landing.nav_about') ?? 'About Us'],
                        ['href' => route('contact'), 'route' => 'contact', 'label' => __('landing.nav_contact') ?? 'Contact'],
                    ] as $link)
                    <a href="{{ $link['href'] }}"
                       onclick="closeMobileMenu()"
                       class="mobile-nav-link group flex items-center justify-between gap-3 px-4 py-3.5 rounded-2xl text-[15px] transition-all duration-200 {{ request()->routeIs($link['route']) ? 'text-primary bg-primary/10 font-black' : 'font-bold text-slate-700 dark:text-zinc-200 hover:bg-white dark:hover:bg-zinc-800/50 hover:shadow-sm hover:text-primary' }}">
                        <span>{{ $link['label'] }}</span>
                        <svg class="w-4 h-4 transition-colors shrink-0 {{ request()->routeIs($link['route']) ? 'text-primary' : 'text-slate-300 dark:text-zinc-600 group-hover:text-primary' }} rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endforeach
                @endif
            </nav>

            <div class="mt-auto pt-4 border-t border-slate-200/50 dark:border-zinc-800/50 space-y-2">
                @if(request()->routeIs('business.view') && isset($business))
                    @if($ctaUrl)
                        <a href="{{ $ctaUrl }}"
                           target="_blank"
                           rel="noopener"
                           onclick="closeMobileMenu()"
                           class="mobile-nav-link block w-full px-4 py-3.5 rounded-2xl bg-primary text-white font-bold text-sm text-center shadow-lg shadow-primary/25 hover:bg-primary-light transition-colors">
                            {{ $ctaLabel }}
                        </a>
                    @else
                        <button @click="shareProfile(); closeMobileMenu();"
                           class="mobile-nav-link block w-full px-4 py-3.5 rounded-2xl bg-primary text-white font-bold text-sm text-center shadow-lg shadow-primary/25 hover:bg-primary-light transition-colors">
                            {{ __('directory.profile_share') ?? 'Share' }}
                        </button>
                    @endif
                @else
                    @guest
                        <a href="{{ route('login') }}"
                           onclick="closeMobileMenu()"
                           class="mobile-nav-link block w-full px-4 py-3.5 rounded-2xl text-sm font-bold text-slate-700 dark:text-zinc-300 text-center hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-primary transition-colors">
                            {{ __('landing.login') }}
                        </a>
                        <a href="{{ route('register') }}"
                           onclick="closeMobileMenu()"
                           class="mobile-nav-link block w-full px-4 py-3.5 rounded-2xl bg-primary text-white font-bold text-sm text-center shadow-lg shadow-primary/25 hover:bg-primary-light transition-colors">
                            {{ __('landing.get_started') }}
                        </a>
                    @else
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                           onclick="closeMobileMenu()"
                           class="mobile-nav-link block w-full px-4 py-3.5 rounded-2xl bg-primary text-white font-bold text-sm text-center shadow-lg shadow-primary/25 hover:bg-primary-light transition-colors">
                            {{ __('nav.dashboard') ?? 'Dashboard' }}
                        </a>
                    @endguest
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════ --}}
    {{-- MAIN CONTENT --}}
    {{-- ═══════════════════════════════════════ --}}
    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════════ --}}
    {{-- FOOTER --}}
    {{-- ═══════════════════════════════════════ --}}
    <div class="w-full px-3 sm:px-6 lg:px-8 mb-3 sm:mb-6 lg:mb-8 mt-16">
        <footer class="border border-zinc-800/60 bg-gradient-to-b from-[#101014] to-[#0a0a0c] rounded-[2rem] shadow-[0_20px_50px_-12px_rgba(0,0,0,0.5)] overflow-hidden relative">
            <div class="absolute inset-0 bg-[url('{{ asset('images/grid.svg') }}')] opacity-[0.03] pointer-events-none"></div>
            
            <div class="px-8 sm:px-12 lg:px-16 relative z-10">
                {{-- Footer Grid --}}
                <div class="py-16 lg:py-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 lg:gap-12">
                    
                    {{-- Brand Column (Left) --}}
                    <div class="sm:col-span-2 lg:col-span-1">
                        <div class="flex items-center gap-3 mb-6">
                            <img src="{{ asset('images/logo.webp') }}" alt="" loading="lazy" decoding="async" class="w-8 h-8 hover:scale-110 transition-transform duration-300">
                            <span class="text-2xl font-[900] tracking-tighter text-zinc-100">alidebo</span>
                        </div>
                        <p class="text-base text-zinc-400 leading-relaxed max-w-sm">
                            {{ __('landing.footer_desc') }}
                        </p>
                    </div>

                    {{-- Quick Links --}}
                    <div class="sm:col-span-1">
                        <h3 class="text-sm font-bold text-zinc-100 mb-6 tracking-wide">{{ __('landing.footer_company') ?? 'Company' }}</h3>
                        <ul class="space-y-4">
                            <li><a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-primary' : 'text-zinc-400 hover:text-zinc-100' }} transition-colors flex items-center gap-2 group"><span class="{{ request()->routeIs('home') ? 'w-2 bg-primary' : 'w-0 group-hover:w-2 bg-zinc-600 group-hover:bg-zinc-300' }} h-px transition-all duration-300"></span>{{ __('landing.nav_home') ?? 'Home' }}</a></li>
                            <li><a href="{{ route('directory.index') }}" class="text-sm font-medium {{ request()->routeIs('directory.*') ? 'text-primary' : 'text-zinc-400 hover:text-zinc-100' }} transition-colors flex items-center gap-2 group"><span class="{{ request()->routeIs('directory.*') ? 'w-2 bg-primary' : 'w-0 group-hover:w-2 bg-zinc-600 group-hover:bg-zinc-300' }} h-px transition-all duration-300"></span>{{ __('landing.nav_companies') ?? 'Companies' }}</a></li>
                            <li><a href="{{ route('about') }}" class="text-sm font-medium {{ request()->routeIs('about') ? 'text-primary' : 'text-zinc-400 hover:text-zinc-100' }} transition-colors flex items-center gap-2 group"><span class="{{ request()->routeIs('about') ? 'w-2 bg-primary' : 'w-0 group-hover:w-2 bg-zinc-600 group-hover:bg-zinc-300' }} h-px transition-all duration-300"></span>{{ __('landing.footer_about') ?? 'About Us' }}</a></li>
                            <li><a href="{{ route('contact') }}" class="text-sm font-medium {{ request()->routeIs('contact') ? 'text-primary' : 'text-zinc-400 hover:text-zinc-100' }} transition-colors flex items-center gap-2 group"><span class="{{ request()->routeIs('contact') ? 'w-2 bg-primary' : 'w-0 group-hover:w-2 bg-zinc-600 group-hover:bg-zinc-300' }} h-px transition-all duration-300"></span>{{ __('landing.nav_contact') ?? 'Contact' }}</a></li>
                        </ul>
                    </div>

                    {{-- Legal Links --}}
                    <div class="sm:col-span-1">
                        <h3 class="text-sm font-bold text-zinc-100 mb-6 tracking-wide">{{ __('landing.footer_legal') }}</h3>
                        <ul class="space-y-4">
                            <li><a href="{{ route('privacy') }}" class="text-sm font-medium {{ request()->routeIs('privacy') ? 'text-primary' : 'text-zinc-400 hover:text-zinc-100' }} transition-colors flex items-center gap-2 group"><span class="{{ request()->routeIs('privacy') ? 'w-2 bg-primary' : 'w-0 group-hover:w-2 bg-zinc-600 group-hover:bg-zinc-300' }} h-px transition-all duration-300"></span>{{ __('landing.footer_privacy') }}</a></li>
                            <li><a href="{{ route('terms') }}" class="text-sm font-medium {{ request()->routeIs('terms') ? 'text-primary' : 'text-zinc-400 hover:text-zinc-100' }} transition-colors flex items-center gap-2 group"><span class="{{ request()->routeIs('terms') ? 'w-2 bg-primary' : 'w-0 group-hover:w-2 bg-zinc-600 group-hover:bg-zinc-300' }} h-px transition-all duration-300"></span>{{ __('landing.footer_terms') }}</a></li>
                            <li><a href="{{ route('cookies') }}" class="text-sm font-medium {{ request()->routeIs('cookies') ? 'text-primary' : 'text-zinc-400 hover:text-zinc-100' }} transition-colors flex items-center gap-2 group"><span class="{{ request()->routeIs('cookies') ? 'w-2 bg-primary' : 'w-0 group-hover:w-2 bg-zinc-600 group-hover:bg-zinc-300' }} h-px transition-all duration-300"></span>{{ __('landing.footer_cookies') }}</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Bottom Bar --}}
                <div class="py-8 border-t border-zinc-800/60 flex flex-col md:flex-row items-center justify-between gap-6">
                    <p class="text-sm text-zinc-500 font-medium">
                        &copy; {{ date('Y') }} alidebo. {{ __('landing.footer_rights') }}
                    </p>
                    <div class="flex items-center gap-5">
                        {{-- Social Icons --}}
                        <a href="https://www.facebook.com/share/1DVyDHa1Lo/" class="text-zinc-500 hover:text-primary transition-colors" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
                        </a>
                        <a href="https://x.com/co_alidebo" class="text-zinc-500 hover:text-primary transition-colors" aria-label="Twitter">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/company/alidebo/" class="text-zinc-500 hover:text-primary transition-colors" aria-label="LinkedIn">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <a href="https://www.instagram.com/co.alidebo?igsh=MW81ZDNtcGJzc2Vjcw==" class="text-zinc-500 hover:text-primary transition-colors" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- ═══════════════════════════════════════ --}}
    {{-- SCRIPTS --}}
    {{-- ═══════════════════════════════════════ --}}
    <script>
        // ═══════════════════════════════════════════════════
        // ALIDEBO — Production Animation System
        // Single source of truth: GSAP + ScrollTrigger
        // ═══════════════════════════════════════════════════

        // ── Theme Toggle ──
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            const btn = document.getElementById('theme-toggle-btn');
            if (btn) {
                btn.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                btn.style.transform = 'rotate(360deg)';
                setTimeout(() => { btn.style.transform = ''; btn.style.transition = ''; }, 500);
            }
        }

        // ── Language Dropdown ──
        let isLangOpen = false;
        function toggleLangDropdown() {
            isLangOpen = !isLangOpen;
            const menu = document.getElementById('langDropdownMenu');
            const chevron = document.getElementById('langChevron');
            if (!menu) return;
            if (isLangOpen) {
                menu.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                menu.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            } else {
                menu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                menu.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        }
        document.addEventListener('click', (e) => {
            const container = document.getElementById('langDropdownContainer');
            if (container && !container.contains(e.target) && isLangOpen) toggleLangDropdown();
        });

        // ── Mobile Menu ──
        let isMobileOpen = false;

        function setMobileMenuIcons(open) {
            const hamburger = document.getElementById('hamburger-icon');
            const close = document.getElementById('close-icon');
            if (hamburger) {
                hamburger.classList.toggle('opacity-0', open);
                hamburger.classList.toggle('pointer-events-none', open);
            }
            if (close) {
                close.classList.toggle('opacity-0', !open);
                close.classList.toggle('pointer-events-none', !open);
            }
        }

        function toggleMobileMenu(forceClose) {
            const menu = document.getElementById('mobile-menu');
            const navEl = document.getElementById('main-nav');
            const btn = document.getElementById('mobile-menu-btn');
            if (!menu) return;

            isMobileOpen = forceClose === true ? false : !isMobileOpen;

            menu.classList.toggle('is-open', isMobileOpen);
            menu.setAttribute('aria-hidden', isMobileOpen ? 'false' : 'true');
            navEl?.classList.toggle('mobile-menu-open', isMobileOpen);
            btn?.setAttribute('aria-expanded', isMobileOpen ? 'true' : 'false');
            document.body.classList.toggle('overflow-hidden', isMobileOpen);
            if (window.lenis) {
                if (isMobileOpen) {
                    window.lenis.stop();
                } else {
                    window.lenis.start();
                }
            }

            setMobileMenuIcons(isMobileOpen);
            updateNavScrollState();
        }

        function closeMobileMenu() {
            if (isMobileOpen) toggleMobileMenu(true);
        }

        // ── Nav scroll — glass on scroll + when mobile menu open ──
        const nav = document.getElementById('main-nav');
        const heroSection = document.getElementById('discover');
        if (nav && heroSection) {
            nav.classList.add('nav-over-hero');
        }

        let lastScrollY = window.scrollY;
        let ticking = false;

        function updateNavScrollState() {
            if (!nav) return;
            const currentScrollY = window.scrollY;
            
            // Glass effect
            const shouldGlass = currentScrollY > 20 || isMobileOpen;
            nav.classList.toggle('is-scrolled', shouldGlass);

            // Handle scroll direction and hide/show
            if (currentScrollY <= 10) {
                // At the top: always show
                nav.classList.remove('-translate-y-[150%]', 'opacity-0', 'pointer-events-none');
            } else {
                if (currentScrollY > lastScrollY && !isMobileOpen) {
                    // Scrolling down: hide
                    nav.classList.add('-translate-y-[150%]', 'opacity-0', 'pointer-events-none');
                } else if (currentScrollY < lastScrollY || isMobileOpen) {
                    // Scrolling up: show
                    nav.classList.remove('-translate-y-[150%]', 'opacity-0', 'pointer-events-none');
                }
            }
            
            lastScrollY = currentScrollY;
            ticking = false;
        }

        if (nav) {
            updateNavScrollState();
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        updateNavScrollState();
                    });
                    ticking = true;
                }
            }, { passive: true });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isMobileOpen) closeMobileMenu();
        });

        // ── Main Init: GSAP Reveal System ──
        document.addEventListener('DOMContentLoaded', () => {
            const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            // ── GSAP Scroll Reveal System ──
            if (!reduceMotion && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);

                // Create a scoped context for cleanup
                const ctx = gsap.context(() => {

                    // Batch-reveal all .reveal elements per section
                    const revealEls = gsap.utils.toArray('.reveal');
                    if (revealEls.length) {
                        // Set initial state via GSAP (not CSS) to avoid conflicts
                        gsap.set(revealEls, { opacity: 0, y: 24 });

                        ScrollTrigger.batch(revealEls, {
                            start: 'top 88%',
                            onEnter: (batch) => {
                                gsap.to(batch, {
                                    opacity: 1,
                                    y: 0,
                                    duration: 0.7,
                                    ease: 'power3.out',
                                    stagger: 0.08,
                                    overwrite: true,
                                    clearProps: 'will-change'
                                });
                            },
                            once: true // Only trigger once — no reverse
                        });
                    }

                }); // end gsap.context

                // Store context for potential cleanup
                window.__gsapLandingCtx = ctx;
            } else if (reduceMotion) {
                document.querySelectorAll('.reveal').forEach((el) => {
                    el.style.opacity = '1';
                    el.style.transform = 'none';
                });
            }
        });
    </script>
    {{-- Luxury Toast Container --}}
    <div id="luxury-toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[1000] pointer-events-none opacity-0 translate-y-4">
        <div class="glass-panel flex items-center gap-4 px-6 py-4 rounded-2xl border border-white/20 dark:border-white/10 shadow-2xl shadow-black/20 rtl:flex-row-reverse">
            <div id="toast-icon-container" class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                <img id="toast-icon" src="{{ asset('images/logo.webp') }}" alt="alidebo" class="w-6 h-6 object-contain">
            </div>
            <div class="flex flex-col">
                <span id="toast-title" class="text-xs font-bold uppercase tracking-widest text-primary/80 mb-0.5 leading-none">{{ __('landing.coming_soon') }}</span>
                <span id="toast-msg" class="text-sm font-bold text-slate-900 dark:text-white leading-tight min-w-[140px]">{{ __('landing.roadmap_2') }}</span>
            </div>
        </div>
    </div>

    <script>
        // Global Luxury Toast Function
        window.showToast = function(title, message, icon = null) {
            const toast = document.getElementById('luxury-toast');
            const titleEl = document.getElementById('toast-title');
            const msgEl = document.getElementById('toast-msg');
            const iconEl = document.getElementById('toast-icon');
            const iconContainer = document.getElementById('toast-icon-container');

            if (!toast || !titleEl || !msgEl || typeof gsap === 'undefined') return;

            // Update Content
            titleEl.innerText = title;
            msgEl.innerText = message;

            if (icon === false || icon === 'hide') {
                if (iconContainer) iconContainer.style.display = 'none';
            } else {
                if (iconContainer) iconContainer.style.display = 'flex';
                if (icon && iconEl) iconEl.src = icon;
            }

            // Kill any active animations
            gsap.killTweensOf(toast);

            const tl = gsap.timeline();
            tl.to(toast, {
                opacity: 1,
                y: 0,
                duration: 0.6,
                ease: 'back.out(1.7)',
                pointerEvents: 'auto'
            })
            .to(toast, {
                opacity: 0,
                y: 10,
                duration: 0.4,
                ease: 'power2.in',
                delay: 3.5,
                pointerEvents: 'none'
            });
        };

        window.showComingSoon = function() {
            showToast("{{ __('landing.coming_soon') }}", "{{ __('landing.roadmap_2') }}");
        };
    </script>

    <!-- Lenis Smooth Scroll -->
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const lenis = new Lenis({
                duration: 1.2,
                smoothTouch: false,
            });

            // If GSAP is available, use its ticker for Lenis to prevent ScrollTrigger jitter
            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                lenis.on('scroll', ScrollTrigger.update);
                gsap.ticker.add((time) => {
                    lenis.raf(time * 1000);
                });
                gsap.ticker.lagSmoothing(0);
            } else {
                // Standalone requestAnimationFrame loop
                function raf(time) {
                    lenis.raf(time);
                    requestAnimationFrame(raf);
                }
                requestAnimationFrame(raf);
            }

            // Expose globally
            window.lenis = lenis;
        });
    </script>

    @stack('scripts')
    <x-chatbot />
    <x-scroll-to-top />
</body>

</html>