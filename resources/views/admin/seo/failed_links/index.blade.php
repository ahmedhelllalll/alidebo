@extends('admin.layouts.admin')

@section('title', __('admin.404_logger'))

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.404_logger') }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.404_logger_desc') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard.seo') }}" class="px-4 py-2.5 bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-sm">
                {{ __('admin.back_to_dashboard') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden shadow-sm dashboard-card-reveal">
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-start text-sm whitespace-nowrap">
                <thead class="uppercase tracking-wider border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-[11px] font-black text-slate-400 dark:text-zinc-500">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-start">{{ __('admin.url') }}</th>
                        <th scope="col" class="px-6 py-4 text-start">{{ __('admin.hits') }}</th>
                        <th scope="col" class="px-6 py-4 text-start">{{ __('admin.last_seen') }}</th>
                        <th scope="col" class="px-6 py-4 text-end">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    @forelse ($failedLinks as $link)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white truncate max-w-xs">{{ $link->url }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-zinc-400">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-zinc-300">
                                    {{ $link->hits }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-zinc-400">
                                {{ $link->last_seen ? $link->last_seen->diffForHumans() : '-' }}
                            </td>
                            <td class="px-6 py-4 text-end">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.seo.redirects.create', ['source_url' => $link->url]) }}" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary font-bold text-[12px] hover:bg-primary hover:text-white transition-colors">
                                        {{ __('admin.create_redirect') }}
                                    </a>
                                    <form action="{{ route('admin.seo.failed-links.destroy', $link) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors tooltip-trigger" data-tooltip="{{ __('admin.delete') }}">
                                            <i class="fa-solid fa-trash-can text-[13px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-zinc-500">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 dark:text-zinc-600">
                                    <i class="fa-solid fa-link-slash text-2xl"></i>
                                </div>
                                <p class="text-[13px]">{{ __('admin.no_data') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-slate-200 dark:divide-white/10">
            @forelse ($failedLinks as $link)
                <div class="p-4 space-y-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-zinc-300">
                            {{ __('admin.hits') }}: {{ $link->hits }}
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.seo.redirects.create', ['source_url' => $link->url]) }}" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary font-bold text-[12px] hover:bg-primary hover:text-white transition-colors">
                                {{ __('admin.create_redirect') }}
                            </a>
                            <form action="{{ route('admin.seo.failed-links.destroy', $link) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors tooltip-trigger" data-tooltip="{{ __('admin.delete') }}">
                                    <i class="fa-solid fa-trash-can text-[13px]"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-1">{{ __('admin.url') }}</p>
                            <p class="font-medium text-[13px] text-slate-900 dark:text-white break-all" dir="ltr">{{ $link->url }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-1">{{ __('admin.last_seen') }}</p>
                            <p class="font-medium text-[13px] text-slate-600 dark:text-zinc-400">{{ $link->last_seen ? $link->last_seen->diffForHumans() : '-' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 dark:text-zinc-500">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 dark:text-zinc-600">
                        <i class="fa-solid fa-link-slash text-2xl"></i>
                    </div>
                    <p class="text-[13px]">{{ __('admin.no_data') }}</p>
                </div>
            @endforelse
        </div>
        
        @if($failedLinks->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-white/10">
                {{ $failedLinks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
