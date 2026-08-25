@extends('layouts.app')

@section('title', __('home.blog_title') ?? 'Blog')
@section('meta_description', __('home.blog_empty_msg') ?? 'Latest insights, industry news, and comprehensive guides.')

@section('content')
<div class="relative overflow-hidden bg-white dark:bg-[#0a0a0c] pt-32 sm:pt-40 pb-24 min-h-screen">
    {{-- Ambient Corner Glows --}}
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
        <div class="absolute -top-40 -start-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full"></div>
        <div class="absolute -top-40 -end-40 w-[500px] h-[500px] bg-primary/10 dark:bg-primary/10 blur-[140px] rounded-full"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $locale = app()->getLocale();
                $featuredPost = null;
                $remainingPosts = collect();
                
                if (!$posts->isEmpty()) {
                    foreach($posts as $p) {
                        $t = $p->title[$locale] ?? null;
                        if (!$t) continue;
                        
                        if (!$featuredPost) {
                            $featuredPost = $p;
                        } else {
                            $remainingPosts->push($p);
                        }
                    }
                }
            @endphp

            @if(!$featuredPost)
                {{-- Smart Empty State --}}
                <div class="relative w-full bg-white dark:bg-[#0a0a0c] rounded-[2rem] lg:rounded-[3rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)]">
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
                @php $featuredTitle = $featuredPost->title[$locale]; @endphp
                    <a href="{{ route('blog.show', $featuredPost->slug) }}" class="group relative flex flex-col lg:flex-row h-full bg-white dark:bg-[#09090b] rounded-[2rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 transition-all duration-300 ease-out will-change-transform hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.4)] hover:-translate-y-1.5 mb-8">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-out pointer-events-none"></div>
                        
                        {{-- Featured Image --}}
                        <div class="w-full lg:w-1/2 h-[280px] sm:h-[350px] lg:h-auto lg:min-h-[400px] relative overflow-hidden bg-slate-100 dark:bg-zinc-800/50 border-b lg:border-b-0 lg:border-e border-slate-200/60 dark:border-zinc-800/60">
                            @if($featuredPost->media_url && $featuredPost->media_type === 'image')
                                <img src="{{ asset('storage/' . $featuredPost->media_url) }}" alt="{{ $featuredTitle }}" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" loading="lazy" decoding="async">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-image text-4xl text-slate-300 dark:text-zinc-600 opacity-50"></i>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Featured Content --}}
                        <div class="w-full lg:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center relative z-10">
                            @php $fDate = $featuredPost->published_at ?? $featuredPost->created_at; @endphp
                            <time datetime="{{ $fDate->toIso8601String() }}" class="text-xs font-black text-primary uppercase tracking-widest mb-6">
                                {{ $fDate->format('M d, Y') }}
                            </time>
                            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-[900] text-slate-900 dark:text-white leading-tight mb-6 group-hover:text-primary transition-colors line-clamp-3">
                                {{ $featuredTitle }}
                            </h2>
                            <div class="text-sm sm:text-base text-slate-600 dark:text-zinc-400 font-medium leading-relaxed line-clamp-3 mb-8">
                                {{ $featuredPost->description[$locale] ?? \Illuminate\Support\Str::limit(strip_tags($featuredPost->content[$locale] ?? ''), 250) }}
                            </div>
                            <div class="flex items-center text-primary font-[900] text-sm uppercase tracking-widest">
                                {{ __('home.read_more') }}
                                <i class="fa-solid fa-arrow-right ms-3 transition-transform duration-300 group-hover:translate-x-2 rtl:rotate-180 rtl:group-hover:-translate-x-2 text-xs"></i>
                            </div>
                        </div>
                    </a>

                {{-- Other Posts (Minimal Cards) --}}
                @if($remainingPosts->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mt-12">
                        @foreach($remainingPosts as $post)
                            @php
                                $postTitle = $post->title[$locale] ?? null;
                                if (!$postTitle) continue;
                            @endphp
                            <a href="{{ route('blog.show', $post->slug) }}" class="group flex flex-col h-full bg-white dark:bg-[#09090b] rounded-[1.5rem] border border-slate-100 dark:border-zinc-800/80 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                                {{-- Thumbnail --}}
                                <div class="w-full aspect-[16/10] overflow-hidden bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-100 dark:border-zinc-800/80">
                                    @if($post->media_url && $post->media_type === 'image')
                                        <img src="{{ asset('storage/' . $post->media_url) }}" alt="{{ $postTitle }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" decoding="async">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fa-solid fa-image text-3xl text-slate-300 dark:text-zinc-600 opacity-50"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Content --}}
                                <div class="p-6 sm:p-8 flex flex-col flex-1">
                                    @php $pDate = $post->published_at ?? $post->created_at; @endphp
                                    <time datetime="{{ $pDate->toIso8601String() }}" class="text-[12px] font-[900] text-primary uppercase tracking-widest mb-4 block">
                                        {{ $pDate->format('M d, Y') }}
                                    </time>
                                    <h4 class="text-xl sm:text-2xl font-[900] text-slate-900 dark:text-white leading-tight group-hover:text-primary transition-colors mb-4 line-clamp-2">
                                        {{ $postTitle }}
                                    </h4>
                                    <div class="text-sm text-slate-600 dark:text-zinc-400 font-medium leading-relaxed line-clamp-3 mb-6 flex-1">
                                        {{ $post->description[$locale] ?? \Illuminate\Support\Str::limit(strip_tags($post->content[$locale] ?? ''), 120) }}
                                    </div>
                                    <div class="flex items-center text-slate-900 dark:text-white font-[900] text-sm uppercase tracking-widest mt-auto group-hover:text-primary transition-colors">
                                        {{ __('home.read_more') }}
                                        <i class="fa-solid fa-arrow-right ms-3 transition-transform duration-300 group-hover:translate-x-2 rtl:rotate-180 rtl:group-hover:-translate-x-2 text-xs"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($posts->hasPages())
                    <div class="mt-16 flex justify-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            @endif
    </div>
</div>

@push('scripts')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Blog",
  "mainEntityOfPage": {
    "@@type": "WebPage",
    "@@id": "{{ url()->current() }}"
  },
  "name": "{{ __('home.blog_title') ?? 'Blog' }}",
  "description": "{{ __('home.blog_empty_msg') ?? 'Latest insights, industry news, and comprehensive guides.' }}",
  "publisher": {
    "@@type": "Organization",
    "name": "alidebo",
    "logo": {
      "@@type": "ImageObject",
      "url": "{{ asset('images/logo.webp') }}"
    }
  }
}
</script>
@endpush
@endsection
