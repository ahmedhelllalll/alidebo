<aside class="sidebar-right" id="sidebar">
    <div class="header-section">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/logo.webp') }}" class="w-11 h-11 object-contain" alt="Logo">
            </div>
            <span class="text-xl font-black tracking-tight label-text dark:text-white text-zinc-900">alidebo</span>
        </div>

        <div class="cursor-pointer p-2.5 hover:bg-zinc-500/10 rounded-xl transition-all hidden lg:block" onclick="toggleSidebar()">
            <svg id="toggle-icon" class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path id="icon-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </div>

        <div class="lg:hidden cursor-pointer p-2 rounded-xl bg-zinc-500/10 transition-all" onclick="closeMobileMenu()">
            <svg class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
    </div>

    <nav class="flex-1 w-full space-y-1">
        <a href="{{ route('dashboard') }}" class="action-item {{ request()->routeIs('dashboard') ? 'active' : '' }} group">
            <div class="icon-container">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="label-text">لوحة التحكم</span>
            <span class="side-tooltip">لوحة التحكم</span>
        </a>

        <a href="#" class="action-item group">
            <div class="icon-container">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <span class="label-text">أعمالي</span>
            <span class="side-tooltip">أعمالي</span>
        </a>

        <div onclick="toggleTheme()" class="action-item group">
            <div class="icon-container">
                <svg id="theme-icon-dark" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path>
                </svg>
                <svg id="theme-icon-light" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </div>
            <span class="label-text">تبديل الوضع</span>
            <span class="side-tooltip">تبديل الوضع</span>
        </div>
    </nav>

    <div class="mt-auto">
        <div class="my-6 h-[1px] w-full bg-gradient-to-l from-transparent via-zinc-400/20 dark:via-white/10 to-transparent"></div>

        <a href="#" class="action-item group">
            <div class="icon-container">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                </svg>
            </div>
            <span class="label-text">الإعدادات</span>
            <span class="side-tooltip">الإعدادات</span>
        </a>

        <div class="user-section group mt-2 relative border border-transparent hover:border-zinc-500/10 transition-all bg-zinc-500/5 lg:bg-transparent">
            <div class="flex items-center gap-3 min-w-0">
                <div class="user-box overflow-hidden">
                    @if(Auth::check() && Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover">
                    @else
                        {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'U' }}
                    @endif
                </div>

                <div class="min-w-0 label-text">
                    <p class="text-sm font-bold truncate dark:text-zinc-100 text-zinc-900 leading-tight">
                        {{ Auth::check() ? Auth::user()->name : 'مستخدم' }}
                    </p>
                    <p class="text-[11px] mt-0.5 dark:text-zinc-400 text-zinc-500 truncate">الحساب الشخصي</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="logout-btn" title="تسجيل الخروج">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>

            <span class="side-tooltip">الملف الشخصي</span>
        </div>
    </div>
</aside>