// ── FOTO DE PERFIL ──────────────────────────────────────────
function previewPhoto(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const preview  = document.getElementById('photoPreview');
        const photoLink = document.getElementById('photoLink');
        const photoIcon  = document.getElementById('photoIcon');
        const photoLabel = document.getElementById('photoLabel');

        if (preview)   { preview.src = e.target.result; preview.style.display = 'block'; }
        if (photoLink) { photoLink.dataset.src = e.target.result; photoLink.style.display = 'block'; }
        if (photoIcon)  photoIcon.style.display  = 'none';
        if (photoLabel) photoLabel.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function openDeleteModal() {
    document.getElementById('delete-modal')?.classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('delete-modal')?.classList.remove('active');
}

function openPhotoPreview(src) {
    if (!src || src === '#') return;
    const lightbox = document.getElementById('photo-lightbox');
    const img      = document.getElementById('lightbox-img');
    if (lightbox && img) {
        img.src = src;
        lightbox.classList.add('active');
    }
}

// ── VALIDACIÓN DEL FORMULARIO ───────────────────────────────
document.getElementById('profileForm')?.addEventListener('submit', function(e) {
    let isValid = true;

    // Nombre
    const name      = document.getElementById('name').value.trim();
    const nameError = document.getElementById('nameError');
    const nameRegex = /^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/;

    if (!name) {
        nameError.classList.remove('hidden');
        nameError.textContent = 'El nombre es obligatorio';
        isValid = false;
    } else if (name.length > 30) {
        nameError.classList.remove('hidden');
        nameError.textContent = 'El nombre no puede exceder los 30 caracteres';
        isValid = false;
    } else if (!nameRegex.test(name)) {
        nameError.classList.remove('hidden');
        nameError.textContent = 'El nombre solo puede contener letras y espacios';
        isValid = false;
    } else {
        nameError.classList.add('hidden');
    }

    // Título profesional
    const title = document.getElementById('title').value.trim();
    let titleError = document.getElementById('titleError');
    if (!titleError) {
        titleError = document.createElement('div');
        titleError.id = 'titleError';
        titleError.className = 'error-message hidden';
        document.getElementById('title').parentNode.appendChild(titleError);
    }
    const titleRegex = /^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/;

    if (title.length > 30) {
        titleError.classList.remove('hidden');
        titleError.textContent = 'El título no puede exceder los 30 caracteres';
        isValid = false;
    } else if (title && !titleRegex.test(title)) {
        titleError.classList.remove('hidden');
        titleError.textContent = 'El título solo puede contener letras y espacios';
        isValid = false;
    } else {
        titleError.classList.add('hidden');
    }

    // Ubicación
    const location = document.getElementById('location').value.trim();
    let locationError = document.getElementById('locationError');
    if (!locationError) {
        locationError = document.createElement('div');
        locationError.id = 'locationError';
        locationError.className = 'error-message hidden';
        document.getElementById('location').parentNode.appendChild(locationError);
    }
    const locationRegex = /^[a-zA-ZáéíóúñÁÉÍÓÚÑ0-9\s.,\-]+$/;

    if (location.length > 30) {
        locationError.classList.remove('hidden');
        locationError.textContent = 'La ubicación no puede exceder los 30 caracteres';
        isValid = false;
    } else if (location && !locationRegex.test(location)) {
        locationError.classList.remove('hidden');
        locationError.textContent = 'La ubicación solo puede contener letras, números, puntos, comas y guiones';
        isValid = false;
    } else {
        locationError.classList.add('hidden');
    }

    // Biografía
    const bio      = document.getElementById('bio').value;
    const bioError = document.getElementById('bioError');
    if (bio.length > 5000) {
        bioError.classList.remove('hidden');
        isValid = false;
    } else {
        bioError.classList.add('hidden');
    }

    // Foto
    const photo      = document.getElementById('photoInput').files[0];
    const photoError = document.getElementById('photoError');
    if (photo) {
        if (!['image/jpeg','image/png'].includes(photo.type)) {
            photoError.classList.remove('hidden');
            photoError.textContent = 'Formato no permitido. Use JPG o PNG';
            isValid = false;
        } else if (photo.size > 2 * 1024 * 1024) {
            photoError.classList.remove('hidden');
            photoError.textContent = 'La imagen no puede superar los 2MB';
            isValid = false;
        } else {
            photoError.classList.add('hidden');
        }
    }

    if (!isValid) e.preventDefault();
});

// ── QUILL (BIOGRAFÍA) ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Quill === 'undefined') return;
    const qc = document.getElementById('quillEditor');
    if (!qc) return;

    const Font = Quill.import('formats/font');
    Font.whitelist = ['sans-serif','serif','monospace','arial','times','courier'];
    Quill.register(Font, true);

    const quill = new Quill('#quillEditor', {
        theme: 'snow',
        placeholder: 'Escribe una breve presentación sobre ti...',
        modules: {
            toolbar: [
                [{ font: ['sans-serif','serif','monospace','arial','times','courier'] }, { size: [] }],
                ['bold','italic','underline','strike'],
                [{ color: [] }, { background: [] }],
                [{ script: 'super' }, { script: 'sub' }],
                [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
                [{ align: [] }],
                ['link'],
                ['clean']
            ]
        }
    });

    const inputBio    = document.getElementById('bio');
    const contadorBio = document.getElementById('contadorBio');
    let lastValidHtml = quill.root.innerHTML;

    function actualizarContador() {
        const len = Math.max(0, quill.getText().length - 1);
        if (contadorBio) {
            contadorBio.textContent = len + '/5000 caracteres';
            contadorBio.style.color = len > 5000 ? '#ef4444' : '#94a3b8';
        }
    }

    quill.on('text-change', function() {
        const len = Math.max(0, quill.getText().length - 1);
        if (len > 5000) {
            quill.root.innerHTML = lastValidHtml;
        } else {
            lastValidHtml = quill.root.innerHTML;
            if (inputBio) inputBio.value = quill.root.innerHTML;
        }
        actualizarContador();
    });

    actualizarContador();
});