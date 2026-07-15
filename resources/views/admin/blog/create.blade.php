@extends('admin.layouts.admin')

@section('title', __('admin.add_blog'))

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 dashboard-header-reveal">
        <div>
            <h1 class="text-2xl sm:text-3xl font-[900] tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">{{ __('admin.add_blog') }}</h1>
        </div>
        <a href="{{ route('admin.blogs.index') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-[#121214] hover:bg-slate-50 dark:hover:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-zinc-300 rounded-xl font-[900] text-[13px] transition-all shadow-sm">
            <i class="fa-solid fa-arrow-left text-[14px]"></i>
            {{ __('admin.cancel') }}
        </a>
    </div>

    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" class="dashboard-card-reveal" 
          x-data="{ 
              activeTab: 'en', 
              mediaType: 'image', 
              imagePreview: null, 
              videoPreview: null,
              fileError: null,
              validateImage(e) {
                  this.fileError = null;
                  const file = e.target.files[0];
                  if (!file) { this.imagePreview = null; return; }
                  if (file.size > 2 * 1024 * 1024) {
                      this.fileError = '{{ __('admin.max_size_image') }}';
                      e.target.value = '';
                      this.imagePreview = null;
                      return;
                  }
                  this.imagePreview = URL.createObjectURL(file);
              },
              validateVideo(e) {
                  this.fileError = null;
                  const file = e.target.files[0];
                  if (!file) { this.videoPreview = null; return; }
                  if (file.size > 20 * 1024 * 1024) {
                      this.fileError = '{{ __('admin.max_size_video') }}';
                      e.target.value = '';
                      this.videoPreview = null;
                      return;
                  }
                  this.videoPreview = URL.createObjectURL(file);
              }
          }">
        @csrf
        <div class="bg-white dark:bg-[#121214] shadow-[0_10px_40px_rgba(0,0,0,0.04)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.4)] border border-slate-200 dark:border-white/10 rounded-2xl p-6 space-y-6">
            
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 p-4 rounded-xl text-sm font-bold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Language Tabs --}}
            <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-white/10 pb-4">
                @foreach(['en' => 'English', 'ar' => 'العربية', 'es' => 'Español', 'de' => 'Deutsch', 'zh' => '中文', 'tr' => 'Türkçe'] as $loc => $label)
                    <button type="button" 
                        @click="activeTab = '{{ $loc }}'"
                        :class="activeTab === '{{ $loc }}' ? 'bg-primary text-white shadow-md' : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700'"
                        class="px-4 py-2 rounded-xl text-sm font-bold transition-all">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.title') }}</label>
                    @foreach(['en', 'ar', 'es', 'de', 'zh', 'tr'] as $loc)
                        <div x-show="activeTab === '{{ $loc }}'" x-cloak>
                            <input type="text" name="title[{{ $loc }}]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="{{ strtoupper($loc) }} Title" {{ $loc === 'en' ? 'required' : '' }}>
                        </div>
                    @endforeach
                </div>
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.slug') }} ({{ __('admin.optional') }})</label>
                    <input type="text" name="slug" id="slug" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" data-ignore-id="">
                    <p id="slug_error" class="mt-1 text-xs text-red-500 hidden"><i class="fa-solid fa-circle-exclamation mr-1"></i> This slug is already taken. Please choose another.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.content') }}</label>
                @foreach(['en', 'ar', 'es', 'de', 'zh', 'tr'] as $loc)
                    <div x-show="activeTab === '{{ $loc }}'" x-cloak class="mt-1">
                        <textarea name="content[{{ $loc }}]" rows="12" class="tinymce-editor block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="{{ strtoupper($loc) }} Content"></textarea>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Status --}}
                <div class="input-group">
                    <label id="status_label" class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 block text-start">
                        {{ __('admin.status') }} <span class="text-red-400" aria-hidden="true">*</span>
                    </label>
                    <div class="relative custom-smart-dropdown" id="dropdown_status">
                        <div class="relative flex items-center group">
                            <div class="absolute start-0 top-0 bottom-0 w-14 flex items-center justify-center text-slate-400 z-10 pointer-events-none transition-colors group-focus-within:text-primary">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </div>
                            <input type="hidden" name="status" id="field_status" value="published" required aria-required="true" class="validate-target">
                            <button type="button" class="dropdown-trigger w-full input-premium rounded-2xl py-5 ps-14 pe-6 text-sm font-bold flex items-center justify-between transition-all duration-300 outline-none cursor-pointer text-slate-800 dark:text-white">
                                <span class="dropdown-label truncate">{{ __('admin.published') }}</span>
                                <i aria-hidden="true" class="fa-solid fa-chevron-down text-[10px] text-slate-300 dark:text-zinc-600 transition-transform duration-300"></i>
                            </button>
                        </div>
                        <div class="dropdown-menu hidden absolute z-50 mt-2 w-full bg-white dark:bg-[#121214] border border-slate-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden opacity-0 translate-y-2">
                            <div class="dropdown-list max-h-60 overflow-y-auto custom-scrollbar p-2 space-y-1">
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="published" data-label="{{ __('admin.published') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-globe text-primary"></i> {{ __('admin.published') }}</div>
                                </div>
                                <div class="dropdown-item p-3 rounded-xl hover:bg-primary/[0.04] text-slate-700 dark:text-zinc-300 font-bold text-xs cursor-pointer transition-colors" data-id="draft" data-label="{{ __('admin.draft') }}">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-file text-slate-400"></i> {{ __('admin.draft') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Media Selector --}}
                <div class="input-group col-span-1 md:col-span-2 space-y-4">
                    <label class="text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest block text-start">
                        {{ __('admin.media_type') }}
                    </label>
                    
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-700 dark:text-zinc-300">
                            <input type="radio" name="media_type" value="image" x-model="mediaType" class="text-primary focus:ring-primary border-gray-300">
                            <i class="fa-solid fa-image text-slate-400"></i> {{ __('admin.image') }}
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-700 dark:text-zinc-300">
                            <input type="radio" name="media_type" value="video_embed" x-model="mediaType" class="text-primary focus:ring-primary border-gray-300">
                            <i class="fa-brands fa-youtube text-slate-400"></i> {{ __('admin.video_embed') }}
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-700 dark:text-zinc-300">
                            <input type="radio" name="media_type" value="video_upload" x-model="mediaType" class="text-primary focus:ring-primary border-gray-300">
                            <i class="fa-solid fa-file-video text-slate-400"></i> {{ __('admin.video_upload') }}
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-bold text-slate-700 dark:text-zinc-300">
                            <input type="radio" name="media_type" value="none" x-model="mediaType" class="text-primary focus:ring-primary border-gray-300">
                            <i class="fa-solid fa-ban text-slate-400"></i> {{ __('admin.none') }}
                        </label>
                    </div>

                    <template x-if="fileError">
                        <div class="mt-2 text-red-500 text-xs font-bold bg-red-50 dark:bg-red-900/20 p-2 rounded-lg border border-red-200 dark:border-red-900/50">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> <span x-text="fileError"></span>
                        </div>
                    </template>

                    {{-- Image Upload --}}
                    <div x-show="mediaType === 'image'" x-cloak class="mt-4">
                        <div class="relative w-full rounded-2xl border-2 border-dashed border-slate-300 dark:border-zinc-700 bg-slate-50 dark:bg-[#18181b] hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors py-12 text-center overflow-hidden" :class="{'border-red-500 bg-red-50/50 dark:bg-red-900/10': fileError}">
                            <input type="file" name="image_upload" id="image_upload" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="validateImage($event)">
                            <i class="fa-solid fa-cloud-arrow-up text-4xl text-slate-400 dark:text-zinc-500 mb-3"></i>
                            <p class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ __('admin.drag_drop_image') }}</p>
                            <p class="text-xs text-slate-500 dark:text-zinc-500">{{ __('admin.max_size_image') }}</p>
                        </div>
                        
                        <template x-if="imagePreview">
                            <div class="mt-4">
                                <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Live Preview</p>
                                <img :src="imagePreview" alt="Preview" class="w-48 h-32 object-cover rounded-xl border border-slate-200 shadow-md">
                            </div>
                        </template>
                    </div>

                    {{-- Video Embed --}}
                    <div x-show="mediaType === 'video_embed'" x-cloak class="mt-4 p-4 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-white/5">
                        <label for="video_url" class="block text-xs font-bold text-slate-600 dark:text-zinc-400 mb-2">
                            {{ __('admin.video_url_placeholder') }}
                        </label>
                        <input type="url" name="video_url" id="video_url" placeholder="https://www.youtube.com/embed/..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    </div>

                    {{-- Video Upload --}}
                    <div x-show="mediaType === 'video_upload'" x-cloak class="mt-4">
                        <div class="relative w-full rounded-2xl border-2 border-dashed border-slate-300 dark:border-zinc-700 bg-slate-50 dark:bg-[#18181b] hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors py-12 text-center overflow-hidden" :class="{'border-red-500 bg-red-50/50 dark:bg-red-900/10': fileError}">
                            <input type="file" name="video_upload" id="video_upload" accept="video/mp4,video/webm,video/ogg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="validateVideo($event)">
                            <i class="fa-solid fa-film text-4xl text-slate-400 dark:text-zinc-500 mb-3"></i>
                            <p class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ __('admin.drag_drop_video') }}</p>
                            <p class="text-xs text-slate-500 dark:text-zinc-500">{{ __('admin.max_size_video') }}</p>
                        </div>

                        <template x-if="videoPreview">
                            <div class="mt-4 w-full max-w-sm rounded-xl overflow-hidden border border-slate-200 shadow-md bg-black">
                                <p class="text-[10px] font-bold text-slate-400 mb-2 mt-2 ml-2 uppercase tracking-widest">Live Preview</p>
                                <video :src="videoPreview" class="w-full aspect-video object-contain" controls></video>
                            </div>
                        </template>
                    </div>

                    {{-- Media Alt Text --}}
                    <div x-show="mediaType !== 'none'" x-cloak class="mt-6 border-t border-slate-200 dark:border-white/10 pt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.media_alt') ?? 'Media Alt Text (SEO)' }}</label>
                        @foreach(['en', 'ar', 'es', 'de', 'zh', 'tr'] as $loc)
                            <div x-show="activeTab === '{{ $loc }}'" x-cloak>
                                <input type="text" name="media_alt[{{ $loc }}]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="{{ strtoupper($loc) }} Alt Text">
                            </div>
                        @endforeach
                        <p class="text-[10px] text-slate-500 mt-1">Provide alternative text for images or title for videos to improve accessibility and SEO.</p>
                    </div>
                </div>
            </div>

            <x-admin.seo-editor />

            <div class="flex justify-end pt-6 border-t border-slate-200 dark:border-gray-700">
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-light text-white rounded-xl font-[900] text-[13px] shadow-[0_8px_20px_rgba(244,80,24,0.25)] hover:shadow-[0_12px_25px_rgba(244,80,24,0.35)] transition-all active:scale-[0.98]">
                    <i class="fa-solid fa-save text-[14px]"></i>
                    {{ __('admin.save_blog') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '.tinymce-editor',
        plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
        toolbar_mode: 'floating',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        height: 400,
        menubar: false,
        base_url: '{{ asset('js/tinymce') }}',
        suffix: '.min',
        license_key: 'gpl',
        promotion: false,
        skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
        content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save(); // ensure textarea gets updated for form submission
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const slugInput = document.getElementById('slug');
        const slugError = document.getElementById('slug_error');
        const submitBtn = document.querySelector('button[type="submit"]');
        let debounceTimer;

        slugInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const slug = this.value.trim();
            const ignoreId = this.dataset.ignoreId || '';
            
            if (slug.length === 0) {
                slugError.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('admin.blogs.check-slug') }}?slug=${encodeURIComponent(slug)}&ignore=${ignoreId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.exists) {
                            slugError.classList.remove('hidden');
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            slugError.classList.add('hidden');
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    })
                    .catch(error => console.error('Error checking slug:', error));
            }, 500);
        });
    });
</script>
@endpush
