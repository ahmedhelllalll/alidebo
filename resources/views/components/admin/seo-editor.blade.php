@props(['model' => null])

@php
    $seo = $model ? $model->seoMetadata : null;
    $locale = app()->getLocale();
    
    $metaTitle = $seo ? ($seo->meta_title[$locale] ?? ($seo->meta_title['en'] ?? '')) : '';
    $metaDesc = $seo ? ($seo->meta_description[$locale] ?? ($seo->meta_description['en'] ?? '')) : '';
    $ogImage = $seo ? $seo->og_image_url : '';
@endphp

<div class="mt-4" x-data="seoPreview()">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('admin.seo_settings') }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.seo_desc') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
        <!-- Input Fields -->
        <div class="space-y-4">
            <div>
                <label for="seo_meta_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.meta_title') }}</label>
                <input type="text" name="seo_metadata[meta_title]" id="seo_meta_title" x-model="title" @input="updatePreview"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                       placeholder="e.g. Best Services | Alidebo" maxlength="60">
                <p class="mt-1 text-xs text-gray-500 text-right"><span x-text="title.length"></span>/60</p>
            </div>

            <div>
                <label for="seo_meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.meta_description') }}</label>
                <textarea name="seo_metadata[meta_description]" id="seo_meta_description" rows="3" x-model="description" @input="updatePreview"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                          placeholder="A brief description of the page..." maxlength="160"></textarea>
                <p class="mt-1 text-xs text-gray-500 text-right"><span x-text="description.length"></span>/160</p>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-500 dark:text-zinc-400 uppercase tracking-widest mb-2.5 text-start">{{ __('admin.open_graph_image') }}</label>
                
                <div x-data="{ 
                        dragover: false, 
                        previewUrl: '{{ $ogImage }}',
                        handleFileChange(event) {
                            const file = event.target.files[0] || (event.dataTransfer && event.dataTransfer.files[0]);
                            if (file) {
                                this.previewUrl = URL.createObjectURL(file);
                            } else {
                                this.previewUrl = '{{ $ogImage }}';
                            }
                        }
                     }"
                     @dragover.prevent="dragover = true"
                     @dragleave.prevent="dragover = false"
                     @drop.prevent="dragover = false; if($event.dataTransfer.files.length > 0) { $refs.fileInput.files = $event.dataTransfer.files; handleFileChange($event) }"
                     class="relative group"
                >
                    <label for="seo_og_image" 
                           class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-2xl cursor-pointer transition-all duration-300"
                           :class="dragover ? 'border-primary bg-primary/[0.02]' : 'border-slate-300 dark:border-white/10 bg-slate-50 dark:bg-white/[0.02] hover:bg-slate-100 dark:hover:bg-white/[0.04]'">
                        
                        <div x-show="!previewUrl" class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl mb-3 text-slate-400 group-hover:text-primary transition-colors"></i>
                            <p class="mb-2 text-sm text-slate-500 dark:text-zinc-400 font-bold"><span class="font-black text-primary">{{ __('admin.click_to_upload') }}</span> {{ __('admin.drag_and_drop') }}</p>
                            <p class="text-xs text-slate-400 dark:text-zinc-500">{{ __('admin.max_file_size') }}</p>
                        </div>

                        <div x-show="previewUrl" class="absolute inset-0 w-full h-full p-2" style="display: none;">
                            <img :src="previewUrl" class="w-full h-full object-contain rounded-xl" />
                            <div class="absolute inset-0 bg-black/50 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center m-2">
                                <span class="text-white font-bold text-sm bg-black/50 px-3 py-1 rounded-lg"><i class="fa-solid fa-pen mr-1"></i> {{ __('admin.change_image') }}</span>
                            </div>
                        </div>

                        <input type="file" name="seo_metadata[og_image]" id="seo_og_image" x-ref="fileInput" @change="handleFileChange" class="hidden" accept="image/*" />
                    </label>
                </div>
            </div>
        </div>

        <!-- Live Preview -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.google_search_preview') }}</h4>
            <div class="bg-white dark:bg-[#202124] p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="flex items-center text-sm text-gray-500 dark:text-[#bdc1c6] mb-1">
                    <span>{{ config('app.url') }}</span>
                    <span class="mx-1">›</span>
                    <span>...</span>
                </div>
                <h3 class="text-xl text-[#1a0dab] dark:text-[#8ab4f8] hover:underline cursor-pointer truncate" x-text="previewTitle"></h3>
                <p class="text-sm text-[#4d5156] dark:text-[#bdc1c6] mt-1 line-clamp-2" x-text="previewDescription"></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('seoPreview', () => ({
            title: `{{ $metaTitle }}`,
            description: `{{ $metaDesc }}`,
            
            get previewTitle() {
                return this.title.trim() === '' ? 'Please enter a Meta Title' : this.title;
            },
            
            get previewDescription() {
                return this.description.trim() === '' ? 'Please enter a Meta Description to see how it looks on Google.' : this.description;
            },
            
            updatePreview() {
                // Alpine handles reactivity
            }
        }))
    })
</script>
