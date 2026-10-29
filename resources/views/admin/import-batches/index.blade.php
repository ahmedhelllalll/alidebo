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

    {{-- Bulk Actions Bar --}}
    <div id="bulk-actions-bar" class="hidden items-center justify-between bg-primary/10 border border-primary/20 p-3 sm:p-4 rounded-2xl mb-4 transition-all duration-300">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center font-bold text-sm shrink-0" id="bulk-selected-count">0</span>
            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2">
                <span class="text-sm font-bold text-slate-800 dark:text-zinc-200">{{ __('admin.batches_selected') ?? 'Batches Selected' }}</span>
                <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400">(~<span id="bulk-businesses-count">0</span> {{ __('admin.businesses') ?? 'businesses' }})</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="promptBulkDelete()" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center gap-1.5 active:scale-[0.98]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ __('admin.delete_selected') ?? 'Bulk Delete' }}
            </button>
            <button type="button" onclick="deselectAllBatches()" class="px-3.5 py-2 bg-white dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-700 text-xs font-bold rounded-xl border border-slate-200 dark:border-white/10 transition-colors">
                {{ __('admin.deselect_all') ?? 'Deselect All' }}
            </button>
        </div>
    </div>

    <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative z-10 w-full overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-start border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-zinc-800/30 border-b border-slate-200/60 dark:border-white/[0.05]">
                        <th class="ps-5 pe-2 py-4 w-10 text-center">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 cursor-pointer transition-all" title="{{ __('admin.select_all') ?? 'Select All' }}">
                        </th>
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
                        <tr id="batch-row-{{ $batch->id }}" class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors group">
                            <td class="ps-5 pe-2 py-4 w-10 text-center">
                                <input type="checkbox" value="{{ $batch->id }}" 
                                    data-status="{{ $batch->status }}" 
                                    data-businesses="{{ (int)$batch->imported_rows + (int)$batch->updated_rows }}"
                                    @if($batch->status === 'processing') disabled title="{{ __('admin.cannot_select_processing_batch') ?? 'Cannot select a processing batch' }}" @endif
                                    class="batch-select-checkbox w-4 h-4 rounded text-primary focus:ring-primary border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 cursor-pointer transition-all disabled:opacity-25 disabled:cursor-not-allowed" 
                                    onchange="updateBulkActionState()">
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-slate-800 dark:text-zinc-200">#{{ $batch->id }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-800 dark:text-zinc-200 truncate max-w-[200px]" title="{{ $batch->original_file_name }}">{{ $batch->original_file_name }}</span>
                                    <span class="text-xs font-medium text-slate-400 mt-1">{{ $batch->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm font-medium text-slate-600 dark:text-zinc-300">{{ $batch->admin->name ?? __('admin.unknown_user') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col items-center" title="{{ __('admin.total_rows') ?? 'Total' }}">
                                        <span class="text-[10px] uppercase font-bold text-slate-400">{{ __('admin.total') ?? 'Total' }}</span>
                                        <span id="batch-total-{{ $batch->id }}" class="text-sm font-bold text-slate-700 dark:text-zinc-300">{{ $batch->total_rows }}</span>
                                    </div>
                                    <div class="flex flex-col items-center" title="{{ __('admin.imported_rows') ?? 'Created' }}">
                                        <span class="text-[10px] uppercase font-bold text-emerald-500">{{ __('admin.created') ?? 'Created' }}</span>
                                        <span id="batch-imported-{{ $batch->id }}" class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $batch->imported_rows }}</span>
                                    </div>
                                    @if($batch->updated_rows > 0)
                                    <div class="flex flex-col items-center" title="{{ __('admin.updated_rows') ?? 'Updated' }}">
                                        <span class="text-[10px] uppercase font-bold text-blue-500">{{ __('admin.updated') ?? 'Updated' }}</span>
                                        <span id="batch-updated-{{ $batch->id }}" class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $batch->updated_rows }}</span>
                                    </div>
                                    @endif
                                    <div class="flex flex-col items-center" title="{{ __('admin.skipped_rows') ?? 'Skipped' }}">
                                        <span class="text-[10px] uppercase font-bold text-red-400">{{ __('admin.failed') ?? 'Failed' }}</span>
                                        <span id="batch-skipped-{{ $batch->id }}" class="text-sm font-bold text-red-500 dark:text-red-400">{{ $batch->skipped_rows }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col items-start gap-1">
                                    @if($batch->is_dry_run)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200/50 dark:border-amber-500/20">
                                            <i class="fa-solid fa-flask text-[9px]"></i> {{ __('admin.dry_run') ?? 'Dry Run' }}
                                        </span>
                                    @endif
                                    <div id="batch-status-{{ $batch->id }}">
                                        @if($batch->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-500/20">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> {{ __('admin.completed') ?? 'Completed' }}
                                            </span>
                                        @elseif($batch->status === 'processing')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-200/50 dark:border-blue-500/20">
                                                <div class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div> {{ __('admin.processing') ?? 'Processing' }}
                                            </span>
                                        @elseif($batch->status === 'queued')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200/50 dark:border-amber-500/20">
                                                <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div> {{ __('admin.queued') ?? 'Queued' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-200/50 dark:border-red-500/20">
                                                <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div> {{ __('admin.failed') ?? 'Failed' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-end">
                                <div class="flex items-center justify-end gap-2 relative">
                                    @if(!$batch->is_dry_run)
                                    <a href="{{ route('admin.businesses.index', ['batch_id' => $batch->id]) }}" class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-xl transition-all" title="{{ __('admin.view_businesses') ?? 'View Businesses' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @endif
                                    @if($batch->error_log_path)
                                    <a href="{{ route('admin.import-batches.errors', $batch->id) }}" class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-500/10 rounded-xl transition-all" title="{{ __('admin.download_errors') ?? 'Download Errors' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    @endif
                                    @if($batch->rejected_file_path)
                                    <a href="{{ route('admin.import-batches.rejected', $batch->id) }}" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all" title="{{ __('admin.download_rejected') ?? 'Download Rejected Rows (CSV)' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9l4 4m0-4l-4 4"/></svg>
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
                            <td colspan="7" class="px-5 py-12 text-center">
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
<x-admin.modal id="bulkDeleteModal" :title="__('admin.warning')" class="max-w-md">
    <div class="text-center px-4 py-6">
        <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-500 mx-auto mb-5 shadow-inner ring-4 ring-red-50 dark:ring-red-500/5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white mb-2 tracking-tight">{{ __('admin.bulk_delete_title') ?? 'Delete Selected Batches?' }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400 mb-4">
            {{ __('admin.bulk_delete_selected_prompt') ?? 'You have selected' }} <span id="bulkModalBatchCount" class="font-bold text-red-500">0</span> {{ __('admin.batches') ?? 'batches' }} (~<span id="bulkModalBizCount" class="font-bold text-slate-800 dark:text-zinc-200">0</span> {{ __('admin.businesses') ?? 'businesses' }}).
        </p>
        <div class="p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-200 dark:border-red-500/20 text-xs text-red-600 dark:text-red-400 font-semibold text-start">
            <i class="fa-solid fa-triangle-exclamation me-1.5"></i> {{ __('admin.bulk_delete_irreversible_warning') ?? 'This action is irreversible. All selected batches, imported businesses, covers, logos, gallery images, and log files will be permanently deleted.' }}
        </div>
    </div>
    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full pb-2">
            <button type="button" onclick="closeModal('bulkDeleteModal')" class="w-full sm:flex-1 px-5 py-3 bg-white dark:bg-[#121214]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[14px] hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">
                {{ __('admin.cancel') }}
            </button>
            <button type="button" id="confirmBulkDeleteBtn" onclick="executeBulkDelete();" class="w-full sm:flex-1 px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-[900] text-[14px] shadow-[0_8px_20px_rgba(239,68,68,0.25)] hover:shadow-[0_12px_25px_rgba(239,68,68,0.35)] transition-all active:scale-[0.98]">
                {{ __('admin.delete_all_selected') ?? 'Delete All Selected' }}
            </button>
        </div>
    </x-slot>
</x-admin.modal>

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

<x-admin.modal id="importExcelModal" title="{{ __('admin.new_import') ?? 'New Bulk Import' }}" width="max-w-xl">
    <div class="p-6 space-y-4">
        <div class="p-4 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl border border-emerald-100 dark:border-emerald-500/20">
            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold leading-relaxed">
                {{ __('admin.import_instructions') ?? 'Upload one or multiple Excel / CSV files (up to 100 sheets). Batches are queued and executed sequentially in the background to ensure minimal server resource usage.' }}
            </p>
        </div>
        <div x-data="{
                files: [],
                isDragging: false,
                handleFiles(fileList) {
                    const incoming = Array.from(fileList);
                    incoming.forEach(f => {
                        if (!this.files.some(existing => existing.name === f.name && existing.size === f.size)) {
                            this.files.push(f);
                        }
                    });
                    this.syncInput();
                },
                removeFile(index) {
                    this.files.splice(index, 1);
                    this.syncInput();
                },
                clearAll() {
                    this.files = [];
                    this.syncInput();
                },
                syncInput() {
                    const dt = new DataTransfer();
                    this.files.forEach(f => dt.items.add(f));
                    const input = document.getElementById('import_files');
                    if (input) input.files = dt.files;
                },
                formatSize(bytes) {
                    if (!bytes) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },
                totalSize() {
                    const total = this.files.reduce((acc, f) => acc + f.size, 0);
                    return this.formatSize(total);
                }
            }">
            
            <input type="file" id="import_files" name="files[]" multiple accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="hidden" @change="handleFiles($event.target.files)">

            <!-- Drop Zone / Upload Card -->
            <div class="mt-4 border-2 border-dashed border-slate-300 dark:border-zinc-700 rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/5 transition-all text-center group"
                 :class="{ 'border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5': isDragging }"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
                 @click="document.getElementById('import_files').click()">
                
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mb-3 transition-transform group-hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                </div>
                <h3 class="text-[14px] font-[900] text-slate-700 dark:text-zinc-200">{{ __('admin.click_to_upload') ?? 'Click to upload files or drag & drop' }}</h3>
                <p class="text-xs font-semibold text-slate-500 dark:text-zinc-500 mt-1">{{ __('admin.multi_file_support_hint') ?? 'Multi-file support: select up to 100 sheets (.xlsx, .csv)' }}</p>
            </div>

            <!-- Selected Files List -->
            <div x-show="files.length > 0" style="display: none;" class="mt-4 space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-slate-600 dark:text-zinc-300 px-1">
                    <span><span class="text-emerald-600 dark:text-emerald-400 font-extrabold" x-text="files.length"></span> {{ __('admin.files_queued') ?? 'files queued' }} (<span x-text="totalSize()"></span>)</span>
                    <button type="button" @click="clearAll()" class="text-rose-500 hover:text-rose-600 transition-colors">{{ __('admin.clear_all') ?? 'Clear All' }}</button>
                </div>
                <div class="max-h-44 overflow-y-auto custom-scrollbar space-y-2 pr-1">
                    <template x-for="(file, index) in files" :key="index">
                        <div class="p-2.5 bg-white dark:bg-zinc-800 border border-slate-200/80 dark:border-white/5 rounded-xl flex items-center justify-between shadow-xs">
                            <div class="flex items-center gap-3 overflow-hidden min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-700 dark:text-zinc-200 truncate" x-text="file.name"></p>
                                    <p class="text-[10px] font-semibold text-slate-400" x-text="formatSize(file.size)"></p>
                                </div>
                            </div>
                            <button type="button" @click.stop="removeFile(index)" class="p-1 text-slate-400 hover:text-rose-500 transition-colors shrink-0" title="{{ __('admin.remove') ?? 'Remove' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Dry Run Mode Option -->
            <div class="mt-4 p-3.5 bg-slate-50 dark:bg-zinc-800/60 rounded-2xl border border-slate-200/70 dark:border-white/5 flex items-start gap-3 cursor-pointer" onclick="document.getElementById('is_dry_run').click()">
                <input type="checkbox" id="is_dry_run" name="is_dry_run" value="1" class="mt-0.5 w-4 h-4 text-emerald-500 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer" onclick="event.stopPropagation()">
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-800 dark:text-zinc-200 cursor-pointer">{{ __('admin.dry_run_mode') ?? 'Dry Run / Preview Mode (Validate without saving)' }}</span>
                    <span class="text-[11px] font-medium text-slate-500 dark:text-zinc-400 mt-0.5">{{ __('admin.dry_run_explanation') ?? 'Validates category hierarchy, duplicates, and columns without writing changes to the database.' }}</span>
                </div>
            </div>
        </div>
    </div>
    <x-slot name="footer">
        <button id="submitImportBtn" onclick="submitImport()" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-500/20">
            {{ __('admin.start_bulk_import') ?? __('admin.start_import') ?? 'Start Bulk Import' }}
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
        const fileInput = document.getElementById('import_files');
        if (fileInput) fileInput.value = '';
        const dryRunCb = document.getElementById('is_dry_run');
        if (dryRunCb) dryRunCb.checked = false;
        window.modals?.importExcelModal?.show() || document.getElementById('importExcelModal').classList.remove('hidden');
    }

    async function submitImport() {
        const fileInput = document.getElementById('import_files');
        const btn = document.getElementById('submitImportBtn');
        if (!fileInput || !fileInput.files.length) {
            if (typeof showToast !== 'undefined') showToast('error', '{{ __('admin.please_select_at_least_one_file') }}');
            return;
        }

        const formData = new FormData();
        for (let i = 0; i < fileInput.files.length; i++) {
            formData.append('files[]', fileInput.files[i]);
        }
        const dryRunCb = document.getElementById('is_dry_run');
        if (dryRunCb && dryRunCb.checked) {
            formData.append('is_dry_run', '1');
        }

        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> {{ __('admin.queuing_sheets') }}';
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
                setTimeout(() => window.location.reload(), 1200);
            } else {
                if (typeof showToast !== 'undefined') showToast('error', res.message || '{{ __('admin.import_failed') }}');
            }
        } catch (e) {
            if (typeof showToast !== 'undefined') showToast('error', '{{ __('admin.network_error_import') }}');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // --- Bulk Selection & Actions ---
    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.batch-select-checkbox:not(:disabled)');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateBulkActionState();
    }

    function updateBulkActionState() {
        const checked = document.querySelectorAll('.batch-select-checkbox:checked');
        const bulkBar = document.getElementById('bulk-actions-bar');
        const countSpan = document.getElementById('bulk-selected-count');
        const bizSpan = document.getElementById('bulk-businesses-count');
        const masterCb = document.getElementById('selectAllCheckbox');

        let totalBiz = 0;
        checked.forEach(cb => {
            totalBiz += parseInt(cb.dataset.businesses || 0);
        });

        if (checked.length > 0) {
            if (countSpan) countSpan.innerText = checked.length;
            if (bizSpan) bizSpan.innerText = totalBiz.toLocaleString();
            if (bulkBar) {
                bulkBar.classList.remove('hidden');
                bulkBar.classList.add('flex');
            }
        } else {
            if (bulkBar) {
                bulkBar.classList.add('hidden');
                bulkBar.classList.remove('flex');
            }
        }

        if (masterCb) {
            const selectable = document.querySelectorAll('.batch-select-checkbox:not(:disabled)');
            masterCb.checked = selectable.length > 0 && checked.length === selectable.length;
            masterCb.indeterminate = checked.length > 0 && checked.length < selectable.length;
        }
    }

    function deselectAllBatches() {
        const checkboxes = document.querySelectorAll('.batch-select-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        const masterCb = document.getElementById('selectAllCheckbox');
        if (masterCb) masterCb.checked = false;
        updateBulkActionState();
    }

    function promptBulkDelete() {
        const checked = document.querySelectorAll('.batch-select-checkbox:checked');
        if (checked.length === 0) {
            if (typeof showToast !== 'undefined') showToast('warning', '{{ __('admin.please_select_at_least_one_batch') }}');
            return;
        }

        // Check for any processing batch
        let hasProcessing = false;
        checked.forEach(cb => {
            if (cb.dataset.status === 'processing') hasProcessing = true;
        });

        if (hasProcessing) {
            if (typeof showToast !== 'undefined') showToast('warning', '{{ __('admin.selected_batches_processing_warning') }}');
            return;
        }

        let totalBiz = 0;
        checked.forEach(cb => totalBiz += parseInt(cb.dataset.businesses || 0));

        const modalBatchSpan = document.getElementById('bulkModalBatchCount');
        const modalBizSpan = document.getElementById('bulkModalBizCount');
        if (modalBatchSpan) modalBatchSpan.innerText = checked.length;
        if (modalBizSpan) modalBizSpan.innerText = totalBiz.toLocaleString();

        window.modals?.bulkDeleteModal?.show() || document.getElementById('bulkDeleteModal').classList.remove('hidden');
    }

    async function executeBulkDelete() {
        const checked = document.querySelectorAll('.batch-select-checkbox:checked');
        const ids = Array.from(checked).map(cb => parseInt(cb.value));
        if (ids.length === 0) return;

        const btn = document.getElementById('confirmBulkDeleteBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> {{ __('admin.deleting_batches') }}';
        btn.disabled = true;

        try {
            const res = await (await fetch(`${window.AppConfig.adminUrl}/import-batches/bulk-delete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.AppConfig.csrfToken || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ batch_ids: ids })
            })).json();

            if (res.success) {
                if (typeof showToast !== 'undefined') showToast('success', res.message);
                window.modals?.bulkDeleteModal?.hide();
                setTimeout(() => window.location.reload(), 1200);
            } else {
                if (typeof showToast !== 'undefined') showToast('error', res.message || '{{ __('admin.deletion_failed') }}');
            }
        } catch (e) {
            if (typeof showToast !== 'undefined') showToast('error', '{{ __('admin.network_error_bulk_delete') }}');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
            window.modals?.bulkDeleteModal?.hide();
        }
    }

    function promptDeleteBatch(id) {
        const rowCb = document.querySelector(`.batch-select-checkbox[value="${id}"]`);
        if (rowCb && rowCb.dataset.status === 'processing') {
            if (typeof showToast !== 'undefined') showToast('warning', '{{ __('admin.cannot_delete_processing_batch') }}');
            return;
        }
        batchToDeleteId = id;
        window.modals?.deleteBatchModal?.show() || document.getElementById('deleteBatchModal').classList.remove('hidden');
    }

    async function executeDeleteBatch() {
        if(!batchToDeleteId) return;
        const btn = document.getElementById('confirmDeleteBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';
        btn.disabled = true;
        try {
            const res = await (await fetch(`${window.AppConfig.adminUrl}/import-batches/${batchToDeleteId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': window.AppConfig.csrfToken || '{{ csrf_token() }}', 'Accept': 'application/json' }
            })).json();
            if (res.success) {
                if (typeof showToast !== 'undefined') showToast('success', res.message);
                setTimeout(() => window.location.reload(), 1200);
            } else {
                if (typeof showToast !== 'undefined') showToast('error', res.message || '{{ __('admin.deletion_failed') }}');
            }
        } catch (e) { 
            if (typeof showToast !== 'undefined') showToast('error', '{{ __("admin.deletion_failed") ?? "Deletion Failed" }}'); 
        }
        finally {
            batchToDeleteId = null;
            btn.innerHTML = originalText;
            btn.disabled = false;
            window.modals?.deleteBatchModal?.hide();
        }
    }

    let pollInterval = null;
    function startPollingIfActive() {
        // Robust language-agnostic detection: check data-status on batch checkboxes
        const hasActive = document.querySelectorAll('.batch-select-checkbox[data-status="processing"], .batch-select-checkbox[data-status="queued"]').length > 0;

        if (!hasActive) return;

        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(async () => {
            try {
                const res = await (await fetch(`${window.AppConfig.adminUrl}/import-batches`, {
                    headers: { 'Accept': 'application/json' }
                })).json();

                if (res.success && res.batches) {
                    res.batches.forEach(b => {
                        const totalEl = document.getElementById(`batch-total-${b.id}`);
                        const importedEl = document.getElementById(`batch-imported-${b.id}`);
                        const updatedEl = document.getElementById(`batch-updated-${b.id}`);
                        const skippedEl = document.getElementById(`batch-skipped-${b.id}`);
                        const statusEl = document.getElementById(`batch-status-${b.id}`);
                        const rowCb = document.querySelector(`.batch-select-checkbox[value="${b.id}"]`);

                        if (totalEl) totalEl.innerText = b.total_rows;
                        if (importedEl) importedEl.innerText = b.imported_rows;
                        if (updatedEl) updatedEl.innerText = b.updated_rows;
                        if (skippedEl) skippedEl.innerText = b.skipped_rows;

                        if (rowCb) {
                            rowCb.dataset.status = b.status;
                            rowCb.dataset.businesses = (b.imported_rows || 0) + (b.updated_rows || 0);
                            if (b.status === 'processing') {
                                rowCb.disabled = true;
                                if (rowCb.checked) {
                                    rowCb.checked = false;
                                    updateBulkActionState();
                                }
                            } else {
                                rowCb.disabled = false;
                            }
                        }

                        if (statusEl) {
                            if (b.status === 'completed') {
                                statusEl.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-500/20"><div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> {{ __('admin.completed') ?? 'Completed' }}</span>`;
                            } else if (b.status === 'processing') {
                                statusEl.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-200/50 dark:border-blue-500/20"><div class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div> {{ __('admin.processing') ?? 'Processing' }}</span>`;
                            } else if (b.status === 'queued') {
                                statusEl.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200/50 dark:border-amber-500/20"><div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div> {{ __('admin.queued') ?? 'Queued' }}</span>`;
                            } else {
                                statusEl.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-200/50 dark:border-red-500/20"><div class="w-1.5 h-1.5 rounded-full bg-red-500"></div> {{ __('admin.failed') ?? 'Failed' }}</span>`;
                            }
                        }
                    });

                    if (!res.has_active) {
                        clearInterval(pollInterval);
                        pollInterval = null;
                    }
                }
            } catch (err) {
                // Ignore transient network hiccups during polling
            }
        }, 7000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initModals();
        startPollingIfActive();
        if (typeof gsap !== 'undefined') {
            gsap.from('.dashboard-header-reveal', { y: -20, opacity: 0, duration: 0.6, ease: "power3.out" });
            gsap.from('.list-card', { y: 30, opacity: 0, duration: 0.7, ease: "power3.out", delay: 0.1 });
        }
    });
</script>
@endpush
@endsection
