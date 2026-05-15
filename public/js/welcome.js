document.addEventListener('DOMContentLoaded', () => {

    /* -------MENÚ HAMBURGUESA (móvil)----------- */
    const menuToggle = document.getElementById('menuToggle');
    const navLinks   = document.getElementById('navLinks');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            const icon   = menuToggle.querySelector('i');
            const isOpen = navLinks.classList.contains('active');
            icon.classList.toggle('fa-bars',  !isOpen);
            icon.classList.toggle('fa-times',  isOpen);
        });

        navLinks.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                icon.classList.replace('fa-times', 'fa-bars');
            });
        });
    }

    /* ------ CARRUSEL DE PORTAFOLIOS RESPONSIVE------- */
    const track   = document.getElementById('carouselTrack');
    const btnPrev = document.getElementById('carouselPrev');
    const btnNext = document.getElementById('carouselNext');
    const dotsWrapper = document.getElementById('carouselDots');

    if (!track) return;

    let currentIndex = 0;
    let cardWidth = 300;
    const gap = 24;

    function updateCardWidth() {
        const screenWidth = window.innerWidth;
        
        if (screenWidth < 600) {
            cardWidth = screenWidth - 48;
        } else if (screenWidth < 1024) {
            cardWidth = 280;
        } else {
            cardWidth = 300;
        }
        return cardWidth;
    }

    function getCardsPerView() {
        const screenWidth = window.innerWidth;
        if (screenWidth < 600) return 1;
        if (screenWidth < 1024) return 2;
        return 3;
    }

    function getTotalPages() {
        const cards = track.children.length;
        const perView = getCardsPerView();
        return Math.ceil(cards / perView);
    }

    function updateDots() {
        if (!dotsWrapper) return;
        const totalPages = getTotalPages();
        dotsWrapper.innerHTML = '';
        
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot' + (i === currentIndex ? ' active' : '');
            dot.setAttribute('aria-label', `Ir a página ${i + 1}`);
            dot.addEventListener('click', () => goToPage(i));
            dotsWrapper.appendChild(dot);
        }
    }

    function goToPage(page) {
        currentIndex = Math.max(0, Math.min(page, getTotalPages() - 1));
        const offset = -currentIndex * (cardWidth + gap) * getCardsPerView();
        track.style.transform = `translateX(${offset}px)`;
        
        if (dotsWrapper) {
            const dots = dotsWrapper.querySelectorAll('.carousel-dot');
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentIndex);
            });
        }
    }

    function next() {
        if (currentIndex < getTotalPages() - 1) {
            goToPage(currentIndex + 1);
        }
    }

    function prev() {
        if (currentIndex > 0) {
            goToPage(currentIndex - 1);
        }
    }

    function initCarousel() {
        updateCardWidth();
        goToPage(0);
        updateDots();
    }

    if (btnPrev) btnPrev.addEventListener('click', prev);
    if (btnNext) btnNext.addEventListener('click', next);

    // Recargar carrusel al redimensionar la ventana
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            updateCardWidth();
            initCarousel();
        }, 150);
    });

    initCarousel();
});