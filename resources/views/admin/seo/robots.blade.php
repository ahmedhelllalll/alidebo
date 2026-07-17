@extends('admin.layouts.admin')

@section('title', __('admin.robots_editor'))

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.robots_editor') }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.robots_editor_desc') }}</p>
        </div>
        <a href="{{ route('admin.dashboard.seo') }}" class="w-fit px-4 py-2.5 bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-arrow-left rtl:fa-arrow-right"></i>
            {{ __('admin.back_to_dashboard') }}
        </a>
    </div>

    <div class="bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dashboard-card-reveal p-6">
        <div class="mb-6 p-4 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 flex gap-4">
            <div class="mt-0.5 text-blue-500">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-blue-900 dark:text-blue-400 mb-1">{{ __('admin.robots_txt_warning') }}</h3>
                <p class="text-[13px] text-blue-700 dark:text-blue-300">{{ __('admin.robots_txt_warning_desc') }}</p>
            </div>
        </div>

        <form action="{{ route('admin.seo.robots.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="content" class="block text-[13px] font-bold text-slate-700 dark:text-zinc-300 flex justify-between">
                    <span>robots.txt</span>
                    <a href="{{ url('/robots.txt') }}" target="_blank" class="text-primary hover:underline font-normal flex items-center gap-1">
                        {{ __('admin.view_live') }}
                        <i class="fa-solid fa-external-link text-[10px]"></i>
                    </a>
                </label>
                <textarea name="content" id="content" rows="15" class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-xl p-4 text-[13px] font-mono text-slate-800 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-y" placeholder="User-agent: *&#10;Allow: /">{{ old('content', $content) }}</textarea>
                @error('content')
                    <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-white/10">
                <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ __('admin.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
