@if($leads->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 px-4 rounded-3xl border border-dashed border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ __('dashboard.index.no_leads') }}</h3>
    </div>
@else
    {{-- Leads Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($leads as $lead)
        <div class="bg-white dark:bg-[#0e0e11] rounded-[24px] p-6 shadow-sm border border-zinc-200/60 dark:border-white/5 flex flex-col transition-all hover:shadow-md">
            
            {{-- Header --}}
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1 min-w-0">
                    <h4 class="text-[15px] font-bold text-zinc-900 dark:text-white truncate" title="{{ $lead->name }}">{{ $lead->name }}</h4>
                    <span class="text-[11px] font-semibold text-zinc-400 mt-1 block">{{ $lead->created_at->diffForHumans() }}</span>
                </div>
                @php
                    $statusColors = [
                        'new' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 border-blue-100 dark:border-blue-800/30',
                        'contacted' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400 border-amber-100 dark:border-amber-800/30',
                        'converted' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800/30',
                        'lost' => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 border-red-100 dark:border-red-800/30',
                    ];
                    $statusColor = $statusColors[$lead->status] ?? $statusColors['new'];
                @endphp
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $statusColor }}">
                    {{ __('dashboard.index.status_'.$lead->status) }}
                </span>
            </div>

            {{-- Contact Info --}}
            <div class="space-y-2 mb-4">
                @if($lead->phone)
                <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <a href="tel:{{ $lead->phone }}" class="hover:text-primary transition-colors" dir="ltr">{{ $lead->phone }}</a>
                </div>
                @endif
                @if($lead->email)
                <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <a href="mailto:{{ $lead->email }}" class="hover:text-primary transition-colors truncate">{{ $lead->email }}</a>
                </div>
                @endif
            </div>

            {{-- Message --}}
            <div class="bg-zinc-50 dark:bg-zinc-900/50 p-3 rounded-xl border border-zinc-100 dark:border-zinc-800 mb-4 flex-1">
                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed line-clamp-3" title="{{ $lead->message }}">{{ $lead->message }}</p>
            </div>

            {{-- Actions --}}
            <div x-data="{ openEdit: false }" class="mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <button @click="openEdit = true" class="w-full text-center text-[13px] font-bold text-primary hover:text-primary-dark transition-colors">
                    {{ __('dashboard.index.update_lead') }}
                </button>

                {{-- Edit Modal --}}
                <div x-show="openEdit" x-cloak class="fixed inset-0 z-[200] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                        <div x-show="openEdit" x-transition.opacity class="fixed inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-opacity" @click="openEdit = false"></div>
                        
                        <div x-show="openEdit" x-transition.scale.origin.bottom class="relative inline-block w-full max-w-lg p-6 sm:p-8 overflow-hidden text-start align-middle transition-all transform bg-white dark:bg-[#0e0e11] shadow-2xl rounded-3xl border border-slate-100 dark:border-white/5">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">{{ __('dashboard.index.update_lead') }}</h3>
                            
                            <form action="{{ route('dashboard.leads.update', $lead->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                
                                {{-- Status Update --}}
                                <div>
                                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-2">Status</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach(['new', 'contacted', 'converted', 'lost'] as $status)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="status" value="{{ $status }}" class="peer sr-only" {{ $lead->status === $status ? 'checked' : '' }}>
                                            <div class="text-center px-3 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 peer-checked:bg-primary/10 peer-checked:text-primary peer-checked:border-primary/30 transition-all">
                                                {{ __('dashboard.index.status_'.$status) }}
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Private Notes --}}
                                <div>
                                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">{{ __('dashboard.index.private_notes') }}</label>
                                    <textarea name="notes" rows="4" placeholder="Write private notes..." class="w-full bg-slate-50 dark:bg-zinc-900/50 border border-slate-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:text-white outline-none resize-none">{{ $lead->notes }}</textarea>
                                </div>

                                <div class="flex gap-3 pt-4">
                                    <button type="button" @click="openEdit = false" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold bg-primary text-white hover:bg-primary-dark transition-colors">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @endforeach
    </div>
@endif
