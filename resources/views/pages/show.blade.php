@extends('layouts.app')

@section('title', $page->seoMetadata->meta_title[app()->getLocale()] ?? $page->title[app()->getLocale()] ?? $page->title['en'])
@section('meta_description', $page->seoMetadata->meta_description[app()->getLocale()] ?? '')
@section('og_image', $page->seoMetadata->og_image ?? '')

@section('content')
    @php
        $contentHTML = $page->content[app()->getLocale()] ?? $page->content['en'];
        $wordCount = str_word_count(strip_tags($contentHTML));
        $readingTime = max(1, ceil($wordCount / 200));
        $title = $page->title[app()->getLocale()] ?? $page->title['en'];
    @endphp

    {{-- Hero Section --}}
    <div class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden bg-slate-50 dark:bg-[#0a0a0c]">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-primary/5 blur-[120px]"></div>
            <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] rounded-full bg-indigo-500/5 blur-[120px]"></div>
        </div>
        
        <div class="container mx-auto px-4 sm:px-6 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                {{-- Breadcrumbs --}}
                <nav class="flex justify-center mb-8 reveal" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3 bg-white/60 dark:bg-white/5 backdrop-blur-md px-4 py-2 rounded-full border border-slate-200 dark:border-white/10 shadow-sm">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">
                                <i class="fa-solid fa-house mr-2"></i> {{ __('landing.home') ?? 'Home' }}
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-slate-400 text-[10px] mx-2"></i>
                                <span class="text-xs font-bold text-slate-800 dark:text-zinc-200">{{ $title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                {{-- Glassmorphic Title Card --}}
                <div class="relative inline-block reveal" style="animation-delay: 0.1s;">
                    <div class="absolute inset-0 bg-gradient-to-r from-primary to-orange-400 blur-2xl opacity-20 dark:opacity-30 rounded-[3rem]"></div>
                    <div class="relative bg-white/80 dark:bg-[#18181b]/80 backdrop-blur-xl border border-white/50 dark:border-white/10 p-8 md:p-12 rounded-[2.5rem] shadow-2xl">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-[1000] tracking-tight text-slate-900 dark:text-white mb-6">
                            {{ $title }}
                        </h1>
                        <div class="flex items-center justify-center gap-4 text-sm font-bold text-slate-500 dark:text-zinc-400">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-clock text-primary"></i>
                                <span>{{ $readingTime }} {{ __('landing.min_read') ?? 'min read' }}</span>
                            </div>
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-zinc-700"></div>
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-calendar text-primary"></i>
                                <span>{{ $page->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    @if($page->layout_style === 'cards')
        <div class="container mx-auto px-4 sm:px-6 py-16 lg:py-24 -mt-16 relative z-20">
            <div class="max-w-5xl mx-auto bg-white dark:bg-[#121214] rounded-[2.5rem] p-8 md:p-12 lg:p-16 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-slate-100 dark:border-white/5 reveal" style="animation-delay: 0.2s;">
                <div class="prose dark:prose-invert prose-lg max-w-none prose-headings:font-[900] prose-a:text-primary hover:prose-a:text-primary-light prose-img:rounded-3xl prose-img:shadow-xl prose-blockquote:border-primary prose-blockquote:bg-primary/5 prose-blockquote:py-2 prose-blockquote:px-6 prose-blockquote:rounded-r-2xl prose-blockquote:not-italic page-content">
                    {!! $contentHTML !!}
                </div>
            </div>
        </div>

    @elseif($page->layout_style === 'split')
        <div class="container mx-auto px-4 sm:px-6 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start relative">
                {{-- Table of Contents Sidebar --}}
                <div class="lg:col-span-4 sticky top-32 reveal hidden lg:block">
                    <div class="bg-white/50 dark:bg-[#121214]/50 backdrop-blur-xl rounded-[2rem] p-8 border border-slate-200 dark:border-white/10 shadow-lg">
                        <h3 class="text-xl font-[900] text-slate-900 dark:text-white mb-2 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <i class="fa-solid fa-list-ul text-sm"></i>
                            </div>
                            {{ __('admin.table_of_contents') ?? 'Table of Contents' }}
                        </h3>
                        <p class="text-slate-500 dark:text-zinc-400 text-sm font-medium mb-6 ml-11">
                            {{ __('landing.page_split_desc') ?? 'Navigate through the sections of this page.' }}
                        </p>
                        
                        <nav id="toc-container" class="space-y-1 ml-11 relative before:absolute before:inset-y-0 before:-left-4 before:w-0.5 before:bg-slate-100 dark:before:bg-zinc-800">
                            {{-- TOC populated by JS --}}
                        </nav>
                    </div>
                </div>

                {{-- Content --}}
                <div class="lg:col-span-8 reveal" style="animation-delay: 0.2s;">
                    <div class="prose dark:prose-invert max-w-none prose-headings:font-[900] prose-a:text-primary hover:prose-a:text-primary-light prose-img:rounded-3xl prose-img:shadow-xl prose-blockquote:border-primary prose-blockquote:bg-primary/5 prose-blockquote:py-2 prose-blockquote:px-6 prose-blockquote:rounded-r-2xl prose-blockquote:not-italic bg-white dark:bg-[#121214] p-8 md:p-12 rounded-[2.5rem] border border-slate-100 dark:border-zinc-800 shadow-xl page-content">
                        {!! $contentHTML !!}
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- Default Layout --}}
        <div class="container mx-auto px-4 sm:px-6 py-16 lg:py-24">
            <div class="max-w-4xl mx-auto reveal" style="animation-delay: 0.2s;">
                <div class="prose dark:prose-invert prose-lg max-w-none prose-headings:font-[900] prose-headings:tracking-tight prose-a:text-primary hover:prose-a:text-primary-light prose-img:rounded-3xl prose-img:shadow-xl prose-blockquote:border-primary prose-blockquote:bg-primary/5 prose-blockquote:py-2 prose-blockquote:px-6 prose-blockquote:rounded-r-2xl prose-blockquote:not-italic page-content">
                    {!! $contentHTML !!}
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Simple GSAP reveal animation
        if (typeof gsap !== 'undefined') {
            gsap.utils.toArray('.reveal').forEach((elem) => {
                gsap.fromTo(elem, 
                    { y: 30, opacity: 0 },
                    { 
                        scrollTrigger: {
                            trigger: elem,
                            start: "top 85%",
                        },
                        y: 0, 
                        opacity: 1, 
                        duration: 0.8, 
                        ease: "power3.out"
                    }
                );
            });
        }

        // Generate Table of Contents for Split Layout
        const tocContainer = document.getElementById('toc-container');
        if (tocContainer) {
            const contentArea = document.querySelector('.page-content');
            const headings = contentArea.querySelectorAll('h2, h3');
            
            if (headings.length > 0) {
                headings.forEach((heading, index) => {
                    // Give heading an ID if it doesn't have one
                    if (!heading.id) {
                        heading.id = 'section-' + index;
                    }
                    
                    const level = parseInt(heading.tagName.charAt(1));
                    const link = document.createElement('a');
                    link.href = '#' + heading.id;
                    link.textContent = heading.textContent;
                    link.className = `block py-2 text-sm font-bold transition-all duration-300 relative before:absolute before:top-1/2 before:-translate-y-1/2 before:-left-4 before:w-0.5 before:h-0 before:bg-primary before:transition-all before:duration-300 ${level === 3 ? 'ml-4 text-slate-500 hover:text-slate-900 dark:text-zinc-500 dark:hover:text-zinc-300 text-xs' : 'text-slate-700 hover:text-primary dark:text-zinc-400 dark:hover:text-primary'} toc-link`;
                    
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const target = document.getElementById(heading.id);
                        const offset = 100;
                        const bodyRect = document.body.getBoundingClientRect().top;
                        const elementRect = target.getBoundingClientRect().top;
                        const elementPosition = elementRect - bodyRect;
                        const offsetPosition = elementPosition - offset;
                        
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    });
                    
                    tocContainer.appendChild(link);
                });

                // Scroll Spy functionality
                const tocLinks = document.querySelectorAll('.toc-link');
                const observerOptions = {
                    root: null,
                    rootMargin: '-100px 0px -60% 0px',
                    threshold: 0
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            tocLinks.forEach(link => {
                                link.classList.remove('text-primary', 'dark:text-primary');
                                link.style.setProperty('--tw-before-h', '0');
                                link.classList.remove('before:h-full');
                            });
                            
                            const activeLink = document.querySelector(`.toc-link[href="#${entry.target.id}"]`);
                            if (activeLink) {
                                activeLink.classList.add('text-primary', 'dark:text-primary');
                                activeLink.classList.add('before:h-full');
                            }
                        }
                    });
                }, observerOptions);

                headings.forEach(heading => observer.observe(heading));
            } else {
                tocContainer.innerHTML = '<p class="text-xs text-slate-400 italic">No sections found.</p>';
            }
        }
    });
</script>
@endpush
