document.addEventListener('DOMContentLoaded', () => {

    /* ── MENÚ HAMBURGUESA ── */
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
                menuToggle.querySelector('i').classList.replace('fa-times', 'fa-bars');
            });
        });
    }

    /* ── CARRUSEL ── */
    const track       = document.getElementById('carouselTrack');
    const btnPrev     = document.getElementById('carouselPrev');
    const btnNext     = document.getElementById('carouselNext');
    const dotsWrapper = document.getElementById('carouselDots');

    if (!track) return;

    const cards = Array.from(track.querySelectorAll('.portfolio-card'));
    if (cards.length === 0) return;

    const GAP = 24;
    let currentIndex = 0;

    /* Cuántas tarjetas se ven según el ancho de pantalla */
    function getPerView() {
        const w = window.innerWidth;
        if (w < 600)  return 1;
        if (w < 1024) return 2;
        return 3;
    }

    /* Ancho real de UNA tarjeta (leído del DOM, no hardcodeado) */
    function getCardWidth() {
        return cards[0].getBoundingClientRect().width;
    }

    /* Total de posiciones de desplazamiento posibles
       — CORREGIDO: usa cards individuales, no páginas,
         así no queda espacio vacío al final             */
    function getMaxIndex() {
        return Math.max(0, cards.length - getPerView());
    }

    /* ── Dots ── */
    function buildDots() {
        dotsWrapper.innerHTML = '';
        const max = getMaxIndex();
        for (let i = 0; i <= max; i++) {
            const btn = document.createElement('button');
            btn.className = 'carousel-dot' + (i === 0 ? ' active' : '');
            btn.setAttribute('aria-label', `Ir a portafolio ${i + 1}`);
            btn.addEventListener('click', () => goTo(i));
            dotsWrapper.appendChild(btn);
        }
    }

    /* ── Ir a una posición ── */
    function goTo(index) {
        currentIndex = Math.max(0, Math.min(index, getMaxIndex()));

        /* Desplazamiento exacto: (ancho tarjeta + gap) × índice */
        const offset = currentIndex * (getCardWidth() + GAP);
        track.style.transform = `translateX(-${offset}px)`;

        /* Actualizar dots */
        dotsWrapper.querySelectorAll('.carousel-dot').forEach((d, i) => {
            d.classList.toggle('active', i === currentIndex);
        });
    }

    btnPrev.addEventListener('click', () => goTo(currentIndex - 1));
    btnNext.addEventListener('click', () => goTo(currentIndex + 1));

    /* Reiniciar al redimensionar */
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            buildDots();
            goTo(0);
        }, 150);
    });

    /* Iniciar */
    buildDots();
    goTo(0);
});