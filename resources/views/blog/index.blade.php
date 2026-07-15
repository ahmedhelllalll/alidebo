@extends('layouts.app')

@section('title', __('home.blog_title') ?? 'Blog')

@section('content')
<div class="relative overflow-hidden bg-white dark:bg-[#0a0a0c] pt-32 sm:pt-40 pb-24 min-h-screen">
    {{-- Ambient Corner Glows --}}
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
        <div class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full"></div>
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-primary/10 dark:bg-primary/10 blur-[140px] rounded-full"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($posts->isEmpty())
                {{-- Smart Empty State --}}
                <div class="reveal relative w-full bg-white dark:bg-[#0a0a0c] rounded-[2rem] lg:rounded-[3rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)]">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent opacity-50 pointer-events-none"></div>
                    <div class="p-12 sm:p-20 lg:p-24 text-center max-w-3xl mx-auto relative z-10">
                        <div class="w-20 h-20 mx-auto rounded-2xl bg-primary/10 flex items-center justify-center mb-8 border border-primary/20">
                            <i class="fa-solid fa-hourglass-half text-primary text-3xl"></i>
                        </div>
                        <h3 class="text-3xl sm:text-4xl lg:text-5xl font-[900] tracking-tight text-slate-900 dark:text-white leading-tight mb-6">
                            {{ __('home.blog_empty_title') ?? 'Great Things Are Coming' }}
                        </h3>
                        <p class="text-lg sm:text-xl text-slate-600 dark:text-zinc-400 font-medium leading-relaxed mb-10">
                            {{ __('home.blog_empty_msg') ?? 'We are currently crafting high-quality insights, industry news, and comprehensive guides. Stay tuned for our upcoming publications.' }}
                        </p>
                        <a href="{{ url('/') }}" class="inline-flex items-center px-8 py-4 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-lg shadow-slate-900/20 dark:shadow-white/20">
                            {{ __('home.back_to_home') ?? 'Back to Home' }}
                            <i class="fa-solid fa-arrow-right ms-3 rtl:rotate-180"></i>
                        </a>
                    </div>
                </div>
            @else
                {{-- Featured Post (first post gets hero treatment) --}}
                @php
                    $locale = app()->getLocale();
                    $featuredPost = null;
                    $remainingPosts = collect();
                    foreach($posts as $idx => $p) {
                        $t = $p->title[$locale] ?? null;
                        if (!$t) continue;
                        if (!$featuredPost) {
                            $featuredPost = $p;
                        } else {
                            $remainingPosts->push($p);
                        }
                    }
                @endphp

                @if($featuredPost)
                    @php $featuredTitle = $featuredPost->title[$locale]; @endphp
                    <a href="{{ route('blog.show', $featuredPost->slug) }}" class="reveal group relative flex flex-col lg:flex-row h-full bg-white dark:bg-[#09090b] rounded-[2rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 transition-all duration-300 ease-out will-change-transform hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-1.5 mb-8">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-out pointer-events-none"></div>
                        
                        {{-- Featured Image --}}
                        <div class="w-full lg:w-1/2 h-[280px] sm:h-[350px] lg:h-auto lg:min-h-[400px] relative overflow-hidden bg-slate-100 dark:bg-zinc-800/50 border-b lg:border-b-0 lg:border-e border-slate-200/60 dark:border-zinc-800/60">
                            @if($featuredPost->media_url && $featuredPost->media_type === 'image')
                                <img src="{{ asset('storage/' . $featuredPost->media_url) }}" alt="{{ $featuredTitle }}" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-image text-4xl text-slate-300 dark:text-zinc-600 opacity-50"></i>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Featured Content --}}
                        <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center relative z-10">
                            <time datetime="{{ $featuredPost->published_at->toIso8601String() }}" class="text-xs font-black text-primary uppercase tracking-widest mb-6">
                                {{ $featuredPost->published_at->format('M d, Y') }}
                            </time>
                            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-[900] text-slate-900 dark:text-white leading-tight mb-6 group-hover:text-primary transition-colors line-clamp-3">
                                {{ $featuredTitle }}
                            </h2>
                            <div class="text-sm sm:text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed line-clamp-3 mb-8">
                                {{ $featuredPost->description[$locale] ?? strip_tags($featuredPost->content[$locale] ?? '') }}
                            </div>
                            <div class="flex items-center text-primary font-[900] text-sm uppercase tracking-widest">
                                {{ __('home.read_more') }}
                                <i class="fa-solid fa-arrow-right ms-3 transition-transform duration-300 group-hover:translate-x-2 rtl:rotate-180 rtl:group-hover:-translate-x-2 text-xs"></i>
                            </div>
                        </div>
                    </a>
                @endif

                {{-- Editorial Vertical List (Next 3 Posts) --}}
                @php
                    $listPosts = $remainingPosts->take(3);
                    $gridPosts = $remainingPosts->skip(3);
                @endphp

                @if($listPosts->isNotEmpty())
                    <div class="mb-16">
                        <div class="flex items-center mb-8 reveal">
                            <h3 class="text-xl font-[900] text-slate-900 dark:text-white uppercase tracking-widest">{{ __('home.latest_insights') ?? 'Latest Insights' }}</h3>
                            <div class="h-px bg-slate-200 dark:bg-zinc-800 flex-1 ms-6"></div>
                        </div>
                        <div class="flex flex-col border-t border-slate-200/80 dark:border-zinc-800/80">
                            @foreach($listPosts as $post)
                                @php
                                    $postTitle = $post->title[$locale] ?? null;
                                    if (!$postTitle) continue;
                                @endphp
                                <a href="{{ route('blog.show', $post->slug) }}" class="reveal group flex flex-col md:flex-row md:items-center py-8 border-b border-slate-200/80 dark:border-zinc-800/80 hover:bg-slate-50/50 dark:hover:bg-zinc-900/20 transition-colors">
                                    <div class="w-full md:w-1/4 mb-4 md:mb-0">
                                        <time datetime="{{ $post->published_at->toIso8601String() }}" class="text-sm font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest block">
                                            {{ $post->published_at->format('M d, Y') }}
                                        </time>
                                    </div>
                                    <div class="w-full md:w-2/4 px-0 md:px-8">
                                        <h3 class="text-2xl font-[900] text-slate-900 dark:text-white leading-tight group-hover:text-primary transition-colors line-clamp-2">
                                            {{ $postTitle }}
                                        </h3>
                                    </div>
                                    <div class="w-full md:w-1/4 flex justify-start md:justify-end mt-6 md:mt-0">
                                        <div class="flex items-center text-primary font-[900] text-sm uppercase tracking-widest opacity-0 -translate-x-4 rtl:translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 rtl:group-hover:translate-x-0 transition-all duration-300">
                                            {{ __('home.read_more') }}
                                            <i class="fa-solid fa-arrow-right ms-3 rtl:rotate-180"></i>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Archive Grid --}}
                @if($gridPosts->isNotEmpty())
                    <div class="flex items-center mb-8 reveal">
                        <h3 class="text-xl font-[900] text-slate-900 dark:text-white uppercase tracking-widest">{{ __('home.archive') ?? 'Archive' }}</h3>
                        <div class="h-px bg-slate-200 dark:bg-zinc-800 flex-1 ms-6"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                        @foreach($gridPosts as $post)
                            @php
                                $postTitle = $post->title[$locale] ?? null;
                                if (!$postTitle) continue;
                            @endphp
                            <a href="{{ route('blog.show', $post->slug) }}" class="reveal group relative flex flex-col sm:flex-row items-center gap-6 p-6 rounded-[2rem] bg-white dark:bg-[#09090b] border border-slate-200/80 dark:border-zinc-800/80 transition-all duration-300 hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-1">
                                {{-- Thumbnail --}}
                                <div class="w-full sm:w-32 h-48 sm:h-32 shrink-0 rounded-[1.5rem] overflow-hidden bg-slate-100 dark:bg-zinc-800/50">
                                    @if($post->media_url && $post->media_type === 'image')
                                        <img src="{{ asset('storage/' . $post->media_url) }}" alt="{{ $postTitle }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fa-solid fa-image text-2xl text-slate-300 dark:text-zinc-600 opacity-50"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Content --}}
                                <div class="flex-1 min-w-0 py-2">
                                    <time datetime="{{ $post->published_at->toIso8601String() }}" class="text-[11px] font-black text-primary uppercase tracking-widest mb-3 block">
                                        {{ $post->published_at->format('M d, Y') }}
                                    </time>
                                    <h4 class="text-lg sm:text-xl font-[900] text-slate-900 dark:text-white leading-tight group-hover:text-primary transition-colors line-clamp-2">
                                        {{ $postTitle }}
                                    </h4>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($posts->hasPages())
                    <div class="mt-16 flex justify-center reveal">
                        {{ $posts->links() }}
                    </div>
                @endif
            @endif
    </div>
</div>
@endsection
