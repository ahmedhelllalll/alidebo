@extends('admin.layouts.admin')

@section('title', __('admin.search_insights'))

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.search_insights') }}</h1>
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-500 mt-1 sm:mt-1.5">{{ __('admin.search_insights_desc') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard.seo') }}" class="px-4 py-2 bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-xl text-sm font-bold text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left rtl:fa-arrow-right mr-2 rtl:ml-2 rtl:mr-0"></i> {{ __('admin.back_to_seo') }}
            </a>
            
            <div class="relative" id="period-dropdown-container">
                <button type="button" onclick="document.getElementById('period-menu').classList.toggle('opacity-0'); document.getElementById('period-menu').classList.toggle('pointer-events-none'); document.getElementById('period-menu').classList.toggle('-translate-y-2');" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-xl text-sm font-bold text-slate-700 dark:text-zinc-300 shadow-sm hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    @if($period == '30')
                        {{ __('admin.last_30_days') }}
                    @elseif($period == '90')
                        {{ __('admin.last_3_months') }}
                    @else
                        {{ __('admin.last_7_days') }}
                    @endif
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div id="period-menu" class="absolute z-50 mt-1 end-0 w-48 bg-white dark:bg-[#1a1a1e] rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-white/5 opacity-0 pointer-events-none transform -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                    <form action="{{ route('admin.dashboard.seo.search-insights') }}" method="GET" id="period-form">
                        <input type="hidden" name="period" id="period-input" value="{{ $period }}">
                        <button type="button" onclick="document.getElementById('period-input').value='7'; document.getElementById('period-form').submit();" class="w-full text-start px-4 py-2.5 text-sm font-bold {{ $period == '7' ? 'text-primary bg-primary/5' : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }} transition-colors">
                            {{ __('admin.last_7_days') }}
                        </button>
                        <button type="button" onclick="document.getElementById('period-input').value='30'; document.getElementById('period-form').submit();" class="w-full text-start px-4 py-2.5 text-sm font-bold {{ $period == '30' ? 'text-primary bg-primary/5' : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }} transition-colors border-t border-slate-100 dark:border-white/5">
                            {{ __('admin.last_30_days') }}
                        </button>
                        <button type="button" onclick="document.getElementById('period-input').value='90'; document.getElementById('period-form').submit();" class="w-full text-start px-4 py-2.5 text-sm font-bold {{ $period == '90' ? 'text-primary bg-primary/5' : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/50' }} transition-colors border-t border-slate-100 dark:border-white/5">
                            {{ __('admin.last_3_months') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Clicks Chart -->
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center">
                    <i class="fa-solid fa-hand-pointer"></i>
                </div>
                {{ __('admin.total_clicks') }}
            </h2>
            <div class="relative h-72">
                <canvas id="clicksChart"></canvas>
            </div>
        </div>

        <!-- Impressions Chart -->
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-500 flex items-center justify-center">
                    <i class="fa-solid fa-eye"></i>
                </div>
                {{ __('admin.total_impressions') }}
            </h2>
            <div class="relative h-72">
                <canvas id="impressionsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Keywords Table Section -->
    <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl overflow-hidden dashboard-card-reveal">
        <div class="p-6 border-b border-slate-100 dark:border-white/5">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                </div>
                {{ __('admin.top_performing_keywords') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-white/5 uppercase text-slate-500 dark:text-zinc-400 text-xs font-black tracking-wider">
                    <tr>
                        <th class="px-6 py-4">{{ __('admin.keyword_query') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('admin.clicks') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('admin.impressions') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('admin.ctr') }}</th>
                        <th class="px-6 py-4 text-center">{{ __('admin.position') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-slate-700 dark:text-zinc-300 font-medium">
                    @forelse($topQueries['rows'] ?? [] as $row)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $row->keys[0] ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">{{ number_format($row->clicks) }}</td>
                        <td class="px-6 py-4 text-center">{{ number_format($row->impressions) }}</td>
                        <td class="px-6 py-4 text-center">{{ number_format($row->ctr * 100, 2) }}%</td>
                        <td class="px-6 py-4 text-center">{{ number_format($row->position, 1) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-zinc-500 font-medium">
                            <i class="fa-regular fa-folder-open text-4xl mb-3 opacity-50 block"></i>
                            {{ __('admin.no_search_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#a1a1aa' : '#64748b';
    const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.05)' : '#f1f5f9';

    // Parse data from PHP
    const rawData = @json($chartData['rows'] ?? []);
    
    const dates = rawData.map(r => r.keys[0]);
    const clicks = rawData.map(r => r.clicks);
    const impressions = rawData.map(r => r.impressions);

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                grid: { display: false, color: gridColor },
                ticks: { color: textColor, font: { family: 'Inter', size: 12 } }
            },
            y: {
                grid: { color: gridColor, borderDash: [5, 5] },
                ticks: { color: textColor, font: { family: 'Inter', size: 12 }, beginAtZero: true }
            }
        }
    };

    // Clicks Chart
    new Chart(document.getElementById('clicksChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Clicks',
                data: clicks,
                borderColor: '#3b82f6', // blue-500
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: commonOptions
    });

    // Impressions Chart
    new Chart(document.getElementById('impressionsChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Impressions',
                data: impressions,
                borderColor: '#a855f7', // purple-500
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#a855f7',
                pointBorderColor: '#fff',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: commonOptions
    });
    // Close period dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('period-dropdown-container');
        const menu = document.getElementById('period-menu');
        if (container && menu && !container.contains(event.target)) {
            menu.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        }
    });
});
</script>
@endpush
