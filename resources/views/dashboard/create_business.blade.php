<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء شركة جديدة | AliDebo</title>

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
                    colors: { primary: '#f45018' },
                    fontFamily: { cairo: ['Cairo', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        * { transition: background-color .4s ease, color .4s ease, border-color .4s ease; }
        body { font-family: 'Cairo', sans-serif; }

        .fade-in { animation: fadeIn 0.8s ease-out forwards; opacity: 0; }
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(15px); } 
            to { opacity: 1; transform: translateY(0); } 
        }

        .glow-text {
            background: linear-gradient(to left, #f45018, #fb923c, #f45018);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
        }
        @keyframes shine { to { background-position: 200% center; } }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .dark .glass-card {
            background: rgba(24, 24, 27, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .loader {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid #fff;
            width: 24px; height: 24px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>

<body class="bg-slate-50 dark:bg-[#09090b] text-slate-900 dark:text-zinc-100 min-h-screen">

    <button onclick="toggleTheme()" class="fixed top-6 left-6 z-50 p-3 rounded-full glass-card shadow-lg hover:scale-110 transition-transform group">
        <svg class="w-5 h-5 text-orange-500 dark:hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path></svg>
        <svg class="w-5 h-5 text-indigo-400 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
    </button>

    <main class="flex flex-col lg:flex-row min-h-screen">
        
        <section class="w-full lg:w-[45%] flex flex-col justify-center px-8 py-12 lg:px-16 bg-white dark:bg-zinc-950 border-l border-slate-100 dark:border-zinc-900 order-2 lg:order-1 relative z-10">
            <div class="w-full max-w-md mx-auto fade-in">
                
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-primary font-bold text-sm mb-10 transition-colors group">
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    العودة للوحة التحكم
                </a>

                <header class="mb-10 text-right">
                    <h1 class="text-3xl font-[900] text-slate-900 dark:text-white mb-3">ابدأ رحلتك الآن 🚀</h1>
                    <p class="text-slate-500 dark:text-zinc-400 font-medium">املأ البيانات الأساسية لإنشاء ملف شركتك الاحترافي.</p>
                </header>

                <form id="create-business-form" method="POST" action="{{ route('business.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-zinc-300 px-1 block text-right">اسم الشركة أو النشاط</label>
                        <input type="text" name="name" required placeholder="مثال: ديبو ديزاين" 
                            class="w-full px-5 py-4 bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl focus:ring-2 focus:ring-primary outline-none transition-all dark:text-white font-medium text-right shadow-sm" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-zinc-300 px-1 block text-right">وصف مختصر</label>
                        <textarea name="description" rows="4" placeholder="ما الذي يميز شركتك؟" 
                            class="w-full px-5 py-4 bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl focus:ring-2 focus:ring-primary outline-none transition-all dark:text-white font-medium text-right shadow-sm resize-none"></textarea>
                    </div>

                    <button type="submit" id="submit-btn" class="w-full flex items-center justify-center bg-primary text-white py-5 rounded-2xl font-black text-lg shadow-[0_10px_30px_rgba(244,80,24,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
                        <span id="btn-text">إنشاء الملف التجاري</span>
                        <div id="btn-loader" class="loader hidden"></div>
                    </button>
                </form>
            </div>
        </section>

        <section class="w-full lg:w-[55%] p-10 lg:p-24 flex flex-col items-center justify-center bg-slate-50 dark:bg-[#0c0c0e] relative overflow-hidden text-center order-1 lg:order-2">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary/10 rounded-full blur-[120px]"></div>
            
            <div class="relative z-10 w-full max-w-2xl mx-auto">
                <div class="fade-in" style="animation-delay: 0.3s">
                    <h2 class="text-4xl lg:text-6xl font-semibold tracking-tight text-slate-800 dark:text-zinc-100 leading-tight mb-6">
                        صمم هويتك...
                        <span class="block mt-4 pb-4 font-[900] glow-text drop-shadow-[0_0_15px_rgba(244,80,24,0.3)]">
                            بلمسة من الإبداع.
                        </span>
                    </h2>
                    <p class="text-lg font-medium text-slate-400 dark:text-zinc-500 max-w-md mx-auto mb-16 italic">
                        "الاسم والوصف هما أول ما يراه عميلك، اجعلهما لا يُنسيان."
                    </p>
                </div>

                <div class="group relative inline-block w-full max-w-sm">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary to-orange-400 rounded-[2.5rem] blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                    <div class="relative bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl">
                        <div class="w-16 h-16 bg-primary/10 rounded-2xl mx-auto mb-6 flex items-center justify-center text-3xl">🏢</div>
                        <div class="space-y-3">
                            <div class="w-3/4 h-3 bg-slate-100 dark:bg-zinc-800 rounded-full mx-auto"></div>
                            <div class="w-1/2 h-3 bg-slate-50 dark:bg-zinc-800/50 rounded-full mx-auto"></div>
                        </div>
                        <div class="mt-8 pt-6 border-t border-slate-50 dark:border-zinc-800/50">
                            <div class="text-[10px] font-black uppercase tracking-widest text-primary">AliDebo Enterprise</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        // سكريبت تبديل الثيم
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }

        // سكريبت التحميل عند الضغط على الزر
        const form = document.getElementById('create-business-form');
        const btn = document.getElementById('submit-btn');
        const loader = document.getElementById('btn-loader');
        const text = document.getElementById('btn-text');

        form.addEventListener('submit', function() {
            btn.disabled = true;
            text.classList.add('hidden');
            loader.classList.remove('hidden');
            btn.classList.add('opacity-80');
        });
    </script>
</body>
</html>