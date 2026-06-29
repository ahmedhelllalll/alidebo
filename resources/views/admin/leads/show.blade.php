@extends('admin.layouts.admin')
@section('title', __('admin.lead_details') ?? 'Lead Details')
@section('content')
<div class="relative min-h-[600px] max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-2 text-sm font-[900] text-slate-500 hover:text-primary transition-colors uppercase tracking-widest mb-2">
                <i class="fa-solid fa-arrow-left"></i> {{ __('admin.back_to_leads') ?? 'Back to Leads' }}
            </a>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight text-slate-900 dark:text-white">{{ __('admin.lead_details') ?? 'Lead Details' }}</h1>
        </div>
        <div>
            @if($lead->status === 'read')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[12px] font-[900] uppercase tracking-widest bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    {{ __('admin.read') ?? 'Read' }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[12px] font-[900] uppercase tracking-widest bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    {{ __('admin.unread') ?? 'Unread' }}
                </span>
            @endif
        </div>
    </div>

    <div class="bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6 sm:p-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-1">
                <label class="text-[10px] font-[900] uppercase tracking-widest text-slate-400 dark:text-zinc-500">{{ __('admin.first_name') ?? 'First Name' }}</label>
                <p class="text-[15px] font-bold text-slate-900 dark:text-white">{{ $lead->first_name }}</p>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-[900] uppercase tracking-widest text-slate-400 dark:text-zinc-500">{{ __('admin.last_name') ?? 'Last Name' }}</label>
                <p class="text-[15px] font-bold text-slate-900 dark:text-white">{{ $lead->last_name }}</p>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-[900] uppercase tracking-widest text-slate-400 dark:text-zinc-500">{{ __('admin.email') ?? 'Email' }}</label>
                <p class="text-[15px] font-bold text-slate-900 dark:text-white">
                    <a href="mailto:{{ $lead->email }}" class="text-primary hover:underline">{{ $lead->email }}</a>
                </p>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-[900] uppercase tracking-widest text-slate-400 dark:text-zinc-500">{{ __('admin.date') ?? 'Date' }}</label>
                <p class="text-[15px] font-bold text-slate-900 dark:text-white">{{ $lead->created_at->format('F d, Y h:i A') }}</p>
            </div>
        </div>

        <div class="space-y-3 pt-6 border-t border-slate-100 dark:border-zinc-800/60">
            <label class="text-[10px] font-[900] uppercase tracking-widest text-slate-400 dark:text-zinc-500 flex items-center gap-2">
                <i class="fa-solid fa-quote-left text-primary opacity-50"></i> {{ __('admin.message') ?? 'Message' }}
            </label>
            <div class="bg-slate-50 dark:bg-zinc-900/50 rounded-2xl p-6 border border-slate-100 dark:border-zinc-800/60">
                <p class="text-[15px] leading-relaxed text-slate-700 dark:text-zinc-300 whitespace-pre-wrap">{{ $lead->message }}</p>
            </div>
        </div>

    </div>
</div>
@endsection
