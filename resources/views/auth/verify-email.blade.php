@extends('layouts.auth')

@section('title', 'تأكيد البريد الإلكتروني')

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
<div class="text-center w-full">
    <h1 class="text-3xl font-[900] text-slate-900 dark:text-white mb-4">تأكيد الحساب</h1>
    <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-zinc-900 rounded-xl mb-6 border border-slate-200 dark:border-zinc-800">
        <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
        <span class="text-sm font-bold text-slate-600 dark:text-zinc-400" dir="ltr">{{ auth()->user()->email }}</span>
    </div>
    <p class="text-slate-500 dark:text-zinc-400 font-semibold text-base leading-relaxed px-4">
        لقد أرسلنا رابط التفعيل إلى بريدك الإلكتروني الموضح أعلاه. يرجى الضغط عليه لتفعيل حسابك.
    </p>
</div>
@endsection

@section('content')
@if (session('status') == 'verification-link-sent')
    <div class="mb-8 p-5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-600 dark:text-emerald-400 font-bold text-sm flex items-start gap-3 animate-pulse">
        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <div class="leading-relaxed text-right">
            تم إرسال الرابط مرة أخرى بنجاح!
            <span class="text-[11px] block mt-1 opacity-80 italic">تفحص صندوق الوارد أو البريد المزعج (Spam).</span>
        </div>
    </div>
@endif

<div class="space-y-6">
    <form id="resend-form" method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" id="submit-btn" class="w-full flex items-center justify-center bg-primary text-white py-4 rounded-2xl font-black text-lg shadow-[0_10px_30px_rgba(244,80,24,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
            <span id="btn-text">إعادة إرسال الرابط</span>
            <div id="btn-loader" class="loader hidden"></div>
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="text-center">
        @csrf
        <button type="submit" class="text-slate-400 hover:text-red-500 dark:hover:text-red-400 font-bold text-sm transition-all underline underline-offset-8 decoration-2">
            تسجيل الخروج
        </button>
    </form>
</div>
@endsection

@section('visuals')
<div class="min-h-[100vh] lg:min-h-0 flex flex-col items-center justify-center w-full py-8 relative text-center">
    <div class="fade-in">
        <div class="inline-flex items-center gap-3 px-4 py-2 rounded-xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 shadow-sm mb-10 mx-auto">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
            </span>
            <span class="text-xs font-black text-slate-700 dark:text-zinc-300">تأكيد أمان</span>
        </div>
        
        <h2 class="text-4xl lg:text-6xl font-semibold tracking-tight text-slate-800 dark:text-zinc-100 leading-tight mb-6">
            ثوانٍ تفصلك عن 
            <span class="block mt-4 pb-4 font-[900] glow-text drop-shadow-[0_0_15px_rgba(244,80,24,0.3)]">
                عالم جديد من الأعمال.
            </span>
        </h2>
        
        <p class="text-lg font-medium text-slate-400 dark:text-zinc-500 max-w-md mx-auto mb-16 px-6">
            فعّل حسابك الآن لتبدأ في تخصيص هويتك الرقمية واستقبال طلبات عملائك.
        </p>
    </div>

    <div class="floating inline-block w-full max-w-[280px] lg:max-w-sm mx-auto mb-10">
        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative">
            <div class="flex items-center gap-5 mb-8">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-primary to-orange-400 flex items-center justify-center border-2 border-white dark:border-zinc-800">
                    <img src="{{ asset('images/logo.webp') }}" class="w-8 h-8 object-contain">
                </div>
                <div class="space-y-3 flex-1 text-right">
                    <div class="w-32 h-3 bg-slate-100 dark:bg-zinc-800 rounded-full"></div>
                    <div class="w-20 h-2 bg-slate-50 dark:bg-zinc-800/50 rounded-full"></div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="w-full h-2.5 bg-slate-100 dark:bg-zinc-800 rounded-full shadow-inner"></div>
                <div class="w-5/6 h-2.5 bg-slate-100 dark:bg-zinc-800 rounded-full shadow-inner"></div>
            </div>
            <div class="absolute -bottom-5 -left-5 w-14 h-14 bg-primary rounded-2xl shadow-lg flex items-center justify-center text-white scale-110 border-4 border-white dark:border-[#09090b]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
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
    const scrollIndicator = document.getElementById('scroll-indicator');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            scrollIndicator.style.opacity = '0';
        } else {
            scrollIndicator.style.opacity = '1';
        }
    });

    handleFormSubmit('resend-form', 'submit-btn', 'btn-text', 'btn-loader');
</script>
@endpush