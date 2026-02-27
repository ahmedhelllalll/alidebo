<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل | {{ $business->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { colors: { primary: '#f45018' }, fontFamily: { cairo: ['Cairo', 'sans-serif'] } } }
        }
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; transition: all 0.3s ease; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .dark .glass-card { background: rgba(24, 24, 27, 0.65); border: 1px solid rgba(255, 255, 255, 0.08); }
        .tab-active { color: #f45018; border-bottom: 3px solid #f45018; }
        .loader { border-top-color: #f45018; animation: spinner 0.6s linear infinite; }
        @keyframes spinner { to { transform: rotate(360deg); } }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .section-item { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-[#09090b] text-slate-900 dark:text-zinc-100 min-h-screen pb-20">

    <div id="loading-overlay" class="hidden fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] flex flex-col items-center">
            <div class="loader w-12 h-12 border-4 border-slate-200 rounded-full mb-4"></div>
            <p class="font-black">جاري المعالجة... يرجى الانتظار</p>
        </div>
    </div>

    <div class="fixed top-24 left-1/2 -translate-x-1/2 z-[60] w-full max-w-md px-4 text-center">
        @if(session('error'))
            <div class="bg-red-500 text-white p-4 rounded-2xl shadow-xl font-bold mb-2 animate-bounce">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-2xl shadow-xl font-bold mb-2">✨ تم التحديث بنجاح</div>
        @endif
    </div>

    <div class="fixed top-0 left-0 right-0 z-50 flex justify-center p-4">
        <header class="glass-card w-full max-w-5xl h-16 rounded-3xl flex items-center justify-between px-6 shadow-lg">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-black text-slate-500 hover:text-primary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                الرجوع
            </a>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-primary rounded-xl flex items-center justify-center text-white text-xs font-black">AD</div>
                <span class="font-black tracking-tighter italic text-lg">alidebo</span>
            </div>
            <button onclick="toggleTheme()" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors">🌓</button>
        </header>
    </div>

    <main class="max-w-5xl mx-auto pt-32 px-6">
        <div class="flex border-b border-slate-200 dark:border-zinc-800 mb-8 overflow-x-auto no-scrollbar">
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
                        <label class="text-[10px] font-black px-2 opacity-50 uppercase tracking-widest">نبذة تعريفية</label>
                        <textarea name="description" rows="4" class="w-full px-6 py-4 bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl outline-none font-bold focus:ring-2 focus:ring-primary transition-all">{{ $business->description }}</textarea>
                    </div>
                    <button type="submit" onclick="showLoader()" class="w-full py-5 bg-primary text-white rounded-2xl font-black text-lg shadow-lg shadow-primary/30 hover:brightness-110 active:scale-[0.98] transition-all">حفظ التغييرات</button>
                </div>
                <div class="glass-card rounded-[2.5rem] p-8 border-dashed border-2 border-slate-200 dark:border-zinc-800 flex flex-col items-center justify-center space-y-4">
                    <div id="image-preview" class="w-32 h-32 bg-slate-100 dark:bg-zinc-800 rounded-3xl overflow-hidden border border-slate-200 dark:border-zinc-700 shadow-inner">
                        @if($business->logo) <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover"> @else <div class="w-full h-full flex items-center justify-center text-5xl">🏢</div> @endif
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
                        <p class="text-slate-500 text-sm font-bold italic mt-1">المرفوع حالياً: {{ $business->media->count() }} / 12</p>
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
                                <button type="submit" class="px-5 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 font-bold text-sm shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">حذف الصورة</button>
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
                    <p class="text-xs text-slate-500 font-bold">اختر نوع القسم الذي تريد ظهوره في صفحتك العامة</p>
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
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                <svg id="icon-sec-{{ $section->id }}" class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
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
                    <div class="py-20 text-center bg-slate-100/50 dark:bg-zinc-900/50 rounded-[2.5rem] border-2 border-dashed border-slate-200 dark:border-zinc-800 opacity-50">
                        لا توجد أقسام.. ابدأ بإضافة قسم جديد 👆
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <script>
        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        }

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
            const isHidden = el.classList.contains('hidden');
            if(isHidden) { el.classList.remove('hidden'); icon.classList.add('rotate-180'); } 
            else { el.classList.add('hidden'); icon.classList.remove('rotate-180'); }
        }

        function previewLogo(event) {
            const reader = new FileReader();
            reader.onload = (e) => { document.getElementById('image-preview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`; };
            reader.readAsDataURL(event.target.files[0]);
        }

        function showLoader() { document.getElementById('loading-overlay').classList.remove('hidden'); }

        function submitMediaForm() {
            const input = document.getElementById('media-input');
            if (input.files.length < 3) { alert('يرجى اختيار 3 صور على الأقل'); return; }
            showLoader();
            document.getElementById('media-form').submit();
        }
    </script>
</body>
</html>