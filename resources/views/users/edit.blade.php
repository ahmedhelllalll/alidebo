@extends('users.layout')
@section('title', __('forms.business.edit_title') . ' | ' . $business->name)
@section('page_title', __('nav.edit_profile') ?? 'Edit Profile')

@section('content')
{{-- ═══ TOAST NOTIFICATION SYSTEM ═══ --}}
<div id="toast-container" class="fixed top-5 end-5 z-[9999] flex flex-col gap-3 pointer-events-none w-[340px]"></div>

{{-- ═══ VALIDATION ERRORS ═══ --}}
@if($errors->any())
<div class="max-w-6xl mx-auto mb-6">
    <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200/60 dark:border-rose-500/20 rounded-2xl p-5">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-500/15 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-rose-800 dark:text-rose-300 mb-1.5">{{ __('forms.business.error') ?? 'Please fix the following issues' }}</p>
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-xs text-rose-600 dark:text-rose-400 flex items-center gap-2">
                        <span class="w-1 h-1 rounded-full bg-rose-400 shrink-0"></span>{{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endif

<div class="max-w-6xl mx-auto pb-32">
    <form id="editForm" action="{{ route('business.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="cover_source"           id="coverSourceInput"    value="existing">
        <input type="hidden" name="selected_category_cover" id="selectedCategoryCover" value="">

        <div class="flex flex-col lg:flex-row gap-6 items-start">

            {{-- ═══ STICKY LEFT SIDEBAR ═══ --}}
            <div class="shrink-0 w-full lg:w-[232px] sticky top-24 z-10 hidden lg:flex flex-col gap-4">
                {{-- Navigation --}}
                <div class="bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl p-2.5 shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)]">
                    <p class="px-3 pt-2 pb-2 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('forms.business.sections') ?? 'Sections' }}</p>
                    <nav id="settingsNav" class="flex flex-col gap-0.5">
                        @foreach([
                            ['id'=>'basic',    'label'=>(__('forms.business.tab_basic') ?? 'Basic Info'),        'path'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                            ['id'=>'visual',   'label'=>(__('forms.business.visual_identity') ?? 'Visuals'),    'path'=>'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['id'=>'location', 'label'=>(__('forms.business.location') ?? 'Location'),          'path'=>'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                            ['id'=>'social',   'label'=>(__('forms.business.tab_social') ?? 'Social & Contact'),'path'=>'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
                            ['id'=>'gallery',  'label'=>(__('forms.business.tab_gallery') ?? 'Gallery'),        'path'=>'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ] as $nav)
                        <a href="#{{ $nav['id'] }}" data-section="{{ $nav['id'] }}"
                           class="settings-nav-btn group flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold transition-all duration-200 text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-white/[0.05]">
                            <div class="nav-icon w-8 h-8 rounded-[10px] flex items-center justify-center bg-zinc-100 dark:bg-zinc-800/80 transition-all duration-200 shrink-0 group-hover:bg-zinc-200/70 dark:group-hover:bg-zinc-700/60">
                                <svg class="w-4 h-4 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $nav['path'] }}"/></svg>
                            </div>
                            <span>{{ $nav['label'] }}</span>
                        </a>
                        @endforeach
                    </nav>
                </div>

                {{-- Profile Health --}}
                @php
                    $checks = [
                        ['label'=>'Business Name', 'done'=>!empty($business->name)],
                        ['label'=>'Description',   'done'=>!empty($business->description)],
                        ['label'=>'Logo',          'done'=>!empty($business->logo)],
                        ['label'=>'Cover Image',   'done'=>!empty($business->cover)],
                        ['label'=>'Location',      'done'=>!empty($business->city_id)],
                        ['label'=>'Contact Info',  'done'=>!empty(array_filter($business->contact_methods ?? []))],
                        ['label'=>'Gallery',       'done'=>$business->media->count() > 0],
                    ];
                    $done = count(array_filter(array_column($checks, 'done')));
                    $pct  = round(($done / count($checks)) * 100);
                @endphp
                <div class="bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl p-4 shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)]">
                    <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3">{{ __('forms.business.profile_health') ?? 'Profile Health' }}</p>
                    <div class="space-y-2 mb-3">
                        @foreach($checks as $c)
                        <div class="flex items-center gap-2.5">
                            @if($c['done'])
                                <div class="w-4 h-4 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <svg class="w-2.5 h-2.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">{{ $c['label'] }}</span>
                            @else
                                <div class="w-4 h-4 rounded-full border-[1.5px] border-zinc-200 dark:border-zinc-700 shrink-0"></div>
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $c['label'] }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="pt-3 border-t border-black/[0.04] dark:border-white/[0.06]">
                        <div class="flex justify-between text-[11px] font-bold text-zinc-400 dark:text-zinc-500 mb-2">
                            <span>{{ __('forms.business.completeness') ?? 'Completeness' }}</span><span class="text-primary">{{ $pct }}%</span>
                        </div>
                        <div class="h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-primary to-primary/80 rounded-full transition-all duration-700" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ MAIN CONTENT ═══ --}}
            <div class="flex-1 w-full space-y-6">

                {{-- ── 1. BASIC INFO ── --}}
                <section id="basic" class="scroll-mt-28 bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl  shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)]">
                    <div class="px-6 md:px-8 py-5 border-b border-black/[0.04] dark:border-white/[0.06] flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-100 dark:border-zinc-700/50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[13px] font-bold text-zinc-900 dark:text-white tracking-tight">{{ __('forms.business.basic_info') ?? 'Basic Information' }}</h3>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">{{ __('forms.business.basic_info_desc') ?? 'Name, category and description' }}</p>
                        </div>
                    </div>
                    <div class="p-6 md:p-8 space-y-6"
                         x-data="{ catId: {{ $business->category_id ?? 'null' }}, catName: '{{ addslashes($business->category?->name ?? '') }}', catOpen: false }">

                        {{-- Business Name --}}
                        <div class="space-y-2">
                            <label for="biz_name" class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.biz_name') ?? 'Business Name' }} <span class="text-primary">*</span></label>
                            <input type="text" name="name" id="biz_name" value="{{ old('name', $business->name) }}" required
                                   class="w-full bg-white dark:bg-zinc-900/80 border {{ $errors->has('name') ? 'border-rose-400 dark:border-rose-500' : 'border-zinc-200 dark:border-zinc-800' }} rounded-xl px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition-all duration-200 placeholder:text-zinc-400 hover:border-zinc-300 dark:hover:border-zinc-700">
                            @error('name')
                            <p class="text-[11px] text-rose-500 dark:text-rose-400 font-medium flex items-center gap-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                            @else
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500">{{ __('forms.business.biz_name_hint') ?? 'Your business name as it will appear to customers' }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.category') ?? 'Category' }} <span class="text-primary">*</span></label>
                            <input type="hidden" name="category_id" :value="catId">
                            <div class="relative">
                                <button type="button" @click="catOpen = !catOpen"
                                        class="w-full text-start px-4 py-3 bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xl flex justify-between items-center text-sm font-medium text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary/15 focus:border-primary transition-all duration-200 hover:border-zinc-300 dark:hover:border-zinc-700">
                                    <span x-text="catName || 'Select Category'" :class="!catName ? 'text-zinc-400' : ''"></span>
                                    <svg class="w-4 h-4 text-zinc-400 transition-transform" :class="catOpen?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="catOpen" @click.away="catOpen=false"
                                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                     class="absolute start-0 end-0 mt-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-[0_16px_48px_rgba(0,0,0,0.12)] dark:shadow-[0_16px_48px_rgba(0,0,0,0.3)] z-50 max-h-60 overflow-y-auto" style="display:none">
                                    @foreach($categories as $cat)
                                    <button type="button"
                                            @click="catId={{ $cat->id }}; catName='{{ addslashes($cat->name) }}'; catOpen=false"
                                            :class="catId==={{ $cat->id }} ? 'bg-primary/5 text-primary font-bold' : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                                            class="w-full text-start px-4 py-2.5 text-sm transition-colors flex items-center gap-3 border-b border-zinc-50 dark:border-zinc-800/50 last:border-0">

                                        {{ $cat->name }}
                                        <svg x-show="catId==={{ $cat->id }}" class="w-3.5 h-3.5 text-primary ms-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.biz_desc') ?? 'Description' }}</label>
                            <textarea name="description" rows="4"
                                      class="w-full bg-white dark:bg-zinc-900/80 border {{ $errors->has('description') ? 'border-rose-400 dark:border-rose-500' : 'border-zinc-200 dark:border-zinc-800' }} rounded-xl px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none resize-none transition-all duration-200 placeholder:text-zinc-400 hover:border-zinc-300 dark:hover:border-zinc-700">{{ old('description', $business->description) }}</textarea>
                            @error('description')
                            <p class="text-[11px] text-rose-500 dark:text-rose-400 font-medium flex items-center gap-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                            @else
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500">{{ __('forms.business.biz_desc_hint') ?? 'Brief overview of your business, services, and specialties' }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- ── 2. VISUAL IDENTITY ── --}}
                <section id="visual" class="scroll-mt-28 bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)]">
                    <div class="px-6 md:px-8 py-5 border-b border-black/[0.04] dark:border-white/[0.06] flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-100 dark:border-zinc-700/50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[13px] font-bold text-zinc-900 dark:text-white tracking-tight">{{ __('forms.business.visual_identity') ?? 'Visual Identity' }}</h3>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">{{ __('forms.business.visual_identity_desc') ?? 'Logo and cover image for your profile' }}</p>
                        </div>
                    </div>
                    <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Logo --}}
                        <div class="space-y-2.5">
                            <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.logo') ?? 'Logo' }}</label>
                            <div class="relative h-52 bg-zinc-50 dark:bg-zinc-900/60 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center justify-center cursor-pointer group hover:border-primary/50 hover:bg-primary/[0.02] transition-all duration-300"
                                 onclick="document.getElementById('logoInput').click()">
                                <img id="logoPreview" src="{{ $business->logo ? asset('storage/'.$business->logo) : '' }}"
                                     class="absolute inset-0 w-full h-full object-contain p-4 rounded-xl {{ $business->logo ? '' : 'hidden' }}">
                                <div id="logoPlaceholder" class="text-center text-zinc-400 group-hover:text-primary transition-colors {{ $business->logo ? 'hidden' : '' }}">
                                    <div class="w-11 h-11 rounded-xl bg-zinc-100 dark:bg-zinc-800 group-hover:bg-primary/10 flex items-center justify-center mx-auto mb-2 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                    <p class="text-xs font-bold">Click to upload</p>
                                    <p class="text-[10px] mt-1 opacity-60">PNG, JPG, WebP – Max 2MB</p>
                                </div>
                                <button type="button" id="clearLogo" onclick="event.stopPropagation();clearImage('logoPreview','logoPlaceholder','logoInput')"
                                        class="absolute top-2 end-2 w-6 h-6 bg-zinc-900/60 hover:bg-rose-500 text-white rounded-full items-center justify-center transition-colors backdrop-blur-sm {{ $business->logo ? 'flex' : 'hidden' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <input type="file" name="logo" id="logoInput" class="hidden" accept="image/*"
                                   onchange="previewImage(this,'logoPreview','logoPlaceholder');document.getElementById('clearLogo').classList.replace('hidden','flex')">
                        </div>

                        {{-- Cover --}}
                        <div class="space-y-2.5">
                            <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.cover_image') ?? 'Cover Image' }}</label>
                            <div class="relative h-52 bg-zinc-50 dark:bg-zinc-900/60 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center justify-center cursor-pointer group hover:border-primary/50 hover:bg-primary/[0.02] transition-all duration-300"
                                 onclick="document.getElementById('coverInput').click()">
                                <img id="coverPreview" src="{{ $business->cover ? asset('storage/'.$business->cover) : '' }}"
                                     class="absolute inset-0 w-full h-full object-cover rounded-xl {{ $business->cover ? '' : 'hidden' }}">
                                <div id="coverPlaceholder" class="text-center text-zinc-400 group-hover:text-primary transition-colors relative z-10 {{ $business->cover ? 'hidden' : '' }}">
                                    <div class="w-11 h-11 rounded-xl bg-zinc-100 dark:bg-zinc-800 group-hover:bg-primary/10 flex items-center justify-center mx-auto mb-2 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-xs font-bold">Click to upload</p>
                                    <p class="text-[10px] mt-1 opacity-60">PNG, JPG, WebP – Max 3MB</p>
                                </div>
                            </div>
                            <input type="file" name="cover" id="coverInput" class="hidden" accept="image/*"
                                   onchange="previewImage(this,'coverPreview','coverPlaceholder');document.getElementById('coverSourceInput').value='upload'">
                        </div>

                    </div>
                </section>

                {{-- ── 3. LOCATION ── --}}
                <section id="location" class="scroll-mt-28 z-10 bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl  shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)]"
                    x-data="{
                        selCountry: {{ $business->city?->country_id ?? 'null' }},
                        selCountryName: '{{ addslashes($business->city?->country?->name ?? '') }}',
                        selCity: {{ $business->city_id ?? 'null' }},
                        selCityName: '{{ addslashes($business->city?->name ?? '') }}',
                        cOpen: false, ctOpen: false,
                        countries: {{ $countries->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'cities'=>$c->cities->map(fn($ci)=>['id'=>$ci->id,'name'=>$ci->name])->values()])->values()->toJson() }},
                        get cities(){ const c=this.countries.find(c=>c.id===this.selCountry); return c?c.cities:[]; },
                        pickCountry(id,name){ this.selCountry=id;this.selCountryName=name;this.selCity=null;this.selCityName='';this.cOpen=false; },
                        pickCity(id,name){ this.selCity=id;this.selCityName=name;this.ctOpen=false; }
                    }">
                    <div class="px-6 md:px-8 py-5 border-b border-black/[0.04] dark:border-white/[0.06] flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-100 dark:border-zinc-700/50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[13px] font-bold text-zinc-900 dark:text-white tracking-tight">{{ __('forms.business.location') ?? 'Location' }}</h3>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">{{ __('forms.business.location_desc') ?? 'Country, city and street address' }}</p>
                        </div>
                    </div>
                    <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Country --}}
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.country') ?? 'Country' }} <span class="text-primary">*</span></label>
                            <div class="relative">
                                <button type="button" @click="cOpen=!cOpen"
                                        class="w-full text-start px-4 py-3 bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xl flex justify-between items-center text-sm font-medium transition-all duration-200 focus:ring-2 focus:ring-primary/15 focus:border-primary hover:border-zinc-300 dark:hover:border-zinc-700">
                                    <span x-text="selCountryName||'Select Country'" :class="!selCountryName?'text-zinc-400':'text-zinc-900 dark:text-white'"></span>
                                    <svg class="w-4 h-4 text-zinc-400 transition-transform" :class="cOpen?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="cOpen" @click.away="cOpen=false"
                                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                     class="absolute start-0 end-0 mt-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-[0_16px_48px_rgba(0,0,0,0.12)] z-50 max-h-56 overflow-y-auto" style="display:none">
                                    <template x-for="c in countries" :key="c.id">
                                        <button type="button" @click="pickCountry(c.id,c.name)"
                                                :class="selCountry===c.id?'bg-primary/5 text-primary font-bold':'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                                                class="w-full text-start px-4 py-2.5 text-sm transition-colors flex items-center justify-between border-b border-zinc-50 dark:border-zinc-800/50 last:border-0">
                                            <span x-text="c.name"></span>
                                            <svg x-show="selCountry===c.id" class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- City (cascades from Country) --}}
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.city') ?? 'City' }} <span class="text-primary">*</span></label>
                            <input type="hidden" name="city_id" :value="selCity">
                            <div class="relative">
                                <button type="button" @click="selCountry && (ctOpen=!ctOpen)" :disabled="!selCountry"
                                        :class="!selCountry?'opacity-50 cursor-not-allowed':''"
                                        class="w-full text-start px-4 py-3 bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xl flex justify-between items-center text-sm font-medium transition-all duration-200 focus:ring-2 focus:ring-primary/15 focus:border-primary hover:border-zinc-300 dark:hover:border-zinc-700">
                                    <span x-text="selCityName||(selCountry?'Select City':'Choose country first')" :class="!selCityName?'text-zinc-400':'text-zinc-900 dark:text-white'"></span>
                                    <svg class="w-4 h-4 text-zinc-400 transition-transform" :class="ctOpen?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="ctOpen" @click.away="ctOpen=false"
                                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                     class="absolute start-0 end-0 mt-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-[0_16px_48px_rgba(0,0,0,0.12)] z-40 max-h-56 overflow-y-auto" style="display:none">
                                    <template x-for="ci in cities" :key="ci.id">
                                        <button type="button" @click="pickCity(ci.id,ci.name)"
                                                :class="selCity===ci.id?'bg-primary/5 text-primary font-bold':'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                                                class="w-full text-start px-4 py-2.5 text-sm transition-colors flex items-center justify-between border-b border-zinc-50 dark:border-zinc-800/50 last:border-0">
                                            <span x-text="ci.name"></span>
                                            <svg x-show="selCity===ci.id" class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </template>
                                    <div x-show="cities.length===0" class="px-4 py-5 text-center text-xs text-zinc-400">No cities available</div>
                                </div>
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('forms.business.address') ?? 'Street Address' }}</label>
                            <input type="text" name="address" value="{{ old('address', $business->address) }}"
                                   placeholder="e.g. 123 King Fahd Road, Al Olaya District"
                                   class="w-full bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition-all duration-200 placeholder:text-zinc-400 hover:border-zinc-300 dark:hover:border-zinc-700">
                        </div>
                    </div>
                </section>

                {{-- ── 4. SOCIAL & CONTACT ── --}}
                <section id="social" class="scroll-mt-28 bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)]">
                    <div class="px-6 md:px-8 py-5 border-b border-black/[0.04] dark:border-white/[0.06] flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-100 dark:border-zinc-700/50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[13px] font-bold text-zinc-900 dark:text-white tracking-tight">{{ __('forms.business.tab_social') ?? 'Social & Contact' }}</h3>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">{{ __('forms.business.social_desc') ?? 'Connect your digital presence with customers' }}</p>
                        </div>
                    </div>
                    <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach([
                            ['field'=>'whatsapp', 'label'=>'WhatsApp',  'placeholder'=>'+966 50 XXX XXXX', 'type'=>'text'],
                            ['field'=>'phone',    'label'=>'Phone',     'placeholder'=>'+966 X XXX XXXX',  'type'=>'text'],
                            ['field'=>'website',  'label'=>'Website',   'placeholder'=>'https://yoursite.com', 'type'=>'url'],
                            ['field'=>'facebook', 'label'=>'Facebook',  'placeholder'=>'https://facebook.com/...','type'=>'url'],
                            ['field'=>'instagram','label'=>'Instagram', 'placeholder'=>'https://instagram.com/...','type'=>'url'],
                        ] as $sf)
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ $sf['label'] }}</label>
                            <div class="flex items-center bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/15 transition-all duration-200 hover:border-zinc-300 dark:hover:border-zinc-700">
                                <div class="w-11 h-11 flex items-center justify-center text-zinc-400 shrink-0 border-e border-zinc-200/80 dark:border-zinc-800">
                                    @if($sf['field']==='whatsapp')    <svg class="w-4 h-4 text-emerald-500 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z" fill="currentColor"/></svg>
                                    @elseif($sf['field']==='phone')   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    @elseif($sf['field']==='facebook') <svg class="w-4 h-4 text-blue-500 fill-current" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                    @elseif($sf['field']==='instagram')<svg class="w-4 h-4 text-pink-500 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    @else <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                    @endif
                                </div>
                                <input type="{{ $sf['type'] }}" name="{{ $sf['field'] }}"
                                       value="{{ old($sf['field'], $business->contact_methods[$sf['field']] ?? '') }}"
                                       placeholder="{{ $sf['placeholder'] }}"
                                       class="flex-1 bg-transparent border-none outline-none focus:ring-0 px-3 py-3 text-sm font-medium text-zinc-900 dark:text-white placeholder:text-zinc-400">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>

                {{-- ── 5. GALLERY ── --}}
                <section id="gallery" class="scroll-mt-28 bg-white dark:bg-zinc-900 border border-black/[0.04] dark:border-white/[0.06] rounded-2xl overflow-hidden shadow-[0_2px_12px_rgba(0,0,0,0.04)] dark:shadow-[0_2px_12px_rgba(0,0,0,0.15)]">
                    <div class="px-6 md:px-8 py-5 border-b border-black/[0.04] dark:border-white/[0.06] flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-100 dark:border-zinc-700/50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-[13px] font-bold text-zinc-900 dark:text-white tracking-tight">{{ __('forms.business.tab_gallery') ?? 'Gallery' }}</h3>
                                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5"><span id="mediaCount">{{ $business->media->count() }}</span>/10 {{ __('forms.business.max_images') ?? 'images · drag to reorder' }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="document.getElementById('galleryInput').click()" id="addImagesBtn"
                                class="flex items-center gap-2 px-4 py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl text-xs font-bold hover:bg-zinc-700 dark:hover:bg-zinc-100 transition-all active:scale-[0.98] shadow-sm whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            {{ __('forms.business.add_new_images') ?? 'Add Images' }}
                        </button>
                    </div>

                    <div class="p-6 md:p-8">
                        <input type="file" id="galleryInput" class="hidden" multiple accept="image/*">

                        {{-- Upload Progress --}}
                        <div id="uploadProgress" class="hidden mb-5 p-4 bg-primary/5 border border-primary/20 rounded-xl">
                            <div class="flex justify-between items-center text-xs font-bold text-primary mb-2">
                                <span>Uploading images…</span><span id="uploadPercent">0%</span>
                            </div>
                            <div class="h-1.5 bg-primary/20 rounded-full overflow-hidden">
                                <div id="uploadBar" class="h-full bg-primary rounded-full transition-all duration-300" style="width:0%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" id="galleryGrid">
                            @forelse($business->media as $media)
                            <div class="group relative aspect-square bg-zinc-50 dark:bg-zinc-900/50 border border-black/[0.04] dark:border-white/[0.06] rounded-xl overflow-hidden shadow-sm" data-id="{{ $media->id }}">
                                <img src="{{ asset('storage/'.$media->file_path) }}" class="w-full h-full object-cover pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/80 via-zinc-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-3">
                                    <div class="flex justify-end">
                                        <button type="button" onclick="deleteMedia({{ $media->id }},this)"
                                                class="w-7 h-7 bg-rose-500 hover:bg-rose-600 text-white rounded-lg flex items-center justify-center transition-colors shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                    <input type="text" value="{{ $media->caption }}"
                                           class="w-full bg-black/40 backdrop-blur-sm border border-white/20 rounded-lg px-2.5 py-1.5 text-xs text-white placeholder:text-white/50 outline-none focus:border-white/60 transition-colors"
                                           placeholder="Add caption…"
                                           onchange="updateCaption({{ $media->id }},this.value)">
                                </div>
                                <div class="absolute top-2 start-2 w-6 h-6 bg-zinc-900/40 backdrop-blur-sm rounded-lg flex items-center justify-center cursor-grab text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/></svg>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full py-16 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl" id="emptyGalleryState">
                                <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-zinc-500 dark:text-zinc-400">{{ __('forms.business.no_images_uploaded') ?? 'No images yet' }}</p>
                                <p class="text-xs text-zinc-400 mt-1">Click "Add Images" to upload up to 10 photos</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </section>

            </div>{{-- /flex-1 --}}
        </div>{{-- /flex-row --}}

        {{-- ═══ STICKY SAVE BAR ═══ --}}
        <div class="fixed bottom-0 inset-x-0 z-50 px-4 pb-4 pointer-events-none">
            <div class="max-w-6xl mx-auto pointer-events-auto">
                <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-2xl border border-black/[0.06] dark:border-white/[0.08] rounded-2xl px-6 py-4 shadow-[0_-4px_32px_rgba(0,0,0,0.08)] dark:shadow-[0_-4px_32px_rgba(0,0,0,0.3)] flex items-center justify-between gap-4">
                    <div class="hidden sm:flex items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                        <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('forms.business.edit_title') ?? 'Editing' }}</span>
                            <span class="text-xs font-bold text-zinc-900 dark:text-white truncate max-w-[200px]">{{ $business->name }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 ms-auto">
                        <a href="{{ route('business.index') }}" class="px-5 py-2.5 text-sm font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-all duration-200 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            {{ __('forms.business.cancel') ?? 'Cancel' }}
                        </a>
                        <button type="submit" id="saveBtn"
                                class="flex items-center gap-2 px-7 py-2.5 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30 hover:bg-primary/90 active:scale-[0.97] transition-all duration-200">
                            <svg id="saveBtnIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span id="saveBtnText">{{ __('forms.business.save_changes') ?? 'Save Changes' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

@push('scripts')

<script>
/* ═══════════════════════════════════════════════
   TOAST NOTIFICATION SYSTEM
═══════════════════════════════════════════════ */
function showToast(type, title, message, duration = 4000) {
    const container = document.getElementById('toast-container');
    const icons = {
        success: `<div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/15 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>`,
        error:   `<div class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-500/15 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div>`,
        warning: `<div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/15 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>`,
        info:    `<div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"><svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>`,
    };
    const toast = document.createElement('div');
    toast.className = 'pointer-events-auto bg-white dark:bg-[#18181b] border border-black/5 dark:border-white/[0.06] rounded-2xl p-4 shadow-[0_8px_32px_rgba(0,0,0,0.12)] flex items-start gap-3 transform transition-all duration-300 translate-x-full opacity-0';
    toast.innerHTML = `${icons[type]||icons.info}<div class="flex-1 min-w-0"><p class="text-xs font-bold text-zinc-900 dark:text-white">${title}</p>${message?`<p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 leading-relaxed">${message}</p>`:''}</div><button onclick="this.closest('div[class*=rounded-2xl]').remove()" class="text-zinc-300 hover:text-zinc-500 dark:text-zinc-600 dark:hover:text-zinc-400 transition-colors shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
    container.appendChild(toast);
    requestAnimationFrame(() => { toast.classList.remove('translate-x-full','opacity-0'); });
    setTimeout(() => { toast.classList.add('translate-x-full','opacity-0'); setTimeout(() => toast.remove(), 300); }, duration);
}

function confirmDialog(title, message, confirmText, onConfirm) {
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm z-[9998] flex items-center justify-center p-4';
    overlay.innerHTML = `
        <div class="bg-white dark:bg-[#18181b] border border-black/5 dark:border-white/[0.06] rounded-2xl p-6 shadow-[0_24px_64px_rgba(0,0,0,0.2)] max-w-sm w-full transform scale-95 opacity-0 transition-all duration-200">
            <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-500/15 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white mb-1">${title}</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-5">${message}</p>
            <div class="flex gap-3">
                <button id="cancelBtn" class="flex-1 px-4 py-2.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl text-sm font-semibold hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                <button id="confirmBtn" class="flex-1 px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-sm font-bold transition-colors shadow-sm">${confirmText||'Delete'}</button>
            </div>
        </div>`;
    document.body.appendChild(overlay);
    const modal = overlay.querySelector('div');
    requestAnimationFrame(() => { modal.classList.remove('scale-95','opacity-0'); });
    overlay.querySelector('#cancelBtn').onclick = () => { modal.classList.add('scale-95','opacity-0'); setTimeout(()=>overlay.remove(),200); };
    overlay.querySelector('#confirmBtn').onclick = () => { modal.classList.add('scale-95','opacity-0'); setTimeout(()=>overlay.remove(),200); onConfirm(); };
    overlay.onclick = (e) => { if(e.target===overlay){ modal.classList.add('scale-95','opacity-0'); setTimeout(()=>overlay.remove(),200); } };
}

/* ═══ HELPERS ═══ */
function previewImage(input, previewId, placeholderId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        if(placeholder) placeholder.classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
}

function clearImage(previewId, placeholderId, inputId) {
    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    const input = document.getElementById(inputId);
    preview.src = ''; preview.classList.add('hidden');
    if(placeholder) placeholder.classList.remove('hidden');
    if(input) input.value = '';
}

function selectCategoryCover(url, catId, el) {
    document.getElementById('coverPreview').src = url;
    document.getElementById('coverPreview').classList.remove('hidden');
    document.getElementById('coverPlaceholder')?.classList.add('hidden');
    document.getElementById('coverSourceInput').value = 'from_category';
    document.getElementById('selectedCategoryCover').value = catId;
    document.querySelectorAll('[id^="cat-thumb-"]').forEach(img => img.classList.remove('border-primary'));
    el.querySelector('img').classList.add('border-primary');
}

/* ═══ SCROLL SPY NAVIGATION ═══ */
document.querySelectorAll('.settings-nav-btn').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        const target = document.querySelector(a.getAttribute('href'));
        if(target) window.scrollTo({ top: target.offsetTop - 96, behavior: 'smooth' });
    });
});
const spy = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if(!entry.isIntersecting) return;
        document.querySelectorAll('.settings-nav-btn').forEach(btn => {
            const isActive = btn.dataset.section === entry.target.id;
            btn.classList.toggle('bg-primary/10', isActive);
            btn.classList.toggle('text-primary', isActive);
            btn.classList.toggle('font-bold', isActive);
            btn.querySelector('.nav-icon').classList.toggle('bg-primary/15', isActive);
            if(!isActive) {
                btn.classList.remove('bg-primary/10','text-primary','font-bold');
                btn.querySelector('.nav-icon').classList.remove('bg-primary/15');
            }
        });
    });
}, { rootMargin: '-20% 0px -70% 0px' });
document.querySelectorAll('#basic,#visual,#location,#social,#gallery').forEach(s => spy.observe(s));

/* ═══ GALLERY — SORTABLE DRAG & DROP ═══ */
const galleryGrid = document.getElementById('galleryGrid');
if(galleryGrid) {
    new Sortable(galleryGrid, {
        animation: 200,
        ghostClass: 'opacity-30',
        handle: '[class*="cursor-grab"]',
        onEnd() {
            const order = [...galleryGrid.children].map(el => el.dataset.id).filter(Boolean);
            if(!order.length) return;
            fetch('{{ route("business.media.order") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ order })
            }).then(r => r.json()).then(d => { if(d.success) showToast('info','Order saved','Gallery order has been updated.', 2000); });
        }
    });
}

/* ═══ GALLERY — AJAX UPLOAD ═══ */
document.getElementById('galleryInput').addEventListener('change', function() {
    if(!this.files.length) return;
    const currentCount = galleryGrid.querySelectorAll('[data-id]').length;
    if(currentCount + this.files.length > 10) {
        showToast('warning','Limit reached',`You can only have 10 images total. You have ${currentCount} currently.`);
        this.value = ''; return;
    }
    const formData = new FormData();
    [...this.files].forEach(f => formData.append('images[]', f));

    const progressEl = document.getElementById('uploadProgress');
    const bar = document.getElementById('uploadBar');
    const pct = document.getElementById('uploadPercent');
    progressEl.classList.remove('hidden');
    bar.style.width = '0%'; pct.textContent = '0%';

    const xhr = new XMLHttpRequest();
    xhr.upload.onprogress = e => {
        if(e.lengthComputable) {
            const p = Math.round((e.loaded/e.total)*100);
            bar.style.width = p+'%'; pct.textContent = p+'%';
        }
    };
    xhr.onload = () => {
        progressEl.classList.add('hidden');
        this.value = '';
        try {
            const data = JSON.parse(xhr.responseText);
            if(xhr.status >= 200 && xhr.status < 300 && data.success) {
                showToast('success','Images uploaded','Your gallery has been updated successfully.');
                const empty = document.getElementById('emptyGalleryState');
                if(empty) empty.remove();
                data.media.forEach(m => {
                    const div = document.createElement('div');
                    div.className = 'group relative aspect-square bg-zinc-50 dark:bg-zinc-900/50 border border-black/5 dark:border-white/[0.04] rounded-xl overflow-hidden shadow-sm';
                    div.dataset.id = m.id;
                    div.innerHTML = `<img src="${m.file_path}" class="w-full h-full object-cover pointer-events-none">
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/80 via-zinc-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-3">
                            <div class="flex justify-end"><button type="button" onclick="deleteMedia(${m.id},this)" class="w-7 h-7 bg-rose-500 hover:bg-rose-600 text-white rounded-lg flex items-center justify-center transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></div>
                            <input type="text" class="w-full bg-black/40 backdrop-blur-sm border border-white/20 rounded-lg px-2.5 py-1.5 text-xs text-white placeholder:text-white/50 outline-none focus:border-white/60" placeholder="Add caption…" onchange="updateCaption(${m.id},this.value)">
                        </div>
                        <div class="absolute top-2 start-2 w-6 h-6 bg-zinc-900/40 backdrop-blur-sm rounded-lg flex items-center justify-center cursor-grab text-white opacity-0 group-hover:opacity-100 transition-opacity"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/></svg></div>`;
                    galleryGrid.appendChild(div);
                });
                document.getElementById('mediaCount').textContent = galleryGrid.querySelectorAll('[data-id]').length;
            } else {
                showToast('error','Upload failed', data.error || 'Could not upload images.');
            }
        } catch(e) { showToast('error','Error','Unexpected server response.'); }
    };
    xhr.onerror = () => { progressEl.classList.add('hidden'); showToast('error','Connection error','Please check your connection.'); };
    xhr.open('POST','{{ route("business.media.upload") }}');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    xhr.send(formData);
});

/* ═══ GALLERY — DELETE ═══ */
function deleteMedia(id, btn) {
    confirmDialog('Delete image?','This action cannot be undone.','Delete', () => {
        fetch(`{{ url('dashboard/business/media') }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        }).then(r => r.json()).then(d => {
            if(d.success) {
                btn.closest('[data-id]').remove();
                document.getElementById('mediaCount').textContent = galleryGrid.querySelectorAll('[data-id]').length;
                showToast('success','Image deleted','The image has been removed from your gallery.');
            } else { showToast('error','Delete failed','Could not delete this image.'); }
        }).catch(() => showToast('error','Error','Connection problem.'));
    });
}

/* ═══ GALLERY — UPDATE CAPTION ═══ */
function updateCaption(id, caption) {
    fetch(`{{ url('dashboard/business/media') }}/${id}/caption`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        body: JSON.stringify({ caption })
    }).then(r => r.json()).then(d => {
        if(d.success) showToast('info','Caption saved','', 2000);
    }).catch(() => showToast('error','Error','Could not save caption.'));
}

/* ═══ MAIN FORM SUBMIT ═══ */
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn  = document.getElementById('saveBtn');
    const icon = document.getElementById('saveBtnIcon');
    const text = document.getElementById('saveBtnText');
    btn.disabled = true;
    icon.innerHTML = `<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>`;
    icon.setAttribute('viewBox','0 0 24 24'); icon.classList.add('animate-spin');
    text.textContent = 'Saving…';

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(res => {
        const ct = res.headers.get('content-type') || '';
        if(ct.includes('application/json')) {
            return res.json().then(data => ({ status: res.status, data }));
        }
        return { status: res.status, data: { message: 'Server returned an unexpected response.' } };
    })
    .then(({ status, data }) => {
        btn.disabled = false; icon.classList.remove('animate-spin');
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>`;
        text.textContent = '{{ __("forms.business.save_changes") ?? "Save Changes" }}';

        if(status >= 200 && status < 300 && data.success) {
            showToast('success','Profile updated!','Your changes have been saved successfully.');
            setTimeout(() => { if(data.redirect) window.location.href = data.redirect; }, 1500);
        } else if(status === 422 && data.errors) {
            const msgs = Object.values(data.errors).flat().join(' · ');
            showToast('warning','Please fix these issues', msgs, 6000);
        } else {
            showToast('error','Could not save', data.message || 'An unexpected error occurred.');
        }
    })
    .catch(() => {
        btn.disabled = false; icon.classList.remove('animate-spin');
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>`;
        text.textContent = '{{ __("forms.business.save_changes") ?? "Save Changes" }}';
        showToast('error','Connection error','Please check your connection and try again.');
    });
});
</script>
@endpush
@endsection