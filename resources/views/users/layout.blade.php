<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="dark antialiased h-full relative">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ __('common.alidebo') ?? 'alidebo' }}</title>

    {{-- Modern High-End Fonts --}}

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dashboard.js'])
    <style>
        /* Pro-level Animations: Handled by GSAP context */
    </style>
    <style>
        /* Minimal Luxury Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(161, 161, 170, 0.3); border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(82, 82, 91, 0.4); }
        ::-webkit-scrollbar-thumb:hover { background: rgba(161, 161, 170, 0.5); }

        [dir="rtl"] { font-family: 'Cairo', sans-serif; }
        [dir="ltr"] { font-family: 'Plus Jakarta Sans', sans-serif; }

        body { overflow-x: hidden; -webkit-tap-highlight-color: transparent; }

        /* Premium Utility */
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .dark .glass-panel {
            background: rgba(9, 9, 11, 0.8);
        }
    </style>
    @stack('styles')
    {{-- Alpine.js (required for x-data, x-show, x-for, etc.) --}}

    {{-- Instant theme application (prevents FOUC) --}}
    <script>
        const savedTheme = localStorage.getItem('theme') || 'dark';
        if (savedTheme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="text-start text-zinc-800 dark:text-zinc-200 bg-zinc-50 dark:bg-[#09090b] selection:bg-primary/20 flex h-screen overflow-hidden">

    {{-- Mobile Overlay --}}
    <div id="mobile-overlay" onclick="closeMobileMenu()" class="fixed inset-0 z-40 bg-zinc-900/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>

    {{-- Strict Structural Sidebar --}}
    <aside id="sidebar" class="fixed inset-y-0 start-0 z-50 w-72 bg-white dark:bg-[#09090b] border-e border-zinc-200 dark:border-zinc-800/80 transition-transform duration-300 ease-in-out lg:!translate-x-0 lg:static flex flex-col h-full -translate-x-full rtl:translate-x-full shadow-2xl lg:shadow-none">

        {{-- Branding --}}
        <div class="h-16 flex items-center px-5 border-b border-black/5 dark:border-white/[0.04] shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0 group">
                <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="w-8 h-8 object-contain transition-transform duration-200 group-hover:scale-110">
                <span class="text-lg font-[900] tracking-tight bg-gradient-to-r dark:from-white dark:to-zinc-400 from-zinc-900 to-zinc-600 bg-clip-text text-transparent leading-none" dir="ltr">alidebo</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-5 custom-scrollbar">

            {{-- Label: General --}}
            <p class="px-3 text-[10px] font-bold uppercase tracking-[0.12em] text-zinc-400/80 dark:text-zinc-500/80 mb-2">
                {{ __('dashboard.sidebar.main') ?? 'General' }}
            </p>
            <div class="space-y-0.5 mb-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary/[0.08] text-primary font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100/60 dark:hover:bg-zinc-800/40 hover:text-zinc-800 dark:hover:text-zinc-200' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>{{ __('nav.home') }}</span>
                </a>
                <a href="{{ route('business.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors {{ request()->routeIs('business.edit') ? 'bg-primary/[0.08] text-primary font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100/60 dark:hover:bg-zinc-800/40 hover:text-zinc-800 dark:hover:text-zinc-200' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>{{ __('dashboard.sidebar.business') ?? 'Business Profile' }}</span>
                </a>
            </div>

            {{-- Label: SEO --}}
            <p class="px-3 text-[10px] font-bold uppercase tracking-[0.12em] text-zinc-400/80 dark:text-zinc-500/80 mb-2">
                {{ __('dashboard.sidebar.seo') ?? 'SEO' }}
            </p>
            <div class="space-y-0.5 mb-5">
                <a href="{{ route('business.translations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors {{ request()->routeIs('business.translations.*') ? 'bg-primary/[0.08] text-primary font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100/60 dark:hover:bg-zinc-800/40 hover:text-zinc-800 dark:hover:text-zinc-200' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    <span>{{ __('dashboard.index.translations') ?? 'Translations' }}</span>
                </a>
                <a href="{{ route('dashboard.reviews.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors {{ request()->routeIs('dashboard.reviews.*') ? 'bg-primary/[0.08] text-primary font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100/60 dark:hover:bg-zinc-800/40 hover:text-zinc-800 dark:hover:text-zinc-200' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <span>{{ __('dashboard.index.reviews') ?? 'Reviews' }}</span>
                </a>
                <a href="{{ route('dashboard.leads.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors {{ request()->routeIs('dashboard.leads.*') ? 'bg-primary/[0.08] text-primary font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100/60 dark:hover:bg-zinc-800/40 hover:text-zinc-800 dark:hover:text-zinc-200' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>{{ __('dashboard.index.leads') ?? 'Leads' }}</span>
                </a>
            </div>

            {{-- Label: Settings --}}
            <p class="px-3 text-[10px] font-bold uppercase tracking-[0.12em] text-zinc-400/80 dark:text-zinc-500/80 mb-2">
                {{ __('dashboard.sidebar.settings') ?? 'Settings' }}
            </p>
            <div class="space-y-0.5">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors {{ request()->routeIs('profile.edit') ? 'bg-primary/[0.08] text-primary font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100/60 dark:hover:bg-zinc-800/40 hover:text-zinc-800 dark:hover:text-zinc-200' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ __('dashboard.account_label') ?? 'Account' }}</span>
                </a>
                <a href="{{ route('support.chat.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] font-medium transition-colors {{ request()->routeIs('support.chat.*') ? 'bg-primary/[0.08] text-primary font-semibold' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100/60 dark:hover:bg-zinc-800/40 hover:text-zinc-800 dark:hover:text-zinc-200' }}">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>{{ __('dashboard.index.help_center') ?? 'Support' }}</span>
                </a>
            </div>

        </nav>

        {{-- User Card + Logout --}}
        <div class="shrink-0 border-t border-black/5 dark:border-white/[0.04] px-4 py-3 space-y-2">
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-zinc-50 dark:bg-[#0e0e11] border border-black/5 dark:border-white/[0.04]">
                <div class="w-8 h-8 text-xs rounded-full bg-gradient-to-br from-primary/20 to-orange-400/20 flex items-center justify-center font-bold text-primary">
                    {{ Auth::check() ? mb_substr(Auth::user()->name, 0, 1) : 'U' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold truncate text-zinc-900 dark:text-zinc-100">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex justify-center items-center gap-2 py-2 rounded-xl text-xs font-semibold text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 dark:hover:text-rose-400 transition-colors">
                    <svg class="w-4 h-4 rtl:-scale-x-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    {{ __('nav.logout') ?? 'Sign out' }}
                </button>
            </form>
        </div>
    </aside>

    {{-- Main App Area --}}
    <div class="flex-1 flex flex-col h-full min-w-0 bg-transparent">

        {{-- High-end Minimal Header --}}
        <header class="h-16 px-6 sm:px-8 flex items-center justify-between shrink-0 bg-white/60 dark:bg-[#09090b]/60 backdrop-blur-xl border-b border-black/5 dark:border-white/[0.04] sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button onclick="toggleMobileMenu()" class="lg:hidden p-1.5 -ms-1.5 rounded-md text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="h-4 w-px bg-zinc-200 dark:bg-zinc-800 hidden lg:block"></div>
                <h1 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 truncate lg:ps-2">@yield('page_title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-3 sm:gap-4 relative">

                {{-- Notifications Bell --}}
                <div class="relative" x-data="notificationsMenu()" @click.away="open = false">
                    <button @click="toggle()" class="relative p-2 rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors border border-transparent">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span x-show="count > 0" x-cloak class="absolute top-1 right-1.5 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    </button>

                    <div x-show="open" x-transition.opacity.scale.95 x-cloak class="absolute top-full right-0 mt-2 w-80 bg-white dark:bg-[#0e0e11] rounded-2xl shadow-xl border border-zinc-200 dark:border-white/5 overflow-hidden z-50 origin-top-right">
                        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Notifications</h3>
                            <button x-show="count > 0" @click="markAllAsRead()" class="text-xs font-semibold text-primary hover:text-primary-dark transition-colors">Mark all read</button>
                        </div>
                        <div class="max-h-80 overflow-y-auto custom-scrollbar">
                            <template x-if="count === 0">
                                <div class="p-6 text-center text-zinc-500 dark:text-zinc-400 text-sm">No new notifications</div>
                            </template>
                            <template x-for="notif in notifications" :key="notif.id">
                                <div class="p-4 border-b border-zinc-50 dark:border-zinc-800/50 hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors cursor-pointer" @click="markAsRead(notif.id)">
                                    <p class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-200" x-text="notif.data.name + ' sent a new lead.'"></p>
                                    <p class="text-[11px] text-zinc-500 mt-1" x-text="new Date(notif.created_at).toLocaleString()"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Theme Switcher --}}
                <button onclick="toggleTheme()" class="p-2 rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors border border-transparent">
                    <svg id="theme-icon-dark" class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                    <svg id="theme-icon-light" class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                {{-- Language Switcher Dropdown --}}
                <div class="relative" id="langDropdownContainer">
                    <button onclick="toggleLangDropdown()" class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-black/5 dark:border-white/[0.04] text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        @if(app()->getLocale() == 'en')
                            <img src="https://flagcdn.com/w40/us.png" alt="US" class="w-[18px] h-[13px] rounded-sm object-cover shadow-[0_1px_2px_rgba(0,0,0,0.1)]">
                            <span class="hidden sm:inline-block">English</span>
                        @elseif(app()->getLocale() == 'ar')
                            <img src="https://flagcdn.com/w40/eg.png" alt="EG" class="w-[18px] h-[13px] rounded-sm object-cover shadow-[0_1px_2px_rgba(0,0,0,0.1)]">
                            <span class="hidden sm:inline-block font-arabic">العربية</span>
                        @elseif(app()->getLocale() == 'es')
                            <img src="https://flagcdn.com/w40/es.png" alt="ES" class="w-[18px] h-[13px] rounded-sm object-cover shadow-[0_1px_2px_rgba(0,0,0,0.1)]">
                            <span class="hidden sm:inline-block">Español</span>
                        @elseif(app()->getLocale() == 'de')
                            <img src="https://flagcdn.com/w40/de.png" alt="DE" class="w-[18px] h-[13px] rounded-sm object-cover shadow-[0_1px_2px_rgba(0,0,0,0.1)]">
                            <span class="hidden sm:inline-block">Deutsch</span>
                        @elseif(app()->getLocale() == 'zh')
                            <img src="https://flagcdn.com/w40/cn.png" alt="CN" class="w-[18px] h-[13px] rounded-sm object-cover shadow-[0_1px_2px_rgba(0,0,0,0.1)]">
                            <span class="hidden sm:inline-block">中文</span>
                        @elseif(app()->getLocale() == 'tr')
                            <img src="https://flagcdn.com/w40/tr.png" alt="TR" class="w-[18px] h-[13px] rounded-sm object-cover shadow-[0_1px_2px_rgba(0,0,0,0.1)]">
                            <span class="hidden sm:inline-block">Türkçe</span>
                        @endif
                        <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="langDropdownMenu" class="absolute top-full mt-2 end-0 w-44 bg-white dark:bg-[#0e0e11] border border-black/5 dark:border-white/[0.04] rounded-xl shadow-lg opacity-0 pointer-events-none transform scale-95 transition-all origin-top-right z-50 py-1.5 overflow-hidden">
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
                                <span class="font-arabic">العربية</span>
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

            </div>
        </header>

        {{-- Scrollable Content Area --}}
        <main class="flex-1 overflow-y-auto w-full custom-scrollbar selection:bg-primary/20">
            <div class="p-6 md:p-10 max-w-7xl mx-auto min-h-full pb-24 lg:pb-12">
                @if(session('success'))
                <div class="mb-8 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 flex items-center gap-3 font-medium text-sm shadow-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-8 p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200/50 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 flex items-center gap-3 font-medium text-sm shadow-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    {{-- Script Handlers --}}
    <script>
        function toggleMobileMenu() { 
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.add('!translate-x-0'); 

            const overlay = document.getElementById('mobile-overlay');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
        }
        function closeMobileMenu() { 
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.remove('!translate-x-0'); 

            const overlay = document.getElementById('mobile-overlay');
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
            overlay.classList.add('opacity-0', 'pointer-events-none');
        }

        // Language Dropdown Logic
        const langDropdownMenu = document.getElementById('langDropdownMenu');
        let isLangDropdownOpen = false;

        function toggleLangDropdown() {
            isLangDropdownOpen = !isLangDropdownOpen;
            if (isLangDropdownOpen) {
                langDropdownMenu.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                langDropdownMenu.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');
            } else {
                langDropdownMenu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                langDropdownMenu.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
            }
        }

        document.addEventListener('click', (e) => {
            const container = document.getElementById('langDropdownContainer');
            if (container && !container.contains(e.target) && isLangDropdownOpen) {
                toggleLangDropdown();
            }
        });

        function toggleTheme() { 
            const html = document.documentElement; 
            html.classList.toggle('dark'); 
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light'); 
            updateThemeIcons();
        }
        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const darkIcons = document.querySelectorAll('#theme-icon-dark');
            const lightIcons = document.querySelectorAll('#theme-icon-light');
            darkIcons.forEach(el => el.classList.toggle('hidden', !isDark));
            lightIcons.forEach(el => el.classList.toggle('hidden', isDark));
        }

        window.onload = () => {
            updateThemeIcons();
        };
    </script>

    {{-- GSAP Animation Enhancements --}}

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Safety fallback: if GSAP failed to load, un-hide everything
            if (typeof gsap === 'undefined') {
                return;
            }

            const isRtl = document.documentElement.dir === 'rtl';
            const xOffset = isRtl ? 20 : -20;

            // Scoped context for cleanup
            const dashCtx = gsap.context(() => {

                // ── Sidebar link stagger ──
                const sidebarLinks = gsap.utils.toArray('#sidebar a');
                if (sidebarLinks.length) {
                    gsap.fromTo(sidebarLinks,
                        { x: xOffset, autoAlpha: 0 },
                        {
                            x: 0, autoAlpha: 1,
                            duration: 0.5,
                            stagger: 0.04,
                            ease: 'power3.out',
                            delay: 0.05,
                            clearProps: 'all'
                        }
                    );
                }

                // ── Header slide down (scoped to the main app header) ──
                const mainHeader = document.querySelector('.flex-1 > header');
                if (mainHeader) {
                    gsap.fromTo(mainHeader,
                        { y: -12, autoAlpha: 0 },
                        {
                            y: 0, autoAlpha: 1,
                            duration: 0.5,
                            ease: 'power3.out',
                            clearProps: 'all'
                        }
                    );
                }

                // ── Main content cascade ──
                const staggerEls = gsap.utils.toArray('.gsap-stagger');
                if (staggerEls.length) {
                    gsap.fromTo(staggerEls,
                        { y: 16, autoAlpha: 0 },
                        {
                            y: 0, autoAlpha: 1,
                            duration: 0.5,
                            stagger: 0.06,
                            ease: 'power3.out',
                            delay: 0.15,
                            clearProps: 'all'
                        }
                    );
                }

            }); // end gsap.context

            // Store for potential cleanup
            window.__gsapDashCtx = dashCtx;
        });

        // Notifications Menu Alpine Component
        document.addEventListener('alpine:init', () => {
            Alpine.data('notificationsMenu', () => ({
                open: false,
                count: 0,
                notifications: [],
                init() {
                    this.fetchUnread();
                    setInterval(() => {
                        this.fetchUnread();
                    }, 10000); // Check every 10 seconds
                },
                toggle() {
                    this.open = !this.open;
                },
                fetchUnread() {
                    fetch('{{ route("notifications.unread") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.count > this.count) {
                            // Optional: play a subtle sound or notification pop
                        }
                        this.count = data.count;
                        this.notifications = data.notifications;
                    })
                    .catch(err => console.error(err));
                },
                markAsRead(id) {
                    fetch(`/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(() => {
                        this.fetchUnread();
                        this.open = false;
                    });
                },
                markAllAsRead() {
                    fetch('{{ route("notifications.readAll") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(() => {
                        this.fetchUnread();
                        this.open = false;
                    });
                }
            }));
        });
    </script>
    @stack('scripts')

    {{-- Luxury Toast Container --}}
    <div id="luxury-toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[1000] pointer-events-none opacity-0 translate-y-4">
        <div class="glass-panel flex items-center gap-4 px-6 py-4 rounded-2xl border border-white/20 dark:border-white/10 shadow-2xl shadow-black/20 rtl:flex-row-reverse">
            <div id="toast-icon-container" class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                <img id="toast-icon" src="{{ asset('images/logo.webp') }}" alt="alidebo" class="w-6 h-6 object-contain">
            </div>
            <div class="flex flex-col">
                <span id="toast-title" class="text-xs font-bold uppercase tracking-widest text-primary/80 mb-0.5 leading-none"></span>
                <span id="toast-msg" class="text-sm font-bold text-zinc-900 dark:text-white leading-tight min-w-[140px]"></span>
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
            showToast("{{ __('landing.coming_soon') ?? 'Coming Soon' }}", "سيتم توفير هذه الميزة قريباً");
        };
    </script>
</body>
</html>