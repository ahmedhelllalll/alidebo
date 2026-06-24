@props(['type' => 'info', 'text'])

@php
    $classes = match($type) {
        'success', 'approved', 'active' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
        'error', 'rejected' => 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 border-red-200 dark:border-red-500/20',
        'warning', 'pending' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
        'hidden' => 'bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-400 border-slate-200 dark:border-zinc-700',
        default => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $classes }}">
    @if(in_array($type, ['success', 'approved', 'active']))
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
    @elseif(in_array($type, ['error', 'rejected']))
        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
    @elseif(in_array($type, ['warning', 'pending']))
        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
    @else
        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
    @endif
    {{ $text }}
</span>
