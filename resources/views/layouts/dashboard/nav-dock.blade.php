<nav class="nav-dock" id="nav-dock">
    <div class="nav-logo-mobile">
        <img src="{{ asset('images/logo.webp') }}" class="w-9 h-9 object-contain" alt="Logo">
        <span class="text-lg font-black dark:text-white text-zinc-900">alidebo</span>
    </div>

    @if(View::hasSection('nav_links'))
        @yield('nav_links')
    @else
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">الرئيسية</a>
        <a href="#" class="nav-link">الإحصائيات</a>
        <a href="#" class="nav-link">المساعدة</a>
    @endif

    <div class="hamburger" id="hamburger" onclick="toggleMobileMenu()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path class="line-1" d="M4 6h16"></path>
            <path class="line-2" d="M4 12h16"></path>
            <path class="line-3" d="M4 18h16"></path>
        </svg>
    </div>
</nav>