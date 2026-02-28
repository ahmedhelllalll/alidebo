<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'alidebo Studio' }}</title>
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
            margin-bottom: 18px;
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

        .sidebar-collapsed .action-item:hover .side-tooltip {
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

        .main-wrapper {
            margin-right: 340px;
            padding-top: 100px;
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

        section {
            scroll-margin-top: 120px;
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
            z-index: 1001;
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
            transition: 0.4s;
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
                border-top-left-radius: 32px;
                border-bottom-left-radius: 32px;
            }

            .sidebar-right.active {
                right: 0;
            }

            .main-wrapper {
                margin-right: 0 !important;
                padding-top: 90px;
            }

            .nav-dock {
                left: 12px;
                right: 12px;
                justify-content: space-between;
                width: auto;
            }

            .hamburger,
            .nav-logo-mobile {
                display: flex;
            }

            .nav-link {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="overlay" onclick="closeMobileMenu()"></div>

    <div class="ambient-canvas">
        <div class="glow-1"></div>
    </div>

    @include('layouts.dashboard.sidebar')

    <main class="main-wrapper" id="main-wrapper">
        @include('layouts.dashboard.nav-dock')

        <div class="max-w-6xl w-full mx-auto px-4">
            @yield('content')
        </div>
    </main>

    <script>
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('nav-dock');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
                nav.style.transform = 'scale(0.95) translateY(-5px)';
            } else {
                nav.classList.remove('scrolled');
                nav.style.transform = 'scale(1) translateY(0)';
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const wrapper = document.getElementById('main-wrapper');
            sidebar.classList.toggle('sidebar-collapsed');
            wrapper.classList.toggle('wrapper-expanded');
        }

        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const hamburger = document.getElementById('hamburger');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('active');
            hamburger.classList.toggle('menu-open');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : 'auto';
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
            document.documentElement.classList.toggle('dark', savedTheme === 'dark');
        };
    </script>
</body>

</html>