import React, { useState, useEffect, useRef } from 'react';
import ReactDOM from 'react-dom/client';


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

function DescripcionColapsable({ texto, limite = 150 }) {
    const [expandido, setExpandido] = useState(false);
    const esMuyLargo = texto.length > limite;

    return (
        <div>
            <div className="historial-desc" style={{
                display: '-webkit-box',
                WebkitLineClamp: expandido ? 'unset' : 3,
                WebkitBoxOrient: 'vertical',
                overflow: expandido ? 'visible' : 'hidden',
            }}>
                {texto}
            </div>
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

function HistorialAcademico({ formaciones: initialFormaciones }) {
    const [formaciones, setFormaciones] = useState(initialFormaciones);
    const [editando, setEditando] = useState(null);
    const [confirmDelete, setConfirmDelete] = useState(null);

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    useEffect(() => {
        if (editando) {
            // Configurar contador para descripción
            const descripcion = document.querySelector(`#edit-descripcion-${editando}`);
            const counterDesc = document.getElementById(`descripcionCounter_${editando}`);
            
            if (descripcion && counterDesc) {
                const updateDesc = () => {
                    counterDesc.textContent = `${descripcion.value.length} / 500`;
                };
                descripcion.addEventListener('input', updateDesc);
                updateDesc();
            }

            // Configurar contador para institución
            const institucion = document.querySelector(`input[name="institucion"]`);
            const counterInst = document.getElementById(`institucionCounter_${editando}`);
            
            if (institucion && counterInst) {
                const updateInst = () => {
                    counterInst.textContent = `${institucion.value.length} / 60`;
                };
                institucion.addEventListener('input', updateInst);
                updateInst();
            }

            // Configurar contador para título obtenido
            const tituloObtenido = document.querySelector(`input[name="titulo_obtenido"]`);
            const counterTit = document.getElementById(`tituloCounter_${editando}`);
            
            if (tituloObtenido && counterTit) {
                const updateTit = () => {
                    counterTit.textContent = `${tituloObtenido.value.length} / 30`;
                };
                tituloObtenido.addEventListener('input', updateTit);
                updateTit();
            }
        }
    }, [editando]);

    // ── ELIMINAR ──
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

    // ── EDITAR ──
    async function guardarEdicion(e, id) {
        e.preventDefault();
        const form = new FormData(e.target);
        const data = Object.fromEntries(form.entries());

        const res = await fetch(`/informacion-academica/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                institucion:     data.institucion,
                titulo_obtenido: data.titulo_obtenido,
                fecha_inicio:    data.fecha_inicio,
                fecha_fin:       data.fecha_fin || null,
                estudio_actual:  data.estudio_actual ? 1 : 0,
                descripcion:     data.descripcion,
            }),
        });

        if (res.ok) {
            const updated = await res.json();
            setFormaciones(formaciones.map(f => f.id === id ? updated : f));
            setEditando(null);
        }else if (res.status === 422) {
            const errores = await res.json();
            const msgs = Object.values(errores.errors).flat().join('\n');
            alert(msgs);
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
                            // ── MODO EDICIÓN ──
                            <form onSubmit={(e) => guardarEdicion(e, f.id)}>
                                <div className="form-row" style={{ marginBottom: '12px' }}>
                                    <div className="form-group">
                                        <label className="form-label">Institución <span className="required">*</span></label>
                                        <input className="form-input" name="institucion" defaultValue={f.institution} required maxLength= "30" />
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">Título obtenido <span className="required">*</span></label>
                                        <input className="form-input" name="titulo_obtenido" defaultValue={f.title} required maxLength="30" />
                                    </div>
                                </div>

                                <div className="form-row" style={{ marginBottom: '12px' }}>
                                    <div className="form-group">
                                        <label className="form-label">Título (Licenciatura, Maestría...)</label>
                                        <input className="form-input" name="titulo" defaultValue={f.degree || ''} maxLength="30" />
                                        
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">Especialidad</label>
                                        <input className="form-input" name="especialidad" defaultValue={f.specialty || ''} maxLength="30" />
                                        
                                    </div>
                                </div>
                                <div className="form-row" style={{ marginBottom: '12px' }}>
                                    <div className="form-group">
                                        <label className="form-label">Fecha de inicio</label>
                                        <input className="form-input" type="date" name="fecha_inicio"
                                            defaultValue={f.start_date ? f.start_date.substring(0, 10) : ''} 
                                            min="1950-01-01" max={new Date().toISOString().substring(0, 10)} required />
                                    </div>
                                    <div className="form-group">
                                        <label className="form-label">Fecha de fin</label>
                                        <input className="form-input" type="date" name="fecha_fin"  id={`fechaFin_${f.id}`}
                                            defaultValue={f.end_date ? f.end_date.substring(0, 10) : ''} 
                                            min="1950-01-01" max={new Date().toISOString().substring(0, 10)} />
                                    </div>
                                </div>
                                <div className="form-checkbox-row" style={{ marginBottom: '12px' }}>
                                    <input 
                                        type="checkbox" 
                                        name="estudio_actual" 
                                        defaultChecked={f.is_current}
                                        onChange={(e) => {
                                            // Usa el ID específico en lugar de querySelector
                                            const fechaFinInput = document.getElementById(`fechaFin_${f.id}`);
                                            if (fechaFinInput) {
                                                if (e.target.checked) {
                                                    fechaFinInput.value = '';
                                                    fechaFinInput.disabled = true;
                                                } else {
                                                    fechaFinInput.disabled = false;
                                                }
                                            }
                                        }}
                                    />
                                    <label>Estudio actual</label>
                                </div>
                                <div className="form-group" style={{ marginBottom: '12px' }}>
                                    <label className="form-label">Descripción</label>
                                    <textarea className="form-textarea" name="descripcion"  id={`edit-descripcion-${f.id}`} defaultValue={f.description} maxLength="500" rows="4"
                                        onInput={(e) => {
                                            const counter = document.getElementById(`descripcionCounter_${f.id}`);
                                            if (counter) counter.textContent = `${e.target.value.length} / 500`;
                                        }} />
                                    <div id={`descripcionCounter_${f.id}`} className="char-counter">
                                        {f.description?.length || 0} / 500
                                    </div>
                                </div>
                                <div className="historial-actions">
                                    <button type="submit" className="btn-sm btn-guardar">
                                        Guardar cambios
                                    </button>
                                    <button type="button" className="btn-sm" onClick={() => setEditando(null)}>
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        ) : (
                            // ── MODO VISTA ──
                            <>
                                <div className="historial-card-header">
                                    <span className="historial-index">{String(index + 1).padStart(2, '0')}</span>
                                    {f.is_current && <span className="badge-actual">Actual</span>}
                                </div>
                                <div className="historial-title">{f.title}</div>
                                <div className="historial-subtitle">{f.institution}</div>
                                <div className="historial-tags">
                                    <span className="tag tag-teal">
                                        {f.start_date?.substring(0, 7)} – {f.is_current ? 'Presente' : (f.end_date?.substring(0, 7) || '')}
                                    </span>
                                    <span className="tag tag-gray">{f.is_current ? 'En curso' : 'Finalizado'}</span>
                                </div>
                                {f.description && (
                                    <DescripcionColapsable texto={f.description} />
                                )}

                                <div className="historial-actions">
                                    <button className="btn-sm" onClick={() => setEditando(f.id)}>
                                        <IconEdit /> Editar
                                    </button>
                                    <button className="btn-sm danger" onClick={() => setConfirmDelete(f.id)}>
                                        <IconTrash /> Eliminar
                                    </button>
                                </div>
                            </>
                        )}
                    </div>
                ))}
            </div>
            {confirmDelete && (
                <div className="modal-overlay" onClick={() => setConfirmDelete(null)}>
                    <div className="modal-box" onClick={e => e.stopPropagation()}>
                        <p className="modal-title">Eliminar formación</p>
                        <p className="modal-text">
                            ¿Estás seguro de que deseas eliminar este registro?<br/>
                            <span>Esta acción no se puede deshacer.</span>
                        </p>
                        <div className="modal-actions">
                            <button className="btn-sm btn-eliminar" onClick={() => eliminar(confirmDelete)}>
                                Sí, eliminar
                            </button>
                            <button className="btn-sm" onClick={() => setConfirmDelete(null)}>
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}



// Montar el componente
const el = document.getElementById('historial-react');
if (el) {
    const formaciones = JSON.parse(el.dataset.formaciones || '[]');
    ReactDOM.createRoot(el).render(<HistorialAcademico formaciones={formaciones} />);
}