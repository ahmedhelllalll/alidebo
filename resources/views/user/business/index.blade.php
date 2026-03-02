@extends('layouts.dashboard.app')

@section('nav_links')
    @if($isEmpty)
        <a href="#hero" class="nav-link">البداية</a>
        <a href="#features" class="nav-link">المميزات</a>
    @else
        <a href="#my-projects" class="nav-link">مشاريعي</a>
        <a href="{{ route('business.create') }}" class="nav-link text-primary">+ إضافة جديد</a>
    @endif
@endsection

@section('content')
<div class="min-h-screen bg-transparent pb-24">
    @if($isEmpty)
        <div class="max-w-7xl mx-auto px-6 pt-12">
            <div class="relative flex flex-col items-center text-center py-20 overflow-hidden rounded-[3rem] bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 shadow-sm">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-accent/5 rounded-full blur-3xl"></div>

                <div class="relative z-10 space-y-8">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-xs font-black uppercase tracking-widest">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        منصة علي ديبو للشركات
                    </div>

                    <h1 class="text-5xl md:text-7xl font-black text-zinc-900 dark:text-white tracking-tighter leading-tight">
                        ابدأ رحلتك الرقمية <br> <span class="text-primary text-transparent bg-clip-text bg-gradient-to-r from-primary to-accent">في دقائق.</span>
                    </h1>

                    <p class="text-zinc-500 dark:text-zinc-400 max-w-xl mx-auto text-lg font-bold leading-relaxed">
                        لم تقم بإنشاء أي نشاط تجاري بعد. انضم لآلاف المبدعين واعرض أعمالك باحترافية الآن.
                    </p>

                    <div class="pt-4">
                        <a href="{{ route('business.create') }}" class="group relative inline-flex items-center gap-3 px-12 py-6 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-[2rem] font-black text-xl hover:scale-105 transition-all shadow-2xl">
                            إنشاء أول بيزنس لك
                            <svg class="w-6 h-6 rotate-180 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <header id="my-projects" class="max-w-7xl mx-auto px-6 py-12 scroll-mt-32">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <h2 class="text-5xl font-black text-zinc-900 dark:text-white tracking-tighter">مشاريعي</h2>
                        <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-xs font-black text-zinc-500">{{ $businesses->count() }}</span>
                    </div>
                    <p class="text-zinc-500 font-bold">إدارة هوياتك الرقمية وتتبع حالة القبول.</p>
                </div>
                
                <a href="{{ route('business.create') }}" class="group flex items-center gap-3 px-8 py-4 bg-primary text-white rounded-2xl font-black shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-1 transition-all">
                    <span class="text-2xl group-hover:rotate-90 transition-transform">+</span>
                    بيزنس جديد
                </a>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($businesses as $business)
            <div class="group relative bg-white dark:bg-zinc-900 rounded-[3rem] border border-zinc-100 dark:border-zinc-800 p-8 hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.1)] transition-all duration-500 flex flex-col">
                
                <div class="flex items-start justify-between mb-8">
                    <div class="w-20 h-20 rounded-[2rem] overflow-hidden bg-zinc-50 dark:bg-zinc-800 border-2 border-white dark:border-zinc-800 shadow-sm group-hover:scale-110 transition-transform duration-500">
                        @if($business->logo)
                            <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl bg-gradient-to-br from-zinc-100 to-zinc-200 dark:from-zinc-800 dark:to-zinc-700">💼</div>
                        @endif
                    </div>
                    
                    <div class="flex flex-col items-end gap-2">
                        @if($business->status === 'approved')
                            <span class="px-4 py-1.5 bg-green-500/10 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                مفعّل
                            </span>
                        @else
                            <span class="px-4 py-1.5 bg-amber-500/10 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                مراجعة
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4 mb-10 flex-grow">
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight group-hover:text-primary transition-colors">{{ $business->name }}</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm font-bold line-clamp-2 leading-relaxed">
                        {{ $business->meta_description ?? 'لا يوجد وصف مختصر لهذا المشروع حالياً.' }}
                    </p>
                    
                    <div class="flex flex-wrap gap-2 pt-2">
                        <span class="text-[10px] font-black uppercase tracking-tighter text-zinc-400 bg-zinc-50 dark:bg-zinc-800 px-3 py-1 rounded-md">
                            📍 {{ $business->city->name ?? 'غير محدد' }}
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-tighter text-zinc-400 bg-zinc-50 dark:bg-zinc-800 px-3 py-1 rounded-md">
                            🗂️ {{ $business->category->name ?? 'عام' }}
                        </span>
                    </div>
                </div>

                <div class="pt-8 border-t border-zinc-50 dark:border-zinc-800/50 flex flex-col gap-4">
                    <div class="flex justify-between items-center text-xs font-bold text-zinc-400">
                        <span>الوسائط: <b>{{ $business->media_count }}</b></span>
                        <span>أنشئ في: <b>{{ $business->created_at->format('Y/m/d') }}</b></span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('business.edit', $business->id) }}" class="flex items-center justify-center py-4 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-2xl font-black text-sm hover:opacity-90 transition-all">
                            تعديل
                        </a>
                        <a href="{{ route('profile.show', $business->slug) }}" target="_blank" class="flex items-center justify-center py-4 border-2 border-zinc-100 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-2xl font-black text-sm hover:border-primary transition-all">
                            معاينة
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .scroll-mt-32 {
        scroll-margin-top: 8rem;
    }

    /* Animation for the grid items */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .group {
        animation: fadeInUp 0.5s ease-out forwards;
    }
</style>
@endsection