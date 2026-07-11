@extends('admin.layouts.admin')
@section('title', __('admin.support_chats'))
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight ltr:bg-gradient-to-r rtl:bg-gradient-to-l from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.support_chats') }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.support_chats_desc') }} (<span id="total-count-header">{{ $sessions->total() }}</span>)</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.location.reload()" class="p-2.5 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-white/10 text-slate-500 hover:text-primary rounded-xl shadow-sm hover:shadow-md transition-all active:scale-[0.98] group" title="{{ __('admin.refresh') }}">
                <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
    </div>

    {{-- Stats Mini --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        @php
            $openCount = $sessions->getCollection()->where('status', 'open')->count();
            $closedCount = $sessions->getCollection()->where('status', 'closed')->count();
            $totalCount = $sessions->total();
        @endphp
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 p-4 group hover:shadow-[0_15px_40px_rgba(244,80,24,0.08)] hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute -top-12 -end-12 w-28 h-28 bg-primary/[0.03] rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <i class="fa-solid fa-comments text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider">{{ __('admin.total') }}</p>
                    <h3 class="text-xl font-[900] text-slate-900 dark:text-white">{{ $totalCount }}</h3>
                </div>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 p-4 group hover:shadow-[0_15px_40px_rgba(16,185,129,0.08)] hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute -top-12 -end-12 w-28 h-28 bg-emerald-500/[0.03] rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider">{{ __('admin.chat_open') }}</p>
                    <h3 class="text-xl font-[900] text-slate-900 dark:text-white">{{ $openCount }}</h3>
                </div>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#18181b] border border-slate-200/80 dark:border-zinc-800/80 p-4 group hover:shadow-[0_15px_40px_rgba(100,116,139,0.08)] hover:-translate-y-0.5 transition-all duration-300 col-span-2 sm:col-span-1">
            <div class="absolute -top-12 -end-12 w-28 h-28 bg-slate-500/[0.03] rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-500/10 flex items-center justify-center text-slate-500 dark:text-zinc-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-500 dark:text-zinc-500 uppercase ltr:tracking-wider">{{ __('admin.chat_closed') }}</p>
                    <h3 class="text-xl font-[900] text-slate-900 dark:text-white">{{ $closedCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Chat Sessions List --}}
    <div class="list-card bg-white/90 dark:bg-[#121214]/85 backdrop-blur-md rounded-[24px] border border-white/60 dark:border-white/[0.05] shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative z-10 w-full">
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-zinc-800/60">
                        <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider text-start">{{ __('admin.user') }}</th>
                        <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider text-start">{{ __('admin.status') }}</th>
                        <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider text-start">{{ __('admin.chat_last_message') }}</th>
                        <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider text-start">{{ __('admin.date') }}</th>
                        <th scope="col" class="px-6 py-5 text-[13px] font-[900] text-slate-500 dark:text-zinc-400 uppercase ltr:tracking-wider text-end">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/60">
                    @forelse($sessions as $session)
                    @php
                        $unreadCount = $session->messages->where('sender_type', 'user')->where('is_read', false)->count();
                        $lastMessage = $session->messages->sortByDesc('created_at')->first();
                    @endphp
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-primary/10 to-orange-500/5 border border-primary/10 flex items-center justify-center text-primary font-[900] text-sm">
                                        {{ strtoupper(substr($session->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    @if($session->status === 'open')
                                    <div class="absolute -bottom-0.5 -end-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white dark:border-[#121214]"></div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[14px] font-[900] text-slate-900 dark:text-white truncate">{{ $session->user->name ?? __('admin.unknown_user') }}</p>
                                    <p class="text-[12px] font-medium text-slate-400 dark:text-zinc-500 truncate">{{ $session->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-[12px] font-[900]
                                {{ $session->status === 'open'
                                    ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                    : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ $session->status === 'open' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-400 dark:bg-zinc-500' }}"></div>
                                {{ $session->status === 'open' ? __('admin.chat_open') : __('admin.chat_closed') }}
                            </div>
                            @if($unreadCount > 0)
                            <span class="ms-2 inline-flex items-center justify-center min-w-[20px] h-[20px] px-1 text-[10px] font-black bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded-lg border border-orange-500/20">{{ $unreadCount }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($lastMessage)
                            <p class="text-[13px] font-medium text-slate-600 dark:text-zinc-300 truncate max-w-[200px]">
                                @if($lastMessage->sender_type === 'admin')
                                    <span class="text-primary font-bold">{{ __('admin.you') }}:</span>
                                @endif
                                {{ Str::limit($lastMessage->message, 40) }}
                            </p>
                            @else
                            <p class="text-[13px] text-slate-400 dark:text-zinc-500 italic">{{ __('admin.no_messages_yet') }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400">{{ $session->updated_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.support-chats.show', $session) }}"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-primary/[0.07] hover:bg-primary text-primary hover:text-white rounded-xl text-[12px] font-[900] transition-all duration-300 active:scale-[0.96] shadow-sm hover:shadow-md hover:shadow-primary/20">
                                    <i class="fa-solid fa-arrow-right rtl:fa-arrow-left text-[11px]"></i>
                                    {{ __('admin.view_chat') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-zinc-800/50 flex items-center justify-center mb-5 shadow-inner">
                                    <i class="fa-solid fa-comments text-2xl text-slate-300 dark:text-zinc-600"></i>
                                </div>
                                <p class="text-[15px] font-[900] text-slate-900 dark:text-white capitalize">{{ __('admin.no_chats_yet') }}</p>
                                <p class="text-[13px] font-medium text-slate-400 dark:text-zinc-500 mt-1.5 max-w-sm">{{ __('admin.no_chats_desc') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-slate-100 dark:divide-zinc-800/60">
            @forelse($sessions as $session)
            @php
                $unreadCount = $session->messages->where('sender_type', 'user')->where('is_read', false)->count();
                $lastMessage = $session->messages->sortByDesc('created_at')->first();
            @endphp
            <div class="p-4 group">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="relative flex-shrink-0">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary/10 to-orange-500/5 border border-primary/10 flex items-center justify-center text-primary font-[900] text-sm">
                                {{ strtoupper(substr($session->user->name ?? '?', 0, 1)) }}
                            </div>
                            @if($session->status === 'open')
                            <div class="absolute -bottom-0.5 -end-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white dark:border-[#121214]"></div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-[14px] font-[900] text-slate-900 dark:text-white truncate">{{ $session->user->name ?? __('admin.unknown_user') }}</p>
                                @if($unreadCount > 0)
                                <span class="flex-shrink-0 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[9px] font-black bg-orange-500 text-white rounded-full">{{ $unreadCount }}</span>
                                @endif
                            </div>
                            <p class="text-[12px] font-medium text-slate-400 dark:text-zinc-500 truncate">{{ $session->user->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-[900] flex-shrink-0
                        {{ $session->status === 'open'
                            ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                            : 'bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400' }}">
                        <div class="w-1.5 h-1.5 rounded-full {{ $session->status === 'open' ? 'bg-emerald-500' : 'bg-slate-400' }}"></div>
                        {{ $session->status === 'open' ? __('admin.chat_open') : __('admin.chat_closed') }}
                    </div>
                </div>
                @if($lastMessage)
                <p class="text-[13px] font-medium text-slate-500 dark:text-zinc-400 truncate mt-2.5 ms-14">
                    @if($lastMessage->sender_type === 'admin')
                        <span class="text-primary font-bold">{{ __('admin.you') }}:</span>
                    @endif
                    {{ Str::limit($lastMessage->message, 50) }}
                </p>
                @endif
                <div class="flex items-center justify-between mt-3 ms-14">
                    <p class="text-[11px] font-medium text-slate-400 dark:text-zinc-500">{{ $session->updated_at->diffForHumans() }}</p>
                    <a href="{{ route('admin.support-chats.show', $session) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/[0.07] hover:bg-primary text-primary hover:text-white rounded-lg text-[11px] font-[900] transition-all">
                        {{ __('admin.view_chat') }}
                        <i class="fa-solid fa-arrow-right rtl:fa-arrow-left text-[9px]"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-zinc-800/50 flex items-center justify-center mb-4 mx-auto shadow-inner">
                    <i class="fa-solid fa-comments text-xl text-slate-300 dark:text-zinc-600"></i>
                </div>
                <p class="text-[14px] font-[900] text-slate-900 dark:text-white">{{ __('admin.no_chats_yet') }}</p>
                <p class="text-[12px] font-medium text-slate-400 dark:text-zinc-500 mt-1 max-w-xs mx-auto">{{ __('admin.no_chats_desc') }}</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($sessions->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-zinc-800/60">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
