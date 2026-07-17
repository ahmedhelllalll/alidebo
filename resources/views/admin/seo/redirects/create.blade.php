@extends('admin.layouts.admin')

@section('title', __('admin.add_new'))

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.add_new') }}</h1>
        </div>
        <a href="{{ route('admin.seo.redirects.index') }}" class="w-fit px-4 py-2.5 bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-arrow-left rtl:fa-arrow-right"></i>
            {{ __('admin.cancel') }}
        </a>
    </div>

    <div class="bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl shadow-sm dashboard-card-reveal p-6">
        <form action="{{ route('admin.seo.redirects.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="source_url" class="block text-[13px] font-bold text-slate-700 dark:text-zinc-300">{{ __('admin.source_url') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="source_url" id="source_url" value="{{ old('source_url', request('source_url')) }}" class="w-full h-11 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 text-[13px] text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required placeholder="/old-url">
                    @error('source_url')
                        <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="target_url" class="block text-[13px] font-bold text-slate-700 dark:text-zinc-300">{{ __('admin.target_url') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="target_url" id="target_url" value="{{ old('target_url') }}" class="w-full h-11 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-4 text-[13px] text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required placeholder="/new-url or https://...">
                    
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="text-[12px] font-medium text-slate-500 dark:text-zinc-400">{{ __('admin.suggested_links') }}</span>
                        <button type="button" onclick="document.getElementById('target_url').value = '/'" class="px-2 py-1 bg-slate-100 dark:bg-white/10 hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20 dark:hover:text-primary-light text-slate-600 dark:text-zinc-300 rounded text-[11px] font-bold transition-colors">/</button>
                        <button type="button" onclick="document.getElementById('target_url').value = '/directory'" class="px-2 py-1 bg-slate-100 dark:bg-white/10 hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20 dark:hover:text-primary-light text-slate-600 dark:text-zinc-300 rounded text-[11px] font-bold transition-colors">/directory</button>
                        <button type="button" onclick="document.getElementById('target_url').value = '/blog'" class="px-2 py-1 bg-slate-100 dark:bg-white/10 hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20 dark:hover:text-primary-light text-slate-600 dark:text-zinc-300 rounded text-[11px] font-bold transition-colors">/blog</button>
                        <button type="button" onclick="document.getElementById('target_url').value = '/contact'" class="px-2 py-1 bg-slate-100 dark:bg-white/10 hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20 dark:hover:text-primary-light text-slate-600 dark:text-zinc-300 rounded text-[11px] font-bold transition-colors">/contact</button>
                    </div>

                    @error('target_url')
                        <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="status_code" class="block text-[13px] font-bold text-slate-700 dark:text-zinc-300">{{ __('admin.status_code') }} <span class="text-red-500">*</span></label>
                    <div class="relative custom-smart-dropdown" id="dropdown_status_code">
                        <div class="relative flex items-center group">
                            <div class="absolute start-0 top-0 bottom-0 w-11 flex items-center justify-center text-slate-400 z-10 pointer-events-none transition-colors group-focus-within:text-primary">
                                <i class="fa-solid fa-server text-[13px]"></i>
                            </div>
                            <input type="hidden" name="status_code" id="status_code" required value="{{ old('status_code', '301') }}">
                            <button type="button" class="dropdown-trigger w-full h-11 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl ps-11 pe-4 text-[13px] text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all flex items-center justify-between cursor-pointer">
                                <span class="dropdown-label truncate">{{ old('status_code') == '302' ? '302 Temporary' : '301 Permanent' }}</span>
                                <i aria-hidden="true" class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-300"></i>
                            </button>
                        </div>
                        <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-xl shadow-lg overflow-hidden opacity-0 translate-y-2">
                            <div class="dropdown-list p-2 space-y-1">
                                <div class="dropdown-item p-2.5 rounded-lg hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-zinc-300 font-medium text-[13px] cursor-pointer transition-colors" data-id="301" data-label="301 Permanent">
                                    301 Permanent
                                </div>
                                <div class="dropdown-item p-2.5 rounded-lg hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-zinc-300 font-medium text-[13px] cursor-pointer transition-colors" data-id="302" data-label="302 Temporary">
                                    302 Temporary
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('status_code')
                        <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-white/10">
                <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ __('admin.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    if (window.SmartDropdownManager) {
        SmartDropdownManager.init();
    }
</script>
@endpush
