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
            
            // Cambiar color según la longitud
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
        actualizarContadorDescripcion(); // Valor inicial
        
    } else {
        console.error('✗ No se encontró el textarea o contador de descripción');
        console.log('textareaDescripcion:', textareaDescripcion);
        console.log('contadorDescripcion:', contadorDescripcion);
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
        
        // Resetear contador de descripción
        if (textareaDescripcion && contadorDescripcion) {
            contadorDescripcion.textContent = '0 / 500';
            contadorDescripcion.style.color = '#6c757d';
        }
        
        // Ocultar mensajes de error
        document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.form-input, .form-textarea').forEach(el => el.classList.remove('error'));
    };
    
    // ==========================================
    // VALIDACIÓN DEL FORMULARIO
    // ==========================================
    const form = document.getElementById('academicForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Limpiar errores previos
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
            
            // Validar Fecha Inicio
            const fechaInicio = document.getElementById('fechaInicio').value;
            if (!fechaInicio) {
                const errorEl = document.getElementById('fechaInicioError');
                if (errorEl) errorEl.classList.remove('hidden');
                document.getElementById('fechaInicio').classList.add('error');
                isValid = false;
            }
            
            // Validar fechas (fin vs inicio)
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
    
    console.log('✅ Script inicializado correctamente');
});