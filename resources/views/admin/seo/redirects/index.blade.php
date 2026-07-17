@extends('admin.layouts.admin')

@section('title', __('admin.redirects_manager'))

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.redirects_manager') }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.redirects_desc') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard.seo') }}" class="px-4 py-2.5 bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-bold text-[13px] hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-sm">
                {{ __('admin.back_to_dashboard') }}
            </a>
            <a href="{{ route('admin.seo.redirects.create') }}" class="px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                {{ __('admin.add_new') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden shadow-sm dashboard-card-reveal">
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-start text-sm whitespace-nowrap">
                <thead class="uppercase tracking-wider border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-[11px] font-black text-slate-400 dark:text-zinc-500">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-start">{{ __('admin.source_url') }}</th>
                        <th scope="col" class="px-6 py-4 text-start">{{ __('admin.target_url') }}</th>
                        <th scope="col" class="px-6 py-4 text-start">{{ __('admin.status_code') }}</th>
                        <th scope="col" class="px-6 py-4 text-end">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    @forelse ($redirects as $redirect)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $redirect->source_url }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-zinc-400">{{ $redirect->target_url }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $redirect->status_code == 301 ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400' }}">
                                    {{ $redirect->status_code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-end">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.seo.redirects.edit', $redirect) }}" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-zinc-400 flex items-center justify-center hover:bg-primary hover:text-white dark:hover:bg-primary dark:hover:text-white transition-colors tooltip-trigger" data-tooltip="{{ __('admin.edit') }}">
                                        <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                    </a>
                                    <form action="{{ route('admin.seo.redirects.destroy', $redirect) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}');">
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
                                    <i class="fa-solid fa-route text-2xl"></i>
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
            @forelse ($redirects as $redirect)
                <div class="p-4 space-y-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $redirect->status_code == 301 ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' : 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400' }}">
                            {{ $redirect->status_code }}
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.seo.redirects.edit', $redirect) }}" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-zinc-400 flex items-center justify-center hover:bg-primary hover:text-white dark:hover:bg-primary dark:hover:text-white transition-colors tooltip-trigger" data-tooltip="{{ __('admin.edit') }}">
                                <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                            </a>
                            <form action="{{ route('admin.seo.redirects.destroy', $redirect) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}');">
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
                            <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-1">{{ __('admin.source_url') }}</p>
                            <p class="font-medium text-[13px] text-slate-900 dark:text-white break-all" dir="ltr">{{ $redirect->source_url }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-1">{{ __('admin.target_url') }}</p>
                            <p class="font-medium text-[13px] text-slate-600 dark:text-zinc-400 break-all" dir="ltr">{{ $redirect->target_url }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 dark:text-zinc-500">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 dark:text-zinc-600">
                        <i class="fa-solid fa-route text-2xl"></i>
                    </div>
                    <p class="text-[13px]">{{ __('admin.no_data') }}</p>
                </div>
            @endforelse
        </div>
        
        @if($redirects->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-white/10">
                {{ $redirects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
