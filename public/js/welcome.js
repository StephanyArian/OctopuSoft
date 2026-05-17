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

    // Inicialización segura del carrusel solo si existe en el DOM
    if (track && btnPrev && btnNext && dotsWrapper) {
        const cards = Array.from(track.querySelectorAll('.portfolio-card'));
        
        if (cards.length > 0) {
            const GAP = 24;
            let currentIndex = 0;

            /* Cuántas tarjetas se ven según el ancho de pantalla */
            function getPerView() {
                const w = window.innerWidth;
                if (w < 600)  return 1;
                if (w < 1024) return 2;
                return 3;
            }

            /* Ancho real de UNA tarjeta */
            function getCardWidth() {
                return cards[0].getBoundingClientRect().width;
            }

            /* Total de posiciones de desplazamiento posibles */
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

            /* Iniciar carrusel */
            buildDots();
            goTo(0);
        }
    }

    /* ── FILTRADO Y BUSCADOR AJAX ── */
    function fetchFilteredPortfolios() {
        // Recopilamos los valores actuales de la interfaz de forma segura
        const query = searchInput ? searchInput.value : '';
        const category = categorySelect ? categorySelect.value : '';
        const skill = skillsSelect ? skillsSelect.value : '';
        const sort = sortSelect ? sortSelect.value : 'desc';

        // CONSTRUCCIÓN DE PARÁMETROS CORREGIDA PARA TU BACKEND
        const params = new URLSearchParams();
        if (query) params.append('search', query);
        if (category) params.append('category', category);
        if (skill) params.append('skills[]', skill); // Se envía en formato array para array_filter o matching directo
        params.append('sort', sort);

        // Efecto visual de carga
        if (portfoliosGrid) {
            portfoliosGrid.style.opacity = '0.6';
        }

        // Realizamos la llamada dinámica a la ruta actual del explorador
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

            // Inyectamos el HTML de las cards
            if (portfoliosGrid && data.html !== undefined) {
                portfoliosGrid.innerHTML = data.html;
            }

            // Actualizamos contadores globales
            if (totalResults) totalResults.textContent = data.count;
            if (totalResultsHero) totalResultsHero.textContent = data.count;
        })
        .catch(error => {
            console.error('Hubo un problema con la petición AJAX:', error);
            if (portfoliosGrid) portfoliosGrid.style.opacity = '1';
        });
    }

    // Escuchadores de cambio inmediato para elementos select
    if (categorySelect) categorySelect.addEventListener('change', fetchFilteredPortfolios);
    if (skillsSelect) skillsSelect.addEventListener('change', fetchFilteredPortfolios);
    if (sortSelect) sortSelect.addEventListener('change', fetchFilteredPortfolios);

    // Escuchador con Debounce para entrada por teclado
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
            e.preventDefault(); // Previene cualquier comportamiento de envío accidental

            if (searchInput) searchInput.value = '';
            if (categorySelect) categorySelect.value = '';
            if (skillsSelect) skillsSelect.value = '';
            if (sortSelect) sortSelect.value = 'desc';

            // Petición de limpieza inmediata
            fetchFilteredPortfolios();
        });
    }
});