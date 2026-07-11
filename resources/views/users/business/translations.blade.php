@extends('users.layout')

@section('title', __('dashboard.index.translations') ?? 'Manage Translations')
@section('page_title', __('dashboard.index.translations') ?? 'Manage Translations')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="translationManager()">
    
    {{-- Header --}}
    <div class="glass-panel p-6 sm:p-8 rounded-2xl border border-black/5 dark:border-white/[0.04] shadow-sm relative overflow-hidden group">
        <div class="relative z-10 flex flex-col md:flex-row gap-6 md:items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">{{ __('dashboard.index.translations') ?? 'Manage Translations' }}</h2>
                <p class="text-sm text-zinc-500 mt-1">{{ __('dashboard.index.translations_desc') ?? 'Translate your business profile to reach a global audience.' }}</p>
            </div>
            
            <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 custom-scrollbar">
                @foreach($locales as $code => $name)
                    <button type="button" @click="activeTab = '{{ $code }}'"
                            :class="activeTab === '{{ $code }}' ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 whitespace-nowrap">
                        {{ $name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form id="translationsForm" action="{{ route('business.translations.update') }}" method="POST" @submit.prevent="saveTranslations">
        @csrf
        
        <div class="glass-panel p-6 sm:p-8 rounded-2xl border border-black/5 dark:border-white/[0.04] shadow-sm relative overflow-hidden group">
            
            @foreach($locales as $code => $name)
            <div x-show="activeTab === '{{ $code }}'" x-transition.opacity style="display: none;">
                
                <div class="mb-6 flex items-center justify-between border-b border-black/5 dark:border-white/5 pb-4">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $name }} Translation</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Business Name --}}
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.biz_name') ?? 'Business Name' }} ({{ $name }})</label>
                        <input type="text" x-model="formData['{{ $code }}'].name"
                               class="w-full bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition-all duration-200"
                               dir="{{ $code === 'ar' ? 'rtl' : 'ltr' }}">
                    </div>

                    {{-- Description --}}
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.biz_desc') ?? 'Description' }} ({{ $name }})</label>
                        <textarea x-model="formData['{{ $code }}'].description" rows="5"
                                  class="w-full bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none resize-none transition-all duration-200"
                                  dir="{{ $code === 'ar' ? 'rtl' : 'ltr' }}"></textarea>
                    </div>

                    {{-- Meta Title --}}
                    <div class="space-y-2">
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">SEO Title ({{ $name }})</label>
                        <input type="text" x-model="formData['{{ $code }}'].meta_title"
                               class="w-full bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition-all duration-200"
                               dir="{{ $code === 'ar' ? 'rtl' : 'ltr' }}">
                    </div>

                    {{-- Meta Description --}}
                    <div class="space-y-2">
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">SEO Description ({{ $name }})</label>
                        <textarea x-model="formData['{{ $code }}'].meta_description" rows="3"
                                  class="w-full bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none resize-none transition-all duration-200"
                                  dir="{{ $code === 'ar' ? 'rtl' : 'ltr' }}"></textarea>
                    </div>

                </div>
            </div>
            @endforeach

        </div>

        {{-- Save Action --}}
        <div class="mt-6 flex justify-end">
            <button type="submit" :disabled="isSaving"
                    class="flex items-center gap-2 px-8 py-3 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary-dark transition-all active:scale-[0.98] shadow-lg shadow-primary/20 disabled:opacity-70 disabled:cursor-not-allowed">
                <i x-show="!isSaving" class="fa-solid fa-save"></i>
                <i x-show="isSaving" class="fa-solid fa-circle-notch fa-spin"></i>
                <span>{{ __('forms.business.save_changes') ?? 'Save Translations' }}</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('translationManager', () => ({
            activeTab: 'en',
            isSaving: false,
            
            // Initialize data with existing translations from backend
            formData: {
                @foreach($locales as $code => $name)
                '{{ $code }}': {
                    name: `{!! addslashes($translations[$code]->name ?? '') !!}`,
                    description: `{!! addslashes($translations[$code]->description ?? '') !!}`,
                    meta_title: `{!! addslashes($translations[$code]->meta_title ?? '') !!}`,
                    meta_description: `{!! addslashes($translations[$code]->meta_description ?? '') !!}`,
                },
                @endforeach
            },

            saveTranslations() {
                this.isSaving = true;

                // Prepare data structure for the backend
                let payload = {
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    translations: this.formData
                };

                fetch('{{ route("business.translations.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    this.isSaving = false;
                    if (data.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Success', data.message);
                        } else {
                            alert(data.message);
                        }
                    } else {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Error', data.message || 'Something went wrong', null);
                        } else {
                            alert('An error occurred.');
                        }
                    }
                })
                .catch(err => {
                    console.error('Error saving translations', err);
                    this.isSaving = false;
                    if (typeof window.showToast === 'function') {
                        window.showToast('Error', 'Network error or server misconfiguration.', null);
                    } else {
                        alert('Network Error');
                    }
                });
            }
        }));
    });
</script>
@endpush
