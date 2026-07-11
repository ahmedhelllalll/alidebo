@extends('users.layout')

@section('title', __('dashboard.index.leads') . ' | ' . __('nav.dashboard'))
@section('page_title', __('dashboard.index.leads'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-black/5 dark:border-white/[0.04]">
        <div>
            <h1 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">{{ __('dashboard.index.leads') }}</h1>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mt-1">{{ __('dashboard.index.leads_desc') }}</p>
        </div>
    </div>

    <div id="leads-container" x-data="leadsBoard()">
        @include('users.leads.partials.list', ['leads' => $leads])
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('leadsBoard', () => ({
            init() {
                // Poll for new leads every 10 seconds
                setInterval(() => {
                    this.fetchLeads();
                }, 10000);
            },
            fetchLeads() {
                fetch('{{ route("dashboard.leads.index") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('leads-container').innerHTML = html;
                })
                .catch(error => console.error('Error fetching leads:', error));
            }
        }));
    });
</script>
@endpush
@endsection
