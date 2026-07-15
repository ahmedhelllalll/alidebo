@extends('layouts.app')

@section('title', __('home.blog_title'))

@section('content')
<div class="relative min-h-screen bg-slate-50 dark:bg-[#0a0a0c] pt-24 pb-12 overflow-hidden">
    <!-- Ambient Background -->
    <div class="absolute top-0 inset-x-0 h-[600px] pointer-events-none overflow-hidden z-0 opacity-40 dark:opacity-30">
        <div class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-primary/10 dark:bg-primary/10 blur-[130px] rounded-full"></div>
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-primary/10 dark:bg-primary/10 blur-[140px] rounded-full"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        {{-- Header --}}
        <div class="text-center max-w-3xl mx-auto mt-8">
            <h1 class="text-4xl md:text-5xl font-[900] tracking-tight text-slate-900 dark:text-white mb-6 leading-tight">
                {{ __('home.blog_title') }}
            </h1>
            <p class="text-lg text-slate-600 dark:text-zinc-400 font-medium">
                {{ __('home.blog_desc') }}
            </p>
        </div>

        {{-- Blog Grid --}}
        @if($posts->isEmpty())
            <div class="bg-white dark:bg-[#121214] border border-slate-200/60 dark:border-white/5 shadow-xl shadow-slate-200/20 dark:shadow-none rounded-3xl p-10 sm:p-16 flex flex-col items-center justify-center text-center">
                <div class="relative mb-8 group">
                    <div class="absolute inset-0 bg-primary/20 rounded-full blur-3xl scale-150 transition-transform duration-700 opacity-40 dark:opacity-20"></div>
                    <div class="relative w-28 h-28 flex items-center justify-center">
                        <i class="fa-solid fa-file-pen text-5xl text-primary/80 drop-shadow-sm"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-3">
                    {{ __('home.no_blogs_yet') }}
                </h3>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    @php
                        // Check if the translation exists for the current locale, otherwise skip or fallback
                        // For the public frontend, we only show posts that have a title in the current locale
                        $locale = app()->getLocale();
                        $title = $post->title[$locale] ?? null;
                        if (!$title) continue; // Skip rendering if no translation available for this locale
                    @endphp
                    <a href="{{ route('blog.show', $post->slug) }}" class="group flex flex-col bg-white dark:bg-[#121214] rounded-3xl overflow-hidden border border-slate-200 dark:border-white/10 shadow-sm hover:shadow-xl hover:border-primary/30 transition-all duration-300 transform hover:-translate-y-1">
                        {{-- Image/Media Cover --}}
                        <div class="relative h-56 bg-slate-100 dark:bg-zinc-800/50 overflow-hidden">
                            @if($post->media_url && $post->media_type === 'image')
                                <img src="{{ asset('storage/' . $post->media_url) }}" alt="{{ $title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-zinc-600">
                                    <i class="fa-solid fa-image text-4xl"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-white/90 dark:bg-[#09090b]/90 backdrop-blur-md px-3 py-1.5 rounded-full text-[11px] font-bold text-slate-700 dark:text-zinc-300 shadow-sm">
                                {{ $post->published_at->format('M d, Y') }}
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-xl font-[900] text-slate-900 dark:text-white leading-tight mb-3 group-hover:text-primary transition-colors line-clamp-2">
                                {{ $title }}
                            </h3>
                            <div class="text-sm text-slate-500 dark:text-zinc-400 font-medium line-clamp-3 mb-6">
                                {!! strip_tags($post->content[$locale] ?? '') !!}
                            </div>
                            
                            <div class="mt-auto flex items-center text-primary font-bold text-[13px] uppercase tracking-wider">
                                {{ __('home.read_more') }}
                                <i class="fa-solid fa-arrow-right ms-2 transition-transform group-hover:translate-x-1 rtl:rotate-180 rtl:group-hover:-translate-x-1"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($posts->hasPages())
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
