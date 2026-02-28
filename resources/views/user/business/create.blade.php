@extends('layouts.dashboard.app')

@section('content')
<main class="flex flex-col lg:flex-row min-h-screen bg-white dark:bg-[#09090b] overflow-hidden">
    <section class="w-full lg:w-[45%] flex flex-col justify-center px-8 py-12 lg:px-16 bg-white dark:bg-zinc-950 border-l border-slate-100 dark:border-zinc-900 order-2 lg:order-1 relative z-10 shadow-2xl">
        <div class="w-full max-w-md mx-auto fade-in">

            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-primary font-bold text-sm mb-10 transition-all group">
                <div class="p-2 rounded-full bg-slate-50 dark:bg-zinc-900 group-hover:bg-primary/10 transition-colors">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </div>
                العودة للوحة التحكم
            </a>

            <header class="mb-10 text-right">
                <h1 class="text-4xl font-[900] text-slate-900 dark:text-white mb-3 tracking-tight">ابدأ رحلتك الآن 🚀</h1>
                <p class="text-slate-500 dark:text-zinc-400 font-medium leading-relaxed">
                    خطوة واحدة تفصلك عن امتلاك صفحة احترافية تليق بعلامتك التجارية.
                </p>
            </header>

            <form id="create-business-form" method="POST" action="{{ route('business.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="group space-y-2">
                    <label class="text-sm font-black text-slate-700 dark:text-zinc-300 px-1 block text-right group-focus-within:text-primary transition-colors">اسم الشركة أو النشاط</label>
                    <div class="relative">
                        <input type="text" name="name" id="input-name" required value="{{ old('name') }}" maxlength="30" placeholder="مثال: ديبو ديزاين"
                            class="w-full px-6 py-4 bg-slate-50 dark:bg-zinc-900/50 border-2 border-slate-100 dark:border-zinc-800 rounded-2xl focus:border-primary focus:ring-0 outline-none transition-all dark:text-white font-bold text-right shadow-sm placeholder:text-slate-300 dark:placeholder:text-zinc-700" />
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 dark:text-zinc-700 font-mono text-xs" id="name-counter">0/30</div>
                    </div>
                    @error('name') <p class="text-red-500 text-xs mt-1 text-right font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="group space-y-2">
                    <label class="text-sm font-black text-slate-700 dark:text-zinc-300 px-1 block text-right group-focus-within:text-primary transition-colors">وصف مختصر (SEO)</label>
                    <textarea name="meta_description" id="input-desc" rows="4" maxlength="160" placeholder="ما الذي يميز شركتك؟ اجذب عملاءك بكلمات بسيطة.."
                        class="w-full px-6 py-4 bg-slate-50 dark:bg-zinc-900/50 border-2 border-slate-100 dark:border-zinc-800 rounded-2xl focus:border-primary focus:ring-0 outline-none transition-all dark:text-white font-medium text-right shadow-sm resize-none placeholder:text-slate-300 dark:placeholder:text-zinc-700 leading-relaxed">{{ old('meta_description') }}</textarea>
                    <div class="flex justify-between items-center px-1">
                        <p class="text-[10px] text-slate-400 font-bold">هذا الوصف يظهر في محركات البحث مثل جوجل.</p>
                        <span class="text-xs font-mono text-slate-300 dark:text-zinc-700" id="desc-counter">0/160</span>
                    </div>
                    @error('meta_description') <p class="text-red-500 text-xs mt-1 text-right font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="group space-y-2">
                    <label class="text-sm font-black text-slate-700 dark:text-zinc-300 px-1 block text-right">شعار الشركة (اختياري)</label>
                    <div class="relative group/file">
                        <input type="file" name="logo" id="input-logo" accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" />
                        <div class="w-full px-6 py-4 bg-slate-50 dark:bg-zinc-900/50 border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-2xl flex items-center justify-between group-hover/file:border-primary/50 transition-colors">
                            <span class="text-primary font-bold text-sm">اختر ملف</span>
                            <span class="text-slate-400 text-sm truncate max-w-[180px]" id="file-name">لم يتم اختيار صورة</span>
                            <div class="p-2 bg-white dark:bg-zinc-800 rounded-xl shadow-sm">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="submit-btn" class="group relative w-full overflow-hidden bg-primary text-white py-5 rounded-2xl font-black text-xl shadow-[0_20px_40px_rgba(244,80,24,0.25)] hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    <span id="btn-text" class="flex items-center justify-center gap-3">
                        إطلاق الملف التجاري
                        <svg class="w-6 h-6 animate-bounce-x" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                    <div id="btn-loader" class="loader hidden mx-auto"></div>
                </button>
            </form>
        </div>
    </section>

    <section class="w-full lg:w-[55%] p-10 lg:p-24 flex flex-col items-center justify-center bg-slate-50 dark:bg-[#0c0c0e] relative overflow-hidden order-1 lg:order-2">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[100px] -mr-40 -mt-40"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-orange-500/5 rounded-full blur-[100px] -ml-20 -mb-20"></div>

        <div class="relative z-10 w-full max-w-lg mx-auto text-center">
            <div class="fade-in mb-12">
                <h2 class="text-3xl lg:text-5xl font-black text-slate-800 dark:text-zinc-100 leading-tight mb-4">
                    كيف سيبدو <span class="glow-text">مشروعك؟</span>
                </h2>
                <p class="text-slate-400 dark:text-zinc-500 font-bold">شاهد معاينة مباشرة لبطاقة تعريفك الرقمية</p>
            </div>

            <div class="group relative">
                <div class="absolute -inset-1.5 bg-gradient-to-r from-primary via-orange-400 to-primary rounded-[3rem] blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
                <div class="relative bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800/50 p-10 rounded-[3rem] shadow-2xl transition-all duration-500">

                    <div class="w-24 h-24 bg-slate-50 dark:bg-zinc-800 rounded-3xl mx-auto mb-8 flex items-center justify-center shadow-inner border border-slate-100 dark:border-zinc-700 overflow-hidden group-hover:scale-110 transition-transform duration-500">
                        <img id="preview-logo-img" src="" class="w-full h-full object-cover hidden">
                        <span id="preview-logo-emoji" class="text-5xl group-hover:rotate-12 transition-transform">💎</span>
                    </div>

                    <div class="space-y-4">
                        <h3 id="preview-name" class="text-3xl font-black dark:text-white text-slate-900 truncate">اسم نشاطك</h3>
                        <div class="flex justify-center gap-2">
                            <span class="px-3 py-1 bg-green-500/10 text-green-500 text-[10px] font-black rounded-full uppercase tracking-widest animate-pulse">Live Preview</span>
                        </div>
                        <p id="preview-desc" class="text-slate-400 dark:text-zinc-500 font-medium leading-relaxed text-sm line-clamp-3 italic">
                            هنا سيظهر وصف مشروعك الذي يبرز قيمتك الفريدة لعملائك..
                        </p>
                    </div>

                    <div class="mt-10 pt-8 border-t border-slate-50 dark:border-zinc-800/50 flex items-center justify-between">
                        <div class="flex gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-slate-200 dark:bg-zinc-700"></div>
                            <div class="w-2 h-2 rounded-full bg-slate-200 dark:bg-zinc-700"></div>
                            <div class="w-2 h-2 rounded-full bg-slate-200 dark:bg-zinc-700"></div>
                        </div>
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/50">alidebo platform</div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex items-center justify-center gap-8 text-slate-400 dark:text-zinc-600 font-bold text-xs uppercase tracking-tighter">
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg> محركات بحث جاهزة</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg> سرعة صاروخية</span>
            </div>
        </div>
    </section>
</main>

<style>
    .fade-in {
        animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .glow-text {
        background: linear-gradient(to left, #f45018, #fb923c, #f45018);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: shine 3s linear infinite;
    }

    @keyframes shine {
        to {
            background-position: 200% center;
        }
    }

    .loader {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 3px solid #fff;
        width: 28px;
        height: 28px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .animate-bounce-x {
        animation: bounce-x 1s infinite;
    }

    @keyframes bounce-x {

        0%,
        100% {
            transform: translateX(0);
        }

        50% {
            transform: translateX(5px);
        }
    }

    input:focus,
    textarea:focus {
        box-shadow: 0 10px 15px -3px rgba(244, 80, 24, 0.05) !important;
    }
</style>

<script>
    const form = document.getElementById('create-business-form');
    const btn = document.getElementById('submit-btn');
    const loader = document.getElementById('btn-loader');
    const text = document.getElementById('btn-text');

    // عناصر الإدخال
    const inputName = document.getElementById('input-name');
    const inputDesc = document.getElementById('input-desc');
    const inputLogo = document.getElementById('input-logo');

    // عناصر المعاينة
    const previewName = document.getElementById('preview-name');
    const previewDesc = document.getElementById('preview-desc');
    const previewLogoImg = document.getElementById('preview-logo-img');
    const previewLogoEmoji = document.getElementById('preview-logo-emoji');
    const fileNameText = document.getElementById('file-name');

    // تحديث المعاينة للاسم
    inputName.addEventListener('input', (e) => {
        const val = e.target.value;
        previewName.innerText = val || "اسم نشاطك";
        document.getElementById('name-counter').innerText = `${val.length}/30`;
    });

    // تحديث المعاينة للوصف
    inputDesc.addEventListener('input', (e) => {
        const val = e.target.value;
        previewDesc.innerText = val || "هنا سيظهر وصف مشروعك الذي يبرز قيمتك الفريدة لعملائك..";
        document.getElementById('desc-counter').innerText = `${val.length}/160`;
    });

    // تحديث المعاينة للشعار
    inputLogo.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            fileNameText.innerText = this.files[0].name;

            reader.onload = function(e) {
                previewLogoImg.src = e.target.result;
                previewLogoImg.classList.remove('hidden');
                previewLogoEmoji.classList.add('hidden');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // إرسال النموذج
    form.addEventListener('submit', function() {
        btn.disabled = true;
        text.classList.add('hidden');
        loader.classList.remove('hidden');
        btn.classList.replace('hover:-translate-y-1', 'opacity-80');
    });
</script>
@endsection