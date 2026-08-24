@extends('layouts.app')

@section('title', __('home.blog_title') ?? 'Resources & Insights')

@section('content')
<div class="relative overflow-hidden bg-white dark:bg-[#111113] pt-32 sm:pt-40 pb-24 min-h-screen">
    {{-- Ambient Corner Glows --}}
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-20">
        <div class="absolute -top-40 -start-40 w-[400px] h-[400px] bg-primary/10 blur-[130px] rounded-full"></div>
        <div class="absolute -top-40 -end-40 w-[500px] h-[500px] bg-primary/10 blur-[140px] rounded-full"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 reveal">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-6">
                {{ __('home.blog_title') ?? 'Resources & Insights' }}
            </h1>
            <p class="text-lg sm:text-xl text-slate-600 dark:text-zinc-400 font-medium leading-relaxed">
                {{ __('home.blog_subtitle') ?? 'Discover the latest product updates, industry insights, and comprehensive guides to grow your business.' }}
            </p>
        </div>

        {{-- Filter/Search Bar (Placeholder for future functionality) --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-16 pb-6 border-b border-slate-200/80 dark:border-zinc-800/80 reveal">
            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 scrollbar-hide" style="-ms-overflow-style: none; scrollbar-width: none;">
                <span class="px-5 py-2 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-bold whitespace-nowrap">{{ __('home.all_posts') ?? 'All Posts' }}</span>
                <span class="px-5 py-2 rounded-full bg-slate-100 dark:bg-zinc-800/50 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-800 text-sm font-semibold transition-colors cursor-pointer whitespace-nowrap">Product</span>
                <span class="px-5 py-2 rounded-full bg-slate-100 dark:bg-zinc-800/50 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-800 text-sm font-semibold transition-colors cursor-pointer whitespace-nowrap">Engineering</span>
                <span class="px-5 py-2 rounded-full bg-slate-100 dark:bg-zinc-800/50 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-800 text-sm font-semibold transition-colors cursor-pointer whitespace-nowrap">Guides</span>
            </div>
            <div class="relative w-full sm:w-72">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 rtl:left-auto rtl:right-4"></i>
                <input type="text" placeholder="{{ __('home.search_posts') ?? 'Search articles...' }}" class="w-full pl-11 pr-4 rtl:pl-4 rtl:pr-11 py-2.5 bg-slate-100 dark:bg-zinc-800/50 border-none rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary dark:text-white placeholder-slate-400 dark:placeholder-zinc-500 transition-shadow outline-none">
            </div>
        </div>

        @php $locale = app()->getLocale(); @endphp

        @if($posts->isEmpty())
            {{-- Smart Empty State --}}
            <div class="reveal relative w-full bg-slate-50 dark:bg-zinc-900/30 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-zinc-800/80">
                <div class="p-12 sm:p-20 text-center max-w-2xl mx-auto relative z-10">
                    <div class="w-16 h-16 mx-auto rounded-xl bg-primary/10 flex items-center justify-center mb-6">
                        <i class="fa-solid fa-hourglass-half text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white mb-4">
                        {{ __('home.blog_empty_title') ?? 'Great Things Are Coming' }}
                    </h3>
                    <p class="text-base text-slate-600 dark:text-zinc-400 font-medium mb-8">
                        {{ __('home.blog_empty_msg') ?? 'We are currently crafting high-quality insights. Stay tuned for our upcoming publications.' }}
                    </p>
                    <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-sm">
                        {{ __('home.back_to_home') ?? 'Back to Home' }}
                        <i class="fa-solid fa-arrow-right ms-2 rtl:rotate-180"></i>
                    </a>
                </div>
            </div>
        @else
            @if($isFirstPage && $featuredPost)
                {{-- Featured Post (Hero) --}}
                @php $featuredTitle = $featuredPost->title[$locale]; @endphp
                <a href="{{ route('blog.show', $featuredPost->slug) }}" class="reveal group relative flex flex-col lg:flex-row h-full bg-white dark:bg-[#111113] rounded-2xl overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 transition-all duration-300 hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-black/50 hover:-translate-y-1 mb-16">
                    {{-- Featured Image --}}
                    <div class="w-full lg:w-1/2 h-[300px] lg:h-auto min-h-[350px] relative overflow-hidden bg-slate-100 dark:bg-zinc-800/50 border-b lg:border-b-0 lg:border-e border-slate-200/60 dark:border-zinc-800/60">
                        @if($featuredPost->media_url && $featuredPost->media_type === 'image')
                            <img src="{{ asset('storage/' . $featuredPost->media_url) }}" alt="{{ $featuredTitle }}" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-image text-3xl text-slate-300 dark:text-zinc-600 opacity-50"></i>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Featured Content --}}
                    <div class="w-full lg:w-1/2 p-8 sm:p-12 flex flex-col justify-center relative z-10">
                        @php $fDate = $featuredPost->published_at ?? $featuredPost->created_at; @endphp
                        <time datetime="{{ $fDate->toIso8601String() }}" class="text-xs font-bold text-primary uppercase tracking-widest mb-4 block">
                            {{ $fDate->format('M d, Y') }}
                        </time>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight mb-5 group-hover:text-primary transition-colors line-clamp-3">
                            {{ $featuredTitle }}
                        </h2>
                        <div class="text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed line-clamp-2 mb-8">
                            {{ $featuredPost->description[$locale] ?? \Illuminate\Support\Str::limit(strip_tags($featuredPost->content[$locale] ?? ''), 140) }}
                        </div>
                        <div class="flex items-center text-primary font-bold text-sm uppercase tracking-widest">
                            {{ __('home.read_more') ?? 'Read Article' }}
                            <i class="fa-solid fa-arrow-right ms-2 transition-transform duration-300 group-hover:translate-x-1 rtl:rotate-180 rtl:group-hover:-translate-x-1 text-xs"></i>
                        </div>
                    </div>
                </a>

                {{-- Latest Insights Grid (Next 3 Posts) --}}
                @if($latestPosts->isNotEmpty())
                    <div class="mb-16">
                        <div class="flex items-center justify-between mb-8 reveal">
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ __('home.latest_insights') ?? 'Latest Insights' }}</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 border-b border-slate-200/80 dark:border-zinc-800/80 pb-16">
                            @foreach($latestPosts as $post)
                                @php $postTitle = $post->title[$locale]; @endphp
                                <a href="{{ route('blog.show', $post->slug) }}" class="reveal group flex flex-col h-full bg-transparent transition-all">
                                    <div class="w-full h-48 sm:h-56 rounded-2xl overflow-hidden mb-5 bg-slate-100 dark:bg-zinc-800/50 relative">
                                        @if($post->media_url && $post->media_type === 'image')
                                            <img src="{{ asset('storage/' . $post->media_url) }}" alt="{{ $postTitle }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fa-solid fa-image text-2xl text-slate-300 dark:text-zinc-600 opacity-50"></i>
                                            </div>
                                        @endif
                                    </div>
                                    @php $pDate = $post->published_at ?? $post->created_at; @endphp
                                    <time datetime="{{ $pDate->toIso8601String() }}" class="text-xs font-bold text-primary uppercase tracking-widest mb-3 block">
                                        {{ $pDate->format('M d, Y') }}
                                    </time>
                                    <h4 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white leading-snug group-hover:text-primary transition-colors line-clamp-2 mb-3">
                                        {{ $postTitle }}
                                    </h4>
                                    <div class="mt-auto flex items-center text-slate-500 dark:text-zinc-400 font-semibold text-sm transition-colors group-hover:text-primary">
                                        {{ __('home.read_more') ?? 'Read Article' }}
                                        <i class="fa-solid fa-arrow-right ms-2 rtl:rotate-180 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                {{-- Newsletter CTA Banner --}}
                <div class="reveal w-full bg-slate-900 dark:bg-zinc-900 rounded-2xl overflow-hidden mb-16 relative shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-transparent pointer-events-none"></div>
                    <div class="p-8 sm:p-12 flex flex-col md:flex-row items-center justify-between relative z-10 gap-8">
                        <div class="text-center md:text-start md:max-w-md">
                            <h3 class="text-2xl font-bold text-white mb-2">{{ __('home.newsletter_title') ?? 'Get the latest insights' }}</h3>
                            <p class="text-slate-400 font-medium text-sm">{{ __('home.newsletter_desc') ?? 'Join our newsletter and receive the best articles delivered straight to your inbox once a week.' }}</p>
                        </div>
                        <div class="w-full md:w-auto flex-1 max-w-sm">
                            <form class="flex flex-col sm:flex-row gap-3">
                                <input type="email" placeholder="{{ __('home.email_placeholder') ?? 'Enter your email' }}" class="w-full bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-400 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all">
                                <button type="button" class="whitespace-nowrap px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                                    {{ __('home.subscribe') ?? 'Subscribe' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Archive Grid --}}
            @if($archivePosts->isNotEmpty())
                <div class="flex items-center justify-between mb-8 reveal">
                    <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ __('home.archive') ?? 'All Articles' }}</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($archivePosts as $post)
                        @php $postTitle = $post->title[$locale]; @endphp
                        <a href="{{ route('blog.show', $post->slug) }}" class="reveal group flex flex-col h-full bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800/80 rounded-2xl p-4 transition-all duration-300 hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-black/50 hover:-translate-y-1">
                            <div class="w-full h-44 rounded-xl overflow-hidden mb-4 bg-slate-100 dark:bg-zinc-800/50">
                                @if($post->media_url && $post->media_type === 'image')
                                    <img src="{{ asset('storage/' . $post->media_url) }}" alt="{{ $postTitle }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fa-solid fa-image text-xl text-slate-300 dark:text-zinc-600 opacity-50"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 flex flex-col px-2 pb-2">
                                @php $gDate = $post->published_at ?? $post->created_at; @endphp
                                <time datetime="{{ $gDate->toIso8601String() }}" class="text-[11px] font-bold text-primary uppercase tracking-widest mb-2 block">
                                    {{ $gDate->format('M d, Y') }}
                                </time>
                                <h4 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white leading-snug group-hover:text-primary transition-colors line-clamp-2">
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
