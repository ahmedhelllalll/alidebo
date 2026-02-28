@extends('layouts.dashboard.app')

@section('content')
<div id="loading-overlay" class="hidden fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center">
    <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] flex flex-col items-center">
        <div class="loader w-12 h-12 border-4 border-slate-200 rounded-full mb-4"></div>
        <p class="font-black">جاري المعالجة... يرجى الانتظار</p>
    </div>
</div>

<main class="max-w-5xl mx-auto pt-10 px-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white">تعديل بيزنس: {{ $business->name }}</h1>
            <p class="text-slate-500 font-medium">تحكم في محتوى صفحتك العامة ومعرض أعمالك</p>
        </div>
        <a href="{{ route('profile.show', $business->slug) }}" target="_blank" class="px-6 py-3 bg-zinc-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 rounded-xl font-bold text-sm hover:bg-zinc-200 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            معاينة الصفحة الحية
        </a>
    </div>

    <div class="flex border-b border-slate-200 dark:border-zinc-800 mb-8 overflow-x-auto no-scrollbar bg-white dark:bg-zinc-950/50 rounded-t-3xl">
        <button onclick="switchTab('general')" id="btn-general" class="tab-active px-8 py-4 font-black text-sm whitespace-nowrap">البيانات الأساسية</button>
        <button onclick="switchTab('media')" id="btn-media" class="px-8 py-4 font-black text-sm text-slate-400 whitespace-nowrap">معرض الصور</button>
        <button onclick="switchTab('sections')" id="btn-sections" class="px-8 py-4 font-black text-sm text-slate-400 whitespace-nowrap">محتوى الأقسام</button>
    </div>

    <div id="tab-general" class="space-y-6">
        <form action="{{ route('business.update', $business->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf @method('PUT')
            <div class="lg:col-span-2 glass-card rounded-[2.5rem] p-8 space-y-6 shadow-xl">
                <div class="space-y-2">
                    <label class="text-[10px] font-black px-2 opacity-50 uppercase tracking-widest">اسم الشركة</label>
                    <input type="text" name="name" value="{{ $business->name }}" class="w-full px-6 py-4 bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl outline-none font-bold focus:ring-2 focus:ring-primary transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black px-2 opacity-50 uppercase tracking-widest">نبذة تعريفية (SEO)</label>
                    <textarea name="meta_description" rows="4" class="w-full px-6 py-4 bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl outline-none font-bold focus:ring-2 focus:ring-primary transition-all">{{ $business->meta_description }}</textarea>
                </div>
                <button type="submit" onclick="showLoader()" class="w-full py-5 bg-primary text-white rounded-2xl font-black text-lg shadow-lg shadow-primary/30 hover:brightness-110 active:scale-[0.98] transition-all">حفظ التغييرات</button>
            </div>

            <div class="glass-card rounded-[2.5rem] p-8 border-dashed border-2 border-slate-200 dark:border-zinc-800 flex flex-col items-center justify-center space-y-4">
                <div id="image-preview" class="w-32 h-32 bg-slate-100 dark:bg-zinc-800 rounded-3xl overflow-hidden border border-slate-200 dark:border-zinc-700 shadow-inner">
                    @if($business->logo)
                    <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-5xl">🏢</div>
                    @endif
                </div>
                <input type="file" name="logo" id="logo-input" class="hidden" accept="image/*" onchange="previewLogo(event)">
                <button type="button" onclick="document.getElementById('logo-input').click()" class="px-6 py-2 bg-slate-900 dark:bg-zinc-100 text-white dark:text-zinc-900 rounded-xl text-xs font-black">تغيير الشعار</button>
            </div>
        </form>
    </div>

    <div id="tab-media" class="hidden space-y-6">
        <div class="glass-card rounded-[2.5rem] p-8 shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white">معرض الأعمال</h2>
                    <p class="text-slate-500 text-sm font-bold italic mt-1">الحد الأقصى: 12 صورة</p>
                </div>
                <form action="{{ route('business.media.upload', $business->id) }}" method="POST" enctype="multipart/form-data" id="media-form">
                    @csrf
                    <input type="file" name="images[]" id="media-input" multiple class="hidden" accept="image/*" onchange="submitMediaForm()">
                    <button type="button" onclick="document.getElementById('media-input').click()" class="px-8 py-4 bg-primary text-white rounded-2xl font-black shadow-lg shadow-primary/20 hover:scale-105 transition-all">إضافة صور جديدة</button>
                </form>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse($business->media as $item)
                <div class="group relative aspect-square rounded-[1.5rem] overflow-hidden border border-slate-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-900 shadow-sm">
                    <img src="{{ asset('storage/' . $item->file_path) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <form action="{{ route('business.media.destroy', $item->id) }}" method="POST" class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-5 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 font-bold text-sm shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">حذف</button>
                    </form>
                </div>
                @empty
                <div class="col-span-full py-20 text-center border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-[2.5rem] opacity-50 font-bold">
                    المعرض فارغ حالياً 📸
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="tab-sections" class="hidden space-y-6">
        <div class="glass-card rounded-3xl p-6 mb-8 border-2 border-dashed border-primary/30 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="text-center md:text-right">
                <h3 class="font-black text-lg text-primary">إضافة عناصر للصفحة</h3>
                <p class="text-xs text-slate-500 font-bold">اختر نوع القسم الذي تريد ظهوره في بروفايلك</p>
            </div>
            <form action="{{ route('sections.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="business_profile_id" value="{{ $business->id }}">
                <select name="section_type" class="px-4 py-3 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl font-bold outline-none focus:ring-2 focus:ring-primary text-sm">
                    <option value="hero">قسم البداية (Hero)</option>
                    <option value="about">من نحن (About)</option>
                    <option value="gallery">المعرض (Gallery)</option>
                    <option value="contact">اتصل بنا (Contact)</option>
                </select>
                <button type="submit" onclick="showLoader()" class="px-6 py-3 bg-primary text-white rounded-xl font-black text-sm hover:scale-105 active:scale-95 transition-all">+ إضافة</button>
            </form>
        </div>

        <div class="space-y-4">
            @forelse($business->sections as $section)
            <div class="section-item glass-card rounded-[2rem] overflow-hidden shadow-md border border-slate-100 dark:border-zinc-800">
                <div class="w-full px-8 py-6 flex items-center justify-between">
                    <div class="flex items-center gap-4 cursor-pointer" onclick="toggleAccordion('sec-{{ $section->id }}')">
                        <span class="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center text-lg">
                            @if($section->section_type == 'hero') 🚀 @elseif($section->section_type == 'about') 📖 @elseif($section->section_type == 'gallery') 🖼️ @elseif($section->section_type == 'contact') 📞 @endif
                        </span>
                        <div class="text-right">
                            <h3 class="font-black text-slate-800 dark:text-zinc-100 uppercase tracking-tighter">قسم {{ $section->section_type }}</h3>
                            <p class="text-[10px] text-slate-400 font-bold italic">انقر لتعديل المحتوى</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('حذف هذا القسم؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                        <svg id="icon-sec-{{ $section->id }}" class="w-5 h-5 text-slate-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <div id="sec-{{ $section->id }}" class="hidden px-8 pb-8 border-t border-slate-50 dark:border-zinc-800/50 pt-6 bg-slate-50/30 dark:bg-zinc-900/10">
                    <form action="{{ route('sections.update', $section->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf @method('PUT')
                        @if($section->section_type == 'hero')
                        <div class="space-y-2">
                            <label class="text-[10px] font-black opacity-40 uppercase px-2">العنوان الرئيسي</label>
                            <input type="text" name="content[title]" value="{{ $section->content['title'] ?? '' }}" class="w-full px-5 py-3 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl outline-none focus:ring-2 focus:ring-primary font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black opacity-40 uppercase px-2">العنوان الفرعي</label>
                            <input type="text" name="content[subtitle]" value="{{ $section->content['subtitle'] ?? '' }}" class="w-full px-5 py-3 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl outline-none focus:ring-2 focus:ring-primary font-bold">
                        </div>
                        @elseif($section->section_type == 'about')
                        <div class="col-span-2 space-y-2">
                            <label class="text-[10px] font-black opacity-40 uppercase px-2">نص عن الشركة</label>
                            <textarea name="content[text]" rows="4" class="w-full px-5 py-3 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl outline-none focus:ring-2 focus:ring-primary font-bold">{{ $section->content['text'] ?? '' }}</textarea>
                        </div>
                        @elseif($section->section_type == 'gallery')
                        <div class="col-span-2 text-center py-4 text-xs font-bold text-slate-400 bg-white dark:bg-zinc-900 rounded-xl border border-slate-100 dark:border-zinc-800">
                            يعرض هذا القسم صور المعرض تلقائياً.
                        </div>
                        @elseif($section->section_type == 'contact')
                        <div class="space-y-2">
                            <label class="text-[10px] font-black opacity-40 uppercase px-2">رقم الهاتف / واتساب</label>
                            <input type="text" name="content[phone]" value="{{ $section->content['phone'] ?? '' }}" class="w-full px-5 py-3 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl outline-none focus:ring-2 focus:ring-primary font-bold">
                        </div>
                        @endif
                        <div class="md:col-span-2 flex justify-end pt-4">
                            <button type="submit" onclick="showLoader()" class="px-8 py-3 bg-primary text-white rounded-xl font-black text-sm shadow-lg shadow-primary/20 hover:brightness-110 transition-all">حفظ القسم</button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <div class="py-20 text-center bg-slate-100/50 dark:bg-zinc-900/50 rounded-[2.5rem] border-2 border-dashed border-slate-200 dark:border-zinc-800 opacity-50 font-black">
                لا توجد أقسام.. ابدأ بإضافة قسم جديد 👆
            </div>
            @endforelse
        </div>
    </div>
</main>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .dark .glass-card {
        background: rgba(24, 24, 27, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .tab-active {
        color: #f45018;
        border-bottom: 3px solid #f45018;
    }

    .loader {
        border-top-color: #f45018;
        animation: spinner 0.6s linear infinite;
    }

    @keyframes spinner {
        to {
            transform: rotate(360deg);
        }
    }

    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
</style>

<script>
    function switchTab(tab) {
        ['general', 'media', 'sections'].forEach(t => {
            document.getElementById('tab-' + t).classList.add('hidden');
            document.getElementById('btn-' + t).classList.replace('tab-active', 'text-slate-400');
        });
        document.getElementById('tab-' + tab).classList.remove('hidden');
        document.getElementById('btn-' + tab).classList.replace('text-slate-400', 'tab-active');
    }

    function toggleAccordion(id) {
        const el = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        el.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    function previewLogo(event) {
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('image-preview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function showLoader() {
        document.getElementById('loading-overlay').classList.remove('hidden');
    }

    function submitMediaForm() {
        showLoader();
        document.getElementById('media-form').submit();
    }
</script>
@endsection