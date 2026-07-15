@extends('layouts.app')

@section('title', __('landing.title') ?? 'The Ultimate Business Directory')
@section('meta_description', __('landing.meta_description') ?? 'Find the perfect business or build your empire. The ultimate directory for clients and companies.')

@section('content')
    @if(session('onboarding_company'))
        <div id="welcome-toast" style="z-index: 2147483647;" class="hero-font fixed bottom-4 left-4 right-4 sm:bottom-6 sm:left-auto sm:right-6 rtl:sm:right-auto rtl:sm:left-6 w-auto sm:w-full sm:max-w-sm bg-white/95 dark:bg-[#09090b]/95 backdrop-blur-xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.3)] dark:shadow-[0_20px_50px_-12px_rgba(0,0,0,0.7)] rounded-[1.25rem] p-5 sm:p-6 border border-gray-200/50 dark:border-white/10 transform transition-all duration-700 translate-y-0 opacity-100 flex flex-col gap-4">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 mt-0.5">
                    <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 border border-primary/20 shadow-inner">
                        <svg class="w-6 h-6 text-primary drop-shadow-sm" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09l2.846.813-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1 w-0">
                    <div class="flex items-start justify-between">
                        <h3 class="text-lg font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('landing.welcome_toast_title', ['company' => session('onboarding_company')]) }}</h3>
                        <button onclick="document.getElementById('welcome-toast').style.display='none'" aria-label="{{ __('landing.welcome_toast_dismiss') }}" class="ms-3 flex-shrink-0 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors bg-transparent border-0 rounded-lg p-1">
                            <span class="sr-only">{{ __('landing.welcome_toast_dismiss') }}</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-300 leading-relaxed rtl:leading-loose">
                        {{ __('landing.welcome_toast_desc') }}
                    </p>
                </div>
            </div>
            
            <div class="mt-2 w-full">
                <a href="{{ route('google.login') }}" class="group relative flex w-full items-center rounded-xl bg-primary text-white hover:bg-primary-light font-bold text-[15px] shadow-md shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-300 overflow-hidden border border-primary/20">
                    <!-- Premium Shine Effect -->
                    <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out skew-x-12 z-0"></div>
                    
                    <!-- Perfectly Centered Text -->
                    <div class="relative z-10 w-full text-center py-3.5 px-4 truncate">
                        {{ __('landing.welcome_toast_btn') }}
                    </div>
                </a>
            </div>
        </div>
        
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    if (url.searchParams.has('company')) {
                        url.searchParams.delete('company');
                        window.history.replaceState({path: url.href}, '', url.href);
                    }
                }
            });
        </script>
    @endif

    @include('landing.companies.hero-search')
    @include('landing.companies.featured')
    @include('landing.companies.cta-ads')
@endsection
