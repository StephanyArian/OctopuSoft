// =====================================================================
// public/js/proyectos.js
// Módulo: Gestión de Proyectos del Portafolio
// CORREGIDO: Límite de 1000 CARACTERES (no palabras)
// =====================================================================

(function() {
    let proyectos = [];
    let editandoId = null;
    let tecnologiasActuales = [];
    let quill = null;
    let lastValidHtml = '';

    // LÍMITE DE 1000 CARACTERES PARA DESCRIPCIÓN
    const LIMITE_CARACTERES_DESC = 5000;

    let textoBusqueda = localStorage.getItem('proy_busqueda') || '';
    let filtroEstadoActual = localStorage.getItem('proy_estado') || 'todos';
    let filtroRolActual = localStorage.getItem('proy_rol') || 'todos';
    let filtroTecnologiaActual = localStorage.getItem('proy_tec') || 'todas';
    let filtroVisibilidadActual = localStorage.getItem('proy_vis') || 'todos';
    let ordenActual = localStorage.getItem('proy_orden') || 'fecha_desc';

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

    const svgCandadoAbierto = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg>';
    const svgCandadoCerrado = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><line x1="3" y1="3" x2="21" y2="21"/></svg>';
    const svgEditar = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0abf9e" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
    const svgEliminar = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>';
    const svgCheck = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
    const svgError = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
    const svgImagen = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
    const svgEnlace = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>';
    const svgRepo = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>';
    const svgVolver = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>';

    function guardarFiltros() {
        localStorage.setItem('proy_busqueda', textoBusqueda);
        localStorage.setItem('proy_estado', filtroEstadoActual);
        localStorage.setItem('proy_rol', filtroRolActual);
        localStorage.setItem('proy_tec', filtroTecnologiaActual);
        localStorage.setItem('proy_vis', filtroVisibilidadActual);
        localStorage.setItem('proy_orden', ordenActual);
    }

    function toggleProyHeader(ocultar) { if (!proyHeader) return; proyHeader.style.display = ocultar ? 'none' : 'flex'; }
    function toggleGrid(ocultar) { if (!grid) return; grid.style.display = ocultar ? 'none' : 'grid'; }
    function mostrarLoading(mostrar) { isLoading = mostrar; if (btnGuardarForm) { btnGuardarForm.disabled = mostrar; btnGuardarForm.textContent = mostrar ? 'Guardando...' : 'Guardar proyecto'; } }

    function mostrarToast(mensaje, tipo = 'success') {
        const toast = document.createElement('div');
        toast.className = `proy-toast ${tipo === 'error' ? 'error' : ''}`;
        toast.innerHTML = `${tipo === 'error' ? svgError : svgCheck} ${mensaje}`;
        toast.style.cssText = `position:fixed;bottom:20px;right:20px;background:${tipo==='error'?'#ef4444':'#10b981'};color:white;padding:12px 20px;border-radius:12px;font-weight:500;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,.15);animation:toastIn .3s ease forwards;display:flex;align-items:center;gap:8px;`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function escapeHtml(str) { if (!str) return ''; return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

    function stripHtml(html) {
        if (!html) return '';
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
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
        for (let tech of techMap) { for (let kw of tech.keywords) { if (lowerTec.includes(kw)) return tech.icon; } }
        return '<i class="fas fa-code"></i>';
    }

    function getBadgeClass(e) { if (e === 'Completado') return 'proy-badge-completado'; if (e === 'En curso') return 'proy-badge-en-curso'; return 'proy-badge-en-pausa'; }

    // ====================================================================
    // CONTADOR DE CARACTERES (CORREGIDO)
    // ====================================================================
    function actualizarContadorCaracteres() {
        if (!contadorDesc) return;
        const text = quill ? quill.getText() : '';
        const caracteres = text.length;
        contadorDesc.textContent = `${caracteres}/${LIMITE_CARACTERES_DESC} caracteres`;
        contadorDesc.classList.remove('warning', 'limit');
        if (caracteres >= LIMITE_CARACTERES_DESC) contadorDesc.classList.add('limit');
        else if (caracteres >= LIMITE_CARACTERES_DESC - 200) contadorDesc.classList.add('warning');
    }

    // ====================================================================
    // QUILL EDITOR - CON LÍMITE DE 1000 CARACTERES
    // ====================================================================
    function initQuill() {
        if (typeof Quill === 'undefined') return;
        const qc = document.getElementById('quillEditor');
        if (!qc) return;
        if (quill) return;
        
        const Font = Quill.import('formats/font');
        Font.whitelist = ['arial', 'times-new-roman', 'georgia'];
        Quill.register(Font, true);
        
        quill = new Quill('#quillEditor', {
            theme: 'snow',
            placeholder: 'Describe brevemente el proyecto...',
            modules: {
                toolbar: [
                    [{ 'font': ['arial', 'times-new-roman', 'georgia'] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'color': [] }],
                    ['link'],
                    [{ 'list': 'bullet' }, { 'list': 'ordered' }],
                    ['clean']
                ]
            }
        });
        
        lastValidHtml = quill.root.innerHTML;
        
        quill.on('text-change', function(delta, oldDelta, source) {
            if (source === 'user') {
                const currentText = quill.getText();
                const caracteresActuales = currentText.length;
                
                if (caracteresActuales > LIMITE_CARACTERES_DESC) {
                    const selection = quill.getSelection();
                    quill.root.innerHTML = lastValidHtml;
                    if (selection && selection.index !== undefined) {
                        quill.setSelection(selection.index, 0);
                    }
                    mostrarToast(`Límite de ${LIMITE_CARACTERES_DESC} caracteres alcanzado`, 'error');
                } else {
                    lastValidHtml = quill.root.innerHTML;
                    if (inputDesc) inputDesc.value = quill.root.innerHTML;
                    actualizarContadorCaracteres();
                }
            } else {
                lastValidHtml = quill.root.innerHTML;
                if (inputDesc) inputDesc.value = quill.root.innerHTML;
                actualizarContadorCaracteres();
            }
        });
        
        actualizarContadorCaracteres();
    }
    
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initQuill); } 
    else { initQuill(); }

    // ====================================================================
    // FUNCIÓN DE ORDENAMIENTO CORREGIDA
    // ====================================================================
    function ordenarProyectos(proyectosLista, criterio, direccion) {
        return [...proyectosLista].sort((a, b) => {
            let valorA, valorB;
            
            switch(criterio) {
                case 'fecha':
                    valorA = a.fecha ? new Date(a.fecha) : new Date(0);
                    valorB = b.fecha ? new Date(b.fecha) : new Date(0);
                    break;
                case 'nombre':
                    valorA = (a.nombre || '').toLowerCase();
                    valorB = (b.nombre || '').toLowerCase();
                    break;
                case 'rol':
                    valorA = (a.rol || '').toLowerCase();
                    valorB = (b.rol || '').toLowerCase();
                    break;
                default:
                    return 0;
            }
            
            if (direccion === 'asc') {
                if (valorA < valorB) return -1;
                if (valorA > valorB) return 1;
                return 0;
            } else {
                if (valorA > valorB) return -1;
                if (valorA < valorB) return 1;
                return 0;
            }
        });
    }

    // ====================================================================
    // VISIBILIDAD
    // ====================================================================
    window.toggleVisDropdown = function(event, id) {
        event.stopPropagation();
        document.querySelectorAll('.vis-mini-dropdown.open').forEach(d => { if (d.dataset.proyectoId != id) d.classList.remove('open'); });
        document.querySelector(`.vis-mini-dropdown[data-proyecto-id="${id}"]`)?.classList.toggle('open');
    };

    window.cambiarVisibilidad = async function(id, nv) {
        const p = proyectos.find(x => x.id === id);
        if (!p) return;
        try {
            const r = await fetch(`/proyectos/${id}/visibilidad`, { method: 'PATCH', headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken }, body: JSON.stringify({ is_visible: nv }) });
            if (!r.ok) throw new Error('Error');
            p.is_visible = nv;
            document.querySelectorAll('.vis-mini-dropdown.open').forEach(d => d.classList.remove('open'));
            renderizar();
            mostrarToast(nv ? 'Visible para todos' : 'Solo para mí');
        } catch (e) { mostrarToast('Error', 'error'); }
    };

    async function cambiarVisibilidadDirecta(id, nv) {
        const p = proyectos.find(x => x.id === id);
        if (!p) return;
        try {
            const r = await fetch(`/proyectos/${id}/visibilidad`, { method: 'PATCH', headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken }, body: JSON.stringify({ is_visible: nv }) });
            if (!r.ok) throw new Error('Error');
            p.is_visible = nv;
            mostrarToast(nv ? 'Visible para todos' : 'Solo para mí');
        } catch (e) { mostrarToast('Error', 'error'); }
    }

    document.addEventListener('click', (e) => { if (!e.target.closest('.vis-toggle-wrap')) document.querySelectorAll('.vis-mini-dropdown.open').forEach(d => d.classList.remove('open')); });

    // ====================================================================
    // FECHA FIN DINÁMICA
    // ====================================================================
    function actualizarEstadoFechaFin() {
        if (!inputFechaFin) return;
        const estado = selectEstado ? selectEstado.value : 'En curso';
        if (estado === 'En curso' || estado === 'En pausa') { inputFechaFin.disabled = true; inputFechaFin.value = ''; }
        else { inputFechaFin.disabled = false; }
    }
    if (selectEstado) { const obs = new MutationObserver(() => actualizarEstadoFechaFin()); obs.observe(selectEstado, { attributes: true, attributeFilter: ['value'] }); }

    // ====================================================================
    // VISTA PREVIA
    // ====================================================================
    async function abrirPreview(id) {
        let evs = [];
        try { const r = await fetch(`/proyectos/${id}/evidencias`, { headers: { 'Accept':'application/json' } }); if (r.ok) evs = await r.json(); } catch(e) {}
        const p = proyectos.find(x => x.id === id);
        if (!p) return;
        toggleGrid(true); toggleProyHeader(true);
        if (formCard) formCard.classList.remove('open');
        if (!previewPage) return;
        previewPage.innerHTML = '';
 
        let evHTML = '';
        if (evs.length > 0) { evHTML = `<hr class="preview-divider"><div class="preview-section-title">Evidencias (${evs.length})</div><div class="preview-evidencias-list">${evs.map(ev => {
            if (ev.imagen_path) return `<div class="preview-evidencia-card"><div class="preview-evidencia-header">${svgImagen} ${escapeHtml(ev.titulo||'Imagen')}</div><img src="/storage/${ev.imagen_path}" class="preview-evidencia-imagen" onerror="this.style.display='none'"></div>`;
            if (ev.enlace) return `<div class="preview-evidencia-card"><div class="preview-evidencia-header">${svgEnlace} ${escapeHtml(ev.titulo||'Enlace')}</div><a href="${escapeHtml(ev.enlace)}" target="_blank" class="preview-evidencia-enlace">${escapeHtml(ev.enlace)}</a></div>`;
            if (ev.repositorio) return `<div class="preview-evidencia-card"><div class="preview-evidencia-header">${svgRepo} ${escapeHtml(ev.titulo||'Repositorio')}</div><div class="preview-evidencia-repo">${escapeHtml(ev.repositorio)}</div></div>`;
            return '';
        }).join('')}</div>`; }
 
        previewPage.innerHTML = `<div class="preview-doc">
            <button class="preview-back" id="previewBackBtn">${svgVolver} Volver a proyectos</button>
            <h1 class="preview-doc-title">${escapeHtml(p.nombre)}</h1>
            <div class="preview-doc-meta"><span class="proy-badge ${getBadgeClass(p.estado)}">${p.estado}</span>${p.is_visible ? `<span class="publico-badge">${svgCandadoAbierto} Público</span>` : `<span class="privado-badge">${svgCandadoCerrado} Privado</span>`}${p.rol ? `<span class="rol-badge"><i class="fas fa-user-check"></i> ${escapeHtml(p.rol)}</span>` : ''}${p.cliente ? `<span class="cliente-badge"><i class="fas fa-building"></i> ${escapeHtml(p.cliente)}</span>` : ''}</div>
            <div class="preview-doc-dates"><span><i class="far fa-calendar-alt"></i> Inicio: ${p.fecha || '—'}</span><span><i class="far fa-calendar-check"></i> Fin: ${p.fecha_fin || '—'}</span></div>
            <hr class="preview-divider"><div class="preview-section-title">Descripción</div>
            <div class="preview-doc-desc" id="previewDescEl">${p.descripcion || ''}</div>
            <button class="ver-mas-preview" id="btnVerMasDesc" style="display:none">Ver más</button>
            ${(p.tecnologias||[]).length>0 ? `<hr class="preview-divider"><div class="preview-section-title">Stack Tecnológico</div><div class="preview-doc-tecs">${p.tecnologias.map(t=>`<span class="tec-mini">${getTecnologiaLogo(t)} ${escapeHtml(t)}</span>`).join('')}</div>` : ''}
            ${evHTML}
            <div class="preview-doc-actions">
                <button class="preview-btn preview-btn-outline" id="previewToggleVisBtn">${p.is_visible ? svgCandadoCerrado + ' Hacer privado' : svgCandadoAbierto + ' Hacer público'}</button>
                <button class="preview-btn preview-btn-outline" id="previewEditBtn2">${svgEditar} Editar</button>
                <button class="preview-btn preview-btn-outline" id="previewDeleteBtn" style="color:#ef4444;border-color:#fecaca;">${svgEliminar} Eliminar</button>
            </div></div>`;
 
        // ── Ver más / Ver menos DESPUÉS de insertar el HTML ──
        const descEl = previewPage.querySelector('#previewDescEl');
        const btnVerMas = previewPage.querySelector('#btnVerMasDesc');
        if (descEl && btnVerMas) {
            const textoCompleto = stripHtml(p.descripcion || '');
            if (textoCompleto.length > 200) {
                btnVerMas.style.display = 'inline-block';
                descEl.style.display = '-webkit-box';
                descEl.style.webkitLineClamp = '4';
                descEl.style.webkitBoxOrient = 'vertical';
                descEl.style.overflow = 'hidden';
                let expandido = false;
                btnVerMas.addEventListener('click', () => {
                    expandido = !expandido;
                    descEl.style.webkitLineClamp = expandido ? 'unset' : '4';
                    descEl.style.overflow = expandido ? 'visible' : 'hidden';
                    btnVerMas.textContent = expandido ? 'Ver menos' : 'Ver más';
                });
            }
        }
 
        previewPage.classList.add('open');
        previewPage.querySelector('#previewBackBtn')?.addEventListener('click', cerrarPreview);
        previewPage.querySelector('#previewEditBtn2')?.addEventListener('click', () => { cerrarPreview(); editarProyecto(id); });
        previewPage.querySelector('#previewDeleteBtn')?.addEventListener('click', () => { cerrarPreview(); eliminarProyectoBD(id); });
        previewPage.querySelector('#previewToggleVisBtn')?.addEventListener('click', async () => { await cambiarVisibilidadDirecta(id, !p.is_visible); abrirPreview(id); });
    }

    function cerrarPreview() { if (previewPage) { previewPage.classList.remove('open'); previewPage.innerHTML = ''; } toggleGrid(false); toggleProyHeader(false); cargarProyectos(); }

    // ====================================================================
    // DROPDOWNS FORM
    // ====================================================================
    function setupCustomDropdown(did,bid,mid,hid,tid){const d=document.getElementById(did),b=document.getElementById(bid),m=document.getElementById(mid),h=document.getElementById(hid),t=document.getElementById(tid);if(!d||!b||!m||!h)return;b.addEventListener('click',(e)=>{e.stopPropagation();const io=d.classList.contains('open');document.querySelectorAll('.custom-dropdown.open').forEach(x=>x.classList.remove('open'));document.querySelectorAll('.stack-dropdown.open').forEach(x=>x.classList.remove('open'));if(!io)d.classList.add('open');});m.querySelectorAll('li').forEach(li=>{li.addEventListener('click',(e)=>{e.stopPropagation();h.value=li.dataset.value;if(t)t.textContent=li.textContent;m.querySelectorAll('li').forEach(l=>l.classList.remove('selected'));li.classList.add('selected');d.classList.remove('open');});});}
    setupCustomDropdown('dropdownEstado','btnEstado','menuEstado','proyEstado','btnEstadoText');
    setupCustomDropdown('dropdownRol','btnRol','menuRol','proyRol','btnRolText');

    // ====================================================================
    // STACK TECNOLÓGICO
    // ====================================================================
    const stackDropdown=document.getElementById('stackDropdown'),stackInputWrapper=document.getElementById('stackInputWrapper'),stackSearch=document.getElementById('stackSearch'),stackMenu=document.getElementById('stackMenu'),stackList=document.getElementById('stackList'),tecBadgesContainer=document.getElementById('tecBadgesContainer');
    function renderStackOptions(filter=''){const disponibles=todasLasTecnologias.filter(t=>!tecnologiasActuales.includes(t));const filtered=filter.trim()===''?disponibles:disponibles.filter(t=>t.toLowerCase().includes(filter.toLowerCase()));if(filtered.length===0){stackList.innerHTML='<div class="stack-no-results">Sin resultados. Presiona Enter para agregar.</div>';return;}stackList.innerHTML=filtered.map(tech=>`<div class="stack-option" data-value="${escapeHtml(tech)}"><span class="check">✓</span>${tech}</div>`).join('');stackList.querySelectorAll('.stack-option').forEach(option=>{option.addEventListener('click',(e)=>{e.stopPropagation();if(tecnologiasActuales.length>=15){mostrarToast('Máximo 15 tecnologías','error');return;}tecnologiasActuales.push(option.dataset.value);renderTecnologiasBadges();renderStackOptions(stackSearch.value);stackSearch.value='';stackSearch.focus();});});}
    function renderTecnologiasBadges(){if(!tecBadgesContainer)return;if(tecCountBadge)tecCountBadge.textContent=tecnologiasActuales.length;if(tecnologiasActuales.length===0){tecBadgesContainer.innerHTML='<div class="tec-empty-state"><div class="tec-empty-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div><div class="tec-empty-text">Aún no hay tecnologías</div></div>';return;}tecBadgesContainer.innerHTML=tecnologiasActuales.map(tec=>`<span class="tec-badge"><span class="tec-badge-logo">${getTecnologiaLogo(tec)}</span><span>${escapeHtml(tec)}</span><span class="tec-badge-remove" data-tec="${escapeHtml(tec)}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></span>`).join('');document.querySelectorAll('.tec-badges-col .tec-badge-remove').forEach(btn=>{btn.addEventListener('click',(e)=>{e.stopPropagation();tecnologiasActuales=tecnologiasActuales.filter(t=>t!==btn.dataset.tec);renderTecnologiasBadges();renderStackOptions(stackSearch.value);});});}
    if(stackInputWrapper){stackInputWrapper.addEventListener('click',(e)=>{e.stopPropagation();const io=stackDropdown.classList.contains('open');document.querySelectorAll('.custom-dropdown.open').forEach(x=>x.classList.remove('open'));document.querySelectorAll('.stack-dropdown.open').forEach(x=>x.classList.remove('open'));if(!io){stackDropdown.classList.add('open');stackSearch.focus();renderStackOptions(stackSearch.value);}});}
    if(stackSearch){stackSearch.setAttribute('maxlength','20');stackSearch.addEventListener('input',(e)=>{if(!stackDropdown.classList.contains('open'))stackDropdown.classList.add('open');renderStackOptions(e.target.value);});stackSearch.addEventListener('click',(e)=>{e.stopPropagation();if(!stackDropdown.classList.contains('open')){stackDropdown.classList.add('open');renderStackOptions(stackSearch.value);}});stackSearch.addEventListener('keypress',(e)=>{if(e.key==='Enter'){e.preventDefault();const v=stackSearch.value.trim();if(v&&!tecnologiasActuales.includes(v)){if(tecnologiasActuales.length>=15){mostrarToast('Máximo 15 tecnologías','error');return;}tecnologiasActuales.push(v);renderTecnologiasBadges();renderStackOptions('');stackSearch.value='';}}});}

    // ====================================================================
    // API CALLS
    // ====================================================================
    async function cargarProyectos(){try{mostrarLoading(true);const r=await fetch('/proyectos',{method:'GET',headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});if(!r.ok)throw new Error('Error');proyectos=await r.json();poblarSelectsDinamicos();restaurarFiltrosVisuales();renderizar();}catch(e){mostrarToast('Error al cargar proyectos','error');}finally{mostrarLoading(false);}}

    async function guardarProyectoBD(){
        if (quill) {
            const textoActual = quill.getText();
            if (textoActual.length > LIMITE_CARACTERES_DESC) {
                mostrarToast(`La descripción excede el límite de ${LIMITE_CARACTERES_DESC} caracteres`, 'error');
                return;
            }
            inputDesc.value = quill.root.innerHTML;
        }
        
        const nombre=inputNombre.value.trim();
        const descripcion=inputDesc.value;
        const fecha=inputFecha.value;
        const fechaFin=inputFechaFin&&!inputFechaFin.disabled?inputFechaFin.value:null;
        const estado=selectEstado?selectEstado.value:'En curso';
        const rol=selectRol?selectRol.value:'';
        const cliente=inputCliente?inputCliente.value.trim().substring(0,60):'';
        const visibilidad='publico';
        const tecnologias=[...tecnologiasActuales];
        
        let isValid=true;
        if(!nombre){inputNombre.classList.add('proy-err');document.getElementById('proyErrNombre').classList.add('visible');isValid=false;}else{inputNombre.classList.remove('proy-err');document.getElementById('proyErrNombre').classList.remove('visible');}
        const descText=quill?quill.getText().trim():descripcion;
        if(!descText){document.getElementById('quillEditor').style.border='1.5px solid #ef4444';document.getElementById('proyErrDesc').classList.add('visible');isValid=false;}else{document.getElementById('quillEditor').style.border='';document.getElementById('proyErrDesc').classList.remove('visible');}
        if(fecha&&fechaFin&&new Date(fechaFin)<new Date(fecha)){mostrarToast('La fecha de fin no puede ser menor a la fecha de inicio','error');isValid=false;}
        if(estado==='Completado'&&!fechaFin){mostrarToast('Un proyecto completado debe tener fecha de fin','error');isValid=false;}
        if(!isValid)return;
        
        const proyectoData={nombre,descripcion,fecha:fecha||null,fecha_fin:fechaFin||null,estado,rol,cliente,visibilidad,tecnologias};
        try{mostrarLoading(true);const url=editandoId?`/proyectos/${editandoId}`:'/proyectos';const method=editandoId?'PUT':'POST';const r=await fetch(url,{method,headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken,'X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(proyectoData)});if(!r.ok){const err=await r.json();throw new Error(err.message||'Error');}
       const resultado=await r.json();
resultado.tiene_evidencias = false;
window._proyectoParaEvidencias=resultado;
sessionStorage.setItem('ultimo_proyecto_activo',JSON.stringify(resultado));
if(typeof window.evInit==='function'){window.evInit(resultado);}
if(!editandoId&&typeof window.evSubirPendientes==='function'){await window.evSubirPendientes(resultado.id);} 
        mostrarToast(editandoId?'Proyecto actualizado':'Proyecto creado');ocultarForm();await cargarProyectos();}catch(e){mostrarToast(e.message||'Error al guardar','error');}finally{mostrarLoading(false);}
    }

    async function eliminarProyectoBD(id){const p=proyectos.find(x=>x.id===id);if(!confirm(`¿Eliminar "${p.nombre}"?`))return;try{mostrarLoading(true);const r=await fetch(`/proyectos/${id}`,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':csrfToken,'X-Requested-With':'XMLHttpRequest'}});if(!r.ok)throw new Error('Error');mostrarToast('Proyecto eliminado');await cargarProyectos();if(editandoId===id)ocultarForm();}catch(e){mostrarToast('Error al eliminar','error');}finally{mostrarLoading(false);}}

    // ====================================================================
    // FILTROS DINÁMICOS
    // ====================================================================
    function poblarSelectsDinamicos(){
        const rolesUnicos=[...new Set(proyectos.map(p=>p.rol).filter(r=>r&&r.trim()!==''))];
        const rolFilterSection = document.getElementById('rolFilterSection');
        if(rolFilterSection){
            rolFilterSection.innerHTML=`<div class="ft-dd-label">Mi rol</div><div class="ft-dd-item ${filtroRolActual==='todos'?'selected':''}" data-filtro="rol" data-val="todos"><span class="ft-dot"></span>Todos los roles</div>${rolesUnicos.map(rol=>`<div class="ft-dd-item ${filtroRolActual===rol?'selected':''}" data-filtro="rol" data-val="${escapeHtml(rol)}"><span class="ft-dot"></span>${escapeHtml(rol)}</div>`).join('')}`;
            rolFilterSection.querySelectorAll('.ft-dd-item').forEach(item=>{item.addEventListener('click',(e)=>{e.stopPropagation();rolFilterSection.querySelectorAll('.ft-dd-item').forEach(i=>i.classList.remove('selected'));item.classList.add('selected');filtroRolActual=item.dataset.val;guardarFiltros();renderizar();const dd=document.getElementById('ddFiltrar');if(dd)dd.classList.remove('open');const btn=document.getElementById('btnFiltrar');if(btn)btn.classList.remove('open');});});
        }
        let proyectosFiltrados=[...proyectos];
        if(filtroVisibilidadActual==='publico')proyectosFiltrados=proyectos.filter(p=>p.is_visible===true);
        else if(filtroVisibilidadActual==='privado')proyectosFiltrados=proyectos.filter(p=>p.is_visible===false);
        const tecnologiasUnicas=[...new Set(proyectosFiltrados.flatMap(p=>p.tecnologias||[]))];
        const tecnologiaFilterSection = document.getElementById('tecnologiaFilterSection');
        if(tecnologiaFilterSection){
            tecnologiaFilterSection.innerHTML=`<div class="ft-dd-item ${filtroTecnologiaActual==='todas'?'selected':''}" data-filtro="tecnologia" data-val="todas"><span class="ft-dot"></span>Todas</div>${tecnologiasUnicas.map(tec=>`<div class="ft-dd-item ${filtroTecnologiaActual===tec?'selected':''}" data-filtro="tecnologia" data-val="${escapeHtml(tec)}"><span class="ft-dot"></span>${escapeHtml(tec)}</div>`).join('')}`;
            tecnologiaFilterSection.querySelectorAll('.ft-dd-item').forEach(item=>{item.addEventListener('click',(e)=>{e.stopPropagation();tecnologiaFilterSection.querySelectorAll('.ft-dd-item').forEach(i=>i.classList.remove('selected'));item.classList.add('selected');filtroTecnologiaActual=item.dataset.val;guardarFiltros();renderizar();const dd=document.getElementById('ddTecnologia');if(dd)dd.classList.remove('open');const btn=document.getElementById('btnTecnologia');if(btn)btn.classList.remove('open');});});
        }
    }

    function filtrarYOrdenarProyectos(){
        if(!proyectos.length)return[];
        let resultados=[...proyectos];
        if(textoBusqueda.trim()!==''){const bl=textoBusqueda.toLowerCase();resultados=resultados.filter(p=>(p.nombre&&p.nombre.toLowerCase().includes(bl))||(p.rol&&p.rol.toLowerCase().includes(bl))||(p.cliente&&p.cliente.toLowerCase().includes(bl))||(p.tecnologias&&p.tecnologias.some(t=>t.toLowerCase().includes(bl))));}
        if(filtroEstadoActual!=='todos')resultados=resultados.filter(p=>p.estado===filtroEstadoActual);
        if(filtroRolActual!=='todos')resultados=resultados.filter(p=>p.rol===filtroRolActual);
        if(filtroTecnologiaActual!=='todas')resultados=resultados.filter(p=>p.tecnologias&&p.tecnologias.includes(filtroTecnologiaActual));
        if(filtroVisibilidadActual!=='todos')resultados=resultados.filter(p=>p.is_visible===(filtroVisibilidadActual==='publico'));
        
        let criterio = 'fecha';
        let direccion = 'desc';
        
        if (ordenActual === 'fecha_desc') { criterio = 'fecha'; direccion = 'desc'; }
        else if (ordenActual === 'fecha_asc') { criterio = 'fecha'; direccion = 'asc'; }
        else if (ordenActual === 'nombre_asc') { criterio = 'nombre'; direccion = 'asc'; }
        else if (ordenActual === 'nombre_desc') { criterio = 'nombre'; direccion = 'desc'; }
        else if (ordenActual === 'rol_asc') { criterio = 'rol'; direccion = 'asc'; }
        else if (ordenActual === 'rol_desc') { criterio = 'rol'; direccion = 'desc'; }
        
        resultados = ordenarProyectos(resultados, criterio, direccion);
        
        if(conteoMostradosSpan)conteoMostradosSpan.textContent=resultados.length;
        if(conteoTotalSpan)conteoTotalSpan.textContent=proyectos.length;
        return resultados;
    }

    function restaurarFiltrosVisuales() {
        const ddOrdenar = document.getElementById('ddOrdenar');
        if (ddOrdenar) {
            const secciones = ddOrdenar.querySelectorAll('.ft-dd-section');
            let base = 'fecha_desc', dir = 'desc';
            if (ordenActual.includes('fecha')) base = 'fecha_desc';
            else if (ordenActual.includes('nombre')) base = 'nombre_asc';
            else if (ordenActual.includes('rol')) base = 'rol_asc';
            dir = ordenActual.includes('desc') ? 'desc' : 'asc';
            if(secciones[0]) secciones[0].querySelectorAll('.ft-dd-item').forEach(i => { i.classList.toggle('selected', i.dataset.val === base); });
            if(secciones[1]) secciones[1].querySelectorAll('.ft-dd-item').forEach(i => { i.classList.toggle('selected', i.dataset.val === dir); });
        }
        const ddFiltrar = document.getElementById('ddFiltrar');
        if (ddFiltrar) {
            ddFiltrar.querySelectorAll('.ft-dd-item').forEach(i => {
                if (i.dataset.filtro === 'estado') i.classList.toggle('selected', i.dataset.val === filtroEstadoActual);
                if (i.dataset.filtro === 'visibilidad') i.classList.toggle('selected', i.dataset.val === filtroVisibilidadActual);
            });
        }
    }

    // ====================================================================
    // UI
    // ====================================================================
    function limpiarForm(){
        inputNombre.value='';if(quill)quill.setText('');if(inputDesc)inputDesc.value='';inputFecha.value='';
        if(inputFechaFin){inputFechaFin.value='';inputFechaFin.disabled=true;}
        if(selectEstado)selectEstado.value='En curso';document.getElementById('btnEstadoText').textContent='En curso';
        document.querySelectorAll('#menuEstado li').forEach(l=>l.classList.remove('selected'));document.querySelector('#menuEstado li[data-value="En curso"]')?.classList.add('selected');
        if(selectRol)selectRol.value='';document.getElementById('btnRolText').textContent='— Seleccionar rol —';
        document.querySelectorAll('#menuRol li').forEach(l=>l.classList.remove('selected'));document.querySelector('#menuRol li[data-value=""]')?.classList.add('selected');
        if(inputCliente)inputCliente.value='';editandoId=null;formTitle.innerHTML=' Nuevo Proyecto';tecnologiasActuales=[];renderTecnologiasBadges();
        if(stackSearch)stackSearch.value='';actualizarContadorCaracteres();actualizarEstadoFechaFin();
        inputNombre.classList.remove('proy-err');document.getElementById('quillEditor').style.border='';
        document.getElementById('proyErrNombre').classList.remove('visible');document.getElementById('proyErrDesc').classList.remove('visible');
    }

    function ocultarForm(){formCard.classList.remove('open');limpiarForm();toggleProyHeader(false);toggleGrid(false);if(previewPage){previewPage.classList.remove('open');previewPage.innerHTML='';}if(buscadorInput)buscadorInput.value=textoBusqueda;restaurarFiltrosVisuales();}

    function mostrarForm(){toggleProyHeader(true);toggleGrid(true);formCard.classList.add('open');inputNombre.focus();if(previewPage){previewPage.classList.remove('open');previewPage.innerHTML='';}actualizarEstadoFechaFin();}

    function renderizar(){
        if(!grid)return;
        const proyectosFiltrados=filtrarYOrdenarProyectos();
        if(proyectosFiltrados.length===0){grid.innerHTML=`<div class="proy-empty"><div class="proy-empty-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#c8cdd8" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><h3>No se encontraron proyectos</h3><p>No hay proyectos que coincidan con los filtros seleccionados.</p><button class="proy-empty-btn" id="limpiarFiltrosEmptyBtn"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg> Limpiar filtros</button></div>`;document.getElementById('limpiarFiltrosEmptyBtn')?.addEventListener('click',resetearFiltros);return;}
        grid.innerHTML='';
        proyectosFiltrados.forEach(p=>{
            const card=document.createElement('div');card.className='proy-card';card.id='proyecto-'+p.id;
            const vb=p.is_visible?`<span class="publico-badge">${svgCandadoAbierto} Público</span>`:`<span class="privado-badge">${svgCandadoCerrado} Privado</span>`;
            let rc='';if(p.rol||p.cliente){rc='<div class="proy-card-rol-cliente">';if(p.rol)rc+=`<span class="rol-badge"><i class="fas fa-user-check"></i> ${escapeHtml(p.rol)}</span>`;if(p.cliente)rc+=`<span class="cliente-badge"><i class="fas fa-building"></i> ${escapeHtml(p.cliente)}</span>`;rc+='</div>';}
            const textoLimpio = stripHtml(p.descripcion);
            const descripcionPreview = textoLimpio.length > 150
                ? textoLimpio.substring(0, 150) + '...'
                : textoLimpio;
            card.innerHTML=`<div class="proy-card-band"></div><div class="proy-card-top"><div class="proy-card-nombre">${escapeHtml(p.nombre)} ${vb}</div><div class="proy-card-actions"><div class="vis-toggle-wrap"><button class="proy-icon-btn btn-toggle-vis" data-id="${p.id}" onclick="toggleVisDropdown(event, ${p.id})">${p.is_visible?svgCandadoAbierto:svgCandadoCerrado}</button><div class="vis-mini-dropdown" data-proyecto-id="${p.id}"><div class="vis-mini-option ${p.is_visible?'active':''}" onclick="cambiarVisibilidad(${p.id},true)"><span class="vis-icon">${svgCandadoAbierto}</span><span>Visible para todos</span><span class="vis-check">✓</span></div><div class="vis-mini-option ${!p.is_visible?'active':''}" onclick="cambiarVisibilidad(${p.id},false)"><span class="vis-icon">${svgCandadoCerrado}</span><span>Solo para mí</span><span class="vis-check">✓</span></div></div></div><button class="proy-icon-btn btn-editar" data-id="${p.id}">${svgEditar}</button><button class="proy-icon-btn btn-eliminar" data-id="${p.id}">${svgEliminar}</button></div></div><div class="proy-card-body">${rc}<div class="proy-card-desc-preview">${descripcionPreview}</div><div class="proy-card-tec">${(p.tecnologias||[]).slice(0,4).map(t=>`<span class="tec-mini">${getTecnologiaLogo(t)} ${escapeHtml(t)}</span>`).join('')}${(p.tecnologias||[]).length>4?`<span class="tec-mini">+${p.tecnologias.length-4}</span>`:''}</div><div class="proy-card-footer"><span class="proy-card-fecha"><i class="far fa-calendar-alt"></i> ${p.fecha||'Sin fecha'}</span><span class="proy-badge ${getBadgeClass(p.estado)}">${p.estado}</span></div></div>`;
            grid.appendChild(card);
            card.addEventListener('click',(e)=>{if(e.target.closest('button')||e.target.closest('.vis-mini-dropdown'))return;abrirPreview(p.id);});
        });
        document.querySelectorAll('.btn-editar').forEach(b=>b.addEventListener('click',(e)=>{e.stopPropagation();editarProyecto(parseInt(b.dataset.id));}));
        document.querySelectorAll('.btn-eliminar').forEach(b=>b.addEventListener('click',(e)=>{e.stopPropagation();eliminarProyectoBD(parseInt(b.dataset.id));}));
    }

    function editarProyecto(id){const p=proyectos.find(x=>x.id===id);if(!p)return;window._proyectoParaEvidencias=p;sessionStorage.setItem('ultimo_proyecto_activo',JSON.stringify(p));editandoId=id;inputNombre.value=p.nombre;if(quill)quill.root.innerHTML=p.descripcion||'';if(inputDesc)inputDesc.value=p.descripcion||'';inputFecha.value=p.fecha||'';if(inputFechaFin)inputFechaFin.value=p.fecha_fin||'';if(selectEstado)selectEstado.value=p.estado;document.getElementById('btnEstadoText').textContent=p.estado;document.querySelectorAll('#menuEstado li').forEach(l=>l.classList.remove('selected'));document.querySelector(`#menuEstado li[data-value="${p.estado}"]`)?.classList.add('selected');if(selectRol)selectRol.value=p.rol||'';document.getElementById('btnRolText').textContent=p.rol||'— Seleccionar rol —';document.querySelectorAll('#menuRol li').forEach(l=>l.classList.remove('selected'));if(p.rol)document.querySelector(`#menuRol li[data-value="${p.rol}"]`)?.classList.add('selected');else document.querySelector('#menuRol li[data-value=""]')?.classList.add('selected');if(inputCliente)inputCliente.value=p.cliente||'';tecnologiasActuales=[...(p.tecnologias||[])];renderTecnologiasBadges();if(stackSearch)stackSearch.value='';actualizarContadorCaracteres();actualizarEstadoFechaFin();formTitle.innerHTML='Editar Proyecto';mostrarForm();setTimeout(()=>{
       if(typeof window.evInit==='function'){window.evInit({...p, tiene_evidencias: true});}
        const evInlineSection=document.getElementById('evInlineSection');if(evInlineSection&&!evInlineSection.classList.contains('open')){evInlineSection.classList.add('open');}},500);}

    function resetearFiltros(){textoBusqueda='';filtroEstadoActual='todos';filtroRolActual='todos';filtroTecnologiaActual='todas';filtroVisibilidadActual='todos';ordenActual='fecha_desc';guardarFiltros();if(buscadorInput){buscadorInput.value='';if(limpiarBuscadorBtn)limpiarBuscadorBtn.style.display='none';}['ddFiltrar','ddOrdenar','ddTecnologia'].forEach(ddId=>{const dd=document.getElementById(ddId);if(dd)dd.querySelectorAll('.ft-dd-item').forEach(i=>i.classList.remove('selected'));});document.querySelector('#ddFiltrar .ft-dd-item[data-val="todos"]')?.classList.add('selected');document.querySelector('#ddOrdenar .ft-dd-item[data-val="fecha_desc"]')?.classList.add('selected');document.querySelector('#ddTecnologia .ft-dd-item[data-val="todas"]')?.classList.add('selected');poblarSelectsDinamicos();renderizar();mostrarToast('Filtros limpiados');}

    // ====================================================================
    // EVIDENCIAS
    // ====================================================================
    function abrirEvidencias(proyecto){if(!proyecto||!proyecto.id){mostrarToast('Error: Proyecto no válido','error');return;}toggleProyHeader(true);toggleGrid(true);window._proyectoParaEvidencias=proyecto;sessionStorage.setItem('ultimo_proyecto_activo',JSON.stringify(proyecto));if(formCard)formCard.classList.remove('open');
    if(typeof window.evInit==='function'){window.evInit({...proyecto, tiene_evidencias: true});}
    else{mostrarToast('Error al cargar evidencias','error');}}
    if(btnEvidencias){btnEvidencias.addEventListener('click',()=>{if(editandoId){const pa=proyectos.find(p=>p.id===editandoId);if(pa){window._proyectoParaEvidencias=pa;sessionStorage.setItem('ultimo_proyecto_activo',JSON.stringify(pa));if(typeof window.evInit==='function'){window.evInit({...pa, tiene_evidencias: true});}const evInlineSection=document.getElementById('evInlineSection');if(evInlineSection){evInlineSection.classList.add('open');}}return;}const evInlineSection=document.getElementById('evInlineSection');if(!evInlineSection){mostrarToast('Panel de evidencias no encontrado','error');return;}evInlineSection.classList.toggle('open');if(evInlineSection.classList.contains('open')){if(typeof window.evResetearPendientes==='function'){window.evResetearPendientes();}}const arrow=btnEvidencias.querySelector('.ev-trigger-arrow');if(arrow){arrow.style.transform=evInlineSection.classList.contains('open')?'rotate(90deg)':'';}});}
    document.addEventListener('ev:volver',()=>{toggleProyHeader(false);toggleGrid(false);cargarProyectos();});

    // ====================================================================
    // DROPDOWNS TOOLBAR
    // ====================================================================
    function setupDropdown(btnId,ddId){const btn=document.getElementById(btnId),dd=document.getElementById(ddId);if(!btn||!dd)return;btn.addEventListener('click',(e)=>{e.stopPropagation();const io=dd.classList.contains('open');document.querySelectorAll('.ft-dropdown').forEach(d=>d.classList.remove('open'));document.querySelectorAll('.ft-btn-sm').forEach(b=>b.classList.remove('open'));if(!io){dd.classList.add('open');btn.classList.add('open');}});dd.querySelectorAll('.ft-dd-item').forEach(item=>{item.addEventListener('click',(e)=>{e.stopPropagation();const filtro=item.dataset.filtro,val=item.dataset.val,section=item.closest('.ft-dd-section');if(section)section.querySelectorAll('.ft-dd-item').forEach(i=>i.classList.remove('selected'));item.classList.add('selected');if(filtro==='estado'){filtroEstadoActual=val;guardarFiltros();}if(filtro==='visibilidad'){filtroVisibilidadActual=val;guardarFiltros();poblarSelectsDinamicos();}if(filtro==='rol'){filtroRolActual=val;guardarFiltros();}if(filtro==='tecnologia'){filtroTecnologiaActual=val;guardarFiltros();}if(filtro==='ordenar'||filtro==='direccion'){const secOrden=document.querySelector('#ddOrdenar .ft-dd-section:first-child .ft-dd-item.selected');const secDir=document.querySelector('#ddOrdenar .ft-dd-section:last-child .ft-dd-item.selected');const base=secOrden?.dataset.val||'fecha_desc';const dir=secDir?.dataset.val||'desc';if(base==='fecha_desc')ordenActual=dir==='asc'?'fecha_asc':'fecha_desc';if(base==='nombre_asc')ordenActual=dir==='asc'?'nombre_asc':'nombre_desc';if(base==='rol_asc')ordenActual=dir==='asc'?'rol_asc':'rol_desc';guardarFiltros();restaurarFiltrosVisuales();}renderizar();dd.classList.remove('open');btn.classList.remove('open');});});}
    setupDropdown('btnOrdenar','ddOrdenar');setupDropdown('btnFiltrar','ddFiltrar');setupDropdown('btnTecnologia','ddTecnologia');
    document.addEventListener('click',()=>{document.querySelectorAll('.ft-dropdown').forEach(d=>d.classList.remove('open'));document.querySelectorAll('.ft-btn-sm').forEach(b=>b.classList.remove('open'));document.querySelectorAll('.custom-dropdown.open').forEach(d=>d.classList.remove('open'));document.querySelectorAll('.stack-dropdown.open').forEach(d=>d.classList.remove('open'));});

    // ====================================================================
    // BUSCADOR
    // ====================================================================
    if(buscadorInput){buscadorInput.setAttribute('maxlength','50');buscadorInput.value=textoBusqueda;if(textoBusqueda&&limpiarBuscadorBtn)limpiarBuscadorBtn.style.display='block';buscadorInput.addEventListener('input',(e)=>{textoBusqueda=e.target.value;guardarFiltros();if(limpiarBuscadorBtn)limpiarBuscadorBtn.style.display=textoBusqueda?'block':'none';renderizar();});}
    if(limpiarBuscadorBtn){limpiarBuscadorBtn.addEventListener('click',()=>{textoBusqueda='';buscadorInput.value='';guardarFiltros();limpiarBuscadorBtn.style.display='none';renderizar();});}
    if(limpiarFiltrosBtn)limpiarFiltrosBtn.addEventListener('click',resetearFiltros);

    // ====================================================================
    // EVENTOS PRINCIPALES
    // ====================================================================
    if(btnMostrarForm)btnMostrarForm.addEventListener('click',()=>{limpiarForm();mostrarForm();if(typeof window.evResetearPendientes==='function')window.evResetearPendientes();});
    if(btnCancelarForm)btnCancelarForm.addEventListener('click',ocultarForm);
    if(btnGuardarForm)btnGuardarForm.addEventListener('click',guardarProyectoBD);
    if(inputCliente)inputCliente.setAttribute('maxlength','60');

    // ====================================================================
    // INICIALIZACIÓN
    // ====================================================================
    toggleProyHeader(false);toggleGrid(false);cargarProyectos();
})();