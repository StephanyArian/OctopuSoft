document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de información académica cargado');
    
    // ==========================================
    // CONTADOR DE DESCRIPCIÓN
    // ==========================================
    const textareaDescripcion = document.getElementById('descripcion');
    const contadorDescripcion = document.getElementById('descripcionCounter');
    
    if (textareaDescripcion && contadorDescripcion) {
        console.log('✓ Contador de descripción encontrado');
        
        function actualizarContadorDescripcion() {
            const longitud = textareaDescripcion.value.length;
            contadorDescripcion.textContent = longitud + ' / 500';
            
            if (longitud >= 500) {
                contadorDescripcion.style.color = 'red';
                contadorDescripcion.style.fontWeight = 'bold';
            } else if (longitud >= 490) {
                contadorDescripcion.style.color = 'orange';
                contadorDescripcion.style.fontWeight = 'normal';
            } else {
                contadorDescripcion.style.color = '#6c757d';
                contadorDescripcion.style.fontWeight = 'normal';
            }
        }
        
        textareaDescripcion.addEventListener('input', actualizarContadorDescripcion);
        actualizarContadorDescripcion();
    } else {
        console.error('✗ No se encontró el textarea o contador de descripción');
    }
    
    // ==========================================
    // FUNCIÓN TOGGLE FECHA FIN
    // ==========================================
    window.toggleFechaFin = function(checkbox) {
        const fechaFin = document.getElementById('fechaFin');
        if (fechaFin) {
            fechaFin.disabled = checkbox.checked;
            if (checkbox.checked) {
                fechaFin.value = '';
                const fechaFinError = document.getElementById('fechaFinError');
                if (fechaFinError) fechaFinError.classList.add('hidden');
            }
        }
    };
    
    // ==========================================
    // FUNCIÓN RESET FORM
    // ==========================================
    window.resetForm = function() {
        const form = document.getElementById('academicForm');
        if (form) form.reset();
        
        const fechaFin = document.getElementById('fechaFin');
        if (fechaFin) fechaFin.disabled = false;
        
        if (textareaDescripcion && contadorDescripcion) {
            contadorDescripcion.textContent = '0 / 500';
            contadorDescripcion.style.color = '#6c757d';
        }

        // Resetear campo "Otro"
        const otroContainer = document.getElementById('otroTipoFormacionContainer');
        const otroInput = document.getElementById('otroTipoFormacion');
        if (otroContainer) otroContainer.style.display = 'none';
        if (otroInput) {
            otroInput.value = '';
            otroInput.removeAttribute('required');
        }
        
        // Resetear dropdown
        const label = document.getElementById('tipoFormacionLabel');
        const hidden = document.getElementById('tipoFormacionHidden');
        if (label) {
            label.textContent = '— Seleccionar tipo —';
            label.classList.add('muted');
        }
        if (hidden) hidden.value = '';
        
        document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.form-input, .form-textarea').forEach(el => el.classList.remove('error'));

        archivosEvidencia = [];
        const grid = document.getElementById('evidenciasGrid');
        if (grid) grid.innerHTML = '';
        const btnAdd = document.getElementById('ev-add-btn');
        if (btnAdd) btnAdd.remove();
        const uploadZone = document.getElementById('uploadZone');
        if (uploadZone) uploadZone.style.display = 'block';
        const errorEv = document.getElementById('evidenciasError');
        if (errorEv) errorEv.classList.add('hidden');
    };
    
    // ==========================================
    // VALIDACIÓN DEL FORMULARIO
    // ==========================================
    const form = document.getElementById('academicForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.form-input, .form-textarea').forEach(el => el.classList.remove('error'));
            
            // Validar Institución
            const institucion = document.getElementById('institucion').value.trim();
            if (!institucion) {
                const errorEl = document.getElementById('institucionError');
                if (errorEl) errorEl.classList.remove('hidden');
                document.getElementById('institucion').classList.add('error');
                isValid = false;
            }
            
            // Validar Título Obtenido
            const tituloObtenido = document.getElementById('tituloObtenido').value.trim();
            if (!tituloObtenido) {
                const errorEl = document.getElementById('tituloObtenidoError');
                if (errorEl) errorEl.classList.remove('hidden');
                document.getElementById('tituloObtenido').classList.add('error');
                isValid = false;
            }
            
            // Validar Tipo de Formación
            const tipoFormacion = document.getElementById('tipoFormacionHidden').value;
            if (!tipoFormacion) {
                const errorEl = document.getElementById('tipoFormacionError');
                if (errorEl) errorEl.classList.remove('hidden');
                isValid = false;
            }
            
            // Validar "Otro" si se seleccionó
            if (tipoFormacion === 'Otro') {
                const otroInput = document.getElementById('otroTipoFormacion');
                const otroError = document.getElementById('otroTipoFormacionError');
                
                if (!otroInput || !otroInput.value.trim()) {
                    isValid = false;
                    if (otroError) {
                        otroError.textContent = 'Por favor, especifica el tipo de formación';
                        otroError.classList.remove('hidden');
                    }
                    if (otroInput) otroInput.classList.add('error');
                }
            }
            
            // Validar Fecha Inicio
            const fechaInicio = document.getElementById('fechaInicio').value;
            if (!fechaInicio) {
                const errorEl = document.getElementById('fechaInicioError');
                if (errorEl) errorEl.classList.remove('hidden');
                document.getElementById('fechaInicio').classList.add('error');
                isValid = false;
            }
            
            // Validar fechas
            const estudioActual = document.getElementById('estudioActual').checked;
            const fechaFin = document.getElementById('fechaFin').value;
            const fechaFinError = document.getElementById('fechaFinError');
            
            if (!estudioActual && fechaInicio) {
                if (!fechaFin) {
                    if (fechaFinError) {
                        fechaFinError.textContent = 'Debes indicar una fecha de fin o marcar "Estudio actual"';
                        fechaFinError.classList.remove('hidden');
                    }
                    document.getElementById('fechaFin').classList.add('error');
                    isValid = false;
                } else if (fechaFin <= fechaInicio) {
                    if (fechaFinError) {
                        fechaFinError.textContent = 'La fecha de fin debe ser posterior a la fecha de inicio';
                        fechaFinError.classList.remove('hidden');
                    }
                    document.getElementById('fechaFin').classList.add('error');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                console.log('Formulario tiene errores');
            } else {
                console.log('Formulario válido, enviando...');
            }
        });
    }
    
    // Inicializar toggle si "Estudio actual" está marcado
    const estudioActualCheck = document.getElementById('estudioActual');
    if (estudioActualCheck && estudioActualCheck.checked) {
        window.toggleFechaFin(estudioActualCheck);
    }
    
    // ==========================================
    // DROPDOWN TIPO DE FORMACIÓN CON "OTRO"
    // ==========================================
    const dropdown = document.getElementById('tipoFormacionDropdown');
    if (dropdown) {
        const toggleBtn = dropdown.querySelector('.custom-dropdown-toggle');
        const menu = dropdown.querySelector('.custom-dropdown-menu');
        const label = document.getElementById('tipoFormacionLabel');
        const hidden = document.getElementById('tipoFormacionHidden');
        const arrow = dropdown.querySelector('.dropdown-arrow');
        const errorEl = document.getElementById('tipoFormacionError');
        const otroContainer = document.getElementById('otroTipoFormacionContainer');
        const otroInput = document.getElementById('otroTipoFormacion');
        const otroError = document.getElementById('otroTipoFormacionError');

        function abrirDrop() {
            dropdown.classList.add('open');
            arrow.textContent = '▼';
            menu.style.display = 'block';
        }
        
        function cerrarDrop() {
            dropdown.classList.remove('open');
            arrow.textContent = '▲';
            menu.style.display = 'none';
        }

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.contains('open') ? cerrarDrop() : abrirDrop();
        });

        // Función para manejar la selección incluyendo "Otro"
        function handleDropdownSelection(li, valor, texto) {
            // Actualizar label y hidden
            label.textContent = texto || valor || '— Seleccionar tipo —';
            if (valor) {
                label.classList.remove('muted');
            } else {
                label.classList.add('muted');
            }
            hidden.value = valor || '';
            
            // Marcar como seleccionado (solo si hay li)
            if (li) {
                menu.querySelectorAll('li').forEach(l => l.classList.remove('selected'));
                li.classList.add('selected');
            }
            
            // Manejar "Otro"
            if (valor === 'Otro') {
                if (otroContainer) otroContainer.style.display = 'block';
                if (otroInput) {
                    otroInput.setAttribute('required', 'required');
                    setTimeout(() => otroInput.focus(), 100);
                }
                if (otroError) otroError.classList.add('hidden');
            } else {
                if (otroContainer) otroContainer.style.display = 'none';
                if (otroInput) {
                    otroInput.removeAttribute('required');
                    otroInput.value = '';
                }
                if (otroError) otroError.classList.add('hidden');
            }
            
            if (errorEl) errorEl.classList.add('hidden');
            cerrarDrop();
        }

        // Agregar event listeners a cada item del dropdown
        menu.querySelectorAll('li:not(.dropdown-group-title)').forEach(function(li) {
            li.addEventListener('click', function() {
                const valor = this.dataset.value || '';
                const texto = this.textContent;
                handleDropdownSelection(this, valor, texto);
            });
        });

        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) cerrarDrop();
        });

        cerrarDrop();
        
        // Si hay un valor guardado (para edición), restaurarlo
        const valorGuardado = hidden.value;
        if (valorGuardado) {
            const opcionesPredefinidas = [
                'Colegio / Bachillerato', 'Técnico Superior', 'Licenciatura / Ingeniería',
                'Maestría', 'Doctorado / PhD', 'Bootcamp', 'Curso online',
                'Certificación profesional', 'Diplomado', 'Intercambio académico', 'Otro'
            ];
            
            if (!opcionesPredefinidas.includes(valorGuardado)) {
                // Es un valor personalizado
                handleDropdownSelection(null, 'Otro', 'Otro');
                if (otroInput) otroInput.value = valorGuardado;
            } else {
                // Es una opción predefinida, buscar y seleccionar
                const itemToSelect = Array.from(menu.querySelectorAll('li[data-value]')).find(
                    li => li.dataset.value === valorGuardado
                );
                if (itemToSelect) {
                    handleDropdownSelection(itemToSelect, valorGuardado, itemToSelect.textContent);
                }
            }
        }
    }

    console.log('✅ Script inicializado correctamente');
});

// ==========================================
// MANEJO DE EVIDENCIAS
// ==========================================
const MAX_SIZE_MB = 2;
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];
let archivosEvidencia = [];

function handleDrop(event) {
    event.preventDefault();
    const zone = document.getElementById('uploadZone');
    zone.style.borderColor = 'var(--gray-300)';
    zone.style.background = 'var(--off)';
    handleFiles(event.dataTransfer.files);
}
 
function handleFiles(files) {
    const errorEl = document.getElementById('evidenciasError');
    errorEl.classList.add('hidden');
 
    Array.from(files).forEach(file => {
        if (!ALLOWED_TYPES.includes(file.type)) {
            errorEl.textContent = `"${file.name}" — Formato no permitido. Use JPG, PNG o PDF`;
            errorEl.classList.remove('hidden');
            return;
        }
        if (file.size > MAX_SIZE_MB * 1024 * 1024) {
            errorEl.textContent = `"${file.name}" — El archivo no puede superar los 2MB`;
            errorEl.classList.remove('hidden');
            return;
        }
        if (archivosEvidencia.find(f => f.name === file.name && f.size === file.size)) return;
 
        archivosEvidencia.push(file);
        renderEvidenciaCard(file, archivosEvidencia.length - 1);
    });
 
    actualizarInputFiles();
    toggleUploadZone();
}
 
function renderEvidenciaCard(file, index) {
    const grid = document.getElementById('evidenciasGrid');
    const card = document.createElement('div');
    card.id = `ev-card-${index}`;
    card.style.cssText = `
        width:110px; border-radius:10px; overflow:hidden;
        border:1px solid var(--gray-100); position:relative;
        box-shadow:0 1px 4px rgba(0,0,0,0.06);
    `;
 
    const isPdf = file.type === 'application/pdf';
 
    if (isPdf) {
        card.innerHTML = `
            <div style="width:110px; height:80px; background:linear-gradient(135deg,#fff3e0,#ffe0b2);
                        display:flex; flex-direction:column; align-items:center;
                        justify-content:center; gap:4px;">
                <span style="font-size:24px;">📄</span>
                <span style="font-size:10px; color:#e65100; font-weight:600;">PDF</span>
            </div>
            <div style="padding:5px 7px; background:#fff;">
                <div style="font-size:10px; color:#6b6a66; white-space:nowrap;
                            overflow:hidden; text-overflow:ellipsis;" title="${file.name}">
                    ${file.name}
                </div>
            </div>
        `;
    } else {
        const url = URL.createObjectURL(file);
        card.innerHTML = `
            <img src="${url}" alt="${file.name}"
                 style="width:110px; height:80px; object-fit:cover; display:block;">
            <div style="padding:5px 7px; background:#fff;">
                <div style="font-size:10px; color:#6b6a66; white-space:nowrap;
                            overflow:hidden; text-overflow:ellipsis;" title="${file.name}">
                    ${file.name}
                </div>
            </div>
        `;
    }
 
    const btnRemove = document.createElement('div');
    btnRemove.style.cssText = `
        position:absolute; top:4px; right:4px; width:20px; height:20px;
        background:rgba(61,10,30,0.85); color:#fff; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        font-size:11px; cursor:pointer; font-weight:700;
    `;
    btnRemove.textContent = '✕';
    btnRemove.onclick = () => eliminarEvidencia(index);
 
    card.appendChild(btnRemove);
    grid.appendChild(card);
}
 
function eliminarEvidencia(index) {
    archivosEvidencia.splice(index, 1);
    const grid = document.getElementById('evidenciasGrid');
    grid.innerHTML = '';
    archivosEvidencia.forEach((file, i) => renderEvidenciaCard(file, i));
    actualizarInputFiles();
    toggleUploadZone();
}
 
function actualizarInputFiles() {
    const input = document.getElementById('evidenciasInput');
    const dataTransfer = new DataTransfer();
    archivosEvidencia.forEach(f => dataTransfer.items.add(f));
    input.files = dataTransfer.files;
}
 
function toggleUploadZone() {
    const grid = document.getElementById('evidenciasGrid');
    const btnAdd = document.getElementById('ev-add-btn');
 
    if (archivosEvidencia.length > 0) {
        if (!btnAdd) {
            const btn = document.createElement('div');
            btn.id = 'ev-add-btn';
            btn.style.cssText = `
                width:110px; height:110px; border:2px dashed var(--gray-300);
                border-radius:10px; display:flex; flex-direction:column;
                align-items:center; justify-content:center; gap:6px;
                cursor:pointer; background:var(--off); transition:all 0.2s;
            `;
            btn.innerHTML = `
                <span style="font-size:22px; color:var(--teal); font-weight:300;">＋</span>
                <span style="font-size:11px; color:var(--gray-500);">Agregar más</span>
            `;
            btn.onmouseenter = () => { btn.style.borderColor = 'var(--teal)'; btn.style.background = 'rgba(10,191,158,0.05)'; };
            btn.onmouseleave = () => { btn.style.borderColor = 'var(--gray-300)'; btn.style.background = 'var(--off)'; };
            btn.onclick = () => document.getElementById('evidenciasInput').click();
            grid.appendChild(btn);
        }
        document.getElementById('uploadZone').style.display = 'none';
    } else {
        if (btnAdd) btnAdd.remove();
        document.getElementById('uploadZone').style.display = 'block';
    }
}