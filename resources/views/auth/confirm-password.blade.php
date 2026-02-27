@extends('layouts.auth')

@section('title', 'تأكيد الهوية')

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
    /* Custom Checkbox Style */
    .custom-checkbox {
        appearance: none;
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        background: transparent;
        cursor: pointer;
        position: relative;
        transition: all 0.2s;
    }
    .dark .custom-checkbox { border-color: #27272a; }
    .custom-checkbox:checked {
        background-color: #f45018;
        border-color: #f45018;
    }
    .custom-checkbox:checked::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 2px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
</style>
@endsection

@section('form_header')
<div class="text-center lg:text-right">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-black mb-4 uppercase tracking-widest border border-amber-500/20">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-3V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2v-3z"></path></svg>
        نظام التحقق النشط
    </div>

    <h1 class="text-3xl font-[900] text-slate-900 dark:text-white mb-4">تأكيد الهوية</h1>
    
    <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-zinc-900 rounded-xl mb-6 border border-slate-200 dark:border-zinc-800">
        <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
        <span class="text-sm font-bold text-slate-600 dark:text-zinc-400" dir="ltr">{{ auth()->user()->email }}</span>
    </div>

    <p class="text-slate-500 dark:text-zinc-400 font-semibold text-base leading-relaxed">
        أنت تحاول الدخول لمنطقة حساسة. يرجى إدخال كلمة المرور الخاصة بك للتأكد من هويتك.
    </p>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('password.confirm') }}" id="confirm-form" class="space-y-6">
    @csrf

    <div class="space-y-2 text-right">
        <label for="password" class="block text-sm font-black text-slate-700 dark:text-zinc-300 mr-2">كلمة المرور</label>
        <input id="password" type="password" name="password" required autocomplete="current-password" dir="ltr" placeholder="••••••••"
            class="block w-full px-5 py-4 bg-slate-50 dark:bg-zinc-900 border-2 border-slate-100 dark:border-zinc-800 rounded-2xl text-slate-900 dark:text-white font-bold outline-none focus:border-primary transition-all text-left" />
        
        @if($errors->has('password'))
            <p class="mt-2 mr-2 font-bold text-xs text-red-500 text-right">
                كلمة المرور التي أدخلتها غير صحيحة.
            </p>
        @endif
    </div>

    {{-- الـ Checkbox الموحد --}}
    <div class="flex items-center gap-3 px-1 justify-start group cursor-pointer" onclick="document.getElementById('show_passwords').click()">
        <input id="show_passwords" type="checkbox" onclick="event.stopPropagation(); togglePasswordVisibility()" class="custom-checkbox">
        <label class="text-sm font-bold text-slate-500 dark:text-zinc-400 cursor-pointer select-none group-hover:text-primary transition-colors">إظهار كلمة المرور</label>
    </div>

    <div class="pt-2">
        <button type="submit" id="submit-btn" class="w-full flex items-center justify-center bg-primary text-white py-4 rounded-2xl font-black text-lg shadow-[0_10px_30px_rgba(244,80,24,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
            <span id="btn-text">تأكيد الهوية</span>
            <div id="btn-loader" class="loader hidden"></div>
        </button>
    </div>
</form>

<div class="mt-8 text-center">
    <button onclick="window.history.back()" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 font-bold text-sm transition-all underline underline-offset-4 decoration-2">
        إلغاء والعودة للخلف
    </button>
</div>
@endsection

@section('visuals')
<div class="min-h-[100vh] lg:min-h-0 flex flex-col items-center justify-center w-full py-8 relative text-center">
    <div class="fade-in">
        {{-- الـ Floating Div الموحد مع باقي الصفحات --}}
        <div class="floating relative inline-block mb-10">
            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
                <div class="w-20 h-20 bg-primary/10 rounded-3xl flex items-center justify-center mx-auto mb-6 text-primary">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path>
                    </svg>
                </div>
                <div class="space-y-3">
                    <div class="w-32 h-3 bg-slate-100 dark:bg-zinc-800 rounded-full mx-auto"></div>
                    <div class="w-20 h-2 bg-slate-50 dark:bg-zinc-800/50 rounded-full mx-auto"></div>
                </div>
            </div>
        </div>

        <h2 class="text-4xl lg:text-5xl font-semibold tracking-tight text-slate-800 dark:text-zinc-100 leading-tight mb-6">
            خصوصيتك هي 
            <span class="block mt-4 pb-4 font-[900] glow-text">أمانك الرقمي الأول.</span>
        </h2>
        
        <p class="text-lg font-medium text-slate-400 dark:text-zinc-500 max-w-sm mx-auto">
            نطلب التأكيد الإضافي لضمان حماية معلوماتك الشخصية من أي وصول غير مصرح به.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const checkbox = document.getElementById('show_passwords');
        passwordInput.type = checkbox.checked ? 'text' : 'password';
    }

    handleFormSubmit('confirm-form', 'submit-btn', 'btn-text', 'btn-loader');
</script>
@endpush    