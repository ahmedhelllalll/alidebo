@extends('admin.layouts.admin')

@section('title', __('admin.pages'))

@section('content')
<div class="space-y-6 lg:space-y-8 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-inner">
                    <i class="fa-solid fa-file-lines text-lg"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">
                    {{ __('admin.pages') }}
                </h1>
            </div>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 ms-1 sm:ms-[52px]">{{ __('admin.pages_desc') }}</p>
        </div>

        <a href="{{ route('admin.pages.create') }}"
           class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
            <i class="fa-solid fa-plus text-[16px]"></i>
            {{ __('admin.add_page') }}
        </a>
    </div>

    @if($pages->isEmpty())
        {{-- ─── Empty State ─── --}}
        <div class="bg-white dark:bg-[#121214] border border-slate-200/60 dark:border-white/5 shadow-xl shadow-slate-200/20 dark:shadow-none rounded-3xl p-10 sm:p-16 flex flex-col items-center justify-center text-center dashboard-card-reveal">
            <div class="relative mb-8 group">
                <div class="absolute inset-0 bg-primary/20 rounded-full blur-3xl scale-150 transition-transform duration-700 opacity-40 dark:opacity-20"></div>
                <div class="relative w-28 h-28 flex items-center justify-center">
                    <i class="fa-solid fa-file-circle-plus text-5xl text-primary/80 drop-shadow-sm"></i>
                </div>
            </div>

            <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-3">
                {{ __('admin.no_pages_yet') }}
            </h3>
            <p class="text-slate-500 dark:text-zinc-400 font-medium max-w-sm mx-auto mb-8 leading-relaxed">
                {{ __('admin.no_pages_desc') }}
            </p>

            <a href="{{ route('admin.pages.create') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl font-bold text-[14px] hover:bg-slate-800 dark:hover:bg-slate-100 hover:scale-[1.03] transition-all shadow-lg active:scale-95">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>{{ __('admin.add_page') }}</span>
            </a>
        </div>
    @else
        {{-- ─── Stats Strip ─── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 dashboard-card-reveal">
            @php
                $totalCount     = $pages->total();
                $publishedCount = $pages->getCollection()->where('status', 'published')->count();
                $draftCount     = $pages->getCollection()->where('status', 'draft')->count();
            @endphp
            <div class="bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                    <i class="fa-solid fa-layer-group text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-[900] text-slate-900 dark:text-white leading-none">{{ $totalCount }}</p>
                    <p class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mt-1">{{ __('admin.total') }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 shrink-0">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-[900] text-slate-900 dark:text-white leading-none">{{ $publishedCount }}</p>
                    <p class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mt-1">{{ __('admin.published') }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl p-5 flex items-center gap-4 shadow-sm col-span-2 sm:col-span-1">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 shrink-0">
                    <i class="fa-solid fa-pen-ruler text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-[900] text-slate-900 dark:text-white leading-none">{{ $draftCount }}</p>
                    <p class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mt-1">{{ __('admin.draft') }}</p>
                </div>
            </div>
        </div>

        {{-- ─── Pages Table ─── --}}
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden dashboard-card-reveal relative flex flex-col">

            {{-- Desktop Table --}}
            <div class="overflow-x-auto hidden sm:block">
                <table class="w-full border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-zinc-800/40 border-b border-slate-200 dark:border-white/10 text-[11px] uppercase tracking-wider text-slate-500 dark:text-zinc-400 font-black">
                            <th class="px-6 py-4 text-start">{{ __('admin.title') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('admin.slug') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('admin.status') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('admin.created_at') }}</th>
                            <th class="px-6 py-4 text-start">{{ __('admin.indexing') }}</th>
                            <th class="px-6 py-4 text-end">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-[14px] font-medium text-slate-700 dark:text-zinc-300">
                        @foreach($pages as $page)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-white/[0.02] transition-colors group" data-page-id="{{ $page->id }}">
                            {{-- Title --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 flex items-center justify-center shrink-0">
                                        <i class="fa-regular fa-file-lines text-slate-400 dark:text-zinc-500"></i>
                                    </div>
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors hover:underline underline-offset-2">
                                        {{ $page->title[app()->getLocale()] ?? $page->title['en'] ?? $page->title['ar'] ?? __('admin.untitled_page') }}
                                    </a>
                                </div>
                            </td>

                            {{-- Slug (clickable) --}}
                            <td class="px-6 py-4">
                                <a href="{{ url('/' . $page->slug) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-zinc-400 text-xs font-mono font-medium border border-slate-200/50 dark:border-white/5 hover:bg-primary/5 hover:text-primary hover:border-primary/20 dark:hover:border-primary/20 transition-all group/slug">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] opacity-40 group-hover/slug:opacity-100 transition-opacity"></i>
                                    /{{ $page->slug }}
                                </a>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @if($page->status === 'published')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        {{ __('admin.published') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-black uppercase tracking-wider bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-200/50 dark:border-amber-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ __('admin.draft') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Created At --}}
                            <td class="px-6 py-4 text-slate-400 dark:text-zinc-500 text-[13px]">
                                {{ $page->created_at?->diffForHumans() }}
                            </td>

                            {{-- Indexing --}}
                            <td class="px-6 py-4">
                                @php
                                    $log = \App\Models\GoogleIndexLog::where('indexable_type', 'App\Models\Page')->where('indexable_id', $page->id)->first();
                                @endphp
                                @if($log)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-black uppercase tracking-wider border {{ $log->status == 'submitted' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : ($log->status == 'failed' ? 'bg-red-50 text-red-600 border-red-200' : 'bg-amber-50 text-amber-600 border-amber-200') }}">
                                        {{ $log->status }}
                                    </span>
                                @else
                                    <form action="{{ route('admin.indexing.request') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="url" value="{{ url('/' . $page->slug) }}">
                                        <input type="hidden" name="indexable_type" value="App\Models\Page">
                                        <input type="hidden" name="indexable_id" value="{{ $page->id }}">
                                        <button type="submit" class="text-[11px] font-black uppercase tracking-wider text-primary hover:text-primary-light transition-colors flex items-center gap-1 bg-primary/10 px-2.5 py-1 rounded-md">
                                            <i class="fa-brands fa-google"></i> {{ __('admin.request_index') }}
                                        </button>
                                    </form>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity duration-200">
                                    {{-- View live --}}
                                    <a href="{{ url('/' . $page->slug) }}" target="_blank"
                                       class="w-8 h-8 rounded-lg bg-white dark:bg-[#18181b] border border-slate-200 dark:border-white/10 shadow-sm flex items-center justify-center text-slate-400 dark:text-zinc-500 hover:text-sky-500 hover:border-sky-500/30 hover:bg-sky-50 dark:hover:bg-sky-500/10 transition-all"
                                       title="{{ __('admin.view_details') }}">
                                        <i class="fa-regular fa-eye text-[13px]"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.pages.edit', $page) }}"
                                       class="w-8 h-8 rounded-lg bg-white dark:bg-[#18181b] border border-slate-200 dark:border-white/10 shadow-sm flex items-center justify-center text-slate-400 dark:text-zinc-500 hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-all"
                                       title="{{ __('admin.edit') }}">
                                        <i class="fa-regular fa-pen-to-square text-[13px]"></i>
                                    </a>
                                    {{-- Delete --}}
                                    <button type="button"
                                            onclick="openDeleteModal('{{ route('admin.pages.destroy', $page) }}', '{{ addslashes($page->title[app()->getLocale()] ?? $page->title['en'] ?? $page->title['ar'] ?? '') }}')"
                                            class="w-8 h-8 rounded-lg bg-white dark:bg-[#18181b] border border-slate-200 dark:border-white/10 shadow-sm flex items-center justify-center text-slate-400 dark:text-zinc-500 hover:text-red-500 hover:border-red-500/30 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all"
                                            title="{{ __('admin.delete') }}">
                                        <i class="fa-regular fa-trash-can text-[13px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="sm:hidden divide-y divide-slate-100 dark:divide-white/5">
                @foreach($pages as $page)
                <div class="p-4 space-y-3" data-page-id="{{ $page->id }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 flex items-center justify-center shrink-0">
                                <i class="fa-regular fa-file-lines text-slate-400 dark:text-zinc-500"></i>
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('admin.pages.edit', $page) }}" class="font-bold text-slate-900 dark:text-white text-[15px] truncate block hover:text-primary transition-colors">
                                    {{ $page->title[app()->getLocale()] ?? $page->title['en'] ?? $page->title['ar'] ?? __('admin.untitled_page') }}
                                </a>
                                <a href="{{ url('/' . $page->slug) }}" target="_blank" class="text-xs font-mono text-slate-400 dark:text-zinc-500 mt-0.5 flex items-center gap-1 hover:text-primary transition-colors">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i>
                                    /{{ $page->slug }}
                                </a>
                            </div>
                        </div>
                        @if($page->status === 'published')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 shrink-0">
                                <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                {{ __('admin.published') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 shrink-0">
                                <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                {{ __('admin.draft') }}
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ url('/' . $page->slug) }}" target="_blank"
                           class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-[12px] font-[900] bg-sky-50 dark:bg-sky-500/5 text-sky-600 dark:text-sky-400 rounded-xl hover:bg-sky-100 dark:hover:bg-sky-500/10 transition-all">
                            <i class="fa-regular fa-eye"></i> {{ __('admin.view_details') }}
                        </a>
                        <a href="{{ route('admin.pages.edit', $page) }}"
                           class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-[12px] font-[900] bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-zinc-300 rounded-xl hover:bg-slate-200/70 dark:hover:bg-white/10 transition-all">
                            <i class="fa-regular fa-pen-to-square"></i> {{ __('admin.edit') }}
                        </a>
                        <button type="button"
                                onclick="openDeleteModal('{{ route('admin.pages.destroy', $page) }}', '{{ addslashes($page->title[app()->getLocale()] ?? $page->title['en'] ?? $page->title['ar'] ?? '') }}')"
                                class="flex items-center justify-center w-10 h-10 bg-red-50 dark:bg-red-500/5 text-red-500 rounded-xl hover:bg-red-100 dark:hover:bg-red-500/10 transition-all">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($pages->hasPages())
            <div class="p-5 border-t border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-zinc-800/20 mt-auto">
                {{ $pages->links() }}
            </div>
            @endif
        </div>
    @endif
</div>

{{-- ─── Delete Confirmation Modal ─── --}}
<x-admin.modal id="deletePageModal" :title="__('admin.warning')">
    <div class="text-center px-4 py-8">
        <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-500 mx-auto mb-6 shadow-inner ring-4 ring-red-50 dark:ring-red-500/5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-3 tracking-tight">{{ __('admin.delete_page_title') }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400" id="deletePageMessage">{{ __('admin.delete_page_desc') }}</p>
    </div>
    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full pb-2">
            <button type="button" onclick="closeModal('deletePageModal')"
                    class="w-full sm:flex-1 px-5 py-3 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[14px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">
                {{ __('admin.cancel') }}
            </button>
            <form id="deletePageForm" method="POST" class="w-full sm:flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" id="confirmDeletePageBtn"
                        class="w-full px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(239,68,68,0.25)] hover:shadow-[0_12px_25px_rgba(239,68,68,0.35)] transition-all active:scale-[0.98]">
                    <i class="fa-regular fa-trash-can me-1.5"></i>
                    {{ __('admin.delete') }}
                </button>
            </form>
        </div>
    </x-slot>
</x-admin.modal>
@endsection

@push('scripts')
<script>
    function openDeleteModal(actionUrl, pageName) {
        const form = document.getElementById('deletePageForm');
        form.action = actionUrl;

        const msg = document.getElementById('deletePageMessage');
        if (pageName) {
            msg.innerHTML = '{!! __("admin.delete_page_desc") !!}'.replace(':name', '<strong class="text-slate-800 dark:text-white">' + pageName + '</strong>');
        }

        if (window.modals && window.modals['deletePageModal']) {
            window.modals['deletePageModal'].show();
        } else {
            const modal = document.getElementById('deletePageModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
</script>
@endpush
