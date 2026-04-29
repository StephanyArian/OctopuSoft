// public/js/proyectos.js
// Versión final con toolbar inline, stack compacto, preview tipo documento, evidencias visibles

(function() {
    let proyectos = [];
    let editandoId = null;
    let tecnologiasActuales = [];

    let textoBusqueda = '';
    let filtroEstadoActual = 'todos';
    let filtroRolActual = 'todos';
    let filtroTecnologiaActual = 'todas';
    let filtroVisibilidadActual = 'todos';
    let ordenActual = 'fecha_desc';

    const proyHeader = document.getElementById('proyHeader');
    const formCard = document.getElementById('proyFormCard');
    const formTitle = document.getElementById('proyFormTitle');
    const btnMostrarForm = document.getElementById('proyBtnMostrarForm');
    const btnCancelarForm = document.getElementById('proyBtnCancelarForm');
    const btnGuardarForm = document.getElementById('proyBtnGuardarForm');
    const inputNombre = document.getElementById('proyNombre');
    const inputDesc = document.getElementById('proyDesc');
    const inputFecha = document.getElementById('proyFecha');
    const inputFechaFin = document.getElementById('proyFechaFin');
    const selectEstado = document.getElementById('proyEstado');
    const selectRol = document.getElementById('proyRol');
    const inputCliente = document.getElementById('proyCliente');
    const grid = document.getElementById('proyGrid');
    const tecCountBadge = document.getElementById('tecCountBadge');
    const evWrapper = document.getElementById('evSectionWrapper');
    const btnEvidencias = document.getElementById('proyBtnEvidencias');
    const evTriggerCount = document.getElementById('evTriggerCount');
    const contadorDesc = document.getElementById('contadorDesc');
    const previewPage = document.getElementById('previewPage');

    const buscadorInput = document.getElementById('buscadorProyectos');
    const limpiarBuscadorBtn = document.getElementById('limpiarBuscador');
    const conteoMostradosSpan = document.getElementById('conteoMostrados');
    const conteoTotalSpan = document.getElementById('conteoTotal');
    const limpiarFiltrosBtn = document.getElementById('limpiarFiltros');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let isLoading = false;

    const todasLasTecnologias = [
        'Angular', 'AWS', 'Azure', 'Bootstrap', 'C#', 'Cassandra', 'Django',
        'Docker', 'Express.js', 'Figma', 'Firebase', 'Flutter', 'Git', 'Go',
        'GraphQL', 'Java', 'JavaScript', 'Jenkins', 'Kotlin', 'Kubernetes',
        'Laravel', 'Linux', 'MongoDB', 'MySQL', 'Next.js', 'Node.js', 'PHP',
        'PostgreSQL', 'Python', 'React', 'Redis', 'Redux', 'Ruby on Rails',
        'Rust', 'Sass', 'Spring Boot', 'Supabase', 'Svelte', 'Swift',
        'Tailwind CSS', 'TypeScript', 'Unity', 'Vue.js', 'Webpack', 'WordPress'
    ];

    function toggleProyHeader(ocultar) {
        if (!proyHeader) return;
        proyHeader.style.display = ocultar ? 'none' : 'flex';
    }

    function toggleGrid(ocultar) {
        if (!grid) return;
        grid.style.display = ocultar ? 'none' : 'grid';
    }

    function mostrarLoading(mostrar) {
        isLoading = mostrar;
        if (btnGuardarForm) {
            btnGuardarForm.disabled = mostrar;
            btnGuardarForm.textContent = mostrar ? 'Guardando...' : 'Guardar proyecto';
        }
    }

    function mostrarToast(mensaje, tipo = 'success') {
        const toast = document.createElement('div');
        toast.className = `proy-toast ${tipo === 'error' ? 'error' : ''}`;
        toast.innerHTML = mensaje;
        toast.style.cssText = `position: fixed; bottom: 20px; right: 20px; background: ${tipo === 'error' ? '#ef4444' : '#10b981'}; color: white; padding: 12px 20px; border-radius: 12px; font-weight: 500; z-index: 9999; animation: toastIn .3s ease forwards;`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function getTecnologiaLogo(tecnologia) {
        const lowerTec = tecnologia.toLowerCase();
        const techMap = [
            { keywords: ['laravel'], icon: '<i class="fab fa-laravel" style="color:#ff2d20"></i>' },
            { keywords: ['react'], icon: '<i class="fab fa-react" style="color:#61dafb"></i>' },
            { keywords: ['vue','vue.js'], icon: '<i class="fab fa-vuejs" style="color:#42b883"></i>' },
            { keywords: ['node','node.js'], icon: '<i class="fab fa-node-js" style="color:#339933"></i>' },
            { keywords: ['python'], icon: '<i class="fab fa-python" style="color:#3776ab"></i>' },
            { keywords: ['javascript','js'], icon: '<i class="fab fa-js" style="color:#f7df1e"></i>' },
            { keywords: ['tailwind'], icon: '<i class="fab fa-css3-alt" style="color:#06b6d4"></i>' },
            { keywords: ['php'], icon: '<i class="fab fa-php" style="color:#777bb4"></i>' },
            { keywords: ['flutter'], icon: '<i class="fab fa-flutter" style="color:#02569b"></i>' },
            { keywords: ['docker'], icon: '<i class="fab fa-docker" style="color:#2496ed"></i>' },
            { keywords: ['mysql'], icon: '<i class="fas fa-database" style="color:#4479a1"></i>' },
            { keywords: ['postgresql'], icon: '<i class="fas fa-database" style="color:#336791"></i>' },
            { keywords: ['mongodb'], icon: '<i class="fas fa-leaf" style="color:#47a248"></i>' },
            { keywords: ['aws'], icon: '<i class="fab fa-aws" style="color:#ff9900"></i>' },
            { keywords: ['figma'], icon: '<i class="fab fa-figma" style="color:#f24e1e"></i>' },
            { keywords: ['git'], icon: '<i class="fab fa-git-alt" style="color:#f05032"></i>' },
            { keywords: ['sass'], icon: '<i class="fab fa-sass" style="color:#cc6699"></i>' },
            { keywords: ['java'], icon: '<i class="fab fa-java" style="color:#007396"></i>' },
            { keywords: ['angular'], icon: '<i class="fab fa-angular" style="color:#dd0031"></i>' },
        ];
        for (let tech of techMap) {
            for (let kw of tech.keywords) {
                if (lowerTec.includes(kw)) return tech.icon;
            }
        }
        return '<i class="fas fa-code"></i>';
    }

    function getBadgeClass(e) {
        if (e === 'Completado') return 'proy-badge-completado';
        if (e === 'En curso') return 'proy-badge-en-curso';
        return 'proy-badge-en-pausa';
    }

    // ========== CONTADOR DE PALABRAS ==========
    function contarPalabras(texto) {
        if (!texto || texto.trim() === '') return 0;
        return texto.trim().split(/\s+/).length;
    }

    function actualizarContador() {
        if (!inputDesc || !contadorDesc) return;
        const totalPalabras = contarPalabras(inputDesc.value);
        contadorDesc.textContent = `${totalPalabras}/500 palabras`;
        contadorDesc.classList.remove('warning', 'limit');
        if (totalPalabras >= 500) contadorDesc.classList.add('limit');
        else if (totalPalabras >= 450) contadorDesc.classList.add('warning');
    }

    if (inputDesc && contadorDesc) {
        inputDesc.addEventListener('input', actualizarContador);
        actualizarContador();
    }

    // ========== MINI DROPDOWN VISIBILIDAD ==========
    window.toggleVisDropdown = function(event, id) {
        event.stopPropagation();
        document.querySelectorAll('.vis-mini-dropdown.open').forEach(d => {
            if (d.dataset.proyectoId != id) d.classList.remove('open');
        });
        const dropdown = document.querySelector(`.vis-mini-dropdown[data-proyecto-id="${id}"]`);
        if (!dropdown) return;
        dropdown.classList.toggle('open');
    };

    window.cambiarVisibilidad = async function(id, nuevaVisibilidad) {
        const proyecto = proyectos.find(p => p.id === id);
        if (!proyecto) return;
        try {
            const response = await fetch(`/proyectos/${id}/visibilidad`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ is_visible: nuevaVisibilidad })
            });
            if (!response.ok) throw new Error('Error al cambiar visibilidad');
            proyecto.is_visible = nuevaVisibilidad;
            document.querySelectorAll('.vis-mini-dropdown.open').forEach(d => d.classList.remove('open'));
            renderizar();
            mostrarToast(nuevaVisibilidad ? '🌍 Visible para todos' : '🔒 Solo para mí');
        } catch (error) {
            mostrarToast('Error al cambiar visibilidad', 'error');
        }
    };

    async function cambiarVisibilidadDirecta(id, nuevaVisibilidad) {
        const proyecto = proyectos.find(p => p.id === id);
        if (!proyecto) return;
        try {
            const response = await fetch(`/proyectos/${id}/visibilidad`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ is_visible: nuevaVisibilidad })
            });
            if (!response.ok) throw new Error('Error');
            proyecto.is_visible = nuevaVisibilidad;
            mostrarToast(nuevaVisibilidad ? '🌍 Visible para todos' : '🔒 Solo para mí');
        } catch (error) {
            mostrarToast('Error al cambiar visibilidad', 'error');
        }
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.vis-toggle-wrap')) {
            document.querySelectorAll('.vis-mini-dropdown.open').forEach(d => d.classList.remove('open'));
        }
    });

    // ========== VISTA PREVIA TIPO DOCUMENTO ==========
    async function abrirPreview(id) {
        // Cargar evidencias primero
        let evidencias = [];
        try {
            const response = await fetch(`/proyectos/${id}/evidencias`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (response.ok) evidencias = await response.json();
        } catch (e) {}

        const p = proyectos.find(p => p.id === id);
        if (!p) return;

        toggleGrid(true);
        toggleProyHeader(true);
        if (formCard) formCard.classList.remove('open');

        if (!previewPage) return;
        previewPage.innerHTML = '';

        const candadoAbiertoSVG = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg>`;
        const candadoCerradoSVG = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><line x1="3" y1="3" x2="21" y2="21"/></svg>`;

        let evidenciasHTML = '';
        if (evidencias.length > 0) {
            evidenciasHTML = `
                <hr class="preview-divider">
                <div class="preview-section-title">Evidencias (${evidencias.length})</div>
                <div class="preview-evidencias-list">
                    ${evidencias.map(ev => {
                        if (ev.imagen_path) {
                            return `
                                <div class="preview-evidencia-card">
                                    <div class="preview-evidencia-header">📸 ${escapeHtml(ev.titulo || 'Imagen')}</div>
                                    <img src="/storage/${ev.imagen_path}" alt="${escapeHtml(ev.titulo || '')}" class="preview-evidencia-imagen" onerror="this.style.display='none'">
                                </div>`;
                        } else if (ev.enlace) {
                            return `
                                <div class="preview-evidencia-card">
                                    <div class="preview-evidencia-header">🔗 ${escapeHtml(ev.titulo || 'Enlace')}</div>
                                    <a href="${escapeHtml(ev.enlace)}" target="_blank" rel="noopener" class="preview-evidencia-enlace">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                        ${escapeHtml(ev.enlace)}
                                    </a>
                                </div>`;
                        } else if (ev.repositorio) {
                            return `
                                <div class="preview-evidencia-card">
                                    <div class="preview-evidencia-header">💻 ${escapeHtml(ev.titulo || 'Repositorio')}</div>
                                    <div class="preview-evidencia-repo">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                                        ${escapeHtml(ev.repositorio)}
                                    </div>
                                </div>`;
                        }
                        return '';
                    }).join('')}
                </div>`;
        }

        previewPage.innerHTML = `
            <div class="preview-doc">
                <button class="preview-back" id="previewBackBtn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                    Volver a proyectos
                </button>
                
                <h1 class="preview-doc-title">${escapeHtml(p.nombre)}</h1>
                
                <div class="preview-doc-meta">
                    <span class="proy-badge ${getBadgeClass(p.estado)}">${p.estado}</span>
                    ${p.is_visible 
                        ? `<span class="publico-badge">${candadoAbiertoSVG} Público</span>` 
                        : `<span class="privado-badge">${candadoCerradoSVG} Privado</span>`
                    }
                    ${p.rol ? `<span class="rol-badge"><i class="fas fa-user-check"></i> ${escapeHtml(p.rol)}</span>` : ''}
                    ${p.cliente ? `<span class="cliente-badge"><i class="fas fa-building"></i> ${escapeHtml(p.cliente)}</span>` : ''}
                </div>
                
                <div class="preview-doc-dates">
                    <span><i class="far fa-calendar-alt"></i> Inicio: ${p.fecha || '—'}</span>
                    <span><i class="far fa-calendar-check"></i> Fin: ${p.fecha_fin || '—'}</span>
                </div>
                
                <hr class="preview-divider">
                
                <div class="preview-section-title">Descripción</div>
                <p class="preview-doc-desc">${escapeHtml(p.descripcion)}</p>
                
                ${(p.tecnologias || []).length > 0 ? `
                    <hr class="preview-divider">
                    <div class="preview-section-title">Stack Tecnológico</div>
                    <div class="preview-doc-tecs">
                        ${(p.tecnologias || []).map(t => `<span class="tec-mini">${getTecnologiaLogo(t)} ${escapeHtml(t)}</span>`).join('')}
                    </div>
                ` : ''}
                
                ${evidenciasHTML}
                
                <div class="preview-doc-actions">
                    <button class="preview-btn preview-btn-outline" id="previewToggleVisBtn">
                        ${p.is_visible 
                            ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><line x1="3" y1="3" x2="21" y2="21"/></svg> Hacer privado'
                            : '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg> Hacer público'
                        }
                    </button>
                    <button class="preview-btn preview-btn-outline" id="previewEditBtn2">✏️ Editar</button>
                    <button class="preview-btn preview-btn-outline" id="previewDeleteBtn" style="color:#ef4444;border-color:#fecaca;">🗑️ Eliminar</button>
                </div>
            </div>
        `;

        previewPage.classList.add('open');

        previewPage.querySelector('#previewBackBtn').addEventListener('click', cerrarPreview);
        previewPage.querySelector('#previewEditBtn2').addEventListener('click', () => { cerrarPreview(); editarProyecto(id); });
        previewPage.querySelector('#previewDeleteBtn').addEventListener('click', () => { cerrarPreview(); eliminarProyectoBD(id); });
        previewPage.querySelector('#previewToggleVisBtn').addEventListener('click', async () => {
            await cambiarVisibilidadDirecta(id, !p.is_visible);
            abrirPreview(id);
        });
    }

    function cerrarPreview() {
        if (previewPage) {
            previewPage.classList.remove('open');
            previewPage.innerHTML = '';
        }
        toggleGrid(false);
        toggleProyHeader(false);
        cargarProyectos();
    }

    // ========== DROPDOWNS FORMULARIO ==========
    function setupCustomDropdown(dropdownId, btnId, menuId, hiddenInputId, btnTextId) {
        const dropdown = document.getElementById(dropdownId);
        const btn = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        const hiddenInput = document.getElementById(hiddenInputId);
        const btnText = document.getElementById(btnTextId);
        if (!dropdown || !btn || !menu || !hiddenInput) return;

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dropdown.classList.contains('open');
            document.querySelectorAll('.custom-dropdown.open').forEach(d => d.classList.remove('open'));
            document.querySelectorAll('.stack-dropdown.open').forEach(d => d.classList.remove('open'));
            if (!isOpen) dropdown.classList.add('open');
        });

        menu.querySelectorAll('li').forEach(li => {
            li.addEventListener('click', (e) => {
                e.stopPropagation();
                hiddenInput.value = li.dataset.value;
                if (btnText) btnText.textContent = li.textContent;
                menu.querySelectorAll('li').forEach(l => l.classList.remove('selected'));
                li.classList.add('selected');
                dropdown.classList.remove('open');
            });
        });
    }

    setupCustomDropdown('dropdownEstado', 'btnEstado', 'menuEstado', 'proyEstado', 'btnEstadoText');
    setupCustomDropdown('dropdownRol', 'btnRol', 'menuRol', 'proyRol', 'btnRolText');

    // ========== STACK TECNOLÓGICO ==========
    const stackDropdown = document.getElementById('stackDropdown');
    const stackInputWrapper = document.getElementById('stackInputWrapper');
    const stackSearch = document.getElementById('stackSearch');
    const stackMenu = document.getElementById('stackMenu');
    const stackList = document.getElementById('stackList');
    const tecBadgesContainer = document.getElementById('tecBadgesContainer');

    function renderStackOptions(filter = '') {
        const disponibles = todasLasTecnologias.filter(t => !tecnologiasActuales.includes(t));
        const filtered = filter.trim() === '' ? disponibles : disponibles.filter(t => t.toLowerCase().includes(filter.toLowerCase()));

        if (filtered.length === 0) {
            stackList.innerHTML = '<div class="stack-no-results">Sin resultados. Presiona Enter para agregar.</div>';
            return;
        }

        stackList.innerHTML = filtered.map(tech => `
            <div class="stack-option" data-value="${escapeHtml(tech)}">
                <span class="check">✓</span>
                ${tech}
            </div>
        `).join('');

        stackList.querySelectorAll('.stack-option').forEach(option => {
            option.addEventListener('click', (e) => {
                e.stopPropagation();
                if (tecnologiasActuales.length >= 15) { mostrarToast('❌ Máximo 15 tecnologías', 'error'); return; }
                tecnologiasActuales.push(option.dataset.value);
                renderTecnologiasBadges();
                renderStackOptions(stackSearch.value);
                stackSearch.value = '';
                stackSearch.focus();
            });
        });
    }

    function renderTecnologiasBadges() {
        if (!tecBadgesContainer) return;
        if (tecCountBadge) tecCountBadge.textContent = tecnologiasActuales.length;

        if (tecnologiasActuales.length === 0) {
            tecBadgesContainer.innerHTML = `<div class="tec-empty-state"><div class="tec-empty-icon">🔧</div><div class="tec-empty-text">Aún no hay tecnologías</div></div>`;
            return;
        }

        tecBadgesContainer.innerHTML = tecnologiasActuales.map(tec => `
            <span class="tec-badge">
                <span class="tec-badge-logo">${getTecnologiaLogo(tec)}</span>
                <span>${escapeHtml(tec)}</span>
                <span class="tec-badge-remove" data-tec="${escapeHtml(tec)}">✕</span>
            </span>
        `).join('');

        document.querySelectorAll('.tec-badges-col .tec-badge-remove').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                tecnologiasActuales = tecnologiasActuales.filter(t => t !== btn.dataset.tec);
                renderTecnologiasBadges();
                renderStackOptions(stackSearch.value);
            });
        });
    }

    if (stackInputWrapper) {
        stackInputWrapper.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = stackDropdown.classList.contains('open');
            document.querySelectorAll('.custom-dropdown.open').forEach(d => d.classList.remove('open'));
            document.querySelectorAll('.stack-dropdown.open').forEach(d => d.classList.remove('open'));
            if (!isOpen) { stackDropdown.classList.add('open'); stackSearch.focus(); renderStackOptions(stackSearch.value); }
        });
    }

    if (stackSearch) {
        stackSearch.addEventListener('input', (e) => { if (!stackDropdown.classList.contains('open')) stackDropdown.classList.add('open'); renderStackOptions(e.target.value); });
        stackSearch.addEventListener('click', (e) => { e.stopPropagation(); if (!stackDropdown.classList.contains('open')) { stackDropdown.classList.add('open'); renderStackOptions(stackSearch.value); } });
        stackSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const valor = stackSearch.value.trim();
                if (valor && !tecnologiasActuales.includes(valor)) {
                    if (tecnologiasActuales.length >= 15) { mostrarToast('❌ Máximo 15 tecnologías', 'error'); return; }
                    tecnologiasActuales.push(valor);
                    renderTecnologiasBadges();
                    renderStackOptions('');
                    stackSearch.value = '';
                }
            }
        });
    }

    // ========== API CALLS ==========
    async function cargarProyectos() {
        try {
            mostrarLoading(true);
            const response = await fetch('/proyectos', { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Error al cargar proyectos');
            proyectos = await response.json();
            poblarSelectsDinamicos();
            renderizar();
        } catch (error) { mostrarToast('Error al cargar proyectos', 'error'); } finally { mostrarLoading(false); }
    }

    async function guardarProyectoBD() {
        const nombre = inputNombre.value.trim();
        const descripcion = inputDesc.value.trim();
        const fecha = inputFecha.value;
        const fechaFin = inputFechaFin ? inputFechaFin.value : null;
        const estado = selectEstado ? selectEstado.value : 'En curso';
        const rol = selectRol ? selectRol.value : '';
        const cliente = inputCliente ? inputCliente.value.trim() : '';
        const visibilidad = 'publico';
        const tecnologias = [...tecnologiasActuales];

        let isValid = true;
        if (!nombre) { inputNombre.classList.add('proy-err'); document.getElementById('proyErrNombre').classList.add('visible'); isValid = false; }
        else { inputNombre.classList.remove('proy-err'); document.getElementById('proyErrNombre').classList.remove('visible'); }
        if (!descripcion) { inputDesc.classList.add('proy-err'); document.getElementById('proyErrDesc').classList.add('visible'); isValid = false; }
        else { inputDesc.classList.remove('proy-err'); document.getElementById('proyErrDesc').classList.remove('visible'); }
        if (!isValid) return;

        const proyectoData = { nombre, descripcion, fecha: fecha || null, fecha_fin: fechaFin || null, estado, rol, cliente, visibilidad, tecnologias };

        try {
            mostrarLoading(true);
            const url = editandoId ? `/proyectos/${editandoId}` : '/proyectos';
            const method = editandoId ? 'PUT' : 'POST';
            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(proyectoData)
            });
            if (!response.ok) { const error = await response.json(); throw new Error(error.message || 'Error al guardar'); }
            const resultado = await response.json();
            window._proyectoParaEvidencias = resultado;
            mostrarToast(editandoId ? '✅ Proyecto actualizado' : '✅ Proyecto creado');
            ocultarForm();
            await cargarProyectos();
        } catch (error) { mostrarToast(error.message || 'Error al guardar proyecto', 'error'); } finally { mostrarLoading(false); }
    }

    async function eliminarProyectoBD(id) {
        const proyecto = proyectos.find(p => p.id === id);
        if (!confirm(`¿Eliminar "${proyecto.nombre}"? Esta acción no se puede deshacer.`)) return;
        try {
            mostrarLoading(true);
            const response = await fetch(`/proyectos/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Error al eliminar');
            mostrarToast('🗑️ Proyecto eliminado');
            await cargarProyectos();
            if (editandoId === id) ocultarForm();
        } catch (error) { mostrarToast('Error al eliminar proyecto', 'error'); } finally { mostrarLoading(false); }
    }

    // ========== FILTROS ==========
    function poblarSelectsDinamicos() {
        const rolesUnicos = [...new Set(proyectos.map(p => p.rol).filter(r => r && r.trim() !== ''))];
        const ddFiltrar = document.getElementById('ddFiltrar');
        if (ddFiltrar) {
            const sectionRol = ddFiltrar.querySelector('.ft-dd-section:last-child');
            if (sectionRol) {
                sectionRol.innerHTML = `
                    <div class="ft-dd-label">Mi rol</div>
                    <div class="ft-dd-item selected" data-filtro="rol" data-val="todos"><span class="ft-dot"></span>Todos los roles</div>
                    ${rolesUnicos.map(rol => `<div class="ft-dd-item" data-filtro="rol" data-val="${escapeHtml(rol)}"><span class="ft-dot"></span>${escapeHtml(rol)}</div>`).join('')}
                `;
                sectionRol.querySelectorAll('.ft-dd-item').forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.stopPropagation();
                        sectionRol.querySelectorAll('.ft-dd-item').forEach(i => i.classList.remove('selected'));
                        item.classList.add('selected');
                        filtroRolActual = item.dataset.val;
                        renderizar();
                        document.getElementById('ddFiltrar')?.classList.remove('open');
                        document.getElementById('btnFiltrar')?.classList.remove('open');
                    });
                });
            }
        }

        const tecnologiasUnicas = [...new Set(proyectos.flatMap(p => p.tecnologias || []))];
        const ddTecnologia = document.getElementById('ddTecnologia');
        if (ddTecnologia) {
            const sectionTec = ddTecnologia.querySelector('.ft-dd-section');
            if (sectionTec) {
                sectionTec.innerHTML = `
                    <div class="ft-dd-item selected" data-filtro="tecnologia" data-val="todas"><span class="ft-dot"></span>Todas</div>
                    ${tecnologiasUnicas.map(tec => `<div class="ft-dd-item" data-filtro="tecnologia" data-val="${escapeHtml(tec)}"><span class="ft-dot"></span>${escapeHtml(tec)}</div>`).join('')}
                `;
                sectionTec.querySelectorAll('.ft-dd-item').forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.stopPropagation();
                        sectionTec.querySelectorAll('.ft-dd-item').forEach(i => i.classList.remove('selected'));
                        item.classList.add('selected');
                        filtroTecnologiaActual = item.dataset.val;
                        renderizar();
                        document.getElementById('ddTecnologia')?.classList.remove('open');
                        document.getElementById('btnTecnologia')?.classList.remove('open');
                    });
                });
            }
        }
    }

    function filtrarYOrdenarProyectos() {
        if (!proyectos.length) return [];
        let resultados = [...proyectos];
        if (textoBusqueda.trim() !== '') {
            const busquedaLower = textoBusqueda.toLowerCase();
            resultados = resultados.filter(p => (p.nombre && p.nombre.toLowerCase().includes(busquedaLower)) || (p.rol && p.rol.toLowerCase().includes(busquedaLower)) || (p.cliente && p.cliente.toLowerCase().includes(busquedaLower)));
        }
        if (filtroEstadoActual !== 'todos') resultados = resultados.filter(p => p.estado === filtroEstadoActual);
        if (filtroRolActual !== 'todos') resultados = resultados.filter(p => p.rol === filtroRolActual);
        if (filtroTecnologiaActual !== 'todas') resultados = resultados.filter(p => p.tecnologias && p.tecnologias.includes(filtroTecnologiaActual));
        if (filtroVisibilidadActual !== 'todos') resultados = resultados.filter(p => p.is_visible === (filtroVisibilidadActual === 'publico'));

        switch(ordenActual) {
            case 'fecha_desc': resultados.sort((a, b) => new Date(b.fecha) - new Date(a.fecha)); break;
            case 'fecha_asc': resultados.sort((a, b) => new Date(a.fecha) - new Date(b.fecha)); break;
            case 'nombre_asc': resultados.sort((a, b) => (a.nombre || '').localeCompare(b.nombre || '')); break;
            case 'nombre_desc': resultados.sort((a, b) => (b.nombre || '').localeCompare(a.nombre || '')); break;
            case 'rol_asc': resultados.sort((a, b) => (a.rol || '').localeCompare(b.rol || '')); break;
        }

        if (conteoMostradosSpan) conteoMostradosSpan.textContent = resultados.length;
        if (conteoTotalSpan) conteoTotalSpan.textContent = proyectos.length;
        return resultados;
    }

    // ========== UI ==========
    function limpiarForm() {
        inputNombre.value = '';
        inputDesc.value = '';
        inputFecha.value = '';
        if (inputFechaFin) inputFechaFin.value = '';
        if (selectEstado) selectEstado.value = 'En curso';
        document.getElementById('btnEstadoText').textContent = 'En curso';
        document.querySelectorAll('#menuEstado li').forEach(l => l.classList.remove('selected'));
        document.querySelector('#menuEstado li[data-value="En curso"]')?.classList.add('selected');
        if (selectRol) selectRol.value = '';
        document.getElementById('btnRolText').textContent = '— Seleccionar rol —';
        document.querySelectorAll('#menuRol li').forEach(l => l.classList.remove('selected'));
        document.querySelector('#menuRol li[data-value=""]')?.classList.add('selected');
        if (inputCliente) inputCliente.value = '';
        editandoId = null;
        formTitle.innerHTML = '➕ Nuevo Proyecto';
        tecnologiasActuales = [];
        renderTecnologiasBadges();
        if (stackSearch) stackSearch.value = '';
        actualizarContador();
        inputNombre.classList.remove('proy-err');
        inputDesc.classList.remove('proy-err');
        document.getElementById('proyErrNombre').classList.remove('visible');
        document.getElementById('proyErrDesc').classList.remove('visible');
    }

    function ocultarForm() { formCard.classList.remove('open'); limpiarForm(); toggleProyHeader(false); toggleGrid(false); if (previewPage) { previewPage.classList.remove('open'); previewPage.innerHTML = ''; } }

    function mostrarForm() { toggleProyHeader(true); toggleGrid(true); formCard.classList.add('open'); inputNombre.focus(); if (previewPage) { previewPage.classList.remove('open'); previewPage.innerHTML = ''; } }

    function renderizar() {
        if (!grid) return;
        const proyectosFiltrados = filtrarYOrdenarProyectos();

        if (proyectosFiltrados.length === 0) {
            grid.innerHTML = `<div class="proy-empty"><div class="proy-empty-icon">🔍</div><h3>No se encontraron proyectos</h3><p>No hay proyectos que coincidan con los filtros seleccionados.</p><button class="proy-empty-btn" id="limpiarFiltrosEmptyBtn">Limpiar filtros</button></div>`;
            document.getElementById('limpiarFiltrosEmptyBtn')?.addEventListener('click', resetearFiltros);
            return;
        }

        grid.innerHTML = '';
        proyectosFiltrados.forEach(p => {
            const card = document.createElement('div');
            card.className = 'proy-card';

            const candadoAbiertoSVG = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg>`;
            const candadoCerradoSVG = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><line x1="3" y1="3" x2="21" y2="21"/></svg>`;

            const visibilidadBadge = p.is_visible
                ? `<span class="publico-badge">${candadoAbiertoSVG} Público</span>`
                : `<span class="privado-badge">${candadoCerradoSVG} Privado</span>`;

            let rolClienteHtml = '';
            if (p.rol || p.cliente) {
                rolClienteHtml = `<div class="proy-card-rol-cliente">`;
                if (p.rol) rolClienteHtml += `<span class="rol-badge"><i class="fas fa-user-check"></i> ${escapeHtml(p.rol)}</span>`;
                if (p.cliente) rolClienteHtml += `<span class="cliente-badge"><i class="fas fa-building"></i> ${escapeHtml(p.cliente)}</span>`;
                rolClienteHtml += `</div>`;
            }

            card.innerHTML = `
                <div class="proy-card-band"></div>
                <div class="proy-card-top">
                    <div class="proy-card-nombre">${escapeHtml(p.nombre)} ${visibilidadBadge}</div>
                    <div class="proy-card-actions">
                        <div class="vis-toggle-wrap">
                            <button class="proy-icon-btn btn-toggle-vis" data-id="${p.id}" onclick="toggleVisDropdown(event, ${p.id})" title="Cambiar visibilidad">
                                ${p.is_visible ? candadoAbiertoSVG : candadoCerradoSVG}
                            </button>
                            <div class="vis-mini-dropdown" data-proyecto-id="${p.id}">
                                <div class="vis-mini-option ${p.is_visible ? 'active' : ''}" onclick="cambiarVisibilidad(${p.id}, true)">
                                    <span class="vis-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg></span>
                                    <span>Visible para todos</span>
                                    <span class="vis-check">✓</span>
                                </div>
                                <div class="vis-mini-option ${!p.is_visible ? 'active' : ''}" onclick="cambiarVisibilidad(${p.id}, false)">
                                    <span class="vis-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><line x1="3" y1="3" x2="21" y2="21"/></svg></span>
                                    <span>Solo para mí</span>
                                    <span class="vis-check">✓</span>
                                </div>
                            </div>
                        </div>
                        <button class="proy-icon-btn btn-editar" data-id="${p.id}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0abf9e" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button class="proy-icon-btn btn-eliminar" data-id="${p.id}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </div>
                </div>
                <div class="proy-card-body">
                    ${rolClienteHtml}
                    <div class="proy-card-desc">${escapeHtml(p.descripcion)}</div>
                    <div class="proy-card-tec">
                        ${(p.tecnologias || []).slice(0, 4).map(t => `<span class="tec-mini">${getTecnologiaLogo(t)} ${escapeHtml(t)}</span>`).join('')}
                        ${(p.tecnologias || []).length > 4 ? `<span class="tec-mini">+${p.tecnologias.length - 4}</span>` : ''}
                    </div>
                    <div class="proy-card-footer">
                        <span class="proy-card-fecha"><i class="far fa-calendar-alt"></i> ${p.fecha || 'Sin fecha'}</span>
                        <span class="proy-badge ${getBadgeClass(p.estado)}">${p.estado}</span>
                    </div>
                </div>`;
            grid.appendChild(card);

            card.addEventListener('click', (e) => {
                if (e.target.closest('button') || e.target.closest('.vis-mini-dropdown')) return;
                abrirPreview(p.id);
            });
        });

        document.querySelectorAll('.btn-editar').forEach(b => b.addEventListener('click', (e) => { e.stopPropagation(); editarProyecto(parseInt(b.dataset.id)); }));
        document.querySelectorAll('.btn-eliminar').forEach(b => b.addEventListener('click', (e) => { e.stopPropagation(); eliminarProyectoBD(parseInt(b.dataset.id)); }));
    }

    function editarProyecto(id) {
        const p = proyectos.find(p => p.id === id);
        if (!p) return;
        window._proyectoParaEvidencias = p;
        editandoId = id;
        inputNombre.value = p.nombre;
        inputDesc.value = p.descripcion;
        inputFecha.value = p.fecha || '';
        if (inputFechaFin) inputFechaFin.value = p.fecha_fin || '';
        if (selectEstado) selectEstado.value = p.estado;
        document.getElementById('btnEstadoText').textContent = p.estado;
        document.querySelectorAll('#menuEstado li').forEach(l => l.classList.remove('selected'));
        document.querySelector(`#menuEstado li[data-value="${p.estado}"]`)?.classList.add('selected');
        if (selectRol) selectRol.value = p.rol || '';
        document.getElementById('btnRolText').textContent = p.rol || '— Seleccionar rol —';
        document.querySelectorAll('#menuRol li').forEach(l => l.classList.remove('selected'));
        if (p.rol) document.querySelector(`#menuRol li[data-value="${p.rol}"]`)?.classList.add('selected');
        else document.querySelector('#menuRol li[data-value=""]')?.classList.add('selected');
        if (inputCliente) inputCliente.value = p.cliente || '';
        tecnologiasActuales = [...(p.tecnologias || [])];
        renderTecnologiasBadges();
        if (stackSearch) stackSearch.value = '';
        actualizarContador();
        formTitle.innerHTML = '✏️ Editar Proyecto';
        mostrarForm();
    }

    function resetearFiltros() {
        textoBusqueda = '';
        filtroEstadoActual = 'todos';
        filtroRolActual = 'todos';
        filtroTecnologiaActual = 'todas';
        filtroVisibilidadActual = 'todos';
        ordenActual = 'fecha_desc';
        if (buscadorInput) { buscadorInput.value = ''; if (limpiarBuscadorBtn) limpiarBuscadorBtn.style.display = 'none'; }
        ['ddFiltrar', 'ddOrdenar', 'ddTecnologia'].forEach(ddId => {
            const dd = document.getElementById(ddId);
            if (dd) dd.querySelectorAll('.ft-dd-item').forEach(i => i.classList.remove('selected'));
        });
        document.querySelector('#ddFiltrar .ft-dd-item[data-val="todos"]')?.classList.add('selected');
        document.querySelector('#ddOrdenar .ft-dd-item[data-val="fecha_desc"]')?.classList.add('selected');
        document.querySelector('#ddTecnologia .ft-dd-item[data-val="todas"]')?.classList.add('selected');
        renderizar();
        mostrarToast('🧹 Filtros limpiados');
    }

    // ========== EVIDENCIAS ==========
    function abrirEvidencias(proyecto) {
        if (!proyecto || !proyecto.id) { mostrarToast('Error: Proyecto no válido', 'error'); return; }
        toggleProyHeader(true);
        toggleGrid(true);
        window._proyectoParaEvidencias = proyecto;
        if (formCard) formCard.classList.remove('open');
        if (evWrapper) evWrapper.style.display = 'block';
        if (typeof window.evInit === 'function') window.evInit(proyecto);
        else mostrarToast('Error al cargar evidencias', 'error');
    }

    if (btnEvidencias) {
        btnEvidencias.addEventListener('click', () => {
            if (!window._proyectoParaEvidencias && !editandoId) { mostrarToast('❌ Primero guarda el proyecto para agregar evidencias', 'error'); return; }
            const proyectoActual = window._proyectoParaEvidencias || (editandoId ? proyectos.find(p => p.id === editandoId) : null);
            if (proyectoActual) abrirEvidencias(proyectoActual);
            else mostrarToast('❌ Primero guarda el proyecto para agregar evidencias', 'error');
        });
    }

    document.addEventListener('ev:volver', () => {
        toggleProyHeader(false);
        toggleGrid(false);
        if (evWrapper) evWrapper.style.display = 'none';
        cargarProyectos();
    });

    // ========== DROPDOWNS TOOLBAR ==========
    function setupDropdown(btnId, ddId) {
        const btn = document.getElementById(btnId);
        const dd = document.getElementById(ddId);
        if (!btn || !dd) return;
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dd.classList.contains('open');
            document.querySelectorAll('.ft-dropdown').forEach(d => d.classList.remove('open'));
            document.querySelectorAll('.ft-btn-sm').forEach(b => b.classList.remove('open'));
            if (!isOpen) { dd.classList.add('open'); btn.classList.add('open'); }
        });
        dd.querySelectorAll('.ft-dd-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const filtro = item.dataset.filtro;
                const val = item.dataset.val;
                const section = item.closest('.ft-dd-section');
                if (section) section.querySelectorAll('.ft-dd-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                if (filtro === 'estado') filtroEstadoActual = val;
                if (filtro === 'visibilidad') filtroVisibilidadActual = val;
                if (filtro === 'rol') filtroRolActual = val;
                if (filtro === 'tecnologia') filtroTecnologiaActual = val;
                if (filtro === 'ordenar' || filtro === 'direccion') {
                    const secOrden = document.querySelector('#ddOrdenar .ft-dd-section:first-child .ft-dd-item.selected');
                    const secDir = document.querySelector('#ddOrdenar .ft-dd-section:last-child .ft-dd-item.selected');
                    const base = secOrden?.dataset.val || 'fecha_desc';
                    const dir = secDir?.dataset.val || 'desc';
                    if (base === 'fecha_desc') ordenActual = dir === 'asc' ? 'fecha_asc' : 'fecha_desc';
                    if (base === 'nombre_asc') ordenActual = dir === 'asc' ? 'nombre_asc' : 'nombre_desc';
                    if (base === 'rol_asc') ordenActual = 'rol_asc';
                }
                renderizar();
                dd.classList.remove('open');
                btn.classList.remove('open');
            });
        });
    }

    setupDropdown('btnOrdenar', 'ddOrdenar');
    setupDropdown('btnFiltrar', 'ddFiltrar');
    setupDropdown('btnTecnologia', 'ddTecnologia');

    document.addEventListener('click', () => {
        document.querySelectorAll('.ft-dropdown').forEach(d => d.classList.remove('open'));
        document.querySelectorAll('.ft-btn-sm').forEach(b => b.classList.remove('open'));
        document.querySelectorAll('.custom-dropdown.open').forEach(d => d.classList.remove('open'));
        document.querySelectorAll('.stack-dropdown.open').forEach(d => d.classList.remove('open'));
    });

    // ========== BUSCADOR ==========
    if (buscadorInput) {
        buscadorInput.addEventListener('input', (e) => {
            textoBusqueda = e.target.value;
            if (limpiarBuscadorBtn) limpiarBuscadorBtn.style.display = textoBusqueda ? 'block' : 'none';
            renderizar();
        });
    }
    if (limpiarBuscadorBtn) {
        limpiarBuscadorBtn.addEventListener('click', () => { textoBusqueda = ''; buscadorInput.value = ''; limpiarBuscadorBtn.style.display = 'none'; renderizar(); });
    }
    if (limpiarFiltrosBtn) limpiarFiltrosBtn.addEventListener('click', resetearFiltros);

    // ========== EVENTOS PRINCIPALES ==========
    if (btnMostrarForm) btnMostrarForm.addEventListener('click', () => { limpiarForm(); mostrarForm(); });
    if (btnCancelarForm) btnCancelarForm.addEventListener('click', ocultarForm);
    if (btnGuardarForm) btnGuardarForm.addEventListener('click', guardarProyectoBD);

    // Inicializar
    toggleProyHeader(false);
    toggleGrid(false);
    cargarProyectos();
})();