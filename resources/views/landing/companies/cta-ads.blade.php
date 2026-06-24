<section class="pt-12 sm:pt-16 pb-24 sm:pb-32 relative bg-slate-50 dark:bg-[#09090b] overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="reveal relative bg-white dark:bg-[#0a0a0c] rounded-[2rem] lg:rounded-[3rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] flex flex-col lg:flex-row items-center justify-between">
            
            {{-- Text Content (Left) --}}
            <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-20 relative z-10 flex flex-col items-center lg:items-start text-center lg:text-start">
                <span class="inline-block py-1.5 px-4 rounded-full bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase mb-6 border border-primary/20">
                    {{ __('landing.cta_badge') ?? 'Grow With Us' }}
                </span>
                
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-[900] tracking-tight text-slate-900 dark:text-white leading-tight mb-6">
                    {{ __('landing.cta_ready') ?? 'Give your business the visibility it deserves' }}
                </h2>
                
                <p class="text-lg text-slate-600 dark:text-zinc-400 font-medium leading-relaxed mb-10 max-w-lg">
                    {{ __('landing.cta_desc') ?? 'Join our exclusive network today. Create a professional profile to showcase your services, build trust, and seamlessly connect with clients actively looking for what you offer.' }}
                </p>
                
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-4 rounded-2xl bg-primary text-white font-bold text-lg shadow-lg shadow-primary/20 hover:bg-primary-light hover:shadow-xl hover:shadow-primary/30 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                        {{ __('landing.create_profile') ?? 'Create Business Profile' }}
                    </a>
                </div>
            </div>

            {{-- Minimalist Visual (Right) --}}
            <div class="w-full lg:w-1/2 h-[350px] sm:h-[450px] lg:h-auto lg:self-stretch relative overflow-hidden bg-slate-100/50 dark:bg-zinc-900/20 border-t lg:border-t-0 lg:border-l border-slate-200/60 dark:border-zinc-800/60 flex items-center justify-center">
                
                {{-- Decorative Gradients --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-primary/20 dark:bg-primary/10 rounded-full blur-[80px]"></div>
                </div>

                {{-- Abstract Business Growth Graph --}}
                <div class="relative z-10 w-[280px] sm:w-[340px] bg-white/70 dark:bg-zinc-900/70 backdrop-blur-2xl border border-white dark:border-zinc-700/50 rounded-[2rem] shadow-2xl shadow-slate-200/50 dark:shadow-black/50 p-6 sm:p-8 transform transition-transform duration-700 hover:-translate-y-2 hover:scale-[1.02]">
                    
                    {{-- Graph Header --}}
                    <div class="flex items-start justify-between mb-8">
                        <div>
                            <div class="text-xs sm:text-sm font-bold text-slate-400 dark:text-zinc-500 mb-1">{{ __('landing.cta_graph_views') ?? 'Profile Views' }}</div>
                            <div class="text-2xl sm:text-3xl font-[900] text-slate-900 dark:text-white flex items-center gap-2">
                                248.5k 
                                <span class="text-[10px] sm:text-xs font-bold px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded-lg flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                    24%
                                </span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>

                    {{-- Bar Chart Area --}}
                    <div class="relative h-32 sm:h-40 w-full flex items-end justify-between gap-1.5 sm:gap-2">
                        <div class="w-full bg-primary/5 dark:bg-primary/10 rounded-t-md h-[20%] transition-all duration-500 hover:h-[25%] hover:bg-primary/20"></div>
                        <div class="w-full bg-primary/5 dark:bg-primary/10 rounded-t-md h-[35%] transition-all duration-500 hover:h-[40%] hover:bg-primary/20"></div>
                        <div class="w-full bg-primary/10 dark:bg-primary/15 rounded-t-md h-[25%] transition-all duration-500 hover:h-[30%] hover:bg-primary/20"></div>
                        <div class="w-full bg-primary/20 dark:bg-primary/20 rounded-t-md h-[50%] transition-all duration-500 hover:h-[55%] hover:bg-primary/30"></div>
                        <div class="w-full bg-primary/30 dark:bg-primary/30 rounded-t-md h-[70%] transition-all duration-500 hover:h-[75%] hover:bg-primary/40 relative group">
                             <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-primary ring-4 ring-primary/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        <div class="w-full bg-gradient-to-t from-primary to-primary-light rounded-t-md h-[100%] relative shadow-[0_0_15px_rgba(244,80,24,0.3)] group cursor-pointer">
                             <div class="absolute -top-8 sm:-top-10 left-1/2 -translate-x-1/2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] sm:text-xs font-bold py-1 px-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0 whitespace-nowrap shadow-xl">{{ __('landing.cta_graph_today') ?? 'Today' }}</div>
                             <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-white shadow-sm"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

