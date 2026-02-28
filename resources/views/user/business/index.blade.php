@extends('layouts.dashboard.app')

@section('content')
@if($isEmpty)
<div class="relative w-full overflow-hidden" style="background: transparent !important;">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24 space-y-32 lg:space-y-48" style="background: transparent !important;">

        {{-- Hero Section --}}
        <section class="relative flex flex-col items-center text-center pt-10" style="background: transparent !important;">
            <div class="relative mb-10 lg:mb-14 group">
                <div class="absolute inset-0 bg-gradient-to-tr from-primary/40 to-accent/40 blur-3xl rounded-full scale-150 animate-pulse group-hover:scale-175 transition-transform duration-700"></div>
                <div class="relative w-24 h-24 md:w-32 md:h-32 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-2xl rounded-[2.5rem] flex items-center justify-center shadow-2xl border border-white dark:border-zinc-700/50 transform group-hover:rotate-6 transition-all duration-500">
                    <svg class="w-12 h-12 md:w-16 md:h-16 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-black mb-8 dark:text-white text-zinc-900 tracking-tighter leading-[1.1]">
                حول شغفك إلى <br class="hidden sm:block">
                <span class="relative inline-block mt-3">
                    <span class="relative z-10 bg-clip-text text-transparent bg-gradient-to-r from-primary via-accent to-primary animate-gradient-x">بيزنس احترافي</span>
                    <div class="absolute -bottom-2 left-0 w-full h-3 bg-primary/10 -rotate-1 rounded-full blur-sm"></div>
                </span>
            </h1>

            <p class="text-zinc-500 dark:text-zinc-400 max-w-2xl mx-auto mb-12 text-lg md:text-xl leading-relaxed font-medium">
                أنت تمتلك المهارة، ونحن نمتلك المنصة. ابدأ الآن في بناء بروفايلك التجاري واجعل العالم يرى احترافيتك <span class="text-zinc-900 dark:text-white font-bold underline decoration-primary/30 decoration-4 underline-offset-4">في أقل من 5 دقائق.</span>
            </p>

            <div class="flex flex-col sm:flex-row gap-5 items-center justify-center w-full">
                <a href="{{ route('business.create') }}" class="group relative px-10 py-5 bg-primary text-white rounded-2xl font-bold text-lg shadow-xl shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300 overflow-hidden w-full sm:w-auto">
                    <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    <span class="relative z-10 flex items-center justify-center gap-3">
                        انطلق الآن مجاناً
                        <svg class="w-5 h-5 rtl:rotate-180 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                </a>
            </div>
        </section>

        {{-- Features Grid --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
            $features = [
            ['icon' => '🎯', 'title' => 'لماذا البروفايل؟', 'desc' => 'البروفايل هو وجهتك الرقمية، يجمع أعمالك، أسعارك، وطرق التواصل في رابط واحد احترافي.'],
            ['icon' => '🛠️', 'title' => 'الخطوات بسيطة', 'desc' => 'أدخل بياناتك، ارفع أعمالك، اختر الأقسام وانطلق! الأمر بهذه البساطة فعلاً.'],
            ['icon' => '📈', 'title' => 'النتيجة فورية', 'desc' => 'ستحصل على صفحة مخصصة متوافقة مع الجوال وجاهزة لاستقبال عملائك فوراً.']
            ];
            @endphp

            @foreach($features as $feature)
            <div class="group bg-white dark:bg-zinc-900/40 p-8 rounded-[2.5rem] border border-zinc-200/60 dark:border-zinc-800/60 hover:border-primary/40 transition-all duration-500 shadow-sm hover:shadow-2xl hover:-translate-y-2">
                <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 group-hover:-rotate-3 transition-transform">
                    {{ $feature['icon'] }}
                </div>
                <h3 class="text-2xl font-bold mb-4 dark:text-white text-zinc-900">{{ $feature['title'] }}</h3>
                <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed text-base font-medium">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </section>
    </div>
</div>

@else
{{-- الجزء اللي هيظهر لما يكون فيه شركات فعلاً --}}
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-10">
    <div class="flex justify-between items-center mb-10">
        <h2 class="text-3xl font-black dark:text-white text-zinc-900 tracking-tighter">مشاريعك الحالية</h2>
        <a href="{{ route('business.create') }}" class="px-6 py-3 bg-primary text-white rounded-xl font-bold shadow-lg hover:-translate-y-1 transition-all">
            + إضافة بيزنس جديد
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($businesses as $business)
        <div class="bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-zinc-100 flex items-center justify-center text-2xl">
                    @if($business->logo)
                    <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover">
                    @else
                    💼
                    @endif
                </div>
                <div>
                    <h3 class="font-bold text-xl dark:text-white">{{ $business->name }}</h3>
                    <span class="text-xs px-2 py-1 rounded-lg {{ $business->status === 'approved' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600' }}">
                        {{ $business->status === 'approved' ? 'منشور' : 'قيد المراجعة' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('business.edit', $business->id) }}" class="flex items-center justify-center py-3 bg-zinc-100 dark:bg-zinc-800 dark:text-white rounded-xl font-bold text-sm hover:bg-zinc-200 transition-colors">
                    تعديل
                </a>
                <a href="{{ route('profile.show', $business->slug) }}" target="_blank" class="flex items-center justify-center py-3 bg-primary/10 text-primary rounded-xl font-bold text-sm hover:bg-primary/20 transition-colors">
                    معاينة
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<style>
    @keyframes gradient-x {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    .animate-gradient-x {
        background-size: 200% 200%;
        animation: gradient-x 4s linear infinite;
    }

    :root {
        --primary-rgb: 79, 70, 229;
    }
</style>
@endsection