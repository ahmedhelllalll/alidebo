@extends('admin.layouts.admin')

@section('title', __('admin.update_page'))

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.update_page') }}: {{ $page->title['en'] ?? '' }}</h1>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-[#121214] hover:bg-slate-50 dark:hover:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] transition-all shadow-sm">
            <i class="fa-solid fa-arrow-left text-[14px]"></i>
            {{ __('admin.cancel') }}
        </a>
    </div>

    <form action="{{ route('admin.pages.update', $page) }}" method="POST" enctype="multipart/form-data" class="dashboard-card-reveal">
        @csrf
        @method('PUT')
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl p-6 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.title_en') }}</label>
                    <input type="text" name="title[en]" id="title_en" value="{{ old('title.en', $page->title['en'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.slug') }}</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" data-ignore-id="{{ $page->id }}">
                    <p id="slug_error" class="mt-1 text-xs text-red-500 hidden"><i class="fa-solid fa-circle-exclamation mr-1"></i> This slug is already taken. Please choose another.</p>
                </div>
            </div>

            <div>
                <label for="content_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.content_en') }}</label>
                <textarea name="content[en]" id="content_en" rows="12" class="tinymce-editor mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">{{ old('content.en', $page->content['en'] ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $currentStatus = old('status', $page->status);
                    $statusLabel = $currentStatus === 'published' ? __('admin.published') : __('admin.draft');
                    
                    $currentLocation = old('location', $page->location);
                    $locationLabel = match($currentLocation) {
                        'navbar' => __('admin.navbar'),
                        'footer' => __('admin.footer'),
                        'both' => __('admin.both'),
                        default => __('admin.hidden'),
                    };
                    
                    $currentLayout = old('layout_style', $page->layout_style);
                    $layoutLabel = match($currentLayout) {
                        'cards' => __('admin.cards'),
                        'split' => __('admin.split'),
                        default => __('admin.default'),
                    };
                @endphp

                {{-- Status --}}
                <div class="input-group">
                    <label id="status_label" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                        {{ __('admin.status') }} <span class="text-red-400" aria-hidden="true">*</span>
                    </label>
                    <div class="relative custom-smart-dropdown" id="dropdown_status">
                        <div class="relative flex items-center group">
                            <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 z-10 pointer-events-none transition-colors group-focus-within:text-primary">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </div>
                            <input type="hidden" name="status" id="field_status" value="{{ $currentStatus }}" required aria-required="true" class="validate-target">
                            <button type="button" class="dropdown-trigger w-full input-premium rounded-2xl py-5 ps-14 pe-6 text-sm font-bold flex items-center justify-between transition-all duration-300 outline-none cursor-pointer text-slate-800 dark:text-white">
                                <span class="dropdown-label truncate">{{ $statusLabel }}</span>
                                <i aria-hidden="true" class="fa-solid fa-chevron-down text-[10px] text-slate-300 dark:text-zinc-600 transition-transform duration-300"></i>
                            </button>
                        </div>
                        <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden opacity-0 translate-y-2">
                            <div class="dropdown-list max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="published" data-label="{{ __('admin.published') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-globe text-primary"></i> {{ __('admin.published') }}</div>
                                </div>
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="draft" data-label="{{ __('admin.draft') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-file text-slate-400"></i> {{ __('admin.draft') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Location --}}
                <div class="input-group">
                    <label id="location_label" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                        {{ __('admin.location') }} <span class="text-red-400" aria-hidden="true">*</span>
                    </label>
                    <div class="relative custom-smart-dropdown" id="dropdown_location">
                        <div class="relative flex items-center group">
                            <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 z-10 pointer-events-none transition-colors group-focus-within:text-primary">
                                <i class="fa-solid fa-map-pin text-sm"></i>
                            </div>
                            <input type="hidden" name="location" id="field_location" value="{{ $currentLocation }}" required aria-required="true" class="validate-target">
                            <button type="button" class="dropdown-trigger w-full input-premium rounded-2xl py-5 ps-14 pe-6 text-sm font-bold flex items-center justify-between transition-all duration-300 outline-none cursor-pointer text-slate-800 dark:text-white">
                                <span class="dropdown-label truncate">{{ $locationLabel }}</span>
                                <i aria-hidden="true" class="fa-solid fa-chevron-down text-[10px] text-slate-300 dark:text-zinc-600 transition-transform duration-300"></i>
                            </button>
                        </div>
                        <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden opacity-0 translate-y-2">
                            <div class="dropdown-list max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="hidden" data-label="{{ __('admin.hidden') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-eye-slash text-slate-400"></i> {{ __('admin.hidden') }}</div>
                                </div>
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="navbar" data-label="{{ __('admin.navbar') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-bars text-primary"></i> {{ __('admin.navbar') }}</div>
                                </div>
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="footer" data-label="{{ __('admin.footer') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-shoe-prints text-primary"></i> {{ __('admin.footer') }}</div>
                                </div>
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="both" data-label="{{ __('admin.both') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-arrows-up-down text-primary"></i> {{ __('admin.both') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Layout Style --}}
                <div class="input-group">
                    <label id="layout_label" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                        {{ __('admin.layout_style') }} <span class="text-red-400" aria-hidden="true">*</span>
                    </label>
                    <div class="relative custom-smart-dropdown" id="dropdown_layout">
                        <div class="relative flex items-center group">
                            <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 z-10 pointer-events-none transition-colors group-focus-within:text-primary">
                                <i class="fa-solid fa-layer-group text-sm"></i>
                            </div>
                            <input type="hidden" name="layout_style" id="field_layout" value="{{ $currentLayout }}" required aria-required="true" class="validate-target">
                            <button type="button" class="dropdown-trigger w-full input-premium rounded-2xl py-5 ps-14 pe-6 text-sm font-bold flex items-center justify-between transition-all duration-300 outline-none cursor-pointer text-slate-800 dark:text-white">
                                <span class="dropdown-label truncate">{{ $layoutLabel }}</span>
                                <i aria-hidden="true" class="fa-solid fa-chevron-down text-[10px] text-slate-300 dark:text-zinc-600 transition-transform duration-300"></i>
                            </button>
                        </div>
                        <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden opacity-0 translate-y-2">
                            <div class="dropdown-list max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="default" data-label="{{ __('admin.default') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-align-justify text-primary"></i> {{ __('admin.default') }}</div>
                                </div>
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="cards" data-label="{{ __('admin.cards') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-table-cells text-primary"></i> {{ __('admin.cards') }}</div>
                                </div>
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="split" data-label="{{ __('admin.split') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-columns text-primary"></i> {{ __('admin.split') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-admin.seo-editor :model="$page" />

            <div class="flex justify-end pt-6 border-t border-slate-200 dark:border-gray-700">
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
                    <i class="fa-solid fa-save text-[14px]"></i>
                    {{ __('admin.update_page') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '.tinymce-editor',
        plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
        toolbar_mode: 'floating',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        height: 400,
        menubar: false,
        base_url: '{{ asset('js/tinymce') }}',
        suffix: '.min',
        license_key: 'gpl',
        promotion: false,
        skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
        content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save(); // ensure textarea gets updated for form submission
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const slugInput = document.getElementById('slug');
        const slugError = document.getElementById('slug_error');
        const submitBtn = document.querySelector('button[type="submit"]');
        let debounceTimer;

        slugInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const slug = this.value.trim();
            const ignoreId = this.dataset.ignoreId || '';
            
            if (slug.length === 0) {
                slugError.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('admin.pages.check-slug') }}?slug=${encodeURIComponent(slug)}&ignore=${ignoreId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.exists) {
                            slugError.classList.remove('hidden');
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            slugError.classList.add('hidden');
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    })
                    .catch(error => console.error('Error checking slug:', error));
            }, 500);
        });
    });
</script>
@endpush
