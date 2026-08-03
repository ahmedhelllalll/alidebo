@extends('admin.layouts.admin')
@section('title', __('admin.import_manager') ?? 'Import Manager')
@section('content')
<div id="import-batches-wrapper" class="relative min-h-[600px]">
    {{-- Header & Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal mb-6" data-total="{{ $batches->total() }}">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.import_manager') ?? 'Import Manager' }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.batch_history') ?? 'Batch History' }} (<span id="total-count-header">{{ $batches->total() }}</span>)</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" onclick="openImportModal()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(16,185,129,0.25)] hover:shadow-[0_12px_25px_rgba(16,185,129,0.35)] transition-all active:scale-[0.98]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                {{ __('admin.new_import') ?? 'New Import' }}
            </button>
        </div>
    </div>

    <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative z-10 w-full overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-start border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-200/60 dark:border-white/[0.05]">
                        <th class="px-5 py-4 text-xs font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider text-start">{{ __('admin.batch_id') ?? 'ID' }}</th>
                        <th class="px-5 py-4 text-xs font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider text-start">{{ __('admin.file_name') ?? 'File Name' }}</th>
                        <th class="px-5 py-4 text-xs font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider text-start">{{ __('admin.uploaded_by') ?? 'Uploaded By' }}</th>
                        <th class="px-5 py-4 text-xs font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider text-start">{{ __('admin.stats') ?? 'Stats' }}</th>
                        <th class="px-5 py-4 text-xs font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider text-start">{{ __('admin.status') ?? 'Status' }}</th>
                        <th class="px-5 py-4 text-xs font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider text-end">{{ __('admin.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/[0.02]">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors group">
                            <td class="px-5 py-4 text-sm font-bold text-slate-800 dark:text-zinc-200">#{{ $batch->id }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-800 dark:text-zinc-200 truncate max-w-[200px]" title="{{ $batch->original_file_name }}">{{ $batch->original_file_name }}</span>
                                    <span class="text-xs font-medium text-slate-400 mt-1">{{ $batch->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm font-medium text-slate-600 dark:text-zinc-300">{{ $batch->admin->name ?? 'Unknown' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col items-center" title="{{ __('admin.total_rows') ?? 'Total' }}">
                                        <span class="text-[10px] uppercase font-bold text-slate-400">Total</span>
                                        <span class="text-sm font-bold text-slate-700 dark:text-zinc-300">{{ $batch->total_rows }}</span>
                                    </div>
                                    <div class="flex flex-col items-center" title="{{ __('admin.imported_rows') ?? 'Success' }}">
                                        <span class="text-[10px] uppercase font-bold text-emerald-500">Success</span>
                                        <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $batch->imported_rows }}</span>
                                    </div>
                                    <div class="flex flex-col items-center" title="{{ __('admin.skipped_rows') ?? 'Skipped' }}">
                                        <span class="text-[10px] uppercase font-bold text-red-400">Failed</span>
                                        <span class="text-sm font-bold text-red-500 dark:text-red-400">{{ $batch->skipped_rows }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if($batch->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-500/20">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> {{ __('admin.completed') ?? 'Completed' }}
                                    </span>
                                @elseif($batch->status === 'processing')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-200/50 dark:border-blue-500/20">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div> {{ __('admin.processing') ?? 'Processing' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-200/50 dark:border-red-500/20">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div> {{ __('admin.failed') ?? 'Failed' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-end">
                                <div class="flex items-center justify-end gap-2 relative">
                                    <a href="{{ route('admin.businesses.index', ['batch_id' => $batch->id]) }}" class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-xl transition-all" title="{{ __('admin.view_businesses') ?? 'View Businesses' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($batch->error_log_path)
                                    <a href="{{ route('admin.import-batches.errors', $batch->id) }}" class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-500/10 rounded-xl transition-all" title="{{ __('admin.download_errors') ?? 'Download Errors' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    @endif
                                    <button onclick="promptDeleteBatch({{ $batch->id }})" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-xl transition-all" title="{{ __('admin.rollback_batch') ?? 'Rollback/Delete Batch' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-zinc-800/50 flex items-center justify-center mb-4 border border-slate-100 dark:border-white/5">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-[15px] font-[900] text-slate-900 dark:text-white">{{ __('admin.no_imports_found') ?? 'No imports found' }}</p>
                                    <p class="text-sm font-medium text-slate-500 mt-1">{{ __('admin.start_new_import_msg') ?? 'Click New Import to get started' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-200/60 dark:border-white/[0.05]">
            {{ $batches->links() }}
        </div>
    </div>
</div>

{{-- Modals --}}
<x-admin.modal id="deleteBatchModal" :title="__('admin.warning')" class="max-w-md">
    <div class="text-center px-4 py-8">
        <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-500 mx-auto mb-6 shadow-inner ring-4 ring-red-50 dark:ring-red-500/5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-3 tracking-tight">{{ __('admin.rollback_confirm_title') ?? 'Rollback Import Batch?' }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400">
            {{ __('admin.rollback_confirm_desc') ?? 'This action will permanently delete this batch AND ALL businesses that were imported within it, including their images. This cannot be undone.' }}
        </p>
    </div>
    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full pb-2">
            <button type="button" onclick="closeModal('deleteBatchModal')" class="w-full sm:flex-1 px-5 py-3 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[14px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">
                {{ __('admin.cancel') }}
            </button>
            <button type="button" id="confirmDeleteBtn" onclick="executeDeleteBatch();" class="w-full sm:flex-1 px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(239,68,68,0.25)] hover:shadow-[0_12px_25px_rgba(239,68,68,0.35)] transition-all active:scale-[0.98]">
                {{ __('admin.rollback') ?? 'Yes, Rollback' }}
            </button>
        </div>
    </x-slot>
</x-admin.modal>

<x-admin.modal id="importExcelModal" title="{{ __('admin.new_import') ?? 'New Import' }}" width="max-w-md">
    <div class="p-6 space-y-4">
        <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl border border-emerald-100 dark:border-emerald-500/20">
            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold leading-relaxed">
                {{ __('admin.import_instructions') ?? 'Upload an Excel or CSV file. The process will run in the background and you will receive an email upon completion.' }}
            </p>
        </div>
        <div x-data="{
                fileName: '',
                fileSize: '',
                isDragging: false,
                handleFileChange(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.fileName = file.name;
                        this.fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    } else {
                        this.fileName = '';
                        this.fileSize = '';
                    }
                },
                removeFile() {
                    this.fileName = '';
                    this.fileSize = '';
                    document.getElementById('import_file').value = '';
                }
            }">
            
            <input type="file" id="import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="hidden" @change="handleFileChange">

            <!-- Drop Zone / Upload Card -->
            <div x-show="!fileName"
                 class="mt-4 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-2xl p-8 flex flex-col items-center justify-center cursor-pointer hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/5 transition-all text-center group"
                 :class="{ 'border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5': isDragging }"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="isDragging = false; document.getElementById('import_file').files = $event.dataTransfer.files; document.getElementById('import_file').dispatchEvent(new Event('change'))"
                 @click="document.getElementById('import_file').click()">
                
                <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mb-4 transition-transform group-hover:scale-110">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <h3 class="text-[14px] font-[900] text-slate-700 dark:text-zinc-200">{{ __('admin.click_to_upload') ?? 'Click to upload or drag & drop' }}</h3>
                <p class="text-xs font-semibold text-slate-500 dark:text-zinc-500 mt-1">{{ __('admin.accepted_formats') ?? 'Accepted formats: .xlsx, .csv' }}</p>
            </div>

            <!-- Selected File Preview -->
            <div x-show="fileName" style="display: none;" class="mt-4 p-4 bg-white dark:bg-zinc-800 border-2 border-emerald-200 dark:border-emerald-500/30 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-4 overflow-hidden">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[14px] font-[900] text-slate-700 dark:text-zinc-200 truncate" x-text="fileName"></p>
                        <p class="text-[12px] font-semibold text-slate-500 dark:text-zinc-500 mt-0.5" x-text="fileSize"></p>
                    </div>
                </div>
                <button type="button" @click.stop="removeFile()" class="p-2.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors shrink-0" title="Remove File">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>
    <x-slot name="footer">
        <button id="submitImportBtn" onclick="submitImport()" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-500/20">
            {{ __('admin.start_import') ?? 'Start Import' }}
        </button>
    </x-slot>
</x-admin.modal>

@push('scripts')
<script>
    let batchToDeleteId = null;

    window.modals = window.modals || {};
    function initModals() {
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
                    this.el.classList.remove('hidden');
                    this.el.classList.add('flex');
                    this.el.style.display = 'flex';
                    this.el.style.opacity = '1';
                    this.el.style.pointerEvents = 'auto';
                    if (typeof gsap !== 'undefined') {
                        gsap.fromTo(backdrop, { opacity: 0 }, { opacity: 1, duration: 0.4, overwrite: 'auto' });
                        gsap.fromTo(panel, 
                            { opacity: 0, scale: 0.9, y: 20 }, 
                            { opacity: 1, scale: 1, y: 0, duration: 0.5, ease: 'expo.out', clearProps: "transform", overwrite: 'auto' }
                        );
                    }
                },
                hide: function() {
                    if (typeof gsap !== 'undefined') {
                        gsap.to(panel, { opacity: 0, scale: 0.95, y: 15, duration: 0.3, ease: 'power2.in', overwrite: 'auto' });
                        gsap.to(backdrop, { opacity: 0, duration: 0.3, overwrite: 'auto', onComplete: () => {
                            this.el.classList.add('hidden');
                            this.el.classList.remove('flex');
                            this.el.style.display = '';
                            this.el.style.opacity = '';
                            this.el.style.pointerEvents = '';
                        }});
                    } else {
                        this.el.classList.add('hidden');
                        this.el.classList.remove('flex');
                    }
                }
            };
        });
    }
    window.closeModal = (id) => window.modals[id]?.hide();

    function openImportModal() {
        document.getElementById('import_file').value = '';
        window.modals?.importExcelModal?.show() || document.getElementById('importExcelModal').classList.remove('hidden');
    }

    async function submitImport() {
        const fileInput = document.getElementById('import_file');
        const btn = document.getElementById('submitImportBtn');
        if (!fileInput.files.length) {
            if (typeof showToast !== 'undefined') showToast('error', 'Please select a file first.');
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Processing...';
        btn.disabled = true;

        try {
            const res = await (await fetch(`${window.AppConfig.adminUrl}/import-batches`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': window.AppConfig.csrfToken || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })).json();

            if (res.success) {
                if (typeof showToast !== 'undefined') showToast('success', res.message);
                window.modals?.importExcelModal?.hide();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                if (typeof showToast !== 'undefined') showToast('error', res.message || 'Error starting import');
            }
        } catch (e) {
            if (typeof showToast !== 'undefined') showToast('error', 'Network error while starting import');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    function promptDeleteBatch(id) {
        batchToDeleteId = id;
        window.modals?.deleteBatchModal?.show() || document.getElementById('deleteBatchModal').classList.remove('hidden');
    }

    async function executeDeleteBatch() {
        if(!batchToDeleteId) return;
        const btn = document.getElementById('confirmDeleteBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';
        try {
            const res = await (await fetch(`${window.AppConfig.adminUrl}/import-batches/${batchToDeleteId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': window.AppConfig.csrfToken || '{{ csrf_token() }}', 'Accept': 'application/json' }
            })).json();
            if (res.success) {
                if (typeof showToast !== 'undefined') showToast('success', res.message);
                setTimeout(() => window.location.reload(), 1500);
            }
        } catch (e) { 
            if (typeof showToast !== 'undefined') showToast('error', '{{ __("admin.deletion_failed") ?? "Deletion Failed" }}'); 
        }
        finally {
            batchToDeleteId = null;
            btn.innerHTML = originalText;
            window.modals?.deleteBatchModal?.hide();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initModals();
        if (typeof gsap !== 'undefined') {
            gsap.from('.dashboard-header-reveal', { y: -20, opacity: 0, duration: 0.6, ease: "power3.out" });
            gsap.from('.list-card', { y: 30, opacity: 0, duration: 0.7, ease: "power3.out", delay: 0.1 });
        }
    });
</script>
@endpush
@endsection
