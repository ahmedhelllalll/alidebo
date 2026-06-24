{{-- Desktop Table (md and up) --}}
<div class="hidden md:block overflow-x-auto">
    <x-admin.table>
        <x-slot name="header">
            <tr class="bg-slate-50/50 dark:bg-zinc-900/40 border-b border-slate-100 dark:border-zinc-800/60">
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider">{{ __('admin.icon') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider">{{ __('admin.name_en') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider">{{ __('admin.name_ar') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider">{{ __('admin.status') }}</th>
                <th scope="col" class="px-6 py-5 text-end text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider">{{ __('admin.actions') }}</th>
            </tr>
        </x-slot>
        @forelse($categories as $index => $category)
        <tr class="hover:bg-slate-50/80 dark:hover:bg-zinc-800/30 transition-all group border-b border-slate-50 dark:border-zinc-800/20 last:border-0" style="--row-index: {{ $index }}">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="w-12 h-12 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-700/50 flex items-center justify-center font-[900] text-slate-400 dark:text-zinc-500 shadow-sm overflow-hidden shrink-0 group-hover:border-primary/30 group-hover:text-primary transition-colors">
                    @if($category->image)
                        <img src="{{ str_starts_with($category->image, 'http') ? $category->image : asset('storage/' . $category->image) }}" class="w-full h-full object-cover">
                    @elseif($category->icon)
                       <i class="fa-solid {!! $category->icon !!}"></i> 
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    @endif
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-[14px] font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">{{ $category->name_en }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-[14px] font-bold text-slate-900 dark:text-white font-cairo group-hover:text-primary transition-colors" dir="rtl">{{ $category->name_ar }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleStatusMenu(event, 'status-menu-desktop-{{ $category->id }}')" class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900 text-[12px] font-[900] shadow-sm hover:border-slate-300 dark:hover:border-zinc-600 transition-colors status-btn-[{{ $category->id }}]">
                        @if($category->status === 'active')
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div><span class="text-emerald-700 dark:text-emerald-400">{{ __('admin.active') }}</span>
                        @else
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div><span class="text-amber-700 dark:text-amber-400">{{ __('admin.pending') }}</span>
                        @endif
                        <svg class="w-3.5 h-3.5 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <!-- Dropdown panel -->
                    <div id="status-menu-desktop-{{ $category->id }}" class="status-menu absolute z-50 mt-1 w-32 bg-white dark:bg-[#1a1a1e] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-white/5 opacity-0 pointer-events-none transform -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                        <button onclick="updateCategoryStatus({{ $category->id }}, 'active', this)" class="w-full flex items-center gap-2 px-4 py-2.5 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300">
                           <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>{{ __('admin.active') }}
                        </button>
                        <button onclick="updateCategoryStatus({{ $category->id }}, 'pending', this)" class="w-full flex items-center gap-2 px-4 py-2.5 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300 border-t border-slate-100 dark:border-white/5">
                           <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>{{ __('admin.pending') }}
                        </button>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-end">
                <div class="flex items-center justify-end gap-3">
                    <button onclick="editCategory({{ $category->toJson() }})" class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-xl transition-all tooltip" aria-label="{{ __('admin.edit') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    <button type="button" onclick="confirmDelete('{{ route('admin.categories.destroy', $category) }}')" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-xl transition-all tooltip" aria-label="{{ __('admin.delete') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-6 py-16 text-center text-slate-500">{{ request()->filled('search') || request()->filled('status') ? __('admin.no_results_found') : __('admin.no_data') }}</td></tr>
        @endforelse
    </x-admin.table>
</div>
{{-- Mobile Cards (below md) --}}
<div class="md:hidden divide-y divide-slate-100 dark:divide-zinc-800/60">
    @forelse($categories as $index => $category)
    <div class="mobile-card p-5 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-all" style="--row-index: {{ $index }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-700/50 flex items-center justify-center font-[900] text-slate-400 dark:text-zinc-500 shadow-sm shrink-0 overflow-hidden">
                    @if($category->image)
                        <img src="{{ str_starts_with($category->image, 'http') ? $category->image : asset('storage/' . $category->image) }}" class="w-full h-full object-cover">
                    @elseif($category->icon)
                       <i class="fa-solid {!! $category->icon !!} text-xl"></i> 
                    @else
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    @endif
                </div>
                <div class="space-y-0.5">
                    <h3 class="text-[15px] font-[900] text-slate-900 dark:text-white leading-snug">{{ $category->name_en }}</h3>
                    <h3 class="text-[14px] font-bold text-slate-500 dark:text-zinc-400 font-cairo leading-snug">{{ $category->name_ar }}</h3>
                </div>
            </div>
            <div class="relative inline-block text-left">
                <button type="button" onclick="toggleStatusMenu(event, 'status-menu-mobile-{{ $category->id }}')" class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900 text-[12px] font-[900] shadow-sm hover:border-slate-300 dark:hover:border-zinc-600 transition-colors status-btn-[{{ $category->id }}]">
                    @if($category->status === 'active')
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div><span class="text-emerald-700 dark:text-emerald-400">{{ __('admin.active') }}</span>
                    @else
                        <div class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div><span class="text-amber-700 dark:text-amber-400">{{ __('admin.pending') }}</span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="status-menu-mobile-{{ $category->id }}" class="status-menu absolute z-50 mt-1 w-32 end-0 bg-white dark:bg-[#1a1a1e] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-white/5 opacity-0 pointer-events-none transform -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                    <button onclick="updateCategoryStatus({{ $category->id }}, 'active', this)" class="w-full flex items-center gap-2 px-4 py-2.5 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300">
                       <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>{{ __('admin.active') }}
                    </button>
                    <button onclick="updateCategoryStatus({{ $category->id }}, 'pending', this)" class="w-full flex items-center gap-2 px-4 py-2.5 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300 border-t border-slate-100 dark:border-white/5">
                       <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>{{ __('admin.pending') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-slate-50 dark:border-white/[0.03]">
            <button onclick="editCategory({{ $category->toJson() }})" class="flex-1 max-w-[100px] flex items-center justify-center gap-2 px-3 py-2 text-[13px] font-[900] bg-slate-100/50 dark:bg-zinc-800/50 text-slate-600 dark:text-zinc-400 rounded-xl hover:bg-primary/10 hover:text-primary transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                {{ __('admin.edit') }}
            </button>
            <button onclick="confirmDelete('{{ route('admin.categories.destroy', $category) }}')" class="flex-1 max-w-[100px] flex items-center justify-center gap-2 px-3 py-2 text-[13px] font-[900] bg-red-50/50 dark:bg-red-500/5 text-red-500 rounded-xl hover:bg-red-500/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ __('admin.delete') }}
            </button>
        </div>
    </div>
    @empty
    <div class="px-6 py-16 text-center">
        <div class="w-16 h-16 bg-slate-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center text-slate-300 dark:text-zinc-600 mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <p class="text-[15px] font-[900] text-slate-900 dark:text-white">{{ request()->filled('search') || request()->filled('status') ? __('admin.no_results_found') : __('admin.no_data') }}</p>
        <p class="text-sm font-medium text-slate-500 mt-1">{{ __('admin.categories_empty_desc') }}</p>
    </div>
    @endforelse
</div>
<div class="p-6 border-t border-slate-100 dark:border-zinc-800/60 bg-slate-50/30 dark:bg-zinc-900/20">
    {{ $categories->links() }}
</div>
