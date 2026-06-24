@extends('admin.layouts.admin')

@section('title', __('admin.backups') ?? 'System Backups')

@section('content')
<div class="space-y-6">
    {{-- Header & Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">
                {{ __('admin.backups') ?? 'System Backups' }}
            </h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">
                {{ __('admin.manage_secure_backups') ?? 'Securely archive and restore your platform data' }}
            </p>
        </div>
        
        <div class="relative dropdown-wrapper" id="backup-generate-wrapper">
            <button id="create-backup-btn" onclick="toggleDropdown('backup-generate-dropdown')" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98] {{ $isGenerating ? 'opacity-50 pointer-events-none' : '' }}" {{ $isGenerating ? 'disabled' : '' }}>
                <i class="fa-solid {{ $isGenerating ? 'fa-spinner fa-spin' : 'fa-cloud-arrow-up' }}"></i>
                {{ $isGenerating ? (__('admin.backup_in_progress_short') ?? 'Generating...') : (__('admin.create_backup') ?? 'Create Backup') }}
                @if(!$isGenerating)<i class="fa-solid fa-chevron-down ms-1 text-[10px]"></i>@endif
            </button>

            <div id="backup-generate-dropdown" class="absolute hidden top-full mt-2 end-0 w-48 bg-white/95 dark:bg-[#121214]/95 backdrop-blur-md border border-slate-200/60 dark:border-white/[0.08] shadow-[0_20px_60px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.5)] rounded-3xl overflow-hidden z-50 transform origin-top-right">
                <button onclick="startBackup('all'); toggleDropdown('backup-generate-dropdown')" class="w-full text-start flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-700 dark:text-zinc-300 hover:bg-primary/5 hover:text-primary transition-colors border-b border-black/5 dark:border-white/5">
                    <i class="fa-solid fa-database w-4 text-center"></i>
                    <span>{{ __('admin.backup_all') ?? 'All (DB + Code)' }}</span>
                </button>
                <button onclick="startBackup('db'); toggleDropdown('backup-generate-dropdown')" class="w-full text-start flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-700 dark:text-zinc-300 hover:bg-primary/5 hover:text-primary transition-colors border-b border-black/5 dark:border-white/5">
                    <i class="fa-solid fa-server w-4 text-center"></i>
                    <span>{{ __('admin.backup_db') ?? 'Database Only' }}</span>
                </button>
                <button onclick="startBackup('files'); toggleDropdown('backup-generate-dropdown')" class="w-full text-start flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-700 dark:text-zinc-300 hover:bg-primary/5 hover:text-primary transition-colors">
                    <i class="fa-regular fa-folder w-4 text-center"></i>
                    <span>{{ __('admin.backup_files') ?? 'Files Only' }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Generating Alert --}}
    @if($isGenerating)
    <div id="backup-pooling" class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-500">
                <i class="fa-solid fa-spinner fa-spin text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-[900] text-emerald-600 dark:text-emerald-400">{{ __('admin.backup_in_progress') ?? 'A Backup is Currently Generating...' }}</h3>
                <p class="text-[12px] font-bold text-slate-500 dark:text-zinc-400">{{ __('admin.backup_background_notice') ?? 'This operates in the background. You can safely navigate away.' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.location.reload()" class="px-4 py-2 bg-white dark:bg-zinc-800 rounded-lg text-xs font-bold text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-700 shadow-sm transition-colors border border-black/5 dark:border-white/5">
                <i class="fa-solid fa-rotate-right me-1"></i> {{ __('admin.refresh_list') ?? 'Refresh List' }}
            </button>
            <button onclick="dismissProgressAlert(this)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 hover:text-slate-700 dark:hover:text-zinc-200 shadow-sm transition-colors border border-black/5 dark:border-white/5" title="{{ __('admin.dismiss') ?? 'Dismiss' }}">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- Storage Location Settings --}}
    <div class="glass-card rounded-3xl overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)]">
        <div class="p-6 border-b border-slate-100 dark:border-white/5">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h2 class="text-[15px] font-[900] text-slate-800 dark:text-white">{{ __('admin.storage_location') ?? 'Storage Location' }}</h2>
            </div>
            <p class="text-[12px] font-medium text-slate-400 dark:text-zinc-500 ms-12">{{ __('admin.storage_location_desc') ?? 'Set the server directory where backups will be saved' }}</p>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-4 text-slate-400 dark:text-zinc-500 pointer-events-none">
                        <i class="fa-solid fa-hard-drive text-sm"></i>
                    </div>
                    <input type="text" id="backup-path-input"
                        value="{{ $backupPath }}"
                        placeholder="{{ __('admin.storage_path_placeholder') ?? 'e.g. C:\\backups or /var/backups' }}"
                        class="w-full ps-11 pe-4 py-3 bg-slate-50 dark:bg-zinc-900/60 border border-slate-200/70 dark:border-white/[0.08] rounded-xl text-[13px] font-bold text-slate-700 dark:text-zinc-300 placeholder:text-slate-400 dark:placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" dir="ltr" />
                </div>
                <button onclick="saveBackupPath()" id="save-path-btn"
                    class="flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 dark:bg-white hover:bg-slate-800 dark:hover:bg-zinc-200 text-white dark:text-slate-900 rounded-xl font-[900] text-[13px] transition-all shadow-md hover:shadow-lg active:scale-[0.97] whitespace-nowrap">
                    <i class="fa-solid fa-floppy-disk" id="save-path-icon"></i>
                    <span id="save-path-text">{{ __('admin.save_path') ?? 'Save Path' }}</span>
                </button>
            </div>
            {{-- Success feedback --}}
            <div id="path-save-feedback" class="hidden mt-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-500 shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="text-[13px] font-bold text-emerald-600 dark:text-emerald-400" id="path-save-message"></p>
            </div>
            {{-- Error feedback --}}
            <div id="path-error-feedback" class="hidden mt-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500 shrink-0">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <p class="text-[13px] font-bold text-red-600 dark:text-red-400" id="path-error-message"></p>
            </div>
        </div>
    </div>

    {{-- Backups List --}}
    <div class="glass-card rounded-3xl overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)]">
        @if(count($files) > 0)
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-start border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/50 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]">
                        <th class="py-4 px-6 text-start text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.file_name') ?? 'Archive Name' }}</th>
                        <th class="py-4 px-6 text-start text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.size') ?? 'Size' }}</th>
                        <th class="py-4 px-6 text-start text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.date') ?? 'Date Created' }}</th>
                        <th class="py-4 px-6 text-end text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach($files as $key => $file)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-file-zipper"></i>
                                </div>
                                <span class="text-[13px] font-bold text-slate-700 dark:text-zinc-300">{{ $file['name'] }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-[13px] font-bold text-slate-500 dark:text-zinc-400">
                            {{ $file['size'] }}
                        </td>
                        <td class="py-4 px-6 text-[13px] font-bold text-slate-500 dark:text-zinc-400">
                            {{ $file['date'] }}
                        </td>
                        <td class="py-4 px-6 text-end">
                            <div class="flex items-center justify-end gap-2 text-xl">
                                <a href="{{ route('admin.backups.download', ['path' => $file['path']]) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-zinc-800/80 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-download text-sm"></i>
                                </a>
                                <button onclick="deleteBackup('{{ $file['path'] }}', this)" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-zinc-800/80 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center flex flex-col items-center justify-center min-h-[400px]">
             <div class="w-20 h-20 rounded-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center text-slate-300 dark:text-zinc-600 mb-6 ring-4 ring-slate-50 dark:ring-zinc-800 border-[8px] border-white dark:border-[#09090b] shadow-xl">
                 <i class="fa-solid fa-server text-3xl"></i>
             </div>
             <h3 class="text-xl font-[900] text-slate-900 dark:text-white mb-2">{{ __('admin.no_backups') ?? 'No Backups Generated' }}</h3>
             <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400 mb-6 max-w-md">{{ __('admin.no_backups_desc') ?? 'Keep your system safe by generating automated or manual platform snapshots. Click create to start archiving.' }}</p>
             <button onclick="startBackup('all')" class="flex items-center justify-center gap-2 px-6 py-2.5 bg-slate-900 dark:bg-white hover:bg-slate-800 dark:hover:bg-zinc-200 text-white dark:text-slate-900 rounded-xl font-[900] text-[14px] transition-all shadow-md hover:scale-105 active:scale-95">
                 <i class="fa-solid fa-plus"></i> {{ __('admin.generate_snapshot') ?? 'Generate First Snapshot' }}
             </button>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    let isBackupRunning = {{ $isGenerating ? 'true' : 'false' }};

    function dismissProgressAlert(btn) {
        const alert = btn.closest('#backup-pooling');
        if (alert && typeof gsap !== 'undefined') {
            gsap.to(alert, { opacity: 0, height: 0, marginTop: 0, marginBottom: 0, padding: 0, duration: 0.35, ease: 'power2.in', onComplete: () => alert.remove() });
        } else if (alert) {
            alert.remove();
        }
    }

    async function startBackup(type) {
        // Prevent duplicate processes
        if (isBackupRunning) {
            if (typeof showToast === 'function') {
                showToast('warning', '{{ __("admin.backup_already_running") ?? "Already Running" }}', '{{ __("admin.backup_already_running_desc") ?? "A backup is already in progress. Please wait for it to finish." }}');
            } else {
                alert('{{ __("admin.backup_already_running_desc") ?? "A backup is already in progress. Please wait for it to finish." }}');
            }
            return;
        }

        isBackupRunning = true;

        // Disable the button visually
        const btn = document.getElementById('create-backup-btn');
        if (btn) {
            btn.classList.add('opacity-50', 'pointer-events-none');
            btn.disabled = true;
        }

        const btnIcon = document.querySelector('.fa-cloud-arrow-up');
        if (btnIcon) {
            btnIcon.className = 'fa-solid fa-spinner fa-spin';
        }

        try {
            const res = await fetch(`{{ route('admin.backups.create') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: type })
            });

            if (res.ok) {
                if (typeof showToast === 'function') {
                    showToast('success', '{{ __('admin.backup_queued') ?? 'Backup Queued' }}', '{{ __('admin.backup_queued_desc') ?? 'Your backup is compressing beautifully in the background.' }}');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    window.location.reload();
                }
            } else {
                isBackupRunning = false;
                if (btn) { btn.classList.remove('opacity-50', 'pointer-events-none'); btn.disabled = false; }
                alert('{{ __('admin.backup_failed') ?? 'Triggering backup failed: Backend error' }}');
                if (btnIcon) btnIcon.className = 'fa-solid fa-cloud-arrow-up';
            }
        } catch (e) {
            isBackupRunning = false;
            if (btn) { btn.classList.remove('opacity-50', 'pointer-events-none'); btn.disabled = false; }
            console.error(e);
        }
    }

    async function deleteBackup(path, btn) {
        if (!confirm('{{ __('admin.confirm_delete_snapshot') ?? 'Are you absolutely certain you want to permanently delete this snapshot?' }}')) return;
        
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i>';
        
        try {
            const res = await fetch(`{{ route('admin.backups.destroy') }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ path: path })
            });

            if (res.ok) {
                const tr = btn.closest('tr');
                gsap.to(tr, { opacity: 0, scale: 0.95, duration: 0.3, onComplete: () => tr.remove() });
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function saveBackupPath() {
        const input = document.getElementById('backup-path-input');
        const icon = document.getElementById('save-path-icon');
        const text = document.getElementById('save-path-text');
        const successBox = document.getElementById('path-save-feedback');
        const errorBox = document.getElementById('path-error-feedback');
        const successMsg = document.getElementById('path-save-message');
        const errorMsg = document.getElementById('path-error-message');

        const path = input.value.trim();
        if (!path) { input.focus(); return; }

        // Loading state
        icon.className = 'fa-solid fa-spinner fa-spin';
        text.textContent = '{{ __("admin.saving") ?? "Saving..." }}';

        // Hide previous feedback
        successBox.classList.add('hidden');
        errorBox.classList.add('hidden');

        try {
            const res = await fetch('{{ route("admin.backups.settings") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ backup_path: path })
            });

            const data = await res.json();

            if (res.ok) {
                // Show success feedback with animation
                successMsg.textContent = data.message;
                successBox.classList.remove('hidden');
                if (typeof gsap !== 'undefined') {
                    gsap.fromTo(successBox, { opacity: 0, y: -10 }, { opacity: 1, y: 0, duration: 0.4, ease: 'back.out(1.4)' });
                }
                if (typeof showToast === 'function') {
                    showToast('success', '{{ __("admin.success") }}', data.message);
                }
            } else {
                // Show error feedback
                errorMsg.textContent = data.message || '{{ __("admin.error") }}';
                errorBox.classList.remove('hidden');
                if (typeof gsap !== 'undefined') {
                    gsap.fromTo(errorBox, { opacity: 0, y: -10 }, { opacity: 1, y: 0, duration: 0.4, ease: 'back.out(1.4)' });
                }
            }
        } catch (e) {
            errorMsg.textContent = '{{ __("admin.network_error") }}';
            errorBox.classList.remove('hidden');
        }

        // Reset button
        icon.className = 'fa-solid fa-floppy-disk';
        text.textContent = '{{ __("admin.save_path") ?? "Save Path" }}';
    }
</script>
@endpush
@endsection
