<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
    @forelse($leads as $index => $lead)
    @php
        if($lead->status === 'read') {
            $cardBg = 'bg-emerald-50/70 dark:bg-emerald-900/20';
            $cardBorder = 'border-emerald-200/80 dark:border-emerald-500/20';
            $hoverShadow = 'hover:shadow-[0_20px_40px_-15px_rgba(16,185,129,0.15)] dark:hover:shadow-[0_20px_40px_-15px_rgba(16,185,129,0.3)]';
            $avatarGradient = 'from-emerald-500 to-emerald-400';
            $avatarShadow = 'shadow-emerald-500/30';
            $avatarText = 'text-emerald-600 dark:text-emerald-500';
            $avatarInnerBg = 'bg-emerald-500/5';
        } else {
            $cardBg = 'bg-rose-50/80 dark:bg-rose-900/20';
            $cardBorder = 'border-rose-200/80 dark:border-rose-500/30';
            $hoverShadow = 'hover:shadow-[0_20px_40px_-15px_rgba(244,63,94,0.15)] dark:hover:shadow-[0_20px_40px_-15px_rgba(244,63,94,0.3)]';
            $avatarGradient = 'from-primary to-orange-400';
            $avatarShadow = 'shadow-primary/30';
            $avatarText = 'text-primary';
            $avatarInnerBg = 'bg-primary/5';
        }
    @endphp
    <div class="group {{ $cardBg }} backdrop-blur-xl border {{ $cardBorder }} rounded-3xl p-5 {{ $hoverShadow }} hover:-translate-y-1.5 transition-all duration-500 relative overflow-visible flex flex-col" data-id="{{ $lead->id }}" style="--row-index: {{ $index }}">
        
        @if($lead->status === 'unread')
            {{-- Unread Indicator Ping --}}
            <div class="absolute -top-1.5 -end-1.5 w-4 h-4 z-10" title="{{ __('admin.unread') }}">
                <span class="absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75 animate-ping"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-gradient-to-tr from-orange-500 to-orange-400 border-2 border-white dark:border-zinc-900 shadow-sm"></span>
            </div>
        @endif

        {{-- Top Section: Avatar & Details --}}
        <div class="flex items-start gap-4 mb-5">
            <div class="relative shrink-0">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr {{ $avatarGradient }} p-[1.5px] shadow-lg {{ $avatarShadow }} group-hover:rotate-6 transition-transform duration-500">
                    <div class="w-full h-full bg-white dark:bg-zinc-900 rounded-[14px] flex items-center justify-center {{ $avatarText }} overflow-hidden relative">
                        <div class="absolute inset-0 {{ $avatarInnerBg }}"></div>
                        <i class="fa-solid fa-user text-xl group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                </div>
            </div>
            <div class="space-y-1.5 min-w-0 flex-1 pt-1">
                <h3 class="text-[16px] font-[900] text-slate-900 dark:text-white leading-tight truncate tracking-tight" title="{{ $lead->first_name }} {{ $lead->last_name }}">{{ $lead->first_name }} {{ $lead->last_name }}</h3>
                <div class="flex items-center gap-1.5 text-slate-500 dark:text-zinc-400">
                    <i class="fa-regular fa-envelope text-[11px] opacity-70"></i>
                    <h3 class="text-[13px] font-medium truncate" title="{{ $lead->email }}">{{ $lead->email }}</h3>
                </div>
            </div>
        </div>

        {{-- Badge Section (Dropdown) --}}
        <div class="mt-auto mb-5 relative inline-block text-start w-full">
            <button type="button" onclick="toggleStatusMenu(event, 'status-menu-{{ $lead->id }}')" class="flex items-center justify-between w-full gap-2 px-3.5 py-2.5 rounded-xl border border-slate-200/80 dark:border-zinc-700/60 bg-slate-50/50 dark:bg-zinc-800/40 text-[11px] font-[900] uppercase tracking-widest shadow-sm hover:border-slate-300 dark:hover:border-zinc-500 hover:bg-white dark:hover:bg-zinc-800 transition-all status-btn-[{{ $lead->id }}]" title="{{ __('admin.change_status') ?? 'Change Status' }}">
                <div class="flex items-center gap-2">
                    @if($lead->status === 'read')
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]"></div>
                        <span class="text-emerald-600 dark:text-emerald-400">{{ __('admin.read') }}</span>
                    @else
                        <div class="w-2 h-2 rounded-full bg-orange-500 animate-pulse shadow-[0_0_10px_rgba(249,115,22,0.4)]"></div>
                        <span class="text-orange-600 dark:text-orange-400">{{ __('admin.unread') }}</span>
                    @endif
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400 ms-1 shrink-0 group-hover:text-slate-600 dark:group-hover:text-slate-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </button>
            
            <div id="status-menu-{{ $lead->id }}" class="status-menu absolute z-[110] mt-2 w-36 start-0 bg-white/95 dark:bg-[#1a1a1e]/95 backdrop-blur-xl rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] border border-slate-200/80 dark:border-white/10 opacity-0 pointer-events-none overflow-hidden transition-all duration-200 p-1">
                <button onclick="updateLeadStatus({{ $lead->id }}, 'read', this)" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-start text-[11px] font-[900] uppercase tracking-widest hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors text-slate-700 dark:text-zinc-300 mb-0.5">
                   <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>{{ __('admin.read') }}
                </button>
                <button onclick="updateLeadStatus({{ $lead->id }}, 'unread', this)" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-start text-[11px] font-[900] uppercase tracking-widest hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors text-slate-700 dark:text-zinc-300">
                   <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>{{ __('admin.unread') }}
                </button>
            </div>
        </div>

        {{-- Divider --}}
        <div class="mb-5 h-px bg-gradient-to-r from-transparent via-slate-200 dark:via-zinc-700 to-transparent w-full"></div>

        {{-- Footer Section --}}
        <div class="flex items-center justify-between gap-3 mt-auto relative z-10">
            <span class="text-[11.5px] font-bold text-slate-400 dark:text-zinc-500 flex items-center gap-1.5" title="{{ __('admin.date') }}">
                <div class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-zinc-800 flex items-center justify-center">
                    <i class="fa-regular fa-clock text-[10px]"></i>
                </div>
                {{ $lead->created_at->format('M d, Y') }}
            </span>
            <div class="flex gap-2">
                <a href="{{ route('admin.leads.show', $lead->id) }}" class="flex items-center justify-center w-9 h-9 text-[13px] bg-primary/10 text-primary rounded-xl hover:bg-primary hover:text-white hover:scale-110 hover:-rotate-3 transition-all duration-300 shadow-sm" title="{{ __('admin.view_details') }}">
                    <i class="fa-regular fa-eye"></i>
                </a>
                <button onclick="confirmDelete('{{ route('admin.leads.destroy', $lead->id) }}')" class="flex items-center justify-center w-9 h-9 text-[13px] bg-red-50/80 dark:bg-red-500/10 text-red-500 rounded-xl hover:bg-red-500 hover:text-white hover:scale-110 hover:rotate-3 transition-all duration-300 shadow-sm" title="{{ __('admin.delete') }}">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full px-6 py-20 text-center">
        <div class="w-20 h-20 bg-slate-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center text-slate-300 dark:text-zinc-600 mx-auto mb-5 shadow-inner ring-4 ring-slate-100/50 dark:ring-zinc-800/20">
            <i class="fa-solid fa-inbox text-3xl"></i>
        </div>
        <p class="text-[16px] font-[900] text-slate-900 dark:text-white capitalize tracking-tight">{{ __('admin.no_leads_yet') ?? 'No leads yet' }}</p>
        <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400 mt-2">{{ __('admin.no_leads_desc') ?? 'When users contact you, their messages will appear here.' }}</p>
    </div>
    @endforelse
</div>

<div class="p-6 border-t border-slate-100 dark:border-zinc-800/60 bg-slate-50/30 dark:bg-zinc-900/20 mt-2 rounded-b-[24px]">
    {{ $leads->links() }}
</div>
