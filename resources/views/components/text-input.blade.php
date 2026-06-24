@props(['disabled' => false, 'icon' => null, 'iconColor' => 'primary'])

<div class="relative group">
    @if($icon)
        <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 pointer-events-none transition-colors group-focus-within:text-{{ $iconColor }} z-10">
            <i class="fa-solid fa-{{ $icon }} text-sm"></i>
        </div>
    @endif
    
    <input @disabled($disabled) {{ $attributes->merge([
        'class' => 'w-full bg-slate-50/50 dark:bg-zinc-950/50 border-slate-200 dark:border-zinc-800 focus:border-'.($iconColor ?? 'primary').' focus:ring-4 focus:ring-'.($iconColor ?? 'primary').'/10 rounded-2xl text-[15px] font-semibold text-slate-800 dark:text-white py-4 '.($icon ? 'ps-14' : 'ps-5').' pe-5 transition-all duration-300 outline-none placeholder:text-slate-400 dark:placeholder:text-zinc-500 hover:border-slate-300 dark:hover:border-white/15 hover:shadow-sm focus:shadow-md focus:shadow-'.($iconColor ?? 'primary').'/5'
    ]) }}>
</div>
