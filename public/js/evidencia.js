
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
        const btnMostrar  = document.getElementById('evBtnMostrarForm');
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

        // ── Leer proyecto desde URL o sessionStorage ──────────────
        function init() {
            const params = new URLSearchParams(window.location.search);
            const id     = params.get('proyecto_id');
            if (!id) { mostrarError('No se especificó ningún proyecto.'); return; }

            // Intentar recuperar datos del proyecto guardados al redirigir
            const stored = sessionStorage.getItem('ev_proyecto_' + id);
            if (stored) {
                proyectoActual = JSON.parse(stored);
                poblarBanner();
                cargarEvidencias();
            } else {
                // Fallback: solo usamos el id
                proyectoActual = { id: parseInt(id) };
                poblarBanner();
                cargarEvidencias();
            }
        }

        function poblarBanner() {
            if (!proyectoActual) return;
            document.getElementById('evBannerNombre').textContent       = proyectoActual.nombre    || '—';
            document.getElementById('evBannerEstado').textContent       = proyectoActual.estado    || '—';
            document.getElementById('evBreadcrumbNombre').textContent   = proyectoActual.nombre    || 'Evidencias';
            const desc = proyectoActual.descripcion || '';
            document.getElementById('evBannerDesc').textContent = desc.length > 80 ? desc.slice(0, 80) + '…' : desc;
        }

        function mostrarError(msg) {
            grid.innerHTML = `<p style="color:#ef4444;text-align:center;padding:40px;grid-column:1/-1">❌ ${msg}</p>`;
        }

        // ── Tabs de tipo ──────────────────────────────────────────
        document.querySelectorAll('.ev-tipo-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.ev-tipo-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.ev-panel').forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                tipoActivo = tab.dataset.tipo;
                document.getElementById('evPanel' + cap(tipoActivo)).classList.add('active');
                limpiarErrores();
            });
        });

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

        // ── Form mostrar / ocultar ────────────────────────────────
        btnMostrar?.addEventListener('click',  () => formCard.classList.add('open'));
        btnCancelar?.addEventListener('click', ocultarForm);

        function ocultarForm() { formCard.classList.remove('open'); limpiarForm(); }

        function limpiarForm() {
            archivosNuevos = [];
            newPreviews.innerHTML = '';
            tipoActivo = 'imagen';
            document.querySelectorAll('.ev-tipo-tab').forEach((t, i) => t.classList.toggle('active', i === 0));
            document.querySelectorAll('.ev-panel').forEach((p, i) => p.classList.toggle('active', i === 0));
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

        // ── Guardar ───────────────────────────────────────────────
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

    // ── Sin proyecto: guardar en lista temporal ──────────────
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
        // Actualizar badge del botón en proyectos
        const countEl = document.getElementById('evTriggerCount');
        if (countEl) countEl.textContent = evidenciasPendientes.length;
        toast(`✅ Evidencia añadida (${evidenciasPendientes.length})`);
        ocultarForm();
        renderizarPendientes();
        return;
    }

    // ── Con proyecto: subir directo al backend (tu código original) ──
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

        // ── Eliminar ──────────────────────────────────────────────
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
            console.log('📌 cargarEvidencias llamado');
            console.log('📌 proyectoActual:', proyectoActual);
            console.log('📌 proyectoActual.id:', proyectoActual?.id);        
            const url = `/proyectos/${proyectoActual.id}/evidencias`;
            console.log('📌 Fetching URL:', url);
            
            grid.innerHTML = '<p style="color:#94a3b8;text-align:center;padding:30px;grid-column:1/-1">Cargando evidencias...</p>';
            try {
                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
                });
                console.log('📌 Response status:', res.status);
                
                if (!res.ok) {
                    console.error('❌ Error response:', res.status);
                    throw new Error('HTTP error');
                }
                
                evidencias = await res.json();
                console.log('📌 Evidencias cargadas:', evidencias.length);
                renderizar();
            } catch (error) {
                console.error('❌ Error en cargarEvidencias:', error);
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

        // ── Renderizar ────────────────────────────────────────────
        function renderizar() {
            document.getElementById('evStatImgs').textContent  = evidencias.filter(e => e.tipo === 'imagen').length;
            document.getElementById('evStatLinks').textContent = evidencias.filter(e => e.tipo === 'enlace').length;
            document.getElementById('evStatRepos').textContent = evidencias.filter(e => e.tipo === 'repositorio').length;
            if (!grid) return;
            const lista = filtradas();
            if (lista.length === 0) {
                grid.innerHTML = `
                    <div class="ev-empty">
                        <div class="ev-empty-icon">📎</div>
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
                                <a href="${esc(ev.url_publica)}" target="_blank" title="Abrir"><i class="fas fa-expand"></i></a>
                                <button class="btn-lightbox" data-src="${esc(ev.url_publica)}" title="Ver"><i class="fas fa-eye"></i></button>
                                <button class="btn-del" data-id="${ev.id}"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="ev-card-body">
                            <span class="ev-card-type-badge ev-badge-img"><i class="fas fa-image"></i> Imagen</span>
                            <div class="ev-card-nombre">${esc(ev.etiqueta || ev.archivo_nombre || 'Imagen')}</div>
                            <div class="ev-card-meta">
                                <span>${esc(ev.archivo_nombre || '')}</span>
                                <div class="ev-card-actions-row">
                                    <button class="ev-icon-btn danger btn-del" data-id="${ev.id}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>`;
                    card.querySelectorAll('.btn-lightbox').forEach(b => b.addEventListener('click', () => abrirLightbox(b.dataset.src)));
                } else if (ev.tipo === 'enlace') {
                    card.innerHTML = `
                        <div class="ev-card-band ev-card-band-link"></div>
                        <div class="ev-card-link-icon">🔗</div>
                        <div class="ev-card-body">
                            <span class="ev-card-type-badge ev-badge-link"><i class="fas fa-link"></i> Enlace</span>
                            <div class="ev-card-nombre">${esc(ev.etiqueta || 'Enlace')}</div>
                            <a class="ev-card-url" href="${esc(ev.url_publica)}" target="_blank">${esc(ev.url_publica)}</a>
                            ${ev.descripcion ? `<div style="font-size:12px;color:#64748b">${esc(ev.descripcion)}</div>` : ''}
                            <div class="ev-card-meta">
                                <span style="color:#94a3b8">Enlace externo</span>
                                <div class="ev-card-actions-row">
                                    <a class="ev-icon-btn" href="${esc(ev.url_publica)}" target="_blank" title="Abrir"><i class="fas fa-external-link-alt"></i></a>
                                    <button class="ev-icon-btn danger btn-del" data-id="${ev.id}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>`;
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
                                <span style="color:#94a3b8">${esc(ev.plataforma || 'Git')}</span>
                                <div class="ev-card-actions-row">
                                    <a class="ev-icon-btn" href="${esc(ev.url_publica)}" target="_blank" title="Abrir repo"><i class="fas fa-external-link-alt"></i></a>
                                    <button class="ev-icon-btn danger btn-del" data-id="${ev.id}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>`;
                }
                card.querySelectorAll('.btn-del').forEach(b => b.addEventListener('click', () => eliminarEvidencia(parseInt(b.dataset.id))));
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

        window.evInit = function(proyecto) {
            console.log('🟢 evInit recibió proyecto:', proyecto);
            
            if (!proyecto || !proyecto.id) {
                console.error('❌ Proyecto inválido en evInit');
                mostrarError('No se pudo cargar el proyecto');
                return;
            }
            
            proyectoActual = proyecto;
            window._proyectoParaEvidencias = proyecto;  // ← IMPORTANTE: sincronizar
            console.log('✅ proyectoActual asignado:', proyectoActual);
            
            limpiarForm();
            ocultarForm();
            poblarBanner();
            cargarEvidencias();
        };
     // Muestra lista temporal en el grid (sin backend)
function renderizarPendientes() {
    if (!grid) return;
    if (evidenciasPendientes.length === 0) {
        grid.innerHTML = `<div class="ev-empty">
            <div class="ev-empty-icon">📎</div>
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
                    <span class="ev-card-type-badge ev-badge-img"><i class="fas fa-image"></i> Imagen</span>
                    <div class="ev-card-nombre">${esc(ev.nombre)}</div>
                    <div class="ev-card-meta">
                        <span style="color:#94a3b8">${ev.archivos.length} archivo(s) — pendiente</span>
                        <button class="ev-icon-btn danger btn-rm-pend" data-idx="${idx}"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>`;
        } else if (ev.tipo === 'enlace') {
            card.innerHTML = `
                <div class="ev-card-band ev-card-band-link"></div>
                <div class="ev-card-link-icon">🔗</div>
                <div class="ev-card-body">
                    <span class="ev-card-type-badge ev-badge-link"><i class="fas fa-link"></i> Enlace</span>
                    <div class="ev-card-nombre">${esc(ev.nombre)}</div>
                    <a class="ev-card-url" href="${esc(ev.url)}" target="_blank">${esc(ev.url)}</a>
                    <div class="ev-card-meta">
                        <span style="color:#94a3b8">pendiente</span>
                        <button class="ev-icon-btn danger btn-rm-pend" data-idx="${idx}"><i class="fas fa-trash-alt"></i></button>
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
                        <span style="color:#94a3b8">pendiente</span>
                        <button class="ev-icon-btn danger btn-rm-pend" data-idx="${idx}"><i class="fas fa-trash-alt"></i></button>
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
        // fetch directo en vez de enviar()
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

// Resetear al abrir nuevo proyecto
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

    // ============================================
// FIX PARA EDITAR PROYECTOS - AGREGADO MANUALMENTE
// ============================================

(function fixEdicionProyectos() {
    // Función global para forzar recarga de evidencias
    window.forzarRecargaEvidencias = function(proyecto) {
        console.log('🔄 Forzando recarga:', proyecto);
        
        if (!proyecto || !proyecto.id) {
            console.error('❌ Proyecto inválido');
            return false;
        }
        
        // Guardar proyecto
        window._proyectoParaEvidencias = proyecto;
        sessionStorage.setItem('ultimo_proyecto_activo', JSON.stringify(proyecto));
        
        // Recargar evidencias
        if (typeof window.evInit === 'function') {
            window.evInit(proyecto);
        }
        
        return true;
    };
    
    // Detectar cuando se abre el formulario de evidencias
    document.addEventListener('click', function(e) {
        // Si el clic es en el botón "Agregar Evidencia"
        if (e.target.id === 'evBtnMostrarForm' || 
            e.target.closest('#evBtnMostrarForm')) {
            
            // Recuperar proyecto del sessionStorage
            const proyectoGuardado = sessionStorage.getItem('ultimo_proyecto_activo');
            
            if (proyectoGuardado && !window._proyectoParaEvidencias) {
                const proyecto = JSON.parse(proyectoGuardado);
                console.log('🔄 Recuperando proyecto guardado:', proyecto);
                
                if (typeof window.evInit === 'function') {
                    window.evInit(proyecto);
                }
            }
        }
    });
})();
    
