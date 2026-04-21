import React, { useState } from 'react';
import ReactDOM from 'react-dom/client';

const MESES = {
    '01': 'Enero',   '02': 'Febrero',  '03': 'Marzo',
    '04': 'Abril',   '05': 'Mayo',     '06': 'Junio',
    '07': 'Julio',   '08': 'Agosto',   '09': 'Septiembre',
    '10': 'Octubre', '11': 'Noviembre','12': 'Diciembre'
};

const ANIO_MAX = new Date().getFullYear();
const DIAS = Array.from({ length: 31 }, (_, i) => String(i + 1).padStart(2, '0'));

function getDia(fecha) {
    if (!fecha) return '';
    return String(fecha).substring(8, 10);
}
function getMes(fecha) {
    if (!fecha) return '';
    return String(fecha).substring(5, 7);
}
function getAnio(fecha) {
    if (!fecha) return '';
    return String(fecha).substring(0, 4);
}

// Formatea "2000-03-15" → "15 de Marzo de 2000"
function fmtFecha(fecha) {
    if (!fecha) return null;
    const dia  = getDia(fecha);
    const mes  = getMes(fecha);
    const anio = getAnio(fecha);
    if (!dia || !mes || !anio) return null;
    return `${parseInt(dia)} de ${MESES[mes] || mes} de ${anio}`;
}

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
                        <SelectDia name="fecha_inicio_dia" defaultValue={inicioDia} />
                        <SelectMes name="fecha_inicio_mes" defaultValue={inicioMes} />
                        <InputAnio name="fecha_inicio_anio" defaultValue={inicioAnio} />
                    </div>
                </div>
                <div className="form-group" style={{ opacity: actual ? 0.4 : 1 }}>
                    <label className="form-label">Fecha de fin</label>
                    <div style={{ display: 'flex', gap: '8px' }}>
                        <SelectDia name="fecha_fin_dia" defaultValue={actual ? '' : finDia} disabled={actual} />
                        <SelectMes name="fecha_fin_mes" defaultValue={actual ? '' : finMes} disabled={actual} />
                        <InputAnio name="fecha_fin_anio" defaultValue={actual ? '' : finAnio} disabled={actual} />
                    </div>
                </div>
            </div>
        </>
    );
}

function ExperienciaLaboral({ experiencias: initialExperiencias }) {
    const [experiencias, setExperiencias]   = useState(initialExperiencias);
    const [editando, setEditando]           = useState(null);
    const [confirmDelete, setConfirmDelete] = useState(null);
    const [errorEdicion, setErrorEdicion]   = useState('');

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function eliminar(id) {
        const res = await fetch(`/experiencia-laboral/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        });
        if (res.ok) {
            setExperiencias(experiencias.filter(e => e.id !== id));
            setConfirmDelete(null);
        }
    }

    async function guardarEdicion(e, id) {
        e.preventDefault();
        setErrorEdicion('');
        const form          = e.target;
        const trabajoActual = form.trabajo_actual.checked;
        const inicioDia     = form.fecha_inicio_dia.value;
        const inicioMes     = form.fecha_inicio_mes.value;
        const inicioAnio    = form.fecha_inicio_anio.value;
        const finDia        = form.fecha_fin_dia?.value;
        const finMes        = form.fecha_fin_mes?.value;
        const finAnio       = form.fecha_fin_anio?.value;

        if (!inicioDia || !inicioMes || !inicioAnio) {
            setErrorEdicion('La fecha de inicio es obligatoria.');
            return;
        }

        if (!trabajoActual && finDia && finMes && finAnio) {
            const inicio = new Date(parseInt(inicioAnio), parseInt(inicioMes)-1, parseInt(inicioDia));
            const fin    = new Date(parseInt(finAnio),    parseInt(finMes)-1,    parseInt(finDia));
            if (fin < inicio) {
                setErrorEdicion('La fecha de fin no puede ser anterior a la fecha de inicio.');
                return;
            }
        }

        const res = await fetch(`/experiencia-laboral/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                empresa:            form.empresa.value,
                cargo:              form.cargo.value,
                location:           form.location.value,
                descripcion:        form.descripcion.value,
                fecha_inicio_dia:   inicioDia,
                fecha_inicio_mes:   inicioMes,
                fecha_inicio_anio:  inicioAnio,
                fecha_fin_dia:      trabajoActual ? null : finDia,
                fecha_fin_mes:      trabajoActual ? null : finMes,
                fecha_fin_anio:     trabajoActual ? null : finAnio,
                trabajo_actual:     trabajoActual,
            }),
        });

        if (res.ok) {
            const actualizado = await res.json();
            setExperiencias(experiencias.map(e => e.id === id ? actualizado : e));
            setEditando(null);
        } else {
            const data = await res.json();
            setErrorEdicion(data.error || 'Error al guardar los cambios.');
        }
    }

    return (
        <div className="historial-list">
            {experiencias.length === 0 && (
                <div className="empty-state">
                    Aún no tienes experiencias laborales registradas.
                </div>
            )}

            {experiencias.map((exp, idx) => (
                <div key={exp.id} className="historial-card">
                    {editando === exp.id ? (
                        <form onSubmit={e => guardarEdicion(e, exp.id)} className="edit-form-wrap">
                            {errorEdicion && <div className="edit-error">{errorEdicion}</div>}

                            <div className="form-row" style={{ marginBottom: '14px' }}>
                                <div className="form-group">
                                    <label className="form-label">Empresa <span className="required">*</span></label>
                                    <input name="empresa" defaultValue={exp.institution} required className="form-input" />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Cargo <span className="required">*</span></label>
                                    <input name="cargo" defaultValue={exp.title} required className="form-input" />
                                </div>
                            </div>

                            <div className="form-row" style={{ marginBottom: '14px' }}>
                                <div className="form-group">
                                    <label className="form-label">Ubicación</label>
                                    <input name="location" defaultValue={exp.location || ''}
                                        placeholder="Ej. Cochabamba, Bolivia" className="form-input" />
                                </div>
                            </div>

                            <ToggleActual
                                defaultChecked={exp.is_current}
                                inicioDia={getDia(exp.start_date)}
                                inicioMes={getMes(exp.start_date)}
                                inicioAnio={getAnio(exp.start_date)}
                                finDia={getDia(exp.end_date)}
                                finMes={getMes(exp.end_date)}
                                finAnio={getAnio(exp.end_date)}
                            />

                            <div className="form-group" style={{ marginBottom: '14px' }}>
                                <label className="form-label">Descripción</label>
                                <textarea name="descripcion" defaultValue={exp.description}
                                    className="form-textarea" rows={3} />
                            </div>

                            <div className="btn-row" style={{ marginTop: 0 }}>
                                <button type="submit" className="btn-sm primary">Guardar cambios</button>
                                <button type="button" className="btn-sm"
                                    onClick={() => { setEditando(null); setErrorEdicion(''); }}>
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    ) : (
                        <>
                            <div className="historial-card-header">
                                <span className="historial-index">#{idx + 1}</span>
                                {exp.is_current && <span className="badge-actual">Trabajo actual</span>}
                            </div>
                            <div className="historial-title">{exp.institution}</div>
                            <div className="historial-subtitle">{exp.title}</div>
                            {exp.location && (
                                <div className="historial-tags">
                                    <span className="tag tag-gray">{exp.location}</span>
                                </div>
                            )}
                            <div className="historial-tags">
                                <span className="tag tag-teal">
                                    {fmtFecha(exp.start_date)} — {exp.is_current ? 'Actualidad' : fmtFecha(exp.end_date)}
                                </span>
                            </div>
                            {exp.description && (
                                <div className="historial-desc">{exp.description}</div>
                            )}
                            <div className="historial-actions">
                                <button className="btn-sm"
                                    onClick={() => { setEditando(exp.id); setErrorEdicion(''); }}>
                                    Editar
                                </button>
                                <button className="btn-sm danger"
                                    onClick={() => setConfirmDelete(exp.id)}>
                                    Eliminar
                                </button>
                            </div>
                        </>
                    )}
                </div>
            ))}

            {confirmDelete && (
                <div className="modal-overlay">
                    <div className="modal-box">
                        <div className="modal-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                stroke="#e74c3c" strokeWidth="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4h6v2"/>
                            </svg>
                        </div>
                        <div className="modal-title">¿Eliminar esta experiencia?</div>
                        <div className="modal-desc">
                            Esta acción no se puede deshacer. La experiencia dejará de aparecer en tu portafolio público.
                        </div>
                        <div className="modal-btns">
                            <button className="btn-sm" onClick={() => setConfirmDelete(null)}>Cancelar</button>
                            <button className="btn-sm danger" onClick={() => eliminar(confirmDelete)}>
                                Sí, eliminar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

const el = document.getElementById('historial-laboral-react');
if (el) {
    const data = JSON.parse(el.dataset.experiencias || '[]');
    ReactDOM.createRoot(el).render(<ExperienciaLaboral experiencias={data} />);
}