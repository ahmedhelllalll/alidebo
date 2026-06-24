<div class="lg:hidden">
    <header id="smart-header" class="fixed top-0 left-0 right-0 z-[1000] px-4 pt-3 pb-2 transition-transform duration-500 will-change-transform" style="transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);">
        <div class="bg-white/80 dark:bg-zinc-950/80 backdrop-blur-xl border border-zinc-200/50 dark:border-zinc-800/50 rounded-2xl shadow-sm h-16 flex items-center justify-between px-4">

            <div class="flex items-center gap-3 min-w-0">
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/logo.webp') }}" class="w-7 h- object-contain" alt="Logo">
                </div>
                <span class="text-l font-black tracking-tight dark:text-white text-zinc-900 whitespace-nowrap">alidebo</span>
            </div>

            <div class="flex items-center gap-1">
                <div onclick="toggleTheme()" class="p-2.5 cursor-pointer text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900 rounded-xl transition-all active:scale-90">
                    <svg id="theme-icon-dark" class="w-5 h-5 hidden dark:block text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path>
                    </svg>
                    <svg id="theme-icon-light" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </div>

                <a href="#" class="relative p-2.5 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900 rounded-xl transition-all active:scale-90">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 border-2 border-white dark:border-zinc-950 rounded-full"></span>
                </a>
            </div>
        </div>
    </header>

    <div class="h-24"></div>

    <nav id="mobile-dock" class="fixed bottom-0 left-0 right-0 z-[1000] bg-white/95 dark:bg-zinc-950/95 backdrop-blur-xl border-t border-zinc-200 dark:border-zinc-800 rounded-t-[1.8rem] shadow-[0_-5px_20px_rgba(0,0,0,0.03)] pb-safe">
        <div class="flex items-center justify-around h-16 px-4">
            @php
            $navItems = [
            ['route' => 'dashboard', 'label' => 'الرئيسية', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'business.index', 'label' => 'أعمالي', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
            ['route' => 'business.create', 'label' => 'إضافة', 'icon' => 'M12 4v16m8-8H4'],
            ['route' => 'profile.edit', 'label' => 'حسابي', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}" class="flex flex-col items-center justify-center flex-1 relative h-full group">
                <div class="flex flex-col items-center gap-1 {{ request()->routeIs($item['route']) ? 'text-primary' : 'text-zinc-400 dark:text-zinc-500' }}">
                    <svg class="w-6 h-6 transition-transform duration-300 {{ request()->routeIs($item['route']) ? 'scale-110' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs($item['route']) ? '2' : '1.5' }}" d="{{ $item['icon'] }}"></path>
                    </svg>
                    <span class="text-[10px] font-bold tracking-tight {{ request()->routeIs($item['route']) ? 'opacity-100' : 'opacity-60 font-medium' }}">{{ $item['label'] }}</span>
                </div>
                @if(request()->routeIs($item['route']))
                <span class="absolute top-0 w-8 h-1 bg-primary rounded-b-full shadow-[0_1px_5px_rgba(var(--primary-rgb),0.4)]"></span>
                @endif
            </a>
            @endforeach
        </div>
    </nav>
</div>

<script>
    let lastScroll = 0;
    const header = document.getElementById('smart-header');

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        // Threshold to prevent jitter (5px)
        if (Math.abs(currentScroll - lastScroll) < 5) return;

        if (currentScroll <= 10) {
            header.classList.remove('-translate-y-[110%]');
            return;
        }

        if (currentScroll > lastScroll && currentScroll > 60) {
            // Scrolling down - Hide with slightly more offset to hide shadow
            header.classList.add('-translate-y-[110%]');
        } else {
            // Scrolling up - Show
            header.classList.remove('-translate-y-[110%]');
        }
        lastScroll = currentScroll;
    }, {
        passive: true
    });
</script>