@extends('layouts.auth')

@section('title', 'تسجيل الدخول')

@section('theme_toggle')
<button onclick="toggleTheme()" class="absolute top-6 left-6 z-50 p-3 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 shadow-xl hover:scale-110 active:scale-95 transition-all duration-300 group">
    <svg id="theme-toggle-dark-icon" class="w-5 h-5 text-orange-500 dark:hidden" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path>
    </svg>
    <svg id="theme-toggle-light-icon" class="w-5 h-5 text-indigo-400 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
    </svg>
</button>

<style>
    @keyframes scroll-ar {
        0% {
            transform: translate(-50%, 0);
            opacity: 0;
        }

        20% {
            opacity: 1;
        }

        100% {
            transform: translate(-50%, 15px);
            opacity: 0;
        }
    }

    .animate-scroll-ar {
        animation: scroll-ar 2s cubic-bezier(0.15, 0.41, 0.69, 0.94) infinite;
    }

    .eye-transition {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .eye-hidden {
        transform: scale(0.5) rotate(15deg);
        opacity: 0;
    }
</style>
@endsection

@section('form_header')
<div class="text-center w-full">
    <h1 class="text-3xl font-[900] text-slate-900 dark:text-white mb-3">أهلاً بعودتك!</h1>
    <p class="text-slate-500 dark:text-zinc-400 font-medium text-sm lg:text-base">سجل دخولك لتكمل ما بدأت في عالم الأعمال.</p>
</div>
@endsection

@section('content')
@if (session('status'))
<div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-600 dark:text-emerald-400 font-bold text-sm text-center">
    {{ session('status') }}
</div>
@endif

<form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf
    <div class="space-y-2 text-right">
        <label class="text-sm font-bold text-slate-700 dark:text-zinc-300 px-1 block">البريد الإلكتروني</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com" dir="ltr"
            class="w-full px-5 py-4 bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-600 font-medium text-right" />
        @error('email')
        <p class="text-red-500 text-xs mt-1 font-bold">عذرًا، البريد الإلكتروني أو كلمة المرور غير صحيحة.</p>
        @enderror
    </div>

    <div class="space-y-2 text-right">
        <div class="flex justify-between items-center px-1">
            <label class="text-sm font-bold text-slate-700 dark:text-zinc-300">كلمة المرور</label>
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-xs font-bold text-primary hover:underline">نسيت الكلمة؟</a>
            @endif
        </div>
        <div class="relative group">
            <input id="password" type="password" name="password" required placeholder="••••••••" dir="ltr"
                class="w-full pl-14 pr-5 py-4 bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-600 font-medium text-right" />
            <button type="button" onclick="togglePasswordVisibility()" class="absolute left-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 hover:text-primary transition-colors z-10">
                <svg id="eye-icon" class="w-6 h-6 eye-transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
    </div>

    <div class="flex items-center gap-2 px-1 justify-start">
        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer">
        <label for="remember_me" class="text-sm font-bold text-slate-500 dark:text-zinc-400 cursor-pointer select-none">تذكرني على هذا الجهاز</label>
    </div>

    <button type="submit" id="submit-btn" class="w-full flex items-center justify-center bg-primary text-white py-4 rounded-2xl font-black text-lg shadow-[0_10px_30px_rgba(244,80,24,0.3)] hover:scale-[1.01] active:scale-[0.98] transition-all duration-300">
        <span id="btn-text">دخول للمنصة</span>
        <div id="btn-loader" class="loader hidden"></div>
    </button>

    <div class="relative py-4">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200 dark:border-zinc-800"></div>
        </div>
        <div class="relative flex justify-center text-xs uppercase"><span class="bg-white dark:bg-zinc-950 px-2 text-slate-500 font-bold">أو استمر عبر</span></div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('google.login') }}" class="flex items-center justify-center gap-2 py-3 px-4 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all font-bold text-sm text-slate-700 dark:text-zinc-300">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
            Google
        </a>
        <a href="{{ route('facebook.login') }}" class="flex items-center justify-center gap-2 py-3 px-4 bg-[#1877F2] text-white rounded-2xl hover:opacity-90 transition-all font-bold text-sm shadow-lg shadow-blue-500/20">
            <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" class="w-5 h-5 brightness-0 invert" alt="Facebook">
            Facebook
        </a>
    </div>

    <p class="text-center text-slate-500 dark:text-zinc-500 font-bold text-sm pt-4">
        ليس لديك حساب؟
        <a href="{{ route('register') }}" class="text-primary hover:underline underline-offset-4">ابدأ مجاناً الآن</a>
    </p>
</form>
@endsection

@section('visuals')
<div class="min-h-[100vh] lg:min-h-0 flex flex-col items-center justify-center w-full py-8 relative">
    <div class="fade-in flex flex-col items-center text-center px-6" style="animation-delay: 0.3s">
        <h2 class="text-4xl lg:text-6xl font-semibold tracking-tight text-slate-800 dark:text-zinc-100 leading-tight mb-4">
            سجل دخولك...
            <span class="block mt-4 pb-4 font-[900] glow-text drop-shadow-[0_0_15px_rgba(244,80,24,0.3)]">عالمك في انتظارك.</span>
        </h2>
        <p class="text-base lg:text-lg font-medium text-slate-400 dark:text-zinc-500 max-w-md mb-8 px-4">
            سواء كنت تدير أعمالك أو تتابع عملائك، نحن هنا لنجعل الأمر أسرع.
        </p>
    </div>

    <div class="floating relative inline-block w-full max-w-[280px] lg:max-w-sm mx-auto mb-10">
        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-6 lg:p-8 rounded-[2rem] lg:rounded-[2.5rem] shadow-2xl">
            <div class="w-16 h-16 lg:w-20 h-20 bg-primary/10 rounded-2xl lg:rounded-3xl mx-auto mb-6 flex items-center justify-center">
                <svg class="w-8 h-8 lg:w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <div class="space-y-3">
                <div class="w-3/4 h-3 bg-slate-100 dark:bg-zinc-800 rounded-full mx-auto"></div>
                <div class="w-1/2 h-2 bg-slate-50 dark:bg-zinc-800/50 rounded-full mx-auto"></div>
            </div>
        </div>
    </div>

    <div id="scroll-indicator" class="lg:hidden flex flex-col items-center gap-2 transition-all duration-500 mt-4">
        <div class="w-[22px] h-[36px] border-2 border-slate-300 dark:border-zinc-700 rounded-full relative">
            <div class="w-1 h-2 bg-primary rounded-full absolute top-2 left-1/2 -translate-x-1/2 animate-scroll-ar"></div>
        </div>
        <span class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">اسحب للأسفل</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);

    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        icon.classList.add('eye-hidden');
        setTimeout(() => {
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
            icon.classList.remove('eye-hidden');
        }, 120);
    }

    window.addEventListener('scroll', function() {
        const indicator = document.getElementById('scroll-indicator');
        if (indicator) {
            if (window.scrollY > 60) {
                indicator.style.opacity = '0';
            } else {
                indicator.style.opacity = '1';
            }
        }
    });

    handleFormSubmit('login-form', 'submit-btn', 'btn-text', 'btn-loader');
</script>
@endpush