@extends('layouts.auth')

@section('title', 'استعادة كلمة المرور')

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
        0% { transform: translate(-50%, 0); opacity: 0; }
        20% { opacity: 1; }
        100% { transform: translate(-50%, 15px); opacity: 0; }
    }
    .animate-scroll-ar { animation: scroll-ar 2s cubic-bezier(0.15, 0.41, 0.69, 0.94) infinite; }
</style>
@endsection

@section('form_header')
<div class="text-center lg:text-right">
    <h1 class="text-3xl font-[900] text-slate-900 dark:text-white mb-4">نسيت كلمة المرور؟</h1>
    <p class="text-slate-500 dark:text-zinc-400 font-semibold text-base leading-relaxed">
        لا تقلق، هذا يحدث لأفضل المحترفين. فقط أدخل بريدك الإلكتروني وسنرسل لك رابطاً لتعيين كلمة مرور جديدة فوراً.
    </p>
</div>
@endsection

@section('content')
@if (session('status'))
<div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-600 dark:text-emerald-400 font-bold text-sm flex items-center gap-3">
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
    </svg>
    <span>
        @if(session('status') == 'We have emailed your password reset link.')
            تفقد بريدك الآن! أرسلنا لك رابط استعادة الوصول لحسابك.
        @else
            {{ session('status') }}
        @endif
    </span>
</div>
@endif

<form id="reset-form" method="POST" action="{{ route('password.email') }}" class="space-y-6">
    @csrf

    <div class="space-y-2">
        <label for="email" class="block text-sm font-black text-slate-700 dark:text-zinc-300 mr-2">البريد الإلكتروني المسجل</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus dir="ltr" placeholder="name@company.com"
            class="block w-full px-5 py-4 bg-slate-50 dark:bg-zinc-900 border-2 border-slate-100 dark:border-zinc-800 rounded-2xl text-slate-900 dark:text-white font-bold outline-none focus:border-primary dark:focus:border-primary transition-all text-left placeholder:text-right lg:placeholder:text-left" />
        
        @if($errors->has('email'))
            <p class="mt-2 mr-2 font-bold text-xs text-red-500">
                @if($errors->first('email') == "We can't find a user with that email address.")
                    عذراً، لا يوجد حساب مسجل بهذا البريد في alidebo.
                @elseif($errors->first('email') == "Please wait before retrying.")
                    يرجى الانتظار قليلاً قبل المحاولة مرة أخرى.
                @else
                    {{ $errors->first('email') }}
                @endif
            </p>
        @endif
    </div>

    <button type="submit" id="submit-btn" class="w-full flex items-center justify-center bg-primary text-white py-4 rounded-2xl font-black text-lg shadow-[0_10px_30px_rgba(244,80,24,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
        <span id="btn-text">إرسال رابط الاستعادة</span>
        <div id="btn-loader" class="loader hidden"></div>
    </button>

    <div class="mt-10 text-center">
        <a href="{{ route('login') }}" class="text-slate-400 hover:text-primary dark:hover:text-primary font-bold text-sm transition-all flex items-center justify-center gap-2 group">
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7 7l-7-7 7-7"></path>
            </svg>
            العودة لتسجيل الدخول
        </a>
    </div>
</form>
@endsection

@section('visuals')
<div class="min-h-[100vh] lg:min-h-0 flex flex-col items-center justify-center w-full py-8 relative text-center">
    <div class="fade-in" style="animation-delay: 0.2s">
        <h2 class="text-4xl lg:text-5xl font-semibold tracking-tight text-slate-800 dark:text-zinc-100 leading-tight mb-8">
            أمان حسابك هو
            <span class="block mt-4 pb-4 font-[900] glow-text">أولويتنا القصوى في alidebo</span>
        </h2>
    </div>

    <div class="floating mt-12 inline-block relative">
        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
            <div class="w-20 h-20 bg-primary/10 rounded-3xl flex items-center justify-center mx-auto mb-6 text-primary">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <div class="space-y-3">
                <div class="w-40 h-3 bg-slate-100 dark:bg-zinc-800 rounded-full mx-auto"></div>
                <div class="w-24 h-2 bg-slate-50 dark:bg-zinc-800/50 rounded-full mx-auto"></div>
            </div>
        </div>
    </div>

    <p class="mt-16 text-slate-400 dark:text-zinc-500 font-medium max-w-sm mx-auto">
        نحن نستخدم أحدث تقنيات التشفير لضمان أن بيانات شركتك في أمان تام دائماً.
    </p>

    <div id="scroll-indicator" class="lg:hidden flex flex-col items-center gap-2 transition-all duration-500 mt-12">
        <div class="w-[22px] h-[36px] border-2 border-slate-300 dark:border-zinc-700 rounded-full relative">
            <div class="w-1 h-2 bg-primary rounded-full absolute top-2 left-1/2 -translate-x-1/2 animate-scroll-ar"></div>
        </div>
        <span class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">اسحب للأسفل</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const scrollIndicator = document.getElementById('scroll-indicator');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            scrollIndicator.style.opacity = '0';
            scrollIndicator.style.transform = 'translateY(20px)';
        } else {
            scrollIndicator.style.opacity = '1';
            scrollIndicator.style.transform = 'translateY(0)';
        }
    });

    handleFormSubmit('reset-form', 'submit-btn', 'btn-text', 'btn-loader');
</script>
@endpush