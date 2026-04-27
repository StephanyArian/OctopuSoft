
// public/js/proyectos.js

(function() {
    let proyectos = [];
    let editandoId = null;
    let tecnologiasActuales = [];

    // DOM Elements
    const proyHeader = document.getElementById('proyHeader');
    const formCard = document.getElementById('proyFormCard');
    const formTitle = document.getElementById('proyFormTitle');
    const btnMostrarForm = document.getElementById('proyBtnMostrarForm');
    const btnCancelarForm = document.getElementById('proyBtnCancelarForm');
    const btnGuardarForm = document.getElementById('proyBtnGuardarForm');
    const inputNombre = document.getElementById('proyNombre');
    const inputDesc = document.getElementById('proyDesc');
    const inputFecha = document.getElementById('proyFecha');
    const selectEstado = document.getElementById('proyEstado');
    const grid = document.getElementById('proyGrid');
    const tecInput = document.getElementById('tecInput');
    const tecBtnAgregar = document.getElementById('tecBtnAgregar');
    const tecCountBadge = document.getElementById('tecCountBadge');
    const evWrapper = document.getElementById('evSectionWrapper');
    const btnEvidencias = document.getElementById('proyBtnEvidencias');
    const evTriggerCount = document.getElementById('evTriggerCount');

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // Loading state
    let isLoading = false;

    function mostrarLoading(mostrar) {
        isLoading = mostrar;
        const btnGuardar = btnGuardarForm;
        if (btnGuardar) {
            btnGuardar.disabled = mostrar;
            btnGuardar.textContent = mostrar ? 'Guardando...' : 'Guardar proyecto';
        }
    }

    // ── LOGOS TECNOLOGÍAS ─────────────────────────────────────
    function getTecnologiaLogo(tecnologia) {
        const lowerTec = tecnologia.toLowerCase();
        const techMap = [
            { keywords: ['laravel'], icon: '<i class="fab fa-laravel" style="color:#ff2d20"></i>' },
            { keywords: ['react'], icon: '<i class="fab fa-react" style="color:#61dafb"></i>' },
            { keywords: ['vue','vue.js'], icon: '<i class="fab fa-vuejs" style="color:#42b883"></i>' },
            { keywords: ['node','node.js'], icon: '<i class="fab fa-node-js" style="color:#339933"></i>' },
            { keywords: ['python'], icon: '<i class="fab fa-python" style="color:#3776ab"></i>' },
            { keywords: ['javascript','js'], icon: '<i class="fab fa-js" style="color:#f7df1e"></i>' },
            { keywords: ['typescript','ts'], icon: '<i class="fab fa-js" style="color:#3178c6"></i>' },
            { keywords: ['tailwind'], icon: '<i class="fab fa-css3-alt" style="color:#06b6d4"></i>' },
            { keywords: ['bootstrap'], icon: '<i class="fab fa-bootstrap" style="color:#7952b3"></i>' },
            { keywords: ['php'], icon: '<i class="fab fa-php" style="color:#777bb4"></i>' },
            { keywords: ['java'], icon: '<i class="fab fa-java" style="color:#007396"></i>' },
            { keywords: ['flutter'], icon: '<i class="fab fa-flutter" style="color:#02569b"></i>' },
            { keywords: ['docker'], icon: '<i class="fab fa-docker" style="color:#2496ed"></i>' },
            { keywords: ['mysql'], icon: '<i class="fas fa-database" style="color:#4479a1"></i>' },
            { keywords: ['postgresql','postgres'], icon: '<i class="fas fa-database" style="color:#336791"></i>' },
            { keywords: ['mongodb'], icon: '<i class="fas fa-database" style="color:#47a248"></i>' },
            { keywords: ['git'], icon: '<i class="fab fa-git-alt" style="color:#f05032"></i>' },
            { keywords: ['github'], icon: '<i class="fab fa-github" style="color:#181717"></i>' },
            { keywords: ['figma'], icon: '<i class="fab fa-figma" style="color:#f24e1e"></i>' },
            { keywords: ['angular'], icon: '<i class="fab fa-angular" style="color:#dd0031"></i>' },
            { keywords: ['django'], icon: '<i class="fab fa-python" style="color:#092e20"></i>' },
            { keywords: ['spring'], icon: '<i class="fab fa-java" style="color:#6db33f"></i>' },
        ];
        for (let tech of techMap) {
            for (let kw of tech.keywords) {
                if (lowerTec.includes(kw)) return tech.icon;
            }
        }
        return '<i class="fas fa-code"></i>';
    }

    function renderizarTecnologias() {
        const cont = document.getElementById('tecBadgesContainer');
        if (!cont) return;
        if (tecCountBadge) tecCountBadge.textContent = tecnologiasActuales.length;
        
        if (tecnologiasActuales.length === 0) {
            cont.innerHTML = `<div class="tec-empty-state">
                <div class="tec-empty-icon"><i class="fas fa-tools"></i></div>
                <div class="tec-empty-text">Aún no hay tecnologías agregadas</div>
                <div class="tec-empty-hint">Comienza escribiendo el nombre de una tecnología</div>
            </div>`;
            return;
        }
        
        cont.innerHTML = '';
        tecnologiasActuales.forEach(tec => {
            const badge = document.createElement('span');
            badge.className = 'tec-badge';
            badge.innerHTML = `
                <span class="tec-badge-logo">${getTecnologiaLogo(tec)}</span>
                <span>${escapeHtml(tec)}</span>
                <span class="tec-badge-remove" data-tec="${escapeHtml(tec)}">✕</span>`;
            cont.appendChild(badge);
        });
        
        document.querySelectorAll('.tec-badge-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                tecnologiasActuales = tecnologiasActuales.filter(t => t !== btn.dataset.tec);
                renderizarTecnologias();
            });
        });
    }

    function agregarTecnologia() {
        const nueva = tecInput.value.trim();
        if (!nueva) { mostrarToast('❌ Ingresa una tecnología', 'error'); return; }
        if (tecnologiasActuales.includes(nueva)) { mostrarToast('❌ Ya está agregada', 'error'); return; }
        if (tecnologiasActuales.length >= 15) { mostrarToast('❌ Máximo 15 tecnologías', 'error'); return; }
        tecnologiasActuales.push(nueva);
        renderizarTecnologias();
        tecInput.value = '';
        tecInput.focus();
        mostrarToast(`✅ "${nueva}" agregada`);
    }

    function limpiarTecnologias() {
        tecnologiasActuales = [];
        renderizarTecnologias();
        tecInput.value = '';
    }

    // ── API CALLS (Base de Datos) ─────────────────────────────
    async function cargarProyectos() {
        try {
            mostrarLoading(true);
            const response = await fetch('/proyectos', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('Error al cargar proyectos');
            
            proyectos = await response.json();
            renderizar();
        } catch (error) {
            console.error('Error:', error);
            mostrarToast('Error al cargar proyectos', 'error');
        } finally {
            mostrarLoading(false);
        }
    }

    async function guardarProyectoBD() {
        const nombre = inputNombre.value.trim();
        const descripcion = inputDesc.value.trim();
        const fecha = inputFecha.value;
        const estado = selectEstado.value;
        const tecnologias = [...tecnologiasActuales];
        
        // Validaciones
        let isValid = true;
        
        if (!nombre) {
            inputNombre.classList.add('proy-err');
            document.getElementById('proyErrNombre').classList.add('visible');
            isValid = false;
        } else {
            inputNombre.classList.remove('proy-err');
            document.getElementById('proyErrNombre').classList.remove('visible');
        }
        
        if (!descripcion) {
            inputDesc.classList.add('proy-err');
            document.getElementById('proyErrDesc').classList.add('visible');
            isValid = false;
        } else {
            inputDesc.classList.remove('proy-err');
            document.getElementById('proyErrDesc').classList.remove('visible');
        }
        
        if (!isValid) return;
        
        const proyectoData = {
            nombre: nombre,
            descripcion: descripcion,
            fecha: fecha || null,
            estado: estado,
            tecnologias: tecnologias
        };
        
        try {
            mostrarLoading(true);
            const url = editandoId ? `/proyectos/${editandoId}` : '/proyectos';
            const method = editandoId ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(proyectoData)
            });
            
            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Error al guardar');
            }


            
            const resultado = await response.json();

            if (editandoId) {
                window._proyectoParaEvidencias = resultado;
            } else {
                window._proyectoParaEvidencias = resultado;
            }
            console.log('Proyecto guardado/actualizado:', window._proyectoParaEvidencias);



            mostrarToast(editandoId ? '✅ Proyecto actualizado' : '✅ Proyecto creado');
            ocultarForm();
            await cargarProyectos();
        } catch (error) {
            console.error('Error:', error);
            mostrarToast(error.message || 'Error al guardar proyecto', 'error');
        } finally {
            mostrarLoading(false);
        }
    }

    async function eliminarProyectoBD(id) {
        const proyecto = proyectos.find(p => p.id === id);
        if (!confirm(`¿Eliminar "${proyecto.nombre}"? Esta acción no se puede deshacer.`)) return;
        
        try {
            mostrarLoading(true);
            const response = await fetch(`/proyectos/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) throw new Error('Error al eliminar');
            
            mostrarToast('🗑️ Proyecto eliminado');
            await cargarProyectos();
            if (editandoId === id) ocultarForm();
        } catch (error) {
            console.error('Error:', error);
            mostrarToast('Error al eliminar proyecto', 'error');
        } finally {
            mostrarLoading(false);
        }
    }

    // ── UI RENDER ─────────────────────────────────────────────
    function limpiarForm() {
        inputNombre.value = '';
        inputDesc.value = '';
        inputFecha.value = '';
        selectEstado.value = 'En curso';
        editandoId = null;
        formTitle.innerHTML = '➕ Nuevo Proyecto';
        limpiarTecnologias();
        inputNombre.classList.remove('proy-err');
        inputDesc.classList.remove('proy-err');
        document.getElementById('proyErrNombre').classList.remove('visible');
        document.getElementById('proyErrDesc').classList.remove('visible');
    }

    function ocultarForm() {
        formCard.classList.remove('open');
        limpiarForm();
    }

    function mostrarForm() {
        formCard.classList.add('open');
        inputNombre.focus();
    }

    function getBadgeClass(e) {
        if (e === 'Completado') return 'proy-badge-completado';
        if (e === 'En curso') return 'proy-badge-en-curso';
        return 'proy-badge-en-pausa';
    }

    function renderizar() {
        if (!grid) return;
        
        if (proyectos.length === 0) {
            grid.innerHTML = `<div class="proy-empty">
                <div class="proy-empty-icon">📂</div>
                <h3>Aún no tienes proyectos</h3>
                <p>Crea tu primer proyecto y empieza a construir tu portafolio profesional.</p>
                <button class="proy-empty-btn" id="proyEmptyBtn">+ Crear primer proyecto</button>
            </div>`;
            document.getElementById('proyEmptyBtn')?.addEventListener('click', mostrarForm);
            return;
        }
        
        const ordenados = [...proyectos].sort((a, b) => {
            if (!a.fecha && !b.fecha) return 0;
            if (!a.fecha) return 1;
            if (!b.fecha) return -1;
            return new Date(b.fecha) - new Date(a.fecha);
        });
        
        grid.innerHTML = '';
        ordenados.forEach(p => {
            const card = document.createElement('div');
            card.className = 'proy-card';
            card.innerHTML = `
                <div class="proy-card-band"></div>
                <div class="proy-card-top">
                    <div class="proy-card-nombre">${escapeHtml(p.nombre)}</div>
                    <div class="proy-card-actions">
                        <button class="proy-icon-btn btn-editar" data-id="${p.id}"><i class="fas fa-edit" style="color:#0abf9e"></i></button>
                        <button class="proy-icon-btn btn-eliminar" data-id="${p.id}"><i class="fas fa-trash-alt" style="color:#ef4444"></i></button>
                    </div>
                </div>
                <div class="proy-card-body">
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
        });
        
        document.querySelectorAll('.btn-editar').forEach(b =>
            b.addEventListener('click', () => editarProyecto(parseInt(b.dataset.id))));
        document.querySelectorAll('.btn-eliminar').forEach(b =>
            b.addEventListener('click', () => eliminarProyectoBD(parseInt(b.dataset.id))));
    }

    function editarProyecto(id) {
        const p = proyectos.find(p => p.id === id);
        if (!p) return;

        console.log('Editando proyecto:', p); // Debug
        
        window._proyectoParaEvidencias = p;
        editandoId = id;
        inputNombre.value = p.nombre;
        inputDesc.value = p.descripcion;
        inputFecha.value = p.fecha || '';
        selectEstado.value = p.estado;
        tecnologiasActuales = [...(p.tecnologias || [])];
        renderizarTecnologias();
        formTitle.innerHTML = '✏️ Editar Proyecto';
        mostrarForm();
    }

    // ── EVIDENCIAS ────────────────────────────────────────────
    function abrirEvidencias(proyecto) {
        console.log('🔵 abrirEvidencias llamado con:', proyecto);
        
        if (!proyecto || !proyecto.id) {
            console.error('❌ Proyecto inválido');
            mostrarToast('Error: Proyecto no válido', 'error');
            return;
        }
        
        // Guardar en variable global
        window._proyectoParaEvidencias = proyecto;
        
        // Ocultar elementos de proyectos
        if (proyHeader) proyHeader.style.display = 'none';
        if (formCard) formCard.classList.remove('open');
        if (grid) grid.style.display = 'none';
        
        // Mostrar wrapper de evidencias
        if (evWrapper) evWrapper.style.display = 'block';
        
        // Inicializar evidencias
        if (typeof window.evInit === 'function') {
            console.log('🔵 Llamando a evInit con:', proyecto);
            window.evInit(proyecto);
        } else {
            console.error('❌ evInit no está definida');
            mostrarToast('Error al cargar evidencias', 'error');
        }
    }



    if (btnEvidencias) {
        btnEvidencias.addEventListener('click', () => {
            if (!window._proyectoParaEvidencias && !editandoId) {
                mostrarToast('❌ Primero guarda el proyecto para agregar evidencias', 'error');
                return;
            }
            const proyectoActual = window._proyectoParaEvidencias || (editandoId ? proyectos.find(p => p.id === editandoId) : null);
            if (proyectoActual) {
                abrirEvidencias(proyectoActual);
            } else {
                mostrarToast('❌ Primero guarda el proyecto para agregar evidencias', 'error');
            }
        });
    }

    document.addEventListener('ev:volver', () => {
        evWrapper.style.display = 'none';
        if (proyHeader) proyHeader.style.display = '';
        grid.style.display = '';
        cargarProyectos();
    });

    // ── UTILIDADES ────────────────────────────────────────────
    function mostrarToast(mensaje, tipo = 'success') {
        const toast = document.createElement('div');
        toast.className = `proy-toast ${tipo === 'error' ? 'error' : ''}`;
        toast.innerHTML = mensaje;
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${tipo === 'error' ? '#ef4444' : '#10b981'};
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 500;
            z-index: 9999;
            animation: toastIn 0.3s ease forwards;
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;')
                  .replace(/'/g, '&#39;');
    }

    // ── EVENTOS ───────────────────────────────────────────────
    if (btnMostrarForm) {
        btnMostrarForm.addEventListener('click', () => {
            limpiarForm();
            mostrarForm();
        });
    }
    
    if (btnCancelarForm) btnCancelarForm.addEventListener('click', ocultarForm);
    if (btnGuardarForm) btnGuardarForm.addEventListener('click', guardarProyectoBD);
    if (tecBtnAgregar) tecBtnAgregar.addEventListener('click', agregarTecnologia);
    if (tecInput) {
        tecInput.addEventListener('keypress', e => {
            if (e.key === 'Enter') agregarTecnologia();
        });
    }

    document.querySelectorAll('.tec-suggestion-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            tecInput.value = chip.dataset.tec;
            agregarTecnologia();
        });
    });

    // Inicializar
    cargarProyectos();
})();