@extends('layouts.auth')

@section('title', 'تعيين كلمة المرور الجديدة')

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
    <h1 class="text-3xl font-[900] text-slate-900 dark:text-white mb-4">خطوة واحدة متبقية</h1>
    <p class="text-slate-500 dark:text-zinc-400 font-semibold text-base leading-relaxed">
        قم بإنشاء كلمة مرور قوية وجديدة لضمان أمان حسابك والعودة لمتابعة أعمالك.
    </p>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('password.store') }}" id="reset-form" class="space-y-5">
    @csrf

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    {{-- البريد الإلكتروني --}}
    <div class="space-y-2">
        <label class="block text-sm font-black text-slate-700 dark:text-zinc-300 text-right mr-2">البريد الإلكتروني</label>
        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required readonly dir="ltr" 
            class="block w-full px-5 py-4 bg-slate-100 dark:bg-zinc-900/50 border-2 border-slate-200 dark:border-zinc-800 rounded-2xl text-slate-400 dark:text-zinc-500 font-bold outline-none cursor-not-allowed text-left" />
    </div>

    {{-- كلمة المرور الجديدة --}}
    <div class="space-y-2 text-right">
        <label for="password" class="block text-sm font-black text-slate-700 dark:text-zinc-300 mr-2">كلمة المرور الجديدة</label>
        <input id="password" type="password" name="password" required autofocus dir="ltr" placeholder="••••••••"
            class="block w-full px-5 py-4 bg-slate-50 dark:bg-zinc-900 border-2 border-slate-100 dark:border-zinc-800 rounded-2xl text-slate-900 dark:text-white font-bold outline-none focus:border-primary transition-all text-left" />
        @if($errors->has('password'))
            <p class="mt-2 font-bold text-xs text-red-500">
                @if(str_contains($errors->first('password'), 'at least 8 characters'))
                    كلمة المرور يجب ألا تقل عن 8 أحرف.
                @elseif(str_contains($errors->first('password'), 'confirmation does not match'))
                    كلمة المرور وتأكيدها غير متطابقين.
                @else
                    {{ $errors->first('password') }}
                @endif
            </p>
        @endif
    </div>

    {{-- تأكيد كلمة المرور --}}
    <div class="space-y-2 text-right">
        <label for="password_confirmation" class="block text-sm font-black text-slate-700 dark:text-zinc-300 mr-2">تأكيد كلمة المرور</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required dir="ltr" placeholder="••••••••"
            class="block w-full px-5 py-4 bg-slate-50 dark:bg-zinc-900 border-2 border-slate-100 dark:border-zinc-800 rounded-2xl text-slate-900 dark:text-white font-bold outline-none focus:border-primary transition-all text-left" />
    </div>

    {{-- الـ Checkbox المطلوب --}}
    <div class="flex items-center gap-3 px-1 justify-start group cursor-pointer" onclick="document.getElementById('show_passwords').click()">
        <input id="show_passwords" type="checkbox" onclick="event.stopPropagation(); toggleAllPasswords()" class="custom-checkbox">
        <label class="text-sm font-bold text-slate-500 dark:text-zinc-400 cursor-pointer select-none group-hover:text-primary transition-colors">إظهار كلمات المرور</label>
    </div>

    <div class="pt-4">
        <button type="submit" id="submit-btn" class="w-full flex items-center justify-center bg-primary text-white py-4 rounded-2xl font-black text-lg shadow-[0_10px_30px_rgba(244,80,24,0.3)] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
            <span id="btn-text">تحديث كلمة المرور والبدء</span>
            <div id="btn-loader" class="loader hidden"></div>
        </button>
    </div>
</form>
@endsection

@section('visuals')
<div class="min-h-[100vh] lg:min-h-0 flex flex-col items-center justify-center w-full py-8 text-center">
    <div class="fade-in">
        <h2 class="text-4xl lg:text-5xl font-semibold tracking-tight text-slate-800 dark:text-zinc-100 leading-tight mb-8">
            كلمة مرور أقوى تعني
            <span class="block mt-4 pb-4 font-[900] glow-text">أمان لا يهتز لأعمالك.</span>
        </h2>
    </div>
    
    <div class="floating mt-12 inline-block">
        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-12 rounded-[3.5rem] shadow-2xl">
            <div class="w-24 h-24 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-500">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div class="space-y-3">
                <div class="w-48 h-3 bg-slate-100 dark:bg-zinc-800 rounded-full mx-auto"></div>
                <div class="w-32 h-3 bg-slate-50 dark:bg-zinc-800/50 rounded-full mx-auto"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAllPasswords() {
        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');
        const checkbox = document.getElementById('show_passwords');
        const type = checkbox.checked ? 'text' : 'password';
        
        password.type = type;
        confirmation.type = type;
    }

    handleFormSubmit('reset-form', 'submit-btn', 'btn-text', 'btn-loader');
</script>
@endpush