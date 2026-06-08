import React, { useState, useEffect, useRef } from 'react';
import ReactDOM from 'react-dom/client';

function acortarNombre(nombre) {
    if (!nombre) return '';
    if (nombre.length <= 25) return nombre;
    return nombre.substring(0, 22) + '...';
}

const MAX_SIZE_MB = 2;
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];
const LIMITE_DESC = 500;

const IconEdit = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style={{flexShrink:0}}>
        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
    </svg>
);

const IconTrash = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style={{flexShrink:0}}>
        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
    </svg>
);

// ==========================================
// HELPER: extraer texto puro de HTML
// ==========================================
function htmlATextoPuro(html) {
    if (!html) return '';
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

// ==========================================
// COMPONENTE DROPDOWN TIPO DE FORMACIÓN
// ==========================================
function TipoFormacionDropdown({ value, name, formacionId }) {
    const [open, setOpen] = useState(false);
    const [selected, setSelected] = useState('');
    const [mostrarOtroInput, setMostrarOtroInput] = useState(false);
    const [otroValue, setOtroValue] = useState('');
    const dropdownRef = useRef(null);

    const TIPO_FORMACION_OPTIONS = {
        'Educación formal': [
            'Colegio / Bachillerato', 'Técnico Superior',
            'Licenciatura / Ingeniería', 'Maestría', 'Doctorado / PhD'
        ],
        'Formación complementaria': [
            'Bootcamp', 'Curso online', 'Certificación profesional',
            'Diplomado', 'Intercambio académico', 'Otro'
        ]
    };

    const todasOpciones = Object.values(TIPO_FORMACION_OPTIONS).flat();

    useEffect(() => {
        if (value && todasOpciones.includes(value) && value !== 'Otro') {
            setSelected(value);
            setMostrarOtroInput(false);
            setOtroValue('');
        } else if (value === 'Otro') {
            setSelected('Otro');
            setMostrarOtroInput(true);
            setOtroValue('');
        } else if (value && !todasOpciones.includes(value)) {
            setSelected('Otro');
            setMostrarOtroInput(true);
            setOtroValue(value);
        } else {
            setSelected('');
            setMostrarOtroInput(false);
            setOtroValue('');
        }
    }, [value]);

    useEffect(() => {
        function handleClickOutside(event) {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const elegirOpcion = (opcion) => {
        setSelected(opcion);
        setOpen(false);
        if (opcion === 'Otro') {
            setMostrarOtroInput(true);
        } else {
            setMostrarOtroInput(false);
            setOtroValue('');
        }
    };

    const getButtonText = () => {
        if (!selected) return '— Seleccionar tipo —';
        if (selected === 'Otro' && otroValue) return `Otro: ${otroValue}`;
        if (selected === 'Otro' && !otroValue) return 'Otro (especificar)';
        return selected;
    };

    const valorFinal = selected === 'Otro' && otroValue ? otroValue : selected;

    return (
        <div className="form-group" style={{ width: '100%' }}>
            <label className="form-label">
                Tipo de formación <span className="required">*</span>
            </label>

            <div
                className={`custom-dropdown ${open ? 'open' : ''}`}
                ref={dropdownRef}
                style={{ position: 'relative', width: '100%' }}
            >
                <button
                    type="button"
                    className="custom-dropdown-toggle"
                    onClick={() => setOpen(!open)}
                >
                    <span className={`dropdown-label ${!selected ? 'muted' : ''}`}>
                        {getButtonText()}
                    </span>
                    <span className="dropdown-arrow">{open ? '▼' : '▲'}</span>
                </button>

                {open && (
                    <ul className="custom-dropdown-menu">
                        <li
                            className={`placeholder-opt ${!selected ? 'selected' : ''}`}
                            onClick={() => elegirOpcion('')}
                        >
                            — Seleccionar tipo —
                        </li>
                        {Object.entries(TIPO_FORMACION_OPTIONS).map(([grupo, opciones]) => (
                            <React.Fragment key={grupo}>
                                <li className="dropdown-group-title">{grupo}</li>
                                {opciones.map(opt => (
                                    <li
                                        key={opt}
                                        className={selected === opt ? 'selected' : ''}
                                        onClick={() => elegirOpcion(opt)}
                                    >
                                        {opt}
                                    </li>
                                ))}
                            </React.Fragment>
                        ))}
                    </ul>
                )}

                <input type="hidden" name={name} value={valorFinal} />
            </div>

            {mostrarOtroInput && (
                <div style={{ marginTop: '12px' }}>
                    <label className="form-label">
                        Especificar otro tipo de formación
                        <span className="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="otro_tipo_formacion"
                        className="form-input"
                        placeholder="Ej. Microcredencial, Taller especializado, Curso presencial, etc."
                        value={otroValue}
                        onChange={(e) => setOtroValue(e.target.value)}
                        maxLength="50"
                        autoFocus={selected === 'Otro'}
                    />
                    {!otroValue && selected === 'Otro' && (
                        <div className="error-message" style={{ marginTop: '4px' }}>
                            Por favor, especifica el tipo de formación
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}

// ==========================================
// DESCRIPCIÓN COLAPSABLE — renderiza HTML sin etiquetas visibles
// ==========================================
function DescripcionColapsable({ html, limite = 150 }) {
    const [expandido, setExpandido] = useState(false);

    if (!html) return null;

    const textoPuro = htmlATextoPuro(html);
    const esMuyLargo = textoPuro.length > limite;

    return (
        <div>
            <div
                className="historial-desc"
                style={{
                    display: '-webkit-box',
                    WebkitLineClamp: expandido ? 'unset' : 3,
                    WebkitBoxOrient: 'vertical',
                    overflow: expandido ? 'visible' : 'hidden',
                }}
                dangerouslySetInnerHTML={{ __html: html }}
            />
            {esMuyLargo && (
                <button
                    onClick={() => setExpandido(!expandido)}
                    style={{
                        background: 'none',
                        border: 'none',
                        color: 'var(--teal)',
                        fontSize: '13px',
                        cursor: 'pointer',
                        padding: '4px 0',
                        fontWeight: '600',
                    }}
                >
                    {expandido ? 'Ver menos' : 'Ver más'}
                </button>
            )}
        </div>
    );
}

// ==========================================
// EDITOR DE EVIDENCIAS
// ==========================================
function EvidenciasEditor({ formacionId, evidenciasIniciales = [] }) {
    // Evidencias ya guardadas en el servidor (con path real como id)
    const [evidencias, setEvidencias] = useState(
        evidenciasIniciales.map(ev => ({ ...ev, marked: false }))
    );
    // Archivos nuevos pendientes de subir
    const [archivos, setArchivos] = useState([]);
    // Paths marcados para eliminar (se envían como campo hidden)
    const [aEliminar, setAEliminar] = useState([]);
    const [errores, setErrores] = useState([]);
    const inputRef = useRef(null);

    function validarArchivo(file) {
        if (!ALLOWED_TYPES.includes(file.type))
            return `"${file.name}" no es JPG, PNG ni PDF.`;
        if (file.size > MAX_SIZE_MB * 1024 * 1024)
            return `"${file.name}" supera los ${MAX_SIZE_MB} MB.`;
        return null;
    }

    function agregarArchivos(e) {
        const nuevos = Array.from(e.target.files);
        const errs = [];
        const validos = [];
        nuevos.forEach(f => {
            const err = validarArchivo(f);
            if (err) errs.push(err);
            else validos.push({ file: f, preview: URL.createObjectURL(f) });
        });
        setErrores(errs);
        setArchivos(prev => [...prev, ...validos]);
        e.target.value = '';
    }

    function quitarPendiente(idx) {
        setArchivos(prev => prev.filter((_, i) => i !== idx));
    }

    // Marcar evidencia guardada para eliminar (se envía al servidor al hacer submit)
    function marcarEliminar(path) {
        setEvidencias(prev => prev.filter(ev => ev.path !== path));
        setAEliminar(prev => [...prev, path]);
    }

    return (
        <div style={{ marginBottom: '12px' }}>
            <label className="form-label">Evidencias (JPG, PNG, PDF — máx. {MAX_SIZE_MB} MB c/u)</label>

            {/* Campo oculto con los paths a eliminar */}
            {aEliminar.length > 0 && (
                <input type="hidden" name="evidencias_eliminar" value={aEliminar.join(',')} />
            )}

            {/* Evidencias guardadas */}
            {evidencias.length > 0 && (
                <div className="evidencias-grid">
                    {evidencias.map(ev => (
                        <div key={ev.path} style={{ position: 'relative', display: 'inline-block' }}>
                            {ev.mime_type?.startsWith('image/') ? (
                                <img src={ev.url} alt="evidencia"
                                    style={{ width: 70, height: 70, objectFit: 'cover', borderRadius: 8, border: '1px solid #ddd' }} />
                            ) : (
                                <a href={ev.url} target="_blank" rel="noreferrer" className="pdf-icon-only">
                                    <span className="pdf-icon-custom"></span>
                                </a>
                            )}
                            <button type="button"
                                onClick={() => marcarEliminar(ev.path)}
                                style={{
                                    position: 'absolute', top: -6, right: -6,
                                    background: '#e53e3e', color: '#fff', border: 'none',
                                    borderRadius: '50%', width: 18, height: 18,
                                    fontSize: 11, cursor: 'pointer', lineHeight: '18px', textAlign: 'center',
                                }}>×</button>
                        </div>
                    ))}
                </div>
            )}

            {/* Archivos nuevos pendientes */}
            {archivos.length > 0 && (
                <div className="evidencias-grid">
                    {archivos.map((a, i) => (
                        <div key={i} style={{ position: 'relative', display: 'inline-block' }}>
                            {a.file.type.startsWith('image/') ? (
                                <img src={a.preview} alt="preview"
                                    style={{ width: 70, height: 70, objectFit: 'cover', borderRadius: 8,
                                             border: '2px dashed var(--teal)', opacity: 0.85 }} />
                            ) : (
                                <div className="pdf-icon-only">
                                    <span className="pdf-icon-custom" style={{ borderColor: 'var(--teal)' }}></span>
                                </div>
                            )}
                            <button type="button" onClick={() => quitarPendiente(i)}
                                style={{
                                    position: 'absolute', top: -6, right: -6,
                                    background: '#718096', color: '#fff', border: 'none',
                                    borderRadius: '50%', width: 18, height: 18,
                                    fontSize: 11, cursor: 'pointer', lineHeight: '18px', textAlign: 'center',
                                }}>×</button>
                        </div>
                    ))}
                </div>
            )}

            {errores.length > 0 && (
                <ul style={{ color: '#e53e3e', fontSize: 12, margin: '4px 0', paddingLeft: 16 }}>
                    {errores.map((er, i) => <li key={i}>{er}</li>)}
                </ul>
            )}

            <input ref={inputRef} type="file" name="evidencias[]" multiple
                accept=".jpg,.jpeg,.png,.pdf"
                onChange={agregarArchivos}
                style={{ display: 'none' }} />
            <button type="button" className="btn-sm"
                onClick={() => inputRef.current?.click()}
                style={{ marginTop: 4 }}>
                + Agregar archivo
            </button>
        </div>
    );
}

// ==========================================
// HISTORIAL ACADÉMICO
// ==========================================
function HistorialAcademico({ formaciones: initialFormaciones }) {
    const [erroresEdicion, setErroresEdicion] = useState({});
    const [formaciones, setFormaciones] = useState(initialFormaciones);
    const [editando, setEditando] = useState(null);
    const [confirmDelete, setConfirmDelete] = useState(null);
    const quillInstances = useRef({});

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // ==========================================
    // INICIALIZAR QUILL EN MODO EDICIÓN
    // ==========================================
    useEffect(() => {
        if (editando && typeof window !== 'undefined' && window.Quill) {
            if (quillInstances.current[editando]) {
                delete quillInstances.current[editando];
            }

            setTimeout(() => {
                const editorId = `editorDescripcionEdit_${editando}`;
                const editorElement = document.getElementById(editorId);
                const hiddenInput = document.getElementById(`descripcionHidden_${editando}`);
                const counterElement = document.getElementById(`descripcionCounter_${editando}`);
                const errorElement = document.getElementById(`descripcionError_${editando}`);

                if (editorElement && !editorElement.querySelector('.ql-editor')) {
                    try {
                        const quill = new window.Quill(`#${editorId}`, {
                            theme: 'snow',
                            placeholder: 'Describe brevemente tus logros, materias destacadas o proyectos...',
                            modules: {
                                toolbar: [
                                    [{ 'font': [] }],
                                    [{ 'size': ['small', false, 'large', 'huge'] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'color': [] }, { 'background': [] }],
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                                    [{ 'align': [] }],
                                    ['link', 'clean']
                                ]
                            }
                        });

                        quillInstances.current[editando] = quill;

                        if (hiddenInput && hiddenInput.value) {
                            quill.root.innerHTML = hiddenInput.value;
                        }

                        function getTextoPuroEdit() {
                            const raw = quill.getText();
                            return raw.endsWith('\n') ? raw.slice(0, -1) : raw;
                        }

                        const actualizarDescripcion = () => {
                            const texto = getTextoPuroEdit();
                            const longitud = texto.length;

                            if (hiddenInput) hiddenInput.value = quill.root.innerHTML;

                            if (counterElement) {
                                counterElement.textContent = `${longitud} / ${LIMITE_DESC}`;
                                if (longitud >= LIMITE_DESC) {
                                    counterElement.style.color = '#e74c3c';
                                    counterElement.style.fontWeight = 'bold';
                                    if (errorElement) errorElement.classList.remove('hidden');
                                } else {
                                    counterElement.style.color = '#6c757d';
                                    counterElement.style.fontWeight = 'normal';
                                    if (errorElement) errorElement.classList.add('hidden');
                                }
                            }
                        };

                        quill.on('text-change', function() {
                            const texto = getTextoPuroEdit();
                            if (texto.length > LIMITE_DESC) {
                                quill.history.undo();
                                return;
                            }
                            actualizarDescripcion();
                        });

                        actualizarDescripcion();

                    } catch (error) {
                        console.error('Error inicializando Quill:', error);
                    }
                }
            }, 100);
        }
    }, [editando]);

    async function eliminar(id) {
        const res = await fetch(`/informacion-academica/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
        });
        if (res.ok) {
            setFormaciones(formaciones.filter(f => f.id !== id));
            setConfirmDelete(null);
        }
    }

    async function guardarEdicion(e, id) {
        e.preventDefault();

        if (quillInstances.current[id]) {
            const quill = quillInstances.current[id];
            const hiddenInput = document.getElementById(`descripcionHidden_${id}`);
            if (hiddenInput) hiddenInput.value = quill.root.innerHTML;
        }

        const formData = new FormData(e.target);
        formData.append('_method', 'PUT');

        const res = await fetch(`/informacion-academica/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (res.ok) {
            const updated = await res.json();
            setFormaciones(formaciones.map(f => f.id === id ? updated : f));
            setEditando(null);
            setErroresEdicion(prev => ({ ...prev, [id]: null }));
        } else if (res.status === 422) {
            const data = await res.json();
            setErroresEdicion(prev => ({ ...prev, [id]: data.errors }));
        }
    }

    if (formaciones.length === 0) return null;

    return (
        <>
            <div className="historial-divider">
                <span>— HISTORIAL —</span>
            </div>

            <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                {formaciones.map((f, index) => (
                    <div key={f.id} className="historial-card">
                        {editando === f.id ? (
                            <form onSubmit={(e) => guardarEdicion(e, f.id)} encType="multipart/form-data">
                                <div className="form-row" style={{ marginBottom: '12px' }}>
                                    <div className="form-group">
                                        <label className="form-label">Institución <span className="required">*</span></label>
                                        <input
                                            className={`form-input${erroresEdicion[f.id]?.institucion ? ' error' : ''}`}
                                            name="institucion"
                                            defaultValue={f.institution}
                                            required
                                            maxLength="60"
                                        />
                                        {erroresEdicion[f.id]?.institucion && (
                                            <span className="error-message">{erroresEdicion[f.id].institucion[0]}</span>
                                        )}
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">Título obtenido <span className="required">*</span></label>
                                        <input
                                            className={`form-input${erroresEdicion[f.id]?.titulo_obtenido ? ' error' : ''}`}
                                            name="titulo_obtenido"
                                            defaultValue={f.title}
                                            required
                                            maxLength="30"
                                        />
                                        {erroresEdicion[f.id]?.titulo_obtenido && (
                                            <span className="error-message">{erroresEdicion[f.id].titulo_obtenido[0]}</span>
                                        )}
                                    </div>
                                </div>

                                <div className="form-row" style={{ marginBottom: '12px' }}>
                                    <div className="form-group">
                                        <label className="form-label">Especialidad (opcional)</label>
                                        <input
                                            className="form-input"
                                            name="especialidad"
                                            defaultValue={f.specialty || ''}
                                            maxLength="50"
                                            placeholder="Ej. Inteligencia Artificial, Redes, Desarrollo Web, QA, DevOps"
                                        />
                                    </div>

                                    <TipoFormacionDropdown
                                        value={f.formation_type || ''}
                                        name="tipo_formacion"
                                        formacionId={f.id}
                                    />
                                </div>

                                <div className="form-row" style={{ marginBottom: '12px' }}>
                                    <div className="form-group">
                                        <label className="form-label">Fecha de inicio</label>
                                        <input
                                            className="form-input"
                                            type="date"
                                            name="fecha_inicio"
                                            defaultValue={f.start_date ? f.start_date.substring(0, 10) : ''}
                                            min="1950-01-01"
                                            max={new Date().toISOString().substring(0, 10)}
                                            required
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">Fecha de fin</label>
                                        <input
                                            className="form-input"
                                            type="date"
                                            name="fecha_fin"
                                            id={`fechaFin_${f.id}`}
                                            defaultValue={f.end_date ? f.end_date.substring(0, 10) : ''}
                                            min="1950-01-01"
                                            max={new Date().toISOString().substring(0, 10)}
                                        />
                                    </div>
                                </div>

                                <div className="form-checkbox-row" style={{ marginBottom: '12px' }}>
                                    <input
                                        type="checkbox"
                                        name="estudio_actual"
                                        defaultChecked={f.is_current}
                                        onChange={(e) => {
                                            const fechaFinInput = document.getElementById(`fechaFin_${f.id}`);
                                            if (fechaFinInput) {
                                                fechaFinInput.disabled = e.target.checked;
                                                if (e.target.checked) fechaFinInput.value = '';
                                            }
                                        }}
                                    />
                                    <label>Estudio actual</label>
                                </div>

                                <div className="form-group" style={{ marginBottom: '12px' }}>
                                    <label className="form-label">Descripción</label>
                                    <div id={`editorDescripcionEdit_${f.id}`} style={{ height: '180px', marginBottom: '45px' }}></div>
                                    <input
                                        type="hidden"
                                        name="descripcion"
                                        id={`descripcionHidden_${f.id}`}
                                        defaultValue={f.description || ''}
                                    />
                                    <div id={`descripcionCounter_${f.id}`} className="char-counter">
                                        {htmlATextoPuro(f.description).length} / {LIMITE_DESC}
                                    </div>
                                    <div id={`descripcionError_${f.id}`} className="error-message hidden">
                                        La descripción no puede exceder los 500 caracteres
                                    </div>
                                    {erroresEdicion[f.id]?.descripcion && (
                                        <span className="error-message">{erroresEdicion[f.id].descripcion[0]}</span>
                                    )}
                                </div>

                                <EvidenciasEditor
                                    formacionId={f.id}
                                    evidenciasIniciales={
                                        f.evidence_url
                                            ? JSON.parse(f.evidence_url).map((path) => ({
                                                path: path,
                                                url: `/storage/${path}`,
                                                mime_type: path.endsWith('.pdf') ? 'application/pdf' : 'image/jpeg',
                                                nombre: path.split('/').pop(),
                                            }))
                                            : []
                                    }
                                />

                                <div className="historial-actions">
                                    <button type="submit" className="btn-sm btn-guardar">
                                        Guardar cambios
                                    </button>
                                    <button type="button" className="btn-sm" onClick={() => { setEditando(null); setErroresEdicion({}); }}>
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        ) : (
                            <div>
                                <div className="historial-card-header">
                                    <span className="historial-index">{String(index + 1).padStart(2, '0')}</span>
                                    {f.is_current && <span className="badge-actual">Actual</span>}
                                </div>
                                <div className="historial-title">{f.title}</div>
                                <div className="historial-subtitle">{f.institution}</div>

                                {f.formation_type && (
                                    <span className="tag tag-gray" style={{marginBottom: '4px', display: 'inline-block'}}>
                                        🎓 {f.formation_type}
                                    </span>
                                )}

                                <div className="historial-tags">
                                    <span className="tag tag-teal">
                                        {f.start_date?.substring(0, 7)} – {f.is_current ? 'Presente' : (f.end_date?.substring(0, 7) || '')}
                                    </span>
                                    <span className="tag tag-gray">{f.is_current ? 'En curso' : 'Finalizado'}</span>
                                </div>

                                {f.description && (
                                    <DescripcionColapsable html={f.description} />
                                )}

                                {f.evidence_url && JSON.parse(f.evidence_url).length > 0 && (
                                    <div className="evidencias-grid">
                                        {JSON.parse(f.evidence_url).map((path, i) => (
                                            path.endsWith('.pdf') ? (
                                                <a key={i}
                                                    href={`/storage/${path}`}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="pdf-icon-only">
                                                    <span className="pdf-icon-custom"></span>
                                                </a>
                                            ) : (
                                                <a key={i}
                                                    href={`/storage/${path}`}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="img-link">
                                                    <img src={`/storage/${path}`} alt="evidencia"
                                                        style={{ width: 70, height: 70, objectFit: 'cover',
                                                                borderRadius: 8, border: '1px solid #ddd' }} />
                                                </a>
                                            )
                                        ))}
                                    </div>
                                )}

                                <div className="historial-actions">
                                    <button className="btn-sm" onClick={() => setEditando(f.id)}>
                                        <IconEdit /> Editar
                                    </button>
                                    <button className="btn-sm danger" onClick={() => setConfirmDelete(f.id)}>
                                        <IconTrash /> Eliminar
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                ))}
            </div>

            {confirmDelete && (
                <div className="modal-backdrop active" onClick={() => setConfirmDelete(null)}>
                    <div className="modal-box" onClick={e => e.stopPropagation()}>
                        <h3>Eliminar formación</h3>
                        <p>
                            ¿Estás seguro de que deseas eliminar este registro?<br/>
                            <span style={{color:'#e74c3c', fontWeight:600}}>Esta acción no se puede deshacer.</span>
                        </p>
                        <div style={{display:'flex', gap:'12px', justifyContent:'center'}}>
                            <button className="btn-danger" onClick={() => eliminar(confirmDelete)}>
                                Sí, eliminar
                            </button>
                            <button className="btn" onClick={() => setConfirmDelete(null)}>
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}

const el = document.getElementById('historial-react');
if (el) {
    const formaciones = JSON.parse(el.dataset.formaciones || '[]');
    ReactDOM.createRoot(el).render(<HistorialAcademico formaciones={formaciones} />);
}