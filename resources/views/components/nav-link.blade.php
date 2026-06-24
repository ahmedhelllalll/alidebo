@props(['href', 'active' => false])

@php
$classes = $active
            ? 'px-5 py-2 rounded-full text-sm font-bold text-slate-900 bg-white shadow-sm transition-all'
            : 'px-5 py-2 rounded-full text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-white hover:shadow-sm transition-all';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
