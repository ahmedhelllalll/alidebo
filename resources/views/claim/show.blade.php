@extends('layouts.app')

@section('title', __('admin.claim_business') ?? 'Claim Business: ' . $business->name)

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-[#09090b] relative overflow-hidden">
    
    <!-- Background Accents -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary/10 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative w-full max-w-2xl bg-white dark:bg-[#121214] rounded-[2rem] border border-slate-200/60 dark:border-white/[0.05] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] overflow-hidden">
        
        <!-- Top Banner -->
        <div class="h-32 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-zinc-800 dark:to-zinc-900 relative">
            @if($business->cover)
                <img src="{{ $business->cover_url }}" alt="Cover" class="w-full h-full object-cover opacity-80 mix-blend-overlay">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-white dark:from-[#121214] to-transparent"></div>
        </div>

        <div class="px-8 pb-10 sm:px-12 sm:pb-14 relative z-10 -mt-16 text-center">
            
            <!-- Logo -->
            <div class="w-32 h-32 mx-auto rounded-[1.5rem] bg-white dark:bg-[#1a1a1e] border-4 border-white dark:border-[#121214] shadow-xl flex items-center justify-center overflow-hidden mb-6">
                @if($business->logo)
                    <img src="{{ $business->logo_url }}" alt="{{ $business->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-4xl font-[900] text-primary">{{ substr($business->name, 0, 1) }}</span>
                @endif
            </div>

            <!-- Content -->
            <h1 class="text-3xl sm:text-4xl font-[900] text-slate-900 dark:text-white tracking-tight mb-3">
                {{ __('Claim Ownership of') }} <span class="text-primary">{{ $business->name }}</span>
            </h1>
            
            <p class="text-slate-500 dark:text-zinc-400 text-[15px] max-w-md mx-auto mb-10 leading-relaxed">
                {{ __('You have been invited to claim and manage this business profile. By claiming it, you will have full control over the information, reviews, and leads.') }}
            </p>

            <form action="{{ route('business.claim.process', ['locale' => app()->getLocale(), 'token' => $token]) }}" method="POST">
                @csrf
                <button type="submit" class="group relative w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 bg-primary text-white rounded-2xl font-[900] text-[16px] overflow-hidden transition-all duration-300 hover:scale-[1.02] active:scale-[0.98] shadow-[0_10px_30px_rgba(244,80,24,0.3)] hover:shadow-[0_15px_40px_rgba(244,80,24,0.4)]">
                    <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                    <span>{{ __('Claim Business Profile Now') }}</span>
                    <svg class="w-5 h-5 shrink-0 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <div class="mt-8 flex items-center justify-center gap-2 text-[13px] font-medium text-slate-400 dark:text-zinc-500">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                {{ __('Secure and verified process') }}
            </div>
        </div>
    </div>
</div>
@endsection
