@extends('admin.layouts.admin')
@section('title', __('admin.locations') ?? 'Locations')
@section('content')
<style>
    .custom-smart-dropdown.is-active { z-index: 9999 !important; }
    .custom-smart-dropdown .dropdown-trigger { text-align: start !important; }
    .custom-smart-dropdown .dropdown-label { text-align: start !important; }
    [dir="rtl"] .custom-smart-dropdown .dropdown-trigger,
    [dir="rtl"] .custom-smart-dropdown .dropdown-label { text-align: right !important; }
    /* Fix z-index and overflow issues in modals */
    .modal-panel:has(.is-active) { overflow: visible !important; }
    .modal-panel:has(.is-active) .overflow-y-auto { overflow: visible !important; }
</style>
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.locations') ?? 'Locations' }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.manage_locations_desc') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openModal('addCountryModal')" class="flex-1 sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 dark:bg-white hover:bg-slate-700 dark:hover:bg-zinc-200 text-white dark:text-slate-900 rounded-xl font-[900] text-[13px] shadow-sm transition-all active:scale-[0.98] whitespace-nowrap">
                <i class="fa-solid fa-plus text-[12px]"></i> {{ __('admin.add_country') ?? 'Add Country' }}
            </button>
            <button onclick="openModal('addCityModal')" class="flex-1 sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98] whitespace-nowrap">
                <i class="fa-solid fa-plus text-[12px]"></i> {{ __('admin.add_city') ?? 'Add City' }}
            </button>
        </div>
    </div>
    {{-- Main Type Tabs --}}
    <div class="flex items-center space-x-2 border-b border-slate-200 dark:border-zinc-800/60 pb-1">
        <button onclick="switchTab('countries')" id="type-tab-countries" class="px-5 py-3 text-[14px] font-[900] transition-colors relative text-primary dark:text-primary-light">
            <i class="fa-solid fa-earth-americas me-1.5"></i> {{ __('admin.countries') }}
            <div class="absolute bottom-[-1px] left-0 w-full h-[2px] bg-primary rounded-t-full origin-left transition-transform duration-300" id="countries-indicator"></div>
        </button>
        <button onclick="switchTab('cities')" id="type-tab-cities" class="px-5 py-3 text-[14px] font-[900] transition-colors relative text-slate-500 hover:text-slate-700 dark:text-zinc-500 dark:hover:text-zinc-300">
            <i class="fa-solid fa-city me-1.5"></i> {{ __('admin.cities') }}
            <div class="absolute bottom-[-1px] left-0 w-full h-[2px] bg-primary rounded-t-full origin-left scale-x-0 transition-transform duration-300" id="cities-indicator"></div>
        </button>
    </div>
    {{-- Filter & Search Bar --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white/50 dark:bg-zinc-900/30 backdrop-blur-xl p-4 rounded-2xl border border-white/60 dark:border-white/[0.05] shadow-sm">
        {{-- Status Tabs --}}
        <div class="flex items-center p-1 bg-slate-100/50 dark:bg-zinc-800/50 rounded-xl w-full md:w-auto">
            <button onclick="filterByStatus('all')" id="status-all" class="flex-1 md:flex-none px-5 py-2 text-[13px] font-[900] rounded-lg transition-all bg-white dark:bg-zinc-700 text-primary shadow-sm">
                {{ __('admin.all') }}
            </button>
            <button onclick="filterByStatus('active')" id="status-active" class="flex-1 md:flex-none px-5 py-2 text-[13px] font-[900] rounded-lg transition-all text-slate-500 dark:text-zinc-400 hover:text-slate-700">
                {{ __('admin.active') }}
            </button>
            <button onclick="filterByStatus('pending')" id="status-pending" class="flex-1 md:flex-none px-5 py-2 text-[13px] font-[900] rounded-lg transition-all text-slate-500 dark:text-zinc-400 hover:text-slate-700">
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
            <input type="text" id="smart-search" oninput="debounceSearch(this.value)" placeholder="{{ __('admin.search') }}" 
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
        <div id="countries-wrapper" class="block">
            <div id="countries-list-container">
                @include('admin.locations._countries_list')
            </div>
        </div>
        <div id="cities-wrapper" class="hidden">
            <div id="cities-list-container">
                @include('admin.locations._cities_list')
            </div>
        </div>
    </div>
</div>
{{-- Custom Add Country Modal --}}
<x-admin.modal id="addCountryModal" :title="__('admin.add_country') ?? 'Add Country'">
    <form action="{{ route('admin.countries.store') }}" method="POST" id="addCountryForm" class="space-y-5 px-1 pb-1">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.name_en') }}</label>
                <input type="text" name="name_en" required class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400">
            </div>
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.name_ar') }}</label>
                <input type="text" name="name_ar" required dir="rtl" class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white font-cairo shadow-sm placeholder:text-slate-400">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.country_code') }}</label>
                <input type="text" name="code" required placeholder="{{ __('admin.countries_placeholder') ?? 'e.g. US, AE, SA' }}" class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold font-mono py-3 px-4 transition-all text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 uppercase">
            </div>
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.status') }}</label>
                <div class="relative custom-smart-dropdown" id="dropdown_country_status">
                    <input type="hidden" name="status" id="field_country_status" value="active">
                    <button type="button" class="dropdown-trigger w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 hover:border-primary/40 focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 flex items-center justify-between transition-all outline-none">
                        <span class="dropdown-label text-slate-900 dark:text-white">{{ __('admin.active') }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-xl shadow-2xl overflow-hidden opacity-0 translate-y-2">
                        <div class="dropdown-list p-2 space-y-1">
                            <div class="dropdown-item p-3 rounded-lg hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="active" data-label="{{ __('admin.active') }}">{{ __('admin.active') }}</div>
                            <div class="dropdown-item p-3 rounded-lg hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="pending" data-label="{{ __('admin.pending') }}">{{ __('admin.pending') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <x-slot name="footer">
        <div class="flex items-center justify-end gap-3 w-full">
            <button type="button" onclick="closeModal('addCountryModal')" class="w-full sm:w-auto px-5 py-2.5 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">{{ __('admin.cancel') }}</button>
            <button type="button" onclick="submitForm('addCountryForm', this);" class="w-full sm:w-auto px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] transition-all">{{ __('admin.save') }}</button>
        </div>
    </x-slot>
</x-admin.modal>
{{-- Custom Add City Modal --}}
<x-admin.modal id="addCityModal" :title="__('admin.add_city') ?? 'Add City'">
    <form action="{{ route('admin.cities.store') }}" method="POST" id="addCityForm" class="space-y-5 px-1 pb-1">
        @csrf
        <div>
            <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.countries') }}</label>
            <div class="relative custom-smart-dropdown" id="dropdown_city_country">
                <input type="hidden" name="country_id" id="field_city_country_id">
                <button type="button" class="dropdown-trigger w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 hover:border-primary/40 focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 flex items-center justify-between transition-all outline-none">
                    <span class="dropdown-label text-slate-400 dark:text-zinc-500">{{ __('admin.select_country') }}</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-xl shadow-2xl overflow-hidden opacity-0 translate-y-2">
                    <div class="p-3 border-b border-slate-100 dark:border-white/5">
                        <input type="text" class="dropdown-search w-full bg-slate-50 dark:bg-zinc-900 border-none rounded-lg py-2 px-3 text-xs font-bold outline-none focus:ring-2 focus:ring-primary/20" placeholder="{{ __('admin.search') }}">
                    </div>
                    <div class="dropdown-list max-h-48 overflow-y-auto p-2 space-y-1">
                        @foreach(\App\Models\Country::all() as $country)
                            <div class="dropdown-item p-3 rounded-lg hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="{{ $country->id }}" data-label="{{ $country->name_en }} / {{ $country->name_ar }}">
                                {{ $country->name_en }} / {{ $country->name_ar }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.name_en') }}</label>
                <input type="text" name="name_en" required class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white shadow-sm">
            </div>
            <div>
                <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.name_ar') }}</label>
                <input type="text" name="name_ar" required dir="rtl" class="w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 focus:bg-white dark:focus:bg-[#09090b] focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 transition-all text-slate-900 dark:text-white font-cairo shadow-sm">
            </div>
        </div>
        <div>
            <label class="block text-[13px] font-[900] text-slate-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">{{ __('admin.status') }}</label>
            <div class="relative custom-smart-dropdown" id="dropdown_city_status">
                <input type="hidden" name="status" id="field_city_status" value="active">
                <button type="button" class="dropdown-trigger w-full bg-slate-50 dark:bg-[#121214]/50 border border-slate-200 dark:border-white/10 hover:border-primary/40 focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl text-[14px] font-bold py-3 px-4 flex items-center justify-between transition-all outline-none">
                    <span class="dropdown-label text-slate-900 dark:text-white">{{ __('admin.active') }}</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-xl shadow-2xl overflow-hidden opacity-0 translate-y-2">
                    <div class="dropdown-list p-2 space-y-1">
                        <div class="dropdown-item p-3 rounded-lg hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="active" data-label="{{ __('admin.active') }}">{{ __('admin.active') }}</div>
                        <div class="dropdown-item p-3 rounded-lg hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="pending" data-label="{{ __('admin.pending') }}">{{ __('admin.pending') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <x-slot name="footer">
        <div class="flex items-center justify-end gap-3 w-full">
            <button type="button" onclick="closeModal('addCityModal')" class="w-full sm:w-auto px-5 py-2.5 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">{{ __('admin.cancel') }}</button>
            <button type="button" onclick="submitForm('addCityForm', this);" class="w-full sm:w-auto px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] transition-all">{{ __('admin.save') }}</button>
        </div>
    </x-slot>
</x-admin.modal>
{{-- Native Delete Modal --}}
<x-admin.modal id="deleteConfirmModal" :title="__('admin.warning')" class="max-w-md">
    <div class="text-center px-4 py-8">
        <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-500 mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-3">{{ __('admin.confirm_deletion') }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400">{{ __('admin.confirm_deletion_resource') }}</p>
    </div>
    <form action="" method="POST" id="deleteLocationForm" class="hidden">
        @csrf @method('DELETE')
    </form>
    <x-slot name="footer">
        <div class="flex items-center justify-center gap-3 w-full pb-2">
            <button type="button" onclick="closeModal('deleteConfirmModal')" class="flex-1 px-5 py-3 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[14px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">{{ __('admin.cancel') }}</button>
            <button type="button" onclick="submitForm('deleteLocationForm', this);" class="flex-1 px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-[900] text-[14px] transition-all">{{ __('admin.delete') }}</button>
        </div>
    </x-slot>
</x-admin.modal>
@push('scripts')
<script>
    /* ── Dropdown Initialization ── */
    if (window.SmartDropdownManager) {
        SmartDropdownManager.init();
    }
    let currentTab = 'countries';
    let currentStatus = 'all';
    let currentSearch = '';
    let searchTimeout = null;
    function switchTab(tab) {
        if (currentTab === tab) return;
        currentTab = tab;
        // Reset filters when switching
        document.getElementById('smart-search').value = '';
        currentSearch = '';
        currentStatus = 'all';
        // Update indicators & UI styles
        if(tab === 'countries'){
            document.getElementById('type-tab-countries').classList.replace('text-slate-500', 'text-primary');
            document.getElementById('type-tab-countries').classList.replace('dark:text-zinc-500', 'dark:text-primary-light');
            document.getElementById('type-tab-countries').classList.remove('hover:text-slate-700', 'dark:hover:text-zinc-300');
            document.getElementById('countries-indicator').classList.remove('scale-x-0');
            document.getElementById('type-tab-cities').classList.replace('text-primary', 'text-slate-500');
            document.getElementById('type-tab-cities').classList.replace('dark:text-primary-light', 'dark:text-zinc-500');
            document.getElementById('type-tab-cities').classList.add('hover:text-slate-700', 'dark:hover:text-zinc-300');
            document.getElementById('cities-indicator').classList.add('scale-x-0');
            document.getElementById('countries-wrapper').classList.replace('hidden', 'block');
            document.getElementById('cities-wrapper').classList.replace('block', 'hidden');
        } else {
            document.getElementById('type-tab-cities').classList.replace('text-slate-500', 'text-primary');
            document.getElementById('type-tab-cities').classList.replace('dark:text-zinc-500', 'dark:text-primary-light');
            document.getElementById('type-tab-cities').classList.remove('hover:text-slate-700', 'dark:hover:text-zinc-300');
            document.getElementById('cities-indicator').classList.remove('scale-x-0');
            document.getElementById('type-tab-countries').classList.replace('text-primary', 'text-slate-500');
            document.getElementById('type-tab-countries').classList.replace('dark:text-primary-light', 'dark:text-zinc-500');
            document.getElementById('type-tab-countries').classList.add('hover:text-slate-700', 'dark:hover:text-zinc-300');
            document.getElementById('countries-indicator').classList.add('scale-x-0');
            document.getElementById('cities-wrapper').classList.replace('hidden', 'block');
            document.getElementById('countries-wrapper').classList.replace('block', 'hidden');
        }
        ['all', 'active', 'pending'].forEach(s => {
            const el = document.getElementById(`status-${s}`);
            if (s === 'all') {
                el.classList.add('bg-white', 'dark:bg-zinc-700', 'text-primary', 'shadow-sm');
                el.classList.remove('text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700');
            } else {
                el.classList.remove('bg-white', 'dark:bg-zinc-700', 'text-primary', 'shadow-sm');
                el.classList.add('text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700');
            }
        });
        fetchList();
    }
    function filterByStatus(status) {
        currentStatus = status;
        ['all', 'active', 'pending'].forEach(s => {
            const el = document.getElementById(`status-${s}`);
            if (s === status) {
                el.classList.add('bg-white', 'dark:bg-zinc-700', 'text-primary', 'shadow-sm');
                el.classList.remove('text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700');
            } else {
                el.classList.remove('bg-white', 'dark:bg-zinc-700', 'text-primary', 'shadow-sm');
                el.classList.add('text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700');
            }
        });
        fetchList();
    }
    function debounceSearch(value) {
        clearTimeout(searchTimeout);
        currentSearch = value;
        const spinner = document.getElementById('search-spinner');
        if (value.length > 0) spinner.classList.remove('hidden');
        searchTimeout = setTimeout(() => {
            fetchList(() => {
                spinner.classList.add('hidden');
            });
        }, 500);
    }
    async function fetchList(callback = null, targetUrl = null) {
        const urlStr = targetUrl || `/admin/${currentTab}?status=${currentStatus}&search=${currentSearch}&page=1`;
        const container = document.getElementById(`${currentTab}-list-container`);
        const overlay = document.getElementById('list-loading-overlay');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
        try {
            const response = await fetch(urlStr, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (response.ok) {
                const html = await response.text();
                container.innerHTML = html;
                window.history.pushState({}, '', window.location.pathname + `?tab=${currentTab}`);
                if (typeof gap !== 'undefined') {
                     // Re-animate the entrance for custom feel
                    const targets = container.querySelectorAll('tr, .mobile-card');
                    gsap.fromTo(targets, { opacity: 0, y: 15 }, { opacity: 1, y: 0, duration: 0.4, stagger: 0.03 });
                }
            }
        } catch (error) { console.error('{{ __('admin.fetch_failed') }}', error); } finally {
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
            if(callback) callback();
            bindPagination();
        }
    }
    function bindPagination() {
        const links = document.querySelectorAll('.pagination a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchList(null, this.href);
            });
        });
    }
    function toggleStatusMenu(event, menuId, type) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        document.querySelectorAll('.status-menu').forEach(m => {
            if (m.id !== menuId) m.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        });
        menu.classList.toggle('opacity-0');
        menu.classList.toggle('pointer-events-none');
        menu.classList.toggle('-translate-y-2');
    }
    document.addEventListener('click', () => {
        document.querySelectorAll('.status-menu').forEach(m => {
            m.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        });
    });
    async function updateLocationStatus(id, status, el, type) {
        const url = `/admin/${type}/${id}/status`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const menu = el.closest('.status-menu');
        if (menu) menu.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        try {
            const response = await fetch(url, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ status })
            });
            const result = await response.json();
            if (result.success) {
                if(typeof showLuxuryToast !== 'undefined') showLuxuryToast('success', result.message);
                const activeHtml = `<div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div><span class="text-emerald-700 dark:text-emerald-400">{{ __('admin.active') }}</span><svg class="w-3.5 h-3.5 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                const pendingHtml = `<div class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div><span class="text-amber-700 dark:text-amber-400">{{ __('admin.pending') }}</span><svg class="w-3.5 h-3.5 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;
                let typePrefix = type === 'countries' ? 'country' : 'city';
                document.querySelectorAll(`.status-btn-${typePrefix}-\\[${id}\\]`).forEach(btn => {
                    btn.innerHTML = status === 'active' ? activeHtml : pendingHtml;
                });
            }
        } catch (error) { console.error('Error:', error); }
    }
    function confirmDelete(url) {
        document.getElementById('deleteLocationForm').action = url;
        openModal('deleteConfirmModal');
    }
    document.addEventListener('DOMContentLoaded', () => {
        SmartDropdownManager.init();
        bindPagination();
        // Handle initial tab from URL if present
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get('tab') === 'cities'){
            switchTab('cities');
        }
        if (typeof gsap === 'undefined') return;
        gsap.from('.dashboard-header-reveal', { y: -20, opacity: 0, duration: 0.8, ease: "power3.out" });
        gsap.from('.list-card', { y: 40, opacity: 0, duration: 0.9, ease: "power3.out", delay: 0.15 });
        @if(session('success'))
            if(typeof showLuxuryToast !== 'undefined') showLuxuryToast('success', "{{ session('success') }}");
        @endif
        @if(session('error'))
            if(typeof showLuxuryToast !== 'undefined') showLuxuryToast('error', "{{ session('error') }}");
        @endif
    });
</script>
@endpush
@endsection
