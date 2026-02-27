@extends('errors.layout')

@section('title', 'تسجيل الدخول مطلوب')
@section('primary_color', '#3b82f6')
@section('toggle_icon_color', 'text-blue-500')
@section('glow_colors', '#3b82f6, #60a5fa, #3b82f6')
@section('float_animation', 'floating 5s ease-in-out infinite')

@section('content')
<section class="w-full lg:w-[45%] flex flex-col justify-center px-8 py-12 lg:px-16 relative z-10 bg-white dark:bg-zinc-950 border-l border-slate-100 dark:border-zinc-900 order-2 lg:order-1">
    <div class="w-full max-w-md mx-auto fade-in">
        <header class="mb-10 text-right">
            <div class="inline-flex items-center justify-center gap-2 mb-6 px-4 py-2 rounded-full bg-blue-500/10 text-blue-600 font-bold text-sm">
                <span>خطأ 401</span>
            </div>
            <h1 class="text-4xl lg:text-5xl font-[900] text-slate-900 dark:text-white mb-4 leading-tight">عفواً، هويتك <br><span class="glow-text">مجهولة!</span></h1>
            <p class="text-slate-500 dark:text-zinc-400 font-medium text-lg leading-relaxed">
                لا يمكنك الوصول إلى هذه الصفحة قبل إثبات هويتك. يرجى تسجيل الدخول أولاً للتمتع بصلاحيات الوصول.
            </p>
        </header>

        <div class="flex flex-col sm:flex-row gap-4 mt-8">
            <a href="{{ route('login') }}" class="flex-1 flex items-center justify-center bg-primary text-white py-4 px-6 rounded-2xl font-black text-lg shadow-[0_10px_30px_rgba(59,130,246,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
                تسجيل الدخول
            </a>
            <button onclick="window.history.back()" class="flex-1 flex items-center justify-center bg-slate-100 dark:bg-zinc-900 text-slate-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-800 py-4 px-6 rounded-2xl font-bold text-lg hover:bg-slate-200 dark:hover:bg-zinc-800 transition-all duration-300">
                الرجوع للخلف
            </button>
        </div>
    </div>
</section>

<section class="w-full lg:w-[55%] p-10 lg:p-24 flex flex-col items-center justify-center bg-slate-50 dark:bg-[#0c0c0e] relative overflow-hidden text-center order-1 lg:order-2">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[120px]"></div>
    <div class="relative z-10 w-full max-w-2xl mx-auto">
        <div class="floating inline-block w-full max-w-sm">
            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-12 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[12rem] font-[900] text-slate-50 dark:text-zinc-800/50 select-none z-0">401</span>
                <div class="relative z-10 w-24 h-24 bg-blue-500/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div class="relative z-10 space-y-4">
                    <div class="w-3/4 h-3 bg-slate-200 dark:bg-zinc-800 rounded-full mx-auto"></div>
                    <div class="w-1/2 h-2 bg-slate-100 dark:bg-zinc-800/50 rounded-full mx-auto"></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection