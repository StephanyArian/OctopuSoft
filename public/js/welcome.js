document.addEventListener('DOMContentLoaded', () => {

    // Captura de elementos de la interfaz para filtros
    const searchInput = document.getElementById('searchRepo');
    const categorySelect = document.getElementById('filterCategory');
    const skillsSelect = document.getElementById('filterSkills');
    const sortSelect = document.getElementById('filterSort');
    const btnClear = document.getElementById('btnClearFilters');
    const portfoliosGrid = document.getElementById('portfoliosGrid');
    const totalResults = document.getElementById('totalResults');
    const totalResultsHero = document.getElementById('totalResultsHero');

    let debounceTimer;

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

    if (track && btnPrev && btnNext && dotsWrapper) {
        const originalCards = Array.from(track.querySelectorAll('.portfolio-card'));

        if (originalCards.length === 0) return;

            const GAP = 20;
            const DURATION = 450;
            let step = originalCards.length; 
            let busy = false;
            let autoTimer;

            function buildExtended() {
                track.innerHTML = '';
                [...originalCards, ...originalCards, ...originalCards].forEach(card => {
                    track.appendChild(card.cloneNode(true));
                });
            }

            function getPerView() {
                const w = window.innerWidth;
                if (w < 600)  return 1;
                if (w < 1024) return 2;
                return 4;
            }

            function cardWidth() {
                return track.querySelector('.portfolio-card').getBoundingClientRect().width;
            }    

            function offsetFor(idx) {
                return idx * (cardWidth() + GAP);
            }

            function realIndex() {
                const n = originalCards.length;
                return ((step - n) % n + n) % n;
            }

            function teleport(idx) {
                track.style.transition = 'none';
                track.offsetHeight; // forzar reflow
                track.style.transform = `translateX(-${offsetFor(idx)}px)`;
                step = idx;
            }

            function slide(idx) {
                track.style.transition = `transform ${DURATION}ms cubic-bezier(0.4,0,0.2,1)`;
                track.style.transform  = `translateX(-${offsetFor(idx)}px)`;
                step = idx;
            }

            function go(delta) {
                if (busy) return;
                busy = true;
                slide(step + delta);
                updateDots();

                setTimeout(() => {
                    const n = originalCards.length;
                
                    if (step <= 0 || step >= n * 2) {
                        teleport(n + realIndex());
                    }
                    busy = false;
                }, DURATION);
            }

            function buildDots() {
                dotsWrapper.innerHTML = '';
                const groups = Math.ceil(originalCards.length / getPerView());
                for (let i = 0; i < groups; i++) {
                    const btn = document.createElement('button');
                    btn.className = 'carousel-dot' + (i === 0 ? ' active' : '');
                    btn.setAttribute('aria-label', `Grupo ${i + 1}`);
                    btn.addEventListener('click', () => {
                        if (busy) return;
                        busy = true;
                        const target = originalCards.length + (i * getPerView());
                        slide(target);
                        updateDots();
                        setTimeout(() => { busy = false; }, DURATION);
                        resetAutoplay();
                    });
                    dotsWrapper.appendChild(btn);
                }
            }

            function updateDots() {
                const group = Math.floor(realIndex() / getPerView());
                dotsWrapper.querySelectorAll('.carousel-dot').forEach((d, i) => {
                    d.classList.toggle('active', i === group);
                });
            }

            function startAutoplay() { autoTimer = setInterval(() => go(1), 4000); }
            function resetAutoplay()  { clearInterval(autoTimer); startAutoplay(); }

            track.addEventListener('mouseenter', () => clearInterval(autoTimer));
            track.addEventListener('mouseleave', resetAutoplay);

            btnPrev.addEventListener('click', () => { go(-1); resetAutoplay(); });
            btnNext.addEventListener('click', () => { go(1);  resetAutoplay(); });

            /* ── Resize ── */
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    buildExtended();
                    buildDots();
                    teleport(originalCards.length);
                    updateDots();
                }, 150);
            });

            /* ── Iniciar ── */
            buildExtended();
            buildDots();
            teleport(originalCards.length);
            startAutoplay();
        }
    
    /* ── FILTRADO Y BUSCADOR AJAX ── */
    function fetchFilteredPortfolios() {
        const query = searchInput
        ? searchInput.value
           .normalize("NFD")
           .replace(/[\u0300-\u036f]/g, "")
        : '';
        const category = categorySelect ? categorySelect.value : '';
        const skill = skillsSelect ? skillsSelect.value : '';
        const sort = sortSelect ? sortSelect.value : 'desc';

        const params = new URLSearchParams();
        if (query) params.append('search', query);
        if (category) params.append('category', category);
        if (skill) params.append('skills[]', skill); 
        params.append('sort', sort);

        if (portfoliosGrid) {
            portfoliosGrid.style.opacity = '0.6';
        }

        fetch(`${window.location.pathname}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            if (portfoliosGrid) portfoliosGrid.style.opacity = '1';

            if (portfoliosGrid && data.html !== undefined) {
                portfoliosGrid.innerHTML = data.html;
            }

            if (totalResults) totalResults.textContent = data.count;
            if (totalResultsHero) totalResultsHero.textContent = data.count;
        })
        .catch(error => {
            console.error('Hubo un problema con la petición AJAX:', error);
            if (portfoliosGrid) portfoliosGrid.style.opacity = '1';
        });
    }

    if (categorySelect) categorySelect.addEventListener('change', fetchFilteredPortfolios);
    if (skillsSelect) skillsSelect.addEventListener('change', fetchFilteredPortfolios);
    if (sortSelect) sortSelect.addEventListener('change', fetchFilteredPortfolios);

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchFilteredPortfolios();
            }, 350);
        });
    }

    // Botón Limpiar Filtros
    if (btnClear) {
        btnClear.addEventListener('click', (e) => {
            e.preventDefault(); 

            if (searchInput) searchInput.value = '';
            if (categorySelect) categorySelect.value = '';
            if (skillsSelect) skillsSelect.value = '';
            if (sortSelect) sortSelect.value = 'desc';

            // Petición de limpieza inmediata
            fetchFilteredPortfolios();
        });
    }

    const menuToggleWelcome = document.getElementById('menuToggle');
    const navLinksWelcome = document.getElementById('navLinks');

    if (menuToggleWelcome && navLinksWelcome) {
        menuToggleWelcome.addEventListener('click', () => {
            navLinksWelcome.classList.toggle('active');
        });
    }

});