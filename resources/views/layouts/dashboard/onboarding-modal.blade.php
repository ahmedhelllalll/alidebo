<div id="onboarding-modal" class="fixed inset-0 z-[2000] flex items-center justify-center p-4 overflow-hidden">
    <div class="absolute inset-0 bg-zinc-950/40 backdrop-blur-md transition-opacity duration-500"></div>

    <div class="relative w-full max-w-lg bg-white/80 dark:bg-zinc-900/80 border border-zinc-200/50 dark:border-zinc-800/50 rounded-[40px] shadow-2xl backdrop-blur-2xl overflow-hidden transform transition-all duration-500 scale-100 opacity-100" id="modal-content">

        <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/20 blur-[100px] rounded-full"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-orange-500/10 blur-[100px] rounded-full"></div>

        <form id="onboarding-form" action="{{ route('business.store') }}" method="POST" enctype="multipart/form-data" class="relative z-10 p-8 md:p-12">
            @csrf

            <div class="flex justify-center gap-1.5 mb-10" id="step-indicators">
                <div class="h-1.5 w-10 rounded-full bg-primary transition-all duration-500"></div>
                <div class="h-1.5 w-6 rounded-full bg-zinc-200 dark:bg-zinc-800 transition-all duration-500"></div>
                <div class="h-1.5 w-6 rounded-full bg-zinc-200 dark:bg-zinc-800 transition-all duration-500"></div>
            </div>

            <div id="onboarding-steps">
                <div class="step-content transition-all duration-500" data-step="1">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-tr from-primary to-orange-400 text-white shadow-xl mb-8">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-black text-zinc-900 dark:text-white mb-4 tracking-tight">أهلاً بك في alidebo</h2>
                        <p class="text-zinc-500 dark:text-zinc-400 text-lg">خطوات بسيطة ونبدأ في تنظيم أعمالك بشكل احترافي.</p>
                    </div>
                </div>

                <div class="step-content hidden opacity-0 translate-x-10 transition-all duration-500" data-step="2">
                    <div class="space-y-4">
                        <div class="text-right">
                            <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2 mr-1">اسم النشاط</label>
                            <input type="text" name="name" required class="w-full p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800 border-none focus:ring-2 focus:ring-primary" placeholder="مثلاً: شركة الإبداع">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-right">
                                <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2 mr-1">التصنيف</label>
                                <select name="category_id" required class="w-full p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800 border-none text-sm">
                                    @foreach(\App\Models\Category::all() as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-right">
                                <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2 mr-1">المدينة</label>
                                <select name="city_id" required class="w-full p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800 border-none text-sm">
                                    @foreach(\App\Models\City::all() as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="text-right">
                            <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2 mr-1">رقم الواتساب</label>
                            <input type="text" name="whatsapp" required class="w-full p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800 border-none" placeholder="010xxxxxxx">
                        </div>
                    </div>
                </div>

                <div class="step-content hidden opacity-0 translate-x-10 transition-all duration-500" data-step="3">
                    <div class="text-center">
                        <div class="w-20 h-20 rounded-full bg-green-500/10 text-green-500 mx-auto flex items-center justify-center mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-black text-zinc-900 dark:text-white mb-4">جاهز للانطلاق؟</h2>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-8">عند الضغط على إنهاء، سنقوم بإنشاء بروفايلك الأول تلقائياً.</p>

                        <div class="text-right bg-zinc-100 dark:bg-zinc-800/50 p-4 rounded-2xl">
                            <label class="block text-xs font-bold mb-2 text-zinc-500">وصف مختصر (اختياري)</label>
                            <textarea name="description" rows="2" class="w-full bg-transparent border-none p-0 focus:ring-0 text-sm" placeholder="اكتب نبذة بسيطة عن عملك..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex items-center gap-4">
                <button type="button" id="prev-btn" onclick="prevStep()" class="hidden px-8 py-4 rounded-2xl font-bold text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all text-sm">السابق</button>
                <button type="button" id="next-btn" onclick="handleNext()" class="flex-1 py-4 bg-primary hover:bg-orange-600 text-white rounded-2xl font-bold transition-all transform active:scale-95 shadow-lg shadow-primary/25">
                    الاستمرار
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 3;

    function handleNext() {
        if (currentStep < totalSteps) {
            updateStep(currentStep + 1);
        } else {
            document.getElementById('onboarding-form').submit();
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            updateStep(currentStep - 1);
        }
    }

    function updateStep(step) {
        const steps = document.querySelectorAll('.step-content');
        const indicators = document.getElementById('step-indicators').children;

        steps.forEach(s => {
            if (s.getAttribute('data-step') == currentStep) {
                s.classList.add('hidden', 'opacity-0', 'translate-x-10');
            }
        });

        const nextStepEl = document.querySelector(`[data-step="${step}"]`);
        nextStepEl.classList.remove('hidden');
        setTimeout(() => {
            nextStepEl.classList.remove('opacity-0', 'translate-x-10');
            nextStepEl.classList.add('opacity-100', 'translate-x-0');
        }, 50);

        for (let i = 0; i < indicators.length; i++) {
            indicators[i].classList.toggle('w-10', i < step);
            indicators[i].classList.toggle('bg-primary', i < step);
            indicators[i].classList.toggle('w-6', i >= step);
            indicators[i].classList.toggle('bg-zinc-200', i >= step);
        }

        currentStep = step;
        document.getElementById('prev-btn').classList.toggle('hidden', currentStep === 1);
        document.getElementById('next-btn').innerText = currentStep === totalSteps ? 'إنهاء وإنشاء البروفايل' : 'الاستمرار';
    }
</script>

<style>
    .step-content {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #modal-content {
        transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .bg-primary {
        background-color: #ff6b00;
    }
</style>