import React, { useState, useRef, useEffect, useCallback } from 'react';
import ReactDOM from 'react-dom/client';

/* ── Constantes ─────────────────────────────────────────── */
const MESES = {
    '01': 'Enero',   '02': 'Febrero',  '03': 'Marzo',
    '04': 'Abril',   '05': 'Mayo',     '06': 'Junio',
    '07': 'Julio',   '08': 'Agosto',   '09': 'Septiembre',
    '10': 'Octubre', '11': 'Noviembre','12': 'Diciembre'
};

const CARGO_OPTIONS = [
    'Frontend Developer', 'Backend Developer', 'Full Stack Developer',
    'UI/UX Designer', 'DevOps Engineer', 'Mobile Developer',
    'Project Manager', 'QA Tester', 'Database Administrator',
    'Technical Leader', 'Data Analyst', 'Scrum Master',
    'Product Owner', 'Business Analyst', 'Security Engineer',
    'Data Engineer', 'Cloud Engineer', 'AI Engineer', 'Systems Analyst',
];

const QUILL_TOOLBAR = [
    [{ font: [] }],
    ['bold', 'italic', 'underline'],
    [{ color: [] }],
    ['link'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['clean'],
];

const MAX_DESC_CHARS = 500;

/* ── Iconos ────────────────────────────────────────────── */
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

const IconX = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16" style={{flexShrink:0}}>
        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
    </svg>
);

/* ── Utilidades de fecha ────────────────────────────────── */
function getDia(fecha)  { return fecha ? String(fecha).substring(8, 10) : ''; }
function getMes(fecha)  { return fecha ? String(fecha).substring(5, 7)  : ''; }
function getAnio(fecha) { return fecha ? String(fecha).substring(0, 4)  : ''; }

function fmtFecha(fecha) {
    if (!fecha) return null;
    const dia = getDia(fecha), mes = getMes(fecha), anio = getAnio(fecha);
    if (!dia || !mes || !anio) return null;
    return `${parseInt(dia)} de ${MESES[mes] || mes} de ${anio}`;
}

function padZ(n) { return String(n).padStart(2, '0'); }
function toDateVal(anio, mes, dia) {
    if (!anio || !mes || !dia) return '';
    return `${anio}-${padZ(mes)}-${padZ(dia)}`;
}
function fromDateVal(val) {
    if (!val) return { dia: '', mes: '', anio: '' };
    const [anio, mes, dia] = val.split('-');
    return { dia, mes, anio };
}

/* ── Agrupación por empresa + start_date ────────────────── */
function agruparExperiencias(experiencias) {
    const mapa = new Map();
    experiencias.forEach(exp => {
        const key = `${exp.institution}||${exp.start_date}`;
        if (!mapa.has(key)) {
            mapa.set(key, {
                key,
                institution: exp.institution,
                start_date:  exp.start_date,
                end_date:    exp.end_date,
                is_current:  exp.is_current,
                location:    exp.location,
                description: exp.description,
                items: [],
            });
        }
        mapa.get(key).items.push(exp);
    });
    return Array.from(mapa.values());
}

/* ══════════════════════════════════════════════════════════
   Hook: useQuillEditor
   Monta Quill en un ref de div, devuelve getHTML / setHTML
   ══════════════════════════════════════════════════════════ */
function useQuillEditor(containerRef, initialHTML, onChange) {
    const quillRef = useRef(null);

    useEffect(() => {
        /* Quill ya está cargado globalmente vía CDN en el Blade */
        if (!window.Quill || !containerRef.current) return;
        /* Evitar doble inicialización */
        if (quillRef.current) return;

        const quill = new window.Quill(containerRef.current, {
            theme: 'snow',
            placeholder: 'Describe brevemente tus responsabilidades y logros en este cargo...',
            modules: { toolbar: QUILL_TOOLBAR },
        });

        /* Cargar contenido inicial */
        if (initialHTML && initialHTML.trim() && initialHTML !== '<p><br></p>') {
            quill.root.innerHTML = initialHTML;
        }

        quill.on('text-change', () => {
            const text = quill.getText().trim();
            /* Limitar a MAX_DESC_CHARS caracteres de texto plano */
            if (text.length > MAX_DESC_CHARS) {
                quill.deleteText(MAX_DESC_CHARS, text.length - MAX_DESC_CHARS);
                return;
            }
            const html = quill.root.innerHTML;
            onChange(html === '<p><br></p>' ? '' : html, text.length);
        });

        quillRef.current = quill;

        /* Cleanup: destruir el editor al desmontar */
        return () => {
            if (quillRef.current) {
                quillRef.current = null;
            }
        };
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []); /* Solo al montar */

    return quillRef;
}

/* ── Dropdown personalizado para React ──────────────────── */
function CargoDropdown({ value, onChange, name }) {
    const [open, setOpen]         = useState(false);
    const [selected, setSelected] = useState(value || '');
    const ref                     = useRef(null);

    useEffect(() => {
        function handler(e) {
            if (ref.current && !ref.current.contains(e.target)) setOpen(false);
        }
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, []);

    function elegir(val) {
        setSelected(val);
        onChange(val);
        setOpen(false);
    }

    return (
        <div className={`custom-dropdown${open ? ' open' : ''}`} ref={ref}>
            <button type="button" className="custom-dropdown-toggle" onClick={() => setOpen(o => !o)}>
                <span className={`dropdown-label${!selected ? ' muted' : ''}`}>
                    {selected || '— Seleccionar cargo —'}
                </span>
                <span className="dropdown-arrow">{open ? '▼' : '▲'}</span>
            </button>
            {open && (
                <ul className="custom-dropdown-menu">
                    <li className={`placeholder-opt${!selected ? ' selected' : ''}`}
                        onClick={() => elegir('')} data-value="">
                        — Seleccionar cargo —
                    </li>
                    {CARGO_OPTIONS.map(opt => (
                        <li key={opt}
                            className={selected === opt ? 'selected' : ''}
                            onClick={() => elegir(opt)}
                            data-value={opt}>
                            {opt}
                        </li>
                    ))}
                </ul>
            )}
            <input type="hidden" name={name} value={selected} />
        </div>
    );
}

/* ── Toggle trabajo actual ──────────────────────────────── */
function ToggleActual({ defaultChecked, inicioDia, inicioMes, inicioAnio, finDia, finMes, finAnio, errorFin }) {
    const [actual,    setActual]    = useState(defaultChecked || false);
    const [inicioVal, setInicioVal] = useState(toDateVal(inicioAnio, inicioMes, inicioDia));
    const [finVal,    setFinVal]    = useState(toDateVal(finAnio, finMes, finDia));
    const today = new Date().toISOString().split('T')[0];

    const inicioPartes = fromDateVal(inicioVal);
    const finPartes    = fromDateVal(finVal);

    return (
        <>
            <input type="hidden" name="fecha_inicio_dia"  value={inicioPartes.dia} />
            <input type="hidden" name="fecha_inicio_mes"  value={inicioPartes.mes} />
            <input type="hidden" name="fecha_inicio_anio" value={inicioPartes.anio} />
            <input type="hidden" name="fecha_fin_dia"     value={actual ? '' : finPartes.dia} />
            <input type="hidden" name="fecha_fin_mes"     value={actual ? '' : finPartes.mes} />
            <input type="hidden" name="fecha_fin_anio"    value={actual ? '' : finPartes.anio} />

            <div className="form-checkbox-row" style={{ marginBottom: '14px' }}>
                <input type="checkbox" name="trabajo_actual" checked={actual}
                    onChange={e => { setActual(e.target.checked); if (e.target.checked) setFinVal(''); }} />
                <label>Trabajo actual</label>
            </div>

            <div className="form-row" style={{ marginBottom: '14px' }}>
                <div className="form-group">
                    <label className="form-label">Fecha de inicio <span className="required">*</span></label>
                    <div className="date-picker-wrap">
                        <input type="date" className="form-input date-picker"
                            value={inicioVal} min="1950-01-01" max={today}
                            onChange={e => setInicioVal(e.target.value)} />
                    </div>
                </div>
                <div className="form-group" style={{ opacity: actual ? 0.4 : 1 }}>
                    <label className="form-label">
                        Fecha de fin {!actual && <span className="required">*</span>}
                    </label>
                    <div className="date-picker-wrap">
                        <input type="date"
                            className={`form-input date-picker${errorFin ? ' is-invalid' : ''}`}
                            value={finVal} min={inicioVal || '1950-01-01'} max={today}
                            disabled={actual}
                            onChange={e => setFinVal(e.target.value)} />
                    </div>
                    {errorFin && <div className="error-message">{errorFin}</div>}
                </div>
            </div>
        </>
    );
}

/* ══════════════════════════════════════════════════════════
   FormEdicionGrupo — con Quill integrado
   ══════════════════════════════════════════════════════════ */
function FormEdicionGrupo({ grupo, token, onGuardado, onCancelar }) {
    const [error,           setError]          = useState('');
    const [errorFin,        setErrorFin]       = useState('');
    const [guardando,       setGuardando]      = useState(false);
    const [confirmEliminar, setConfirmEliminar] = useState(null);
    const [empresa,         setEmpresa]        = useState(grupo.institution || '');
    const [ubicacion,       setUbicacion]      = useState(grupo.location || '');

    /* ── Estado de descripción (HTML de Quill) ── */
    const [descripcion,     setDescripcion]    = useState(grupo.description || '');
    const [descCharCount,   setDescCharCount]  = useState(0);

    /* Ref del div donde montará Quill */
    const quillContainerRef = useRef(null);

    /* Callback estable para el hook */
    const handleDescChange = useCallback((html, charCount) => {
        setDescripcion(html);
        setDescCharCount(charCount);
    }, []);

    /* Montar Quill cuando el componente se monte */
    useQuillEditor(quillContainerRef, grupo.description || '', handleDescChange);

    /* Calcular chars iniciales al montar */
    useEffect(() => {
        if (grupo.description) {
            /* Contar texto plano del HTML inicial */
            const tmp = document.createElement('div');
            tmp.innerHTML = grupo.description;
            setDescCharCount(tmp.textContent.trim().length);
        }
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const [cargos, setCargos] = useState(
        grupo.items.map(exp => ({ id: exp.id, tempId: null, value: exp.title || '', deleted: false }))
    );

    const cargosVisibles = cargos.filter(c => !c.deleted);

    function getKey(c) { return c.id ?? c.tempId; }

    function setCargo(key, val) {
        setCargos(prev => prev.map(c => getKey(c) === key ? { ...c, value: val } : c));
    }

    function agregarNuevoCargo() {
        if (cargosVisibles.length >= 5) return;
        setCargos(prev => [...prev, { id: null, tempId: 'new_' + Date.now(), value: '', deleted: false }]);
    }

    function marcarEliminado(key) {
        if (cargosVisibles.length <= 1) return;
        setCargos(prev => prev.map(c => getKey(c) === key ? { ...c, deleted: true } : c));
        setConfirmEliminar(null);
    }

    function desmarcarEliminado(key) {
        setCargos(prev => prev.map(c => getKey(c) === key ? { ...c, deleted: false } : c));
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setError('');

        /* Validar cargos */
        for (const c of cargosVisibles) {
            if (!c.value) { setError('Debes seleccionar un cargo en cada campo.'); return; }
        }
        const valores = cargosVisibles.map(c => c.value.toLowerCase());
        const duplicado = valores.find((v, i) => valores.indexOf(v) !== i);
        if (duplicado) {
            const nombre = cargosVisibles.find(c => c.value.toLowerCase() === duplicado).value;
            setError(`El cargo "${nombre}" está duplicado. Cada cargo debe ser único.`);
            return;
        }

        setErrorFin('');
        const form          = e.target;
        const trabajoActual = form.trabajo_actual.checked;
        const inicioDia     = form.fecha_inicio_dia.value;
        const inicioMes     = form.fecha_inicio_mes.value;
        const inicioAnio    = form.fecha_inicio_anio.value;
        const finDia        = form.fecha_fin_dia?.value;
        const finMes        = form.fecha_fin_mes?.value;
        const finAnio       = form.fecha_fin_anio?.value;

        if (!inicioDia || !inicioMes || !inicioAnio) {
            setError('La fecha de inicio es obligatoria.'); return;
        }
        if (!trabajoActual && (!finDia || !finMes || !finAnio)) {
            setErrorFin('Selecciona la fecha de fin o marca "Trabajo actual".'); return;
        }
        if (!trabajoActual && finDia && finMes && finAnio) {
            const ini = new Date(parseInt(inicioAnio), parseInt(inicioMes)-1, parseInt(inicioDia));
            const fin = new Date(parseInt(finAnio),    parseInt(finMes)-1,    parseInt(finDia));
            if (fin < ini) { setErrorFin('La fecha de fin no puede ser anterior a la de inicio.'); return; }
        }

        /* descripcion ya tiene el HTML actualizado gracias al hook */
        const payload = {
            empresa,
            location:          ubicacion,
            descripcion:       descripcion,  /* ← HTML de Quill */
            fecha_inicio_dia:  inicioDia,
            fecha_inicio_mes:  inicioMes,
            fecha_inicio_anio: inicioAnio,
            fecha_fin_dia:     trabajoActual ? null : finDia,
            fecha_fin_mes:     trabajoActual ? null : finMes,
            fecha_fin_anio:    trabajoActual ? null : finAnio,
            trabajo_actual:    trabajoActual,
        };

        setGuardando(true);
        try {
            const idsEliminados = new Set();

            /* Eliminar cargos marcados */
            const aEliminar = cargos.filter(c => c.deleted && c.id);
            for (const c of aEliminar) {
                await fetch(`/experiencia-laboral/${c.id}`, {
                    method:  'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                });
                idsEliminados.add(c.id);
            }

            /* Actualizar cargos existentes */
            const existentes = cargosVisibles.filter(c => c.id);
            const putResults = [];
            for (const c of existentes) {
                const res = await fetch(`/experiencia-laboral/${c.id}`, {
                    method:  'PUT',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ...payload, cargo: c.value }),
                });
                if (res.ok) {
                    putResults.push(await res.json());
                } else {
                    const d = await res.json().catch(() => ({}));
                    throw d;
                }
            }

            /* Crear nuevos cargos */
            const nuevos      = cargosVisibles.filter(c => !c.id);
            const postResults = [];
            for (const c of nuevos) {
                const res = await fetch('/experiencia-laboral', {
                    method:  'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ...payload, cargos: [c.value] }),
                });
                if (res.ok) {
                    const data = await res.json();
                    postResults.push(...(Array.isArray(data) ? data : [data]));
                } else {
                    const d = await res.json().catch(() => ({}));
                    throw d;
                }
            }

            /* Construir start_date / end_date para el payloadLocal */
            const startDate = (inicioAnio && inicioMes && inicioDia)
                ? `${inicioAnio}-${String(inicioMes).padStart(2,'0')}-${String(inicioDia).padStart(2,'0')}`
                : null;
            const endDate = (!trabajoActual && finAnio && finMes && finDia)
                ? `${finAnio}-${String(finMes).padStart(2,'0')}-${String(finDia).padStart(2,'0')}`
                : null;

            /* IDs de TODOS los items del grupo: los que se actualizaron via PUT
               y los que quedaron igual. Todos comparten description/empresa/fechas. */
            const idsGrupoCompleto = new Set(grupo.items.map(i => i.id));

            const payloadLocal = {
                idsGrupo:    idsGrupoCompleto,
                descripcion: descripcion,
                empresa:     empresa,
                location:    ubicacion,
                start_date:  startDate,
                end_date:    endDate,
                is_current:  trabajoActual,
            };

            onGuardado([...putResults, ...postResults], idsEliminados, payloadLocal);

        } catch (err) {
            if (err && err.errors) setError(Object.values(err.errors).join(' — '));
            else setError((err && err.error) || 'Error al guardar los cambios.');
        } finally {
            setGuardando(false);
        }
    }

    const rep = grupo.items[0];

    return (
        <form onSubmit={handleSubmit} className="edit-form-wrap">
            {error && <div className="edit-error">{error}</div>}

            {/* Empresa */}
            <div className="form-row" style={{ marginBottom: '14px' }}>
                <div className="form-group">
                    <label className="form-label">Empresa <span className="required">*</span></label>
                    <input name="empresa" value={empresa} maxLength={100} required className="form-input"
                        onChange={e => setEmpresa(e.target.value)} />
                    <div className="char-counter">{empresa.length}/100</div>
                </div>
            </div>

            {/* Cargos */}
            <div className="form-group" style={{ marginBottom: '14px' }}>
                <label className="form-label">
                    Cargos <span className="required">*</span>
                    <span className="cargos-hint">(máx. 5)</span>
                </label>

                {cargos.map((c) => {
                    const key = getKey(c);
                    if (c.deleted) {
                        return (
                            <div key={key} className="cargo-deleted-row">
                                <span className="cargo-deleted-label">
                                    <IconTrash /> {c.value || 'Cargo sin seleccionar'} — se eliminará al guardar
                                </span>
                                <button type="button" className="btn-sm" onClick={() => desmarcarEliminado(key)}
                                    style={{ fontSize: '11px', padding: '3px 10px' }}>
                                    Deshacer
                                </button>
                            </div>
                        );
                    }
                    const idxVisible = cargosVisibles.findIndex(v => getKey(v) === key);
                    return (
                        <div key={key} style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' }}>
                            <span style={{ fontSize: '12px', color: 'var(--gray-500)', minWidth: '20px' }}>
                                {idxVisible + 1}.
                            </span>
                            <div style={{ flex: 1 }}>
                                <CargoDropdown
                                    value={c.value}
                                    onChange={val => setCargo(key, val)}
                                    name={`cargo_${key}`}
                                />
                            </div>
                            <button
                                type="button"
                                className="btn-remove-cargo"
                                disabled={cargosVisibles.length <= 1}
                                title={cargosVisibles.length <= 1 ? 'Debe haber al menos un cargo' : 'Quitar este cargo'}
                                onClick={() => setConfirmEliminar(key)}>
                                <IconTrash />
                            </button>
                        </div>
                    );
                })}

                {confirmEliminar && (
                    <div className="confirm-cargo-inline">
                        <span>¿Quitar este cargo? Se eliminará al guardar los cambios.</span>
                        <div style={{ display: 'flex', gap: '8px', marginTop: '8px' }}>
                            <button type="button" className="btn-sm danger" onClick={() => marcarEliminado(confirmEliminar)}>
                                <IconTrash /> Sí, quitar
                            </button>
                            <button type="button" className="btn-sm" onClick={() => setConfirmEliminar(null)}>
                                <IconX /> Cancelar
                            </button>
                        </div>
                    </div>
                )}

                {cargosVisibles.length < 5 && (
                    <button type="button" className="btn-add-cargo" onClick={agregarNuevoCargo}>
                        + Agregar otro cargo
                    </button>
                )}
            </div>

            {/* Ubicación */}
            <div className="form-row" style={{ marginBottom: '14px' }}>
                <div className="form-group">
                    <label className="form-label">Ubicación</label>
                    <input name="location" value={ubicacion} maxLength={100}
                        placeholder="Ej. Cochabamba, Bolivia" className="form-input"
                        onChange={e => setUbicacion(e.target.value)} />
                    <div className="char-counter">{ubicacion.length}/100</div>
                </div>
            </div>

            {/* Fechas */}
            <ToggleActual
                defaultChecked={grupo.is_current}
                inicioDia={getDia(rep.start_date)}  inicioMes={getMes(rep.start_date)}  inicioAnio={getAnio(rep.start_date)}
                finDia={getDia(rep.end_date)}        finMes={getMes(rep.end_date)}        finAnio={getAnio(rep.end_date)}
                errorFin={errorFin}
            />

            {/* ── Descripción con Quill ── */}
            <div className="form-group" style={{ marginBottom: '14px' }}>
                <label className="form-label">Descripción</label>

                {/* Contador de caracteres sobre el editor */}
                <div className="char-counter" style={{ textAlign: 'right', marginBottom: '4px' }}>
                    {descCharCount} / {MAX_DESC_CHARS} caracteres
                </div>

                {/* El div donde Quill se montará */}
                <div ref={quillContainerRef} className="quill-editor-wrap" />
            </div>

            <div className="btn-row" style={{ marginTop: 0 }}>
                <button type="submit" className="btn-sm primary" disabled={guardando}>
                    {guardando ? 'Guardando...' : 'Guardar cambios'}
                </button>
                <button type="button" className="btn-sm" onClick={onCancelar} disabled={guardando}>
                    Cancelar
                </button>
            </div>
        </form>
    );
}

/* ── Ver más / Ver menos ────────────────────────────────── */
const DESC_LIMIT = 120;
function DescripcionExpandible({ texto }) {
    const [expandida, setExpandida] = useState(false);
    if (!texto) return null;

    /* Si el texto es HTML (tiene tags), renderizar como HTML */
    const esHTML = /<[a-z][\s\S]*>/i.test(texto);

    if (esHTML) {
        /* Extraer texto plano para saber si es largo */
        const tmp = document.createElement('div');
        tmp.innerHTML = texto;
        const plain = tmp.textContent || '';
        const corta = plain.length > DESC_LIMIT;

        return (
            <div className="historial-desc">
                {corta && !expandida
                    ? (
                        <>
                            <div
                                className="quill-rendered-content"
                                dangerouslySetInnerHTML={{
                                    __html: texto.slice(0, texto.indexOf(' ', DESC_LIMIT)) || texto
                                }}
                                style={{ display: 'inline' }}
                            />
                            <span>…</span>
                        </>
                    )
                    : (
                        <div
                            className="quill-rendered-content"
                            dangerouslySetInnerHTML={{ __html: texto }}
                        />
                    )
                }
                {corta && (
                    <button type="button" className="btn-ver-mas" onClick={() => setExpandida(v => !v)}>
                        {expandida ? ' Ver menos' : ' Ver más'}
                    </button>
                )}
            </div>
        );
    }

    /* Texto plano (compatibilidad con registros antiguos) */
    const corta = texto.length > DESC_LIMIT;
    return (
        <div className="historial-desc">
            {corta && !expandida ? texto.slice(0, DESC_LIMIT) + '…' : texto}
            {corta && (
                <button type="button" className="btn-ver-mas" onClick={() => setExpandida(v => !v)}>
                    {expandida ? ' Ver menos' : ' Ver más'}
                </button>
            )}
        </div>
    );
}

/* ══════════════════════════════════════════════════════════
   Componente principal
   ══════════════════════════════════════════════════════════ */
function ExperienciaLaboral({ experiencias, setExperiencias }) {
    const [editandoGrupo, setEditandoGrupo] = useState(null);
    const [confirmDelete, setConfirmDelete] = useState(null);

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function eliminarGrupo(idRep) {
        const res = await fetch(`/experiencia-laboral/${idRep}?grupo=1`, {
            method:  'DELETE',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        });
        if (res.ok) {
            const grupo = agruparExperiencias(experiencias)
                .find(g => g.items.some(i => i.id === idRep));
            if (grupo) {
                const idsGrupo = new Set(grupo.items.map(i => i.id));
                setExperiencias(prev => prev.filter(e => !idsGrupo.has(e.id)));
            }
            setConfirmDelete(null);
        }
    }

    function handleGuardado(actualizados, idsEliminados, payloadLocal) {
        setExperiencias(prev => {
            // 1. Quitar los eliminados
            let siguiente = prev.filter(e => !idsEliminados.has(e.id));

            // 2. Actualizar los existentes
            siguiente = siguiente.map(e => {
                const match = actualizados.find(a => a.id === e.id);
                if (match) return match;

                // Si el servidor no devolvió este id en `actualizados` pero sí
                // estaba en el grupo editado, forzar los campos compartidos
                // (description, institution, location, fechas) desde payloadLocal.
                if (payloadLocal && payloadLocal.idsGrupo && payloadLocal.idsGrupo.has(e.id)) {
                    return {
                        ...e,
                        description:  payloadLocal.descripcion   ?? e.description,
                        institution:  payloadLocal.empresa        ?? e.institution,
                        location:     payloadLocal.location       ?? e.location,
                        start_date:   payloadLocal.start_date     ?? e.start_date,
                        end_date:     payloadLocal.end_date       ?? e.end_date,
                        is_current:   payloadLocal.is_current     ?? e.is_current,
                    };
                }
                return e;
            });

            // 3. Agregar nuevos
            const idsExistentes = new Set(prev.map(e => e.id));
            const nuevos = actualizados.filter(a => !idsExistentes.has(a.id));
            return [...siguiente, ...nuevos];
        });
        setEditandoGrupo(null);
    }

    const grupos = agruparExperiencias(experiencias);

    return (
        <div className="historial-list">
            {grupos.length === 0 && (
                <div className="empty-state">Aún no tienes experiencias laborales registradas.</div>
            )}

            {grupos.map((grupo, gi) => (
                <div key={grupo.key} className="historial-card grupo-empresa">

                    <div className="historial-card-header">
                        <span className="historial-index">#{gi + 1}</span>
                        {grupo.is_current && <span className="badge-actual">Trabajo actual</span>}
                    </div>

                    {editandoGrupo === grupo.key ? (
                        <FormEdicionGrupo
                            grupo={grupo}
                            token={token}
                            onGuardado={handleGuardado}
                            onCancelar={() => setEditandoGrupo(null)}
                        />
                    ) : (
                        <>
                            <div className="historial-title">{grupo.institution}</div>

                            {grupo.location && (
                                <div className="historial-tags">
                                    <span className="tag tag-gray">{grupo.location}</span>
                                </div>
                            )}

                            <div className="historial-tags">
                                <span className="tag tag-teal">
                                    {fmtFecha(grupo.start_date)} — {grupo.is_current ? 'Actualidad' : fmtFecha(grupo.end_date)}
                                </span>
                            </div>

                            <div className="cargos-grupo-list">
                                {grupo.items.map(exp => (
                                    <div key={exp.id} className="cargo-fila-vista">
                                        <span className="cargo-bullet-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" viewBox="0 0 16 16">
                                                <circle cx="8" cy="8" r="5"/>
                                            </svg>
                                        </span>
                                        <span className="cargo-nombre-vista">{exp.title}</span>
                                    </div>
                                ))}
                            </div>

                            <DescripcionExpandible texto={grupo.description} />

                            <div className="historial-actions">
                                <button className="btn-sm" onClick={() => setEditandoGrupo(grupo.key)}>
                                    <IconEdit /> Editar
                                </button>
                                <button
                                    className="btn-sm danger"
                                    title="Eliminar experiencia"
                                    onClick={() => setConfirmDelete({ id: grupo.items[0].id })}>
                                    <IconTrash /> Eliminar
                                </button>
                            </div>
                        </>
                    )}
                </div>
            ))}

            {/* Modal de confirmación */}
            {confirmDelete && (
                <div className="modal-overlay">
                    <div className="modal-box">
                        <div className="modal-title">¿Eliminar esta experiencia?</div>
                        <div className="modal-desc">
                            Esta acción eliminará <strong>todos los cargos</strong> asociados a
                            esta empresa. No se puede deshacer.
                        </div>
                        <div className="modal-btns">
                            <button
                                className="btn-sm danger-confirm"
                                onClick={() => eliminarGrupo(confirmDelete.id)}>
                                Sí, eliminar
                            </button>
                            <button
                                className="btn-sm cancel-modal"
                                onClick={() => setConfirmDelete(null)}>
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

/* ── Bootstrap ─────────────────────────────────────────── */
const el = document.getElementById('historial-laboral-react');
if (el) {
    const STORAGE_KEY = 'exp_laboral_state';

    /* Leer datos: preferir sessionStorage (estado más reciente) sobre el HTML */
    function getDatos() {
        try {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved) return JSON.parse(saved);
        } catch (_) {}
        return JSON.parse(el.dataset.experiencias || '[]');
    }

    const root = ReactDOM.createRoot(el);

    /* Wrapper que intercepta cambios de estado para persistirlos */
    function AppWrapper() {
        const [experiencias, setExperiencias] = React.useState(getDatos);

        function setYGuardar(fn) {
            setExperiencias(prev => {
                const next = typeof fn === 'function' ? fn(prev) : fn;
                try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(next)); } catch (_) {}
                return next;
            });
        }

        return <ExperienciaLaboral
            experiencias={experiencias}
            setExperiencias={setYGuardar}
        />;
    }

    root.render(<AppWrapper />);

    /* También cubrir el bfcache por si acaso */
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) root.render(<AppWrapper />);
    });
}