@extends('admin.layouts.admin')

@section('title', __('admin.search_insights'))

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-4 lg:space-y-6 max-w-7xl mx-auto">
    <!-- Header Area -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal mb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[800] tracking-tight text-slate-900 dark:text-white">{{ __('admin.search_insights') }}</h1>
            <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">{{ __('admin.search_insights_desc') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard.seo') }}" class="px-4 py-2 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-lg text-sm font-semibold text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left rtl:fa-arrow-right mr-2 rtl:ml-2 rtl:mr-0"></i> {{ __('admin.back_to_seo') }}
            </a>
            
            <div class="relative" id="period-dropdown-container">
                <button type="button" onclick="document.getElementById('period-menu').classList.toggle('opacity-0'); document.getElementById('period-menu').classList.toggle('pointer-events-none'); document.getElementById('period-menu').classList.toggle('-translate-y-2');" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-lg text-sm font-semibold text-slate-700 dark:text-zinc-300 shadow-sm hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">
                    @if($period == '30')
                        {{ __('admin.last_30_days') }}
                    @elseif($period == '90')
                        {{ __('admin.last_3_months') }}
                    @else
                        {{ __('admin.last_7_days') }}
                    @endif
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div id="period-menu" class="absolute z-50 mt-1 end-0 w-48 bg-white dark:bg-zinc-900 rounded-lg shadow-lg border border-slate-200 dark:border-zinc-800 opacity-0 pointer-events-none transform -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                    <form action="{{ route('admin.dashboard.seo.search-insights') }}" method="GET" id="period-form">
                        <input type="hidden" name="period" id="period-input" value="{{ $period }}">
                        <button type="button" onclick="document.getElementById('period-input').value='7'; document.getElementById('period-form').submit();" class="w-full text-start px-4 py-2.5 text-sm font-semibold {{ $period == '7' ? 'text-blue-600 bg-blue-50 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800' }} transition-colors">
                            {{ __('admin.last_7_days') }}
                        </button>
                        <button type="button" onclick="document.getElementById('period-input').value='30'; document.getElementById('period-form').submit();" class="w-full text-start px-4 py-2.5 text-sm font-semibold {{ $period == '30' ? 'text-blue-600 bg-blue-50 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800' }} transition-colors border-t border-slate-100 dark:border-zinc-800">
                            {{ __('admin.last_30_days') }}
                        </button>
                        <button type="button" onclick="document.getElementById('period-input').value='90'; document.getElementById('period-form').submit();" class="w-full text-start px-4 py-2.5 text-sm font-semibold {{ $period == '90' ? 'text-blue-600 bg-blue-50 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800' }} transition-colors border-t border-slate-100 dark:border-zinc-800">
                            {{ __('admin.last_3_months') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    @php
        $totalClicks = collect($chartData['rows'] ?? [])->sum('clicks');
        $totalImpressions = collect($chartData['rows'] ?? [])->sum('impressions');
        $avgCtr = $totalImpressions > 0 ? ($totalClicks / $totalImpressions) * 100 : 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 dashboard-header-reveal">
        <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 rounded-xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-slate-500 dark:text-zinc-400">{{ __('admin.total_clicks') }}</p>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shadow-sm border border-blue-100 dark:border-blue-500/20">
                    <i class="fa-solid fa-hand-pointer"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($totalClicks) }}</h3>
            </div>
        </div>
        
        <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 rounded-xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-slate-500 dark:text-zinc-400">{{ __('admin.total_impressions') }}</p>
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shadow-sm border border-purple-100 dark:border-purple-500/20">
                    <i class="fa-solid fa-eye"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($totalImpressions) }}</h3>
            </div>
        </div>
        
        <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800/80 rounded-xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-slate-500 dark:text-zinc-400">{{ __('admin.ctr') }}</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shadow-sm border border-emerald-100 dark:border-emerald-500/20">
                    <i class="fa-solid fa-percent"></i>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ number_format($avgCtr, 2) }}%</h3>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
        <!-- Main Performance Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 shadow-sm border border-slate-200/80 dark:border-zinc-800/80 rounded-xl p-5 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('admin.search_performance') ?? 'Search Performance' }}</h2>
                </div>
                <div class="flex items-center gap-4 text-sm font-medium">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span><span class="text-slate-600 dark:text-zinc-400">{{ __('admin.clicks') }}</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-purple-500"></span><span class="text-slate-600 dark:text-zinc-400">{{ __('admin.impressions') }}</span></div>
                </div>
            </div>
            <div class="relative flex-1 min-h-[220px] w-full">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Device Breakdown Chart -->
        <div class="bg-white dark:bg-zinc-900 shadow-sm border border-slate-200/80 dark:border-zinc-800/80 rounded-xl p-5 flex flex-col relative">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('admin.device_breakdown') }}</h2>
            </div>
            <div class="relative flex-1 min-h-[160px] flex items-center justify-center">
                <canvas id="devicesChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalClicks) }}</span>
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">{{ __('admin.clicks') }}</span>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-0 border border-slate-100 dark:border-zinc-800 rounded-lg overflow-hidden">
                @forelse($deviceData['rows'] ?? [] as $index => $row)
                    @php $deviceKey = strtolower($row['keys'][0] ?? 'unknown'); @endphp
                    <div class="flex items-center justify-between p-2.5 {{ $index > 0 ? 'border-t border-slate-100 dark:border-zinc-800' : '' }} bg-slate-50/50 dark:bg-zinc-800/30">
                        <div class="flex items-center gap-3">
                            <div class="w-2.5 h-2.5 rounded-full {{ $deviceKey == 'mobile' ? 'bg-sky-500' : ($deviceKey == 'desktop' ? 'bg-indigo-500' : 'bg-teal-500') }}"></div>
                            <span class="text-slate-700 dark:text-zinc-300 font-semibold capitalize text-sm">{{ __('admin.device_' . $deviceKey) }}</span>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-white text-sm">{{ number_format($row['clicks']) }}</span>
                    </div>
                @empty
                    <div class="text-sm text-center text-slate-500 p-4">{{ __('admin.no_search_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Data Tables with Tabs Navigation -->
    <div class="flex flex-col mt-4">
        <!-- Sleek Tabs Navigation -->
        <div class="border-b border-slate-200/80 dark:border-zinc-800/80">
            <nav class="flex overflow-x-auto hide-scrollbar -mb-px gap-8" aria-label="Tabs">
                <button type="button" onclick="window.switchTab('keywords')" id="tab-keywords" class="whitespace-nowrap py-3 border-b-2 font-semibold text-sm transition-colors border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400">
                    {{ __('admin.top_performing_keywords') }}
                </button>
                <button type="button" onclick="window.switchTab('pages')" id="tab-pages" class="whitespace-nowrap py-3 border-b-2 font-semibold text-sm transition-colors border-transparent text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300 hover:border-slate-300 dark:hover:border-zinc-600">
                    {{ __('admin.top_performing_pages') }}
                </button>
                <button type="button" onclick="window.switchTab('countries')" id="tab-countries" class="whitespace-nowrap py-3 border-b-2 font-semibold text-sm transition-colors border-transparent text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300 hover:border-slate-300 dark:hover:border-zinc-600">
                    {{ __('admin.geographical_distribution') }}
                </button>
                <button type="button" onclick="window.switchTab('appearance')" id="tab-appearance" class="whitespace-nowrap py-3 border-b-2 font-semibold text-sm transition-colors border-transparent text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300 hover:border-slate-300 dark:hover:border-zinc-600">
                    {{ __('admin.search_appearance') }}
                </button>
                <button type="button" onclick="window.switchTab('sitemaps')" id="tab-sitemaps" class="whitespace-nowrap py-3 border-b-2 font-semibold text-sm transition-colors border-transparent text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300 hover:border-slate-300 dark:hover:border-zinc-600">
                    {{ __('admin.sitemaps_status') }}
                </button>
            </nav>
        </div>

        <!-- Tab Panels Container -->
        <div class="relative w-full pt-6">

    <!-- Keywords Table Section -->
    <div id="panel-keywords" class="bg-white dark:bg-zinc-900 shadow-sm border border-slate-200/80 dark:border-zinc-800/80 rounded-xl overflow-hidden block">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm whitespace-nowrap">
                <thead class="bg-slate-50/50 dark:bg-zinc-800/50 border-b border-slate-200/80 dark:border-zinc-800/80 text-slate-500 dark:text-zinc-400 text-xs font-semibold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">{{ __('admin.keyword_query') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.clicks') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.impressions') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.ctr') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.position') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-slate-700 dark:text-zinc-300 font-medium">
                    @forelse($topQueries as $row)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3 text-slate-900 dark:text-white font-semibold">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-500 text-xs shrink-0">
                                    <i class="fa-solid fa-search"></i>
                                </div>
                                {{ $row['keys'][0] ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['clicks']) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['impressions']) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['ctr'] * 100, 2) }}%</td>
                        <td class="px-4 py-3 text-right tabular-nums">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ number_format($row['position'], 1) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-zinc-500">
                            <p>{{ __('admin.no_search_data') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($topQueries->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            {{ $topQueries->links() }}
        </div>
        @endif
    </div>

    <!-- Pages Table Section -->
    <div id="panel-pages" class="bg-white dark:bg-zinc-900 shadow-sm border border-slate-200/80 dark:border-zinc-800/80 rounded-xl overflow-hidden hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm whitespace-nowrap">
                <thead class="bg-slate-50/50 dark:bg-zinc-800/50 border-b border-slate-200/80 dark:border-zinc-800/80 text-slate-500 dark:text-zinc-400 text-xs font-semibold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">{{ __('admin.page_url') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.clicks') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.impressions') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.ctr') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.position') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-slate-700 dark:text-zinc-300 font-medium">
                    @forelse($topPages as $row)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3 text-slate-900 dark:text-white font-semibold">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-500 text-xs shrink-0">
                                    <i class="fa-solid fa-link"></i>
                                </div>
                                <a href="{{ $row['keys'][0] ?? '#' }}" target="_blank" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center gap-2">
                                    <span class="max-w-[300px] truncate block" title="{{ $row['keys'][0] ?? '-' }}">
                                        {{ str_replace(env('APP_URL'), '', $row['keys'][0] ?? '-') }}
                                    </span>
                                </a>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['clicks']) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['impressions']) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['ctr'] * 100, 2) }}%</td>
                        <td class="px-4 py-3 text-right tabular-nums">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ number_format($row['position'], 1) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-zinc-500">
                            <p>{{ __('admin.no_search_data') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($topPages->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            {{ $topPages->links() }}
        </div>
        @endif
    </div>

    <!-- Countries Table Section -->
    <div id="panel-countries" class="bg-white dark:bg-zinc-900 shadow-sm border border-slate-200/80 dark:border-zinc-800/80 rounded-xl overflow-hidden hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm whitespace-nowrap">
                <thead class="bg-slate-50/50 dark:bg-zinc-800/50 border-b border-slate-200/80 dark:border-zinc-800/80 text-slate-500 dark:text-zinc-400 text-xs font-semibold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">{{ __('admin.country') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.clicks') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.impressions') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.ctr') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.position') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-slate-700 dark:text-zinc-300 font-medium">
                    @forelse($topCountries as $row)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3 text-slate-900 dark:text-white font-semibold">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-500 text-xs shrink-0 uppercase">
                                    {{ substr($row['keys'][0] ?? '-', 0, 2) }}
                                </div>
                                <span class="uppercase tracking-widest">{{ $row['keys'][0] ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['clicks']) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['impressions']) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['ctr'] * 100, 2) }}%</td>
                        <td class="px-4 py-3 text-right tabular-nums">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ number_format($row['position'], 1) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-zinc-500">
                            <p>{{ __('admin.no_search_data') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($topCountries->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            {{ $topCountries->links() }}
        </div>
        @endif
    </div>

    <!-- Search Appearance Table Section -->
    <div id="panel-appearance" class="bg-white dark:bg-zinc-900 shadow-sm border border-slate-200/80 dark:border-zinc-800/80 rounded-xl overflow-hidden hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm whitespace-nowrap">
                <thead class="bg-slate-50/50 dark:bg-zinc-800/50 border-b border-slate-200/80 dark:border-zinc-800/80 text-slate-500 dark:text-zinc-400 text-xs font-semibold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">{{ __('admin.appearance_type') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.clicks') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.impressions') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.ctr') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.position') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-slate-700 dark:text-zinc-300 font-medium">
                    @forelse($topAppearance as $row)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3 text-slate-900 dark:text-white font-semibold">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-800 dark:text-zinc-300 text-xs">
                                {{ ucwords(strtolower(str_replace('_', ' ', $row['keys'][0] ?? '-'))) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['clicks']) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['impressions']) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['ctr'] * 100, 2) }}%</td>
                        <td class="px-4 py-3 text-right tabular-nums">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ number_format($row['position'], 1) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-zinc-500">
                            <p>{{ __('admin.no_search_data') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($topAppearance->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            {{ $topAppearance->links() }}
        </div>
        @endif
    </div>

    <!-- Sitemaps Table Section -->
    <div id="panel-sitemaps" class="bg-white dark:bg-zinc-900 shadow-sm border border-slate-200/80 dark:border-zinc-800/80 rounded-xl overflow-hidden hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm whitespace-nowrap">
                <thead class="bg-slate-50/50 dark:bg-zinc-800/50 border-b border-slate-200/80 dark:border-zinc-800/80 text-slate-500 dark:text-zinc-400 text-xs font-semibold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">{{ __('admin.sitemap_path') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.last_downloaded') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.discovered_urls') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.indexed_urls') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.warnings') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('admin.errors') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-slate-700 dark:text-zinc-300 font-medium">
                    @forelse($sitemapsList as $sitemap)
                    @php
                        $submittedUrls = 0;
                        $indexedUrls = 0;
                        if (isset($sitemap['contents']) && is_array($sitemap['contents'])) {
                            foreach($sitemap['contents'] as $content) {
                                $submittedUrls += $content['submitted'] ?? 0;
                                $indexedUrls += $content['indexed'] ?? 0;
                            }
                        }
                        $isChild = !empty($sitemap['is_child']);
                    @endphp
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors {{ $isChild ? 'bg-slate-50/30 dark:bg-zinc-900/50' : '' }}">
                        <td class="px-4 py-3 text-slate-900 dark:text-white font-semibold">
                            <div class="flex items-center gap-3 {{ $isChild ? 'pl-8' : '' }}">
                                @if($isChild)
                                    <i class="fa-solid fa-arrow-turn-up rotate-90 text-slate-300 dark:text-zinc-600 text-[10px]"></i>
                                @else
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-500 text-xs shrink-0">
                                        <i class="fa-solid fa-sitemap"></i>
                                    </div>
                                @endif
                                <a href="{{ $sitemap['path'] ?? '#' }}" target="_blank" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center gap-2 {{ $isChild ? 'font-medium text-slate-600 dark:text-zinc-400 text-sm' : '' }}">
                                    <span class="max-w-[300px] truncate block" title="{{ $sitemap['path'] ?? '-' }}">
                                        {{ str_replace(env('APP_URL'), '', $sitemap['path'] ?? '-') }}
                                    </span>
                                </a>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums text-slate-500 dark:text-zinc-400">
                            {{ isset($sitemap['lastDownloaded']) ? \Carbon\Carbon::parse($sitemap['lastDownloaded'])->format('M d, Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums text-blue-600 dark:text-blue-400 font-bold">
                            {{ $submittedUrls > 0 ? number_format($submittedUrls) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums text-emerald-600 dark:text-emerald-400 font-bold">
                            {{ $indexedUrls > 0 ? number_format($indexedUrls) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if(empty($sitemap['warnings']) || $sitemap['warnings'] == 0)
                                <span class="text-slate-400 dark:text-zinc-600">-</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-500"><i class="fa-solid fa-triangle-exclamation"></i> {{ $sitemap['warnings'] }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if(empty($sitemap['errors']) || $sitemap['errors'] == 0)
                                <span class="text-slate-400 dark:text-zinc-600">-</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-500"><i class="fa-solid fa-circle-xmark"></i> {{ $sitemap['errors'] }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-zinc-500">
                            <p>{{ __('admin.no_search_data') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sitemapsList->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
            {{ $sitemapsList->links() }}
        </div>
        @endif
    </div>
    
        </div> <!-- End Tab Panels Container -->
    </div> <!-- End Data Tables with Tabs Navigation -->
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#a1a1aa' : '#64748b'; // zinc-400 / slate-500
    const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

    // Parse data from PHP
    const rawData = @json($chartData['rows'] ?? []);
    
    const dates = rawData.map(r => r.keys[0]);
    const clicks = rawData.map(r => r.clicks);
    const impressions = rawData.map(r => r.impressions);

    // Format dates for better x-axis reading
    const formattedDates = dates.map(dateStr => {
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });

    // Performance Chart
    const perfCtx = document.getElementById('performanceChart').getContext('2d');
    
    // Create Gradients
    const blueGradient = perfCtx.createLinearGradient(0, 0, 0, 300);
    blueGradient.addColorStop(0, isDarkMode ? 'rgba(59, 130, 246, 0.25)' : 'rgba(59, 130, 246, 0.15)');
    blueGradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

    const purpleGradient = perfCtx.createLinearGradient(0, 0, 0, 300);
    purpleGradient.addColorStop(0, isDarkMode ? 'rgba(168, 85, 247, 0.15)' : 'rgba(168, 85, 247, 0.05)');
    purpleGradient.addColorStop(1, 'rgba(168, 85, 247, 0)');

    new Chart(perfCtx, {
        type: 'line',
        data: {
            labels: formattedDates,
            datasets: [
                {
                    label: 'Clicks',
                    data: clicks,
                    borderColor: '#3b82f6', // blue-500
                    backgroundColor: blueGradient,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y',
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHitRadius: 10
                },
                {
                    label: 'Impressions',
                    data: impressions,
                    borderColor: '#a855f7', // purple-500
                    backgroundColor: purpleGradient,
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y1',
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#a855f7',
                    pointBorderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHitRadius: 10
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDarkMode ? 'rgba(24, 24, 27, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                    titleColor: isDarkMode ? '#f4f4f5' : '#0f172a',
                    bodyColor: isDarkMode ? '#d4d4d8' : '#334155',
                    borderColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    padding: 12,
                    boxPadding: 6,
                    usePointStyle: true,
                    titleFont: { size: 13, family: 'Inter', weight: '600' },
                    bodyFont: { size: 12, family: 'Inter', weight: '500' }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { 
                        color: textColor, 
                        font: { family: 'Inter', size: 11, weight: '500' },
                        maxTicksLimit: window.innerWidth < 768 ? 6 : 12,
                        maxRotation: 0
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: gridColor, drawBorder: false },
                    border: { display: false },
                    ticks: { color: textColor, font: { family: 'Inter', size: 11, weight: '500' }, beginAtZero: true, padding: 10 }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: textColor, font: { family: 'Inter', size: 11, weight: '500' }, beginAtZero: true, padding: 10 }
                }
            }
        }
    });

    // Devices Doughnut Chart
    const rawDeviceData = @json($deviceData['rows'] ?? []);
    if (rawDeviceData.length > 0) {
        const deviceLabels = rawDeviceData.map(r => r.keys[0].toLowerCase());
        const deviceClicks = rawDeviceData.map(r => r.clicks);
        const deviceColors = deviceLabels.map(label => {
            if (label === 'mobile') return '#0ea5e9'; // sky-500
            if (label === 'desktop') return '#6366f1'; // indigo-500
            return '#14b8a6'; // teal-500
        });

        new Chart(document.getElementById('devicesChart'), {
            type: 'doughnut',
            data: {
                labels: deviceLabels,
                datasets: [{
                    data: deviceClicks,
                    backgroundColor: deviceColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDarkMode ? 'rgba(24, 24, 27, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                        titleColor: isDarkMode ? '#f4f4f5' : '#0f172a',
                        bodyColor: isDarkMode ? '#d4d4d8' : '#334155',
                        borderColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.raw.toLocaleString() + ' Clicks';
                            }
                        }
                    }
                }
            }
        });
    }

    // Close period dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('period-dropdown-container');
        const menu = document.getElementById('period-menu');
        if (container && menu && !container.contains(event.target)) {
            menu.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        }
    });

    // Tab Switcher Logic
    const urlParams = new URLSearchParams(window.location.search);
    let activeTab = 'keywords';
    
    // Auto-switch based on pagination query params
    if (urlParams.has('pages_page')) activeTab = 'pages';
    else if (urlParams.has('country_page')) activeTab = 'countries';
    else if (urlParams.has('appearance_page')) activeTab = 'appearance';
    else if (urlParams.has('sitemaps_page')) activeTab = 'sitemaps';
    
    // Check localStorage fallback if no pagination param is present
    if (!window.location.search.includes('_page=') && localStorage.getItem('search_insights_tab')) {
        activeTab = localStorage.getItem('search_insights_tab');
    }

    // Expose switchTab to global window object
    window.switchTab = function(tabId, save = true) {
        if (save) localStorage.setItem('search_insights_tab', tabId);
        
        const allTabs = ['keywords', 'pages', 'countries', 'appearance', 'sitemaps'];
        
        allTabs.forEach(id => {
            const panel = document.getElementById('panel-' + id);
            const btn = document.getElementById('tab-' + id);
            
            if (!panel || !btn) return;

            if (id === tabId) {
                // Show panel
                panel.classList.remove('hidden');
                panel.classList.add('block');
                // Activate button
                btn.classList.add('border-blue-600', 'text-blue-600', 'dark:border-blue-500', 'dark:text-blue-400');
                btn.classList.remove('border-transparent', 'text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700', 'dark:hover:text-zinc-300', 'hover:border-slate-300', 'dark:hover:border-zinc-600');
            } else {
                // Hide panel
                panel.classList.add('hidden');
                panel.classList.remove('block');
                // Deactivate button
                btn.classList.remove('border-blue-600', 'text-blue-600', 'dark:border-blue-500', 'dark:text-blue-400');
                btn.classList.add('border-transparent', 'text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700', 'dark:hover:text-zinc-300', 'hover:border-slate-300', 'dark:hover:border-zinc-600');
            }
        });
    };

    // Initialize the correct tab
    window.switchTab(activeTab, false);
});
</script>
@endpush
