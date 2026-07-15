@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $title = $post->title[$locale] ?? $post->title['en'] ?? '';
    $content = $post->content[$locale] ?? $post->content['en'] ?? '';
    
    // Fallbacks for SeoMetadata from HasSeoMetadata trait
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
<div class="relative min-h-screen bg-white dark:bg-[#0a0a0c] pt-24 pb-16">
    <!-- Ambient Background -->
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
        <div class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb & Back --}}
        <div class="mb-8">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-primary transition-colors">
                <i class="fa-solid fa-arrow-left me-2 rtl:rotate-180"></i>
                {{ __('home.back_to_blog') }}
            </a>
        </div>

        {{-- Header --}}
        <header class="mb-10 text-center">
            <div class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-6">
                {{ $post->published_at->format('M d, Y') }}
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-[900] tracking-tight text-slate-900 dark:text-white leading-tight mb-6">
                {{ $title }}
            </h1>
        </header>

        {{-- Media Container --}}
        @if($post->media_url)
            @php
                $mediaAlt = $post->media_alt[app()->getLocale()] ?? $title;
            @endphp
            @if($post->media_type === 'image')
                <div class="w-full h-[400px] md:h-[500px] rounded-3xl overflow-hidden shadow-2xl mb-12 border border-slate-200/50 dark:border-white/10 relative">
                    <img src="{{ asset('storage/' . $post->media_url) }}" alt="{{ $mediaAlt }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                </div>
            @elseif($post->media_type === 'video')
                <div class="w-full aspect-video rounded-3xl overflow-hidden shadow-2xl mb-12 border border-slate-200/50 dark:border-white/10 relative">
                    @if(\Illuminate\Support\Str::startsWith($post->media_url, 'http'))
                        <iframe src="{{ $post->media_url }}" title="{{ $mediaAlt }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    @else
                        <video src="{{ asset('storage/' . $post->media_url) }}" aria-label="{{ $mediaAlt }}" class="w-full h-full object-cover" controls playsinline></video>
                    @endif
                </div>
            @endif
        @endif

        {{-- Content --}}
        <article class="prose prose-lg dark:prose-invert max-w-none prose-img:rounded-2xl prose-headings:font-[900] prose-a:text-primary hover:prose-a:text-primary-light">
            {!! $content !!}
        </article>
    </div>
</div>
@endsection
