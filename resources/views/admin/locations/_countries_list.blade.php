{{-- Desktop Table (md and up) --}}
<div class="hidden md:block overflow-x-auto">
    <x-admin.table>
        <x-slot name="header">
            <tr class="bg-slate-50/50 dark:bg-zinc-900/40 border-b border-slate-100 dark:border-zinc-800/60">
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('admin.name_en') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('admin.name_ar') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('admin.country_code') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('admin.cities_count') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('admin.status') }}</th>
                <th scope="col" class="px-6 py-5 text-end text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('admin.actions') }}</th>
            </tr>
        </x-slot>
        @forelse($countries as $index => $country)
        <tr class="hover:bg-slate-50/80 dark:hover:bg-zinc-800/30 transition-all group border-b border-slate-50 dark:border-zinc-800/20 last:border-0" style="--row-index: {{ $index }}">
            <td class="px-6 py-4 whitespace-nowrap text-[14px] font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                {{ $country->name_en }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-[14px] font-bold text-slate-900 dark:text-white font-cairo group-hover:text-primary transition-colors" dir="rtl">{{ $country->name_ar }}</td>
            <td class="px-6 py-4 whitespace-nowrap"><span class="px-3 py-1 bg-slate-100/80 dark:bg-zinc-800/80 rounded-lg text-slate-600 dark:text-zinc-300 font-mono text-[12px] font-bold">{{ $country->code }}</span></td>
            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-0.5 bg-primary/10 text-primary rounded text-xs font-[900]">{{ $country->cities_count ?? 0 }}</span></td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleStatusMenu(event, 'status-menu-country-desk-{{ $country->id }}', 'countries')" class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900 text-[12px] font-[900] shadow-sm hover:border-slate-300 dark:hover:border-zinc-600 transition-colors status-btn-country-[{{ $country->id }}]">
                        @if($country->status === 'active')
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div><span class="text-emerald-700 dark:text-emerald-400">{{ __('admin.active') }}</span>
                        @else
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div><span class="text-amber-700 dark:text-amber-400">{{ __('admin.pending') }}</span>
                        @endif
                        <svg class="w-3.5 h-3.5 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <!-- Dropdown panel -->
                    <div id="status-menu-country-desk-{{ $country->id }}" class="status-menu absolute z-50 mt-1 w-32 bg-white dark:bg-[#1a1a1e] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-white/5 opacity-0 pointer-events-none transform -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                        <button onclick="updateLocationStatus({{ $country->id }}, 'active', this, 'countries')" class="w-full flex items-center gap-2 px-4 py-2.5 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300">
                           <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>{{ __('admin.active') }}
                        </button>
                        <button onclick="updateLocationStatus({{ $country->id }}, 'pending', this, 'countries')" class="w-full flex items-center gap-2 px-4 py-2.5 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300 border-t border-slate-100 dark:border-white/5">
                           <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>{{ __('admin.pending') }}
                        </button>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-end">
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="confirmDelete('{{ route('admin.countries.destroy', $country) }}')" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-xl transition-all tooltip" aria-label="{{ __('admin.delete') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-6 py-16 text-center text-slate-500">{{ request()->filled('search') || request()->filled('status') ? __('admin.no_results_found') : __('admin.no_data') }}</td></tr>
        @endforelse
    </x-admin.table>
</div>
{{-- Mobile Cards (below md) --}}
<div class="md:hidden divide-y divide-slate-100 dark:divide-zinc-800/60">
    @forelse($countries as $index => $country)
    <div class="mobile-card p-5 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-all" style="--row-index: {{ $index }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="space-y-0.5">
                    <h3 class="text-[15px] font-[900] text-slate-900 dark:text-white leading-snug">{{ $country->name_en }}</h3>
                    <h3 class="text-[14px] font-bold text-slate-500 dark:text-zinc-400 font-cairo leading-snug">{{ $country->name_ar }}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-zinc-800 rounded text-[10px] font-mono text-slate-600 dark:text-zinc-400">{{ $country->code }}</span>
                    </div>
                </div>
            </div>
            <div class="relative inline-block text-left">
                <button type="button" onclick="toggleStatusMenu(event, 'status-menu-country-mobile-{{ $country->id }}', 'countries')" class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900 text-[12px] font-[900] shadow-sm hover:border-slate-300 dark:hover:border-zinc-600 transition-colors status-btn-country-[{{ $country->id }}]">
                    @if($country->status === 'active')
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div><span class="text-emerald-700 dark:text-emerald-400">{{ __('admin.active') }}</span>
                    @else
                        <div class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div><span class="text-amber-700 dark:text-amber-400">{{ __('admin.pending') }}</span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="status-menu-country-mobile-{{ $country->id }}" class="status-menu absolute z-50 mt-1 w-32 end-0 bg-white dark:bg-[#1a1a1e] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-white/5 opacity-0 pointer-events-none transform -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                    <button onclick="updateLocationStatus({{ $country->id }}, 'active', this, 'countries')" class="w-full flex items-center gap-2 px-4 py-2.5 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300">
                       <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>{{ __('admin.active') }}
                    </button>
                    <button onclick="updateLocationStatus({{ $country->id }}, 'pending', this, 'countries')" class="w-full flex items-center gap-2 px-4 py-2.5 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300 border-t border-slate-100 dark:border-white/5">
                       <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>{{ __('admin.pending') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-slate-50 dark:border-white/[0.03]">
            <button onclick="confirmDelete('{{ route('admin.countries.destroy', $country) }}')" class="flex-1 max-w-[100px] flex items-center justify-center gap-2 px-3 py-2 text-[13px] font-[900] bg-red-50/50 dark:bg-red-500/5 text-red-500 rounded-xl hover:bg-red-500/10 transition-all">
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
    </div>
    @endforelse
</div>
<div class="p-6 border-t border-slate-100 dark:border-zinc-800/60 bg-slate-50/30 dark:bg-zinc-900/20">
    {{ $countries->links() }}
</div>
