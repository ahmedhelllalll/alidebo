<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | alidebo</title>

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
                        primary: '#f45018',
                        error: '#ef4444'
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
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glow-text {
            background: linear-gradient(to left, #f45018, #fb923c, #f45018);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
        }

        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        .floating {
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .loader {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid #fff;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-white dark:bg-[#09090b] transition-colors duration-500 overflow-x-hidden text-right">
    @yield('theme_toggle')

    <main class="flex flex-col lg:flex-row min-h-screen text-center">
        <section class="w-full lg:w-[45%] flex flex-col justify-center px-8 py-12 lg:px-16 relative z-10 bg-white dark:bg-zinc-950 border-l border-slate-100 dark:border-zinc-900 order-2 lg:order-1">
            <div class="w-full max-w-md mx-auto fade-in">
                <header class="mb-10 relative">
                    <div class="flex items-center justify-between mb-10">
                        <div class="flex items-center gap-3 group cursor-pointer" onclick="window.location.href='/'">
                            <img src="{{ asset('images/logo.webp') }}" alt="alidebo" class="w-10 h-10 object-contain">
                            <span class="text-3xl font-[900] tracking-tighter text-slate-900 dark:text-white">alidebo</span>
                        </div>
                    </div>
                    @yield('form_header')
                </header>
                @yield('content')
            </div>
        </section>

        <section class="w-full lg:w-[55%] flex flex-col items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 dark:from-[#0c0c0e] dark:to-[#121215] relative overflow-hidden text-center order-1 lg:order-2">
    <div class="absolute top-[-10%] right-[-10%] w-[70%] h-[70%] bg-primary/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[60%] h-[60%] bg-indigo-500/5 rounded-full blur-[100px] pointer-events-none"></div>
    
    <div class="relative z-10 w-full max-w-2xl mx-auto px-8 lg:px-12">
        @yield('visuals')
    </div>
</section> 
    </main>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateIcons();
        }

        function updateIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');

            if (lightIcon) lightIcon.classList.toggle('hidden', !isDark);
            if (darkIcon) darkIcon.classList.toggle('hidden', isDark);
        }

        function handleFormSubmit(formId, btnId, textId, loaderId) {
            const form = document.getElementById(formId);
            if (form) {
                form.addEventListener('submit', () => {
                    document.getElementById(btnId).disabled = true;
                    document.getElementById(textId).classList.add('hidden');
                    document.getElementById(loaderId).classList.remove('hidden');
                });
            }
        }
        updateIcons();
    </script>
    @stack('scripts')
</body>

</html>