<div dir="rtl" class="font-cairo antialiased">
    <style>
        /* Cinematic Header Transitions */
        #smart-header {
            transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), 
                        max-width 0.8s cubic-bezier(0.16, 1, 0.3, 1), 
                        padding 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform, max-width, padding;
        }
        
        #header-container {
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: border-radius, background, box-shadow;
        }

        #nav-inner {
            transition: height 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #main-logo {
            transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1), height 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Scrolled State */
        #smart-header.is-scrolled {
            max-width: 900px !important;
            padding-top: 0.75rem !important;
        }

        #smart-header.is-scrolled #header-container {
            border-radius: 2rem !important;
            background: rgba(255, 255, 255, 0.65) !important;
            backdrop-filter: blur(24px) !important;
            -webkit-backdrop-filter: blur(24px) !important;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05) !important;
        }
        
        .dark #smart-header.is-scrolled #header-container {
            background: rgba(9, 9, 11, 0.65) !important;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.5) !important;
        }

        #smart-header.is-scrolled #nav-inner {
            height: 4.5rem !important;
        }

        #smart-header.is-scrolled #main-logo {
            width: 34px !important;
            height: 34px !important;
        }

        /* Hidden State (Scrolling Down) */
        #smart-header.is-hidden {
            transform: translate(-50%, -120%) !important;
        }
    </style>

    <header id="smart-header" 
            class="fixed top-0 left-1/2 -translate-x-1/2 z-[1000] w-full max-w-7xl px-4 pt-4">

        <div class="bg-white/80 dark:bg-zinc-950/80 backdrop-blur-2xl border border-zinc-200/50 dark:border-zinc-800/50 rounded-[2.5rem] shadow-2xl shadow-black/5 overflow-hidden"
             id="header-container">

            <div class="flex items-center justify-between px-10 py-2.5 border-b border-zinc-200/30 dark:border-zinc-800/50 bg-zinc-50/50 dark:bg-white/[0.02]">
                <div class="flex items-center gap-5 text-zinc-400 dark:text-zinc-500">
                    <a href="#" class="hover:text-primary hover:-translate-y-0.5 transition-all duration-300"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                    <a href="#" class="hover:text-primary hover:-translate-y-0.5 transition-all duration-300"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>
                    <a href="#" class="hover:text-primary hover:-translate-y-0.5 transition-all duration-300"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452z"/></svg></a>
                </div>

                <div class="flex items-center gap-4">
                    <div onclick="toggleTheme()" class="cursor-pointer text-zinc-400 hover:text-yellow-500 transition-colors">
                        <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                        <svg class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </div>
                    @auth
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 group">
                        <span class="text-[10px] font-bold text-zinc-500 group-hover:text-primary transition-colors">الملف الشخصي</span>
                        <div class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300/50 dark:border-zinc-700/50 overflow-hidden group-hover:ring-2 ring-primary/20 transition-all">
                             <svg class="w-full h-full p-1 text-zinc-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                    </a>
                    @endauth
                </div>
            </div>

            <div id="nav-inner" class="flex items-center justify-between px-10 h-20">

                <div class="flex items-center w-1/4">
                    <a href="{{ url('/') }}" class="group flex items-center gap-3">
                        <div class="relative">
                            <img src="{{ asset('images/logo.webp') }}" id="main-logo" class="w-11 h-11 object-contain transition-all duration-500 group-hover:scale-110 group-hover:rotate-3" alt="Logo">
                            <div class="absolute inset-0 bg-primary/20 blur-xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        <span class="text-2xl font-[1000] tracking-tighter dark:text-white text-zinc-950 uppercase italic">alidebo</span>
                    </a>
                </div>

                <nav class="hidden lg:flex items-center justify-center gap-10 w-2/4">
                    @foreach(['المميزات' => '#features', 'كيف يعمل' => '#how-it-works', 'الأسعار' => '#pricing', 'الدليل' => '#directory'] as $label => $link)
                    <a href="{{ $link }}" class="text-[13px] font-black text-zinc-500 dark:text-zinc-400 hover:text-primary hover:-translate-y-1 transition-all duration-300 ease-out flex flex-col items-center group">
                        {{ $label }}
                        <span class="w-1 h-1 bg-primary rounded-full opacity-0 group-hover:opacity-100 transition-opacity mt-1"></span>
                    </a>
                    @endforeach
                    @if(isset($navbarPages) && $navbarPages->count() > 0)
                        @foreach($navbarPages as $navPage)
                        <a href="{{ url($navPage->slug) }}" class="text-[13px] font-black text-zinc-500 dark:text-zinc-400 hover:text-primary hover:-translate-y-1 transition-all duration-300 ease-out flex flex-col items-center group">
                            {{ $navPage->title[app()->getLocale()] ?? $navPage->title['en'] }}
                            <span class="w-1 h-1 bg-primary rounded-full opacity-0 group-hover:opacity-100 transition-opacity mt-1"></span>
                        </a>
                        @endforeach
                    @endif
                </nav>

                <div class="hidden lg:flex items-center justify-end gap-5 w-1/4">
                    @auth
                        <a href="{{ route('dashboard') }}" 
                           class="relative inline-flex items-center gap-2 px-7 py-3 overflow-hidden font-black text-white transition-all duration-300 bg-zinc-950 dark:bg-white dark:text-black rounded-2xl group shadow-xl hover:shadow-primary/20">
                            <span class="absolute top-0 right-0 inline-block w-4 h-4 transition-all duration-500 ease-in-out bg-primary rounded group-hover:-mr-4 group-hover:-mt-4">
                                <span class="absolute top-0 right-0 w-5 h-5 rotate-45 translate-x-1/2 -translate-y-1/2 bg-white/20"></span>
                            </span>
                            <span class="relative text-xs tracking-widest uppercase">لوحة التحكم</span>
                            <svg class="w-4 h-4 relative transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-zinc-500 hover:text-primary transition-colors">دخول</a>
                        <a href="{{ route('register') }}" class="group relative px-7 py-3 font-black text-white transition-all duration-300 bg-primary rounded-2xl shadow-lg shadow-primary/25 hover:scale-[1.03] active:scale-95 overflow-hidden">
                            <span class="relative z-10 text-xs uppercase tracking-wider">ابدأ الآن</span>
                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                        </a>
                    @endauth
                </div>

                <div class="lg:hidden p-2 bg-zinc-100 dark:bg-zinc-900 rounded-xl">
                    <svg class="w-6 h-6 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                </div>
            </div>
        </div>
    </header>

    <div class="lg:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[1000] w-[85%] max-w-[380px]">
        <nav class="bg-white/80 dark:bg-zinc-950/80 backdrop-blur-3xl border border-white/20 dark:border-zinc-800/50 rounded-[2.5rem] shadow-2xl p-2">
            <div class="flex items-center justify-around h-14">
                <a href="#features" class="p-3 text-zinc-400 hover:text-primary transition-all"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></a>
                <a href="#pricing" class="p-3 text-zinc-400 hover:text-primary transition-all"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/></svg></a>
                <a href="{{ route('login') }}" class="p-3 bg-primary text-white rounded-2xl shadow-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg></a>
            </div>
        </nav>
    </div>
</div>

<script>
    let lastScroll = 0;
    const header = document.getElementById('smart-header');

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        // Smooth shrink effect
        if (currentScroll > 80) {
            header.classList.add('is-scrolled');
        } else {
            header.classList.remove('is-scrolled');
        }

        // Cinematic hide/show effect
        if (currentScroll > lastScroll && currentScroll > 200) {
            header.classList.add('is-hidden');
        } else {
            header.classList.remove('is-hidden');
        }
        lastScroll = currentScroll;
    }, { passive: true });
</script>