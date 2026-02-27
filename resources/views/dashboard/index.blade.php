@extends('layouts.dashboard.app')

@section('content')
@if($isEmpty)
<div class="relative w-full overflow-hidden " style="background: transparent !important;">
   

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
                <a href="#" class="px-10 py-5 rounded-2xl font-bold text-lg text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all duration-300 w-full sm:w-auto text-center">
                    استكشف الميزات
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

        {{-- Vision Section with Grid Lines --}}
        <section class="relative">
            <div id="vision-card" class="group relative bg-zinc-950 p-10 sm:p-16 lg:p-24 rounded-[3rem] overflow-hidden border border-zinc-800 shadow-[0_0_50px_-12px_rgba(0,0,0,0.5)]">

                {{-- Grid Lines Background --}}
                <div class="absolute inset-0 z-0 opacity-20 [mask-image:radial-gradient(ellipse_at_center,white,transparent)]">
                    <div class="absolute inset-0" style="background-image: linear-gradient(#333 1px, transparent 1px), linear-gradient(90deg, #333 1px, transparent 1px); background-size: 40px 40px;"></div>
                </div>

                <div id="spotlight" class="pointer-events-none absolute -inset-px opacity-0 group-hover:opacity-100 transition duration-500 hidden sm:block"
                    style="background: radial-gradient(600px circle at var(--x, 50%) var(--y, 50%), rgba(var(--primary-rgb, 120, 113, 255), 0.1), transparent 80%);">
                </div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center gap-16">
                    <div class="w-full lg:w-2/3 text-center lg:text-start">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/20 text-primary text-xs font-black tracking-widest uppercase mb-10 border border-primary/30">
                            <span class="w-2 h-2 bg-primary rounded-full animate-ping"></span>
                            رؤيتنا المستقبلية
                        </span>
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black mb-8 leading-loose text-white tracking-tighter" style="line-height: 1.3;">
                            لا نبني مجرد بروفايل،
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-accent">نبني اقتصاداً جديداً.</span>
                        </h2>
                        <p class="text-zinc-400 text-lg lg:text-xl leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                            "علي ديبو" يتطور ليكون Marketplace ضخم يربط المحترفين بالعملاء مباشرة. هدفنا هو إلغاء العمولات وتمكينك من النمو بحرية.
                        </p>
                    </div>

                    <div class="w-full lg:w-1/3 grid grid-cols-1 gap-6">
                        <div class="p-8 bg-white/5 backdrop-blur-xl rounded-[2rem] border border-white/10 hover:border-primary/50 transition-all duration-300">
                            <span class="block text-4xl lg:text-5xl font-black text-white mb-2">2026</span>
                            <span class="text-xs text-zinc-500 uppercase tracking-[0.2em] font-bold">سنة الانطلاق الشامل</span>
                        </div>
                        <div class="p-8 bg-white/5 backdrop-blur-xl rounded-[2rem] border border-white/10 hover:border-accent/50 transition-all duration-300">
                            <span class="block text-4xl lg:text-5xl font-black text-white mb-2">+1k</span>
                            <span class="text-xs text-zinc-500 uppercase tracking-[0.2em] font-bold">شريك نجاح مسجل</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="text-center py-20 relative">
            <!-- <div class="absolute inset-0 bg-amber-500/5 blur-[120px] -z-10"></div> -->

            <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-black mb-10 border border-amber-500/20">
                <span class="flex h-2 w-2 bg-amber-500 rounded-full"></span>
                لفترة محدودة: جميع الميزات مجانية 100%
            </div>

            <h2 class="text-4xl md:text-6xl font-black mb-6 dark:text-white text-zinc-900 tracking-tighter">احجز اسم البيزنس الخاص بك</h2>
            <p class="text-zinc-500 dark:text-zinc-400 max-w-xl mx-auto mb-14 text-lg md:text-xl font-medium">
                نحن الآن في المرحلة التجريبية. انضم للأوائل واحصل على أولوية الظهور في محركات البحث.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="{{ route('business.create') }}" class="w-full sm:w-auto px-12 py-5 bg-zinc-900 dark:bg-white dark:text-zinc-950 text-white rounded-2xl font-black text-lg hover:scale-105 transition-all duration-300 shadow-2xl">
                    ابدأ رحلتك الآن
                </a>
                <a href="#" class="w-full sm:w-auto px-12 py-5 rounded-2xl font-black text-lg border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 bg-transparent hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all">
                    نماذج حية
                </a>
            </div>
        </section>
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
        /* قم بتعديل هذا حسب لون الـ Primary عندك */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const card = document.getElementById('vision-card');
        const spotlight = document.getElementById('spotlight');

        if (card && spotlight) {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                spotlight.style.setProperty('--x', `${e.clientX - rect.left}px`);
                spotlight.style.setProperty('--y', `${e.clientY - rect.top}px`);
            });
        }
    });
</script>
@endsection