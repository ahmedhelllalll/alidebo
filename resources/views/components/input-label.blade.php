@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block text-[10px] font-black uppercase tracking-tighter text-slate-400 dark:text-zinc-500 mb-2.5']) }}>
    {{ $value ?? $slot }} @if($required) <span class="text-primary" aria-hidden="true">*</span> @endif
</label>
