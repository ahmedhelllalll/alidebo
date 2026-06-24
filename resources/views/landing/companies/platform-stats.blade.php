<section id="growth" class="py-24 relative bg-white dark:bg-[#09090b] scroll-mt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="reveal text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-[900] tracking-tight text-slate-900 dark:text-white mb-4">
                {{ __('landing.stats_title') ?? 'Platform Growth & Reach' }}
            </h2>
            <p class="text-slate-600 dark:text-zinc-400 font-medium max-w-2xl mx-auto text-lg">
                {{ __('landing.stats_desc') ?? 'Join thousands of businesses already leveraging our platform to expand their global footprint.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Stats Summary -->
            <div class="lg:col-span-1 flex flex-col gap-6">
                <div class="reveal luxury-card p-8 bg-slate-50 dark:bg-zinc-900/50">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="text-4xl font-[900] text-slate-900 dark:text-white mb-2">15,000+</div>
                    <div class="text-sm font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Active Businesses</div>
                </div>
                <div class="reveal luxury-card p-8 bg-slate-50 dark:bg-zinc-900/50">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 dark:bg-blue-500/10 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </div>
                    <div class="text-4xl font-[900] text-slate-900 dark:text-white mb-2">120+</div>
                    <div class="text-sm font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-widest">Countries Reached</div>
                </div>
            </div>

            <!-- Chart -->
            <div class="lg:col-span-2 reveal luxury-card p-6 sm:p-8 bg-white dark:bg-zinc-950 flex flex-col relative overflow-hidden">
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Monthly Engagement</h3>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 font-medium">User visits and profile interactions.</p>
                    </div>
                    <span class="px-3 py-1 bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400 text-xs font-bold rounded-full flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        +24.5%
                    </span>
                </div>
                <div class="relative flex-1 w-full min-h-[300px]">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('growthChart');
        if (!ctx) return;

        // Ensure Chart is loaded
        if (typeof Chart === 'undefined') return;

        // Colors based on theme
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#a1a1aa' : '#64748b'; // zinc-400 : slate-500
        const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
        
        // Primary brand color
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(244, 80, 24, 0.4)');
        gradient.addColorStop(1, 'rgba(244, 80, 24, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Interactions',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 35000, 32000, 45000, 48000, 52000, 65000],
                    borderColor: '#f45018',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f45018',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // smooth curves
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#18181b' : '#fff',
                        titleColor: isDark ? '#fff' : '#0f172a',
                        bodyColor: isDark ? '#a1a1aa' : '#64748b',
                        borderColor: isDark ? '#27272a' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString() + ' visits';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: textColor, font: { family: 'inherit', size: 12 } }
                    },
                    y: {
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { 
                            color: textColor, 
                            font: { family: 'inherit', size: 12 },
                            callback: function(value) { return value >= 1000 ? value/1000 + 'k' : value; }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    });
</script>
@endpush
