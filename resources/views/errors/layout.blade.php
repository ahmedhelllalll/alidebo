<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | AliDebo</title>

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { 
                        primary: "<?php echo $__env->yieldContent('primary_color', '#f45018'); ?>" 
                    },
                    fontFamily: { cairo: ['Cairo', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        * { transition: background-color .4s ease, color .4s ease, border-color .4s ease; }
        body { font-family: 'Cairo', sans-serif; }
        .fade-in { animation: fadeIn 0.8s ease-out forwards; opacity: 0; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        
        .glow-text {
            background: linear-gradient(to left, <?php echo $__env->yieldContent('glow_colors'); ?>);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
        }
        @keyframes shine { to { background-position: 200% center; } }
        
        .floating { animation: <?php echo $__env->yieldContent('float_animation'); ?>; }
        @keyframes floating_scale { 0%, 100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-20px) scale(1.02); } }
        
        /* أنيميشن الماوس */
        @keyframes scroll-ar {
            0% { transform: translate(-50%, 0); opacity: 0; }
            20% { opacity: 1; }
            100% { transform: translate(-50%, 15px); opacity: 0; }
        }
        .animate-scroll-ar { animation: scroll-ar 2s cubic-bezier(0.15, 0.41, 0.69, 0.94) infinite; }

        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .dark .glass-card { background: rgba(24, 24, 27, 0.65); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-[#09090b] text-slate-900 dark:text-zinc-100 transition-colors duration-500 min-h-screen overflow-x-hidden text-right">
    
    <button id="themeToggleBtn" class="fixed top-6 left-6 z-50 p-3 rounded-full glass-card shadow-lg hover:scale-110 transition-transform group" aria-label="Toggle Theme">
        <svg id="themeToggleLightIcon" class="w-5 h-5 @yield('toggle_icon_color') dark:hidden group-hover:rotate-45 transition-transform" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path>
        </svg>
        <svg id="themeToggleDarkIcon" class="w-5 h-5 text-indigo-400 hidden dark:block group-hover:-rotate-45 transition-transform" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
    </button>

    <main class="flex flex-col lg:flex-row min-h-screen">
        @yield('content')
    </main>

    <script>
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeToggleDarkIcon = document.getElementById('themeToggleDarkIcon');
        const themeToggleLightIcon = document.getElementById('themeToggleLightIcon');

        function updateIcons() {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleLightIcon?.classList.remove('hidden');
                themeToggleDarkIcon?.classList.add('hidden');
            } else {
                themeToggleDarkIcon?.classList.remove('hidden');
                themeToggleLightIcon?.classList.add('hidden');
            }
        }
        updateIcons();

        themeToggleBtn.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateIcons();
        });

        // إخفاء الـ Scroll Indicator عند النزول
        window.addEventListener('scroll', () => {
            const indicator = document.getElementById('scroll-indicator');
            if (indicator) {
                indicator.style.opacity = window.scrollY > 50 ? '0' : '1';
            }
        });
    </script>
</body>
</html>