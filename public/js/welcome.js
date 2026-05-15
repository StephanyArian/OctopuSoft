/* =======Archivo: public/js/welcome.js======= */

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

    /* ------ CARRUSEL DE PORTAFOLIOS------- */
    const track       = document.getElementById('carouselTrack');
    const btnPrev     = document.getElementById('carouselPrev');
    const btnNext     = document.getElementById('carouselNext');
    const dotsWrapper = document.getElementById('carouselDots');

    if (!track) return;

    const cards   = track.querySelectorAll('.portfolio-card');
    const gap     = 24;   
    const visible = window.innerWidth < 600 ? 1 
              : window.innerWidth < 1024 ? 2 
              : 3;

    const total   = Math.max(1, cards.length - visible + 1);
    let current   = 0;

    /* Crear dots */
    for (let i = 0; i < total; i++) {
        const dot = document.createElement('button');
        dot.className   = 'carousel-dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', `Ir a portafolio ${i + 1}`);
        dot.addEventListener('click', () => goTo(i));
        dotsWrapper.appendChild(dot);
    }

    function goTo(index) {
        current = Math.max(0, Math.min(index, total - 1));
        const cardWidth = cards[0].offsetWidth + gap;
        track.style.transform = `translateX(-${current * cardWidth}px)`;

        dotsWrapper.querySelectorAll('.carousel-dot').forEach((d, i) => {
            d.classList.toggle('active', i === current);
        });
    }

    btnPrev.addEventListener('click', () => goTo(current - 1));
    btnNext.addEventListener('click', () => goTo(current + 1));

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => goTo(0), 200);
    });

});