@extends('layouts.dashboard.app')

@section('content')
<div class="min-h-screen bg-[#f8f9fb] dark:bg-zinc-950 pb-20"
    x-data="builder({ 
        name: '{{ addslashes($business->name) }}', 
        phone: '{{ $business->whatsapp }}', 
        initialSections: {{ $business->sections->toJson() }} 
     })">

    <div class="sticky top-0 z-40 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border-b border-zinc-100 dark:border-zinc-800 mb-8">
        <div class="max-w-[1600px] mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('business.index') }}" class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-2xl hover:bg-primary hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 12H5m7 7l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-black dark:text-white tracking-tighter" x-text="businessName"></h1>
                    <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">محرر الصفحات الذكي</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.show', $business->slug) }}" target="_blank" class="hidden md:flex px-6 py-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-xl font-black text-xs">معاينة ↗</a>
                <button @click="saveData()" class="px-8 py-3 bg-primary text-white rounded-xl font-black text-xs shadow-lg shadow-primary/20 hover:scale-105 transition-all">حفظ التغييرات</button>
            </div>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-7 space-y-8">
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-8 border border-zinc-100 dark:border-zinc-800 shadow-sm">
                <h3 class="text-lg font-black dark:text-white mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-primary rounded-full"></span> البيانات الأساسية
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest px-2">اسم النشاط</label>
                        <input type="text" x-model="businessName" class="w-full px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-2 border-transparent focus:border-primary rounded-2xl outline-none transition-all dark:text-white font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest px-2">رقم الواتساب</label>
                        <input type="text" x-model="whatsapp" class="w-full px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-2 border-transparent focus:border-primary rounded-2xl outline-none transition-all dark:text-white font-bold text-left" dir="ltr">
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center px-4">
                    <h3 class="text-lg font-black dark:text-white">أقسام الصفحة</h3>
                    <button @click="addSection()" class="text-primary font-black text-xs uppercase hover:underline">+ إضافة قسم</button>
                </div>

                <template x-for="(section, index) in sections" :key="section.id || index">
                    <div class="bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 overflow-hidden transition-all shadow-sm">
                        <div class="p-5 flex justify-between items-center cursor-pointer" @click="section.open = !section.open">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800 rounded-xl text-xs font-black dark:text-zinc-400" x-text="index + 1"></div>
                                <div>
                                    <h4 class="font-black text-sm dark:text-white uppercase tracking-tighter" x-text="section.section_type"></h4>
                                    <p class="text-[10px] text-zinc-400 font-bold uppercase" x-text="section.template_key"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click.stop="removeSection(index)" class="p-2 text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <svg class="w-5 h-5 text-zinc-300 transition-transform" :class="section.open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>

                        <div x-show="section.open" x-collapse class="p-8 border-t border-zinc-50 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-800/10 space-y-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">التنسيق</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="tpl in getTemplates(section.section_type)">
                                        <button @click="section.template_key = tpl"
                                            :class="section.template_key == tpl ? 'bg-primary text-white' : 'bg-white dark:bg-zinc-800 text-zinc-500 border border-zinc-100 dark:border-zinc-700'"
                                            class="px-5 py-2 rounded-xl text-[10px] font-black transition-all" x-text="tpl"></button>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <template x-for="(val, key) in section.content">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest px-1" x-text="key"></label>
                                        <template x-if="key != 'text' && key != 'description'">
                                            <input type="text" x-model="section.content[key]" class="w-full px-5 py-3 bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 rounded-xl outline-none focus:border-primary dark:text-white font-bold text-sm">
                                        </template>
                                        <template x-if="key == 'text' || key == 'description'">
                                            <textarea x-model="section.content[key]" rows="4" class="w-full px-5 py-3 bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 rounded-xl outline-none focus:border-primary dark:text-white font-bold text-sm resize-none"></textarea>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="sticky top-28">
                <div class="mx-auto w-full max-w-[320px] aspect-[9/19] bg-zinc-900 rounded-[3rem] p-3 shadow-2xl border-[8px] border-zinc-800 overflow-hidden relative">
                    <div class="w-full h-full bg-white dark:bg-[#0c0c0e] rounded-[2.2rem] overflow-y-auto no-scrollbar flex flex-col">
                        <div class="relative p-6 text-center border-b border-zinc-50 dark:border-zinc-900">
                            <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-2xl mx-auto mb-4 border-2 border-white dark:border-zinc-800 shadow-sm overflow-hidden flex items-center justify-center text-2xl">
                                @if($business->logo) <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover"> @else 🏬 @endif
                            </div>
                            <h2 class="text-sm font-black dark:text-white" x-text="businessName"></h2>
                            <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ $business->category->name ?? '' }}</p>
                        </div>

                        <div class="flex-grow">
                            <template x-for="section in sections" :key="section.id || index">
                                <div class="py-6 px-6 border-b border-zinc-50 dark:border-zinc-900/50">
                                    <template x-if="section.section_type == 'hero'">
                                        <div class="text-center space-y-1">
                                            <h3 class="text-xs font-black dark:text-white" x-text="section.content.title"></h3>
                                            <p class="text-[8px] text-zinc-500 font-medium" x-text="section.content.subtitle"></p>
                                        </div>
                                    </template>
                                    <template x-if="section.section_type == 'about'">
                                        <div class="space-y-2">
                                            <h3 class="text-[10px] font-black dark:text-white">عن الشركة</h3>
                                            <p class="text-[8px] text-zinc-500 leading-relaxed" x-text="section.content.text"></p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <div class="p-6">
                            <div class="w-full py-3 bg-green-500 text-white rounded-xl text-[9px] font-black text-center shadow-lg shadow-green-500/20">تواصل عبر واتساب</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    function builder(config) {
        return {
            businessName: config.name,
            whatsapp: config.phone,
            sections: config.initialSections.map(s => ({
                id: s.id,
                section_type: s.section_type,
                template_key: s.template_key || 'default',
                content: typeof s.content === 'string' ? JSON.parse(s.content) : s.content,
                open: false
            })),

            getTemplates(type) {
                const map = {
                    'hero': ['default', 'centered', 'minimal'],
                    'about': ['default', 'modern'],
                    'gallery': ['grid', 'slider'],
                    'contact': ['default']
                };
                return map[type] || ['default'];
            },

            addSection() {
                const type = prompt("hero, about, gallery, contact", "hero");
                if (!type) return;
                let content = type === 'hero' ? {
                        title: 'عنوان القسم',
                        subtitle: 'وصف فرعي'
                    } :
                    type === 'about' ? {
                        text: 'تفاصيل عنا'
                    } :
                    type === 'gallery' ? {
                        title: 'المعرض'
                    } : {
                        phone: this.whatsapp
                    };
                this.sections.push({
                    id: Date.now(),
                    section_type: type,
                    template_key: 'default',
                    content: content,
                    open: true
                });
            },

            removeSection(index) {
                if (confirm('حذف القسم؟')) this.sections.splice(index, 1);
            },

            async saveData() {
                try {
                    const mainForm = new FormData();
                    mainForm.append('_token', '{{ csrf_token() }}');
                    mainForm.append('_method', 'PUT');
                    mainForm.append('name', this.businessName);
                    mainForm.append('whatsapp', this.whatsapp);
                    mainForm.append('category_id', '{{ $business->category_id }}');
                    mainForm.append('city_id', '{{ $business->city_id }}');

                    await fetch('{{ route("business.update", $business->id) }}', {
                        method: 'POST',
                        body: mainForm
                    });

                    const response = await fetch('{{ route("business.sections.sync", $business->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            sections: this.sections
                        })
                    });

                    if (response.ok) alert('تم الحفظ بنجاح ✨');
                } catch (e) {
                    alert('خطأ في الحفظ');
                }
            }
        }
    }
</script>

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection