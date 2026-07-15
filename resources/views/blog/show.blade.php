@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $title = $post->title[$locale] ?? $post->title['en'] ?? '';
    $content = $post->content[$locale] ?? $post->content['en'] ?? '';
    
    if (isset($post->seoMetadata)) {
        $metaTitle = $post->seoMetadata->meta_title[$locale] ?? null;
        $metaDesc = $post->seoMetadata->meta_description[$locale] ?? null;
    }
@endphp

@if(isset($metaTitle))
    @section('title', $metaTitle)
@else
    @section('title', $title)
@endif

@if(isset($metaDesc))
    @section('meta_description', $metaDesc)
@endif

@section('content')
<div class="relative overflow-hidden bg-white dark:bg-[#0a0a0c]">
    {{-- Ambient Corner Glows --}}
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
        <div class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full"></div>
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-primary/10 dark:bg-primary/10 blur-[140px] rounded-full"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 sm:pt-40 pb-24">
        
        {{-- Back Link --}}
        <div class="mb-12 reveal">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center text-sm font-[900] text-slate-500 dark:text-zinc-400 hover:text-primary transition-colors group uppercase tracking-widest">
                <i class="fa-solid fa-arrow-left me-3 rtl:rotate-180 transition-transform duration-300 group-hover:-translate-x-1 rtl:group-hover:translate-x-1 text-xs"></i>
                {{ __('home.back_to_blog') }}
            </a>
        </div>

        {{-- Article Header --}}
        <header class="mb-12 reveal">
            <time datetime="{{ $post->published_at->toIso8601String() }}" class="text-xs font-black text-primary uppercase tracking-widest mb-6 block">
                {{ $post->published_at->format('M d, Y') }}
            </time>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-[900] tracking-tight text-slate-900 dark:text-white leading-tight mb-6">
                {{ $title }}
            </h1>
            @if(!empty($post->description[$locale] ?? ''))
                <p class="text-lg sm:text-xl text-slate-600 dark:text-zinc-400 font-medium leading-relaxed max-w-3xl">
                    {{ $post->description[$locale] }}
                </p>
            @endif
        </header>

        {{-- Media --}}
        @if($post->media_url)
            @php
                $mediaAlt = $post->media_alt[app()->getLocale()] ?? $title;
            @endphp
            <div class="reveal mb-16">
                <div class="w-full rounded-[2rem] overflow-hidden border border-slate-200/80 dark:border-zinc-800/80 bg-slate-50 dark:bg-zinc-900 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)]">
                    @if($post->media_type === 'image')
                        <img src="{{ asset('storage/' . $post->media_url) }}" alt="{{ $mediaAlt }}" class="w-full h-auto max-h-[550px] object-cover">
                    @elseif($post->media_type === 'video')
                        <div class="w-full aspect-video">
                            @if(\Illuminate\Support\Str::startsWith($post->media_url, 'http'))
                                <iframe src="{{ $post->media_url }}" title="{{ $mediaAlt }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            @else
                                <video src="{{ asset('storage/' . $post->media_url) }}" aria-label="{{ $mediaAlt }}" class="w-full h-full object-cover" controls playsinline></video>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Article Content --}}
        <div class="reveal">
            <article class="
                prose prose-lg dark:prose-invert max-w-none
                prose-headings:font-[900] prose-headings:tracking-tight prose-headings:text-slate-900 dark:prose-headings:text-white
                prose-h2:text-2xl prose-h2:sm:text-3xl prose-h2:mt-14 prose-h2:mb-6
                prose-h3:text-xl prose-h3:sm:text-2xl prose-h3:mt-10 prose-h3:mb-5
                prose-p:text-[17px] prose-p:text-slate-600 dark:prose-p:text-zinc-400 prose-p:font-medium prose-p:leading-[1.9] prose-p:mb-7
                prose-a:text-primary hover:prose-a:text-primary-light prose-a:font-bold prose-a:underline prose-a:underline-offset-4 prose-a:decoration-primary/30 hover:prose-a:decoration-primary
                prose-blockquote:border-s-4 prose-blockquote:border-primary/30 prose-blockquote:bg-slate-50 dark:prose-blockquote:bg-zinc-900/50 prose-blockquote:py-4 prose-blockquote:px-6 prose-blockquote:rounded-e-2xl prose-blockquote:text-slate-700 dark:prose-blockquote:text-zinc-300 prose-blockquote:font-medium prose-blockquote:not-italic
                prose-strong:font-[900] prose-strong:text-slate-900 dark:prose-strong:text-white
                prose-ul:list-disc prose-ul:ps-6 prose-ol:list-decimal prose-ol:ps-6
                prose-li:text-[17px] prose-li:text-slate-600 dark:prose-li:text-zinc-400 prose-li:font-medium prose-li:mb-2 prose-li:leading-[1.9]
                prose-img:rounded-[1.5rem] prose-img:border prose-img:border-slate-200/80 dark:prose-img:border-zinc-800/80 prose-img:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.08)]
                prose-code:bg-slate-100 dark:prose-code:bg-zinc-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-lg prose-code:text-sm prose-code:font-bold prose-code:text-primary
                text-start
            ">
                {!! $content !!}
            </article>
        </div>

        {{-- Divider & Share --}}
        <div class="reveal mt-20 pt-10 border-t border-slate-200/80 dark:border-zinc-800/80">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <span class="text-xs font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest">
                    {{ __('home.share_article') ?? 'Share' }}
                </span>
                <div class="flex items-center gap-3">
                    <button onclick="navigator.clipboard.writeText(window.location.href); showToast('{{ __('home.copied') ?? 'Copied' }}', '{{ __('home.link_copied') ?? 'Link copied.' }}', false)" class="group w-11 h-11 rounded-xl bg-white dark:bg-[#09090b] border border-slate-200/80 dark:border-zinc-800/80 text-slate-500 dark:text-zinc-400 hover:border-primary/30 hover:text-primary transition-all duration-300 flex items-center justify-center hover:shadow-[0_4px_20px_-4px_rgba(244,80,24,0.15)]">
                        <i class="fa-solid fa-link text-sm"></i>
                    </button>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="group w-11 h-11 rounded-xl bg-white dark:bg-[#09090b] border border-slate-200/80 dark:border-zinc-800/80 text-slate-500 dark:text-zinc-400 hover:border-[#1DA1F2]/30 hover:text-[#1DA1F2] transition-all duration-300 flex items-center justify-center hover:shadow-[0_4px_20px_-4px_rgba(29,161,242,0.15)]">
                        <i class="fa-brands fa-x-twitter text-sm"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="group w-11 h-11 rounded-xl bg-white dark:bg-[#09090b] border border-slate-200/80 dark:border-zinc-800/80 text-slate-500 dark:text-zinc-400 hover:border-[#1877F2]/30 hover:text-[#1877F2] transition-all duration-300 flex items-center justify-center hover:shadow-[0_4px_20px_-4px_rgba(24,119,242,0.15)]">
                        <i class="fa-brands fa-facebook-f text-sm"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="group w-11 h-11 rounded-xl bg-white dark:bg-[#09090b] border border-slate-200/80 dark:border-zinc-800/80 text-slate-500 dark:text-zinc-400 hover:border-[#0A66C2]/30 hover:text-[#0A66C2] transition-all duration-300 flex items-center justify-center hover:shadow-[0_4px_20px_-4px_rgba(10,102,194,0.15)]">
                        <i class="fa-brands fa-linkedin-in text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
