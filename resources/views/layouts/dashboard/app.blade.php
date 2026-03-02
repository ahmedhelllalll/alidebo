<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
            transition: background-color 0.5s;
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }

        html.dark body {
            background: #09090b;
            color: #f4f4f5;
        }

        html:not(.dark) body {
            background: #f8f9fa;
            color: #09090b;
        }

        .sidebar-right {
            position: fixed;
            right: 24px;
            top: 24px;
            bottom: 24px;
            z-index: 1000;
            border-radius: 32px;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(20px);
        }

        .main-wrapper {
            margin-right: 310px;
            padding: 40px 20px;
            min-height: 100vh;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .wrapper-expanded {
            margin-right: 120px;
        }

        @media (max-width: 1024px) {
            .sidebar-right {
                right: -100%;
                top: 0;
                bottom: 0;
                height: 100vh;
                width: 300px !important;
                border-radius: 0;
            }

            .sidebar-right.active {
                right: 0;
            }

            .main-wrapper {
                margin-right: 0 !important;
                padding-bottom: 140px;
            }
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="overlay" onclick="closeMobileMenu()"></div>

    @include('layouts.dashboard.sidebar')

    <main class="main-wrapper" id="main-wrapper">
        <div class="max-w-6xl w-full mx-auto">
            @yield('content')
        </div>
    </main>

    @include('layouts.dashboard.nav-dock')

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const wrapper = document.getElementById('main-wrapper');
            sidebar.classList.toggle('sidebar-collapsed');
            wrapper.classList.toggle('wrapper-expanded');
        }

        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.remove('active');
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
    @if(!auth()->user()->has_completed_onboarding)
    @include('layouts.dashboard.onboarding-modal')
    @endif
</body>

</html>