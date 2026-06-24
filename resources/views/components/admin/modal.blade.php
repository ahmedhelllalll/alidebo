@props(['id', 'title'])

<div id="{{ $id }}" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/40 dark:bg-[#09090b]/40 backdrop-blur-sm transition-opacity modal-backdrop" onclick="closeModal('{{ $id }}')"></div>

    {{-- Panel --}}
    <div class="modal-panel relative bg-white dark:bg-[#121214] border border-slate-200 dark:border-zinc-800 rounded-2xl shadow-2xl w-full max-w-lg mx-auto flex flex-col max-h-full overflow-hidden transition-all duration-300">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between shrink-0">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title-{{ $id }}">{{ $title }}</h3>
            <button type="button" onclick="closeModal('{{ $id }}')" class="p-2 text-slate-400 hover:text-slate-500 dark:hover:text-zinc-300 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-4 overflow-y-auto flex-1 custom-scrollbar">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @if(isset($footer))
        <div class="px-6 py-4 bg-slate-50/50 dark:bg-zinc-900/20 border-t border-slate-100 dark:border-zinc-800 flex justify-end gap-3 rounded-b-2xl shrink-0">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>

<style>
/* Smooth scrollbar for modals */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.5);
}
</style>
