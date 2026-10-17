<div class="space-y-4" x-data="{ page: 1, limit: 10, total: {{ count($categories) }} }">
    @foreach($categories as $index => $category)
        <label class="flex items-start gap-3.5 cursor-pointer group" x-show="{{ $index }} >= (page - 1) * limit && {{ $index }} < page * limit" @if($index >= 10) x-cloak @endif
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-x-2"
            x-transition:enter-end="opacity-100 translate-x-0">
            <div class="relative flex items-center justify-center w-5 h-5 mt-0.5 shrink-0">
                <input type="checkbox" 
                    value="{{ $category->slug }}" 
                    :checked="isL1Active('{{ $category->slug }}', '{{ $category->id }}')"
                    @change="toggleL1('{{ $category->slug }}', '{{ $category->id }}')"
                    class="peer appearance-none w-5 h-5 border-2 border-slate-200 dark:border-zinc-700 rounded bg-transparent checked:bg-primary checked:border-primary transition-colors cursor-pointer">
                <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                        d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-[15px] font-semibold text-slate-600 dark:text-zinc-400 group-hover:text-primary transition-colors flex-1 leading-snug"
                :class="isL1Active('{{ $category->slug }}', '{{ $category->id }}') ? 'font-black text-primary dark:text-primary-light' : ''">
                {{ $category->name }}
            </span>
        </label>
    @endforeach
    @if(count($categories) > 10)
        <div class="flex items-center justify-between pt-2 mt-4 border-t border-slate-100 dark:border-zinc-800/50">
            <button type="button" @click="if(page > 1) page--" :disabled="page === 1"
                class="p-1.5 text-slate-400 hover:text-primary disabled:opacity-30 disabled:hover:text-slate-400 transition-colors rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-800">
                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span class="text-xs font-bold text-slate-400 dark:text-zinc-500 font-mono tracking-widest">
                <span x-text="page"></span> / <span x-text="Math.ceil(total / limit)"></span>
            </span>
            <button type="button" @click="if(page < Math.ceil(total / limit)) page++" :disabled="page >= Math.ceil(total / limit)"
                class="p-1.5 text-slate-400 hover:text-primary disabled:opacity-30 disabled:hover:text-slate-400 transition-colors rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-800">
                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    @endif
</div>
