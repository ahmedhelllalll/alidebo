{{-- Desktop Table (md and up) --}}
<div class="hidden md:block">
    <x-admin.table>
        <x-slot name="header">
            <tr class="bg-slate-50/50 dark:bg-zinc-800/20 border-b border-slate-100 dark:border-zinc-800/60">
                <th scope="col" class="w-12 px-6 py-5 text-center">
                    <input type="checkbox" id="selectAllBusinesses" class="rounded bg-slate-100 dark:bg-zinc-800 border-slate-300 dark:border-zinc-600 text-primary focus:ring-primary shadow-sm cursor-pointer" onclick="toggleAllCheckboxes(this)">
                </th>
                <th scope="col" class="px-6 py-5 text-start text-[12px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('admin.business') }}</th>
                <th scope="col" class="px-6 py-5 text-start text-[12px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('admin.owner') }}</th>
                <th scope="col" class="px-6 py-5 text-start text-[12px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('admin.status') }}</th>
                <th scope="col" class="px-6 py-5 text-end text-[12px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-widest">{{ __('admin.actions') }}</th>
            </tr>
        </x-slot>
        <tbody id="business-table-body" class="divide-y divide-slate-50 dark:divide-white/[0.03]">
            @forelse($businesses as $index => $business)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-zinc-800/30 transition-all group business-row" data-id="{{ $business->id }}" style="--row-index: {{ $index }}">
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <input type="checkbox" class="business-checkbox rounded bg-slate-100 dark:bg-zinc-800 border-slate-300 dark:border-zinc-600 text-primary focus:ring-primary shadow-sm cursor-pointer" value="{{ $business->id }}" onchange="updateBulkActionVisibility()">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-700/50 flex items-center justify-center font-[900] text-slate-400 dark:text-zinc-500 shadow-sm overflow-hidden shrink-0 group-hover:border-primary/30 transition-colors">
                            @if($business->logo)
                                <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center">
                                    <span class="text-lg text-primary">{{ substr($business->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('business.view', $business->slug) }}" target="_blank" class="text-[14px] font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors flex items-center gap-1.5 tooltip max-w-[200px] truncate" title="{{ __('admin.view_business') }}">
                                {{ $business->name }}
                                <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1 text-[11px] font-medium text-slate-400">
                                <span class="bg-primary/5 text-primary dark:bg-primary/10 px-1.5 py-0.5 rounded text-[10px] font-[900] tracking-wide">{{ $business->category->name ?? "N/A" }}</span>
                                <span class="text-slate-300 dark:text-zinc-700">•</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $business->city->name ?? "N/A" }}@if($business->city && $business->city->country), {{ $business->city->country->name }}@endif
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($business->owner_id)
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 text-primary flex items-center justify-center font-bold text-[10px] uppercase">
                                {{ substr($business->owner->name ?? '?', 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[13px] font-bold text-slate-700 dark:text-zinc-300 leading-tight">{{ $business->owner->name ?? "N/A" }}</span>
                                <span class="text-[11px] font-medium text-slate-400">{{ $business->owner->email ?? '' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-zinc-800/40 border border-slate-100 dark:border-white/5 w-fit">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-zinc-600"></span>
                            <span class="text-[11px] font-black uppercase tracking-widest text-slate-400 dark:text-zinc-500">{{ __('admin.no_owner_assigned') }}</span>
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="relative inline-block text-start w-[130px]">
                        <button type="button" onclick="toggleStatusMenu(event, 'status-menu-desktop-{{ $business->id }}')" class="w-full flex items-center justify-between gap-2 px-3 py-1.5 rounded-xl border transition-all duration-300 status-btn-[{{ $business->id }}] {{ 
                            $business->status === 'approved' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 hover:bg-emerald-500/20' : (
                            $business->status === 'pending' ? 'bg-amber-500/10 border-amber-500/20 text-amber-600 hover:bg-amber-500/20' : 
                            'bg-red-500/10 border-red-500/20 text-red-600 hover:bg-red-500/20')
                        }}">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ 
                                    $business->status === 'approved' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : (
                                    $business->status === 'pending' ? 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]' : 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]')
                                }}"></span>
                                <span class="text-[12px] font-[900] tracking-widest uppercase">{{ __('admin.' . $business->status) }}</span>
                            </div>
                            <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="status-menu-desktop-{{ $business->id }}" class="status-menu absolute z-50 mt-1 w-full bg-white dark:bg-[#1a1a1e] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-white/5 opacity-0 pointer-events-none transform -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                            <button type="button" onclick="updateBusinessStatus({{ $business->id }}, 'approved', this)" class="w-full flex items-center gap-2 px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300 text-start">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span class="text-[12px] font-bold">{{ __('admin.approved') }}</span>
                            </button>
                            <button type="button" onclick="updateBusinessStatus({{ $business->id }}, 'pending', this)" class="w-full flex items-center gap-2 px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors border-t border-slate-100 dark:border-white/5 text-slate-700 dark:text-zinc-300 text-start">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span><span class="text-[12px] font-bold">{{ __('admin.pending') }}</span>
                            </button>
                            <button type="button" onclick="openRejectModal({{ $business->id }})" class="w-full flex items-center gap-2 px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors border-t border-slate-100 dark:border-white/5 text-slate-700 dark:text-zinc-300 text-start">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span><span class="text-[12px] font-bold">{{ __('admin.rejected') }}</span>
                            </button>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-end">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openEditForm({{ $business->id }})" class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-xl transition-all tooltip" title="{{ __('admin.edit') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button onclick="promptDeleteBusiness({{ $business->id }})" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-xl transition-all tooltip" title="{{ __('admin.delete') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            @endforelse
        </tbody>
    </x-admin.table>
</div>
{{-- Mobile Cards (below md) --}}
<div class="md:hidden divide-y divide-slate-100 dark:divide-zinc-800/60">
    @forelse($businesses as $index => $business)
    <div class="mobile-card p-5 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-all business-row" data-id="{{ $business->id }}" style="--row-index: {{ $index }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4 text-start">
                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-700/50 flex items-center justify-center font-[900] text-slate-400 dark:text-zinc-500 shadow-sm shrink-0 overflow-hidden">
                    @if($business->logo)
                        <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center">
                            <span class="text-xl text-primary">{{ substr($business->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div>
                    <a href="{{ route('business.view', $business->slug) }}" target="_blank" class="text-[15px] font-[900] text-slate-900 dark:text-white leading-snug hover:text-primary flex items-center gap-1.5 transition-colors tooltip" title="{{ __('admin.view_business') }}">
                        {{ $business->name }}
                        <svg class="w-3.5 h-3.5 opacity-50 hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <p class="text-[13px] font-bold text-slate-500 dark:text-zinc-400 mt-0.5">{{ $business->category->name ?? "N/A" }}</p>
                    <div class="flex items-center gap-2 mt-1.5">
                        @if($business->owner_id)
                            <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-primary/5 border border-primary/10 text-[10px] font-bold text-primary">
                                <i class="fa-solid fa-user-check"></i>
                                {{ $business->owner->name }}
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-slate-50 dark:bg-zinc-800/40 border border-slate-100 dark:border-white/5">
                                <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-600"></span>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">{{ __('admin.no_owner_assigned') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5 mt-1 text-[11px] font-medium text-slate-400">
                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $business->city->name ?? "N/A" }}@if($business->city && $business->city->country), {{ $business->city->country->name }}@endif
                    </div>
                </div>
            </div>
            <div class="relative inline-block text-start w-[130px]">
                <button type="button" onclick="toggleStatusMenu(event, 'status-menu-mobile-{{ $business->id }}')" class="w-full flex items-center justify-between gap-2 px-3 py-1.5 rounded-xl border transition-all duration-300 status-btn-[{{ $business->id }}] {{ 
                    $business->status === 'approved' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 hover:bg-emerald-500/20' : (
                    $business->status === 'pending' ? 'bg-amber-500/10 border-amber-500/20 text-amber-600 hover:bg-amber-500/20' : 
                    'bg-red-500/10 border-red-500/20 text-red-600 hover:bg-red-500/20')
                }}">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full {{ 
                            $business->status === 'approved' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : (
                            $business->status === 'pending' ? 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]' : 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]')
                        }}"></span>
                        <span class="text-[12px] font-[900] tracking-widest uppercase">{{ __('admin.' . $business->status) }}</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="status-menu-mobile-{{ $business->id }}" class="status-menu absolute z-50 mt-1 w-full end-0 bg-white dark:bg-[#1a1a1e] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-white/5 opacity-0 pointer-events-none transform -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                    <button type="button" onclick="updateBusinessStatus({{ $business->id }}, 'approved', this)" class="w-full flex items-center gap-2 px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300 text-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span class="text-[12px] font-bold">{{ __('admin.approved') }}</span>
                    </button>
                    <button type="button" onclick="updateBusinessStatus({{ $business->id }}, 'pending', this)" class="w-full flex items-center gap-2 px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors border-t border-slate-100 dark:border-white/5 text-slate-700 dark:text-zinc-300 text-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span><span class="text-[12px] font-bold">{{ __('admin.pending') }}</span>
                    </button>
                    <button type="button" onclick="openRejectModal({{ $business->id }})" class="w-full flex items-center gap-2 px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors border-t border-slate-100 dark:border-white/5 text-slate-700 dark:text-zinc-300 text-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span><span class="text-[12px] font-bold">{{ __('admin.rejected') }}</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-5 pt-4 border-t border-slate-50 dark:border-white/[0.03]">
            <button onclick="openEditForm({{ $business->id }})" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 text-[13px] font-[900] bg-primary/5 text-primary rounded-xl hover:bg-primary/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                {{ __('admin.edit') }}
            </button>
            <button onclick="promptDeleteBusiness({{ $business->id }})" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 text-[13px] font-[900] bg-red-50/50 dark:bg-red-500/5 text-red-500 rounded-xl hover:bg-red-500/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ __('admin.delete') }}
            </button>
        </div>
    </div>
    @empty
    @endforelse
</div>
{{-- Empty State --}}
@forelse($businesses as $business)
@empty
<div class="px-6 py-20 text-center flex flex-col items-center justify-center relative overflow-hidden group/empty">
    <!-- Pulse Effect -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-50">
        <div class="w-40 h-40 bg-primary/5 rounded-full blur-3xl group-hover/empty:scale-150 transition-transform duration-1000"></div>
    </div>
    <div class="relative w-24 h-24 mb-6 group-hover/empty:scale-110 transition-transform duration-500">
        <div class="absolute inset-0 bg-slate-50 dark:bg-zinc-800/80 rounded-full scale-100 animate-[ping_3s_ease-in-out_infinite] opacity-20"></div>
        <div class="relative w-full h-full bg-slate-50/80 dark:bg-zinc-800 border border-slate-100 dark:border-white/5 rounded-full flex items-center justify-center text-slate-300 dark:text-zinc-600 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)]">
            <svg class="w-10 h-10 text-slate-400 dark:text-zinc-500 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
    </div>
    <div class="space-y-2 relative z-10">
        <h3 class="text-[18px] font-[900] text-slate-900 dark:text-white tracking-tight">{{ __('admin.no_results_found') }}</h3>
        <p class="text-[14px] font-medium text-slate-500 dark:text-zinc-400 max-w-[280px] mx-auto leading-relaxed">{{ __('admin.try_adjusting_filters') }}</p>
    </div>
</div>
@endforelse
{{-- Detailed Pagination --}}
<div class="px-6 py-4 bg-slate-50/10 dark:bg-zinc-800/5 border-t border-slate-100 dark:border-white/5" id="pagination-container">
    {{ $businesses->appends(request()->query())->links() }}
</div>
