@php
    $proofLabels = trans('landing.proof_labels');
@endphp

<section class="py-10 border-b border-slate-100 dark:border-zinc-900/80 bg-slate-50/40 dark:bg-zinc-950/40 overflow-hidden" aria-hidden="true">
    <div class="marquee">
        <div class="marquee-content">
            @foreach ($proofLabels as $label)
                <span class="inline-flex items-center gap-2 shrink-0 text-sm font-semibold text-slate-500 dark:text-zinc-500 whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary/70" aria-hidden="true"></span>
                    {{ $label }}
                </span>
            @endforeach
            @foreach ($proofLabels as $label)
                <span class="inline-flex items-center gap-2 shrink-0 text-sm font-semibold text-slate-500 dark:text-zinc-500 whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary/70" aria-hidden="true"></span>
                    {{ $label }}
                </span>
            @endforeach
        </div>
    </div>
</section>
