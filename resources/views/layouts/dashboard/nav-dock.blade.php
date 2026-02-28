<nav class="nav-dock" id="nav-dock">
    <div class="nav-logo-mobile">
        <img src="{{ asset('images/logo.webp') }}" class="w-9 h-9 object-contain" alt="Logo">
        <span class="text-lg font-black dark:text-white text-zinc-900">alidebo</span>
    </div>

    <div class="nav-links-container flex items-center gap-2 overflow-x-auto no-scrollbar px-2">
        @if(View::hasSection('nav_links'))
        @yield('nav_links')
        @else
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">الرئيسية</a>
        <a href="#features" class="nav-link">المميزات</a>
        <a href="#about-ali" class="nav-link">عن المنصة</a>
        @endif
    </div>

    <div class="hamburger" id="hamburger" onclick="toggleMobileMenu()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-6 h-6">
            <path class="line-1" d="M4 6h16"></path>
            <path class="line-2" d="M4 12h16"></path>
            <path class="line-3" d="M4 18h16"></path>
        </svg>
    </div>
</nav>

<script>
    // دالة فتح وإغلاق المنيو (التي كانت ناقصة)
    function toggleMobileMenu() {
        const navDock = document.getElementById('nav-dock');
        navDock.classList.toggle('menu-open');
        // هنا يمكنك إضافة منطق إظهار القائمة المنسدلة
    }

    function initScrollSpy() {
        const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
        const sections = [];

        navLinks.forEach(link => {
            const id = link.getAttribute('href');
            if (!id || id === '#') return;
            const section = document.querySelector(id);
            if (section) sections.push(section);

            link.onclick = (e) => {
                e.preventDefault();
                const target = document.querySelector(id);
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            };
        });

        const observerOptions = {
            root: null,
            rootMargin: '-25% 0px -65% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    navLinks.forEach(link => {
                        link.classList.toggle('active', link.getAttribute('href') === `#${id}`);
                    });
                }
            });
        }, observerOptions);

        sections.forEach(section => observer.observe(section));
    }

    document.addEventListener('DOMContentLoaded', initScrollSpy);

    window.addEventListener('scroll', () => {
        const nav = document.getElementById('nav-dock');
        if (window.scrollY > 20) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
</script>