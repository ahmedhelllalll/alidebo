@extends('admin.layouts.admin')

@section('title', __('admin.blogs'))

@section('content')
<div class="space-y-6 lg:space-y-8 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-inner">
                    <i class="fa-solid fa-blog text-lg"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">
                    {{ __('admin.blogs') }}
                </h1>
            </div>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 ms-1 sm:ms-[52px]">{{ __('admin.blogs_desc') }}</p>
        </div>

        <a href="{{ route('admin.blogs.create') }}"
           class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
            <i class="fa-solid fa-plus text-[16px]"></i>
            {{ __('admin.add_blog') }}
        </a>
    </div>

    @if($posts->isEmpty())
        {{-- ─── Empty State ─── --}}
        <div class="bg-white dark:bg-[#121214] border border-slate-200/60 dark:border-white/5 shadow-xl shadow-slate-200/20 dark:shadow-none rounded-3xl p-10 sm:p-16 flex flex-col items-center justify-center text-center dashboard-card-reveal">
            <div class="relative mb-8 group">
                <div class="absolute inset-0 bg-primary/20 rounded-full blur-3xl scale-150 transition-transform duration-700 opacity-40 dark:opacity-20"></div>
                <div class="relative w-28 h-28 flex items-center justify-center">
                    <i class="fa-solid fa-file-circle-plus text-5xl text-primary/80 drop-shadow-sm"></i>
                </div>
            </div>

            <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-3">
                {{ __('admin.no_blogs_yet') }}
            </h3>
            <p class="text-slate-500 dark:text-zinc-400 font-medium max-w-sm mx-auto mb-8 leading-relaxed">
                {{ __('admin.no_blogs_desc') }}
            </p>

            <a href="{{ route('admin.blogs.create') }}"
               class="inline-flex items-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl font-bold text-[14px] hover:bg-slate-800 dark:hover:bg-slate-100 hover:scale-[1.03] transition-all shadow-lg active:scale-95">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>{{ __('admin.add_blog') }}</span>
            </a>
        </div>
    @else
        {{-- ─── Stats Strip ─── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 dashboard-card-reveal">
            @php
                $totalCount     = $posts->total();
                $publishedCount = $posts->getCollection()->where('status', 'published')->count();
                $draftCount     = $posts->getCollection()->where('status', 'draft')->count();
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

        {{-- ─── Posts Grid ─── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($posts as $post)
            <div class="group bg-white dark:bg-[#121214] border border-slate-200/60 dark:border-white/5 shadow-sm hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.3)] rounded-3xl overflow-hidden dashboard-card-reveal flex flex-col transition-all duration-300 hover:-translate-y-1">
                
                {{-- Media Header --}}
                <div class="relative w-full aspect-[16/10] bg-slate-100 dark:bg-[#1a1a1c] overflow-hidden border-b border-slate-200/60 dark:border-white/5">
                    @if($post->media_url && $post->media_type === 'image')
                        <img src="{{ asset('storage/' . $post->media_url) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fa-regular fa-image text-4xl text-slate-300 dark:text-zinc-700 opacity-50"></i>
                        </div>
                    @endif

                    {{-- Status Badge (Absolute Top Right) --}}
                    <div class="absolute top-3 right-3 rtl:right-auto rtl:left-3 z-10">
                        @if($post->status === 'published')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-white/90 dark:bg-zinc-900/90 text-emerald-600 dark:text-emerald-400 backdrop-blur-md shadow-sm border border-black/5 dark:border-white/10">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ __('admin.published') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-white/90 dark:bg-zinc-900/90 text-amber-600 dark:text-amber-400 backdrop-blur-md shadow-sm border border-black/5 dark:border-white/10">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                {{ __('admin.draft') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Content Body --}}
                <div class="p-6 flex flex-col flex-1">
                    {{-- Title --}}
                    <a href="{{ route('admin.blogs.edit', $post) }}" class="text-lg font-[900] text-slate-900 dark:text-white leading-tight mb-2 group-hover:text-primary transition-colors line-clamp-2">
                        {{ $post->title[app()->getLocale()] ?? $post->title['en'] ?? $post->title['ar'] ?? __('admin.untitled_blog') }}
                    </a>

                    {{-- Slug Link --}}
                    <a href="{{ url('/blog/' . $post->slug) }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] font-mono text-slate-400 dark:text-zinc-500 hover:text-primary transition-colors line-clamp-1 mb-6">
                        <i class="fa-solid fa-link text-[10px]"></i>
                        /blog/{{ $post->slug }}
                    </a>

                    {{-- Footer: Date & Actions --}}
                    <div class="mt-auto pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                        <div class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-regular fa-calendar text-[12px]"></i>
                            {{ $post->created_at?->format('M d, Y') }}
                        </div>
                        
                        <div class="flex items-center gap-1">
                            <a href="{{ url('/blog/' . $post->slug) }}" target="_blank" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 hover:text-sky-500 hover:bg-sky-50 dark:hover:bg-sky-500/10 transition-colors tooltip" title="{{ __('admin.view_details') }}">
                                <i class="fa-regular fa-eye text-[13px]"></i>
                            </a>
                            <a href="{{ route('admin.blogs.edit', $post) }}" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 hover:text-primary hover:bg-primary/10 transition-colors tooltip" title="{{ __('admin.edit') }}">
                                <i class="fa-regular fa-pen-to-square text-[13px]"></i>
                            </a>
                            <button type="button" onclick="openDeleteModal('{{ route('admin.blogs.destroy', $post) }}', '{{ addslashes($post->title[app()->getLocale()] ?? $post->title['en'] ?? $post->title['ar'] ?? '') }}')" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors tooltip" title="{{ __('admin.delete') }}">
                                <i class="fa-regular fa-trash-can text-[13px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($posts->hasPages())
            <div class="p-5 border-t border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-zinc-800/20 mt-auto">
                {{ $posts->links() }}
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
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-3 tracking-tight">{{ __('admin.delete_blog_title') }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400" id="deletePageMessage">{{ __('admin.delete_blog_desc') }}</p>
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
            msg.innerHTML = '{!! __("admin.delete_blog_desc") !!}'.replace(':name', '<strong class="text-slate-800 dark:text-white">' + pageName + '</strong>');
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
