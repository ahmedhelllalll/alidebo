<button id="scrollToTopBtn" 
        class="fixed bottom-6 right-6 rtl:right-auto rtl:left-6 z-50 p-3.5 bg-primary/90 backdrop-blur-md text-white rounded-full shadow-lg shadow-primary/30 border border-white/10 hover:bg-primary hover:shadow-xl hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300 opacity-0 pointer-events-none translate-y-4 flex items-center justify-center group"
        aria-label="Scroll to top">
    <svg class="w-5 h-5 transition-transform duration-300 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
    </svg>
</button>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        
        scrollToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.lenis) {
                // Use a smoother and slightly faster quartic ease-out function
                window.lenis.scrollTo(0, { 
                    duration: 1.0, 
                    easing: (t) => 1 - Math.pow(1 - t, 4)
                });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        // Listen to scroll events to show/hide the button
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                scrollToTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                scrollToTopBtn.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
            } else {
                scrollToTopBtn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                scrollToTopBtn.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            }
        });
    });
</script>
