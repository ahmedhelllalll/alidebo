@extends('errors.layout')

@section('title', 'خطأ في الخادم')
@section('primary_color', '#ef4444')
@section('toggle_icon_color', 'text-orange-500')
@section('glow_colors', '#ef4444, #fb923c, #ef4444')
@section('float_animation', 'floating 5s ease-in-out infinite')

@section('content')
    <section class="w-full lg:w-[55%] min-h-screen p-10 lg:p-24 flex flex-col items-center justify-center bg-slate-50 dark:bg-[#0c0c0e] relative overflow-hidden text-center order-1 lg:order-2">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-red-500/10 rounded-full blur-[120px]"></div>
        <div class="relative z-10 w-full max-w-2xl mx-auto flex flex-col items-center">
            <div class="floating inline-block w-full max-w-sm mb-12">
                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-12 rounded-[2.5rem] shadow-2xl relative overflow-hidden min-h-[320px] flex flex-col items-center justify-center">
                    <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[12rem] font-[900] text-slate-300/70 dark:text-zinc-800/50 select-none z-0">500</span>
                    <div class="relative z-10 flex flex-col items-center w-full h-full">
                        <div class="w-24 h-24 bg-red-500/10 rounded-3xl mb-20 flex items-center justify-center backdrop-blur-sm rotate-12">
                            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="w-full mt-auto">
                            <div class="w-1/2 h-2.5 bg-slate-200/40 dark:bg-zinc-800/40 rounded-full mx-auto backdrop-blur-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="scroll-indicator" class="lg:hidden flex flex-col items-center gap-2 transition-all duration-500">
                <div class="w-[20px] h-[32px] border-2 border-slate-300 dark:border-zinc-700 rounded-full relative">
                    <div class="w-1 h-2 bg-red-500 rounded-full absolute top-1.5 left-1/2 -translate-x-1/2 animate-scroll-ar"></div>
                </div>
                <span class="text-[9px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-[0.2em]">انزل للتفاصيل</span>
            </div>
        </div>
    </section>

    <section class="w-full lg:w-[45%] min-h-screen flex flex-col justify-center px-8 py-12 lg:px-16 relative z-10 bg-white dark:bg-zinc-950 border-r lg:border-l border-slate-100 dark:border-zinc-900 order-2 lg:order-1 text-right">
        <div class="w-full max-w-md mx-auto fade-in">
            <header class="mb-10">
                <div class="flex items-center justify-start gap-3 mb-10 group cursor-pointer" onclick="window.location.href='/'">
                    <img src="{{ asset('images/logo.webp') }}" alt="alidebo" class="w-10 h-10 object-contain">
                    <span class="text-3xl font-[900] tracking-tighter text-slate-900 dark:text-white">alidebo</span>
                </div>
                <div class="inline-flex items-center justify-center gap-2 mb-6 px-4 py-2 rounded-full bg-red-500/10 text-red-500 font-bold text-sm">
                    <span>خطأ 500</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-[900] text-slate-900 dark:text-white mb-4 leading-tight">عطل فني <br><span class="glow-text">مفاجئ!</span></h1>
                <p class="text-slate-500 dark:text-zinc-400 font-medium text-lg leading-relaxed">يبدو أن خوادمنا تواجه مشكلة تقنية خارجة عن إرادتنا. فريقنا يعمل بأقصى سرعة لحلها.</p>
            </header>
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <button onclick="window.location.href = window.location.href" class="flex-1 flex items-center justify-center bg-red-600 text-white py-4 px-6 rounded-2xl font-black text-lg shadow-[0_10px_30px_rgba(239,68,68,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">تحديث الصفحة</button>
                <a href="{{ url('/') }}" class="flex-1 flex items-center justify-center bg-slate-100 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-800 py-4 px-6 rounded-2xl font-bold text-lg hover:bg-slate-200 dark:hover:bg-zinc-800 transition-all duration-300">الرئيسية</a>
            </div>
        </div>
    </section>
@endsection