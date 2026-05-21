document.addEventListener('DOMContentLoaded', function () {

    // ══════════════════════════════════════
    // VALIDACIÓN DEL FORMULARIO
    // ══════════════════════════════════════
    const form = document.getElementById('idiomaForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            let isValid = true;

            // Limpiar errores
            document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));

            // Validar nombre
            const nombre = document.getElementById('nombre').value.trim();
            if (!nombre) {
                document.getElementById('nombreError').classList.remove('hidden');
                document.getElementById('nombre').classList.add('error');
                isValid = false;
            }

            // Validar nivel
            const nivel = document.getElementById('nivel').value;
            if (!nivel) {
                document.getElementById('nivelError').classList.remove('hidden');
                document.getElementById('nivel').classList.add('error');
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    }

    // ══════════════════════════════════════
    // RESET FORMULARIO
    // ══════════════════════════════════════
    window.resetIdiomaForm = function () {
        const form = document.getElementById('idiomaForm');
        if (form) form.reset();
        document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));
        // Limpiar preview evidencia
        const preview = document.getElementById('evidenciaPreview');
        if (preview) { preview.innerHTML = ''; preview.classList.add('hidden'); }
        const zone = document.getElementById('uploadZoneIdioma');
        if (zone) zone.style.display = 'block';
    };

    // ══════════════════════════════════════
    // EDITAR / OCULTAR EDICIÓN
    // ══════════════════════════════════════
    window.mostrarEditar = function (id) {
        document.getElementById(`vista-${id}`).classList.add('hidden');
        document.getElementById(`edicion-${id}`).classList.remove('hidden');
    };

    window.ocultarEditar = function (id) {
        document.getElementById(`vista-${id}`).classList.remove('hidden');
        document.getElementById(`edicion-${id}`).classList.add('hidden');
    };
});

// Marcar en rojo campos con errores de servidor
document.querySelectorAll('.error-message').forEach(el => {
    if (!el.classList.contains('hidden')) {
        const input = el.closest('.form-group')?.querySelector('.form-input');
        if (input) input.classList.add('error');
    }
});

// ══════════════════════════════════════
// MANEJO DE ARCHIVOS — FORMULARIO CREAR
// ══════════════════════════════════════
const MAX_MB = 2;
const TIPOS  = ['image/jpeg', 'image/png', 'application/pdf'];

function handleDropIdioma(event) {
    event.preventDefault();
    const zone = document.getElementById('uploadZoneIdioma');
    zone.classList.remove('drag-over');
    if (event.dataTransfer.files.length > 0) {
        handleFileIdioma(event.dataTransfer.files[0]);
    }
}

function handleFileIdioma(file) {
    const errorEl = document.getElementById('evidenciaError');
    errorEl.classList.add('hidden');

    if (!file) return;

    // Validar tipo
    if (!TIPOS.includes(file.type)) {
        errorEl.textContent = 'Formato no permitido. Use JPG, PNG o PDF';
        errorEl.classList.remove('hidden');
        return;
    }

    // Validar tamaño
    if (file.size > MAX_MB * 1024 * 1024) {
        errorEl.textContent = 'El archivo no puede superar los 2MB';
        errorEl.classList.remove('hidden');
        return;
    }

    // Sincronizar con input
    const input = document.getElementById('evidenciaInput');
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;

    // Ocultar zona y mostrar preview
    document.getElementById('uploadZoneIdioma').style.display = 'none';
    mostrarPreviewIdioma(file);
}

function mostrarPreviewIdioma(file) {
    const preview = document.getElementById('evidenciaPreview');
    const isPdf   = file.type === 'application/pdf';
    const sizeMB  = (file.size / 1024 / 1024).toFixed(1);

    preview.innerHTML = `
        <span class="ev-icon">${isPdf ? '📄' : '🖼️'}</span>
        <span class="ev-name">${file.name}</span>
        <span class="ev-size">${sizeMB} MB</span>
        <span class="ev-remove" onclick="quitarEvidenciaIdioma()">✕</span>
    `;
    preview.classList.remove('hidden');
}

function quitarEvidenciaIdioma() {
    // Limpiar input
    const input = document.getElementById('evidenciaInput');
    input.value = '';

    // Ocultar preview y mostrar zona
    const preview = document.getElementById('evidenciaPreview');
    preview.innerHTML = '';
    preview.classList.add('hidden');
    document.getElementById('uploadZoneIdioma').style.display = 'block';
}

// ══════════════════════════════════════
// MANEJO DE ARCHIVOS — MODO EDICIÓN
// ══════════════════════════════════════
function handleDropEdit(event, id) {
    event.preventDefault();
    document.getElementById(`uploadZoneEdit-${id}`).classList.remove('drag-over');
    if (event.dataTransfer.files.length > 0) {
        handleFileEdit(event.dataTransfer.files[0], id);
    }
}

function handleFileEdit(file, id) {
    if (!file) return;

    if (!TIPOS.includes(file.type)) {
        alert('Formato no permitido. Use JPG, PNG o PDF');
        return;
    }
    if (file.size > MAX_MB * 1024 * 1024) {
        alert('El archivo no puede superar los 2MB');
        return;
    }

    // Sincronizar con input
    const input = document.getElementById(`evidenciaInputEdit-${id}`);
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;

    // Ocultar zona y mostrar preview
    document.getElementById(`uploadZoneEdit-${id}`).style.display = 'none';
    mostrarPreviewEdit(file, id);
}

function mostrarPreviewEdit(file, id) {
    const preview = document.getElementById(`evidenciaPreviewEdit-${id}`);
    const isPdf   = file.type === 'application/pdf';
    const sizeMB  = (file.size / 1024 / 1024).toFixed(1);

    preview.innerHTML = `
        <span class="ev-icon">${isPdf ? '📄' : '🖼️'}</span>
        <span class="ev-name">${file.name}</span>
        <span class="ev-size">${sizeMB} MB</span>
        <span class="ev-remove" onclick="quitarEvidenciaEdit(${id})">✕</span>
    `;
    preview.classList.remove('hidden');
}

function quitarEvidenciaEdit(id) {
    document.getElementById(`evidenciaInputEdit-${id}`).value = '';
    const preview = document.getElementById(`evidenciaPreviewEdit-${id}`);
    preview.innerHTML = '';
    preview.classList.add('hidden');
    document.getElementById(`uploadZoneEdit-${id}`).style.display = 'block';
}

function openDeleteModalIdioma(id, name) {
    document.getElementById('modal-idioma-name').textContent = name;
    document.getElementById('delete-form-idioma').action = '/idiomas/' + id;
    document.getElementById('delete-modal-idioma').classList.add('active');
}
function closeDeleteModalIdioma() {
    document.getElementById('delete-modal-idioma').classList.remove('active');
}
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('delete-modal-idioma');
    if (modal) modal.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModalIdioma();
    });
});