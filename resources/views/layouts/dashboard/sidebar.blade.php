<aside id="sidebar" class="sidebar-right flex flex-col transition-all duration-500 border-l border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 group/sidebar w-64 [&.sidebar-collapsed]:w-20">
    <div class="header-section flex flex-col flex-shrink-0 p-5">
        <div class="flex items-center justify-between w-full group-[.sidebar-collapsed]/sidebar:flex-col group-[.sidebar-collapsed]/sidebar:gap-8">
            <div class="flex items-center gap-3 min-w-0 group-[.sidebar-collapsed]/sidebar:justify-center group-[.sidebar-collapsed]/sidebar:gap-0">
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/logo.webp') }}" class="w-10 h-10 object-contain" alt="Logo">
                </div>
                <span class="text-xl font-black tracking-tight label-text dark:text-white text-zinc-900 whitespace-nowrap overflow-hidden transition-all duration-300 group-[.sidebar-collapsed]/sidebar:w-0 group-[.sidebar-collapsed]/sidebar:opacity-0 group-[.sidebar-collapsed]/sidebar:mr-0">alidebo</span>
            </div>

            <div class="flex items-center gap-2 group-[.sidebar-collapsed]/sidebar:flex-col group-[.sidebar-collapsed]/sidebar:w-full group-[.sidebar-collapsed]/sidebar:items-center group-[.sidebar-collapsed]/sidebar:gap-5">
                <div class="p-2 cursor-pointer text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900 rounded-xl transition-all relative group/tip flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-zinc-950"></span>
                    <span class="pointer-events-none absolute right-full mr-4 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-xs rounded-lg opacity-0 group-hover/tip:opacity-100 transition-all translate-x-2 group-hover/tip:translate-x-0 whitespace-nowrap z-50 shadow-xl border border-zinc-200 dark:border-white font-bold">الإشعارات</span>
                </div>

                <div class="cursor-pointer p-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 rounded-xl transition-all hidden lg:flex items-center justify-center relative group/tip" onclick="toggleSidebar()">
                    <svg id="toggle-icon" class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="icon-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <span class="pointer-events-none absolute right-full mr-4 top-1/2 -translate-y-1/2 px-2.5 py-1.5 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-xs rounded-lg opacity-0 group-hover/tip:opacity-100 transition-all translate-x-2 group-hover/tip:translate-x-0 whitespace-nowrap z-50 shadow-xl border border-zinc-200 dark:border-white font-bold">طي القائمة</span>
                </div>

                <div class="lg:hidden p-2 cursor-pointer text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900 rounded-xl transition-all flex items-center justify-center" onclick="closeMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 mb-6">
        <div class="h-px w-full bg-zinc-200/50 dark:bg-zinc-800/50"></div>
    </div>

    <nav class="flex-1 px-4 space-y-3">
        <div class="space-y-1.5 group-[.sidebar-collapsed]/sidebar:hidden">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-all hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-600 dark:text-zinc-400 {{ request()->routeIs('dashboard') ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white shadow-sm' : '' }}">
                <div class="flex-shrink-0 flex items-center justify-center w-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <span class="font-bold mr-3.5 whitespace-nowrap">الرئيسية</span>
            </a>
            <a href="{{ route('business.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-600 dark:text-zinc-400 {{ request()->routeIs('business.index') ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white shadow-sm' : '' }}">
                <div class="flex-shrink-0 flex items-center justify-center w-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <span class="font-bold mr-3.5 whitespace-nowrap">أعمالي</span>
            </a>
            <a href="{{ route('business.create') }}" class="flex items-center px-4 py-3 rounded-xl transition-all hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-600 dark:text-zinc-400 {{ request()->routeIs('business.create') ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white shadow-sm' : '' }}">
                <div class="flex-shrink-0 flex items-center justify-center w-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="font-bold mr-3.5 whitespace-nowrap">إضافة بيزنس</span>
            </a>
        </div>

        <div class="hidden group-[.sidebar-collapsed]/sidebar:flex flex-col items-center space-y-4 pt-2">
            <a href="{{ route('dashboard') }}" class="group/link flex items-center justify-center p-3 rounded-xl transition-all hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-600 dark:text-zinc-400 relative {{ request()->routeIs('dashboard') ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white' : '' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="pointer-events-none absolute right-full mr-4 top-1/2 -translate-y-1/2 px-2.5 py-2 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-xs rounded-lg opacity-0 group-hover/link:opacity-100 transition-all translate-x-2 group-hover/link:translate-x-0 whitespace-nowrap z-50 shadow-xl border border-zinc-200 dark:border-white font-bold">الرئيسية</span>
            </a>
            <a href="{{ route('business.index') }}" class="group/link flex items-center justify-center p-3 rounded-xl transition-all hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-600 dark:text-zinc-400 relative {{ request()->routeIs('business.index') ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white' : '' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span class="pointer-events-none absolute right-full mr-4 top-1/2 -translate-y-1/2 px-2.5 py-2 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-xs rounded-lg opacity-0 group-hover/link:opacity-100 transition-all translate-x-2 group-hover/link:translate-x-0 whitespace-nowrap z-50 shadow-xl border border-zinc-200 dark:border-white font-bold">أعمالي</span>
            </a>
            <a href="{{ route('business.create') }}" class="group/link flex items-center justify-center p-3 rounded-xl transition-all hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-600 dark:text-zinc-400 relative {{ request()->routeIs('business.create') ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white' : '' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="pointer-events-none absolute right-full mr-4 top-1/2 -translate-y-1/2 px-2.5 py-2 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-xs rounded-lg opacity-0 group-hover/link:opacity-100 transition-all translate-x-2 group-hover/link:translate-x-0 whitespace-nowrap z-50 shadow-xl border border-zinc-200 dark:border-white font-bold">إضافة بيزنس</span>
            </a>
        </div>
    </nav>

    <div class="mt-auto px-4 pb-6 flex-shrink-0 space-y-5">
        <div class="user-section p-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800/50 transition-all relative flex items-center group/user group-[.sidebar-collapsed]/sidebar:p-1.5 group-[.sidebar-collapsed]/sidebar:bg-transparent group-[.sidebar-collapsed]/sidebar:border-transparent group-[.sidebar-collapsed]/sidebar:justify-center">
            <div class="user-box relative flex-shrink-0 w-10 h-10 rounded-xl overflow-hidden bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center font-bold shadow-sm group-hover/user:scale-105 transition-transform">
                @if(Auth::check() && Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" class="w-full h-full object-cover">
                @else
                {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'U' }}
                @endif
                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-zinc-900 rounded-full"></div>
            </div>
            <div class="min-w-0 mr-3.5 flex-1 transition-all duration-300 overflow-hidden group-[.sidebar-collapsed]/sidebar:hidden">
                <p class="text-sm font-bold truncate dark:text-zinc-100 text-zinc-900">{{ Auth::check() ? Auth::user()->name : 'مستخدم' }}</p>
                <p class="text-[10px] lowercase dark:text-zinc-500 text-zinc-400 truncate mt-0.5">{{ Auth::check() ? Auth::user()->email : 'mail@example.com' }}</p>
            </div>
            <span class="pointer-events-none absolute right-full mr-4 top-1/2 -translate-y-1/2 px-2.5 py-2 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-xs rounded-lg opacity-0 group-hover/user:opacity-100 transition-all translate-x-2 group-hover/user:translate-x-0 whitespace-nowrap z-50 shadow-xl border border-zinc-200 dark:border-white font-bold hidden group-[.sidebar-collapsed]/sidebar:block">الملف الشخصي</span>
        </div>

        <div class="h-px w-full bg-zinc-200/60 dark:bg-zinc-800/60 group-[.sidebar-collapsed]/sidebar:hidden"></div>

        <div class="relative w-full">
            <div class="flex items-center justify-between w-full gap-2 group-[.sidebar-collapsed]/sidebar:hidden px-1">
                <a href="{{ route('profile.edit') }}" class="flex-1 flex justify-center p-2.5 rounded-xl text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-all relative group/tip">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="pointer-events-none absolute bottom-full mb-3 left-1/2 -translate-x-1/2 px-2.5 py-1.5 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-[10px] rounded-lg opacity-0 group-hover/tip:opacity-100 transition-all translate-y-1 group-hover/tip:translate-y-0 whitespace-nowrap z-50 shadow-xl border border-zinc-200 dark:border-white font-bold">الإعدادات</span>
                </a>

                <div onclick="toggleTheme()" class="flex-1 flex justify-center p-2.5 rounded-xl text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900 cursor-pointer transition-all relative group/tip">
                    <svg id="theme-icon-dark" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path>
                    </svg>
                    <svg id="theme-icon-light" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <span class="pointer-events-none absolute bottom-full mb-3 left-1/2 -translate-x-1/2 px-2.5 py-1.5 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-[10px] rounded-lg opacity-0 group-hover/tip:opacity-100 transition-all translate-y-1 group-hover/tip:translate-y-0 whitespace-nowrap z-50 shadow-xl border border-zinc-200 dark:border-white font-bold">تبديل الوضع</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full flex justify-center p-2.5 rounded-xl text-zinc-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all relative group/tip">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="pointer-events-none absolute bottom-full mb-3 left-1/2 -translate-x-1/2 px-2.5 py-1.5 bg-red-600 text-white text-[10px] rounded-lg opacity-0 group-hover/tip:opacity-100 transition-all translate-y-1 group-hover/tip:translate-y-0 whitespace-nowrap z-50 shadow-xl font-bold">خروج</span>
                    </button>
                </form>
            </div>

            <div class="hidden group-[.sidebar-collapsed]/sidebar:flex flex-col items-center w-full pt-2">
                <form action="{{ route('logout') }}" method="POST" class="w-full flex justify-center">
                    @csrf
                    <button type="submit" class="group/link flex items-center justify-center p-3 rounded-xl transition-all hover:bg-red-50 dark:hover:bg-red-500/10 text-zinc-500 hover:text-red-500 relative">
                        <svg class="w-6 h-6 transition-transform group-hover/link:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="pointer-events-none absolute right-full mr-4 top-1/2 -translate-y-1/2 px-2.5 py-2 bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 text-xs rounded-lg opacity-0 group-hover/link:opacity-100 transition-all translate-x-2 group-hover/link:translate-x-0 whitespace-nowrap z-50 shadow-xl border border-zinc-200 dark:border-white font-bold">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>