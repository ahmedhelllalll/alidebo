<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin.dashboard')) | {{ config('app.name', 'alidebo') }} {{ __('admin.admin_role') }}</title>
    {{-- Instant theme application (prevents FOUC) --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        // Dynamic Base Configuration for AJAX / Fetch
        window.AppConfig = {
            baseUrl: '{{ url('/') }}',
            adminUrl: '{{ url('admin') }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    {{-- Fonts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dashboard.js'])
    {{-- GSAP --}}
    <style>
        /* =============================================
            FOUNDATION
            ============================================= */
        [dir="rtl"] {
            font-family: 'Cairo', sans-serif;
            letter-spacing: normal !important;
        }
        [dir="rtl"] * {
            letter-spacing: normal !important;
        }
        [dir="ltr"] {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(161, 161, 170, 0.25);
            border-radius: 10px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: rgba(82, 82, 91, 0.35);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(161, 161, 170, 0.4);
        }
        /* Loading global overlay on buttons */
        .btn-loading {
            pointer-events: none !important;
            color: transparent !important;
            position: relative !important;
            cursor: default !important;
        }
        .btn-loader {
            position: absolute !important;
            inset: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 10 !important;
            color: white !important;
        }
        /* Dark mode adjustment for white backgrounds */
        .bg-white.btn-loading .btn-loader,
        [class*="bg-slate"].btn-loading .btn-loader,
        [class*="bg-zinc-100"].btn-loading .btn-loader,
        [class*="bg-gray-100"].btn-loading .btn-loader {
            color: #f45018 !important;
        }
        /* =============================================
            SIDEBAR — CSS-Driven Transitions
            ============================================= */
        #admin-sidebar {
            width: 260px;
            transition: width 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: width;
        }
        /* Collapsed Desktop State */
        @media (min-width: 1024px) {
            #admin-sidebar[data-collapsed="true"] {
                width: 76px;
            }
        }
        /* Sidebar text elements — smooth fade */
        .sidebar-text {
            transition: opacity 0.2s ease, max-width 0.25s ease;
            max-width: 200px;
            opacity: 1;
            overflow: hidden;
            white-space: nowrap;
        }
        @media (min-width: 1024px) {
            #admin-sidebar[data-collapsed="true"] .sidebar-text {
                opacity: 0;
                max-width: 0;
                pointer-events: none;
            }
        }
        /* Section label specifically */
        .sidebar-section-label {
            transition: opacity 0.15s ease, height 0.2s ease, margin 0.2s ease;
            overflow: hidden;
        }
        @media (min-width: 1024px) {
            #admin-sidebar[data-collapsed="true"] .sidebar-section-label {
                opacity: 0;
                height: 0;
                margin: 0;
                pointer-events: none;
            }
        }
        /* Nav items — center icons when collapsed */
        .sidebar-nav-item {
            transition: padding 0.25s ease, justify-content 0.25s ease;
        }
        @media (min-width: 1024px) {
            #admin-sidebar[data-collapsed="true"] .sidebar-nav-item {
                padding-inline-start: 0;
                padding-inline-end: 0;
                justify-content: center;
                gap: 0;
            }
            #admin-sidebar[data-collapsed="true"] .sidebar-dropdown-body {
                padding-left: 0;
                padding-right: 0;
            }
            #admin-sidebar[data-collapsed="true"] [x-collapse] {
                height: auto !important;
                overflow: visible !important;
                display: block !important;
            }
            #admin-sidebar[data-collapsed="true"] .sidebar-dropdown-icon {
                display: none !important;
            }
            #admin-sidebar[data-collapsed="true"] .sidebar-logo-area {
                padding-inline: 0;
                justify-content: center;
            }
            #admin-sidebar[data-collapsed="true"] .sidebar-logo-link {
                display: none;
            }
        }
        /* =============================================
            COLLAPSED TOOLTIPS (CSS-Only)
            ============================================= */
        .sidebar-tooltip {
            display: none !important;
        }
        /* =============================================
            NAV ITEM ACTIVE INDICATOR
            ============================================= */
        .sidebar-nav-item .nav-active-bar {
            position: absolute;
            inset-inline-start: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 0;
            border-radius: 0 3px 3px 0;
            background: #f45018;
            transition: height 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        [dir="rtl"] .sidebar-nav-item .nav-active-bar {
            border-radius: 3px 0 0 3px;
        }
        .sidebar-nav-item.nav-active .nav-active-bar {
            height: 20px;
        }
        /* =============================================
            MOBILE SIDEBAR
            ============================================= */
        @media (max-width: 1023px) {
            #admin-sidebar {
                position: fixed;
                inset-block: 0;
                inset-inline-start: 0;
                z-index: 50;
                width: 280px !important;
                transform: translateX(-100%);
                transition: transform 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            [dir="rtl"] #admin-sidebar {
                transform: translateX(100%);
            }
            #admin-sidebar.mobile-open {
                transform: translateX(0) !important;
            }
            /* Show all text when mobile sidebar is open */
            #admin-sidebar.mobile-open .sidebar-text {
                opacity: 1 !important;
                max-width: 200px !important;
                pointer-events: auto !important;
            }
            #admin-sidebar.mobile-open .sidebar-section-label {
                opacity: 1 !important;
                height: auto !important;
                margin-bottom: 0.75rem !important;
                pointer-events: auto !important;
            }
        }
        /* Mobile floating toggle button */
        @media (max-width: 1023px) {
            #mobile-toggle-fab {
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 45;
                display: flex !important;
            }
            [dir="rtl"] #mobile-toggle-fab {
                left: auto;
                right: 1rem;
            }
        }
        @media (min-width: 1024px) {
            #mobile-toggle-fab {
                display: none !important;
            }
        }
        /* Mobile overlay */
        #sidebar-overlay {
            transition: opacity 0.3s ease;
        }
        /* =============================================
            TOPBAR
            ============================================= */
        .topbar-action {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.15s ease;
        }
        .topbar-action:hover {
            transform: scale(1.05);
        }
        .topbar-action:active {
            transform: scale(0.97);
        }
        /* =============================================
            USER SECTION — Collapsed state
            ============================================= */
        .sidebar-user-section {
            transition: padding 0.25s ease;
        }
        @media (min-width: 1024px) {
            #admin-sidebar[data-collapsed="true"] .sidebar-user-section {
                padding-inline: 0;
                justify-content: center;
            }
            #admin-sidebar[data-collapsed="true"] .sidebar-user-details,
            #admin-sidebar[data-collapsed="true"] .sidebar-user-logout {
                opacity: 0;
                max-width: 0;
                overflow: hidden;
                pointer-events: none;
                transition: opacity 0.15s ease, max-width 0.2s ease;
            }
        }
        .sidebar-user-details,
        .sidebar-user-logout {
            transition: opacity 0.2s ease 0.05s, max-width 0.25s ease;
        }
        /* Sidebar toggle icon morph */
        .toggle-sidebar-btn {
            transition: background-color 0.2s ease, transform 0.15s ease;
        }
        .toggle-sidebar-btn:hover {
            transform: scale(1.08);
        }
        .toggle-sidebar-btn:active {
            transform: scale(0.95);
        }
        /* =============================================
            PREMIUM INPUT STYLES
            ============================================= */
        .input-premium {
            background: rgba(248, 250, 252, 0.5);
            dark: background: rgba(9, 9, 11, 0.5);
            border-color: rgba(226, 232, 240, 1);
            dark: border-color: rgba(39, 39, 42, 1);
            transition: all 0.3s ease;
        }
        .dark .input-premium {
            background: rgba(9, 9, 11, 0.5);
            border-color: rgba(39, 39, 42, 1);
        }
        .input-premium:focus {
            ring-width: 4px;
            ring-color: rgba(244, 80, 24, 0.1);
            border-color: #f45018;
            background: white;
        }
        .dark .input-premium:focus {
            background: #09090b;
        }
        /* Consistent form spacing */
        .form-group {
            margin-bottom: 1.75rem;
        }
        .form-label {
            display: block;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(148, 163, 184, 1);
            dark: color: rgba(161, 161, 170, 1);
            margin-bottom: 0.625rem;
        }
        .dark .form-label {
            color: rgba(161, 161, 170, 1);
        }
        /* Focus states for all inputs */
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            transition: all 0.3s ease;
        }
        /* Placeholder styling */
        input::placeholder,
        textarea::placeholder {
            color: rgba(148, 163, 184, 0.8);
            dark: color: rgba(161, 161, 170, 0.8);
            transition: opacity 0.3s ease;
        }
        .dark input::placeholder,
        .dark textarea::placeholder {
            color: rgba(161, 161, 170, 0.8);
        }
        input:focus::placeholder,
        textarea:focus::placeholder {
            opacity: 0.5;
        }
    </style>
</head>
<body
    class="rtl:font-cairo ltr:font-jakarta bg-slate-50 dark:bg-[#09090b] text-slate-900 dark:text-white transition-colors duration-300 antialiased overflow-hidden flex h-screen selection:bg-primary/30 selection:text-primary-dark dark:selection:text-primary-light">
    {{-- ============================================
    MOBILE OVERLAY
    ============================================ --}}
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] z-40 opacity-0 pointer-events-none lg:hidden"
        onclick="closeMobileSidebar()">
    </div>
    {{-- Mobile Floating Toggle Removed - Now in Topbar --}}
    {{-- ============================================
    SIDEBAR
    ============================================ --}}
    <aside id="admin-sidebar" data-collapsed="false"
        class="flex flex-col w-[260px] shrink-0 bg-white dark:bg-[#09090b]/80 backdrop-blur-md border-e border-slate-200/50 dark:border-white/5 shadow-[4px_0_24px_rgba(0,0,0,0.02)] dark:shadow-none z-50 lg:z-auto transition-all duration-300">
        {{-- ── Logo Area ── --}}
        <div
            class="sidebar-logo-area h-16 flex items-center px-4 sm:px-5 border-b border-slate-100 dark:border-zinc-800/60 shrink-0 gap-3 justify-between transition-all duration-300">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo-link flex items-center gap-2.5 min-w-0 group">
                <div class="relative flex-shrink-0">
                    <img src="{{ asset('images/logo.webp') }}" alt="alidebo"
                        class="w-8 h-8 object-contain group-hover:scale-110 transition-transform duration-200 origin-center">
                </div>
                <span
                    class="sidebar-text text-lg font-[900] ltr:tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">
                    alidebo
                </span>
            </a>
            {{-- Unified Sidebar Toggle Button --}}
            <button id="sidebar-toggle-btn" onclick="toggleSidebar()"
                class="toggle-sidebar-btn flex items-center justify-center w-10 h-10 rounded-xl text-slate-400 hover:text-slate-600 dark:text-zinc-500 dark:hover:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800/50 transition-colors"
                aria-label="Toggle sidebar">
                <svg id="sidebar-toggle-icon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path id="sidebar-toggle-path" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
        {{-- ── Navigation ── --}}
                        <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-5 space-y-2">
            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group
                {{ request()->routeIs('admin.dashboard') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                <span class="nav-active-bar"></span>
                <svg class="w-[20px] h-[20px] shrink-0 transition-transform duration-200 group-hover:scale-110"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="sidebar-text">{{ __('admin.dashboard') }}</span>
                <span class="sidebar-tooltip">{{ __('admin.dashboard') }}</span>
            </a>

            @php
                $isDirectoryActive = request()->routeIs('admin.businesses.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.locations.*');
                $isContentActive = request()->routeIs('admin.pages.*') || request()->routeIs('admin.seo.*') || (request()->routeIs('admin.coming-soon') && request()->query('feature') === 'blogs');
                $isCrmActive = request()->routeIs('admin.leads.*') || request()->routeIs('admin.support-chats.*');
                $isSystemActive = request()->routeIs('admin.users.*') || request()->routeIs('admin.backups.*');
            @endphp

            {{-- Modules Section --}}
            <div class="mt-6 mb-2 px-4 sidebar-text">
                <span class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('admin.modules') }}</span>
            </div>
            
            <div class="space-y-1 mt-1">
                    <a href="{{ route('admin.businesses.index') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.businesses.index') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar"></span>
                        <svg class="w-[20px] h-[20px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="sidebar-text">{{ __('admin.all_businesses') }}</span>
                        <span class="sidebar-tooltip">{{ __('admin.all_businesses') }}</span>
                    </a>
                    <a href="{{ route('admin.businesses.create') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.businesses.create') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar"></span>
                        <div class="w-[20px] h-[20px] shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-plus text-[14px] transition-transform duration-200 group-hover:scale-110"></i>
                        </div>
                        <span class="sidebar-text">{{ __('admin.add_new') }}</span>
                        <span class="sidebar-tooltip">{{ __('admin.add_new') }}</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.categories.*') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar"></span>
                        <svg class="w-[20px] h-[20px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="sidebar-text">{{ __('admin.categories') }}</span>
                        <span class="sidebar-tooltip">{{ __('admin.categories') }}</span>
                    </a>
                    <a href="{{ route('admin.locations.index') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.locations.*') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar"></span>
                        <i class="fa-solid fa-earth-americas w-[20px] shrink-0 text-center text-lg transition-transform duration-200 group-hover:scale-110"></i>
                        <span class="sidebar-text">{{ __('admin.locations') }}</span>
                        <span class="sidebar-tooltip">{{ __('admin.locations') }}</span>
                    </a>
                </div>

            {{-- Content & SEO Section --}}
            <div class="mt-6 mb-2 px-4 sidebar-text">
                <span class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('admin.content_seo') }}</span>
            </div>
            
            <div class="space-y-1 mt-1">
                    <a href="{{ route('admin.pages.index') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.pages.*') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar"></span>
                        <i class="fa-solid fa-file-lines w-[20px] shrink-0 text-center text-lg transition-transform duration-200 group-hover:scale-110"></i>
                        <span class="sidebar-text">{{ __('admin.pages') }}</span>
                        <span class="sidebar-tooltip">{{ __('admin.pages') }}</span>
                    </a>
                    <a href="{{ route('admin.dashboard.seo') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.dashboard.seo*') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar"></span>
                        <i class="fa-solid fa-magnifying-glass-chart w-[20px] shrink-0 text-center text-lg transition-transform duration-200 group-hover:scale-110"></i>
                        <span class="sidebar-text">{{ __('admin.seo_settings') }}</span>
                        <span class="sidebar-tooltip">{{ __('admin.seo_settings') }}</span>
                    </a>
                    <a href="{{ route('admin.coming-soon', ['feature' => 'blogs']) }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.coming-soon') && request()->query('feature') === 'blogs' ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar {{ request()->routeIs('admin.coming-soon') && request()->query('feature') === 'blogs' ? '' : 'hidden' }}"></span>
                        <i class="fa-solid fa-blog w-[20px] shrink-0 text-center text-lg transition-transform duration-200 group-hover:scale-110"></i>
                        <div class="flex-1 flex items-center justify-between sidebar-text">
                            <span>{{ __('admin.blogs') }}</span>
                            <span class="font-bold uppercase tracking-widest bg-orange-500/10 text-orange-500 px-1.5 py-0.5 rounded shadow-sm" style="font-size: 9px;">{{ __('admin.soon') }}</span>
                        </div>
                        <span class="sidebar-tooltip">{{ __('admin.blogs') }}</span>
                    </a>
                </div>

            {{-- CRM & Support Section --}}
            @php
                $unreadLeadsCount = \App\Models\ContactMessage::where('status', '!=', 'read')->orWhereNull('status')->count();
            @endphp
            <div class="mt-6 mb-2 px-4 sidebar-text flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('admin.crm_support') }}</span>
            </div>
            
            <div class="space-y-1 mt-1">
                    <a href="{{ route('admin.leads.index') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.leads.*') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar {{ request()->routeIs('admin.leads.*') ? '' : 'hidden' }}"></span>
                        <div class="relative shrink-0 w-[20px]">
                            <i class="fa-solid fa-envelope-open-text text-center text-lg transition-transform duration-200 group-hover:scale-110"></i>
                            @if($unreadLeadsCount > 0)
                                <span class="absolute -top-1 -end-1 w-2.5 h-2.5 bg-orange-500 rounded-full border-[1.5px] border-white dark:border-[#09090b] lg:hidden lg:group-has-[[data-collapsed="true"]]:block" style="display: var(--collapsed-dot-display, none);"></span>
                            @endif
                        </div>
                        <div class="flex-1 flex items-center justify-between sidebar-text">
                            <span>{{ __('admin.leads') }}</span>
                            @if($unreadLeadsCount > 0)
                                <span class="flex items-center justify-center min-w-[20px] h-[20px] px-1 text-[10px] font-black bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded-lg shadow-sm border border-orange-500/20">{{ $unreadLeadsCount > 99 ? '99+' : $unreadLeadsCount }}</span>
                            @endif
                        </div>
                        <span class="sidebar-tooltip">{{ __('admin.leads') }} @if($unreadLeadsCount > 0) ({{ $unreadLeadsCount }}) @endif</span>
                    </a>
                    <a href="{{ route('admin.support-chats.index') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.support-chats.*') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar {{ request()->routeIs('admin.support-chats.*') ? '' : 'hidden' }}"></span>
                        <i class="fa-solid fa-comments w-[20px] shrink-0 text-center text-lg transition-transform duration-200 group-hover:scale-110"></i>
                        <div class="flex-1 flex items-center justify-between sidebar-text">
                            <span>{{ __('admin.support_chats') }}</span>
                        </div>
                        <span class="sidebar-tooltip">{{ __('admin.support_chats') }}</span>
                    </a>
                </div>

            {{-- System Section --}}
            <div class="mt-6 mb-2 px-4 sidebar-text">
                <span class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('admin.system') }}</span>
            </div>
            
            <div class="space-y-1 mt-1">
                    <a href="{{ route('admin.users.index') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.users.*') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar"></span>
                        <svg class="w-[20px] h-[20px] shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="sidebar-text">{{ __('admin.users') }}</span>
                        <span class="sidebar-tooltip">{{ __('admin.users') }}</span>
                    </a>
                    <a href="{{ route('admin.backups.index') }}"
                        class="sidebar-nav-item relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-colors group {{ request()->routeIs('admin.backups.*') ? 'nav-active bg-primary/[0.07] text-primary dark:text-primary-light' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800/40 hover:text-slate-700 dark:hover:text-zinc-200' }}">
                        <span class="nav-active-bar"></span>
                        <i class="fa-solid fa-cloud-arrow-up w-[20px] shrink-0 text-center text-lg transition-transform duration-200 group-hover:scale-110"></i>
                        <div class="flex-1 flex items-center justify-between sidebar-text">
                            <span>{{ __('admin.backups') }}</span>
                        </div>
                        <span class="sidebar-tooltip">{{ __('admin.backups') }}</span>
                    </a>
                </div>        </nav>

        {{-- ── User Section (Bottom) ── --}}
        <div class="border-t border-slate-100 dark:border-zinc-800/60 p-4">
            <div class="sidebar-user-section flex items-center gap-3 px-2 py-2 rounded-2xl hover:bg-slate-50 dark:hover:bg-zinc-800/30 transition-all duration-300 relative group cursor-pointer border border-transparent hover:border-slate-200/50 dark:hover:border-white/5 shadow-sm hover:shadow-md">
                {{-- Premium Avatar --}}
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-primary to-orange-400 p-[1.5px] shadow-lg shadow-primary/20 group-hover:rotate-3 transition-transform duration-500">
                        <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center overflow-hidden">
                            <i class="fa-solid fa-user text-primary text-sm"></i>
                        </div>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-white dark:bg-[#09090b] flex items-center justify-center shadow-sm">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse border-2 border-white dark:border-[#09090b]"></div>
                    </div>
                </div>
                {{-- User Details --}}
                <div class="sidebar-user-details min-w-0 flex-1">
                    <p class="text-[13px] font-[900] text-slate-800 dark:text-zinc-100 truncate leading-tight ltr:tracking-tight">
                        {{ auth()->user()->name ?? __('admin.administrator') }}
                    </p>
                    <p class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 truncate mt-0.5 uppercase ltr:tracking-widest opacity-70">
                        {{ auth()->user()->role ?? __('admin.admin_role') }}
                    </p>
                </div>
                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="sidebar-user-logout shrink-0">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-center w-8 h-8 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all duration-300 transform hover:scale-110"
                        title="{{ __('admin.logout') }}">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </button>
                </form>
                {{-- Collapsed tooltip --}}
                <span class="sidebar-tooltip">{{ auth()->user()->name ?? __('admin.admin_role') }}</span>
            </div>
        </div>
    </aside>
    {{-- ============================================
    MAIN CONTENT WINDOW
    ============================================ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
        {{-- ── Topbar ── --}}
        <header
            class="w-full h-16 bg-white/60 dark:bg-[#09090b]/70 backdrop-blur-md border-b border-slate-200/50 dark:border-white/5 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-30 shrink-0 shadow-[0_4px_30px_rgba(0,0,0,0.03)] dark:shadow-none transition-colors duration-300">
            {{-- Left: Mobile Toggle & Search --}}
            <div class="flex items-center gap-3 lg:gap-5">
                {{-- Mobile Hamburger --}}
                <button onclick="toggleSidebar()" 
                    class="lg:hidden flex items-center justify-center w-10 h-10 rounded-[14px] bg-slate-100/60 dark:bg-zinc-800/60 border border-slate-200/60 dark:border-white/5 text-slate-600 dark:text-zinc-400 hover:text-primary dark:hover:text-primary-light hover:bg-white dark:hover:bg-zinc-800 hover:shadow-sm active:scale-95 transition-all duration-300"
                    aria-label="Toggle sidebar">
                    <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                {{-- Global Search / Command Palette --}}
                <div class="hidden sm:flex relative items-center group/search" id="global-search-wrapper">
                    <div class="absolute inset-y-0 start-3.5 flex items-center pointer-events-none z-10">
                        <svg class="w-4 h-4 text-slate-400 dark:text-zinc-500 group-focus-within/search:text-primary transition-all duration-300 group-focus-within/search:scale-110"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="global-search-input" placeholder="{{ __('admin.type_to_search') }}" autocomplete="off"
                        class="w-24 xs:w-44 sm:w-64 lg:w-80 xl:w-96 bg-slate-100/50 dark:bg-zinc-800/40 border border-slate-200/60 dark:border-white/5 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary/40 focus:ring-8 focus:ring-primary/5 rounded-[18px] text-[13px] font-[600] py-2.5 ps-10 pe-16 transition-all text-slate-900 dark:text-white placeholder:text-slate-400/80 dark:placeholder:text-zinc-500 shadow-inner group-hover/search:shadow-md">
                    {{-- Shortcut Badge --}}
                    <div class="absolute inset-y-0 end-3 flex items-center pointer-events-none">
                        <div class="hidden lg:flex items-center gap-1.5 px-2 py-1 text-[10px] font-black text-slate-400 dark:text-zinc-500 bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-white/5 rounded-lg shadow-sm">
                            <span class="text-[9px] opacity-60">CTRL</span>
                            <span class="text-primary/70">K</span>
                        </div>
                    </div>
                    {{-- Search Results Dropdown --}}
                    <div id="global-search-results" 
                        class="absolute top-full mt-4 start-0 w-[400px] lg:w-[500px] bg-white/95 dark:bg-[#121214]/95 backdrop-blur-md border border-slate-200/60 dark:border-white/[0.08] rounded-3xl shadow-[0_30px_70px_rgba(0,0,0,0.18)] dark:shadow-[0_30px_70px_rgba(0,0,0,0.6)] opacity-0 pointer-events-none transform translate-y-[-10px] transition-all duration-500 z-[100] overflow-hidden">
                        <div id="search-results-content" class="max-h-[520px] overflow-y-auto custom-scrollbar p-3 min-h-[120px]">
                            {{-- Content injected via JS --}}
                        </div>
                        {{-- Search Footer --}}
                        <div class="px-5 py-3.5 bg-slate-50/50 dark:bg-white/[0.02] border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                            <div class="flex items-center gap-5">
                                <div class="flex items-center gap-2">
                                    <kbd class="flex items-center justify-center w-5 h-5 text-[10px] font-black bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-md text-slate-500 shadow-sm">↑</kbd>
                                    <kbd class="flex items-center justify-center w-5 h-5 text-[10px] font-black bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-md text-slate-500 shadow-sm">↓</kbd>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-tight">{{ __('admin.navigate') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <kbd class="flex items-center justify-center h-5 px-1.5 text-[10px] font-black bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-md text-slate-500 shadow-sm">↵</kbd>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-tight">{{ __('admin.select') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-primary/60">
                                <i class="fa-solid fa-bolt-lightning text-xs animate-pulse"></i>
                                <span class="text-[11px] font-black uppercase tracking-[0.2em]">{{ __('admin.global_search') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Right: Actions --}}
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Theme Switcher --}}
                <button onclick="toggleTheme()"
                    class="topbar-action group/theme text-slate-400 hover:text-primary hover:bg-primary/[0.06] dark:text-zinc-500 dark:hover:text-primary-light dark:hover:bg-primary/[0.08] outline-none rounded-2xl p-2.5 transition-all duration-300"
                    aria-label="Toggle theme">
                    <svg class="w-5 h-5 dark:hidden group-hover/theme:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block group-hover/theme:rotate-[30deg] transition-transform duration-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
                {{-- Language Switcher Component --}}
                <div class="relative" id="lang-dropdown-wrapper">
                    <button onclick="toggleDropdown('lang-dropdown')" class="flex items-center gap-2.5 px-3.5 py-1.5 rounded-2xl border border-black/5 dark:border-white/[0.04] text-[13px] font-[900] text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all duration-300 hover:shadow-sm">
                        @if(app()->getLocale() == 'en')
                            <img src="https://flagcdn.com/w40/us.png" alt="EN" class="w-[20px] h-[14px] rounded-sm object-cover shadow-sm ring-1 ring-black/5">
                            <span class="hidden sm:inline-block">EN</span>
                        @elseif(app()->getLocale() == 'ar')
                            <img src="https://flagcdn.com/w40/eg.png" alt="AR" class="w-[20px] h-[14px] rounded-sm object-cover shadow-sm ring-1 ring-black/5">
                            <span class="hidden sm:inline-block">AR</span>
                        @elseif(app()->getLocale() == 'es')
                            <img src="https://flagcdn.com/w40/es.png" alt="ES" class="w-[20px] h-[14px] rounded-sm object-cover shadow-sm ring-1 ring-black/5">
                            <span class="hidden sm:inline-block">ES</span>
                        @elseif(app()->getLocale() == 'de')
                            <img src="https://flagcdn.com/w40/de.png" alt="DE" class="w-[20px] h-[14px] rounded-sm object-cover shadow-sm ring-1 ring-black/5">
                            <span class="hidden sm:inline-block">DE</span>
                        @elseif(app()->getLocale() == 'zh')
                            <img src="https://flagcdn.com/w40/cn.png" alt="ZH" class="w-[20px] h-[14px] rounded-sm object-cover shadow-sm ring-1 ring-black/5">
                            <span class="hidden sm:inline-block">ZH</span>
                        @elseif(app()->getLocale() == 'tr')
                            <img src="https://flagcdn.com/w40/tr.png" alt="TR" class="w-[20px] h-[14px] rounded-sm object-cover shadow-sm ring-1 ring-black/5">
                            <span class="hidden sm:inline-block">TR</span>
                        @else
                            <span class="uppercase">{{ app()->getLocale() }}</span>
                        @endif
                        <svg class="w-3.5 h-3.5 text-zinc-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    {{-- Premium Language Dropdown --}}
                    <div id="lang-dropdown" class="absolute hidden top-full mt-4 end-0 w-44 bg-white/95 dark:bg-[#121214]/95 backdrop-blur-md border border-black/5 dark:border-white/[0.04] rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.5)] z-50 py-1.5 overflow-hidden origin-top-right">
                        <a href="{{ route('lang.switch', 'en') }}" class="flex items-center justify-between px-4 py-2.5 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ app()->getLocale() == 'en' ? 'text-primary font-bold bg-primary/5 dark:bg-primary/10' : 'text-zinc-700 dark:text-zinc-300 font-medium' }}">
                            <div class="flex items-center gap-3">
                                <img src="https://flagcdn.com/w40/us.png" alt="US" class="w-[18px] h-[13px] rounded-sm object-cover">
                                <span>English</span>
                            </div>
                            @if(app()->getLocale() == 'en')
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('lang.switch', 'ar') }}" class="flex items-center justify-between px-4 py-2.5 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ app()->getLocale() == 'ar' ? 'text-primary font-bold bg-primary/5 dark:bg-primary/10' : 'text-zinc-700 dark:text-zinc-300 font-medium' }}">
                            <div class="flex items-center gap-3">
                                <img src="https://flagcdn.com/w40/eg.png" alt="EG" class="w-[18px] h-[13px] rounded-sm object-cover">
                                <span class="font-cairo">العربية</span>
                            </div>
                            @if(app()->getLocale() == 'ar')
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('lang.switch', 'es') }}" class="flex items-center justify-between px-4 py-2.5 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ app()->getLocale() == 'es' ? 'text-primary font-bold bg-primary/5 dark:bg-primary/10' : 'text-zinc-700 dark:text-zinc-300 font-medium' }}">
                            <div class="flex items-center gap-3">
                                <img src="https://flagcdn.com/w40/es.png" alt="ES" class="w-[18px] h-[13px] rounded-sm object-cover">
                                <span>Español</span>
                            </div>
                            @if(app()->getLocale() == 'es')
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('lang.switch', 'de') }}" class="flex items-center justify-between px-4 py-2.5 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ app()->getLocale() == 'de' ? 'text-primary font-bold bg-primary/5 dark:bg-primary/10' : 'text-zinc-700 dark:text-zinc-300 font-medium' }}">
                            <div class="flex items-center gap-3">
                                <img src="https://flagcdn.com/w40/de.png" alt="DE" class="w-[18px] h-[13px] rounded-sm object-cover">
                                <span>Deutsch</span>
                            </div>
                            @if(app()->getLocale() == 'de')
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('lang.switch', 'zh') }}" class="flex items-center justify-between px-4 py-2.5 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ app()->getLocale() == 'zh' ? 'text-primary font-bold bg-primary/5 dark:bg-primary/10' : 'text-zinc-700 dark:text-zinc-300 font-medium' }}">
                            <div class="flex items-center gap-3">
                                <img src="https://flagcdn.com/w40/cn.png" alt="CN" class="w-[18px] h-[13px] rounded-sm object-cover">
                                <span>中文 (Chinese)</span>
                            </div>
                            @if(app()->getLocale() == 'zh')
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                        <a href="{{ route('lang.switch', 'tr') }}" class="flex items-center justify-between px-4 py-2.5 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ app()->getLocale() == 'tr' ? 'text-primary font-bold bg-primary/5 dark:bg-primary/10' : 'text-zinc-700 dark:text-zinc-300 font-medium' }}">
                            <div class="flex items-center gap-3">
                                <img src="https://flagcdn.com/w40/tr.png" alt="TR" class="w-[18px] h-[13px] rounded-sm object-cover">
                                <span>Türkçe (Turkish)</span>
                            </div>
                            @if(app()->getLocale() == 'tr')
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </a>
                    </div>
                </div>
                <div class="h-5 w-px bg-slate-200 dark:bg-zinc-800 mx-0.5 hidden sm:block"></div>
                {{-- Premium Profile Dropdown --}}
                <div class="relative" id="profile-dropdown-wrapper">
                    <button onclick="toggleDropdown('profile-dropdown')"
                        class="topbar-action group/profile text-slate-400 hover:text-primary hover:bg-primary/[0.06] dark:text-zinc-500 dark:hover:text-primary-light dark:hover:bg-primary/[0.08] outline-none rounded-2xl p-2.5 transition-all duration-300">
                        <i class="fa-solid fa-user-circle text-lg group-hover/profile:scale-110 transition-transform duration-300"></i>
                    </button>
                    <div id="profile-dropdown"
                        class="absolute hidden top-full mt-4 w-60 end-0 bg-white/95 dark:bg-[#121214]/95 backdrop-blur-md border border-slate-200/60 dark:border-white/[0.08] shadow-[0_20px_60px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.5)] rounded-3xl overflow-hidden z-50 transform origin-top-right">
                        <div class="p-5 bg-gradient-to-br from-slate-50/50 to-white/50 dark:from-white/[0.03] dark:to-transparent border-b border-slate-100 dark:border-white/5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-primary to-orange-400 p-[1.5px] shadow-lg shadow-primary/20">
                                    <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center overflow-hidden">
                                        <i class="fa-solid fa-user text-primary text-lg"></i>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[15px] font-[900] text-slate-900 dark:text-white truncate ltr:tracking-tight">
                                        {{ auth()->user()->name ?? __('admin.administrator') }}
                                    </p>
                                    <p class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 truncate mt-0.5 opacity-80">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-2.5">
                            <a href="{{ route('admin.profile') }}" class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-[13px] font-[800] text-slate-600 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-white/[0.04] hover:text-primary transition-all group/item">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-zinc-800/50 flex items-center justify-center text-slate-400 dark:text-zinc-500 group-hover/item:text-primary group-hover/item:bg-primary/10 transition-colors">
                                        <i class="fa-solid fa-user-circle text-sm"></i>
                                    </div>
                                    {{ __('admin.profile') }}
                                </div>
                                <i class="fa-solid fa-chevron-right text-[10px] opacity-0 group-hover/item:opacity-30 transition-all"></i>
                            </a>
                            <a href="{{ route('admin.settings') }}" class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-[13px] font-[800] text-slate-600 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-white/[0.04] hover:text-primary transition-all group/item">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-zinc-800/50 flex items-center justify-center text-slate-400 dark:text-zinc-500 group-hover/item:text-primary group-hover/item:bg-primary/10 transition-colors">
                                        <i class="fa-solid fa-gear text-sm"></i>
                                    </div>
                                    {{ __('admin.settings') }}
                                </div>
                                <i class="fa-solid fa-chevron-right text-[10px] opacity-0 group-hover/item:opacity-30 transition-all"></i>
                            </a>
                        </div>
                        <div class="p-2.5 pt-0">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 px-3.5 py-3 rounded-2xl text-[13px] font-[800] text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all group/logout">
                                    <div class="w-8 h-8 rounded-xl bg-red-50 dark:bg-red-500/5 flex items-center justify-center text-red-400 group-hover/logout:bg-red-500 group-hover/logout:text-white transition-all">
                                        <i class="fa-solid fa-right-from-bracket text-[13px]"></i>
                                    </div>
                                    {{ __('admin.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        {{-- ── Page Content ── --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 relative scroll-smooth" id="main-scroll">
            <div class="max-w-[1600px] mx-auto w-full">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <x-admin.alert type="success" :message="session('success')" />
                @endif
                @if(session('error'))
                    <x-admin.alert type="error" :message="session('error')" />
                @endif
                @if($errors->any())
                    <div
                        class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 rounded-xl reveal-item">
                        <div class="flex items-center gap-2 mb-2 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ __('admin.error') }}</span>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    {{-- ============================================
    GLOBAL TOOLTIP (Solves Clipping)
    ============================================ --}}
    <div id="dynamic-tooltip"
        class="fixed z-[99999] opacity-0 pointer-events-none transition-opacity duration-200 bg-white dark:bg-[#27272a] text-[#18181b] dark:text-[#fafafa] border border-black/5 dark:border-white/10 shadow-xl px-3 py-1.5 rounded-lg text-[13px] font-bold whitespace-nowrap">
    </div>
    {{-- ============================================
    TOAST CONTAINER
    ============================================ --}}
    <div id="admin-toast-container"
        class="fixed bottom-6 end-6 z-[1000] flex flex-col gap-3 pointer-events-none items-end"></div>
    {{-- ============================================
    JAVASCRIPT
    ============================================ --}}
    <script>
        // =============================================
        // GLOBAL FORM LOADING
        // =============================================
        function submitForm(formId, button) {
            const form = document.getElementById(formId);
            if (!form) return;
            // Check HTML5 validation
            if (!form.reportValidity()) return;
            // Add loading state & disable (pointer-events)
            button.classList.add('btn-loading');
            button.setAttribute('aria-disabled', 'true');
            // Add spinner if not exists
            if (!button.querySelector('.btn-loader')) {
                const loader = document.createElement('div');
                loader.className = 'btn-loader';
                loader.innerHTML = `
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;
                button.appendChild(loader);
            }
            // Visual feedback before submit
            setTimeout(() => {
                form.submit();
            }, 150);
        }
        // =============================================
        // THEME
        // =============================================
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
        // =============================================
        // SIDEBAR — Unified Toggle (Mobile & Desktop)
        // =============================================
        const SIDEBAR_KEY = 'admin_sidebar_collapsed';
        function initSidebarState() {
            const sidebar = document.getElementById('admin-sidebar');
            if (!sidebar) return;
            // Only apply persisted state on desktop
            if (window.innerWidth >= 1024) {
                const saved = localStorage.getItem(SIDEBAR_KEY);
                if (saved === 'true') {
                    sidebar.dataset.collapsed = 'true';
                    updateToggleIcon(true);
                }
            }
        }
        function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            if (!sidebar) return;
            
            // Check if we're on mobile or desktop
            if (window.innerWidth < 1024) {
                // Mobile: Toggle open/close
                if (sidebar.classList.contains('mobile-open')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                // Desktop: Toggle collapse/expand
                toggleDesktopSidebar();
            }
        }
        function toggleDesktopSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            if (!sidebar) return;
            const isCollapsed = sidebar.dataset.collapsed === 'true';
            const newState = !isCollapsed;
            sidebar.dataset.collapsed = String(newState);
            localStorage.setItem(SIDEBAR_KEY, String(newState));
            updateToggleIcon(newState);
        }
        function updateToggleIcon(isCollapsed) {
            const icon = document.getElementById('sidebar-toggle-icon');
            const path = document.getElementById('sidebar-toggle-path');
            const mobileIcon = document.getElementById('mobile-toggle-icon');
            const mobilePath = document.getElementById('mobile-toggle-path');
            
            // Check if mobile sidebar is open
            const sidebar = document.getElementById('admin-sidebar');
            const isMobileOpen = sidebar && sidebar.classList.contains('mobile-open');
            
            const iconPath = isMobileOpen ? 'M6 18L18 6M6 6l12 12' : 
                            (isCollapsed ? 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5' : 
                            'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12');
            
            // Update sidebar toggle icon
            if (path) {
                path.setAttribute('d', iconPath);
            }
            
            // Update mobile floating toggle icon
            if (mobilePath) {
                mobilePath.setAttribute('d', iconPath);
            }
        }
        // =============================================
        // SIDEBAR — Mobile Open/Close
        // =============================================
        function openMobileSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (!sidebar || !overlay) return;
            sidebar.classList.add('mobile-open');
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
            document.body.style.overflow = 'hidden';
            updateToggleIcon(false); // Update to X icon
        }
        function closeMobileSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (!sidebar || !overlay) return;
            sidebar.classList.remove('mobile-open');
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            document.body.style.overflow = '';
            updateToggleIcon(false); // Update to hamburger icon
        }
        // Reset mobile state on resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeMobileSidebar();
                // Update icon based on collapsed state
                const sidebar = document.getElementById('admin-sidebar');
                if (sidebar) {
                    updateToggleIcon(sidebar.dataset.collapsed === 'true');
                }
            }
        });
        // =============================================
        // DROPDOWNS
        // =============================================
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            if (!dropdown) return;
            const isHidden = dropdown.classList.contains('hidden');
            // Close all other dropdowns
            document.querySelectorAll('[id$="-dropdown"]').forEach(el => {
                if (el.id !== id && !el.classList.contains('hidden')) {
                    gsap.to(el, { 
                        opacity: 0, 
                        scale: 0.95, 
                        y: -12, 
                        duration: 0.2, 
                        ease: "power2.in", 
                        onComplete: () => el.classList.add('hidden') 
                    });
                }
            });
            if (isHidden) {
                dropdown.classList.remove('hidden');
                gsap.fromTo(dropdown, { 
                    opacity: 0, 
                    scale: 0.95, 
                    y: -12,
                    filter: "blur(4px)"
                }, { 
                    opacity: 1, 
                    scale: 1, 
                    y: 0, 
                    filter: "blur(0px)",
                    duration: 0.4, 
                    ease: "back.out(1.4)" 
                });
            } else {
                gsap.to(dropdown, { 
                    opacity: 0, 
                    scale: 0.95, 
                    y: -12, 
                    duration: 0.2, 
                    ease: "power2.in", 
                    onComplete: () => dropdown.classList.add('hidden') 
                });
            }
        }
        // Global outside-click to close dropdowns
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown-wrapper') && !e.target.closest('#lang-dropdown-wrapper') && !e.target.closest('#profile-dropdown-wrapper')) {
                document.querySelectorAll('[id$="-dropdown"]').forEach(dropdown => {
                    if (!dropdown.classList.contains('hidden')) {
                        gsap.to(dropdown, { opacity: 0, scale: 0.95, y: -8, duration: 0.15, onComplete: () => dropdown.classList.add('hidden') });
                    }
                });
            }
        });
        // =============================================
        // FORM LOADING STATES
        // =============================================
        document.addEventListener('submit', function (e) {
            if (e.target.tagName === 'FORM') {
                const btn = e.target.querySelector('button[type="submit"]');
                if (btn && !btn.hasAttribute('data-no-loading')) {
                    if (btn.dataset.isSubmitting === 'true') {
                        e.preventDefault();
                        return;
                    }
                    btn.dataset.isSubmitting = 'true';
                    btn.classList.add('btn-loading');
                    const originalContent = btn.innerHTML;
                    btn.dataset.originalHtml = originalContent;
                    btn.innerHTML = `<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
                }
            }
        });
        // =============================================
        // MODALS
        // =============================================
        window.openModal = function (id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            const panel = modal.querySelector('.modal-panel');
            const backdrop = modal.querySelector('.modal-backdrop');
            gsap.fromTo(backdrop, { opacity: 0 }, { opacity: 1, duration: 0.3 });
            gsap.fromTo(panel, { opacity: 0, y: 20, scale: 0.95 }, { opacity: 1, y: 0, scale: 1, duration: 0.4, ease: "back.out(1.2)" });
        }
        window.closeModal = function (id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            const panel = modal.querySelector('.modal-panel');
            const backdrop = modal.querySelector('.modal-backdrop');
            gsap.to(backdrop, { opacity: 0, duration: 0.3 });
            gsap.to(panel, {
                opacity: 0, y: 10, scale: 0.95, duration: 0.3, onComplete: () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });
        }
        // =============================================
        // TOASTS
        // =============================================
        window.showToast = function (type, title, message) {
            // Handle 2 arguments: (type, message)
            if (message === undefined) {
                message = title;
                title = type === 'success' ? 'Success' : 
                        type === 'error' ? 'Error' : 
                        type === 'warning' ? 'Warning' : 'Notice';
            }
            const container = document.getElementById('admin-toast-container');
            const el = document.createElement('div');
            let colors = '';
            let icon = '';
            if (type === 'success') {
                colors = 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400';
                icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
            } else if (type === 'error') {
                colors = 'bg-red-500/10 border-red-500/20 text-red-600 dark:text-red-400';
                icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
            } else if (type === 'warning') {
                colors = 'bg-amber-500/10 border-amber-500/20 text-amber-600 dark:text-amber-400';
                icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
            } else {
                colors = 'bg-primary/10 border-primary/20 text-primary dark:text-primary-light';
                icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />';
            }
            el.className = `flex items-start gap-3 p-4 rounded-xl border bg-white dark:bg-[#121214] shadow-lg shadow-black/5 pointer-events-auto min-w-[300px] max-w-sm ${colors}`;
            el.innerHTML = `
                    <div class="shrink-0 mt-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">${icon}</svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white capitalize">${title}</h3>
                        <p class="text-xs mt-1 font-medium opacity-90 text-slate-600 dark:text-zinc-400">${message}</p>
                    </div>
                `;
            container.appendChild(el);
            const dir = document.dir;
            const xOffset = dir === 'rtl' ? -50 : 50;
            gsap.fromTo(el, { opacity: 0, x: xOffset, scale: 0.95 }, { opacity: 1, x: 0, scale: 1, duration: 0.4, ease: "back.out(1.2)" });
            setTimeout(() => {
                gsap.to(el, { opacity: 0, y: -20, scale: 0.95, duration: 0.3, onComplete: () => el.remove() });
            }, 4000);
        }
        // =============================================
        // LUXURY TOASTS
        // =============================================
        window.showLuxuryToast = function(type, message) {
            const container = document.getElementById('admin-toast-container');
            if (!container) return;
            const toastId = 'toast-' + Date.now();
            const colors = {
                success: ['bg-emerald-500', 'text-emerald-500', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'],
                error: ['bg-red-500', 'text-red-500', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'],
            };
            const [bgClass, textClass, iconPath] = colors[type] || colors['success'];
            const toastHtml = `
                <div id="${toastId}" class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-[#18181b] border border-black/5 dark:border-white/10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] dark:shadow-[0_8px_30px_rgba(0,0,0,0.5)] transform translate-x-full opacity-0 z-[9999]">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full ${bgClass}/10 flex items-center justify-center ${textClass}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${iconPath}
                        </svg>
                    </div>
                    <p class="text-[14px] font-bold text-slate-800 dark:text-zinc-100">${message}</p>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', toastHtml);
            const toastEl = document.getElementById(toastId);
            const xOffset = document.dir === 'rtl' ? -100 : 100;
            if (typeof gsap !== 'undefined') {
                gsap.fromTo(toastEl, { x: xOffset, opacity: 0 }, { x: 0, opacity: 1, duration: 0.5, ease: "back.out(1.5)" });
                setTimeout(() => {
                    gsap.to(toastEl, { x: xOffset, opacity: 0, duration: 0.4, ease: "power2.in", onComplete: () => toastEl.remove() });
                }, 3500);
            } else {
                toastEl.style.transform = 'translateX(0)';
                toastEl.style.opacity = '1';
                setTimeout(() => {
                    toastEl.style.transform = `translateX(${xOffset}%)`;
                    toastEl.style.opacity = '0';
                    setTimeout(() => toastEl.remove(), 300);
                }, 3500);
            }
        }
        // =============================================
        // INITIALIZATION & GSAP
        // =============================================
        window.addEventListener('load', () => {
            // Restore sidebar state immediately
            initSidebarState();
            // Guard clause if GSAP hasn't loaded instantly
            if (typeof gsap === 'undefined') {
                return;
            }
            // Initial Timeline
            const tl = gsap.timeline();
            // Animate Topbar & Content entry nicely
            tl.from('header', { y: -20, opacity: 0, duration: 0.5, ease: "back.out(1.2)" }, "-=0.2");
            // Stagger sidebar items
            tl.from('.sidebar-nav-item', {
                x: -20,
                opacity: 0,
                duration: 0.4,
                stagger: 0.05,
                ease: "power3.out"
            }, "-=0.3");
            // Animate any existing reveal items in the main content
            tl.from('.reveal-item', { opacity: 0, y: 15, duration: 0.5, ease: "power2.out", stagger: 0.05 }, "-=0.2");
        });
        // =============================================
        // GLOBAL DYNAMIC TOOLTIP
        // =============================================
        document.addEventListener('DOMContentLoaded', () => {
            const globalTooltip = document.getElementById('dynamic-tooltip');
            if (!globalTooltip) return;
            const navItems = document.querySelectorAll('.sidebar-nav-item, .sidebar-user-section');
            navItems.forEach(item => {
                item.addEventListener('mouseenter', (e) => {
                    const sidebar = document.getElementById('admin-sidebar');
                    if (sidebar.dataset.collapsed !== 'true' || window.innerWidth < 1024) return;
                    const tooltipText = item.querySelector('.sidebar-tooltip')?.textContent;
                    if (!tooltipText) return;
                    globalTooltip.textContent = tooltipText;
                    globalTooltip.style.opacity = '1';
                    globalTooltip.style.transform = 'translateY(0) scale(1)';
                    const rect = item.getBoundingClientRect();
                    const isRtl = document.documentElement.dir === 'rtl';
                    if (isRtl) {
                        globalTooltip.style.right = (window.innerWidth - rect.left + 10) + 'px';
                        globalTooltip.style.left = 'auto';
                    } else {
                        globalTooltip.style.left = (rect.right + 10) + 'px';
                        globalTooltip.style.right = 'auto';
                    }
                    globalTooltip.style.top = (rect.top + rect.height / 2 - globalTooltip.offsetHeight / 2) + 'px';
                });
                item.addEventListener('mouseleave', () => {
                    globalTooltip.style.opacity = '0';
                    globalTooltip.style.transform = 'translateY(2px) scale(0.98)';
                });
            });
            const navContainer = document.querySelector('#admin-sidebar nav');
            if (navContainer) {
                navContainer.addEventListener('scroll', () => {
                    globalTooltip.style.opacity = '0';
                });
            }
        });
        // =============================================
        // GLOBAL SEARCH & COMMAND PALETTE
        // =============================================
        (function() {
            const searchInput = document.getElementById('global-search-input');
            const searchResults = document.getElementById('global-search-results');
            const resultsContent = document.getElementById('search-results-content');
            const wrapper = document.getElementById('global-search-wrapper');
            let searchTimeout = null;
            let currentQuery = '';
            let selectedIndex = -1;
            let resultsCount = 0;
            const entityColors = {
                user: 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                business: 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                category: 'bg-purple-500/10 text-purple-500 border-purple-500/20',
                location: 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                command: 'bg-primary/10 text-primary border-primary/20',
                suggestion: 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20'
            };
            const suggestions = []; // No longer using hardcoded suggestions
            async function renderSuggestions() {
                try {
                    const response = await fetch('/admin/search?q=');
                    const data = await response.json();
                    let html = `
                        <div class="px-4 py-3 flex items-center justify-between border-b border-slate-100 dark:border-white/5 mb-2">
                            <div class="flex items-center gap-2.5 text-[10px] font-[900] text-primary uppercase ltr:tracking-[0.2em]">
                                <i class="fa-solid fa-bolt-lightning text-[11px] animate-pulse"></i>
                                <span>{{ __('admin.suggestions') }}</span>
                            </div>
                            <div class="text-[9px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">
                                {{ __('admin.recent_activity') }}
                            </div>
                        </div>
                        <div class="space-y-1">
                    `;
                    resultsCount = 0;
                    selectedIndex = -1;
                    // Render recent items and commands from backend
                    const commands = data.commands || [];
                    commands.forEach(item => {
                        html += renderItemHTML(item, '');
                        resultsCount++;
                    });
                    html += '</div>';
                    const history = JSON.parse(localStorage.getItem('admin_search_history') || '[]');
                    if (history.length > 0) {
                        html += `
                            <div class="px-4 py-3 mt-4 flex items-center gap-2.5 text-[10px] font-[900] text-slate-400 dark:text-zinc-500 uppercase ltr:tracking-[0.2em] border-b border-slate-100 dark:border-white/5 mb-2">
                                <i class="fa-solid fa-clock-rotate-left text-[11px] opacity-40"></i>
                                <span>{{ __('admin.recent_searches') }}</span>
                            </div>
                            <div class="space-y-1">
                        `;
                        history.forEach(item => {
                            html += renderItemHTML(item, '');
                            resultsCount++;
                        });
                        html += '</div>';
                    }
                    resultsContent.innerHTML = html;
                    openSearch();
                    gsap.from('.search-result-item', { scale: 0.95, opacity: 0, duration: 0.4, stagger: 0.04, ease: "power2.out" });
                } catch (error) {
                    console.error('Failed to load suggestions', error);
                }
            }
            function highlightMatch(text, query) {
                if (!query || query.length < 2) return text;
                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<span class="text-primary font-black underline decoration-2 underline-offset-4 decoration-primary/20">$1</span>');
            }
            function renderItemHTML(item, query) {
                const colorClass = entityColors[item.type] || 'bg-slate-100 text-slate-400';
                const highlightedTitle = highlightMatch(item.title, query);
                const highlightedSubtitle = item.subtitle ? highlightMatch(item.subtitle, query) : '';
                return `
                    <a href="${item.url}" class="search-result-item flex items-center gap-4 px-4 py-3 rounded-[20px] hover:bg-primary/[0.06] dark:hover:bg-primary/[0.08] transition-all group border border-transparent hover:border-primary/10" data-index="${resultsCount}" data-json='${JSON.stringify(item)}'>
                        <div class="w-10 h-10 rounded-xl ${colorClass} flex items-center justify-center transition-all group-hover:scale-110 shadow-sm border border-transparent group-hover:border-current/20">
                            <i class="fa-solid ${item.icon || 'fa-magnifying-glass'} text-[14px]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[14px] font-[800] text-slate-700 dark:text-zinc-200 truncate group-hover:text-primary transition-colors tracking-tight">${highlightedTitle}</div>
                            ${highlightedSubtitle ? `<div class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 truncate mt-0.5 opacity-80">${highlightedSubtitle}</div>` : ''}
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                            <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </div>
                        </div>
                    </a>
                `;
            }
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                currentQuery = query;
                clearTimeout(searchTimeout);
                if (query.length < 2 && !query.startsWith('/')) {
                    if (query.length === 0) renderSuggestions();
                    else closeSearch();
                    return;
                }
                resultsContent.innerHTML = `
                    <div class="py-16 text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-3xl bg-primary/5 dark:bg-primary/10 mb-4 text-primary">
                            <svg class="animate-spin h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <p class="text-[15px] font-[900] text-slate-400 dark:text-zinc-500 ltr:tracking-wider animate-pulse uppercase">${query.startsWith('/') ? '{{ __("admin.command") ?? "Command" }}' : '{{ __("admin.searching") }}'}...</p>
                    </div>
                `;
                openSearch();
                searchTimeout = setTimeout(() => fetchSearchResults(query), 200);
            });
            searchInput.addEventListener('focus', () => {
                if (searchInput.value.trim().length === 0) renderSuggestions();
                else if (searchInput.value.trim().length >= 2) openSearch();
            });
            async function fetchSearchResults(query) {
                try {
                    const response = await fetch(`/admin/search?q=${encodeURIComponent(query)}`);
                    const data = await response.json();
                    if (currentQuery === query) renderResults(data, query);
                } catch (error) {
                    resultsContent.innerHTML = '<div class="p-8 text-center text-red-500 font-bold">{{ __("admin.fetch_failed") }}</div>';
                }
            }
            function renderResults(data, query) {
                let html = '';
                resultsCount = 0;
                selectedIndex = -1;
                const sections = [
                    { key: 'commands', label: '{{ __("admin.commands") ?? "Commands" }}', icon: 'fa-bolt-lightning' },
                    { key: 'users', label: '{{ __("admin.users") }}', icon: 'fa-users' },
                    { key: 'businesses', label: '{{ __("admin.businesses") }}', icon: 'fa-building' },
                    { key: 'categories', label: '{{ __("admin.categories") }}', icon: 'fa-layer-group' },
                    { key: 'locations', label: '{{ __("admin.locations") }}', icon: 'fa-earth-americas' }
                ];
                sections.forEach(section => {
                    const items = data[section.key] || [];
                    if (items.length > 0) {
                        html += `
                            <div class="mb-5 last:mb-0 px-1">
                                <div class="px-3 py-2 flex items-center gap-2.5 text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-[0.15em] opacity-80">
                                    <i class="fa-solid ${section.icon} text-[11px] opacity-40"></i>
                                    <span>${section.label}</span>
                                </div>
                                <div class="space-y-1">
                        `;
                        items.forEach(item => {
                            html += renderItemHTML(item, query);
                            resultsCount++;
                        });
                        html += `</div></div>`;
                    }
                });
                if (html === '') {
                    html = `
                        <div class="py-16 text-center">
                            <div class="w-16 h-16 bg-slate-50 dark:bg-zinc-800/40 rounded-3xl flex items-center justify-center text-slate-300 dark:text-zinc-600 mx-auto mb-5 border border-slate-100 dark:border-white/5 shadow-inner">
                                <i class="fa-solid fa-magnifying-glass text-2xl"></i>
                            </div>
                            <p class="text-[15px] font-[900] text-slate-800 dark:text-zinc-200">{{ __('admin.no_search_results') }} <span class="text-primary italic">"${query}"</span></p>
                        </div>
                    `;
                }
                resultsContent.innerHTML = html;
                gsap.from('.search-result-item', { y: 10, opacity: 0, duration: 0.4, stagger: 0.05, ease: "power2.out", clearProps: "all" });
            }
            function openSearch() {
                searchResults.classList.remove('pointer-events-none');
                gsap.to(searchResults, { opacity: 1, y: 0, duration: 0.5, ease: "power4.out", overwrite: true });
            }
            function closeSearch() {
                searchResults.classList.add('pointer-events-none');
                gsap.to(searchResults, { opacity: 0, y: -15, duration: 0.3, ease: "power4.in", overwrite: true });
                selectedIndex = -1;
            }
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); searchInput.focus(); }
                if (e.key === 'Escape') closeSearch();
                if (resultsCount > 0 && searchResults.style.opacity === '1') {
                    if (e.key === 'ArrowDown') { e.preventDefault(); selectedIndex = (selectedIndex + 1) % resultsCount; highlightItem(); }
                    else if (e.key === 'ArrowUp') { e.preventDefault(); selectedIndex = (selectedIndex - 1 + resultsCount) % resultsCount; highlightItem(); }
                    else if (e.key === 'Enter' && selectedIndex >= 0) {
                        e.preventDefault();
                        const target = document.querySelector(`.search-result-item[data-index="${selectedIndex}"]`);
                        if (target) target.click();
                    }
                }
            });
            function highlightItem() {
                document.querySelectorAll('.search-result-item').forEach((item, i) => {
                    if (i === selectedIndex) {
                        item.classList.add('bg-primary/[0.08]', 'dark:bg-primary/[0.12]', 'border-primary/20', 'scale-[1.02]', 'shadow-lg');
                        item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } else {
                        item.classList.remove('bg-primary/[0.08]', 'dark:bg-primary/[0.12]', 'border-primary/20', 'scale-[1.02]', 'shadow-lg');
                    }
                });
            }
            document.addEventListener('click', (e) => { if (!wrapper.contains(e.target)) closeSearch(); });
        })();
    /* ── Unified Smart Dropdown Manager (Global) ── */
    window.SmartDropdownManager = {
        activeDropdown: null,
        initialized: false,
        init() {
            if (this.initialized) {
                this.activeDropdown = null;
                return;
            }
            this.bindEvents();
            this.initialized = true;
        },
        bindEvents() {
            // Global Trigger & Selection Logic (Delegated)
            document.addEventListener('click', (e) => {
                const trigger = e.target.closest('.dropdown-trigger');
                if (trigger) {
                    e.stopPropagation();
                    const dropdown = trigger.closest('.custom-smart-dropdown');
                    if (dropdown) this.toggle(dropdown);
                    return;
                }
                const item = e.target.closest('.dropdown-item');
                if (item) {
                    e.stopPropagation();
                    const dropdown = item.closest('.custom-smart-dropdown');
                    if (dropdown) this.select(dropdown, item.dataset.id, item.dataset.label || item.textContent.trim());
                    return;
                }
                // Close when clicking outside
                if (this.activeDropdown && !this.activeDropdown.contains(e.target)) {
                    this.close(this.activeDropdown);
                }
            });
            // Search Logic (Delegated)
            document.addEventListener('input', (e) => {
                const search = e.target.closest('.dropdown-search');
                if (!search) return;
                const query = e.target.value.trim().toLowerCase();
                const menu = search.closest('.dropdown-menu');
                const items = menu.querySelectorAll('.dropdown-item');
                const noResults = menu.querySelector('.no-results-msg');
                let hasMatch = false;
                items.forEach(item => {
                    const label = item.dataset.label ? item.dataset.label.toLowerCase() : item.textContent.toLowerCase();
                    const isMatch = label.includes(query);
                    item.classList.toggle('hidden', !isMatch);
                    if (isMatch) hasMatch = true;
                });
                if (noResults) {
                    noResults.classList.toggle('hidden', hasMatch);
                    if (!hasMatch) {
                        gsap.fromTo(noResults, { opacity: 0, y: -5 }, { opacity: 1, y: 0, duration: 0.2 });
                    }
                }
            });
            // Prevent closing when clicking search or list
            document.addEventListener('click', (e) => {
                const search = e.target.closest('.dropdown-search');
                const list = e.target.closest('.dropdown-list');
                if (search || list) e.stopPropagation();
            });
        },
        toggle(dropdown) {
            if (this.activeDropdown === dropdown) {
                this.close(dropdown);
            } else {
                if (this.activeDropdown) this.close(this.activeDropdown);
                this.open(dropdown);
            }
        },
        open(dropdown) {
            const menu = dropdown.querySelector('.dropdown-menu');
            const chevron = dropdown.querySelector('.fa-chevron-down');
            const search = dropdown.querySelector('.dropdown-search');
            const parentCard = dropdown.closest('.group\\/card');
            const inputGroup = dropdown.closest('.input-group');
            this.activeDropdown = dropdown;
            // Apply elevation classes
            dropdown.classList.add('is-active');
            if (parentCard) parentCard.classList.add('has-active-dropdown');
            if (inputGroup) inputGroup.classList.add('has-active-dropdown');
            menu.classList.remove('hidden');
            gsap.fromTo(menu, 
                { opacity: 0, y: 10, scale: 0.98 }, 
                { opacity: 1, y: 0, scale: 1, duration: 0.3, ease: 'power2.out' }
            );
            if (chevron) gsap.to(chevron, { rotate: 180, duration: 0.3 });
            if (search) setTimeout(() => search.focus(), 100);
        },
        close(dropdown) {
            if (!dropdown) return;
            const menu = dropdown.querySelector('.dropdown-menu');
            const chevron = dropdown.querySelector('.fa-chevron-down');
            const parentCard = dropdown.closest('.group\\/card');
            const inputGroup = dropdown.closest('.input-group');
            gsap.to(menu, { 
                opacity: 0, y: 5, scale: 0.98, duration: 0.2, ease: 'power2.in',
                onComplete: () => {
                    menu.classList.add('hidden');
                    dropdown.classList.remove('is-active');
                    if (parentCard) parentCard.classList.remove('has-active-dropdown');
                    if (inputGroup) inputGroup.classList.remove('has-active-dropdown');
                    if (this.activeDropdown === dropdown) this.activeDropdown = null;
                }
            });
            if (chevron) gsap.to(chevron, { rotate: 0, duration: 0.2 });
        },
        select(dropdown, id, label) {
            const hiddenInput = dropdown.querySelector('input[type="hidden"]');
            const labelSpan = dropdown.querySelector('.dropdown-label');
            const trigger = dropdown.querySelector('.dropdown-trigger');
            if (hiddenInput) {
                hiddenInput.value = id;
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (labelSpan) {
                labelSpan.textContent = label;
                labelSpan.classList.remove('text-slate-400', 'dark:text-zinc-600', 'dark:text-zinc-500');
                labelSpan.classList.add('text-slate-800', 'dark:text-white', 'text-slate-900', 'font-black');
            }
            if (trigger) {
                trigger.classList.add('!border-primary/40', 'dark:!border-primary/30', 'bg-primary/[0.02]');
            }
            // Custom event for module-specific logic
            dropdown.dispatchEvent(new CustomEvent('smart-select', { detail: { id, label } }));
            this.close(dropdown);
        }
    };
    SmartDropdownManager.init();
    </script>
    @stack('scripts')
</body>
</html>
