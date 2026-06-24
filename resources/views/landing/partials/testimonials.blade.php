@php
    $testimonials = [
        ['text' => __('landing.testimonial_1_text'), 'name' => __('landing.testimonial_1_name'), 'role' => __('landing.testimonial_1_role'), 'initial' => 'A'],
        ['text' => __('landing.testimonial_2_text'), 'name' => __('landing.testimonial_2_name'), 'role' => __('landing.testimonial_2_role'), 'initial' => 'S'],
        ['text' => __('landing.testimonial_3_text'), 'name' => __('landing.testimonial_3_name'), 'role' => __('landing.testimonial_3_role'), 'initial' => 'O'],
    ];
@endphp

<section class="py-24 lg:py-32 px-4 sm:px-6 bg-slate-50/40 dark:bg-zinc-950/30 border-y border-slate-100 dark:border-zinc-900/50">
    <div class="max-w-7xl mx-auto">
        <header class="text-center max-w-3xl mx-auto mb-16 lg:mb-20">
            <p class="reveal text-xs font-bold uppercase tracking-widest text-primary mb-4">{{ __('landing.testimonials_badge') }}</p>
            <h2 class="reveal text-3xl sm:text-4xl lg:text-5xl font-[900] tracking-tight text-slate-950 dark:text-white">
                {{ __('landing.testimonials_heading') }}
            </h2>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            @foreach ($testimonials as $t)
                <figure class="reveal luxury-card p-8 flex flex-col h-full">
                    <div class="flex gap-1 mb-6" aria-hidden="true">
                        @for ($s = 0; $s < 5; $s++)
                            <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                        @endfor
                    </div>
                    <blockquote class="flex-1">
                        <p class="text-sm sm:text-base text-slate-700 dark:text-zinc-300 font-medium leading-relaxed">“{{ $t['text'] }}”</p>
                    </blockquote>
                    <figcaption class="flex items-center gap-3 pt-8 mt-8 border-t border-slate-100 dark:border-zinc-800/80">
                        <span class="w-10 h-10 rounded-full bg-primary/15 flex items-center justify-center text-sm font-bold text-primary">{{ $t['initial'] }}</span>
                        <span>
                            <cite class="not-italic text-sm font-bold text-slate-900 dark:text-white block">{{ $t['name'] }}</cite>
                            <span class="text-xs text-slate-500 dark:text-zinc-500 font-medium">{{ $t['role'] }}</span>
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
