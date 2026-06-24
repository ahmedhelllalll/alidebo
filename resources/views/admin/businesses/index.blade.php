@extends('admin.layouts.admin')
@section('title', __('admin.businesses'))
@section('content')
<div id="businesses-main-wrapper" class="relative min-h-[600px]">
    {{-- Section 1: Listing --}}
    <div id="businesses-list-section" class="space-y-6 transition-all duration-500">
        {{-- Header & Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal" data-total="{{ $businesses->total() }}">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.businesses') }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.all_businesses') }} (<span id="total-count-header" data-total="{{ $businesses->total() }}">{{ $businesses->total() }}</span>)</p>
        </div>
        <a href="{{ route('admin.businesses.create') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            {{ __('admin.add_new') }}
        </a>
    </div>
    {{-- Filter & Search Bar --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white/50 dark:bg-zinc-900/30 backdrop-blur-xl p-4 rounded-2xl border border-white/60 dark:border-white/[0.05] shadow-sm relative z-20">
        {{-- Status Tabs --}}
        <div class="flex items-center p-1 bg-slate-100/50 dark:bg-zinc-800/50 rounded-xl w-full md:w-auto overflow-x-auto custom-scrollbar-hide">
            <button onclick="setStatusFilter('', this)" id="tab-all" class="flex-1 md:flex-none px-5 py-2 text-[13px] font-[900] rounded-lg transition-all {{ !request('status') ? 'bg-white dark:bg-zinc-700 text-primary shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700' }}">
                {{ __('admin.all') }}
            </button>
            <button onclick="setStatusFilter('pending', this)" id="tab-pending" class="flex-1 md:flex-none px-5 py-2 text-[13px] font-[900] rounded-lg transition-all {{ request('status') == 'pending' ? 'bg-white dark:bg-zinc-700 text-primary shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700' }}">
                {{ __('admin.pending') }}
            </button>
            <button onclick="setStatusFilter('approved', this)" id="tab-approved" class="flex-1 md:flex-none px-5 py-2 text-[13px] font-[900] rounded-lg transition-all {{ request('status') == 'approved' ? 'bg-white dark:bg-zinc-700 text-primary shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700' }}">
                {{ __('admin.approved') }}
            </button>
        </div>
        {{-- Smart Search --}}
        <div class="relative w-full md:w-1/2 group" id="search-input-wrapper">
            <div class="absolute inset-y-0 start-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" id="business-search" oninput="debounceSearch()" placeholder="{{ __('admin.search') }}" autocomplete="off"
                class="w-full bg-slate-100/50 dark:bg-zinc-800/50 border border-transparent focus:border-primary/30 focus:bg-white dark:focus:bg-[#09090b] focus:ring-4 focus:ring-primary/10 rounded-xl text-[13px] font-bold py-2.5 ps-11 pe-10 transition-all text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 shadow-sm">
            {{-- Search Spinner --}}
            <div id="search-spinner" class="absolute inset-y-0 end-4 flex items-center hidden">
                <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            {{-- Autocomplete Dropdown --}}
            <div id="search-suggestions-dropdown" class="absolute top-full mt-2 start-0 w-full bg-white/95 dark:bg-[#121214]/95 backdrop-blur-md border border-slate-200/60 dark:border-white/[0.08] rounded-2xl shadow-xl hidden z-50 max-h-[350px] overflow-y-auto custom-scrollbar p-2">
                <div id="search-suggestions-content" class="space-y-1">
                    {{-- Dynamically populated via JS --}}
                </div>
            </div>
        </div>
    </div>
    {{-- Bulk Actions Bar --}}
    <div id="bulk-actions-bar" class="hidden items-center justify-between bg-primary/10 border border-primary/20 p-3 rounded-2xl mb-4 transition-all duration-300">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center font-bold text-sm" id="bulk-selected-count">0</span>
            <span class="text-sm font-bold text-slate-700 dark:text-zinc-300">{{ __('admin.businesses_selected') ?? 'Businesses Selected' }}</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="executeBulkStatus('approved')" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
                {{ __('admin.approve_selected') ?? 'Approve Selected' }}
            </button>
            <button onclick="executeBulkStatus('pending')" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
                {{ __('admin.pend_selected') ?? 'Mark as Pending' }}
            </button>
            <button onclick="executeBulkStatus('rejected')" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
                {{ __('admin.reject_selected') ?? 'Reject Selected' }}
            </button>
        </div>
    </div>
    <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative z-10 w-full min-h-[400px] overflow-hidden">
        {{-- Smooth Overlay Loader --}}
        <div id="list-loading-overlay" class="absolute inset-0 z-50 bg-white/60 dark:bg-[#121214]/60 backdrop-blur-[4px] flex flex-col items-center justify-center opacity-0 transition-opacity duration-300 pointer-events-none hidden">
            <div class="relative flex items-center justify-center">
                <div class="w-14 h-14 rounded-full border-4 border-slate-200/50 dark:border-zinc-700/50"></div>
                <div class="w-14 h-14 rounded-full border-4 border-primary border-t-transparent animate-spin absolute inset-0"></div>
            </div>
            <p class="mt-4 text-[11px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-[0.2em] animate-pulse">{{ __('admin.loading_data') ?? 'L O A D I N G' }}</p>
        </div>
        <div id="business-list-container" class="relative z-10 transition-all duration-300">
            @include('admin.businesses.partials.table', ['businesses' => $businesses])
        </div>
    </div>
    </div>
</div>
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
    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full pb-2">
            <button type="button" onclick="closeModal('deleteConfirmModal')" class="w-full sm:flex-1 px-5 py-3 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[14px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">
                {{ __('admin.cancel') }}
            </button>
            <button type="button" id="confirmDeleteBtn" onclick="executeDeleteBusiness();" class="w-full sm:flex-1 px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(239,68,68,0.25)] hover:shadow-[0_12px_25px_rgba(239,68,68,0.35)] transition-all active:scale-[0.98]">
                {{ __('admin.delete') }}
            </button>
        </div>
    </x-slot>
</x-admin.modal>
<x-admin.modal id="rejectBusinessModal" title="{{ __('admin.reject_business') }}" width="max-w-md">
    <div class="p-6 space-y-4">
        <div class="p-4 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-100 dark:border-red-500/20">
            <p class="text-xs text-red-600 dark:text-red-400 font-bold leading-relaxed">
                {{ __('admin.rejection_reason_email') }}
            </p>
        </div>
        <textarea id="reject_admin_notes" rows="4" class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-xl text-sm p-4 outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500" placeholder="{{ __('admin.description_placeholder') }}"></textarea>
    </div>
    <x-slot name="footer">
        <button onclick="submitRejection()" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-red-500/20">
            {{ __('admin.confirm_rejection') }}
        </button>
    </x-slot>
</x-admin.modal>

@push('scripts')
<script>
    let currentStatus = '{{ request("status", "") }}';
    let currentSearch = '{{ request("search", "") }}';
    let searchTimeout = null;
    let businessToReject = null;
    let businessToDeleteId = null;
// --- Global Modal Engine (Quiet Luxury) ---
    window.modals = window.modals || {};
    function initModals() {
        console.log('Initializing Modals...');
        const modalElements = document.querySelectorAll('[role="dialog"]');
        if (modalElements.length === 0) return;
        modalElements.forEach(modalEl => {
            const id = modalEl.id;
            if (!id || window.modals[id]) return;
            const panel = modalEl.querySelector('.modal-panel');
            const backdrop = modalEl.querySelector('.modal-backdrop');
            window.modals[id] = {
                id: id,
                el: modalEl,
                show: function() {
                    console.log(`Showing modal: ${id}`);
                    this.el.classList.remove('hidden');
                    this.el.classList.add('flex');
                    this.el.style.display = 'flex'; // Triple-ensure visibility
                    this.el.style.opacity = '1';
                    this.el.style.pointerEvents = 'auto';
                    gsap.fromTo(backdrop, { opacity: 0 }, { opacity: 1, duration: 0.4, overwrite: 'auto' });
                    gsap.fromTo(panel, 
                        { opacity: 0, scale: 0.9, y: 20 }, 
                        { opacity: 1, scale: 1, y: 0, duration: 0.5, ease: 'expo.out', clearProps: "transform", overwrite: 'auto' }
                    );
                },
                hide: function() {
                    console.log(`Hiding modal: ${id}`);
                    gsap.to(panel, { opacity: 0, scale: 0.95, y: 15, duration: 0.3, ease: 'power2.in', overwrite: 'auto' });
                    gsap.to(backdrop, { opacity: 0, duration: 0.3, overwrite: 'auto', onComplete: () => {
                        this.el.classList.add('hidden');
                        this.el.classList.remove('flex');
                        this.el.style.display = ''; // Reset inline display
                        this.el.style.opacity = ''; // Reset inline opacity
                        this.el.style.pointerEvents = ''; // Reset inline pointer-events
                    }});
                }
            };
        });
    }
    // Export for inline onclicks in component
    window.closeModal = (id) => window.modals[id]?.hide();
    initModals();

    function openEditForm(id) {
        window.location.href = `${window.AppConfig.adminUrl}/businesses/${id}/edit`;
    }

    function promptDeleteBusiness(id) {
        businessToDeleteId = id;
        window.modals?.deleteConfirmModal?.show() || document.getElementById('deleteConfirmModal').classList.remove('hidden');
    }
    async function executeDeleteBusiness() {
        if(!businessToDeleteId) return;
        const btn = document.getElementById('confirmDeleteBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';
        try {
            const res = await (await fetch(`${window.AppConfig.adminUrl}/businesses/${businessToDeleteId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': window.AppConfig.csrfToken || '{{ csrf_token() }}', 'Accept': 'application/json' }
            })).json();
            if (res.success) {
                showToast('success', res.message);
                updateList();
            }
        } catch (e) { showToast('error', '{{ __("admin.deletion_failed") }}'); }
        finally {
            businessToDeleteId = null;
            btn.innerHTML = originalText;
            window.modals?.deleteConfirmModal?.hide();
        }
    }
    // --- Bulk Actions ---
    function toggleAllCheckboxes(source) {
        const checkboxes = document.querySelectorAll('.business-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateBulkActionVisibility();
    }

    function updateBulkActionVisibility() {
        const selected = document.querySelectorAll('.business-checkbox:checked').length;
        const bulkBar = document.getElementById('bulk-actions-bar');
        const countSpan = document.getElementById('bulk-selected-count');
        if (selected > 0) {
            countSpan.innerText = selected;
            bulkBar.classList.remove('hidden');
            bulkBar.classList.add('flex');
        } else {
            bulkBar.classList.add('hidden');
            bulkBar.classList.remove('flex');
        }
    }

    async function executeBulkStatus(status) {
        const checkboxes = document.querySelectorAll('.business-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value);
        if (ids.length === 0) return;

        try {
            const res = await (await fetch(`${window.AppConfig.adminUrl}/businesses/bulk-status`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': window.AppConfig.csrfToken || '{{ csrf_token() }}', 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ids: ids, status: status })
            })).json();
            if (res.success) {
                showToast('success', res.message);
                updateList();
            } else {
                showToast('error', res.message || 'Error occurred');
            }
        } catch (e) { 
            showToast('error', 'Error updating bulk status'); 
        }
    }

    // --- Search & Filters ---
    function setStatusFilter(status, btn) {
        currentStatus = status;
        ['all', 'pending', 'approved'].forEach(s => {
            const tabId = s === 'all' ? 'tab-all' : `tab-${s}`;
            const el = document.getElementById(tabId);
            if (!el) return;
            if ((s === 'all' && status === '') || s === status) {
                el.classList.add('bg-white', 'dark:bg-zinc-700', 'text-primary', 'shadow-sm');
                el.classList.remove('text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700');
            } else {
                el.classList.remove('bg-white', 'dark:bg-zinc-700', 'text-primary', 'shadow-sm');
                el.classList.add('text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700');
            }
        });
        updateList();
    }
    function debounceSearch() {
        clearTimeout(searchTimeout);
        const input = document.getElementById('business-search');
        if (!input) return;
        
        const query = input.value.trim();
        const dropdown = document.getElementById('search-suggestions-dropdown');
        const spinner = document.getElementById('search-spinner');
        
        if (query.length === 0) {
            if (dropdown) dropdown.classList.add('hidden');
            if (spinner) spinner.classList.add('hidden');
            return;
        }
        
        if (spinner) spinner.classList.remove('hidden');
        
        searchTimeout = setTimeout(async () => {
            try {
                const url = new URL('{{ route("admin.businesses.index") }}');
                url.searchParams.set('search', query);
                url.searchParams.set('suggest', '1');
                
                const response = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (response.ok) {
                    const results = await response.json();
                    renderSuggestions(results);
                }
            } catch (error) {
                console.error('Suggestions fetch failed:', error);
            } finally {
                if (spinner) spinner.classList.add('hidden');
            }
        }, 200);
    }

    function renderSuggestions(results) {
        const dropdown = document.getElementById('search-suggestions-dropdown');
        const content = document.getElementById('search-suggestions-content');
        if (!dropdown || !content) return;
        
        content.innerHTML = '';
        
        if (results.length === 0) {
            content.innerHTML = `
                <div class="flex flex-col items-center justify-center px-4 py-10 text-center">
                    <div class="w-14 h-14 rounded-full bg-slate-50 dark:bg-zinc-800/80 flex items-center justify-center mb-4 border border-slate-100 dark:border-white/5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)]">
                        <svg class="w-6 h-6 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                        </svg>
                    </div>
                    <p class="text-[14px] font-[900] text-slate-900 dark:text-white tracking-tight">{{ __('admin.no_results_found') }}</p>
                    <p class="text-[12px] font-medium text-slate-500 dark:text-zinc-400 mt-1.5">{{ __('admin.try_adjusting_filters') ?? 'Try adjusting your search query' }}</p>
                </div>
            `;
            dropdown.classList.remove('hidden');
            return;
        }
        
        results.forEach(biz => {
            const row = document.createElement('div');
            row.className = "flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-zinc-800/40 transition-all cursor-pointer group/item text-start";
            row.onclick = () => {
                window.location.href = biz.edit_url;
            };
            
            let logoHtml = '';
            if (biz.logo) {
                logoHtml = `<img src="${biz.logo}" class="w-full h-full object-cover">`;
            } else {
                logoHtml = `
                    <div class="w-full h-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center">
                        <span class="text-sm text-primary font-bold">${biz.name.substring(0, 1)}</span>
                    </div>
                `;
            }
            
            let metaText = `${biz.category} • ${biz.city}`;
            if (biz.owner) {
                metaText += ` • ${biz.owner}`;
            }
            
            let statusClass = '';
            if (biz.status === 'approved') {
                statusClass = 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20';
            } else if (biz.status === 'pending') {
                statusClass = 'bg-amber-500/10 text-amber-600 border border-amber-500/20';
            } else {
                statusClass = 'bg-red-500/10 text-red-600 border border-red-500/20';
            }
            
            row.innerHTML = `
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-9 h-9 rounded-xl border border-slate-200/60 dark:border-zinc-700/50 flex items-center justify-center bg-white dark:bg-zinc-900 overflow-hidden shrink-0 group-hover/item:border-primary/30 transition-colors">
                        ${logoHtml}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-800 dark:text-zinc-200 truncate group-hover/item:text-primary transition-colors">${biz.name}</p>
                        <p class="text-[10px] font-medium text-slate-400 mt-0.5 truncate">${metaText}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0 ml-2 rtl:mr-2 rtl:ml-0">
                    <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-lg ${statusClass}">
                        ${biz.status}
                    </span>
                    <a href="${biz.view_url}" target="_blank" onclick="event.stopPropagation();" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 dark:hover:bg-primary/20 rounded-lg transition-all" title="{{ __('admin.view_business') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            `;
            
            content.appendChild(row);
        });
        
        dropdown.classList.remove('hidden');
    }

    async function updateList(callback = null) {
        const container = document.getElementById('business-list-container');
        const overlay = document.getElementById('list-loading-overlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.add('opacity-100');
                overlay.classList.remove('pointer-events-none');
            }, 10);
        }
        try {
            const url = new URL(window.location.href);
            url.searchParams.set('status', currentStatus);
            url.searchParams.set('page', 1);
            const response = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (response.ok) {
                const html = await response.text();
                container.innerHTML = html;
                window.history.pushState({}, '', url.toString());
                if (typeof gsap !== 'undefined') {
                    const targets = container.querySelectorAll('tr, .mobile-card');
                    gsap.fromTo(targets, 
                        { opacity: 0, scale: 0.98, y: 15 },
                        { 
                            y: 0, 
                            opacity: 1, 
                            scale: 1,
                            duration: 0.5, 
                            stagger: 0.04, 
                            ease: "power2.out"
                        }
                    );
                }
            }
        } catch (error) { 
            console.error('Filtering failed:', error); 
            showToast('error', 'Error refreshing list');
        } finally {
            if (callback) callback();
            if (overlay) {
                overlay.classList.remove('opacity-100');
                overlay.classList.add('pointer-events-none');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
            bindPagination();
            updateBulkActionVisibility();
        }
    }
    // --- Helpers ---
    function toggleStatusMenu(event, menuId) {
        event.stopPropagation();
        
        // Close other menus and reset their parent z-indexes
        document.querySelectorAll('.status-menu').forEach(menu => {
            if (menu.id !== menuId) {
                menu.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
                const td = menu.closest('td');
                const tr = menu.closest('tr');
                const mobileCard = menu.closest('.mobile-card');
                if (td) { td.style.zIndex = ''; td.style.position = ''; }
                if (tr) { tr.style.zIndex = ''; tr.style.position = ''; }
                if (mobileCard) { mobileCard.style.zIndex = ''; mobileCard.style.position = ''; }
            }
        });

        const menu = document.getElementById(menuId);
        if (menu) {
            const isOpening = menu.classList.contains('opacity-0');
            menu.classList.toggle('opacity-0');
            menu.classList.toggle('pointer-events-none');
            menu.classList.toggle('-translate-y-2');
            
            const td = menu.closest('td');
            const tr = menu.closest('tr');
            const mobileCard = menu.closest('.mobile-card');
            
            if (isOpening) {
                if (td) { td.style.zIndex = '50'; td.style.position = 'relative'; }
                if (tr) { tr.style.zIndex = '50'; tr.style.position = 'relative'; }
                if (mobileCard) { mobileCard.style.zIndex = '50'; mobileCard.style.position = 'relative'; }
            } else {
                if (td) { td.style.zIndex = ''; td.style.position = ''; }
                if (tr) { tr.style.zIndex = ''; tr.style.position = ''; }
                if (mobileCard) { mobileCard.style.zIndex = ''; mobileCard.style.position = ''; }
            }
        }
    }

    document.addEventListener('click', () => {
        document.querySelectorAll('.status-menu').forEach(menu => {
            menu.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
            const td = menu.closest('td');
            const tr = menu.closest('tr');
            const mobileCard = menu.closest('.mobile-card');
            if (td) { td.style.zIndex = ''; td.style.position = ''; }
            if (tr) { tr.style.zIndex = ''; tr.style.position = ''; }
            if (mobileCard) { mobileCard.style.zIndex = ''; mobileCard.style.position = ''; }
        });
    });

    async function updateBusinessStatus(id, status, btnElement = null) {
        if (btnElement) {
            const originalHTML = btnElement.innerHTML;
            btnElement.innerHTML = '<i class="fa-solid fa-spinner animate-spin mx-auto text-primary"></i>';
        }
        
        try {
            const res = await (await fetch(`${window.AppConfig.adminUrl}/businesses/${id}/status`, {
                method: 'PATCH',
                headers: { 
                    'X-CSRF-TOKEN': window.AppConfig.csrfToken || '{{ csrf_token() }}', 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })).json();

            if (res.success) {
                showToast('success', res.message);
                updateList();
            } else {
                showToast('error', res.message || 'Error updating status');
            }
        } catch (e) {
            showToast('error', 'Network error while updating status');
        }
    }

    function openRejectModal(id) {
        businessToReject = id;
        document.getElementById('reject_admin_notes').value = '';
        window.modals?.rejectBusinessModal?.show() || document.getElementById('rejectBusinessModal').classList.remove('hidden');
    }

    async function submitRejection() {
        if (!businessToReject) return;
        const notes = document.getElementById('reject_admin_notes').value;
        try {
            const res = await (await fetch(`${window.AppConfig.adminUrl}/businesses/${businessToReject}/status`, {
                method: 'PATCH',
                headers: { 
                    'X-CSRF-TOKEN': window.AppConfig.csrfToken || '{{ csrf_token() }}', 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: 'rejected', admin_notes: notes })
            })).json();

            if (res.success) {
                showToast('success', res.message);
                window.modals?.rejectBusinessModal?.hide();
                updateList();
            }
        } catch (e) {
            showToast('error', 'Network error while rejecting');
        } finally {
            businessToReject = null;
        }
    }
    function bindPagination() {
        const links = document.querySelectorAll('#business-list-container .pagination a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchPage(this.href);
            });
        });
    }
    async function fetchPage(urlStr) {
        const container = document.getElementById('business-list-container');
        const overlay = document.getElementById('list-loading-overlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('opacity-100');
        }
        try {
            const response = await fetch(urlStr, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (response.ok) {
                const html = await response.text();
                container.innerHTML = html;
                window.history.pushState({}, '', urlStr);
                window.scrollTo({ top: 0, behavior: 'smooth' });
                if (typeof gsap !== 'undefined') {
                    const targets = container.querySelectorAll('tr');
                    gsap.from(targets, { opacity: 0, y: 10, stagger: 0.03, duration: 0.4 });
                }
            }
        } catch (error) {
            console.error('Pagination failed:', error);
        } finally {
            if (overlay) {
                overlay.classList.remove('opacity-100');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
            bindPagination();
        }
    }
    // Initialize list reveal & Modals
    document.addEventListener('DOMContentLoaded', async () => { 
        updateList(); 
        initModals();
        
        // Search suggestions listeners
        const searchInput = document.getElementById('business-search');
        const suggestionsDropdown = document.getElementById('search-suggestions-dropdown');
        if (searchInput && suggestionsDropdown) {
            document.addEventListener('click', (e) => {
                if (!suggestionsDropdown.contains(e.target) && e.target !== searchInput) {
                    suggestionsDropdown.classList.add('hidden');
                }
            });
            searchInput.addEventListener('focus', () => {
                const query = searchInput.value.trim();
                if (query.length > 0) {
                    suggestionsDropdown.classList.remove('hidden');
                }
            });
        }

        // Zero-Loading: Pre-load the creation form in the background
        try {
            const response = await fetch('{{ route("admin.businesses.create") }}', { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' } 
            });
            if (response.ok) {
                const html = await response.text();
                formCache.set('create_form', html);
                console.log('Creation form pre-loaded for zero-latency.');
            }
        } catch (e) { console.warn('Pre-loading failed, will load on demand.'); }
        // Final Header entrance
        if (typeof gsap !== 'undefined') {
            gsap.from('.dashboard-header-reveal', {
                y: -20, opacity: 0, duration: 0.6, ease: "power3.out"
            });
            gsap.from('.list-card', {
                y: 30, opacity: 0, duration: 0.7, ease: "power3.out", delay: 0.1
            });
        }
    });
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initModals();
    }
</script>
@endpush
@endsection
