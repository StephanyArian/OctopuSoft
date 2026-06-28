// ============================================================
// FUNCIONES GENERALES
// ============================================================
function toggleDesc(id, btn) {
    var el = document.getElementById(id);
    if (el.classList.contains('collapsed')) {
        el.classList.replace('collapsed', 'expanded');
        btn.textContent = 'Ver menos';
    } else {
        el.classList.replace('expanded', 'collapsed');
        btn.textContent = 'Ver más';
    }
}

function toggleFabMenu() {
    var menu = document.getElementById('fabMenu');
    var icon = document.getElementById('fabIcon');
    var isOpen = menu.classList.contains('open');
    menu.classList.toggle('open', !isOpen);
    icon.className = isOpen ? 'fas fa-ellipsis-h' : 'fas fa-times';
}

document.addEventListener('click', function(e) {
    var container = document.getElementById('fabContainer');
    if (container && !container.contains(e.target)) {
        document.getElementById('fabMenu').classList.remove('open');
        document.getElementById('fabIcon').className = 'fas fa-ellipsis-h';
    }
});

// ============================================================
// LIGHTBOX
// ============================================================
function abrirLightbox(src) {
    var lb = document.getElementById('lightbox-modal');
    if (!lb) {
        lb = document.createElement('div');
        lb.id = 'lightbox-modal';
        lb.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.9);z-index:20000;align-items:center;justify-content:center;cursor:pointer;';
        lb.innerHTML = '<div style="position:relative;max-width:90vw;max-height:90vh;"><img id="lightbox-img" style="max-width:100%;max-height:90vh;object-fit:contain;border-radius:8px;"><button id="lightbox-close" style="position:absolute;top:-40px;right:0;background:rgba(0,0,0,0.5);border:none;color:white;font-size:28px;cursor:pointer;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;">✕</button></div>';
        document.body.appendChild(lb);
        lb.addEventListener('click', function(e) { if (e.target === lb || e.target.id === 'lightbox-close') lb.style.display = 'none'; });
    }
    document.getElementById('lightbox-img').src = src;
    lb.style.display = 'flex';
}

// ============================================================
// MODAL DETALLE EXPERIENCIA
// ============================================================
function abrirDetalleExp(i) {
    var d = window.previewExperienciasData[i];
    if (!d) return;
    document.getElementById('det-exp-empresa').textContent = d.empresa;
    document.getElementById('det-exp-cargo').textContent = d.cargo;
    var fecha = d.fecha_inicio;
    if (d.fecha_fin) fecha += ' — ' + d.fecha_fin;
    else if (d.trabajo_actual) fecha += ' — Actualidad';
    document.getElementById('det-exp-fecha-txt').textContent = fecha;
    var ubicPill = document.getElementById('det-exp-ubicacion-pill');
    if (d.ubicacion) {
        document.getElementById('det-exp-ubicacion-txt').textContent = d.ubicacion;
        ubicPill.style.display = 'inline-flex';
    } else {
        ubicPill.style.display = 'none';
    }
    document.getElementById('det-exp-desc-inner').innerHTML = d.descripcion || '<em style="color:#94a3b8;">Sin descripción disponible.</em>';
    document.getElementById('modal-detalle-exp').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarDetalleExp() {
    document.getElementById('modal-detalle-exp').style.display = 'none';
    document.body.style.overflow = '';
}

// ============================================================
// MODAL DETALLE ACADÉMICA
// ============================================================
function abrirDetalleAca(i) {
    var d = window.previewAcademicasData[i];
    if (!d) return;
    document.getElementById('det-aca-inst').textContent = d.institucion;
    document.getElementById('det-aca-titulo').textContent = d.titulo;
    var fecha = d.fecha_inicio;
    if (d.fecha_fin) fecha += ' — ' + d.fecha_fin;
    else if (d.estudio_actual) fecha += ' — Actualidad';
    document.getElementById('det-aca-fecha-txt').textContent = fecha;
    var spPill = document.getElementById('det-aca-specialty-pill');
    if (d.specialty) {
        document.getElementById('det-aca-specialty-txt').textContent = d.specialty;
        spPill.style.display = 'inline-flex';
    } else {
        spPill.style.display = 'none';
    }
    document.getElementById('det-aca-desc').innerHTML = d.descripcion || '<em style="color:#94a3b8;">Sin descripción disponible.</em>';
    var certsDiv = document.getElementById('det-aca-certs');
    certsDiv.innerHTML = '';
    if (d.evidencias && d.evidencias.length) {
        certsDiv.style.display = 'flex';
        d.evidencias.forEach(function(ev) {
            if (ev.ext === 'pdf') {
                certsDiv.innerHTML += '<a href="' + ev.url + '" target="_blank" style="display:inline-flex;align-items:center;gap:6px;color:#0abf9e;font-size:12px;font-weight:600;text-decoration:none;padding:6px 14px;border-radius:20px;background:#f0fdf9;border:1px solid #d1fae5;" onmouseover="this.style.background=\'#d1fae5\'" onmouseout="this.style.background=\'#f0fdf9\'"><i class="fas fa-file-pdf"></i> Ver PDF</a>';
            } else {
               certsDiv.innerHTML += '<button onclick="document.getElementById(\'modal-detalle-aca\').style.display=\'none\'; abrirLightbox(\'' + ev.url + '\')" style="display:inline-flex;align-items:center;gap:6px;color:#0abf9e;font-size:12px;font-weight:600;cursor:pointer;padding:6px 14px;border-radius:20px;background:#f0fdf9;border:1px solid #d1fae5;font-family:inherit;" onmouseover="this.style.background=\'#d1fae5\'" onmouseout="this.style.background=\'#f0fdf9\'"><i class="fas fa-certificate"></i> Ver certificado</button>';
            }
        });
    } else {
        certsDiv.style.display = 'none';
    }
    document.getElementById('modal-detalle-aca').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarDetalleAca() {
    document.getElementById('modal-detalle-aca').style.display = 'none';
    document.body.style.overflow = '';
}

// ============================================================
// MODAL PROYECTO
// ============================================================
function previewEscapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function abrirModal(data) {
    document.getElementById('modal-nombre').textContent = data.nombre;

    var badgesDiv = document.getElementById('modal-badges');
    badgesDiv.innerHTML = '';
    var badge = function(t, bg, c) {
        return '<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:'+bg+';color:'+c+';">'+t+'</span>';
    };
    var badgeIcon = function(ic, t, bg, c) {
        return '<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:'+bg+';color:'+c+';"><i class="'+ic+'" style="font-size:10px;"></i>'+t+'</span>';
    };
    if (data.estado) {
        var bg = data.estado==='Completado'?'#d1fae5':data.estado==='En curso'?'#fef3c7':'#f1f5f9';
        var c  = data.estado==='Completado'?'#065f46':data.estado==='En curso'?'#92400e':'#64748b';
        badgesDiv.innerHTML += badge(previewEscapeHtml(data.estado), bg, c);
    }
    if (data.rol)    badgesDiv.innerHTML += badgeIcon('fas fa-user-check', previewEscapeHtml(data.rol),    '#ede9fe','#5b21b6');
    if (data.cliente) badgesDiv.innerHTML += badgeIcon('fas fa-building',  previewEscapeHtml(data.cliente),'#f1f5f9','#475569');

    var fechasDiv = document.getElementById('modal-fechas');
    fechasDiv.innerHTML = '';
    if (data.fecha_inicio) fechasDiv.innerHTML += '<span><i class="far fa-calendar-alt" style="color:#94a3b8;margin-right:4px;"></i>Inicio: <strong>'+previewEscapeHtml(data.fecha_inicio)+'</strong></span>';
    if (data.fecha_fin)    fechasDiv.innerHTML += '<span><i class="far fa-calendar-alt" style="color:#94a3b8;margin-right:4px;"></i>Fin: <strong>'+previewEscapeHtml(data.fecha_fin)+'</strong></span>';

    document.getElementById('modal-descripcion').innerHTML = data.descripcion || '';

    var tecDiv     = document.getElementById('modal-tecnologias');
    var tecSection = document.getElementById('modal-tec-section');
    tecDiv.innerHTML = '';
    if (data.tecnologias && data.tecnologias.length) {
        tecSection.style.display = 'block';
        data.tecnologias.forEach(function(t) { tecDiv.innerHTML += '<span class="tec-badge">'+previewEscapeHtml(t)+'</span>'; });
    } else { tecSection.style.display = 'none'; }

    var evDiv = document.getElementById('modal-evidencias-lista');
    var evSec = document.getElementById('modal-evidencias');
    evDiv.innerHTML = '';
    if (data.evidencias && data.evidencias.length) {
        evSec.style.display = 'block';
        document.getElementById('modal-ev-count').textContent = '(' + data.evidencias.length + ')';
        data.evidencias.forEach(function(ev) {
            var item = document.createElement('div');
            if (ev.tipo === 'imagen' && ev.imagen) {
                item.style.cssText = 'background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;';
                item.innerHTML =
                    '<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">' +
                        '<div style="width:36px;height:36px;background:#0abf9e15;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image" style="color:#0abf9e;font-size:18px;"></i></div>' +
                        '<strong style="font-size:14px;color:#0f172a;flex:1;">Imagen del proyecto</strong>' +
                        '<button onclick="abrirLightbox(\''+ev.imagen+'\')" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:40px;border:1.5px solid #0abf9e;background:#f0fdf9;color:#0abf9e;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background=\'#0abf9e\';this.style.color=\'white\';" onmouseout="this.style.background=\'#f0fdf9\';this.style.color=\'#0abf9e\';"><i class="fas fa-eye"></i> Ver</button>' +
                    '</div>' +
                    '<div style="padding:16px;cursor:pointer;" onclick="abrirLightbox(\''+ev.imagen+'\')">' +
                        '<img src="'+ev.imagen+'" style="width:100%;max-height:220px;object-fit:cover;border-radius:12px;" onmouseover="this.style.opacity=\'0.85\'" onmouseout="this.style.opacity=\'1\'">' +
                    '</div>';
            } else if (ev.tipo === 'enlace' && ev.url) {
                item.style.cssText = 'background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;';
                item.innerHTML =
                    '<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">' +
                        '<div style="width:36px;height:36px;background:#0abf9e15;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-link" style="color:#0abf9e;font-size:18px;"></i></div>' +
                        '<strong style="font-size:14px;color:#0f172a;flex:1;">'+previewEscapeHtml(ev.titulo||'Enlace')+'</strong>' +
                    '</div>' +
                    '<div style="padding:16px;display:flex;align-items:center;justify-content:space-between;gap:10px;">' +
                        '<span style="font-size:12px;color:#94a3b8;word-break:break-all;flex:1;">'+previewEscapeHtml(ev.url)+'</span>' +
                        '<a href="'+ev.url+'" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:40px;background:#0abf9e;color:white;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;transition:background 0.2s;" onmouseover="this.style.background=\'#07866e\';" onmouseout="this.style.background=\'#0abf9e\';"><i class="fas fa-eye"></i> Ver enlace</a>' +
                    '</div>';
            } else if (ev.tipo === 'repositorio' && ev.url) {
                var icon = ev.plataforma==='GitLab'?'fa-gitlab':ev.plataforma==='Bitbucket'?'fa-bitbucket':'fa-github';
                item.style.cssText = 'background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;';
                item.innerHTML =
                    '<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">' +
                        '<div style="width:36px;height:36px;background:#0abf9e15;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fab '+icon+'" style="color:#0abf9e;font-size:18px;"></i></div>' +
                        '<strong style="font-size:14px;color:#0f172a;flex:1;">'+previewEscapeHtml(ev.titulo||'Repositorio')+'</strong>' +
                    '</div>' +
                    '<div style="padding:16px;display:flex;align-items:center;justify-content:space-between;gap:10px;">' +
                        '<span style="font-size:12px;color:#94a3b8;word-break:break-all;flex:1;">'+previewEscapeHtml(ev.url)+'</span>' +
                        '<a href="'+ev.url+'" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:40px;background:#0abf9e;color:white;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;transition:background 0.2s;" onmouseover="this.style.background=\'#07866e\';" onmouseout="this.style.background=\'#0abf9e\';"><i class="fas fa-eye"></i> Ver repositorio</a>' +
                    '</div>';
            }
            evDiv.appendChild(item);
        });
    } else { evSec.style.display = 'none'; }

    document.getElementById('modal-proyecto').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModal() { 
    document.getElementById('modal-proyecto').style.display = 'none'; 
    document.body.style.overflow = ''; 
}

function abrirModalPorId(projectId) { 
    var d = (window.previewProjectsById||{})[projectId]; 
    if (d) abrirModal(d); 
}

// ============================================================
// MODALES "VER TODOS"
// ============================================================
function abrirModalTodosProyectos()  { 
    var el=document.getElementById('modal-todos-proyectos');   
    if(el){el.style.display='flex';document.body.style.overflow='hidden';} 
}

function cerrarModalTodosProyectos() { 
    var el=document.getElementById('modal-todos-proyectos');   
    if(el){el.style.display='none';document.body.style.overflow='';} 
}

function abrirModalExperiencias()    { 
    var el=document.getElementById('modal-todos-experiencias');
    if(el){el.style.display='flex';document.body.style.overflow='hidden';} 
}

function cerrarModalExperiencias()   { 
    var el=document.getElementById('modal-todos-experiencias');
    if(el){el.style.display='none';document.body.style.overflow='';} 
}

function abrirModalAcademicas()      { 
    var el=document.getElementById('modal-todos-academicas');  
    if(el){el.style.display='flex';document.body.style.overflow='hidden';} 
}

function cerrarModalAcademicas()     { 
    var el=document.getElementById('modal-todos-academicas');  
    if(el){el.style.display='none';document.body.style.overflow='';} 
}

function abrirModalTecnicas()        { 
    var el=document.getElementById('modal-todos-tecnicas');    
    if(el){el.style.display='flex';document.body.style.overflow='hidden';} 
}

function cerrarModalTecnicas()       { 
    var el=document.getElementById('modal-todos-tecnicas');    
    if(el){el.style.display='none';document.body.style.overflow='';} 
}

function abrirModalBlandas()         { 
    var el=document.getElementById('modal-todos-blandas');     
    if(el){el.style.display='flex';document.body.style.overflow='hidden';} 
}

function cerrarModalBlandas()        { 
    var el=document.getElementById('modal-todos-blandas');     
    if(el){el.style.display='none';document.body.style.overflow='';} 
}

function abrirModalIdiomas()         { 
    var el=document.getElementById('modal-todos-idiomas');     
    if(el){el.style.display='flex';document.body.style.overflow='hidden';} 
}

function cerrarModalIdiomas()        { 
    var el=document.getElementById('modal-todos-idiomas');     
    if(el){el.style.display='none';document.body.style.overflow='';} 
}

// ============================================================
// MODAL COMPARTIR (con QR)
// ============================================================
function abrirModalCompartir() { 
    document.getElementById('modal-compartir').style.display='flex';
    document.body.style.overflow='hidden';
    generarQR();
}

function cerrarModalCompartir() { 
    document.getElementById('modal-compartir').style.display='none'; 
    document.body.style.overflow='';
    var btnTexto = document.getElementById('btn-copiar-texto');
    if (btnTexto) btnTexto.textContent = 'Copiar';
}

// ============================================================
// COPIAR ENLACE + QR
// ============================================================
function copiarLinkPortafolio() {
    var input = document.getElementById('share-link-input');
    if (!input) return;
    
    input.select();
    input.setSelectionRange(0, 99999);
    
    var link = input.value;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(link).then(function() {
            var texto = document.getElementById('btn-copiar-texto');
            var btn = document.getElementById('btn-copiar-link');
            if (texto) texto.textContent = '¡Copiado!';
            if (btn) btn.style.background = '#10b981';
            
            setTimeout(function() {
                if (texto) texto.textContent = 'Copiar';
                if (btn) btn.style.background = '#0abf9e';
            }, 2000);
        });
    } else {
        document.execCommand('copy');
        Swal.fire({
            icon: 'success',
            title: 'Enlace copiado',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

function generarQR() {
    var container = document.getElementById('qrCodeContainer');
    var input = document.getElementById('share-link-input');
    
    if (!container || !input) return;
    
    var link = input.value;
    if (!link) return;
    
    container.innerHTML = '';
    
    if (typeof QRCode !== 'undefined') {
        try {
            new QRCode(container, {
                text: link,
                width: 100,
                height: 100,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch (e) {
            container.innerHTML = '<span style="font-size:10px;color:#94a3b8;">QR no disponible</span>';
        }
    } else {
        container.innerHTML = '<span style="font-size:10px;color:#94a3b8;">Cargando QR...</span>';
    }
}

function copiarQR() {
    var canvas = document.querySelector('#qrCodeContainer canvas');
    if (!canvas) {
        Swal.fire({
            icon: 'info',
            title: 'QR no disponible',
            text: 'Primero genera el código QR.',
            confirmButtonColor: '#0abf9e'
        });
        return;
    }

    canvas.toBlob(function(blob) {
        if (!blob) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo copiar el QR.',
                confirmButtonColor: '#0abf9e'
            });
            return;
        }

        if (navigator.clipboard && navigator.clipboard.write) {
            var item = new ClipboardItem({
                'image/png': blob
            });
            navigator.clipboard.write([item]).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: '✅ QR copiado',
                    text: 'El código QR se ha copiado como imagen.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }).catch(function() {
                descargarQR();
            });
        } else {
            descargarQR();
        }
    }, 'image/png');
}

function descargarQR() {
    var canvas = document.querySelector('#qrCodeContainer canvas');
    if (!canvas) return;

    var link = document.createElement('a');
    link.download = 'codigo-qr-portafolio.png';
    link.href = canvas.toDataURL('image/png');
    link.click();

    Swal.fire({
        icon: 'success',
        title: '✅ QR descargado',
        text: 'El código QR se ha descargado como imagen.',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

// Cerrar modales al clic fuera
['modal-todos-proyectos','modal-todos-experiencias','modal-todos-academicas','modal-todos-tecnicas','modal-todos-blandas','modal-todos-idiomas','modal-proyecto','modal-compartir','modal-detalle-exp','modal-detalle-aca'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('click', function(e) { 
        if (e.target === this) { 
            this.style.display='none'; 
            document.body.style.overflow=''; 
        } 
    });
});

// ============================================================
// PDF / IMAGEN
// ============================================================
function crearCopiaCompleta() {
    var original = document.getElementById('previewContainer');
    var clone = original.cloneNode(true);

    clone.querySelectorAll('.description.collapsed, .proyecto-desc-wrap.collapsed').forEach(function(el) {
        el.classList.remove('collapsed'); el.classList.add('expanded');
        el.style.maxHeight = 'none'; el.style.overflow = 'visible';
    });

    clone.querySelectorAll('.ver-mas-btn').forEach(function(btn) { btn.remove(); });
    clone.querySelectorAll('.btn-ver-todos').forEach(function(btn) { btn.style.display = 'none'; });

    [
        ['#experiencias-completas', '#experiencias-grid',   '.cards-grid'],
        ['#academicas-completas',   '#academicas-grid',     '.cards-grid'],
        ['#blandas-completas',      '#blandas-grid',        '.skills-container'],
        ['#idiomas-completos',      '#idiomas-grid',        '.idiomas-preview-grid'],
        ['#proyectos-completos',    '#proyectos-grid',      '.cards-grid'],
    ].forEach(function(pair) {
        var source = document.querySelector(pair[0]);
        if (!source) return;
        var target = clone.querySelector(pair[1]);
        if (!target) return;
        var inner = source.querySelector(pair[2]);
        if (inner) target.innerHTML = inner.innerHTML;
    });

    var tecSource = document.getElementById('tecnicas-completas');
    if (tecSource) { 
        var tecTarget = clone.querySelector('#tecnicas-grid'); 
        if (tecTarget) tecTarget.innerHTML = tecSource.innerHTML; 
    }

    clone.querySelectorAll('.idioma-cert-link,.btn-certificado,a[onclick*="abrirLightbox"]').forEach(function(el) { el.remove(); });

    var seccionesARevisar = [
        { id: 'section-experiencias', selector: '.card:not(.empty-message-preview)' },
        { id: 'section-academicas',   selector: '.card:not(.empty-message-preview)' },
        { id: 'section-tecnicas',     selector: '.tech-skill-item' },
        { id: 'section-blandas',      selector: '.soft-skill-tag' },
        { id: 'section-idiomas',      selector: '.idioma-preview-card' },
        { id: 'section-proyectos',    selector: '.card:not(.empty-message-preview)' },
    ];
    seccionesARevisar.forEach(function(s) {
        var sec = clone.querySelector('#' + s.id);
        if (!sec) return;
        var tieneContenido = sec.querySelectorAll(s.selector).length > 0;
        var tieneVacio     = sec.querySelector('.empty-message-preview') !== null;
        if (!tieneContenido || tieneVacio) {
            sec.remove();
        }
    });

    clone.querySelectorAll('.empty-message-preview').forEach(function(el) { el.remove(); });

    var fabClone    = clone.querySelector('#fabContainer');       if (fabClone)    fabClone.style.display    = 'none';
    var barClone    = clone.querySelector('#previewBottomBar');   if (barClone)    barClone.style.display    = 'none';
    var volverClone = clone.querySelector('.btn-volver-flotante');if (volverClone) volverClone.style.display = 'none';
    var vibeSide    = clone.querySelector('#vibeSidebar');        if (vibeSide)    vibeSide.style.display    = 'none';
    var vibeBack    = clone.querySelector('#vibeBackdrop');       if (vibeBack)    vibeBack.style.display    = 'none';
    clone.style.paddingBottom = '0';

    return clone;
}

async function descargarPDF() {
    
    var fabMenu = document.getElementById('fabMenu');
    var fabMenuTop = document.getElementById('fabMenuTop');
    var fabIconTop = document.getElementById('fabIconTop');
    if (fabMenu) fabMenu.classList.remove('open');
    if (fabMenuTop) fabMenuTop.classList.remove('open');
    if (fabIconTop) fabIconTop.className = 'fas fa-ellipsis-h';

    // Expandir todas las descripciones colapsadas
    document.querySelectorAll('.description.collapsed, .proyecto-desc-wrap.collapsed').forEach(function(el) {
        el.classList.remove('collapsed');
        el.classList.add('expanded');
        el.style.maxHeight = 'none';
        el.style.overflow = 'visible';
    });

    // Mostrar contenido completo de cada sección
    var secciones = [
        { source: '#proyectos-completos',    target: '#proyectos-grid',    inner: '.cards-grid' },
        { source: '#experiencias-completas', target: '#experiencias-grid', inner: '.cards-grid' },
        { source: '#academicas-completas',   target: '#academicas-grid',   inner: '.cards-grid' },
        { source: '#tecnicas-completas',     target: '#tecnicas-grid',     inner: '' },
        { source: '#blandas-completas',      target: '#blandas-grid',      inner: '.skills-container' },
        { source: '#idiomas-completos',      target: '#idiomas-grid',      inner: '.idiomas-preview-grid' }
    ];

    secciones.forEach(function(sec) {
        var src = document.querySelector(sec.source);
        var tgt = document.querySelector(sec.target);
        if (src && tgt) {
            var content = sec.inner ? src.querySelector(sec.inner) : src;
            if (content) tgt.innerHTML = content.innerHTML;
        }
    });

    document.querySelectorAll('.btn-ver-todos').forEach(function(el) {
        el.style.display = 'none';
    });

    // Esperar que el DOM se actualice y luego imprimir
    await new Promise(resolve => setTimeout(resolve, 400));
    
    window.print();
}

async function descargarImagen() {
    document.getElementById('fabMenu').classList.remove('open');
    Swal.fire({ title:'Generando imagen...', allowOutsideClick:false, showConfirmButton:false, didOpen:function(){ Swal.showLoading(); } });
    try {
        var clone = crearCopiaCompleta();
        var tempDiv = document.createElement('div');
        Object.assign(tempDiv.style, { position:'absolute', left:'-9999px', top:'-9999px', width:'1200px', backgroundColor:'white' });
        tempDiv.appendChild(clone); document.body.appendChild(tempDiv);
        clone.style.cssText = 'max-width:1200px;margin:0 auto;';
        setTimeout(async function() {
            try {
                var canvas = await html2canvas(tempDiv, { scale:2.5, useCORS:true, backgroundColor:'#ffffff', logging:false, windowWidth:tempDiv.scrollWidth, windowHeight:tempDiv.scrollHeight });
                var link = document.createElement('a');
                link.download = 'portafolio.png'; link.href = canvas.toDataURL('image/png'); link.click();
                document.body.removeChild(tempDiv);
                Swal.fire({ icon:'success', title:'¡Imagen descargada!', toast:true, position:'top-end', showConfirmButton:false, timer:3000 });
            } catch(err) { document.body.removeChild(tempDiv); throw err; }
        }, 800);
    } catch(e) { Swal.fire({ icon:'error', title:'Error', text:'No se pudo generar la imagen.' }); }
}

// ============================================================
// PUBLICAR
// ============================================================
document.getElementById('formPublicar').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title:'¿Publicar portafolio?', text:'Tu perfil será visible para todos los usuarios.',
        showCancelButton:true, confirmButtonColor:'#0abf9e', cancelButtonColor:'#6c757d',
        confirmButtonText:'Publicar', cancelButtonText:'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({ title:'Publicando...', showConfirmButton:false, allowOutsideClick:false });
            document.getElementById('formPublicar').submit();
        }
    });
});

function filtrarProyectosPreview(q) {
    q = q.toLowerCase().trim();
    var cards = document.querySelectorAll('#folder-grid-preview [data-nombre]');
    var visible = 0;
    cards.forEach(function(card) {
        var match = !q || card.dataset.nombre.includes(q) || card.dataset.techs.includes(q) || card.dataset.estado.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    var noRes = document.getElementById('no-results-preview');
    if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';
}

// ============================================================
// VIBE SELECTOR
// ============================================================
var selectedTheme = document.getElementById('previewContainer')?.className.match(/theme-(\w+)/)?.[1] || 'default';

function toggleVibeSidebar(show) {
    var sidebar  = document.getElementById('vibeSidebar');
    var backdrop = document.getElementById('vibeBackdrop');
    if (show) {
        sidebar.classList.add('open'); backdrop.classList.add('show');
        document.getElementById('fabMenu').classList.remove('open');
        document.getElementById('fabIcon').className = 'fas fa-ellipsis-h';
    } else {
        sidebar.classList.remove('open'); backdrop.classList.remove('show');
        applyThemeClass(selectedTheme);
        document.querySelectorAll('.vibe-card').forEach(function(c) { 
            c.classList.toggle('active', c.dataset.theme === selectedTheme); 
        });
    }
}

function selectVibe(card) {
    document.querySelectorAll('.vibe-card').forEach(function(c) { c.classList.remove('active'); });
    card.classList.add('active');
    applyThemeClass(card.dataset.theme);
}

function applyThemeClass(theme) {
    var container = document.getElementById('previewContainer');
    if (!container) return;
    container.classList.remove('theme-sunset','theme-emerald','theme-midnight','theme-ocean','theme-sakura');
    if (theme !== 'default') container.classList.add('theme-' + theme);
}

function saveVibeTheme() {
    var activeCard = document.querySelector('.vibe-card.active');
    if (!activeCard) return;
    var theme = activeCard.dataset.theme;
    var saveBtn = document.getElementById('saveVibeBtn');
    saveBtn.disabled = true; 
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    fetch("{{ route('portfolio.theme.update') }}", {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        body: JSON.stringify({ theme: theme })
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        saveBtn.disabled = false; 
        saveBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Vibra';
        if (data.success) {
            selectedTheme = theme;
            Swal.fire({ icon:'success', title:'¡Vibra actualizada!', toast:true, position:'top-end', showConfirmButton:false, timer:3000 });
            toggleVibeSidebar(false);
        } else { 
            Swal.fire({ icon:'error', title:'Error', text:'Ocurrió un error.' }); 
        }
    })
    .catch(function(){
        saveBtn.disabled = false; 
        saveBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Vibra';
        Swal.fire({ icon:'error', title:'Error', text:'No se pudo conectar.' });
    });
}

// ============================================================
// ALERTA DE PORTAFOLIO INCOMPLETO
// ============================================================
function mostrarAlertaIncompleto() {
    document.getElementById('fabMenu').classList.remove('open');
    
    Swal.fire({
        icon: 'warning',
        title: '⚠️ Portafolio incompleto',
        html: `
            <div style="text-align: left;">
                <div style="background: #f0fdf4; border-left: 4px solid #0abf9e; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
                    <p style="margin: 0; font-size: 14px; color: #065f46;">
                        <strong>Requisito:</strong> Completa tu portafolio para descargar
                    </p>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #047857;">
                        Agrega experiencias, proyectos o formación académica
                    </p>
                </div>
                <div style="background: #f8fafc; border-radius: 6px; padding: 10px 14px; text-align: center;">
                    <span style="font-size: 13px; color: #64748b;">
                        💡 Completa tu perfil desde el panel de edición
                    </span>
                </div>
            </div>
        `,
        confirmButtonColor: '#0abf9e',
        confirmButtonText: 'Entendido',
        showCancelButton: false,
        width: 480,
    });
}

// ============================================================
// INICIALIZACIÓN
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    var shareInput = document.getElementById('share-link-input');
    if (shareInput) {
        var link = shareInput.value || window.location.href;
        if (link) {
            shareInput.value = link;
        }
    }
});