@extends('layouts.dashboard.app')

@section('nav_links')
@if($isEmpty)
<a href="#hero" class="nav-link">البداية</a>
<a href="#features" class="nav-link">المميزات</a>
<a href="#about-ali" class="nav-link">عن المنصة</a>
@else
<a href="#my-projects" class="nav-link">مشاريعي</a>
<a href="{{ route('business.create') }}" class="nav-link">+ جديد</a>
@endif
@endsection

@section('content')
<div class="min-h-screen bg-transparent pb-20">
    @if($isEmpty)
    <div class="max-w-4xl mx-auto px-6 pt-8">
        <div class="relative h-1.5 w-full bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner">
            <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-primary via-accent to-primary w-1/4 animate-progress-glow"></div>
        </div>
        <div class="flex justify-between mt-3 text-[10px] font-black tracking-[0.2em] uppercase text-zinc-400 dark:text-zinc-500">
            <span>إعداد الحساب</span>
            <span class="text-primary animate-pulse">خطوة واحدة تفصلك عن الاحترافية ✨</span>
            <span>25%</span>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12 lg:py-16 space-y-20">
        <section id="hero" class="relative flex flex-col items-center text-center py-10 scroll-mt-32">
            <div class="absolute -top-20 -z-10 w-80 h-80 bg-primary/10 blur-[140px] rounded-full"></div>
            <div class="relative mb-6">
                <div class="relative px-5 py-1.5 bg-white/10 dark:bg-zinc-900/40 backdrop-blur-2xl border border-zinc-200/50 dark:border-zinc-700/30 rounded-full shadow-sm">
                    <span class="text-xs font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-accent">
                        🚀 أهلاً بك في مستقبل أعمالك الرقمية
                    </span>
                </div>
            </div>

            <h1 class="text-6xl md:text-8xl font-black mb-8 dark:text-white text-zinc-900 tracking-tight leading-[0.9] lg:leading-[1.1]">
                حول شغفك إلى <br>
                <span class="relative">
                    <span class="relative z-10 text-transparent bg-clip-text bg-gradient-to-b from-primary to-primary/80">بيزنس لا يتوقف</span>
                    <svg class="absolute -bottom-2 left-0 w-full h-4 text-primary/20 -z-10" viewBox="0 0 100 10" preserveAspectRatio="none">
                        <path d="M0 5 Q 25 0 50 5 T 100 5" stroke="currentColor" stroke-width="8" fill="none" />
                    </svg>
                </span>
            </h1>

            <p class="text-zinc-500 dark:text-zinc-400 max-w-2xl mx-auto mb-12 text-lg md:text-xl font-medium leading-relaxed">
                أنت تمتلك الموهبة، ونحن نمنحك المنصة التي تستحقها. ابدأ الآن في بناء بروفايلك التجاري واجعل العالم يرى احترافيتك <span class="text-zinc-900 dark:text-white font-bold underline decoration-primary/30 underline-offset-4">في أقل من دقيقتين.</span>
            </p>

            <div class="flex flex-col sm:flex-row gap-5 items-center justify-center w-full max-w-lg">
                <a href="{{ route('business.create') }}" class="group relative px-10 py-5 bg-primary text-white rounded-2xl font-black text-xl shadow-[0_20px_50px_rgba(79,70,229,0.3)] hover:shadow-primary/50 hover:-translate-y-1 transition-all duration-300 w-full sm:w-auto overflow-hidden">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        أنشئ بروفايلك الآن
                        <svg class="w-6 h-6 rtl:rotate-180 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </span>
                </a>
                <a href="#" class="group px-10 py-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl font-bold text-lg hover:border-primary/50 transition-all w-full sm:w-auto text-zinc-700 dark:text-zinc-300 flex items-center justify-center gap-2">
                    <span>رؤية بروفايل مثالي</span>
                    <span class="text-xl group-hover:scale-125 transition-transform">💎</span>
                </a>
            </div>
        </section>

        <section id="features" class="grid grid-cols-1 md:grid-cols-6 gap-6 py-10 scroll-mt-32">
            <div class="md:col-span-3 lg:col-span-2 p-8 bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-primary/30 transition-all group">
                <div class="w-14 h-14 bg-zinc-50 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:rotate-6 transition-transform">📝</div>
                <h3 class="text-2xl font-black mb-3 dark:text-white">بساطة مطلقة</h3>
                <p class="text-zinc-500 dark:text-zinc-400 font-medium leading-relaxed text-sm">أدخل بياناتك، ارفع أعمالك، وانشر الرابط. لا حاجة لخبرة في البرمجة أو التصميم.</p>
            </div>
            <div class="md:col-span-3 lg:col-span-2 p-8 bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] shadow-sm hover:border-primary/30 transition-all group">
                <div class="w-14 h-14 bg-zinc-50 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:rotate-6 transition-transform">🌍</div>
                <h3 class="text-2xl font-black mb-3 dark:text-white">رابط واحد لكل شيء</h3>
                <p class="text-zinc-500 dark:text-zinc-400 font-medium leading-relaxed text-sm">اجمع معرض أعمالك، حساباتك الاجتماعية، وطرق التواصل في صفحة واحدة برابط يحمل اسمك.</p>
            </div>
            <div class="md:col-span-6 lg:col-span-2 p-8 bg-zinc-900 text-white rounded-[2.5rem] shadow-xl flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 text-8xl group-hover:scale-110 transition-transform">📈</div>
                <h3 class="text-2xl font-black mb-3">نمو بلا حدود</h3>
                <p class="text-zinc-400 font-medium leading-relaxed text-sm relative z-10">نحن نهتم بالجانب التقني والـ SEO لتظهر في نتائج البحث، لتتفرغ أنت للإبداع فقط.</p>
            </div>
        </section>

        <section id="about-ali" class="py-10 scroll-mt-32">
            <div class="p-1 bg-gradient-to-r from-primary/20 via-accent/20 to-primary/20 rounded-[3rem]">
                <div class="bg-white dark:bg-zinc-950 rounded-[2.9rem] p-8 md:p-12 flex flex-col lg:flex-row items-center gap-12 border border-white/10">
                    <div class="lg:w-1/3 flex justify-center">
                        <div class="relative">
                            <div class="absolute inset-0 bg-primary/20 blur-3xl rounded-full scale-110"></div>
                            <div class="relative w-48 h-48 md:w-56 md:h-56 bg-zinc-100 dark:bg-zinc-800 rounded-[3rem] overflow-hidden border-4 border-white dark:border-zinc-800 shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-500">
                                <img src="{{ asset('assets/images/ali-debo-avatar.png') }}" alt="Ali Debo" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all">
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-2/3 text-center lg:text-right space-y-6">
                        <div class="inline-block px-4 py-1 bg-primary/10 text-primary text-xs font-black rounded-full uppercase tracking-tighter">لماذا علي ديبو؟</div>
                        <h2 class="text-4xl md:text-5xl font-black dark:text-white text-zinc-900 tracking-tight">أكثر من مجرد منصة، نحن شريك نجاحك.</h2>
                        <p class="text-zinc-500 dark:text-zinc-400 text-lg font-medium leading-relaxed italic">
                            "رؤيتي هي تمكين كل مبدع عربي من امتلاك هوية رقمية تليق بمستواه العالمي. علي ديبو ليست مجرد أداة لبناء البروفايلات، بل هي بوابتك لعرض قيمتك الحقيقية أمام العالم."
                        </p>
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-4">
                            <a href="https://wa.me/your-whatsapp" class="flex items-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-bold hover:bg-green-600 transition-all hover:-translate-y-1 shadow-lg shadow-green-500/20">
                                <span class="text-xl">💬</span> تواصل معي مباشرة
                            </a>
                            <a href="#" class="px-6 py-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white rounded-xl font-bold hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all border border-zinc-200 dark:border-zinc-700">
                                اكتشف رؤيتنا
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @else
    <section id="my-projects" class="max-w-7xl mx-auto px-6 lg:px-8 py-10 scroll-mt-32">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12 border-b border-zinc-100 dark:border-zinc-800 pb-10">
            <div>
                <h2 class="text-5xl font-black dark:text-white text-zinc-900 tracking-tighter mb-2">مشاريعك</h2>
                <p class="text-zinc-500 font-bold">إليك نظرة سريعة على جميع بروفايلاتك الرقمية.</p>
            </div>
            <a href="{{ route('business.create') }}" class="group px-8 py-4 bg-primary text-white rounded-2xl font-black shadow-xl shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-1 transition-all flex items-center gap-3">
                <span class="text-2xl group-hover:rotate-90 transition-transform">+</span>
                <span>بيزنس جديد</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($businesses as $business)
            <div class="group bg-white dark:bg-zinc-900/50 backdrop-blur-sm rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 p-8 shadow-sm hover:shadow-2xl transition-all duration-500">
                <div class="flex items-center gap-6 mb-10">
                    <div class="w-20 h-20 rounded-3xl overflow-hidden bg-zinc-50 dark:bg-zinc-800 border-4 border-white dark:border-zinc-800 shadow-md group-hover:scale-110 transition-transform">
                        @if($business->logo)
                        <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-4xl">💎</div>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-black text-2xl dark:text-white">{{ $business->name }}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-2 h-2 rounded-full {{ $business->status === 'approved' ? 'bg-green-500 animate-pulse' : 'bg-amber-500' }}"></span>
                            <span class="text-[10px] font-black uppercase tracking-tighter {{ $business->status === 'approved' ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $business->status === 'approved' ? 'Live on Web' : 'Reviewing' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('business.edit', $business->id) }}" class="flex items-center justify-center py-4 bg-zinc-900 dark:bg-white text-white dark:text-black rounded-2xl font-black text-sm hover:opacity-90 transition-all">
                        تعديل
                    </a>
                    <a href="{{ route('profile.show', $business->slug) }}" target="_blank" class="flex items-center justify-center py-4 border-2 border-zinc-100 dark:border-zinc-800 text-zinc-900 dark:text-white rounded-2xl font-black text-sm hover:border-primary transition-all">
                        معاينة
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>

<style>
    @keyframes progress-glow {
        0% {
            filter: brightness(1) drop-shadow(0 0 2px rgba(244, 80, 24, 0.5));
        }

        50% {
            filter: brightness(1.3) drop-shadow(0 0 8px rgba(244, 80, 24, 0.8));
        }

        100% {
            filter: brightness(1) drop-shadow(0 0 2px rgba(244, 80, 24, 0.5));
        }
    }

    .animate-progress-glow {
        animation: progress-glow 3s infinite;
    }

    .scroll-mt-32 {
        scroll-margin-top: 8rem;
    }
</style>
@endsection