@extends('admin.layouts.admin')
@section('title', __('admin.settings') ?? 'Settings')
@section('content')
<div class="space-y-6 lg:space-y-8 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.settings') ?? 'System Settings' }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.update_settings_desc') ?? 'Manage global application configuration and maintenance.' }}</p>
        </div>
    </div>

    <div class="bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md p-6 sm:p-8 rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative reveal-item">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                <i class="fa-solid fa-sliders text-lg"></i>
            </div>
            <h2 class="text-xl font-[900] text-slate-900 dark:text-white">{{ __('admin.general_settings') ?? 'General Settings' }}</h2>
        </div>

        <form method="post" action="{{ route('admin.settings.update') }}" class="space-y-6" id="settingsForm">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-2 group">
                    <label for="site_name" class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300">{{ __('admin.site_name') ?? 'Site Name' }}</label>
                    <input id="site_name" name="site_name" type="text" value="{{ old('site_name', config('app.name')) }}" required
                        class="w-full bg-slate-50 dark:bg-[#09090b] border border-slate-200 dark:border-zinc-800 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl px-4 py-3 text-[14px] font-semibold text-slate-900 dark:text-white transition-all shadow-sm">
                    @error('site_name')<p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2 group">
                    <label for="support_email" class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300">{{ __('admin.support_email') ?? 'Support Email' }}</label>
                    <input id="support_email" name="support_email" type="email" value="{{ old('support_email', 'support@alidebo.com') }}" required
                        class="w-full bg-slate-50 dark:bg-[#09090b] border border-slate-200 dark:border-zinc-800 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl px-4 py-3 text-[14px] font-semibold text-slate-900 dark:text-white transition-all shadow-sm">
                    @error('support_email')<p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-white/5">
                <div class="flex items-center justify-between gap-4 p-4 rounded-xl border border-orange-200/50 dark:border-orange-500/10 bg-orange-50/50 dark:bg-orange-500/5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-500/20 text-orange-500 flex items-center justify-center shrink-0 mt-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="text-[14px] font-[900] text-slate-900 dark:text-white">{{ __('admin.maintenance_mode') ?? 'Maintenance Mode' }}</h3>
                            <p class="text-xs font-medium text-slate-500 dark:text-zinc-400 mt-1">{{ __('admin.maintenance_desc') ?? 'When active, the public application will show a maintenance screen.' }}</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer" {{ old('maintenance_mode', app()->isDownForMaintenance() ? 1 : 0) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-orange-500"></div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 dark:border-white/5 mt-8">
                <button type="button" onclick="submitForm('settingsForm', this)" class="inline-flex items-center gap-2 px-8 py-3.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98] relative overflow-hidden">
                    <i class="fa-solid fa-save"></i>
                    {{ __('admin.save_settings') ?? 'Save Settings' }}
                </button>
            </div>
        </form>
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
