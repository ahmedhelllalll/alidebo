@extends('admin.layouts.admin')
@section('title', __('admin.leads') ?? 'Leads')
@section('content')
<div id="leads-main-wrapper" class="relative min-h-[600px]">
    <div id="leads-list-section" class="space-y-6 transition-all duration-500">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal" data-total="{{ $leads->total() }}">
            <div>
                <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.leads') ?? 'Leads' }}</h1>
                <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.all_leads') ?? 'All Contact Messages' }} (<span id="total-count-header">{{ $leads->total() }}</span>)</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="loadLeadsList()" class="p-2.5 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-white/10 text-slate-500 hover:text-primary rounded-xl shadow-sm hover:shadow-md transition-all active:scale-[0.98] group" title="{{ __('admin.refresh') }}">
                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>
        </div>
        
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white/50 dark:bg-zinc-900/30 backdrop-blur-xl p-4 rounded-2xl border border-white/60 dark:border-white/[0.05] shadow-sm relative z-20">
            <div class="flex items-center gap-4 w-full md:w-auto relative" id="filter-dropdown-container">
                <div class="flex items-center gap-2 hidden sm:flex">
                    <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                    <span class="text-[12px] font-[900] text-slate-400 uppercase tracking-widest">{{ __('admin.management') ?? 'Management' }}</span>
                </div>
                
                <div class="relative w-full sm:w-auto">
                    <button type="button" onclick="toggleFilterMenu(event)" class="w-full sm:w-auto flex items-center justify-between gap-3 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:border-primary/50 rounded-xl text-[13px] font-bold py-2 px-4 transition-all text-slate-700 dark:text-zinc-300 shadow-sm focus:ring-4 focus:ring-primary/10 outline-none">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-filter text-slate-400"></i>
                            <span id="current-filter-text">{{ __('admin.all') }}</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div id="filter-menu" class="absolute z-[120] mt-2 w-48 end-0 bg-white/95 dark:bg-[#1a1a1e]/95 backdrop-blur-xl rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] border border-slate-200/80 dark:border-white/10 opacity-0 pointer-events-none overflow-hidden transition-all duration-200 p-1 hidden origin-top-right rtl:origin-top-left">
                        <button onclick="applyFilter('', '{{ __('admin.all') }}')" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-start text-[12px] font-[900] hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors text-slate-700 dark:text-zinc-300 mb-0.5">
                           {{ __('admin.all') }}
                        </button>
                        <button onclick="applyFilter('read', '{{ __('admin.read') }}')" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-start text-[12px] font-[900] hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors text-slate-700 dark:text-zinc-300 mb-0.5">
                           <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>{{ __('admin.read') }}
                        </button>
                        <button onclick="applyFilter('unread', '{{ __('admin.unread') }}')" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-start text-[12px] font-[900] hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors text-slate-700 dark:text-zinc-300">
                           <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>{{ __('admin.unread') }}
                        </button>
                    </div>
                </div>
                <input type="hidden" id="leads-status-filter" value="">
            </div>
            
            <div class="relative w-full md:w-72 group">
                <div class="absolute inset-y-0 start-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" id="leads-search" placeholder="{{ __('admin.search') }}" 
                    class="w-full bg-slate-100/50 dark:bg-zinc-800/50 border border-transparent focus:border-primary/30 focus:bg-white dark:focus:bg-[#09090b] focus:ring-4 focus:ring-primary/10 rounded-xl text-[13px] font-bold py-2.5 ps-11 pe-10 transition-all text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 shadow-sm">
                
                <div id="search-spinner" class="absolute inset-y-0 end-4 flex items-center hidden">
                    <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative z-10 w-full min-h-[400px] reveal-item">
            <div id="list-loading-overlay" class="absolute inset-0 z-50 bg-white/60 dark:bg-[#121214]/60 backdrop-blur-[4px] flex flex-col items-center justify-center opacity-0 transition-opacity duration-300 pointer-events-none hidden rounded-[24px]">
                <div class="relative flex items-center justify-center">
                    <div class="w-14 h-14 rounded-full border-4 border-slate-200/50 dark:border-zinc-700/50"></div>
                    <div class="w-14 h-14 rounded-full border-4 border-primary border-t-transparent animate-spin absolute inset-0"></div>
                </div>
                <p class="mt-4 text-[11px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-[0.2em] animate-pulse">{{ __('admin.loading_data') }}</p>
            </div>
            <div id="leads-list-container" class="relative z-10 transition-all duration-300">
                @include('admin.leads._list', ['leads' => $leads])
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
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-3 tracking-tight">{{ __('admin.delete_lead_confirm_title') }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400">{{ __('admin.delete_lead_confirm_desc') }}</p>
    </div>
    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full pb-2">
            <button type="button" onclick="closeModal('deleteConfirmModal')" class="w-full sm:flex-1 px-5 py-3 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[14px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">
                {{ __('admin.cancel') }}
            </button>
            <button type="button" id="confirmDeleteBtn" class="w-full sm:flex-1 px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(239,68,68,0.25)] hover:shadow-[0_12px_25px_rgba(239,68,68,0.35)] transition-all active:scale-[0.98]">
                {{ __('admin.delete') }}
            </button>
        </div>
    </x-slot>
</x-admin.modal>

@push('scripts')
<script>
    let searchTimeout = null;
    let itemToDeleteUrl = null;

    document.getElementById('leads-search')?.addEventListener('input', function(e) {
        document.getElementById('search-spinner')?.classList.remove('hidden');
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadLeadsList(this.value);
        }, 400);
    });
    
    let activeFilterMenu = false;
    window.toggleFilterMenu = function(event) {
        event.stopPropagation();
        const menu = document.getElementById('filter-menu');
        if (!menu) return;
        
        if (activeFilterMenu) {
            closeFilterMenu(menu);
        } else {
            menu.classList.remove('hidden');
            setTimeout(() => {
                menu.classList.remove('opacity-0', 'pointer-events-none');
                activeFilterMenu = true;
                if (window.gsap) {
                    gsap.fromTo(menu, { opacity: 0, y: -10, scale: 0.95 }, { 
                        opacity: 1, y: 0, scale: 1, duration: 0.3, ease: 'back.out(1.7)' 
                    });
                }
            }, 10);
        }
    };
    
    function closeFilterMenu(menu) {
        if (!menu) menu = document.getElementById('filter-menu');
        if (!menu || !activeFilterMenu) return;
        
        if (window.gsap) {
            gsap.to(menu, { opacity: 0, y: -10, scale: 0.95, duration: 0.2, onComplete: () => {
                menu.classList.add('opacity-0', 'pointer-events-none', 'hidden');
                activeFilterMenu = false;
            }});
        } else {
            menu.classList.add('opacity-0', 'pointer-events-none', 'hidden');
            activeFilterMenu = false;
        }
    }
    
    window.applyFilter = function(val, text) {
        document.getElementById('current-filter-text').innerText = text;
        document.getElementById('leads-status-filter').value = val;
        closeFilterMenu();
        document.getElementById('search-spinner')?.classList.remove('hidden');
        loadLeadsList();
    };

    async function loadLeadsList(searchQuery = null, url = null) {
        const overlay = document.getElementById('list-loading-overlay');
        const container = document.getElementById('leads-list-container');
        if (!container) return;
        
        if (overlay) {
            overlay.classList.remove('hidden');
            gsap.to(overlay, { opacity: 1, duration: 0.3 });
        }
        
        let fetchUrl = url || '{{ route("admin.leads.index") }}';
        const urlObj = new URL(fetchUrl);
        
        const searchInput = document.getElementById('leads-search');
        if (searchInput && searchInput.value) {
            urlObj.searchParams.set('search', searchInput.value);
        } else if (searchQuery !== null) {
            urlObj.searchParams.set('search', searchQuery);
        }
        
        const statusFilter = document.getElementById('leads-status-filter');
        if (statusFilter && statusFilter.value) {
            urlObj.searchParams.set('status', statusFilter.value);
        }
        
        fetchUrl = urlObj.toString();
        
        try {
            const response = await fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });
            if (response.ok) {
                const html = await response.text();
                container.innerHTML = html;
                window.history.pushState({}, '', fetchUrl);
                
                if (window.gsap) {
                    gsap.fromTo(container.querySelectorAll('tr, .mobile-card'), 
                        { opacity: 0, y: 15 }, 
                        { opacity: 1, y: 0, duration: 0.4, stagger: 0.05, ease: 'power2.out', clearProps: 'all' }
                    );
                }
            }
        } catch (error) {
            console.error('Error loading leads:', error);
            if (window.showToast) showToast('error', '{{ __('admin.error') }}', 'Failed to load leads');
        } finally {
            document.getElementById('search-spinner')?.classList.add('hidden');
            if (overlay) {
                if (window.gsap) {
                    gsap.to(overlay, { opacity: 0, duration: 0.3, onComplete: () => overlay.classList.add('hidden') });
                } else {
                    overlay.classList.add('hidden');
                }
            }
        }
    }

    window.confirmDelete = function(url) {
        itemToDeleteUrl = url;
        const modal = document.getElementById('deleteConfirmModal');
        if (modal) {
            const btn = document.getElementById('confirmDeleteBtn');
            btn.onclick = executeDelete;
            if (window.modals && window.modals['deleteConfirmModal']) {
                window.modals['deleteConfirmModal'].show();
            } else {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
    };

    async function executeDelete() {
        if (!itemToDeleteUrl) return;
        const btn = document.getElementById('confirmDeleteBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> {{ __("admin.delete") }}...';
        
        try {
            const response = await fetch(itemToDeleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.success) {
                if (window.modals && window.modals['deleteConfirmModal']) window.modals['deleteConfirmModal'].hide();
                else document.getElementById('deleteConfirmModal').classList.add('hidden');
                
                if(window.showToast) showToast('success', '{{ __('admin.success') }}', data.message);
                
                const id = itemToDeleteUrl.split('/').pop();
                const rows = document.querySelectorAll(`tr[data-id="${id}"], .mobile-card[data-id="${id}"]`);
                if (window.gsap) {
                    gsap.to(rows, { opacity: 0, x: 20, duration: 0.4, stagger: 0.1, onComplete: () => loadLeadsList() });
                } else {
                    loadLeadsList();
                }
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            if(window.showToast) showToast('error', '{{ __('admin.error') }}', error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
            itemToDeleteUrl = null;
        }
    }

    let activeStatusMenu = null;
    window.toggleStatusMenu = function(event, menuId) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        if (!menu) return;
        
        const isOpen = !menu.classList.contains('opacity-0');
        if (isOpen) {
            closeStatusMenu(menu);
        } else {
            if (activeStatusMenu) closeStatusMenu(activeStatusMenu);
            menu.classList.remove('opacity-0', 'pointer-events-none');
            activeStatusMenu = menu;
            const target = menu.closest('.group');
            if(target) {
                target.style.zIndex = '200';
            }
            if (window.gsap) {
                gsap.fromTo(menu, { opacity: 0, y: -10, scale: 0.95 }, { 
                    opacity: 1, y: 0, scale: 1, duration: 0.3, ease: 'back.out(1.7)' 
                });
            }
        }
    }
    
    function closeStatusMenu(menu) {
        if (!menu || menu.classList.contains('opacity-0')) return;
        if (window.gsap) {
            gsap.to(menu, { opacity: 0, y: -10, scale: 0.95, duration: 0.2, onComplete: () => {
                menu.classList.add('opacity-0', 'pointer-events-none');
                const target = menu.closest('.group');
                if(target) target.style.zIndex = '';
            }});
        } else {
            menu.classList.add('opacity-0', 'pointer-events-none');
            const target = menu.closest('.group');
            if(target) target.style.zIndex = '';
        }
        if (activeStatusMenu === menu) activeStatusMenu = null;
    }
    
    document.addEventListener('click', (e) => {
        if (activeStatusMenu && !activeStatusMenu.contains(e.target)) {
            closeStatusMenu(activeStatusMenu);
        }
        const filterMenu = document.getElementById('filter-menu');
        if (activeFilterMenu && filterMenu && !filterMenu.contains(e.target)) {
            closeFilterMenu(filterMenu);
        }
    });

    window.updateLeadStatus = async function(id, newStatus, buttonEl) {
        try {
            const btnGroup = document.querySelectorAll(`.status-btn-\\[${id}\\]`);
            const isRead = newStatus === 'read';
            
            // Optimistic UI update
            btnGroup.forEach(btn => {
                const label = btn.querySelector('span');
                const dot = btn.querySelector('div.rounded-full');
                if (label && dot) {
                    label.textContent = isRead ? '{{ __('admin.read') }}' : '{{ __('admin.unread') }}';
                    label.className = isRead ? 'text-emerald-700 dark:text-emerald-400' : 'text-orange-700 dark:text-orange-400';
                    dot.className = isRead 
                        ? 'w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' 
                        : 'w-1.5 h-1.5 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.5)] animate-pulse';
                }
            });
            closeStatusMenu(buttonEl.closest('.status-menu'));
            
            const response = await fetch(`{{ route('admin.dashboard') }}/../leads/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            });
            const data = await response.json();
            if (data.success) {
                if(window.showToast) showToast('success', '{{ __('admin.success') }}', data.message);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            if(window.showToast) showToast('error', '{{ __('admin.error') }}', error.message);
            loadLeadsList(); // Reload to reset optimistic UI
        }
    };

    window.closeModal = (id) => {
        if (window.modals && window.modals[id]) window.modals[id].hide();
        else document.getElementById(id).classList.add('hidden');
    };
</script>
@endpush
@endsection
