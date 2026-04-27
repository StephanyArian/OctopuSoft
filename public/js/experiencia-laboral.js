
        function toggleFechaFin(checkbox) {
            const group = document.getElementById('fechaFinGroup');
            const ids   = ['fechaFinDia','fechaFinMes','fechaFinAnio'];
            ids.forEach(id => {
                document.getElementById(id).disabled = checkbox.checked;
                if (checkbox.checked) document.getElementById(id).value = '';
            });
            group.style.opacity = checkbox.checked ? '0.4' : '1';
        }

        function resetForm() {
            document.getElementById('experienciaForm').reset();
            document.getElementById('fechaFinGroup').style.opacity = '1';
            ['fechaFinDia','fechaFinMes','fechaFinAnio'].forEach(id => {
                document.getElementById(id).disabled = false;
            });
        }

        document.getElementById('experienciaForm').addEventListener('submit', function(e) {
            let isValid = true;

            const empresa = document.getElementById('empresa').value.trim();
            const empresaError = document.getElementById('empresaError');
            if (!empresa) { empresaError.classList.remove('hidden'); isValid = false; }
            else { empresaError.classList.add('hidden'); }

            const cargo = document.getElementById('cargo').value.trim();
            const cargoError = document.getElementById('cargoError');
            if (!cargo) { cargoError.classList.remove('hidden'); isValid = false; }
            else { cargoError.classList.add('hidden'); }

            const inicioDia  = document.getElementById('fechaInicioDia').value;
            const inicioMes  = document.getElementById('fechaInicioMes').value;
            const inicioAnio = document.getElementById('fechaInicioAnio').value;
            const fechaInicioError = document.getElementById('fechaInicioError');
            if (!inicioDia || !inicioMes || !inicioAnio) {
                fechaInicioError.classList.remove('hidden'); isValid = false;
            } else { fechaInicioError.classList.add('hidden'); }

            const finDia   = document.getElementById('fechaFinDia').value;
            const finMes   = document.getElementById('fechaFinMes').value;
            const finAnio  = document.getElementById('fechaFinAnio').value;
            const actual   = document.getElementById('trabajoActual').checked;
            const fechaFinError = document.getElementById('fechaFinError');

            if (!actual && finDia && finMes && finAnio && inicioDia && inicioMes && inicioAnio) {
                const inicio = new Date(inicioAnio, parseInt(inicioMes)-1, parseInt(inicioDia));
                const fin    = new Date(finAnio,    parseInt(finMes)-1,    parseInt(finDia));
                if (fin < inicio) {
                    fechaFinError.classList.remove('hidden'); isValid = false;
                } else { fechaFinError.classList.add('hidden'); }
            } else { fechaFinError.classList.add('hidden'); }

            if (!isValid) e.preventDefault();
        });

        window.addEventListener('DOMContentLoaded', function() {
            const cb = document.getElementById('trabajoActual');
            if (cb.checked) toggleFechaFin(cb);
        });
    