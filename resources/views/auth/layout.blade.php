    <!DOCTYPE html>
    <html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
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
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
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
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .luxury-shadow {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            }
            .dark .luxury-shadow {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            }
        </style>
        @stack('styles')
    </head>
    <body class="rtl:font-cairo ltr:font-jakarta bg-white dark:bg-[#09090b] transition-colors duration-500 overflow-x-hidden text-start">
        @yield('theme_toggle')
        <main class="flex flex-col lg:flex-row min-h-screen text-center">
            <section class="w-full lg:w-[45%] flex flex-col justify-center px-8 py-12 lg:px-16 relative z-10 bg-white dark:bg-zinc-950 border-e border-slate-100 dark:border-zinc-900 order-2 lg:order-1">
                <div class="w-full max-w-md mx-auto fade-in">
                    <header class="mb-10 relative">
                        <div class="flex items-center justify-between mb-10">
                            <div class="flex items-center gap-3 group cursor-pointer" onclick="window.location.href='/'">
                                <img src="{{ asset('images/logo.webp') }}" alt="alidebo" class="w-10 h-10 object-contain">
                                <span class="text-3xl font-[900] tracking-tighter text-slate-900 dark:text-white">alidebo</span>
                            </div>
                            
                            {{-- Language Switcher --}}
                            <div class="relative" id="langDropdownContainer">
                                <button type="button" onclick="toggleLangDropdown()" id="lang-toggle-btn" aria-label="Toggle language" class="flex items-center gap-1.5 sm:gap-2 px-2.5 py-1.5 rounded-[0.85rem] text-sm font-bold text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/60 border border-transparent hover:border-slate-200 dark:hover:border-zinc-700 transition-all duration-200">
                                    @if(app()->getLocale() == 'en')
                                        <img src="https://flagcdn.com/w40/us.png" alt="EN" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="hidden sm:inline">EN</span>
                                    @elseif(app()->getLocale() == 'ar')
                                        <img src="https://flagcdn.com/w40/eg.png" alt="AR" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="hidden sm:inline">عربي</span>
                                    @elseif(app()->getLocale() == 'es')
                                        <img src="https://flagcdn.com/w40/es.png" alt="ES" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="hidden sm:inline">ES</span>
                                    @elseif(app()->getLocale() == 'de')
                                        <img src="https://flagcdn.com/w40/de.png" alt="DE" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="hidden sm:inline">DE</span>
                                    @elseif(app()->getLocale() == 'zh')
                                        <img src="https://flagcdn.com/w40/cn.png" alt="CN" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="hidden sm:inline">ZH</span>
                                    @elseif(app()->getLocale() == 'tr')
                                        <img src="https://flagcdn.com/w40/tr.png" alt="TR" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="hidden sm:inline">TR</span>
                                    @endif
                                    <svg class="w-3 h-3 opacity-50 transition-transform duration-200" id="langChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                {{-- Dropdown --}}
                                <div id="langDropdownMenu" class="absolute top-full mt-2 end-0 w-44 bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800 rounded-2xl shadow-xl shadow-black/5 dark:shadow-black/30 opacity-0 pointer-events-none scale-95 transition-all duration-200 origin-top z-50 p-1.5 text-start">
                                    <a href="{{ route('lang.switch', 'en') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'en' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                        <img src="https://flagcdn.com/w40/us.png" alt="US" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="flex-1">English</span>
                                        @if(app()->getLocale() == 'en')
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </a>
                                    <a href="{{ route('lang.switch', 'ar') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'ar' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                        <img src="https://flagcdn.com/w40/eg.png" alt="EG" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="flex-1 font-cairo">العربية</span>
                                        @if(app()->getLocale() == 'ar')
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </a>
                                    <a href="{{ route('lang.switch', 'es') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'es' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                        <img src="https://flagcdn.com/w40/es.png" alt="ES" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="flex-1">Español</span>
                                        @if(app()->getLocale() == 'es')
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </a>
                                    <a href="{{ route('lang.switch', 'de') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'de' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                        <img src="https://flagcdn.com/w40/de.png" alt="DE" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="flex-1">Deutsch</span>
                                        @if(app()->getLocale() == 'de')
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </a>
                                    <a href="{{ route('lang.switch', 'zh') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'zh' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                        <img src="https://flagcdn.com/w40/cn.png" alt="CN" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="flex-1">中文 (Chinese)</span>
                                        @if(app()->getLocale() == 'zh')
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </a>
                                    <a href="{{ route('lang.switch', 'tr') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ app()->getLocale() == 'tr' ? 'bg-primary/8 dark:bg-primary/10 text-primary font-bold' : 'text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-zinc-800/50' }}">
                                        <img src="https://flagcdn.com/w40/tr.png" alt="TR" class="w-5 h-3.5 rounded-[3px] object-cover shadow-sm">
                                        <span class="flex-1">Türkçe (Turkish)</span>
                                        @if(app()->getLocale() == 'tr')
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                        @yield('form_header')
                    </header>
                    @yield('content')
                </div>
            </section>
    <section class="w-full lg:w-[55%] flex flex-col items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 dark:from-[#0c0c0e] dark:to-[#121215] relative overflow-hidden text-center order-1 lg:order-2">
        <div class="absolute top-[-10%] end-[-10%] w-[70%] h-[70%] bg-primary/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] start-[-10%] w-[60%] h-[60%] bg-indigo-500/5 rounded-full blur-[100px] pointer-events-none"></div>
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

            // Language Dropdown Logic
            let isLangOpen = false;
            function toggleLangDropdown() {
                isLangOpen = !isLangOpen;
                const menu = document.getElementById('langDropdownMenu');
                const chevron = document.getElementById('langChevron');
                if (!menu) return;
                if (isLangOpen) {
                    menu.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                    menu.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                } else {
                    menu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
                    menu.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                }
            }
            document.addEventListener('click', (e) => {
                const container = document.getElementById('langDropdownContainer');
                if (container && !container.contains(e.target) && isLangOpen) toggleLangDropdown();
            });
        </script>
        @stack('scripts')
    </body>
    </html>