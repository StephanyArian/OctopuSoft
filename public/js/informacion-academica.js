        function toggleFechaFin(checkbox) {
            const fechaFin = document.getElementById('fechaFin');
            fechaFin.disabled = checkbox.checked;
            if (checkbox.checked) {
                fechaFin.value = '';
                const fechaFinError = document.getElementById('fechaFinError');
                if (fechaFinError) fechaFinError.classList.add('hidden');
            }
        }

        function resetForm() {
            document.getElementById('academicForm').reset();
            document.getElementById('fechaFin').disabled = false;
            // Ocultar todos los mensajes de error
            document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));
        }

        document.getElementById('academicForm').addEventListener('submit', function(e) {
            let isValid = true;

            // Limpiar errores previos
            document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));

            // Validar Institución
            const institucion = document.getElementById('institucion').value.trim();
            if (!institucion) {
                document.getElementById('institucionError').classList.remove('hidden');
                document.getElementById('institucion').classList.add('error');
                isValid = false;
            } else if (!soloLetras.test(institucion)) {
                     document.getElementById('institucionError').textContent = 'La institución solo debe contener letras';
                     document.getElementById('institucionError').classList.remove('hidden');
                    document.getElementById('institucion').classList.add('error');
                    isValid = false;
            } else {
                document.getElementById('institucionError').classList.add('hidden');
            }

            // Validar Título Obtenido
            const tituloObtenido = document.getElementById('tituloObtenido').value.trim();
            if (!tituloObtenido) {
                document.getElementById('tituloObtenidoError').classList.remove('hidden');
                document.getElementById('tituloObtenido').classList.add('error');
                isValid = false;
            } else if (!soloLetras.test(tituloObtenido)) {
                 document.getElementById('tituloObtenidoError').textContent = 'El título solo debe contener letras';
                    document.getElementById('tituloObtenidoError').classList.remove('hidden');
                 document.getElementById('tituloObtenido').classList.add('error');
                 isValid = false;
            } else {
                 document.getElementById('tituloObtenidoError').classList.add('hidden');
            }

            

            // Validar Fecha Inicio
            const fechaInicio = document.getElementById('fechaInicio').value;
            if (!fechaInicio) {
                document.getElementById('fechaInicioError').classList.remove('hidden');
                document.getElementById('fechaInicio').classList.add('error');
                isValid = false;
            }

            // Validación de fechas (lo NUEVO)
            const estudioActual = document.getElementById('estudioActual').checked;
            const fechaFin = document.getElementById('fechaFin').value;
            const fechaFinError = document.getElementById('fechaFinError');
            
            if (!estudioActual && fechaInicio) {
                if (!fechaFin) {
                    fechaFinError.innerText = 'Debes indicar una fecha de fin o marcar "Estudio actual"';
                    fechaFinError.classList.remove('hidden');
                    document.getElementById('fechaFin').classList.add('error');
                    isValid = false;
                } else if (fechaFin <= fechaInicio) {
                    fechaFinError.innerText = 'La fecha de fin debe ser posterior a la fecha de inicio';
                    fechaFinError.classList.remove('hidden');
                    document.getElementById('fechaFin').classList.add('error');
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    