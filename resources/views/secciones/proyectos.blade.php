{{-- resources/views/secciones/proyectos.blade.php --}}
{{-- HU-10: Gestionar mis proyectos (T1–T11) --}}

<style>
    /* ── Variables de color del sistema ── */
    :root {
        --burg-deep: #2d0a1e;
        --teal:      #0abf9e;
        --teal-dim:  #07866e;
        --teal-light:#1de8c0;
    }

    /* ══════════════════════════════════════
       HEADER
    ══════════════════════════════════════ */
    .proy-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 32px;
    }

    .proy-title-wrap h2 {
        font-size: 26px;
        font-weight: 800;
        color: var(--burg-deep);
        letter-spacing: -.5px;
        position: relative;
        display: inline-block;
        margin-bottom: 6px;
    }

    .proy-title-wrap h2::after {
        content: '';
        position: absolute;
        bottom: -6px; left: 0;
        width: 48px; height: 3px;
        background: linear-gradient(90deg, var(--teal), var(--teal-light));
        border-radius: 3px;
    }

    .proy-title-wrap p {
        font-size: 13px;
        color: #9ca3af;
        margin-top: 14px;
    }

    .proy-btn-nuevo {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 40px;
        border: none;
        background: linear-gradient(135deg, var(--teal), var(--teal-dim));
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: transform .2s, box-shadow .2s;
    }

    .proy-btn-nuevo:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(10,191,158,.45);
    }

    /* ══════════════════════════════════════
       FORMULARIO ACOPLADO (Nuevo/Editar)
    ══════════════════════════════════════ */
    .proy-form-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 32px;
        display: none;
    }

    .proy-form-card.open {
        display: block;
        animation: fadeIn .3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .proy-form-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--burg-deep);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--teal);
        display: inline-block;
    }

    .proy-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .proy-field-full {
        grid-column: span 2;
    }

    .proy-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .proy-field label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .proy-field label span {
        color: var(--teal);
    }

    .proy-inp, .proy-textarea, .proy-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 14px;
        font-family: inherit;
        background: white;
        width: 100%;
        transition: all .2s;
    }

    .proy-inp:focus, .proy-textarea:focus, .proy-select:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 3px rgba(10,191,158,.1);
    }

    .proy-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .proy-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .proy-btn-cancel {
        padding: 8px 24px;
        border-radius: 40px;
        border: 1.5px solid #e2e8f0;
        background: white;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }

    .proy-btn-cancel:hover {
        background: #f1f5f9;
    }

    .proy-btn-save {
        padding: 8px 28px;
        border-radius: 40px;
        border: none;
        background: linear-gradient(135deg, var(--teal), var(--teal-dim));
        color: white;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
    }

    .proy-btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(10,191,158,.4);
    }

    .proy-err-msg {
        font-size: 11px;
        color: #ef4444;
        display: none;
    }

    .proy-err-msg.visible {
        display: block;
    }

    .proy-inp.proy-err, .proy-textarea.proy-err {
        border-color: #ef4444;
    }

    /* ══════════════════════════════════════
       TARJETAS DE PROYECTOS
    ══════════════════════════════════════ */
    .proy-grid {
        display: grid;
        gap: 20px;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }

    .proy-card {
        background: white;
        border: 1px solid #edf0f4;
        border-radius: 18px;
        overflow: hidden;
        transition: all .2s;
    }

    .proy-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,.08);
    }

    .proy-card-band {
        height: 4px;
        background: linear-gradient(90deg, var(--burg-deep), var(--teal));
    }

    .proy-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 16px 16px 0;
    }

    .proy-card-nombre {
        font-size: 16px;
        font-weight: 700;
        color: var(--burg-deep);
    }

    .proy-card-actions {
        display: flex;
        gap: 4px;
    }

    .proy-icon-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 6px;
        border-radius: 8px;
        transition: background .2s;
    }

    .proy-icon-btn:hover {
        background: #f1f5f9;
    }

    .proy-card-body {
        padding: 12px 16px 16px;
    }

    .proy-card-desc {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .proy-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
    }

    .proy-card-fecha {
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .proy-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    .proy-badge-completado { background: #dcfce7; color: #166534; }
    .proy-badge-en-curso   { background: #fef9c3; color: #854d0e; }
    .proy-badge-en-pausa   { background: #f1f5f9; color: #475569; }

    /* ESTADO VACÍO */
    .proy-empty {
        text-align: center;
        padding: 60px 20px;
        background: #f8fafc;
        border-radius: 20px;
        border: 2px dashed #e2e8f0;
    }

    .proy-empty-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    .proy-empty h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--burg-deep);
        margin-bottom: 8px;
    }

    .proy-empty p {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 20px;
    }

    .proy-empty-btn {
        background: linear-gradient(135deg, var(--teal), var(--teal-dim));
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 40px;
        font-weight: 700;
        cursor: pointer;
    }

    /* TOASTS */
    .proy-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 500;
        z-index: 9999;
        animation: toastIn .3s ease forwards;
    }

    @keyframes toastIn {
        from { opacity: 0; transform: translateX(100px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @media (max-width: 640px) {
        .proy-form-grid {
            grid-template-columns: 1fr;
        }
        .proy-field-full {
            grid-column: span 1;
        }
    }
</style>

{{-- HEADER --}}
<div class="proy-header">
    <div class="proy-title-wrap">
        <h2>Mis Proyectos</h2>
        <p>Gestiona y organiza todos tus proyectos profesionales</p>
    </div>
    <button class="proy-btn-nuevo" id="proyBtnMostrarForm">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Nuevo Proyecto
    </button>
</div>

{{-- FORMULARIO ACOPLADO (se muestra al hacer clic) --}}
<div class="proy-form-card" id="proyFormCard">
    <div class="proy-form-title" id="proyFormTitle">➕ Nuevo Proyecto</div>
    
    <div class="proy-form-grid">
        <div class="proy-field-full">
            <label>Nombre del proyecto <span>*</span></label>
            <input type="text" id="proyNombre" class="proy-inp" placeholder="Ej: Portafolio Web Personal" maxlength="100">
            <span class="proy-err-msg" id="proyErrNombre">El nombre es obligatorio</span>
        </div>
        
        <div class="proy-field-full">
            <label>Descripción <span>*</span></label>
            <textarea id="proyDesc" class="proy-textarea" placeholder="Describe brevemente el proyecto..." maxlength="500"></textarea>
            <span class="proy-err-msg" id="proyErrDesc">La descripción es obligatoria</span>
        </div>
        
        <div class="proy-field">
            <label>Fecha</label>
            <input type="date" id="proyFecha" class="proy-inp">
        </div>
        
        <div class="proy-field">
            <label>Estado</label>
            <select id="proyEstado" class="proy-select">
                <option value="En curso">🟡 En curso</option>
                <option value="Completado">🟢 Completado</option>
                <option value="En pausa">⚪ En pausa</option>
            </select>
        </div>
    </div>
    
    <div class="proy-form-actions">
        <button class="proy-btn-cancel" id="proyBtnCancelarForm">Cancelar</button>
        <button class="proy-btn-save" id="proyBtnGuardarForm">Guardar proyecto</button>
    </div>
</div>

{{-- GRID DE PROYECTOS --}}
<div class="proy-grid" id="proyGrid"></div>

<script>
(function() {
    const STORAGE_KEY = 'portafolio_proyectos';
    let proyectos = [];
    let editandoId = null;

    // Elementos DOM
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

    function cargar() {
        try {
            proyectos = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        } catch(e) { proyectos = []; }
        renderizar();
    }

    function guardar() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(proyectos));
    }

    function limpiarForm() {
        inputNombre.value = '';
        inputDesc.value = '';
        inputFecha.value = '';
        selectEstado.value = 'En curso';
        editandoId = null;
        formTitle.innerHTML = '➕ Nuevo Proyecto';
        // Limpiar errores
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

    function renderizar() {
        if (!grid) return;
        
        if (proyectos.length === 0) {
            grid.innerHTML = `
                <div class="proy-empty">
                    <div class="proy-empty-icon">📂</div>
                    <h3>Aún no tienes proyectos</h3>
                    <p>Crea tu primer proyecto y empieza a construir tu portafolio profesional.</p>
                    <button class="proy-empty-btn" id="proyEmptyBtn">+ Crear primer proyecto</button>
                </div>
            `;
            const emptyBtn = document.getElementById('proyEmptyBtn');
            if (emptyBtn) emptyBtn.addEventListener('click', () => { mostrarForm(); });
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
                        <button class="proy-icon-btn btn-editar" data-id="${p.id}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0abf9e" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button class="proy-icon-btn btn-eliminar" data-id="${p.id}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v6"/><path d="M14 11v6"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="proy-card-body">
                    <div class="proy-card-desc">${escapeHtml(p.descripcion)}</div>
                    <div class="proy-card-footer">
                        <span class="proy-card-fecha">📅 ${p.fecha || 'Sin fecha'}</span>
                        <span class="proy-badge ${getBadgeClass(p.estado)}">${p.estado}</span>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });

        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', () => editarProyecto(parseInt(btn.dataset.id)));
        });
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', () => eliminarProyecto(parseInt(btn.dataset.id)));
        });
    }

    function getBadgeClass(estado) {
        if (estado === 'Completado') return 'proy-badge-completado';
        if (estado === 'En curso') return 'proy-badge-en-curso';
        return 'proy-badge-en-pausa';
    }

    function editarProyecto(id) {
        const proyecto = proyectos.find(p => p.id === id);
        if (!proyecto) return;
        editandoId = id;
        inputNombre.value = proyecto.nombre;
        inputDesc.value = proyecto.descripcion;
        inputFecha.value = proyecto.fecha || '';
        selectEstado.value = proyecto.estado;
        formTitle.innerHTML = '✏️ Editar Proyecto';
        mostrarForm();
    }

    function eliminarProyecto(id) {
        const proyecto = proyectos.find(p => p.id === id);
        if (confirm(`¿Eliminar "${proyecto.nombre}"? Esta acción no se puede deshacer.`)) {
            proyectos = proyectos.filter(p => p.id !== id);
            guardar();
            renderizar();
            if (editandoId === id) ocultarForm();
            mostrarToast('Proyecto eliminado', 'success');
        }
    }

    function guardarProyecto() {
        const nombre = inputNombre.value.trim();
        const descripcion = inputDesc.value.trim();
        const fecha = inputFecha.value;
        const estado = selectEstado.value;
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

        if (editandoId) {
            const index = proyectos.findIndex(p => p.id === editandoId);
            proyectos[index] = { ...proyectos[index], nombre, descripcion, fecha, estado };
            mostrarToast('Proyecto actualizado', 'success');
        } else {
            proyectos.push({ id: Date.now(), nombre, descripcion, fecha, estado });
            mostrarToast('Proyecto creado', 'success');
        }

        guardar();
        ocultarForm();
        renderizar();
    }

    function mostrarToast(mensaje, tipo) {
        const toast = document.createElement('div');
        toast.className = 'proy-toast';
        toast.innerHTML = `✅ ${mensaje}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // Eventos
    btnMostrarForm.addEventListener('click', () => { limpiarForm(); mostrarForm(); });
    btnCancelarForm.addEventListener('click', ocultarForm);
    btnGuardarForm.addEventListener('click', guardarProyecto);

    // Iniciar
    cargar();
})();
</script>