@extends('layouts.app')

@section('title', $business->name)

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- STATUS BANNERS (Non-blocking, contextual) --}}
    {{-- ═══════════════════════════════════════════════════════ --}}

    @if($business->status === 'pending')
    <div class="relative bg-amber-50 dark:bg-amber-950/30 border-b border-amber-200 dark:border-amber-900/50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center gap-3">
            <div class="shrink-0 w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-amber-800 dark:text-amber-300">
                    {{ __('dashboard.index.review_overlay.title') }}
                </p>
                <p class="text-xs text-amber-600/80 dark:text-amber-400/70">
                    {{ __('dashboard.index.review_overlay.message') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($business->status === 'rejected')
    <div class="relative bg-red-50 dark:bg-red-950/20 border-b border-red-200 dark:border-red-900/30">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-start gap-3">
            <div class="shrink-0 w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center mt-0.5">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-red-800 dark:text-red-300">
                    {{ __('dashboard.index.rejected_overlay.title') }}
                </p>
                @if($business->rejection_reason)
                <p class="text-xs text-red-600/80 dark:text-red-400/70 mt-1">
                    {{ $business->rejection_reason }}
                </p>
                @endif
                <a href="{{ route('business.edit') }}" class="inline-flex items-center gap-1.5 mt-2 text-xs font-semibold text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">
                    {{ __('dashboard.index.rejected_overlay.cta') }}
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- HERO CARD --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-8 pb-12">
        <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200/60 dark:border-slate-800/60 overflow-hidden">

            {{-- Cover Image --}}
            <div class="relative h-48 sm:h-64 w-full">
                @if($business->cover)
                    <img src="{{ str_contains($business->cover, 'categories') ? asset($business->cover) : asset('storage/' . $business->cover) }}" 
                         alt="{{ $business->name }}" 
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-white/90 dark:from-slate-900/95 via-white/20 dark:via-slate-900/30 to-transparent"></div>
            </div>

            {{-- Identity --}}
            <div class="relative px-6 sm:px-8 -mt-16 sm:-mt-20 pb-8">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">
                    {{-- Logo --}}
                    <div class="shrink-0">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-white dark:bg-slate-800 border-4 border-white dark:border-slate-900 shadow-lg overflow-hidden">
                            @if($business->logo)
                                <img src="{{ asset('storage/' . $business->logo) }}" alt="{{ $business->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 text-2xl font-bold">
                                    {{ substr($business->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Name & Meta --}}
                    <div class="flex-1 min-w-0 pt-2">
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                            {{ $business->name }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-500/20">
                                {{ $business->category->name }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $business->city->name }}, {{ $business->city->country->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- ACTION BAR --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 -mt-6 mb-12">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/60 dark:border-slate-800/60 p-2">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @if(!empty($business->contact_methods['whatsapp']))
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->contact_methods['whatsapp']) }}" target="_blank" 
                   class="group flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 hover:text-emerald-700 dark:hover:text-emerald-400 transition-all duration-200">
                    <svg class="w-5 h-5 text-emerald-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.012 2c-5.508 0-9.987 4.479-9.987 9.988 0 1.757.455 3.47 1.316 4.972l-1.341 4.922 5.035-1.321c1.442.786 3.064 1.2 4.733 1.2 5.507 0 9.988-4.479 9.988-9.988 0-5.508-4.481-9.988-9.988-9.988zm5.952 14.129c-.255.719-1.488 1.309-2.039 1.391-.497.073-.789.243-2.993-.655-2.203-.898-3.626-3.141-3.737-3.289-.112-.149-.912-1.21-.912-2.308 0-1.097.575-1.637.779-1.859.204-.223.446-.279.595-.279.149 0 .297.001.427.007.137.007.319-.052.499.383.181.437.618 1.503.671 1.614.053.111.088.241.014.39-.074.148-.111.241-.223.371-.111.13-.231.291-.329.39-.111.111-.227.233-.098.455.129.223.576.953 1.238 1.541.852.759 1.569 1.01 1.792 1.121.223.111.353.093.484-.056.13-.149.559-.652.707-.869.149-.223.298-.186.502-.112.204.073 1.298.614 1.52.726.223.111.371.167.426.26.056.093.056.541-.199 1.26z"/>
                    </svg>
                    <span>WhatsApp</span>
                </a>
                @endif

                @if(!empty($business->contact_methods['phone']))
                <a href="tel:{{ $business->contact_methods['phone'] }}" 
                   class="group flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/20 hover:text-indigo-700 dark:hover:text-indigo-400 transition-all duration-200">
                    <svg class="w-5 h-5 text-indigo-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span>{{ __('landing.nav_contact') }}</span>
                </a>
                @endif

                @if(!empty($business->contact_methods['website']))
                <a href="{{ $business->contact_methods['website'] }}" target="_blank" 
                   class="group flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200">
                    <svg class="w-5 h-5 text-slate-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span>Website</span>
                </a>
                @endif

                @if(!empty($business->contact_methods['instagram']))
                <a href="{{ $business->contact_methods['instagram'] }}" target="_blank" 
                   class="group flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-pink-50 dark:hover:bg-pink-950/20 hover:text-pink-700 dark:hover:text-pink-400 transition-all duration-200">
                    <svg class="w-5 h-5 text-pink-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                    <span>Instagram</span>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- GALLERY SECTION --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @php
        $count = $business->media->count();
        $media = $business->media;
    @endphp

    @if($count > 0)
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pb-20">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Gallery</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $count }} {{ $count === 1 ? 'photo' : 'photos' }}</p>
            </div>
        </div>

        <div class="grid gap-3">
            @if($count === 1)
                {{-- Single image: full width --}}
                <div class="group relative aspect-[16/9] rounded-2xl overflow-hidden cursor-pointer bg-slate-200 dark:bg-slate-800" 
                     onclick="openLightbox('{{ asset('storage/' . $media[0]->file_path) }}', '{{ $media[0]->caption }}')">
                    <img src="{{ asset('storage/' . $media[0]->file_path) }}" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @if($media[0]->caption)
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <p class="text-white text-sm font-medium">{{ $media[0]->caption }}</p>
                    </div>
                    @endif
                </div>

            @elseif($count === 2)
                {{-- Two images: side by side --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($media as $item)
                    <div class="group relative aspect-[4/3] rounded-2xl overflow-hidden cursor-pointer bg-slate-200 dark:bg-slate-800"
                         onclick="openLightbox('{{ asset('storage/' . $item->file_path) }}', '{{ $item->caption }}')">
                        <img src="{{ asset('storage/' . $item->file_path) }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @if($item->caption)
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-5">
                            <p class="text-white text-sm font-medium">{{ $item->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

            @elseif($count === 3)
                {{-- Three images: featured + two stacked --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="group relative aspect-[4/3] sm:aspect-auto sm:row-span-2 rounded-2xl overflow-hidden cursor-pointer bg-slate-200 dark:bg-slate-800"
                         onclick="openLightbox('{{ asset('storage/' . $media[0]->file_path) }}', '{{ $media[0]->caption }}')">
                        <img src="{{ asset('storage/' . $media[0]->file_path) }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @if($media[0]->caption)
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-5">
                            <p class="text-white text-sm font-medium">{{ $media[0]->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @foreach($media->slice(1, 2) as $item)
                    <div class="group relative aspect-[4/3] rounded-2xl overflow-hidden cursor-pointer bg-slate-200 dark:bg-slate-800"
                         onclick="openLightbox('{{ asset('storage/' . $item->file_path) }}', '{{ $item->caption }}')">
                        <img src="{{ asset('storage/' . $item->file_path) }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @if($item->caption)
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-5">
                            <p class="text-white text-sm font-medium">{{ $item->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

            @elseif($count === 4)
                {{-- Four images: 2x2 grid --}}
                <div class="grid grid-cols-2 gap-3">
                    @foreach($media as $item)
                    <div class="group relative aspect-[4/3] rounded-2xl overflow-hidden cursor-pointer bg-slate-200 dark:bg-slate-800"
                         onclick="openLightbox('{{ asset('storage/' . $item->file_path) }}', '{{ $item->caption }}')">
                        <img src="{{ asset('storage/' . $item->file_path) }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @if($item->caption)
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <p class="text-white text-sm font-medium">{{ $item->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

            @else
                {{-- 5+ images: masonry-style with "more" overlay --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($media->take(6) as $index => $item)
                    <div class="group relative {{ $index === 0 ? 'col-span-2 sm:col-span-2 sm:row-span-2 aspect-[16/9] sm:aspect-auto' : 'aspect-square' }} rounded-2xl overflow-hidden cursor-pointer bg-slate-200 dark:bg-slate-800"
                         onclick="openLightbox('{{ asset('storage/' . $item->file_path) }}', '{{ $item->caption }}')">
                        <img src="{{ asset('storage/' . $item->file_path) }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                        @if($index === 5 && $count > 6)
                        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm flex flex-col items-center justify-center text-white">
                            <span class="text-2xl font-bold">+{{ $count - 6 }}</span>
                            <span class="text-xs font-medium opacity-70 mt-1">more</span>
                        </div>
                        @elseif($item->caption && !($index === 5 && $count > 6))
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <p class="text-white text-sm font-medium">{{ $item->caption }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- LIGHTBOX --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div id="lightbox" class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 flex items-center justify-center p-4" onclick="if(event.target === this) closeLightbox()">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 sm:top-6 sm:right-6 text-white/60 hover:text-white transition-colors p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div id="lightbox-content" class="max-w-5xl w-full max-h-[85vh] flex flex-col items-center">
            <img id="lightbox-img" src="" alt="" class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-2xl">
            <p id="lightbox-caption" class="mt-4 text-white/80 text-sm font-medium text-center"></p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    (function() {
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxCaption = document.getElementById('lightbox-caption');

        window.openLightbox = function(src, caption) {
            lightboxImg.src = src;
            lightboxCaption.textContent = caption || '';
            lightbox.classList.remove('pointer-events-none');
            lightbox.classList.remove('opacity-0');
            document.body.style.overflow = 'hidden';
        };

        window.closeLightbox = function() {
            lightbox.classList.add('opacity-0');
            setTimeout(() => {
                lightbox.classList.add('pointer-events-none');
                lightboxImg.src = '';
                document.body.style.overflow = '';
            }, 300);
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });
    })();
</script>
@endpush