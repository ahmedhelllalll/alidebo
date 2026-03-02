@extends('layouts.dashboard.app')

@section('nav_links')
<a href="#data-section" class="nav-link">1. البيانات</a>
<a href="#preview-section" class="nav-link">2. المعاينة</a>
<a href="#steps-section" class="nav-link">3. ما بعد الإنشاء</a>
@endsection

@section('content')
<div class="min-h-screen bg-transparent pb-32">
    <div class="sticky top-0 z-40 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-100 dark:border-zinc-900 mb-12">
        <div class="max-w-4xl mx-auto px-6 py-6">
            <div class="relative h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div id="progress-bar" class="absolute top-0 left-0 h-full bg-gradient-to-r from-primary to-accent w-1/3 transition-all duration-700 ease-out animate-progress-glow"></div>
            </div>
            <div class="flex justify-between mt-3 text-[10px] font-black uppercase tracking-widest text-zinc-400">
                <span id="step-label">تعبئة البيانات</span>
                <span class="text-primary font-bold">جاري بناء هويتك الرقمية ✨</span>
                <span id="progress-percent">35%</span>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 space-y-32">

        <section id="data-section" class="scroll-mt-40">
            <div class="text-right mb-12">
                <h2 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tighter mb-4">لنبدأ بالأساسيات</h2>
                <p class="text-zinc-500 font-bold text-lg">أدخل معلومات نشاطك التجاري بدقة ليتم أرشفة بروفايلك بشكل صحيح.</p>
            </div>

            <form id="main-form" action="{{ route('business.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white dark:bg-zinc-900 p-8 md:p-12 rounded-[3rem] border border-zinc-100 dark:border-zinc-800 shadow-sm">
                @csrf

                <div class="space-y-3 md:col-span-2">
                    <label class="text-xs font-black uppercase tracking-widest text-zinc-400 pr-2">اسم البيزنس / البراند</label>
                    <input type="text" name="name" id="input-name" required placeholder="مثلاً: علي ديبو ستوديو"
                        class="w-full px-6 py-5 bg-zinc-50 dark:bg-zinc-800/50 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl focus:border-primary outline-none transition-all dark:text-white font-black text-xl shadow-inner">
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-black uppercase tracking-widest text-zinc-400 pr-2">التصنيف</label>
                    <select name="category_id" required class="w-full px-6 py-5 bg-zinc-50 dark:bg-zinc-800/50 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl focus:border-primary outline-none transition-all dark:text-white font-bold appearance-none cursor-pointer">
                        <option value="">اختر التصنيف...</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-black uppercase tracking-widest text-zinc-400 pr-2">المدينة</label>
                    <select name="city_id" required class="w-full px-6 py-5 bg-zinc-50 dark:bg-zinc-800/50 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl focus:border-primary outline-none transition-all dark:text-white font-bold appearance-none cursor-pointer">
                        <option value="">اختر المدينة...</option>
                        @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-black uppercase tracking-widest text-zinc-400 pr-2">رقم الواتساب</label>
                    <input type="tel" name="whatsapp" required placeholder="01xxxxxxxxx"
                        class="w-full px-6 py-5 bg-zinc-50 dark:bg-zinc-800/50 border-2 border-zinc-100 dark:border-zinc-800 rounded-2xl focus:border-primary outline-none transition-all dark:text-white font-bold text-left ltr shadow-inner">
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-black uppercase tracking-widest text-zinc-400 pr-2">اللوجو (اختياري)</label>
                    <div class="relative group h-[68px]">
                        <input type="file" name="logo" id="input-logo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="flex items-center justify-between px-6 py-5 bg-zinc-50 dark:bg-zinc-800/50 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl transition-all group-hover:border-primary">
                            <span class="text-zinc-400 text-sm font-bold" id="file-name">اختر ملف الصورة...</span>
                            <span class="text-primary text-xl">🖼️</span>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 pt-6">
                    <button type="submit" class="w-full py-6 bg-zinc-900 dark:bg-white text-white dark:text-black rounded-[2rem] font-black text-2xl hover:scale-[1.02] transition-transform shadow-2xl active:scale-95 flex items-center justify-center gap-3">
                        <span>إنشاء البيزنس الآن</span>
                        <svg class="w-6 h-6 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>
                </div>
            </form>
        </section>

        <section id="preview-section" class="scroll-mt-40 flex flex-col items-center">
            <div class="text-center mb-16">
                <span class="px-4 py-1 bg-primary/10 text-primary text-[10px] font-black rounded-full uppercase tracking-widest mb-4 inline-block">Visual Verification</span>
                <h2 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tighter">معاينة حية لبروفايلك</h2>
            </div>

            <div class="relative w-full max-w-2xl group">
                <div class="absolute inset-0 bg-primary/5 blur-[120px] rounded-full transform group-hover:scale-110 transition-transform duration-700"></div>

                <div class="relative bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 p-12 md:p-16 rounded-[4rem] shadow-2xl text-center overflow-hidden transition-all duration-500 hover:-rotate-1">
                    <div class="w-32 h-32 md:w-40 md:h-40 bg-zinc-100 dark:bg-zinc-800 rounded-[3rem] mx-auto mb-10 flex items-center justify-center border-4 border-white dark:border-zinc-800 shadow-xl overflow-hidden group-hover:rotate-6 transition-transform">
                        <img id="preview-logo" src="" class="w-full h-full object-cover hidden">
                        <span id="preview-placeholder" class="text-6xl">💎</span>
                    </div>

                    <h3 id="preview-name-text" class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white mb-6 tracking-tighter">اسم نشاطك</h3>

                    <div class="flex flex-wrap justify-center gap-3 opacity-50">
                        <span class="px-5 py-2 bg-zinc-100 dark:bg-zinc-800 rounded-full text-xs font-black uppercase tracking-widest dark:text-zinc-400">Category</span>
                        <span class="px-5 py-2 bg-zinc-100 dark:bg-zinc-800 rounded-full text-xs font-black uppercase tracking-widest dark:text-zinc-400">City</span>
                    </div>

                    <div class="mt-12 flex justify-center">
                        <div class="w-full max-w-xs py-4 bg-green-500/10 text-green-600 rounded-2xl font-black flex items-center justify-center gap-2 border border-green-500/20">
                            <span class="text-xl">💬</span> واتساب مفعل
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="steps-section" class="scroll-mt-40">
            <div class="text-right mb-16">
                <h2 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tighter mb-4">ماذا يحدث بعد الضغط على إنشاء؟</h2>
                <p class="text-zinc-500 font-bold text-lg">رحلتك للاحترافية تبدأ بمجرد ضغطة زر.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-10 bg-zinc-50 dark:bg-zinc-900/50 rounded-[3rem] border border-zinc-100 dark:border-zinc-800 relative group overflow-hidden">
                    <div class="text-5xl mb-6 group-hover:scale-110 transition-transform block">📧</div>
                    <h4 class="text-xl font-black mb-3 dark:text-white text-zinc-900">رسالة التأكيد</h4>
                    <p class="text-zinc-500 dark:text-zinc-400 font-medium text-sm leading-relaxed">ستصلك رسالة بريد إلكتروني تحتوي على تفاصيل الدخول لوحة تحكم نشاطك الجديد.</p>
                    <span class="absolute -bottom-4 -left-4 text-8xl font-black opacity-[0.03] dark:opacity-[0.05]">01</span>
                </div>

                <div class="p-10 bg-zinc-900 rounded-[3rem] border border-zinc-800 relative group overflow-hidden shadow-2xl">
                    <div class="text-5xl mb-6 group-hover:scale-110 transition-transform block">🛠️</div>
                    <h4 class="text-xl font-black mb-3 text-white">إكمال البروفايل</h4>
                    <p class="text-zinc-400 font-medium text-sm leading-relaxed">يجب عليك إضافة معرض أعمالك، الروابط الاجتماعية، وساعات العمل ليصبح البروفايل "احترافياً".</p>
                    <span class="absolute -bottom-4 -left-4 text-8xl font-black opacity-10 text-white">02</span>
                </div>

                <div class="p-10 bg-zinc-50 dark:bg-zinc-900/50 rounded-[3rem] border border-zinc-100 dark:border-zinc-800 relative group overflow-hidden">
                    <div class="text-5xl mb-6 group-hover:scale-110 transition-transform block">📈</div>
                    <h4 class="text-xl font-black mb-3 dark:text-white text-zinc-900">تقدم الإنجاز</h4>
                    <p class="text-zinc-500 dark:text-zinc-400 font-medium text-sm leading-relaxed">كلما أضفت تفاصيل أكثر، زاد شريط تقدم حسابك (Progress) وظهرت في نتائج بحث المنصة بشكل أفضل.</p>
                    <span class="absolute -bottom-4 -left-4 text-8xl font-black opacity-[0.03] dark:opacity-[0.05]">03</span>
                </div>
            </div>
        </section>

    </div>
</div>

<style>
    @keyframes progress-glow {

        0%,
        100% {
            filter: brightness(1) drop-shadow(0 0 2px rgba(244, 80, 24, 0.5));
        }

        50% {
            filter: brightness(1.3) drop-shadow(0 0 10px rgba(244, 80, 24, 0.8));
        }
    }

    .animate-progress-glow {
        animation: progress-glow 3s infinite;
    }

    .scroll-mt-40 {
        scroll-margin-top: 10rem;
    }

    .ltr {
        direction: ltr;
    }
</style>

<script>
    // Live Preview Logic
    const inputName = document.getElementById('input-name');
    const previewName = document.getElementById('preview-name-text');
    const inputLogo = document.getElementById('input-logo');
    const previewLogo = document.getElementById('preview-logo');
    const previewPlaceholder = document.getElementById('preview-placeholder');
    const fileName = document.getElementById('file-name');

    inputName.addEventListener('input', (e) => {
        previewName.innerText = e.target.value || "اسم نشاطك";
        updateProgress();
    });

    inputLogo.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileName.innerText = this.files[0].name;
            const reader = new FileReader();
            reader.onload = function(e) {
                previewLogo.src = e.target.result;
                previewLogo.classList.remove('hidden');
                previewPlaceholder.classList.add('hidden');
            }
            reader.readAsDataURL(this.files[0]);
        }
        updateProgress();
    });

    // Dynamic Progress Bar
    function updateProgress() {
        const progressBar = document.getElementById('progress-bar');
        const progressPercent = document.getElementById('progress-percent');
        const stepLabel = document.getElementById('step-label');

        // Simple logic: if name is filled +25%, if logo +10%, base is 25%
        let progress = 25;
        if (inputName.value.length > 2) progress += 25;
        if (inputLogo.files.length > 0) progress += 15;

        progressBar.style.width = progress + '%';
        progressPercent.innerText = progress + '%';

        if (progress > 50) stepLabel.innerText = "أوشكت على الانتهاء";
    }

    // Smooth scroll for nav links
    document.querySelectorAll('.nav-link').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
@endsection