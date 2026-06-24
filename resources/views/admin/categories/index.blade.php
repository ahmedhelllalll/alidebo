@extends('admin.layouts.admin')
@section('title', __('admin.categories'))
@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.categories') }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.categories_desc') }}</p>
        </div>
        <button onclick="openModal('addCategoryModal')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            {{ __('admin.add_new') }}
        </button>
    </div>
    {{-- Filter & Search Bar --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white/50 dark:bg-zinc-900/30 backdrop-blur-xl p-4 rounded-2xl border border-white/60 dark:border-white/[0.05] shadow-sm">
        {{-- Status Tabs --}}
        <div class="flex items-center p-1 bg-slate-100/50 dark:bg-zinc-800/50 rounded-xl w-full md:w-auto">
            <button onclick="filterByStatus('all')" id="tab-all" class="flex-1 md:flex-none px-5 py-2 text-[13px] font-[900] rounded-lg transition-all {{ !request('status') || request('status') == 'all' ? 'bg-white dark:bg-zinc-700 text-primary shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700' }}">
                {{ __('admin.all') }}
            </button>
            <button onclick="filterByStatus('active')" id="tab-active" class="flex-1 md:flex-none px-5 py-2 text-[13px] font-[900] rounded-lg transition-all {{ request('status') == 'active' ? 'bg-white dark:bg-zinc-700 text-primary shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700' }}">
                {{ __('admin.active') }}
            </button>
            <button onclick="filterByStatus('pending')" id="tab-pending" class="flex-1 md:flex-none px-5 py-2 text-[13px] font-[900] rounded-lg transition-all {{ request('status') == 'pending' ? 'bg-white dark:bg-zinc-700 text-primary shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700' }}">
                {{ __('admin.pending') }}
            </button>
        </div>
        {{-- Smart Search --}}
        <div class="relative w-full md:w-72 group">
            <div class="absolute inset-y-0 start-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" id="smart-search" oninput="debounceSearch(this.value)" value="{{ request('search') }}" placeholder="{{ __('admin.search') }}" 
                class="w-full bg-slate-100/50 dark:bg-zinc-800/50 border border-transparent focus:border-primary/30 focus:bg-white dark:focus:bg-[#09090b] focus:ring-4 focus:ring-primary/10 rounded-xl text-[13px] font-bold py-2.5 ps-11 pe-10 transition-all text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 shadow-sm">
            {{-- Search Spinner --}}
            <div id="search-spinner" class="absolute inset-y-0 end-4 flex items-center hidden">
                <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden relative z-10 w-full min-h-[400px]">
        {{-- Loading Overlay --}}
        <div id="list-loading-overlay" class="absolute inset-0 bg-white/40 dark:bg-black/20 backdrop-blur-[2px] z-[20] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
            <div class="flex flex-col items-center gap-3">
                <div class="relative">
                    <div class="w-12 h-12 rounded-full border-2 border-primary/20 border-t-primary animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                    </div>
                </div>
                <span class="text-[11px] font-[900] uppercase tracking-widest text-primary/80 animate-pulse">{{ __('admin.filtering') }}...</span>
            </div>
        </div>
        <div id="category-list-container">
            @include('admin.categories._list')
        </div>
    </div>
</div>
{{-- Custom Add Modal --}}
<x-admin.modal id="addCategoryModal" :title="__('admin.add_category')">
    <form action="{{ route('admin.categories.store') }}" method="POST" id="addCategoryForm" class="space-y-5 px-1 pb-1" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.name_en') }}</label>
                <input type="text" name="name_en" required class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="{{ __('admin.eg_technology') }}">
            </div>
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.name_ar') }}</label>
                <input type="text" name="name_ar" required dir="rtl" class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all font-cairo text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="{{ __('admin.eg_technology_ar') }}">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
             <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.slug') }} ({{ __('admin.optional') }})</label>
                <input type="text" name="slug" class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="{{ __('admin.placeholder_slug') }}">
            </div>
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.icon') }}</label>
                <input type="text" name="icon" class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="{{ __('admin.placeholder_icon') }}">
            </div>
        </div>
        <div>
            <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.image') }}</label>
            <div class="group relative w-full h-32 rounded-2xl border-2 border-dashed border-slate-200 dark:border-zinc-800 hover:border-primary/50 transition-all flex flex-col items-center justify-center gap-2 overflow-hidden bg-slate-50/50 dark:bg-zinc-900/30">
                <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="previewImage(this, 'add_image_preview')">
                <div id="add_image_preview" class="absolute inset-0 hidden">
                    <img src="" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="text-white text-xs font-bold">{{ __('admin.change_image') }}</p>
                    </div>
                </div>
                <div id="add_image_placeholder" class="text-center">
                    <svg class="w-8 h-8 text-slate-400 dark:text-zinc-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-xs font-bold text-slate-500 dark:text-zinc-500">{{ __('admin.click_to_upload') }}</p>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.status') }}</label>
            <div class="custom-select-wrapper relative" data-name="status" id="add_category_status_select">
                <input type="hidden" name="status" id="add_status" value="active">
                <button type="button" class="custom-select-button w-full flex items-center justify-between bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all outline-none text-slate-900 dark:text-white shadow-sm hover:border-slate-300 dark:hover:border-white/20">
                    <span class="selected-text flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-emerald-500"></div>{{ __('admin.active') }}</span>
                    <svg class="h-5 w-5 text-slate-400 dark:text-zinc-500 transition-transform duration-200 icon-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="custom-select-dropdown absolute z-[60] top-full mt-2 w-full bg-white dark:bg-[#1a1a1e] border border-slate-200 dark:border-white/10 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] opacity-0 pointer-events-none transform translate-y-[-10px] transition-all duration-200 origin-top flex flex-col overflow-hidden">
                    <div class="custom-option px-4 py-3 hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer flex items-center gap-2 transition-colors border-b border-slate-100 dark:border-white/5 last:border-0" data-value="active">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="font-bold text-slate-900 dark:text-white text-[13px]">{{ __('admin.active') }}</span>
                    </div>
                    <div class="custom-option px-4 py-3 hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer flex items-center gap-2 transition-colors" data-value="pending">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <span class="font-bold text-slate-900 dark:text-white text-[13px]">{{ __('admin.pending') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 w-full">
            <button type="button" onclick="closeModal('addCategoryModal')" class="w-full sm:w-auto px-5 py-2.5 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">
                {{ __('admin.cancel') }}
            </button>
            <button type="button" onclick="submitForm('addCategoryForm', this);" class="w-full sm:w-auto px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
                {{ __('admin.save') }}
            </button>
        </div>
    </x-slot>
</x-admin.modal>
{{-- Custom Edit Modal --}}
<x-admin.modal id="editCategoryModal" :title="__('admin.edit_category')">
    <form action="" method="POST" id="editCategoryForm" class="space-y-5 px-1 pb-1" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.name_en') }}</label>
                <input type="text" name="name_en" id="edit_name_en" required class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 dark:placeholder:text-zinc-600">
            </div>
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.name_ar') }}</label>
                <input type="text" name="name_ar" id="edit_name_ar" required dir="rtl" class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all font-cairo text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 dark:placeholder:text-zinc-600">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
             <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.slug') }}</label>
                <input type="text" name="slug" id="edit_slug" class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="{{ __('admin.placeholder_slug') }}">
            </div>
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.icon') }}</label>
                <input type="text" name="icon" id="edit_icon" class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 dark:placeholder:text-zinc-600" placeholder="{{ __('admin.placeholder_icon') }}">
            </div>
        </div>
        <div>
            <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.image') }}</label>
            <div class="group relative w-full h-32 rounded-2xl border-2 border-dashed border-slate-200 dark:border-zinc-800 hover:border-primary/50 transition-all flex flex-col items-center justify-center gap-2 overflow-hidden bg-slate-50/50 dark:bg-zinc-900/30">
                <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer z-10" onchange="previewImage(this, 'edit_image_preview')">
                <div id="edit_image_preview" class="absolute inset-0">
                    <img src="" class="w-full h-full object-cover" id="edit_image_display">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="text-white text-xs font-bold">{{ __('admin.change_image') }}</p>
                    </div>
                </div>
                <div id="edit_image_placeholder" class="text-center hidden">
                    <svg class="w-8 h-8 text-slate-400 dark:text-zinc-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-xs font-bold text-slate-500 dark:text-zinc-500">{{ __('admin.click_to_upload') }}</p>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.status') }}</label>
            <div class="custom-select-wrapper relative" data-name="status" id="edit_category_status_select">
                <input type="hidden" name="status" id="edit_status" value="active">
                <button type="button" class="custom-select-button w-full flex items-center justify-between bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all outline-none text-slate-900 dark:text-white shadow-sm hover:border-slate-300 dark:hover:border-white/20">
                    <span class="selected-text flex items-center gap-2" id="edit_status_display"><div class="w-2 h-2 rounded-full bg-emerald-500"></div>{{ __('admin.active') }}</span>
                    <svg class="h-5 w-5 text-slate-400 dark:text-zinc-500 transition-transform duration-200 icon-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="custom-select-dropdown absolute z-[60] top-full mt-2 w-full bg-white dark:bg-[#1a1a1e] border border-slate-200 dark:border-white/10 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] opacity-0 pointer-events-none transform translate-y-[-10px] transition-all duration-200 origin-top flex flex-col overflow-hidden">
                    <div class="custom-option px-4 py-3 hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer flex items-center gap-2 transition-colors border-b border-slate-100 dark:border-white/5 last:border-0" data-value="active">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="font-bold text-slate-900 dark:text-white text-[13px]">{{ __('admin.active') }}</span>
                    </div>
                    <div class="custom-option px-4 py-3 hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer flex items-center gap-2 transition-colors" data-value="pending">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <span class="font-bold text-slate-900 dark:text-white text-[13px]">{{ __('admin.pending') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 w-full">
            <button type="button" onclick="closeModal('editCategoryModal')" class="w-full sm:w-auto px-5 py-2.5 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">
                {{ __('admin.cancel') }}
            </button>
            <button type="button" onclick="submitForm('editCategoryForm', this);" class="w-full sm:w-auto px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
                {{ __('admin.save') }}
            </button>
        </div>
    </x-slot>
</x-admin.modal>
{{-- Custom Native Delete Alert Modal --}}
<x-admin.modal id="deleteConfirmModal" :title="__('admin.warning')" class="max-w-md">
    <div class="text-center px-4 py-8">
        <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-500 mx-auto mb-6 shadow-inner ring-4 ring-red-50 dark:ring-red-500/5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-3 tracking-tight">{{ __('admin.delete_confirm_title') }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400">{{ __('admin.delete_confirm_desc') }}</p>
    </div>
    <form action="" method="POST" id="deleteCategoryForm" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full pb-2">
            <button type="button" onclick="closeModal('deleteConfirmModal')" class="w-full sm:flex-1 px-5 py-3 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[14px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">
                {{ __('admin.cancel') }}
            </button>
            <button type="button" onclick="submitForm('deleteCategoryForm', this);" class="w-full sm:flex-1 px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(239,68,68,0.25)] hover:shadow-[0_12px_25px_rgba(239,68,68,0.35)] transition-all active:scale-[0.98]">
                {{ __('admin.delete') }}
            </button>
        </div>
    </x-slot>
</x-admin.modal>
@push('scripts')
<script>
    // Custom Select Initialization logic
    function initCustomSelects() {
        const wrappers = document.querySelectorAll('.custom-select-wrapper');
        wrappers.forEach(wrapper => {
            const button = wrapper.querySelector('.custom-select-button');
            const dropdown = wrapper.querySelector('.custom-select-dropdown');
            const options = wrapper.querySelectorAll('.custom-option');
            const input = wrapper.querySelector('input[type="hidden"]');
            const selectedText = wrapper.querySelector('.selected-text');
            const arrow = wrapper.querySelector('.icon-arrow');
            let isOpen = false;
            function toggleDropdown() {
                isOpen = !isOpen;
                if (isOpen) {
                    dropdown.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-[-10px]');
                    arrow.classList.add('rotate-180');
                    button.classList.add('border-primary', 'ring-2', 'ring-primary/20');
                } else {
                    dropdown.classList.add('opacity-0', 'pointer-events-none', 'translate-y-[-10px]');
                    arrow.classList.remove('rotate-180');
                    button.classList.remove('border-primary', 'ring-2', 'ring-primary/20');
                }
            }
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                // Close other open selects
                document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                    if (w !== wrapper) {
                        const drp = w.querySelector('.custom-select-dropdown');
                        const arr = w.querySelector('.icon-arrow');
                        const btn = w.querySelector('.custom-select-button');
                        if(drp) drp.classList.add('opacity-0', 'pointer-events-none', 'translate-y-[-10px]');
                        if(arr) arr.classList.remove('rotate-180');
                        if(btn) btn.classList.remove('border-primary', 'ring-2', 'ring-primary/20');
                    }
                });
                toggleDropdown();
            });
            options.forEach(option => {
                option.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const value = option.getAttribute('data-value');
                    input.value = value;
                    selectedText.innerHTML = option.innerHTML;
                    toggleDropdown();
                });
            });
            document.addEventListener('click', (e) => {
                if (isOpen && !wrapper.contains(e.target)) {
                    toggleDropdown();
                }
            });
        });
    }
    // Custom Luxury Toast logic
    document.addEventListener('DOMContentLoaded', () => {
        initCustomSelects();
        if (typeof gsap === 'undefined') return;
        // Header entrance
        gsap.from('.dashboard-header-reveal', {
            y: -20,
            opacity: 0,
            duration: 0.8,
            ease: "power3.out"
        });
        // Wrapper/Card staggered entrance
        gsap.from('.list-card', {
            y: 40,
            opacity: 0,
            duration: 0.9,
            ease: "power3.out",
            delay: 0.15
        });
        // Check for session flash messages rendered as JS variables
        @if(session('success'))
            showLuxuryToast('success', "{{ session('success') }}");
        @endif
        @if(session('error'))
            showLuxuryToast('error', "{{ session('error') }}");
        @endif
    });
    // Image Preview logic
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(previewId.replace('_preview', '_placeholder'));
        const img = preview.querySelector('img');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    // Populate Modals
    function editCategory(category) {
        document.getElementById('edit_name_en').value = category.name_en;
        document.getElementById('edit_name_ar').value = category.name_ar;
        document.getElementById('edit_slug').value = category.slug || '';
        document.getElementById('edit_icon').value = category.icon || '';
        // Handle Image
        const preview = document.getElementById('edit_image_preview');
        const placeholder = document.getElementById('edit_image_placeholder');
        const img = document.getElementById('edit_image_display');
        if (category.image) {
            img.src = category.image.startsWith('http') ? category.image : `/storage/${category.image}`;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
        // Custom select update
        const statusInput = document.getElementById('edit_status');
        const statusDisplay = document.getElementById('edit_status_display');
        statusInput.value = category.status;
        const langNodes = {
            active: '<div class="w-2 h-2 rounded-full bg-emerald-500"></div>{{ __("admin.active") }}',
            pending: '<div class="w-2 h-2 rounded-full bg-amber-500"></div>{{ __("admin.pending") }}'
        };
        statusDisplay.innerHTML = langNodes[category.status] || langNodes['active'];
        document.getElementById('editCategoryForm').action = `/admin/categories/${category.id}`;
        openModal('editCategoryModal');
    }
    function confirmDelete(url) {
        document.getElementById('deleteCategoryForm').action = url;
        openModal('deleteConfirmModal');
    }
    // Smart Search & Filtration Logic
    let searchTimeout = null;
    let currentStatus = '{{ request("status", "all") }}';
    let currentSearch = '{{ request("search", "") }}';
    function filterByStatus(status) {
        currentStatus = status;
        // Update UI Tabs
        ['all', 'active', 'pending'].forEach(s => {
            const el = document.getElementById(`tab-${s}`);
            if (s === status) {
                el.classList.add('bg-white', 'dark:bg-zinc-700', 'text-primary', 'shadow-sm');
                el.classList.remove('text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700');
            } else {
                el.classList.remove('bg-white', 'dark:bg-zinc-700', 'text-primary', 'shadow-sm');
                el.classList.add('text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700');
            }
        });
        updateList();
    }
    function debounceSearch(value) {
        clearTimeout(searchTimeout);
        currentSearch = value;
        const spinner = document.getElementById('search-spinner');
        if (value.length > 0) spinner.classList.remove('hidden');
        searchTimeout = setTimeout(() => {
            updateList(() => {
                spinner.classList.add('hidden');
            });
        }, 500);
    }
    async function updateList(callback = null) {
        const container = document.getElementById('category-list-container');
        const overlay = document.getElementById('list-loading-overlay');
        // Show Loading
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
        try {
            const url = new URL(window.location.href);
            url.searchParams.set('status', currentStatus);
            url.searchParams.set('search', currentSearch);
            url.searchParams.set('page', 1); // Reset to page 1 on new filter
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (response.ok) {
                const html = await response.text();
                container.innerHTML = html;
                // Update URL without reload
                window.history.pushState({}, '', url.toString());
                // Re-initialize animations with strict visibility
                if (typeof gsap !== 'undefined') {
                    const targets = container.querySelectorAll('tr, .mobile-card');
                    gsap.set(targets, { opacity: 0, y: 15 });
                    gsap.to(targets, {
                        y: 0,
                        opacity: 1,
                        duration: 0.5,
                        stagger: 0.04,
                        ease: "power2.out",
                        overwrite: true,
                        onComplete: () => {
                            // Safety: Force 100% opacity to avoid "stuck" transparent rows
                            targets.forEach(el => el.style.opacity = '1');
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Filtering failed:', error);
        } finally {
            // Hide Loading
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
            if (callback) callback();
            // Re-bind pagination links
            bindPagination();
        }
    }
    function bindPagination() {
        const links = document.querySelectorAll('#category-list-container .pagination a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                fetchPage(url);
            });
        });
    }
    async function fetchPage(url) {
        const container = document.getElementById('category-list-container');
        const overlay = document.getElementById('list-loading-overlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('opacity-100');
        try {
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (response.ok) {
                const html = await response.text();
                container.innerHTML = html;
                window.history.pushState({}, '', url.toString());
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } catch (error) {
            console.error('Pagination failed:', error);
        } finally {
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
            bindPagination();
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        bindPagination();
    });
    // Inline Status Menu Toggle
    function toggleStatusMenu(event, menuId) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        // Close all other open menus
        document.querySelectorAll('.status-menu').forEach(m => {
            if (m.id !== menuId) {
                m.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
            }
        });
        // Toggle current menu
        menu.classList.toggle('opacity-0');
        menu.classList.toggle('pointer-events-none');
        menu.classList.toggle('-translate-y-2');
    }
    // Close menus when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.status-menu').forEach(m => {
            m.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        });
    });
    // Handle AJAX Status Update
    async function updateCategoryStatus(id, status, el) {
        const url = `/admin/categories/${id}/status`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        // Close the menu immediately
        const menu = el.closest('.status-menu');
        if (menu) menu.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        try {
            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status })
            });
            const result = await response.json();
            if (result.success) {
                showLuxuryToast('success', result.message);
                // Fast UI update
                const activeHtml = `<div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div><span class="text-emerald-700 dark:text-emerald-400">{{ __('admin.active') }}</span><svg class="w-3.5 h-3.5 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                const pendingHtml = `<div class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div><span class="text-amber-700 dark:text-amber-400">{{ __('admin.pending') }}</span><svg class="w-3.5 h-3.5 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                // Update all buttons targeting this category ID (handles desktop and mobile views simultaneously)
                document.querySelectorAll(".status-btn-\\[" + id + "\\]").forEach(btn => {
                    btn.innerHTML = status === 'active' ? activeHtml : pendingHtml;
                });
            } else {
                showLuxuryToast('error', '{{ __('admin.update_failed') }}');
            }
        } catch (error) {
            console.error('Status update failed:', error);
            showLuxuryToast('error', '{{ __('admin.update_failed') }}');
        }
    }
</script>
@endpush
@endsection
