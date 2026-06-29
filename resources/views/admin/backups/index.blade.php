@extends('admin.layouts.admin')

@section('title', __('admin.backups') ?? 'System Backups')

@section('content')
<div id="backups-main-wrapper" class="relative min-h-[600px]">
    <div class="space-y-6 transition-all duration-500">

        {{-- Auto-Backup Info Card --}}
        <div class="flex items-start sm:items-center gap-4 p-5 bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/5 rounded-2xl shadow-sm">
            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <h2 class="text-[14px] font-[900] text-slate-800 dark:text-white">{{ __('admin.auto_backup_active') ?? 'Automated Backups Active' }}</h2>
                <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400 mt-0.5">{{ __('admin.auto_backup_desc') ?? 'Rest easy! Your system automatically generates and securely stores a fresh, encrypted snapshot of all your database records every 12 hours.' }}</p>
            </div>
        </div>

        {{-- Header & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
            <div>
                <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">
                    {{ __('admin.backups') ?? 'System Backups' }}
                </h1>
                <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">
                    {{ __('admin.manage_secure_backups') ?? 'Securely archive and restore your platform data' }} (<span id="total-count-header">{{ count($files) }}</span>)
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.location.reload()" class="p-2.5 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-white/10 text-slate-500 hover:text-primary rounded-xl shadow-sm hover:shadow-md transition-all active:scale-[0.98] group" title="{{ __('admin.refresh') ?? 'Refresh' }}">
                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
                <button id="create-backup-btn" onclick="startBackup()" class="flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98] {{ $isGenerating ? 'opacity-50 pointer-events-none' : '' }}" {{ $isGenerating ? 'disabled' : '' }}>
                    <i class="fa-solid {{ $isGenerating ? 'fa-spinner fa-spin' : 'fa-cloud-arrow-up' }} backup-btn-icon"></i>
                    <span class="backup-btn-text">{{ $isGenerating ? (__('admin.backup_in_progress_short') ?? 'Generating...') : (__('admin.create_backup') ?? 'Create Backup') }}</span>
                </button>
            </div>
        </div>

        {{-- Management Bar --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white/50 dark:bg-zinc-900/30 backdrop-blur-xl p-4 rounded-2xl border border-white/60 dark:border-white/[0.05] shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                <span class="text-[12px] font-[900] text-slate-400 uppercase tracking-widest">{{ __('admin.management') ?? 'Management' }}</span>
            </div>
            @if($isGenerating)
            <div class="flex items-center gap-3 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl" id="backup-pooling">
                <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-500">
                    <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                </div>
                <span class="text-[12px] font-[900] text-emerald-600 dark:text-emerald-400">{{ __('admin.backup_in_progress') ?? 'Generating backup...' }}</span>
                <button onclick="dismissProgressAlert(this)" class="w-5 h-5 flex items-center justify-center rounded-md text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200 transition-colors" title="{{ __('admin.dismiss') ?? 'Dismiss' }}">
                    <i class="fa-solid fa-xmark text-[10px]"></i>
                </button>
            </div>
            @endif
        </div>

        {{-- Backups Table Card --}}
        <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative z-10 w-full min-h-[300px] reveal-item">
            @if(count($files) > 0)

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto custom-scrollbar">
                <table class="w-full text-start border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200/50 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]">
                            <th class="py-4 px-6 text-start text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.file_name') ?? 'File Name' }}</th>
                            <th class="py-4 px-6 text-start text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.date') ?? 'Date & Time' }}</th>
                            <th class="py-4 px-6 text-center text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.local_status') ?? 'Local' }}</th>
                            <th class="py-4 px-6 text-center text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.cloud_status') ?? 'Cloud R2' }}</th>
                            <th class="py-4 px-6 text-start text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.size') ?? 'File Size' }}</th>
                            <th class="py-4 px-6 text-center text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.status') ?? 'Status' }}</th>
                            <th class="py-4 px-6 text-end text-[11px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('admin.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($files as $file)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors group backup-row" data-id="{{ $file->id }}">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/20 transition-colors shrink-0">
                                        <i class="fa-solid fa-file-zipper"></i>
                                    </div>
                                    <span class="text-[13px] font-bold text-slate-700 dark:text-zinc-300 truncate max-w-[220px]" title="{{ $file->filename }}">{{ $file->filename }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-[13px] font-bold text-slate-500 dark:text-zinc-400 whitespace-nowrap">
                                {{ $file->backup_date->format('Y-m-d H:i') }}
                            </td>
                            <td class="py-4 px-6 text-center text-[15px]">
                                @if($file->stored_locally)
                                    <span class="text-emerald-500" title="{{ __('admin.saved') ?? 'Saved' }}"><i class="fa-solid fa-circle-check"></i></span>
                                @else
                                    <span class="text-slate-300 dark:text-zinc-700" title="{{ __('admin.not_saved') ?? 'Not Saved' }}"><i class="fa-solid fa-circle-xmark"></i></span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center text-[15px]">
                                @if($file->stored_on_r2)
                                    <span class="text-emerald-500" title="{{ __('admin.uploaded') ?? 'Uploaded' }}"><i class="fa-solid fa-circle-check"></i></span>
                                @else
                                    <span class="text-slate-300 dark:text-zinc-700" title="{{ __('admin.not_uploaded') ?? 'Not Uploaded' }}"><i class="fa-solid fa-circle-xmark"></i></span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-[13px] font-bold text-slate-500 dark:text-zinc-400 whitespace-nowrap">
                                {{ $file->formatted_size }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($file->status === 'success')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-[11px] font-[900] uppercase tracking-wider">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.5)]"></div>
                                        {{ __('admin.success') ?? 'Success' }}
                                    </span>
                                @elseif($file->status === 'in_progress')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg text-[11px] font-[900] uppercase tracking-wider">
                                        <i class="fa-solid fa-spinner fa-spin text-[9px]"></i>
                                        {{ __('admin.in_progress') ?? 'In Progress' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-500/10 text-red-600 dark:text-red-400 rounded-lg text-[11px] font-[900] uppercase tracking-wider cursor-help" title="{{ $file->error_message }}">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_6px_rgba(239,68,68,0.5)]"></div>
                                        {{ __('admin.failed') ?? 'Failed' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-end">
                                <div class="flex items-center justify-end gap-2">
                                    @if($file->status === 'success' && ($file->stored_locally || $file->stored_on_r2))
                                    <a href="{{ route('admin.backups.download', $file->id) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-zinc-800/80 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors" title="{{ __('admin.download') ?? 'Download' }}">
                                        <i class="fa-solid fa-download text-[13px]"></i>
                                    </a>
                                    @endif
                                    <button onclick="confirmDelete('{{ $file->id }}', this)" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-zinc-800/80 text-slate-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="{{ __('admin.delete') ?? 'Delete' }}">
                                        <i class="fa-solid fa-trash-can text-[13px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-slate-100 dark:divide-white/5">
                @foreach($files as $file)
                <div class="p-5 mobile-card backup-row" data-id="{{ $file->id }}">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                <i class="fa-solid fa-file-zipper text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[13px] font-bold text-slate-700 dark:text-zinc-300 truncate">{{ $file->filename }}</p>
                                <p class="text-[11px] font-medium text-slate-400 dark:text-zinc-500 mt-0.5">{{ $file->backup_date->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        @if($file->status === 'success')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-[10px] font-[900] uppercase tracking-wider shrink-0">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                {{ __('admin.success') ?? 'OK' }}
                            </span>
                        @elseif($file->status === 'in_progress')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg text-[10px] font-[900] uppercase tracking-wider shrink-0">
                                <i class="fa-solid fa-spinner fa-spin text-[8px]"></i>
                                {{ __('admin.in_progress') ?? 'Running' }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-500/10 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-[900] uppercase tracking-wider shrink-0">
                                <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                                {{ __('admin.failed') ?? 'Failed' }}
                            </span>
                        @endif
                    </div>

                    {{-- Info Grid --}}
                    <div class="grid grid-cols-3 gap-3 mb-4 p-3 bg-slate-50/80 dark:bg-zinc-900/40 rounded-xl">
                        <div class="text-center">
                            <p class="text-[10px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1">{{ __('admin.local_status') ?? 'Local' }}</p>
                            @if($file->stored_locally)
                                <span class="text-emerald-500 text-lg"><i class="fa-solid fa-circle-check"></i></span>
                            @else
                                <span class="text-slate-300 dark:text-zinc-700 text-lg"><i class="fa-solid fa-circle-xmark"></i></span>
                            @endif
                        </div>
                        <div class="text-center">
                            <p class="text-[10px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1">{{ __('admin.cloud_status') ?? 'Cloud' }}</p>
                            @if($file->stored_on_r2)
                                <span class="text-emerald-500 text-lg"><i class="fa-solid fa-circle-check"></i></span>
                            @else
                                <span class="text-slate-300 dark:text-zinc-700 text-lg"><i class="fa-solid fa-circle-xmark"></i></span>
                            @endif
                        </div>
                        <div class="text-center">
                            <p class="text-[10px] font-[900] text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1">{{ __('admin.size') ?? 'Size' }}</p>
                            <span class="text-[13px] font-bold text-slate-600 dark:text-zinc-300">{{ $file->formatted_size }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        @if($file->status === 'success' && ($file->stored_locally || $file->stored_on_r2))
                        <a href="{{ route('admin.backups.download', $file->id) }}" class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-[12px] font-[900] bg-emerald-50/50 dark:bg-emerald-500/5 text-emerald-600 dark:text-emerald-400 rounded-xl hover:bg-emerald-500/10 transition-all shadow-sm">
                            <i class="fa-solid fa-download"></i> {{ __('admin.download') ?? 'Download' }}
                        </a>
                        @endif
                        <button onclick="confirmDelete('{{ $file->id }}', this)" class="flex-1 max-w-[100px] flex items-center justify-center gap-2 px-3 py-2 text-[12px] font-[900] bg-red-50/50 dark:bg-red-500/5 text-red-500 rounded-xl hover:bg-red-500/10 transition-all shadow-sm">
                            <i class="fa-solid fa-trash-can"></i> {{ __('admin.delete') ?? 'Delete' }}
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            @else
            {{-- Empty State --}}
            <div class="p-12 text-center flex flex-col items-center justify-center min-h-[400px]">
                <div class="w-20 h-20 rounded-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center text-slate-300 dark:text-zinc-600 mb-6 ring-4 ring-slate-50 dark:ring-zinc-800 border-[8px] border-white dark:border-[#09090b] shadow-xl">
                    <i class="fa-solid fa-server text-3xl"></i>
                </div>
                <h3 class="text-xl font-[900] text-slate-900 dark:text-white mb-2">{{ __('admin.no_backups') ?? 'No Backups Generated' }}</h3>
                <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400 mb-6 max-w-md">{{ __('admin.no_backups_desc') ?? 'Keep your system safe by generating automated or manual platform snapshots. Click create to start archiving.' }}</p>
                <button onclick="startBackup()" class="flex items-center justify-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[14px] transition-all shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] hover:scale-105 active:scale-95">
                    <i class="fa-solid fa-plus"></i> {{ __('admin.generate_snapshot') ?? 'Generate First Snapshot' }}
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<x-admin.modal id="deleteConfirmModal" :title="__('admin.warning')" class="max-w-md">
    <div class="text-center px-4 py-8">
        <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-500 mx-auto mb-6 shadow-inner ring-4 ring-red-50 dark:ring-red-500/5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-3 tracking-tight">{{ __('admin.delete_snapshot_title') ?? 'Delete Snapshot?' }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400">{{ __('admin.delete_snapshot_desc') ?? 'Are you absolutely sure you want to permanently delete this snapshot? This action cannot be undone.' }}</p>
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
    let isBackupRunning = {{ $isGenerating ? 'true' : 'false' }};
    let progressInterval = null;
    let backupToDeleteId = null;
    let backupDeleteBtnEl = null;

    // ─── GSAP Entrance Animations ─────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof gsap === 'undefined') return;

        // Note: .reveal-item is automatically animated by the global timeline in admin.blade.php
        
        // Header
        gsap.from('.dashboard-header-reveal', {
            y: -20,
            opacity: 0,
            duration: 0.8,
            delay: 0.1,
            ease: "power3.out",
            clearProps: "all"
        });

        // Table rows / mobile cards
        gsap.from('.backup-row', {
            y: 15,
            opacity: 0,
            duration: 0.5,
            stagger: 0.05,
            delay: 0.3,
            ease: "power2.out",
            clearProps: "all"
        });
    });

    // ─── Real-time Polling ─────────────────────────────────────
    if (isBackupRunning) {
        startPollingProgress();
    }

    function startPollingProgress() {
        if (progressInterval) return;

        progressInterval = setInterval(async () => {
            try {
                const res = await fetch('{{ route("admin.backups.status") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!data.isGenerating) {
                    clearInterval(progressInterval);
                    if (typeof showToast === 'function') {
                        showToast('success', '{{ __("admin.success") ?? "Success" }}', '{{ __("admin.backup_completed") ?? "Backup completed successfully!" }}');
                    }
                    setTimeout(() => window.location.reload(), 1000);
                }
            } catch (e) {
                console.error('Error polling backup status:', e);
            }
        }, 3000);
    }

    function dismissProgressAlert(btn) {
        const alert = btn.closest('#backup-pooling');
        if (alert && typeof gsap !== 'undefined') {
            gsap.to(alert, { opacity: 0, height: 0, marginTop: 0, marginBottom: 0, padding: 0, duration: 0.35, ease: 'power2.in', onComplete: () => alert.remove() });
        } else if (alert) {
            alert.remove();
        }
    }

    // ─── Create Backup ─────────────────────────────────────────
    async function startBackup() {
        if (isBackupRunning) {
            if (typeof showToast === 'function') {
                showToast('warning', '{{ __("admin.backup_already_running") ?? "Already Running" }}', '{{ __("admin.backup_already_running_desc") ?? "A backup is already in progress. Please wait for it to finish." }}');
            }
            return;
        }

        isBackupRunning = true;

        const btn = document.getElementById('create-backup-btn');
        if (btn) {
            btn.classList.add('opacity-50', 'pointer-events-none');
            btn.disabled = true;
        }

        const btnIcon = document.querySelector('.backup-btn-icon');
        const btnText = document.querySelector('.backup-btn-text');
        if (btnIcon) btnIcon.className = 'fa-solid fa-spinner fa-spin backup-btn-icon';
        if (btnText) btnText.textContent = '{{ __("admin.backup_in_progress_short") ?? "Generating..." }}';

        try {
            const res = await fetch('{{ route("admin.backups.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                if (typeof showToast === 'function') {
                    showToast('success', '{{ __("admin.backup_queued") ?? "Backup Queued" }}', '{{ __("admin.backup_queued_desc") ?? "Your backup is compressing beautifully in the background." }}');
                }
                setTimeout(() => window.location.reload(), 1500);
            } else {
                isBackupRunning = false;
                if (btn) { btn.classList.remove('opacity-50', 'pointer-events-none'); btn.disabled = false; }
                if (btnIcon) btnIcon.className = 'fa-solid fa-cloud-arrow-up backup-btn-icon';
                if (btnText) btnText.textContent = '{{ __("admin.create_backup") ?? "Create Backup" }}';
                if (typeof showToast === 'function') {
                    showToast('error', '{{ __("admin.error") ?? "Error" }}', '{{ __("admin.backup_failed") ?? "Triggering backup failed." }}');
                }
            }
        } catch (e) {
            isBackupRunning = false;
            if (btn) { btn.classList.remove('opacity-50', 'pointer-events-none'); btn.disabled = false; }
            if (btnIcon) btnIcon.className = 'fa-solid fa-cloud-arrow-up backup-btn-icon';
            console.error(e);
        }
    }

    // ─── Delete Logic ──────────────────────────────────────────
    window.confirmDelete = function(id, btn) {
        backupToDeleteId = id;
        backupDeleteBtnEl = btn;

        const confirmBtn = document.getElementById('confirmDeleteBtn');
        confirmBtn.onclick = executeDeleteBackup;

        if (window.modals && window.modals['deleteConfirmModal']) {
            window.modals['deleteConfirmModal'].show();
        } else {
            const m = document.getElementById('deleteConfirmModal');
            if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
        }
    };

    async function executeDeleteBackup() {
        if (!backupToDeleteId) return;
        const btn = document.getElementById('confirmDeleteBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> {{ __("admin.delete") }}...';

        try {
            const res = await fetch(`{{ url("admin/backups") }}/${backupToDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                // Close modal
                if (window.modals && window.modals['deleteConfirmModal']) window.modals['deleteConfirmModal'].hide();
                else document.getElementById('deleteConfirmModal')?.classList.add('hidden');

                if (typeof showToast === 'function') {
                    showToast('success', '{{ __("admin.deleted") ?? "Deleted!" }}', '{{ __("admin.snapshot_deleted_desc") ?? "Snapshot deleted successfully." }}');
                }

                // Animate removal
                const rows = document.querySelectorAll(`.backup-row[data-id="${backupToDeleteId}"]`);
                if (typeof gsap !== 'undefined') {
                    gsap.to(rows, { opacity: 0, x: 20, height: 0, padding: 0, margin: 0, duration: 0.4, stagger: 0.1, onComplete: () => {
                        rows.forEach(r => r.remove());
                        // Update count
                        const counter = document.getElementById('total-count-header');
                        if (counter) counter.textContent = Math.max(0, parseInt(counter.textContent) - 1);
                    }});
                } else {
                    rows.forEach(r => r.remove());
                }
            } else {
                if (typeof showToast === 'function') {
                    showToast('error', '{{ __("admin.error") ?? "Error" }}', '{{ __("admin.delete_failed") ?? "Failed to delete snapshot." }}');
                }
            }
        } catch (e) {
            console.error(e);
            if (typeof showToast === 'function') {
                showToast('error', '{{ __("admin.error") ?? "Error" }}', '{{ __("admin.delete_failed") ?? "Failed to delete snapshot." }}');
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
            backupToDeleteId = null;
            backupDeleteBtnEl = null;
        }
    }

    // Modal Global for inline close
    window.closeModal = window.closeModal || ((id) => {
        if (window.modals && window.modals[id]) window.modals[id].hide();
        else document.getElementById(id)?.classList.add('hidden');
    });
</script>
@endpush
@endsection
