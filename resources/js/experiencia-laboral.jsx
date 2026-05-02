import React, { useState, useRef, useEffect } from 'react';
import ReactDOM from 'react-dom/client';

/* ── Constantes ─────────────────────────────────────────── */
const MESES = {
    '01': 'Enero',   '02': 'Febrero',  '03': 'Marzo',
    '04': 'Abril',   '05': 'Mayo',     '06': 'Junio',
    '07': 'Julio',   '08': 'Agosto',   '09': 'Septiembre',
    '10': 'Octubre', '11': 'Noviembre','12': 'Diciembre'
};
const ANIO_MAX = new Date().getFullYear();
const DIAS     = Array.from({ length: 31 }, (_, i) => String(i + 1).padStart(2, '0'));

const CARGO_OPTIONS = [
    'Frontend Developer',
    'Backend Developer',
    'Full Stack Developer',
    'UI/UX Designer',
    'DevOps Engineer',
    'Mobile Developer',
    'Project Manager',
    'QA Tester',
    'Database Administrator',
    'Technical Leader',
    'Data Analyst',
    'Scrum Master',
    'Product Owner',
];

/* ── Iconos Bootstrap como SVG inline ─────────────────────
   (no requiere ninguna dependencia extra)                   */
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

const IconCheck = () => (
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style={{flexShrink:0}}>
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
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

/* ── Selects de fecha ───────────────────────────────────── */
function SelectDia({ name, defaultValue, disabled }) {
    return (
        <select name={name} defaultValue={defaultValue || ''} disabled={disabled}
            className="form-input" style={{ width: '80px' }}>
            <option value="">Día</option>
            {DIAS.map(d => <option key={d} value={d}>{parseInt(d)}</option>)}
        </select>
    );
}
function SelectMes({ name, defaultValue, disabled }) {
    return (
        <select name={name} defaultValue={defaultValue || ''} disabled={disabled}
            className="form-input" style={{ flex: 1 }}>
            <option value="">Mes</option>
            {Object.entries(MESES).map(([num, nom]) => (
                <option key={num} value={num}>{nom}</option>
            ))}
        </select>
    );
}
function InputAnio({ name, defaultValue, disabled }) {
    return (
        <input type="number" name={name} defaultValue={defaultValue || ''}
            disabled={disabled} className="form-input" style={{ width: '90px' }}
            placeholder="Año" min="1950" max={ANIO_MAX} />
    );
}

/* ── Toggle trabajo actual ──────────────────────────────── */
function ToggleActual({ defaultChecked, inicioDia, inicioMes, inicioAnio, finDia, finMes, finAnio }) {
    const [actual, setActual] = useState(defaultChecked || false);
    return (
        <>
            <div className="form-checkbox-row" style={{ marginBottom: '14px' }}>
                <input type="checkbox" name="trabajo_actual" checked={actual}
                    onChange={e => setActual(e.target.checked)} />
                <label>Trabajo actual</label>
            </div>
            <div className="form-row" style={{ marginBottom: '14px' }}>
                <div className="form-group">
                    <label className="form-label">Fecha de inicio <span className="required">*</span></label>
                    <div style={{ display: 'flex', gap: '8px' }}>
                        <SelectDia  name="fecha_inicio_dia"  defaultValue={inicioDia} />
                        <SelectMes  name="fecha_inicio_mes"  defaultValue={inicioMes} />
                        <InputAnio  name="fecha_inicio_anio" defaultValue={inicioAnio} />
                    </div>
                </div>
                <div className="form-group" style={{ opacity: actual ? 0.4 : 1 }}>
                    <label className="form-label">Fecha de fin</label>
                    <div style={{ display: 'flex', gap: '8px' }}>
                        <SelectDia  name="fecha_fin_dia"  defaultValue={actual ? '' : finDia}  disabled={actual} />
                        <SelectMes  name="fecha_fin_mes"  defaultValue={actual ? '' : finMes}  disabled={actual} />
                        <InputAnio  name="fecha_fin_anio" defaultValue={actual ? '' : finAnio} disabled={actual} />
                    </div>
                </div>
            </div>
        </>
    );
}

/* ══════════════════════════════════════════════════════════
   FormEdicionGrupo — edita TODOS los cargos + datos comunes
   de un grupo en un solo formulario
   ══════════════════════════════════════════════════════════ */
function FormEdicionGrupo({ grupo, token, onGuardado, onCancelar }) {
    const [error, setError] = useState('');

    // Estado para cada cargo del grupo
    const [cargos, setCargos] = useState(
        grupo.items.map(exp => ({ id: exp.id, value: exp.title || '' }))
    );

    function setCargo(id, val) {
        setCargos(prev => prev.map(c => c.id === id ? { ...c, value: val } : c));
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setError('');
        const form = e.target;

        // Validar que todos los cargos estén seleccionados
        for (const c of cargos) {
            if (!c.value) { setError('Debes seleccionar un cargo en cada campo.'); return; }
        }

        // Validar que no haya cargos duplicados
        const valores = cargos.map(c => c.value.toLowerCase());
        const duplicado = valores.find((v, i) => valores.indexOf(v) !== i);
        if (duplicado) {
            const nombreDup = cargos.find(c => c.value.toLowerCase() === duplicado).value;
            setError(`El cargo "${nombreDup}" está duplicado. Cada cargo debe ser único.`);
            return;
        }

        const trabajoActual = form.trabajo_actual.checked;
        const inicioDia  = form.fecha_inicio_dia.value;
        const inicioMes  = form.fecha_inicio_mes.value;
        const inicioAnio = form.fecha_inicio_anio.value;
        const finDia     = form.fecha_fin_dia?.value;
        const finMes     = form.fecha_fin_mes?.value;
        const finAnio    = form.fecha_fin_anio?.value;

        if (!inicioDia || !inicioMes || !inicioAnio) {
            setError('La fecha de inicio es obligatoria.'); return;
        }
        if (!trabajoActual && finDia && finMes && finAnio) {
            const ini = new Date(parseInt(inicioAnio), parseInt(inicioMes)-1, parseInt(inicioDia));
            const fin = new Date(parseInt(finAnio),    parseInt(finMes)-1,    parseInt(finDia));
            if (fin < ini) { setError('La fecha de fin no puede ser anterior a la de inicio.'); return; }
        }

        // Actualizar cada cargo con PUT individual
        const empresa    = form.empresa.value;
        const location   = form.location.value;
        const descripcion = form.descripcion.value;

        try {
            const resultados = await Promise.all(
                cargos.map(c =>
                    fetch(`/experiencia-laboral/${c.id}`, {
                        method:  'PUT',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            empresa,
                            cargo:             c.value,
                            location,
                            descripcion,
                            fecha_inicio_dia:  inicioDia,
                            fecha_inicio_mes:  inicioMes,
                            fecha_inicio_anio: inicioAnio,
                            fecha_fin_dia:     trabajoActual ? null : finDia,
                            fecha_fin_mes:     trabajoActual ? null : finMes,
                            fecha_fin_anio:    trabajoActual ? null : finAnio,
                            trabajo_actual:    trabajoActual,
                        }),
                    }).then(r => r.ok ? r.json() : r.json().then(d => Promise.reject(d)))
                )
            );
            onGuardado(resultados);
        } catch (err) {
            if (err.errors) setError(Object.values(err.errors).join(' — '));
            else setError(err.error || 'Error al guardar los cambios.');
        }
    }

    const rep = grupo.items[0]; // representante para fechas y datos comunes

    return (
        <form onSubmit={handleSubmit} className="edit-form-wrap">
            {error && <div className="edit-error">{error}</div>}

            {/* Empresa */}
            <div className="form-row" style={{ marginBottom: '14px' }}>
                <div className="form-group">
                    <label className="form-label">Empresa <span className="required">*</span></label>
                    <input name="empresa" defaultValue={grupo.institution} required className="form-input" />
                </div>
            </div>

            {/* Cargos — uno por fila */}
            <div className="form-group" style={{ marginBottom: '14px' }}>
                <label className="form-label">
                    Cargos <span className="required">*</span>
                </label>
                {cargos.map((c, idx) => (
                    <div key={c.id} style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' }}>
                        <span style={{ fontSize: '12px', color: 'var(--gray-500)', minWidth: '20px' }}>
                            {idx + 1}.
                        </span>
                        <div style={{ flex: 1 }}>
                            <CargoDropdown
                                value={c.value}
                                onChange={val => setCargo(c.id, val)}
                                name={`cargo_${c.id}`}
                            />
                        </div>
                    </div>
                ))}
            </div>

            {/* Ubicación */}
            <div className="form-row" style={{ marginBottom: '14px' }}>
                <div className="form-group">
                    <label className="form-label">Ubicación</label>
                    <input name="location" defaultValue={grupo.location || ''}
                        placeholder="Ej. Cochabamba, Bolivia" className="form-input" />
                </div>
            </div>

            {/* Fechas + trabajo actual */}
            <ToggleActual
                defaultChecked={grupo.is_current}
                inicioDia={getDia(rep.start_date)}  inicioMes={getMes(rep.start_date)}  inicioAnio={getAnio(rep.start_date)}
                finDia={getDia(rep.end_date)}        finMes={getMes(rep.end_date)}        finAnio={getAnio(rep.end_date)}
            />

            {/* Descripción */}
            <div className="form-group" style={{ marginBottom: '14px' }}>
                <label className="form-label">Descripción</label>
                <textarea name="descripcion" defaultValue={grupo.description}
                    className="form-textarea" rows={3} />
            </div>

            <div className="btn-row" style={{ marginTop: 0 }}>
                <button type="submit" className="btn-sm primary">
                    <IconCheck /> Guardar cambios
                </button>
                <button type="button" className="btn-sm" onClick={onCancelar}>
                    <IconX /> Cancelar
                </button>
            </div>
        </form>
    );
}

/* ══════════════════════════════════════════════════════════
   Componente principal
   ══════════════════════════════════════════════════════════ */
function ExperienciaLaboral({ experiencias: initialExperiencias }) {
    const [experiencias,   setExperiencias]   = useState(initialExperiencias);
    const [editandoGrupo,  setEditandoGrupo]  = useState(null); // key del grupo en edición
    const [confirmDelete,  setConfirmDelete]  = useState(null);

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    /* Eliminar grupo completo */
    async function eliminarGrupo(idRep) {
        const res = await fetch(`/experiencia-laboral/${idRep}`, {
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

    /* Recibir array de registros actualizados (uno por cargo) */
    function handleGuardado(actualizados) {
        setExperiencias(prev =>
            prev.map(e => {
                const match = actualizados.find(a => a.id === e.id);
                return match ?? e;
            })
        );
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

                    {/* Cabecera */}
                    <div className="historial-card-header">
                        <span className="historial-index">#{gi + 1}</span>
                        {grupo.is_current && <span className="badge-actual">Trabajo actual</span>}
                    </div>

                    {editandoGrupo === grupo.key ? (
                        /* ── Modo edición ── */
                        <FormEdicionGrupo
                            grupo={grupo}
                            token={token}
                            onGuardado={handleGuardado}
                            onCancelar={() => setEditandoGrupo(null)}
                        />
                    ) : (
                        /* ── Modo vista ── */
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

                            {/* Lista de cargos — solo lectura, sin botón Editar individual */}
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

                            {grupo.description && (
                                <div className="historial-desc">{grupo.description}</div>
                            )}

                            {/* Acciones: UN solo Editar + Eliminar con iconos */}
                            <div className="historial-actions">
                                <button className="btn-sm"
                                    onClick={() => setEditandoGrupo(grupo.key)}>
                                    <IconEdit /> Editar
                                </button>
                                <button className="btn-sm danger"
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
                        <div className="modal-icon">
                            <IconTrash />
                        </div>
                        <div className="modal-title">¿Eliminar esta experiencia?</div>
                        <div className="modal-desc">
                            Esta acción eliminará <strong>todos los cargos</strong> asociados a esta empresa. No se puede deshacer.
                        </div>
                        <div className="modal-btns">
                            <button className="btn-sm" onClick={() => setConfirmDelete(null)}>
                                <IconX /> Cancelar
                            </button>
                            <button className="btn-sm danger" onClick={() => eliminarGrupo(confirmDelete.id)}>
                                <IconTrash /> Sí, eliminar
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
    const data = JSON.parse(el.dataset.experiencias || '[]');
    ReactDOM.createRoot(el).render(<ExperienciaLaboral experiencias={data} />);
}