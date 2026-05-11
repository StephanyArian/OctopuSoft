(function () {
    let evidencias     = [];
    let proyectoActual = null;
    let tipoActivo     = 'imagen';
    let archivosNuevos = [];
    let filtroActivo   = 'todos';
    let busqueda       = '';
    let evidenciasPendientes = [];

    const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const formCard    = document.getElementById('evFormCard');
    const btnCancelar = document.getElementById('evBtnCancelar');
    const btnGuardar  = document.getElementById('evBtnGuardar');
    const grid        = document.getElementById('evGrid');
    const dropzone    = document.getElementById('evDropzone');
    const fileInput   = document.getElementById('evFileInput');
    const newPreviews = document.getElementById('evNewPreviews');
    const searchInp   = document.getElementById('evSearchInp');
    const lightbox    = document.getElementById('evLightbox');
    const lightboxImg = document.getElementById('evLightboxImg');
    const lightboxClose = document.getElementById('evLightboxClose');

    // ── DROPDOWN AGREGAR EVIDENCIA ──
    const dropdownTrigger = document.getElementById('evBtnDropdownTrigger');
    const dropdownMenu = document.getElementById('evDropdownMenu');

    function cambiarPanelPorTipo(tipo) {
        tipoActivo = tipo;
        document.querySelectorAll('.ev-panel').forEach(p => p.classList.remove('active'));
        if (tipo === 'imagen') document.getElementById('evPanelImagen').classList.add('active');
        else if (tipo === 'enlace') document.getElementById('evPanelEnlace').classList.add('active');
        else if (tipo === 'repositorio') document.getElementById('evPanelRepositorio').classList.add('active');
        limpiarErrores();
    }

    if (dropdownTrigger && dropdownMenu) {
        dropdownTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownTrigger.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        dropdownMenu.querySelectorAll('.ev-dropdown-item').forEach(item => {
            item.addEventListener('click', () => {
                const tipo = item.dataset.tipo;
                dropdownMenu.classList.remove('show');
                cambiarPanelPorTipo(tipo);

                // Abrir el formulario
                if (formCard) {
                    if (proyectoActual) {
                        formCard.classList.add('open');
                    } else {
                        // Es proyecto nuevo, limpiar y abrir
                        evidencias = [];
                        const imgSpan = document.getElementById('evStatImgs')?.querySelector('span');
                        const linkSpan = document.getElementById('evStatLinks')?.querySelector('span');
                        const repoSpan = document.getElementById('evStatRepos')?.querySelector('span');
                        if (imgSpan) imgSpan.textContent = '0';
                        if (linkSpan) linkSpan.textContent = '0';
                        if (repoSpan) repoSpan.textContent = '0';
                        formCard.classList.add('open');
                    }
                }
            });
        });
    }

    // ── Leer proyecto desde URL o sessionStorage ──────────────
    function init() {
        const params = new URLSearchParams(window.location.search);
        const id     = params.get('proyecto_id');
        if (!id) { mostrarError('No se especificó ningún proyecto.'); return; }

        const stored = sessionStorage.getItem('ev_proyecto_' + id);
        if (stored) {
            proyectoActual = JSON.parse(stored);
            cargarEvidencias();
        } else {
            proyectoActual = { id: parseInt(id) };
            cargarEvidencias();
        }
    }

    function mostrarError(msg) {
        grid.innerHTML = `<p style="color:#ef4444;text-align:center;padding:40px;grid-column:1/-1">❌ ${msg}</p>`;
    }

    // ── Dropzone ──────────────────────────────────────────────
    dropzone?.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
    dropzone?.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
    dropzone?.addEventListener('drop', e => { e.preventDefault(); dropzone.classList.remove('drag-over'); agregarArchivos([...e.dataTransfer.files]); });
    fileInput?.addEventListener('change', () => { agregarArchivos([...fileInput.files]); fileInput.value = ''; });

    function agregarArchivos(files) {
        files.forEach(f => {
            if (!['image/jpeg','image/jpg','image/png'].includes(f.type)) { toast(`❌ "${f.name}" no es JPG/PNG`, 'error'); return; }
            if (f.size > 5 * 1024 * 1024) { toast(`❌ "${f.name}" supera 5 MB`, 'error'); return; }
            archivosNuevos.push(f);
            const reader = new FileReader();
            reader.onload = ev => agregarThumb(f, ev.target.result);
            reader.readAsDataURL(f);
        });
    }

    function agregarThumb(file, src) {
        const wrap = document.createElement('div');
        wrap.className = 'ev-new-thumb';
        wrap.innerHTML = `<img src="${src}" alt="${esc(file.name)}"><button class="ev-new-thumb-rm" title="Quitar">✕</button>`;
        wrap.querySelector('button').addEventListener('click', () => { archivosNuevos = archivosNuevos.filter(f => f !== file); wrap.remove(); });
        newPreviews?.appendChild(wrap);
    }

    btnCancelar?.addEventListener('click', ocultarForm);

    function ocultarForm() { formCard.classList.remove('open'); limpiarForm(); }

    function limpiarForm() {
        archivosNuevos = [];
        newPreviews.innerHTML = '';
        // No reseteamos tipoActivo para mantener el panel seleccionado, pero sí limpiamos campos
        ['evImgNombre','evLinkNombre','evLinkUrl','evLinkDesc','evRepoNombre','evRepoUrl','evRepoDesc'].forEach(id => {
            const el = document.getElementById(id); if (el) el.value = '';
        });
        const sel = document.getElementById('evRepoPlataforma'); if (sel) sel.value = 'GitHub';
        limpiarErrores();
    }

    function limpiarErrores() {
        document.querySelectorAll('.ev-err-msg').forEach(e => e.classList.remove('visible'));
        document.querySelectorAll('.ev-inp.ev-err').forEach(e => e.classList.remove('ev-err'));
    }

    // ── Guardar evidencia ───────────────────────────────────────
    btnGuardar?.addEventListener('click', guardarEvidencia);

    async function enviar(fd) {
        try {
            const res = await fetch(`/proyectos/${proyectoActual.id}/evidencias`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: fd
            });
            if (!res.ok) throw new Error('Error al guardar');
            toast('✅ Evidencia guardada');
            ocultarForm();
            cargarEvidencias();
        } catch (err) {
            toast('❌ Error al guardar evidencia', 'error');
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar evidencia';
        }
    }

    async function guardarEvidencia() {
        limpiarErrores();

        if (!proyectoActual) {
            let isValid = true;
            if (tipoActivo === 'imagen') {
                const nombre = document.getElementById('evImgNombre').value.trim();
                if (!nombre) { setErr('evImgNombre','evErrImgNombre'); isValid = false; }
                if (archivosNuevos.length === 0) { document.getElementById('evErrImgFile').classList.add('visible'); isValid = false; }
                if (!isValid) return;
                evidenciasPendientes.push({ tipo:'imagen', nombre, archivos:[...archivosNuevos],
                    previews:[...newPreviews.querySelectorAll('img')].map(i=>i.src) });
            } else if (tipoActivo === 'enlace') {
                const nombre = document.getElementById('evLinkNombre').value.trim();
                const url    = document.getElementById('evLinkUrl').value.trim();
                const desc   = document.getElementById('evLinkDesc').value.trim();
                if (!nombre) { setErr('evLinkNombre','evErrLinkNombre'); isValid = false; }
                if (!url||!isValidUrl(url)) { setErr('evLinkUrl','evErrLinkUrl'); isValid = false; }
                if (!isValid) return;
                evidenciasPendientes.push({ tipo:'enlace', nombre, url, desc });
            } else {
                const nombre     = document.getElementById('evRepoNombre').value.trim();
                const url        = document.getElementById('evRepoUrl').value.trim();
                const plataforma = document.getElementById('evRepoPlataforma').value;
                const desc       = document.getElementById('evRepoDesc').value.trim();
                if (!nombre) { setErr('evRepoNombre','evErrRepoNombre'); isValid = false; }
                if (!url||!isValidUrl(url)) { setErr('evRepoUrl','evErrRepoUrl'); isValid = false; }
                if (!isValid) return;
                evidenciasPendientes.push({ tipo:'repositorio', nombre, url, plataforma, desc });
            }
            const countEl = document.getElementById('evTriggerCount');
            if (countEl) countEl.textContent = evidenciasPendientes.length;
            toast(`✅ Evidencia añadida (${evidenciasPendientes.length})`);
            ocultarForm();
            renderizarPendientes();
            return;
        }

        let isValid = true;
        if (tipoActivo === 'imagen') {
            const nombre = document.getElementById('evImgNombre').value.trim();
            if (!nombre) { setErr('evImgNombre','evErrImgNombre'); isValid = false; }
            if (archivosNuevos.length === 0) { document.getElementById('evErrImgFile').classList.add('visible'); isValid = false; }
            if (!isValid) return;
            btnGuardar.disabled = true; btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            const fd = new FormData();
            fd.append('tipo','imagen'); fd.append('nombre',nombre); fd.append('project_id',proyectoActual.id);
            archivosNuevos.forEach(f => fd.append('imagenes[]', f));
            await enviar(fd);
        } else if (tipoActivo === 'enlace') {
            const nombre = document.getElementById('evLinkNombre').value.trim();
            const url    = document.getElementById('evLinkUrl').value.trim();
            const desc   = document.getElementById('evLinkDesc').value.trim();
            if (!nombre) { setErr('evLinkNombre','evErrLinkNombre'); isValid = false; }
            if (!url||!isValidUrl(url)) { setErr('evLinkUrl','evErrLinkUrl'); isValid = false; }
            if (!isValid) return;
            btnGuardar.disabled = true; btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            const fd = new FormData();
            fd.append('tipo','enlace'); fd.append('etiqueta',nombre); fd.append('url',url);
            fd.append('descripcion',desc); fd.append('project_id',proyectoActual.id);
            await enviar(fd);
        } else {
            const nombre     = document.getElementById('evRepoNombre').value.trim();
            const url        = document.getElementById('evRepoUrl').value.trim();
            const plataforma = document.getElementById('evRepoPlataforma').value;
            const desc       = document.getElementById('evRepoDesc').value.trim();
            if (!nombre) { setErr('evRepoNombre','evErrRepoNombre'); isValid = false; }
            if (!url||!isValidUrl(url)) { setErr('evRepoUrl','evErrRepoUrl'); isValid = false; }
            if (!isValid) return;
            btnGuardar.disabled = true; btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            const fd = new FormData();
            fd.append('tipo','repositorio'); fd.append('etiqueta',nombre); fd.append('url',url);
            fd.append('plataforma',plataforma); fd.append('descripcion',desc); fd.append('project_id',proyectoActual.id);
            await enviar(fd);
        }
    }

    // ── Eliminar evidencia ──────────────────────────────────────
    async function eliminarEvidencia(id) {
        const modal = document.getElementById('evModalEliminar');
        modal.style.display = 'flex';

        return new Promise(resolve => {
            document.getElementById('evModalConfirmar').onclick = async () => {
                modal.style.display = 'none';
                try {
                    const res = await fetch(`/evidencias/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    });
                    if (!res.ok) throw new Error();
                    evidencias = evidencias.filter(e => e.id !== id);
                    toast('Evidencia eliminada');
                    renderizar();
                } catch { toast('❌ No se pudo eliminar', 'error'); }
            };
            document.getElementById('evModalCancelar').onclick = () => {
                modal.style.display = 'none';
            };
            modal.onclick = (e) => {
                if (e.target === modal) modal.style.display = 'none';
            };
        });
    }

    // ── Cargar desde API ──────────────────────────────────────
    async function cargarEvidencias() {
        const url = `/proyectos/${proyectoActual.id}/evidencias`;
        grid.innerHTML = '<p style="color:#94a3b8;text-align:center;padding:30px;grid-column:1/-1">Cargando evidencias...</p>';
        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            if (!res.ok) throw new Error('HTTP error');
            evidencias = await res.json();
            renderizar();
        } catch (error) {
            grid.innerHTML = '<p style="color:#ef4444;text-align:center;padding:30px;grid-column:1/-1">❌ No se pudieron cargar las evidencias</p>';
        }
    }

    // ── Filtros y búsqueda ────────────────────────────────────
    document.querySelectorAll('.ev-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.ev-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filtroActivo = btn.dataset.filter;
            renderizar();
        });
    });

    searchInp?.addEventListener('input', () => { busqueda = searchInp.value.trim().toLowerCase(); renderizar(); });

    function filtradas() {
        return evidencias.filter(e => {
            const matchTipo    = filtroActivo === 'todos' || e.tipo === filtroActivo;
            const texto        = ((e.etiqueta||'') + ' ' + (e.archivo_nombre||'') + ' ' + (e.url_publica||'') + ' ' + (e.descripcion||'')).toLowerCase();
            const matchBusqueda = !busqueda || texto.includes(busqueda);
            return matchTipo && matchBusqueda;
        });
    }

    // ── Renderizar grid ────────────────────────────────────────
    function renderizar() {
        const imgSpan = document.getElementById('evStatImgs')?.querySelector('span');
        const linkSpan = document.getElementById('evStatLinks')?.querySelector('span');
        const repoSpan = document.getElementById('evStatRepos')?.querySelector('span');
        if (imgSpan) imgSpan.textContent = evidencias.filter(e => e.tipo === 'imagen').length;
        if (linkSpan) linkSpan.textContent = evidencias.filter(e => e.tipo === 'enlace').length;
        if (repoSpan) repoSpan.textContent = evidencias.filter(e => e.tipo === 'repositorio').length;

        if (!grid) return;
        const lista = filtradas();
        if (lista.length === 0) {
            grid.innerHTML = `
                <div class="ev-empty">
                    <h3>Sin evidencias${filtroActivo !== 'todos' ? ' en esta categoría' : ''}</h3>
                    <p>${busqueda ? 'No se encontraron resultados para tu búsqueda.' : 'Agrega imágenes, enlaces o repositorios a este proyecto.'}</p>
                    ${!busqueda && filtroActivo === 'todos' ? '<button class="ev-empty-btn" id="evEmptyBtn">+ Agregar primera evidencia</button>' : ''}
                </div>`;
            document.getElementById('evEmptyBtn')?.addEventListener('click', () => formCard.classList.add('open'));
            return;
        }
        grid.innerHTML = '';
        lista.forEach(ev => {
            const card = document.createElement('div');
            card.className = 'ev-card';
            if (ev.tipo === 'imagen') {
                card.innerHTML = `
                    <div class="ev-card-band ev-card-band-img"></div>
                    <div class="ev-card-img-wrap">
                        <img src="${esc(ev.url_publica)}" alt="${esc(ev.archivo_nombre || 'imagen')}">
                        <div class="ev-card-img-overlay">
                            <button class="btn-lightbox" data-src="${esc(ev.url_publica)}" title="Ver"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                            <button class="btn-del" data-id="${ev.id}" title="Eliminar"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>
                    </div>
                    <div class="ev-card-body">
                        <span class="ev-card-type-badge ev-badge-img"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> Imagen</span>
                        <div class="ev-card-nombre">${esc(ev.etiqueta || ev.archivo_nombre || 'Imagen')}</div>
                        <div class="ev-card-meta">
                            <span>${esc(ev.archivo_nombre || '')}</span>
                            <button class="ev-icon-btn danger btn-del" data-id="${ev.id}" title="Eliminar"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>
                    </div>`;
                card.querySelectorAll('.btn-lightbox').forEach(b => b.addEventListener('click', () => abrirLightbox(b.dataset.src)));
                card.querySelectorAll('.btn-del').forEach(b => b.addEventListener('click', () => eliminarEvidencia(parseInt(b.dataset.id))));
            } else if (ev.tipo === 'enlace') {
                card.innerHTML = `
                    <div class="ev-card-band ev-card-band-link"></div>
                    <div class="ev-card-link-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div>
                    <div class="ev-card-body">
                        <span class="ev-card-type-badge ev-badge-link"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg> Enlace</span>
                        <div class="ev-card-nombre">${esc(ev.etiqueta || 'Enlace')}</div>
                        <a class="ev-card-url" href="${esc(ev.url_publica)}" target="_blank">${esc(ev.url_publica)}</a>
                        ${ev.descripcion ? `<div style="font-size:12px;color:#64748b">${esc(ev.descripcion)}</div>` : ''}
                        <div class="ev-card-meta">
                            <span>Enlace externo</span>
                            <button class="ev-icon-btn danger btn-del" data-id="${ev.id}" title="Eliminar"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>
                    </div>`;
                card.querySelectorAll('.btn-del').forEach(b => b.addEventListener('click', () => eliminarEvidencia(parseInt(b.dataset.id))));
            } else {
                const icon = ev.plataforma === 'GitLab' ? 'fa-gitlab' : ev.plataforma === 'Bitbucket' ? 'fa-bitbucket' : 'fa-github';
                card.innerHTML = `
                    <div class="ev-card-band ev-card-band-repo"></div>
                    <div class="ev-card-link-icon"><i class="fab ${icon}" style="font-size:32px;color:#374151"></i></div>
                    <div class="ev-card-body">
                        <span class="ev-card-type-badge ev-badge-repo"><i class="fab ${icon}"></i> ${esc(ev.plataforma || 'Repositorio')}</span>
                        <div class="ev-card-nombre">${esc(ev.etiqueta || 'Repositorio')}</div>
                        <a class="ev-card-url" href="${esc(ev.url_publica)}" target="_blank">${esc(ev.url_publica)}</a>
                        ${ev.descripcion ? `<div style="font-size:12px;color:#64748b">${esc(ev.descripcion)}</div>` : ''}
                        <div class="ev-card-meta">
                            <span>${esc(ev.plataforma || 'Git')}</span>
                            <button class="ev-icon-btn danger btn-del" data-id="${ev.id}" title="Eliminar"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>
                    </div>`;
                card.querySelectorAll('.btn-del').forEach(b => b.addEventListener('click', () => eliminarEvidencia(parseInt(b.dataset.id))));
            }
            grid.appendChild(card);
        });
    }

    // ── Lightbox ──────────────────────────────────────────────
    function abrirLightbox(src) { lightboxImg.src = src; lightbox.classList.add('open'); }
    lightboxClose?.addEventListener('click', () => lightbox.classList.remove('open'));
    lightbox?.addEventListener('click', e => { if (e.target === lightbox) lightbox.classList.remove('open'); });

    // ── Utilidades ────────────────────────────────────────────
    function setErr(inputId, errId) { document.getElementById(inputId)?.classList.add('ev-err'); document.getElementById(errId)?.classList.add('visible'); }
    function cap(str) { return str.charAt(0).toUpperCase() + str.slice(1); }
    function isValidUrl(str) { try { new URL(str); return true; } catch { return false; } }
    function esc(str) { if (!str) return ''; return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function toast(msg, tipo) {
        const el = document.createElement('div');
        el.className = 'ev-toast' + (tipo === 'error' ? ' error' : '');
        el.textContent = msg;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3200);
    }

    // ── Exposición global ──────────────────────────────────────
    window.evInit = function(proyecto) {
        if (!proyecto || !proyecto.id) return;

        evidencias = [];
        filtroActivo = 'todos';
        busqueda = '';
        if (searchInp) searchInp.value = '';
        document.querySelectorAll('.ev-filter-btn').forEach((b, i) => b.classList.toggle('active', i === 0));

        proyectoActual = proyecto;
        window._proyectoParaEvidencias = proyecto;
        sessionStorage.setItem('ultimo_proyecto_activo', JSON.stringify(proyecto));

        window._esProyectoNuevo = !proyecto.tiene_evidencias;

        ocultarForm();
        renderizarPendientes();
        cargarEvidencias();
    };

    function renderizarPendientes() {
        if (!grid) return;
        if (evidenciasPendientes.length === 0) {
            grid.innerHTML = `<div class="ev-empty">
                <h3>Aún no has añadido evidencias.</h3>
                <p>Se guardarán junto al proyecto.</p>
            </div>`;
            return;
        }
        grid.innerHTML = '';
        evidenciasPendientes.forEach((ev, idx) => {
            const card = document.createElement('div');
            card.className = 'ev-card';
            if (ev.tipo === 'imagen') {
                const imgSrc = ev.previews?.[0] || '';
                card.innerHTML = `
                    <div class="ev-card-band ev-card-band-img"></div>
                    ${imgSrc ? `<div class="ev-card-img-wrap"><img src="${imgSrc}"></div>` : ''}
                    <div class="ev-card-body">
                        <span class="ev-card-type-badge ev-badge-img"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> Imagen</span>
                        <div class="ev-card-nombre">${esc(ev.nombre)}</div>
                        <div class="ev-card-meta">
                            <span>${ev.archivos.length} archivo(s) — pendiente</span>
                            <button class="ev-icon-btn danger btn-rm-pend" data-idx="${idx}"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>
                    </div>`;
            } else if (ev.tipo === 'enlace') {
                card.innerHTML = `
                    <div class="ev-card-band ev-card-band-link"></div>
                    <div class="ev-card-link-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div>
                    <div class="ev-card-body">
                        <span class="ev-card-type-badge ev-badge-link"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg> Enlace</span>
                        <div class="ev-card-nombre">${esc(ev.nombre)}</div>
                        <a class="ev-card-url" href="${esc(ev.url)}" target="_blank">${esc(ev.url)}</a>
                        <div class="ev-card-meta">
                            <span>pendiente</span>
                            <button class="ev-icon-btn danger btn-rm-pend" data-idx="${idx}"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>
                    </div>`;
            } else {
                card.innerHTML = `
                    <div class="ev-card-band ev-card-band-repo"></div>
                    <div class="ev-card-link-icon"><i class="fab fa-github" style="font-size:32px"></i></div>
                    <div class="ev-card-body">
                        <span class="ev-card-type-badge ev-badge-repo"><i class="fab fa-github"></i> ${esc(ev.plataforma||'Repo')}</span>
                        <div class="ev-card-nombre">${esc(ev.nombre)}</div>
                        <a class="ev-card-url" href="${esc(ev.url)}" target="_blank">${esc(ev.url)}</a>
                        <div class="ev-card-meta">
                            <span>pendiente</span>
                            <button class="ev-icon-btn danger btn-rm-pend" data-idx="${idx}"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>
                    </div>`;
            }
            card.querySelector('.btn-rm-pend').addEventListener('click', () => {
                evidenciasPendientes.splice(idx, 1);
                const countEl = document.getElementById('evTriggerCount');
                if (countEl) countEl.textContent = evidenciasPendientes.length;
                renderizarPendientes();
            });
            grid.appendChild(card);
        });
    }

    window.evSubirPendientes = async function(proyectoId) {
        if (evidenciasPendientes.length === 0) return;
        proyectoActual = { id: proyectoId };
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        for (const ev of evidenciasPendientes) {
            const fd = new FormData();
            fd.append('project_id', proyectoId);
            if (ev.tipo === 'imagen') {
                fd.append('tipo','imagen'); fd.append('nombre', ev.nombre);
                ev.archivos.forEach(f => fd.append('imagenes[]', f));
            } else if (ev.tipo === 'enlace') {
                fd.append('tipo','enlace'); fd.append('etiqueta', ev.nombre);
                fd.append('url', ev.url); fd.append('descripcion', ev.desc||'');
            } else {
                fd.append('tipo','repositorio'); fd.append('etiqueta', ev.nombre);
                fd.append('url', ev.url); fd.append('plataforma', ev.plataforma||'GitHub');
                fd.append('descripcion', ev.desc||'');
            }
            try {
                await fetch(`/proyectos/${proyectoId}/evidencias`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: fd,
                });
            } catch(e) { console.error('Error subiendo evidencia pendiente:', e); }
        }
        evidenciasPendientes = [];
        const countEl = document.getElementById('evTriggerCount');
        if (countEl) countEl.textContent = '0';
    };

    window.evResetearPendientes = function() {
        evidenciasPendientes = [];
        proyectoActual = null;
        renderizarPendientes();
        const countEl = document.getElementById('evTriggerCount');
        if (countEl) countEl.textContent = '0';
    };

    if (window.location.pathname.includes('mis-evidencias')) {
        init();
    }
})();