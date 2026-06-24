@props(['type' => 'success', 'message'])

@php
    $classes = match($type) {
        'success' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400',
        'error' => 'bg-red-500/10 border-red-500/20 text-red-600 dark:text-red-400',
        'warning' => 'bg-amber-500/10 border-amber-500/20 text-amber-600 dark:text-amber-400',
        'info' => 'bg-blue-500/10 border-blue-500/20 text-blue-600 dark:text-blue-400',
        default => 'bg-slate-500/10 border-slate-500/20 text-slate-600 dark:text-slate-400',
    };

    $icons = [
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'error' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
        'info' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />',
    ];
@endphp

<div class="luxury-alert flex items-start gap-3 p-4 rounded-xl border {{ $classes }} mb-4" role="alert">
    <div class="shrink-0 mt-0.5">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            {!! $icons[$type] ?? $icons['info'] !!}
        </svg>
    </div>
    <div class="flex-1">
        <h3 class="text-sm font-bold">{{ __('admin.'.$type) }}</h3>
        <p class="text-sm mt-1 opacity-90">{{ $message }}</p>
    </div>
</div>
