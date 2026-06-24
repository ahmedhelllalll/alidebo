<div class="rounded-2xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-[#121214] shadow-sm overflow-visible">
    <table class="min-w-full text-start text-sm text-slate-600 dark:text-zinc-400">
        <thead class="bg-slate-50/50 dark:bg-zinc-900/50 border-b border-slate-200 dark:border-zinc-800 text-xs uppercase font-bold text-slate-500 dark:text-zinc-500">
            {{ $header }}
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/80">
            {{ $slot }}
        </tbody>
    </table>
</div>
