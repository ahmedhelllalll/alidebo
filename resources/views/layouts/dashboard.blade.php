<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>alidebo Studio | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#f45018',
                        accent: '#e11d48'
                    },
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            transition: background-color 0.5s, color 0.5s;
            overflow-x: hidden;
            line-height: 1.6;
        }

        html.dark body {
            background: #09090b;
            color: #f4f4f5;
        }

        html:not(.dark) body {
            background: #f8f9fa;
            color: #09090b;
        }

        .ambient-canvas {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .glow-1 {
            position: absolute;
            top: -10%;
            left: 20%;
            width: 70%;
            height: 60%;
            background: radial-gradient(circle, rgba(244, 80, 24, 0.07) 0%, transparent 70%);
        }

        .sidebar-right {
            position: fixed;
            right: 24px;
            top: 24px;
            bottom: 24px;
            width: 280px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            padding: 28px 20px;
            border-radius: 32px;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(20px);
        }

        html.dark .sidebar-right {
            background: rgba(18, 18, 20, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.5);
        }

        html:not(.dark) .sidebar-right {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.08);
        }

        .sidebar-collapsed {
            width: 90px;
            padding: 28px 15px;
        }

        .header-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 48px;
            padding: 0 8px;
        }

        .sidebar-collapsed .header-section {
            justify-content: center;
            flex-direction: column;
            gap: 24px;
        }

        .action-item {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 16px;
            border-radius: 18px;
            transition: all 0.3s ease;
            margin-bottom: 8px;
            cursor: pointer;
            position: relative;
        }

        html.dark .action-item {
            color: #a1a1aa;
        }

        html:not(.dark) .action-item {
            color: #52525b;
        }

        .sidebar-collapsed .action-item {
            justify-content: center;
            padding: 14px 0;
        }

        .icon-container {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .action-item:hover {
            background: rgba(244, 80, 24, 0.1);
            color: #f45018;
        }

        .action-item.active {
            background: #f45018;
            color: #ffffff !important;
            box-shadow: 0 10px 20px -5px rgba(244, 80, 24, 0.4);
        }

        .side-tooltip {
            position: absolute;
            right: calc(100% + 15px);
            top: 50%;
            transform: translateY(-50%) translateX(10px);
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: 0.2s ease;
            z-index: 1000;
        }

        html.dark .side-tooltip {
            background: #1f1f23;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        html:not(.dark) .side-tooltip {
            background: #ffffff;
            color: #18181b;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .sidebar-collapsed .action-item:hover .side-tooltip,
        .sidebar-collapsed .user-section:hover .side-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateY(-50%) translateX(0);
        }

        .label-text {
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            transition: opacity 0.3s;
        }

        .sidebar-collapsed .label-text {
            opacity: 0;
            display: none;
        }

        .user-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            border-radius: 20px;
            transition: 0.3s;
            width: 100%;
        }

        .sidebar-collapsed .user-section {
            justify-content: center;
            padding: 12px 0;
        }

        .user-box {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #f45018, #e11d48);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 8px 20px -5px rgba(244, 80, 24, 0.4);
        }

        .logout-btn {
            padding: 10px;
            border-radius: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-collapsed .logout-btn {
            display: none;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444 !important;
        }

        .main-wrapper {
            margin-right: 340px;
            padding: 40px;
            min-height: 100vh;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .wrapper-expanded {
            margin-right: 140px;
        }

        .nav-dock {
            position: fixed;
            top: 25px;
            z-index: 90;
            display: flex;
            align-items: center;
            gap: 16px;
            backdrop-filter: blur(24px) saturate(180%);
            padding: 12px 24px;
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-dock.scrolled {
            padding: 8px 16px;
            opacity: 0.95;
            top: 15px;
            transform: scale(0.98);
        }

        html.dark .nav-dock {
            background: rgba(20, 20, 22, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
        }

        html:not(.dark) .nav-dock {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
        }

        .nav-link {
            padding: 8px 20px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 700;
            transition: 0.3s ease;
        }

        html.dark .nav-link {
            color: #a1a1aa;
        }

        html:not(.dark) .nav-link {
            color: #71717a;
        }

        .nav-link.active {
            color: white !important;
            background: #f45018;
            box-shadow: 0 8px 15px -4px rgba(244, 80, 24, 0.3);
        }

        .hamburger {
            display: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 12px;
            transition: 0.3s;
            position: relative;
            z-index: 1001;
        }

        .hamburger svg {
            width: 32px;
            height: 32px;
        }

        .hamburger path {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
            transform-origin: center;
        }

        .nav-logo-mobile {
            display: none;
            align-items: center;
            gap: 10px;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 1024px) {
            .sidebar-right {
                right: -100%;
                top: 0;
                bottom: 0;
                height: 100vh;
                width: 300px !important;
                border-radius: 0;
                border-top-right-radius: 32px;
                border-bottom-right-radius: 32px;
                padding: 32px 24px;
                display: flex;
            }

            .sidebar-right.active {
                right: 0;
            }

            .main-wrapper {
                margin-right: 0 !important;
                padding: 110px 20px;
            }

            .nav-dock {
                left: 12px;
                right: 12px;
                justify-content: space-between;
                width: auto;
                padding: 10px 16px;
                border-radius: 20px;
            }

            .nav-link {
                display: none;
            }

            .hamburger,
            .nav-logo-mobile {
                display: flex;
            }

            .menu-open .line-1 {
                transform: translateY(6px) rotate(45deg);
            }

            .menu-open .line-2 {
                opacity: 0;
                transform: translateX(-10px);
            }

            .menu-open .line-3 {
                transform: translateY(-6px) rotate(-45deg);
            }

            .sidebar-right .header-section {
                display: flex !important;
                margin-bottom: 32px;
            }

            .sidebar-right .label-text {
                display: block !important;
                opacity: 1 !important;
            }

            .sidebar-right .logout-btn {
                display: flex !important;
            }

            .action-item {
                padding: 16px 20px;
                margin-bottom: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="overlay" onclick="closeMobileMenu()"></div>

    <div class="ambient-canvas">
        <div class="glow-1"></div>
    </div>

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
            <a href="{{ route('dashboard') }}" class="action-item active group">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
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

    <main class="main-wrapper" id="main-wrapper">
        <nav class="nav-dock" id="nav-dock">
            <div class="nav-logo-mobile">
                <img src="{{ asset('images/logo.webp') }}" class="w-9 h-9 object-contain" alt="Logo">
                <span class="text-lg font-black dark:text-white text-zinc-900">alidebo</span>
            </div>

            <a href="#hero" class="nav-link active">الرئيسية</a>
            <a href="#features" class="nav-link">المميزات</a>
            <a href="#how-to" class="nav-link">المساعدة</a>

            <div class="hamburger" id="hamburger" onclick="toggleMobileMenu()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path class="line-1" d="M4 6h16"></path>
                    <path class="line-2" d="M4 12h16"></path>
                    <path class="line-3" d="M4 18h16"></path>
                </svg>
            </div>
        </nav>

        <div class="max-w-6xl w-full mx-auto mt-24">
            @yield('content')
        </div>
    </main>

    <script>
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('nav-dock');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const wrapper = document.getElementById('main-wrapper');
            const path = document.getElementById('icon-path');
            sidebar.classList.toggle('sidebar-collapsed');
            wrapper.classList.toggle('wrapper-expanded');

            if (sidebar.classList.contains('sidebar-collapsed')) {
                path.setAttribute('d', 'M4 6h16M4 12h8m-8 6h16');
            } else {
                path.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
            }
        }

        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const hamburger = document.getElementById('hamburger');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('active');
            hamburger.classList.toggle('menu-open');
            overlay.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const hamburger = document.getElementById('hamburger');
            const overlay = document.getElementById('overlay');
            sidebar.classList.remove('active');
            hamburger.classList.remove('menu-open');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function toggleTheme() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        }

        window.onload = () => {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        };
    </script>
</body>

</html>