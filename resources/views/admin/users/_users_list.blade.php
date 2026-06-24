{{-- Desktop Table (md and up) --}}
<div class="hidden md:block overflow-visible">
    <table class="min-w-full text-start text-sm text-slate-600 dark:text-zinc-400 border-separate border-spacing-0">
        <thead>
            <tr class="bg-slate-50/50 dark:bg-zinc-900/40 border-b border-slate-100 dark:border-zinc-800/60 sticky top-0 z-20">
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider border-b border-slate-100 dark:border-zinc-800/60">{{ __('admin.name') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider border-b border-slate-100 dark:border-zinc-800/60">{{ __('admin.email') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider border-b border-slate-100 dark:border-zinc-800/60">{{ __('admin.role') }}</th>
                <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider border-b border-slate-100 dark:border-zinc-800/60">{{ __('admin.created_at') }}</th>
                <th scope="col" class="px-6 py-5 text-end text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider border-b border-slate-100 dark:border-zinc-800/60">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/80">
        @forelse($users as $index => $user)
        <tr class="hover:bg-slate-50/80 dark:hover:bg-zinc-800/30 transition-all group border-b border-slate-50 dark:border-zinc-800/20 last:border-0" data-id="{{ $user->id }}" style="--row-index: {{ $index }}">
            <td class="px-6 py-4 whitespace-nowrap text-[14px] font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-[900] shrink-0 text-slate-500 dark:text-zinc-400 border border-slate-200/60 dark:border-zinc-700/50 shadow-sm transition-transform group-hover:scale-110">
                        @if($user->role === 'admin')
                            <i class="fa-solid fa-user-shield text-purple-500/70"></i>
                        @else
                            <i class="fa-solid fa-user"></i>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <span class="truncate max-w-[180px] leading-tight">{{ $user->name }}</span>
                        @if(auth()->id() === $user->id)
                            <span class="text-[10px] font-black text-primary uppercase ltr:tracking-widest mt-0.5">{{ __('admin.current_user') }}</span>
                        @endif
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-[14px] font-medium text-slate-500 dark:text-zinc-400">{{ $user->email }}</td>
            <td class="px-6 py-4 whitespace-nowrap relative">
                <div class="relative inline-block text-left">
                    <button type="button" onclick="toggleRoleMenu(event, 'role-menu-desk-{{ $user->id }}')" class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900 text-[12px] font-[900] shadow-sm hover:border-slate-300 dark:hover:border-zinc-600 transition-colors role-btn-[{{ $user->id }}] {{ auth()->id() === $user->id ? 'opacity-50 cursor-not-allowed' : '' }}" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                        @if($user->role === 'admin')
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]"></div><span class="text-purple-700 dark:text-purple-400">{{ __('admin.admin_role') }}</span>
                        @else
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-500 shadow-[0_0_8px_rgba(100,116,139,0.5)]"></div><span class="text-slate-700 dark:text-slate-400">{{ __('admin.user_role') }}</span>
                        @endif
                        @if(auth()->id() !== $user->id)
                        <svg class="w-3 h-3 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        @endif
                    </button>
                    @if(auth()->id() !== $user->id)
                    <!-- Dropdown panel -->
                    <div id="role-menu-desk-{{ $user->id }}" class="role-menu absolute z-[110] mt-2 w-36 end-0 bg-white dark:bg-[#1a1a1e] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] border border-slate-200 dark:border-white/5 opacity-0 pointer-events-none origin-top-right overflow-hidden transition-all duration-200">
                        <button onclick="updateUserRole({{ $user->id }}, 'admin', this)" class="w-full flex items-center gap-2 px-4 py-3 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300">
                           <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>{{ __('admin.admin_role') }}
                        </button>
                        <button onclick="updateUserRole({{ $user->id }}, 'user', this)" class="w-full flex items-center gap-2 px-4 py-3 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300 border-t border-slate-100 dark:border-white/5">
                           <div class="w-1.5 h-1.5 rounded-full bg-slate-500"></div>{{ __('admin.user_role') }}
                        </button>
                    </div>
                    @endif
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-[13px] font-medium text-slate-400">{{ $user->created_at->format('M d, Y') }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-end">
                <div class="flex items-center justify-end gap-2">
                    @if(auth()->id() !== $user->id)
                    <button type="button" onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}')" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-xl transition-all" title="{{ __('admin.delete') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    @else
                    <span class="text-[10px] text-primary font-black uppercase ltr:tracking-[0.2em] px-3 py-1 bg-primary/10 rounded-lg">{{ __('admin.owner_badge') }}</span>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-6 py-20 text-center"><p class="text-[14px] font-[900] text-slate-400 capitalize">{{ __('admin.no_results_found') }}</p></td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{-- Mobile Cards (below md) --}}
<div class="md:hidden divide-y divide-slate-100 dark:divide-zinc-800/60">
    @forelse($users as $index => $user)
    <div class="mobile-card p-5 hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-all" data-id="{{ $user->id }}" style="--row-index: {{ $index }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-700/50 flex items-center justify-center font-[900] text-slate-400 dark:text-zinc-500 shadow-sm shrink-0">
                    @if($user->role === 'admin')
                        <i class="fa-solid fa-user-shield text-purple-500/70 text-lg"></i>
                    @else
                        <i class="fa-solid fa-user text-lg"></i>
                    @endif
                </div>
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <h3 class="text-[15px] font-[900] text-slate-900 dark:text-white leading-snug">{{ $user->name }}</h3>
                        @if(auth()->id() === $user->id)
                            <span class="px-1.5 py-0.5 rounded-md bg-primary/10 text-primary text-[9px] font-black uppercase ltr:tracking-widest">{{ __('admin.current_user') }}</span>
                        @endif
                    </div>
                    <h3 class="text-[12px] font-medium text-slate-500 dark:text-zinc-400 break-all">{{ $user->email }}</h3>
                </div>
            </div>
            <div class="relative inline-block text-left">
                <button type="button" onclick="toggleRoleMenu(event, 'role-menu-mobile-{{ $user->id }}')" class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700/50 bg-white dark:bg-zinc-900 text-[12px] font-[900] shadow-sm hover:border-slate-300 dark:hover:border-zinc-600 transition-colors role-btn-[{{ $user->id }}] {{ auth()->id() === $user->id ? 'opacity-50 cursor-not-allowed' : '' }}" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                    @if($user->role === 'admin')
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]"></div><span class="text-purple-700 dark:text-purple-400">{{ __('admin.admin_role') }}</span>
                    @else
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-500 shadow-[0_0_8px_rgba(100,116,139,0.5)]"></div><span class="text-slate-700 dark:text-slate-400">{{ __('admin.user_role') }}</span>
                    @endif
                    @if(auth()->id() !== $user->id)
                    <svg class="w-3 h-3 text-slate-400 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    @endif
                </button>
                @if(auth()->id() !== $user->id)
                <div id="role-menu-mobile-{{ $user->id }}" class="role-menu absolute z-[110] mt-2 w-36 end-0 bg-white dark:bg-[#1a1a1e] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] border border-slate-200 dark:border-white/5 opacity-0 pointer-events-none origin-top-right overflow-hidden transition-all duration-200">
                    <button onclick="updateUserRole({{ $user->id }}, 'admin', this)" class="w-full flex items-center gap-2 px-4 py-3 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300">
                       <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>{{ __('admin.admin_role') }}
                    </button>
                    <button onclick="updateUserRole({{ $user->id }}, 'user', this)" class="w-full flex items-center gap-2 px-4 py-3 text-start text-[12px] font-[900] hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors text-slate-700 dark:text-zinc-300 border-t border-slate-100 dark:border-white/5">
                       <div class="w-1.5 h-1.5 rounded-full bg-slate-500"></div>{{ __('admin.user_role') }}
                    </button>
                </div>
                @endif
            </div>
        </div>
        <div class="flex items-center justify-between gap-3 mt-4 pt-4 border-t border-slate-50 dark:border-white/5">
            <span class="text-[11px] font-bold text-slate-400">{{ $user->created_at->format('M d, Y') }}</span>
            @if(auth()->id() !== $user->id)
            <button onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}')" class="flex items-center gap-2 px-3 py-2 text-[12px] font-[900] bg-red-50/50 dark:bg-red-500/5 text-red-500 rounded-xl hover:bg-red-500/10 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                {{ __('admin.delete') }}
            </button>
            @else
            <span class="text-[10px] text-primary font-black uppercase ltr:tracking-widest px-2 opacity-60">{{ __('admin.admin_badge') }}</span>
            @endif
        </div>
    </div>
    @empty
    <div class="px-6 py-20 text-center">
        <div class="w-16 h-16 bg-slate-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center text-slate-300 dark:text-zinc-600 mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <p class="text-[15px] font-[900] text-slate-900 dark:text-white capitalize">{{ __('admin.no_results_found') }}</p>
    </div>
    @endforelse
</div>
<div class="p-6 border-t border-slate-100 dark:border-zinc-800/60 bg-slate-50/30 dark:bg-zinc-900/20">
    {{ $users->links() }}
</div>
