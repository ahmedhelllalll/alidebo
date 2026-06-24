@extends('admin.layouts.admin')
@section('title', __('admin.profile') ?? 'Profile')
@section('content')
<div class="space-y-6 lg:space-y-8 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.profile') ?? 'Profile Settings' }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.update_profile_desc') ?? 'Manage your personal information and password.' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        {{-- Profile Information Form --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md p-6 sm:p-8 rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative reveal-item">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <i class="fa-solid fa-user-pen text-lg"></i>
                    </div>
                    <h2 class="text-xl font-[900] text-slate-900 dark:text-white">{{ __('admin.profile_information') ?? 'Profile Information' }}</h2>
                </div>

                <form method="post" action="{{ route('admin.profile.update') }}" class="space-y-6" id="profileForm">
                    @csrf
                    @method('patch')

                    <div class="space-y-2 group">
                        <label for="name" class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300">{{ __('admin.name') ?? 'Name' }}</label>
                        <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required autocomplete="name"
                            class="w-full bg-slate-50 dark:bg-[#09090b] border border-slate-200 dark:border-zinc-800 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl px-4 py-3 text-[14px] font-semibold text-slate-900 dark:text-white transition-all shadow-sm">
                        @error('name')<p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2 group">
                        <label for="email" class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300">{{ __('admin.email') ?? 'Email Address' }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required autocomplete="email"
                            class="w-full bg-slate-50 dark:bg-[#09090b] border border-slate-200 dark:border-zinc-800 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl px-4 py-3 text-[14px] font-semibold text-slate-900 dark:text-white transition-all shadow-sm">
                        @error('email')<p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="button" onclick="submitForm('profileForm', this)" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-[900] text-[14px] hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-md relative overflow-hidden">
                            <i class="fa-solid fa-save"></i>
                            {{ __('admin.save') ?? 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Update Password Form --}}
            <div class="bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md p-6 sm:p-8 rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative reveal-item" style="animation-delay: 0.1s;">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center">
                        <i class="fa-solid fa-lock text-lg"></i>
                    </div>
                    <h2 class="text-xl font-[900] text-slate-900 dark:text-white">{{ __('admin.update_password') ?? 'Update Password' }}</h2>
                </div>

                <form method="post" action="{{ route('admin.profile.password') }}" class="space-y-6" id="passwordForm">
                    @csrf
                    @method('patch')

                    <div class="space-y-2 group">
                        <label for="current_password" class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300">{{ __('admin.current_password') ?? 'Current Password' }}</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                            class="w-full bg-slate-50 dark:bg-[#09090b] border border-slate-200 dark:border-zinc-800 focus:border-orange-500/50 focus:ring-4 focus:ring-orange-500/10 rounded-xl px-4 py-3 text-[14px] font-semibold text-slate-900 dark:text-white transition-all shadow-sm">
                        @error('current_password', 'updatePassword')<p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2 group">
                            <label for="password" class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300">{{ __('admin.new_password') ?? 'New Password' }}</label>
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                class="w-full bg-slate-50 dark:bg-[#09090b] border border-slate-200 dark:border-zinc-800 focus:border-orange-500/50 focus:ring-4 focus:ring-orange-500/10 rounded-xl px-4 py-3 text-[14px] font-semibold text-slate-900 dark:text-white transition-all shadow-sm">
                            @error('password', 'updatePassword')<p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-2 group">
                            <label for="password_confirmation" class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300">{{ __('admin.confirm_password') ?? 'Confirm Password' }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                class="w-full bg-slate-50 dark:bg-[#09090b] border border-slate-200 dark:border-zinc-800 focus:border-orange-500/50 focus:ring-4 focus:ring-orange-500/10 rounded-xl px-4 py-3 text-[14px] font-semibold text-slate-900 dark:text-white transition-all shadow-sm">
                            @error('password_confirmation', 'updatePassword')<p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="button" onclick="submitForm('passwordForm', this)" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-[900] text-[14px] hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-md relative overflow-hidden">
                            <i class="fa-solid fa-key"></i>
                            {{ __('admin.update_password') ?? 'Update Password' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar Stats Card --}}
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-primary to-orange-500 p-[1.5px] rounded-[24px] shadow-lg shadow-primary/20 relative reveal-item" style="animation-delay: 0.2s;">
                <div class="bg-white dark:bg-[#121214] rounded-[22px] p-6 text-center relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-2xl"></div>
                    
                    <div class="w-24 h-24 mx-auto bg-slate-50 dark:bg-zinc-800 rounded-full flex items-center justify-center border-4 border-white dark:border-zinc-900 shadow-md relative z-10">
                        <i class="fa-solid fa-user-shield text-4xl text-primary"></i>
                    </div>
                    
                    <h3 class="mt-4 text-lg font-[900] text-slate-900 dark:text-white tracking-tight">{{ auth()->user()->name }}</h3>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400 mt-1">{{ auth()->user()->email }}</p>
                    
                    <div class="mt-6 pt-6 border-t border-slate-100 dark:border-white/5 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('admin.role') ?? 'Role' }}</span>
                            <span class="text-xs font-black text-primary uppercase tracking-widest bg-primary/10 px-2 py-1 rounded-md">{{ __('admin.administrator') ?? 'Administrator' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('admin.joined') ?? 'Joined' }}</span>
                            <span class="text-sm font-bold text-slate-700 dark:text-zinc-300">{{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    if (typeof gsap !== 'undefined') {
        gsap.fromTo('.reveal-item', 
            { opacity: 0, y: 20 }, 
            { opacity: 1, y: 0, duration: 0.5, stagger: 0.1, ease: 'back.out(1.5)' }
        );
    }
</script>
@endpush
@endsection
