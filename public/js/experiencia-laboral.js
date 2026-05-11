/* =========================================================
   experiencia-laboral.js  —  HU-12 + dropdown personalizado
   ========================================================= */

const MAX_CARGOS = 5;

/* Lista maestra de opciones (debe coincidir con el blade) */
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
    'Business Analyst',
    'Security Engineer',
    'Data Engineer',
    'Cloud Engineer',
    'AI Engineer',
    'Systems Analyst',
];

/* ── SVG del ícono de basurero ── */
const TRASH_ICON = `
<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
</svg>`;

/* ── Generador de HTML para un dropdown de cargo ── */
function crearDropdownHTML(index) {
    const opts = CARGO_OPTIONS.map(o =>
        `<li data-value="${o}">${o}</li>`
    ).join('');

    return `
        <div class="cargo-item" data-index="${index}">
            <div class="custom-dropdown">
                <button type="button" class="custom-dropdown-toggle" onclick="toggleDropdown(this)">
                    <span class="dropdown-label muted">— Seleccionar cargo —</span>
                    <span class="dropdown-arrow">▲</span>
                </button>
                <ul class="custom-dropdown-menu">
                    <li data-value="" class="placeholder-opt selected">— Seleccionar cargo —</li>
                    ${opts}
                </ul>
                <input type="hidden" name="cargos[]" value="">
            </div>
            <button type="button" class="btn-remove-cargo" onclick="removeCargo(this)" title="Eliminar cargo">
                ${TRASH_ICON}
            </button>
        </div>`;
}

/* ── Abrir / cerrar un dropdown ── */
window.toggleDropdown = function (btn) {
    const wrapper = btn.closest('.custom-dropdown');
    const isOpen  = wrapper.classList.contains('open');

    // Cerrar todos los demás
    document.querySelectorAll('.custom-dropdown.open').forEach(d => {
        if (d !== wrapper) cerrarDropdown(d);
    });

    if (isOpen) {
        cerrarDropdown(wrapper);
    } else {
        abrirDropdown(wrapper);
    }
};

function abrirDropdown(wrapper) {
    wrapper.classList.add('open');
    wrapper.querySelector('.dropdown-arrow').textContent = '▼';
    // Adjuntar listeners a los <li> solo cuando se abre
    wrapper.querySelectorAll('.custom-dropdown-menu li').forEach(li => {
        li.onclick = () => seleccionarOpcion(li);
    });
}

function cerrarDropdown(wrapper) {
    wrapper.classList.remove('open');
    wrapper.querySelector('.dropdown-arrow').textContent = '▲';
}

function seleccionarOpcion(li) {
    const wrapper = li.closest('.custom-dropdown');
    const valor   = li.dataset.value;
    const label   = wrapper.querySelector('.dropdown-label');
    const hidden  = wrapper.querySelector('input[type=hidden]');

    // Actualizar label
    if (valor === '') {
        label.textContent = '— Seleccionar cargo —';
        label.classList.add('muted');
    } else {
        label.textContent = valor;
        label.classList.remove('muted');
    }

    // Marcar seleccionada
    wrapper.querySelectorAll('.custom-dropdown-menu li').forEach(l => l.classList.remove('selected'));
    li.classList.add('selected');

    // Guardar en el hidden
    hidden.value = valor;

    cerrarDropdown(wrapper);
    ocultarCargosError();
}

/* Cerrar dropdowns al hacer clic fuera */
document.addEventListener('click', function (e) {
    if (!e.target.closest('.custom-dropdown')) {
        document.querySelectorAll('.custom-dropdown.open').forEach(cerrarDropdown);
    }
});

/* ── Gestión de la lista de cargos ── */
function getCargosItems() {
    return document.querySelectorAll('#cargos-list .cargo-item');
}

function actualizarBotonesRemover() {
    const items = getCargosItems();
    items.forEach(item => {
        item.querySelector('.btn-remove-cargo').disabled = items.length === 1;
    });
    document.getElementById('btnAgregarCargo').style.display =
        items.length >= MAX_CARGOS ? 'none' : '';
}

window.agregarCargo = function () {
    const items = getCargosItems();
    if (items.length >= MAX_CARGOS) return;
    const idx = items.length;
    const div = document.createElement('div');
    div.innerHTML = crearDropdownHTML(idx);
    const nuevoItem = div.firstElementChild;
    document.getElementById('cargos-list').appendChild(nuevoItem);
    actualizarBotonesRemover();
};

window.removeCargo = function (btn) {
    if (getCargosItems().length <= 1) {
        mostrarCargosError('Debe haber al menos un cargo registrado.');
        return;
    }
    // Cerrar dropdown si está abierto
    const wrapper = btn.closest('.cargo-item').querySelector('.custom-dropdown');
    if (wrapper) cerrarDropdown(wrapper);

    btn.closest('.cargo-item').remove();
    ocultarCargosError();
    actualizarBotonesRemover();
};

/* ── Mensajes de error de cargos ── */
function mostrarCargosError(msg) {
    const el = document.getElementById('cargosError');
    el.textContent = msg;
    el.classList.remove('hidden');
}
function ocultarCargosError() {
    document.getElementById('cargosError').classList.add('hidden');
}

/* ── Validación de cargos al enviar ── */
function validarCargos() {
    const hiddens = document.querySelectorAll('#cargos-list input[type=hidden][name="cargos[]"]');
    const valores = [];

    for (const input of hiddens) {
        const val = input.value.trim();
        if (!val) {
            mostrarCargosError('Debes seleccionar un cargo en cada campo.');
            return false;
        }
        if (valores.includes(val.toLowerCase())) {
            mostrarCargosError(`El cargo "${val}" está duplicado.`);
            return false;
        }
        valores.push(val.toLowerCase());
    }
    ocultarCargosError();
    return true;
}

/* ── Sync date picker → hidden inputs para el controller ── */
window.syncFechaInicio = function (val) {
    const fullEl = document.getElementById('fechaInicioFull');
    if (!val) {
        ['fechaInicioDia','fechaInicioMes','fechaInicioAnio'].forEach(id => document.getElementById(id).value = '');
        if (fullEl) fullEl.value = '';
        return;
    }
    const [anio, mes, dia] = val.split('-');
    if (parseInt(anio) < 1950) { document.getElementById('fechaInicioPicker').value = '1950-01-01'; return; }
    document.getElementById('fechaInicioDia').value  = dia;
    document.getElementById('fechaInicioMes').value  = mes;
    document.getElementById('fechaInicioAnio').value = anio;
    if (fullEl) fullEl.value = val;
    const finPicker = document.getElementById('fechaFinPicker');
    if (finPicker) finPicker.min = val;
};

window.syncFechaFin = function (val) {
    const fullEl = document.getElementById('fechaFinFull');
    if (!val) {
        ['fechaFinDia','fechaFinMes','fechaFinAnio'].forEach(id => document.getElementById(id).value = '');
        if (fullEl) fullEl.value = '';
        return;
    }
    const [anio, mes, dia] = val.split('-');
    document.getElementById('fechaFinDia').value  = dia;
    document.getElementById('fechaFinMes').value  = mes;
    document.getElementById('fechaFinAnio').value = anio;
    if (fullEl) fullEl.value = val;
};

/* ── Toggle trabajo actual ── */
window.toggleFechaFin = function (checkbox) {
    const group     = document.getElementById('fechaFinGroup');
    const picker    = document.getElementById('fechaFinPicker');
    const required  = document.getElementById('fechaFinRequired');
    const hiddens   = ['fechaFinDia','fechaFinMes','fechaFinAnio'];

    if (checkbox.checked) {
        picker.disabled = true;
        picker.value = '';
        hiddens.forEach(id => document.getElementById(id).value = '');
        group.style.opacity = '0.4';
        if (required) required.style.display = 'none';
    } else {
        picker.disabled = false;
        group.style.opacity = '1';
        if (required) required.style.display = '';
    }
};

/* ── Contador de caracteres ── */
window.updateCounter = function(inputId, counterId) {
    const el  = document.getElementById(inputId) || document.querySelector(`[name="${inputId}"]`);
    const cnt = document.getElementById(counterId);
    if (el && cnt) cnt.textContent = el.value.length;
};

/* ── Reset completo del formulario ── */
window.resetForm = function () {
    document.getElementById('experienciaForm').reset();
    document.getElementById('fechaFinGroup').style.opacity = '1';
    const finPicker = document.getElementById('fechaFinPicker');
    if (finPicker) { finPicker.disabled = false; finPicker.value = ''; }
    const iniPicker = document.getElementById('fechaInicioPicker');
    if (iniPicker) iniPicker.value = '';
    ['fechaFinDia','fechaFinMes','fechaFinAnio','fechaInicioDia','fechaInicioMes','fechaInicioAnio'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.disabled = false; el.value = ''; }
    });
    // Reset counters
    ['empresaCount','locationCount','descripcionCount'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '0';
    });

    // Restaurar lista a un solo dropdown vacío
    const list = document.getElementById('cargos-list');
    list.innerHTML = '';
    const div = document.createElement('div');
    div.innerHTML = crearDropdownHTML(0);
    list.appendChild(div.firstElementChild);
    // El primero no se puede eliminar
    list.querySelector('.btn-remove-cargo').disabled = true;

    ocultarCargosError();
    actualizarBotonesRemover();
};

/* ── Validación global al submit ── */
document.getElementById('experienciaForm').addEventListener('submit', function (e) {
    let isValid = true;

    // Empresa
    const empresa      = document.getElementById('empresa').value.trim();
    const empresaError = document.getElementById('empresaError');
    if (!empresa) { empresaError.classList.remove('hidden'); isValid = false; }
    else          { empresaError.classList.add('hidden'); }

    // Cargos
    if (!validarCargos()) isValid = false;

    // Fecha inicio
    const inicioDia        = document.getElementById('fechaInicioDia').value;
    const inicioMes        = document.getElementById('fechaInicioMes').value;
    const inicioAnio       = document.getElementById('fechaInicioAnio').value;
    const fechaInicioError = document.getElementById('fechaInicioError');
    if (!inicioDia || !inicioMes || !inicioAnio) {
        fechaInicioError.classList.remove('hidden'); isValid = false;
    } else { fechaInicioError.classList.add('hidden'); }

    // Fecha fin — obligatoria si no es trabajo actual
    const finDia        = document.getElementById('fechaFinDia').value;
    const finMes        = document.getElementById('fechaFinMes').value;
    const finAnio       = document.getElementById('fechaFinAnio').value;
    const actual        = document.getElementById('trabajoActual').checked;
    const fechaFinError = document.getElementById('fechaFinError');

    if (!actual && (!finDia || !finMes || !finAnio)) {
        fechaFinError.textContent = 'Selecciona la fecha de fin o marca "Trabajo actual".';
        fechaFinError.classList.remove('hidden'); isValid = false;
    } else if (!actual && finDia && finMes && finAnio && inicioDia && inicioMes && inicioAnio) {
        const inicio = new Date(inicioAnio, parseInt(inicioMes) - 1, parseInt(inicioDia));
        const fin    = new Date(finAnio,    parseInt(finMes) - 1,    parseInt(finDia));
        if (fin < inicio) {
            fechaFinError.textContent = 'La fecha de fin no puede ser anterior a la de inicio.';
            fechaFinError.classList.remove('hidden'); isValid = false;
        } else { fechaFinError.classList.add('hidden'); }
    } else { fechaFinError.classList.add('hidden'); }

    if (!isValid) e.preventDefault();
});

/* ── Init ── */
window.addEventListener('DOMContentLoaded', function () {
    const cb = document.getElementById('trabajoActual');
    if (cb && cb.checked) toggleFechaFin(cb);
    actualizarBotonesRemover();

    // También actualizar el ícono del primer btn-remove-cargo que viene del blade
    document.querySelectorAll('#cargos-list .btn-remove-cargo').forEach(btn => {
        if (btn.textContent.trim() === '×') btn.innerHTML = TRASH_ICON;
    });

    ['empresa','location','descripcion'].forEach(name => {
        const el  = document.querySelector(`[name="${name}"]`);
        const map = { empresa:'empresaCount', location:'locationCount', descripcion:'descripcionCount' };
        if (el && map[name]) {
            const cnt = document.getElementById(map[name]);
            if (cnt) cnt.textContent = el.value.length;
        }
    });
    const iniAnio = document.getElementById('fechaInicioAnio')?.value;
    const iniMes  = document.getElementById('fechaInicioMes')?.value;
    const iniDia  = document.getElementById('fechaInicioDia')?.value;
    if (iniAnio && iniMes && iniDia) {
        const p = document.getElementById('fechaInicioPicker');
        if (p) p.value = `${iniAnio}-${iniMes}-${iniDia}`;
    }
    const finAnio = document.getElementById('fechaFinAnio')?.value;
    const finMes  = document.getElementById('fechaFinMes')?.value;
    const finDia  = document.getElementById('fechaFinDia')?.value;
    if (finAnio && finMes && finDia) {
        const p = document.getElementById('fechaFinPicker');
        if (p) p.value = `${finAnio}-${finMes}-${finDia}`;
    }
});