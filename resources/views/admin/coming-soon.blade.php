@extends('admin.layouts.admin')
@section('title', __('admin.coming_soon') ?? 'Coming Soon')
@section('content')
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="text-center space-y-6 max-w-xl mx-auto px-4">
        @php
            $feature = request()->query('feature');
            
            // Set properties based on query parameter
            $icon = 'fa-rocket';
            $title = __('admin.coming_soon') ?? 'Coming Soon';
            $desc = __('admin.feature_in_development') ?? 'We are working hard to bring you this feature. Stay tuned for our next major update!';
            
            if ($feature === 'blogs') {
                $icon = 'fa-blog';
                $title = __('admin.blogs_soon_title') ?? 'Blogs & Article Manager';
                $desc = __('admin.blogs_soon_desc') ?? 'A comprehensive publishing suite to write, categorize, and schedule announcements, updates, and SEO-optimized blog posts directly on the platform.';
            } elseif ($feature === 'backups') {
                $icon = 'fa-cloud-arrow-up';
                $title = __('admin.backups_soon_title') ?? 'System & Database Backups';
                $desc = __('admin.backups_soon_desc') ?? 'Automated and on-demand snapshots of platform database records and uploads. Keep data completely secure with secure downloads and restoration features.';
            } elseif ($feature === 'contact_channel') {
                $icon = 'fa-headset';
                $title = __('admin.contact_soon_title') ?? 'Customer Support Helpdesk';
                $desc = __('admin.contact_soon_desc') ?? 'An integrated inquiry hub to track visitor claims, support requests, and user messages with built-in status management and instant alerts.';
            }
        @endphp

        <div class="mb-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-orange-500/10 text-orange-500 border border-orange-500/20 shadow-sm animate-pulse">
                <i class="fa-solid fa-bolt text-[8px]"></i>
                {{ __('admin.coming_soon') ?? 'Coming Soon' }}
            </span>
        </div>

        <div class="w-32 h-32 bg-primary/10 rounded-full flex items-center justify-center mx-auto relative group">
            <div class="absolute inset-0 bg-primary/20 rounded-full blur-xl group-hover:scale-110 transition-transform duration-500"></div>
            <i class="fa-solid {{ $icon }} text-5xl text-primary transform group-hover:rotate-12 group-hover:scale-110 transition-transform duration-500 relative z-10"></i>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-[900] text-slate-900 dark:text-white tracking-tight leading-snug">
            {{ $title }}
        </h1>
        
        <p class="text-base md:text-lg text-slate-500 dark:text-zinc-400 font-medium leading-relaxed max-w-lg mx-auto">
            {{ $desc }}
        </p>

        <div class="pt-8">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-2xl font-[900] text-[14px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm hover:shadow-md">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('admin.back_to_dashboard') ?? 'Back to Dashboard' }}
            </a>
        </div>
    </div>
</div>
@endsection
