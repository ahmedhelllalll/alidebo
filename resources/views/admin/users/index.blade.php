@extends('admin.layouts.admin')
@section('title', __('admin.users'))
@section('content')
<div id="users-main-wrapper" class="relative min-h-[600px]">
    {{-- Section 1: Listing --}}
    <div id="users-list-section" class="space-y-6 transition-all duration-500">
        {{-- Header & Stats --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal" data-total="{{ $users->total() }}">
            <div>
                <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.users') }}</h1>
                <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.all_users') }} (<span id="total-count-header">{{ $users->total() }}</span>)</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="loadUsersList()" class="p-2.5 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-white/10 text-slate-500 hover:text-primary rounded-xl shadow-sm hover:shadow-md transition-all active:scale-[0.98] group" title="{{ __('admin.refresh') }}">
                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>
        </div>
        {{-- Filter & Search Bar --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white/50 dark:bg-zinc-900/30 backdrop-blur-xl p-4 rounded-2xl border border-white/60 dark:border-white/[0.05] shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                <span class="text-[12px] font-[900] text-slate-400 uppercase tracking-widest">{{ __('admin.management') ?? __('admin.platform_access') }}</span>
            </div>
            {{-- Smart Search --}}
            <div class="relative w-full md:w-72 group">
                <div class="absolute inset-y-0 start-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" id="users-search" placeholder="{{ __('admin.search') }}" 
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
        {{-- Table Card --}}
        <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative z-10 w-full min-h-[400px] reveal-item">
            {{-- Smooth Overlay Loader --}}
            <div id="list-loading-overlay" class="absolute inset-0 z-50 bg-white/60 dark:bg-[#121214]/60 backdrop-blur-[4px] flex flex-col items-center justify-center opacity-0 transition-opacity duration-300 pointer-events-none hidden rounded-[24px]">
                <div class="relative flex items-center justify-center">
                    <div class="w-14 h-14 rounded-full border-4 border-slate-200/50 dark:border-zinc-700/50"></div>
                    <div class="w-14 h-14 rounded-full border-4 border-primary border-t-transparent animate-spin absolute inset-0"></div>
                </div>
                <p class="mt-4 text-[11px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-[0.2em] animate-pulse">{{ __('admin.loading_data') }}</p>
            </div>
            <div id="users-list-container" class="relative z-10 transition-all duration-300">
                @include('admin.users._users_list', ['users' => $users])
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
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-3 tracking-tight">{{ __('admin.delete_user_confirm_title') }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400">{{ __('admin.delete_user_confirm_desc') }}</p>
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
    let userToDeleteUrl = null;
    // Search trigger
    document.getElementById('users-search')?.addEventListener('input', function(e) {
        document.getElementById('search-spinner')?.classList.remove('hidden');
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadUsersList(this.value);
        }, 400);
    });
    // Main AJAX loader
    async function loadUsersList(searchQuery = null, url = null) {
        const overlay = document.getElementById('list-loading-overlay');
        const container = document.getElementById('users-list-container');
        if (!container) return;
        if (overlay) {
            overlay.classList.remove('hidden');
            gsap.to(overlay, { opacity: 1, duration: 0.3 });
        }
        let fetchUrl = url || '{{ route("admin.users.index") }}';
        if (searchQuery !== null) {
            const urlObj = new URL(fetchUrl);
            urlObj.searchParams.set('search', searchQuery);
            fetchUrl = urlObj.toString();
        }
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
                // Re-initialize GSAP animations
                gsap.fromTo(container.querySelectorAll('tr, .mobile-card'), 
                    { opacity: 0, y: 15 }, 
                    { opacity: 1, y: 0, duration: 0.4, stagger: 0.05, ease: 'power2.out', clearProps: 'all' }
                );
            }
        } catch (error) {
            console.error('Error loading users:', error);
            if (window.showToast) showToast('error', '{{ __('admin.error') }}', '{{ __('admin.failed_to_load_users') }}');
        } finally {
            document.getElementById('search-spinner')?.classList.add('hidden');
            if (overlay) {
                gsap.to(overlay, { opacity: 0, duration: 0.3, onComplete: () => overlay.classList.add('hidden') });
            }
        }
    }
    // Role Dropdown & Update
    let activeRoleMenu = null;
    window.toggleRoleMenu = function(event, menuId) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        if (!menu) return;
        const isOpen = !menu.classList.contains('opacity-0');
        if (isOpen) {
            closeRoleMenu(menu);
        } else {
            // Close existing
            if (activeRoleMenu) closeRoleMenu(activeRoleMenu);
            // Open new
            menu.classList.remove('opacity-0', 'pointer-events-none');
            activeRoleMenu = menu;
            const target = menu.closest('td') || menu.closest('.mobile-card');
            const tr = menu.closest('tr');
            if(target) {
                target.style.zIndex = '200';
                target.style.position = 'relative';
            }
            if(tr) {
                tr.style.zIndex = '200';
                tr.style.position = 'relative';
            }
            gsap.fromTo(menu, { opacity: 0, y: -10, scale: 0.95 }, { 
                opacity: 1, y: 0, scale: 1, duration: 0.3, ease: 'back.out(1.7)' 
            });
        }
    }
    function closeRoleMenu(menu) {
        if (!menu || menu.classList.contains('opacity-0')) return;
        gsap.to(menu, { opacity: 0, y: -10, scale: 0.95, duration: 0.2, onComplete: () => {
            menu.classList.add('opacity-0', 'pointer-events-none');
            const target = menu.closest('td') || menu.closest('.mobile-card');
            const tr = menu.closest('tr');
            if(target) {
                target.style.zIndex = '';
                target.style.position = '';
            }
            if(tr) {
                tr.style.zIndex = '';
                tr.style.position = '';
            }
        }});
        if (activeRoleMenu === menu) activeRoleMenu = null;
    }
    document.addEventListener('click', (e) => {
        if (activeRoleMenu && !activeRoleMenu.contains(e.target)) {
            closeRoleMenu(activeRoleMenu);
        }
    });
    window.updateUserRole = async function(userId, newRole, buttonEl) {
        try {
            const btnGroup = document.querySelectorAll(`.role-btn-\\[${userId}\\]`);
            const isAdmin = newRole === 'admin';
            // Optimistic UI
            btnGroup.forEach(btn => {
                const label = btn.querySelector('span');
                const dot = btn.querySelector('div');
                if (label && dot) {
                    label.textContent = isAdmin ? '{{ __('admin.admin_role') }}' : '{{ __('admin.user_role') }}';
                    label.className = isAdmin ? 'text-purple-700 dark:text-purple-400' : 'text-slate-700 dark:text-slate-400';
                    dot.className = isAdmin ? 'w-1.5 h-1.5 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]' : 'w-1.5 h-1.5 rounded-full bg-slate-500 shadow-[0_0_8px_rgba(100,116,139,0.5)]';
                }
            });
            closeRoleMenu(buttonEl.closest('.role-menu'));
            const response = await fetch(`${window.AppConfig.adminUrl}/users/${userId}/role`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ role: newRole })
            });
            const data = await response.json();
            if (data.success) {
                if(window.showToast) showToast('success', '{{ __('admin.success') }}', data.message);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            if(window.showToast) showToast('error', '{{ __('admin.error') }}', error.message);
            loadUsersList();
        }
    };
    // Delete Logic
    window.confirmDelete = function(url) {
        userToDeleteUrl = url;
        const modal = document.getElementById('deleteConfirmModal');
        if (modal) {
            const btn = document.getElementById('confirmDeleteBtn');
            btn.onclick = executeDeleteUser;
            // Premium Modal Show logic if available
            if (window.modals && window.modals['deleteConfirmModal']) {
                window.modals['deleteConfirmModal'].show();
            } else {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
    };
    async function executeDeleteUser() {
        if (!userToDeleteUrl) return;
        const btn = document.getElementById('confirmDeleteBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> {{ __("admin.delete") }}...';
        try {
            const response = await fetch(userToDeleteUrl, {
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
                // Animate removal
                const id = userToDeleteUrl.split('/').pop();
                const rows = document.querySelectorAll(`tr[data-id="${id}"], .mobile-card[data-id="${id}"]`);
                gsap.to(rows, { opacity: 0, x: 20, duration: 0.4, stagger: 0.1, onComplete: () => loadUsersList() });
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            if(window.showToast) showToast('error', '{{ __('admin.error') }}', error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
            userToDeleteUrl = null;
        }
    }
    // Modal Global for inline close
    window.closeModal = (id) => {
        if (window.modals && window.modals[id]) window.modals[id].hide();
        else document.getElementById(id).classList.add('hidden');
    };
</script>
@endpush
@endsection
