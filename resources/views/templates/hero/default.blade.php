<section class="relative min-h-[70vh] flex items-center justify-center overflow-hidden bg-slate-900">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0 bg-gradient-to-br from-primary to-transparent"></div>
        @if($business->logo)
            <img src="{{ asset('storage/' . $business->logo) }}" class="w-full h-full object-cover blur-3xl">
        @endif
    </div>

    <div class="relative z-10 max-w-5xl mx-auto px-6 text-center">
        @if($business->logo)
            <img src="{{ asset('storage/' . $business->logo) }}" class="w-24 h-24 mx-auto mb-8 rounded-3xl shadow-2xl border-2 border-white/10 object-cover">
        @endif
        
        <h1 class="text-5xl md:text-7xl font-black text-white mb-6 tracking-tight">
            {{ $content['title'] ?? $business->name }}
        </h1>
        
        <p class="text-xl md:text-2xl text-slate-300 font-medium max-w-2xl mx-auto mb-10 leading-relaxed">
            {{ $content['subtitle'] ?? '' }}
        </p>

        <div class="flex flex-wrap justify-center gap-4">
            <a href="#contact" class="px-8 py-4 bg-primary text-white rounded-2xl font-black text-lg shadow-xl shadow-primary/30 hover:scale-105 transition-transform">
                تواصل معنا
            </a>
            <a href="#about" class="px-8 py-4 bg-white/10 text-white backdrop-blur-md rounded-2xl font-black text-lg border border-white/10 hover:bg-white/20 transition-all">
                اكتشف المزيد
            </a>
        </div>
    </div>
</section>