// ── FOTO DE PERFIL ──────────────────────────────────────────
function previewPhoto(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('photoPreview');
        const photoLink = document.getElementById('photoLink');
        const photoIcon = document.getElementById('photoIcon');
        const photoLabel = document.getElementById('photoLabel');

        if (preview) { preview.src = e.target.result; preview.style.display = 'block'; }
        if (photoLink) { photoLink.dataset.src = e.target.result; photoLink.style.display = 'block'; }
        if (photoIcon) photoIcon.style.display = 'none';
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
    const img = document.getElementById('lightbox-img');
    if (lightbox && img) {
        img.src = src;
        lightbox.classList.add('active');
    }
}

// ── BIO: conteo sin espacios (igual que el servidor) ────────
const BIO_LIMITE = 500;

function bioTextoSinEspacios(texto) {
    if (!texto) return '';
    let text = texto;
    if (text.length > 0 && text.charCodeAt(text.length - 1) === 10) {
        text = text.slice(0, -1);
    }
    return text.replace(/[\s\u00a0\u1680\u2000-\u200b\u202f\u205f\u3000\ufeff]/g, '');
}

function bioConteoDesdeHtml(html) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html || '';
    return bioTextoSinEspacios(tempDiv.textContent || tempDiv.innerText || '');
}

// ── VALIDACIÓN DEL FORMULARIO ───────────────────────────────
document.getElementById('profileForm')?.addEventListener('submit', function (e) {
    let isValid = true;

    const name = document.getElementById('name').value.trim();
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

    const locationInput = document.getElementById('location');
    const location = locationInput ? locationInput.value.trim() : '';
    const locationError = document.getElementById('locationError');
    const isLocationSelected = locationInput && locationInput.dataset.isValidLocation === 'true';

    if (location && !isLocationSelected) {
        locationError.classList.remove('hidden');
        locationError.textContent = 'Por favor, selecciona una ubicación de la lista de sugerencias.';
        isValid = false;
    } else if (location.length > 30) {
        locationError.classList.remove('hidden');
        locationError.textContent = 'La ubicación no puede exceder los 30 caracteres';
        isValid = false;
    } else {
        locationError.classList.add('hidden');
    }

    const inputBio = document.getElementById('bio');
    const bioError = document.getElementById('bioError');
    if (window.quillBio) {
        inputBio.value = window.quillBio.root.innerHTML;
    }
    const cleanText = bioConteoDesdeHtml(inputBio ? inputBio.value : '');

    if (cleanText.length > BIO_LIMITE) {
        bioError.classList.remove('hidden');
        bioError.textContent = 'La biografía no puede exceder los 500 caracteres (sin contar espacios)';
        isValid = false;
    } else {
        bioError.classList.add('hidden');
    }

    const photo = document.getElementById('photoInput').files[0];
    const photoError = document.getElementById('photoError');
    if (photo) {
        if (!['image/jpeg', 'image/png'].includes(photo.type)) {
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

document.addEventListener('DOMContentLoaded', function () {
    // ── AUTOCOMPLETADO DE UBICACIÓN (Nominatim) ────────────────
    const NOMINATIM = 'https://nominatim.openstreetmap.org/search';
    const locationInput = document.getElementById('location');
    const suggestionsList = document.getElementById('locationSuggestions');
    const locationError = document.getElementById('locationError');
    let locationDebounce = null;

    function formatLocationLabel(item) {
        const a = item.address || {};
        const partes = [];
        if (a.city || a.town || a.village || a.municipality) {
            partes.push(a.city || a.town || a.village || a.municipality);
        }
        if (a.state || a.region) partes.push(a.state || a.region);
        if (a.country) partes.push(a.country);
        if (partes.length) return partes.join(', ');
        return item.display_name.split(',').slice(0, 3).join(',').trim();
    }

    function truncateLocation(label) {
        return label.length > 30 ? label.substring(0, 30) : label;
    }

    if (locationInput && suggestionsList) {
        locationInput.dataset.isValidLocation = locationInput.value.trim() !== '' ? 'true' : 'false';

        function showLocationSuggestions(items) {
            suggestionsList.innerHTML = '';
            if (!items.length) {
                suggestionsList.classList.add('hidden');
                return;
            }
            items.forEach(function (item) {
                const label = truncateLocation(formatLocationLabel(item));
                const li = document.createElement('li');
                li.textContent = label;
                li.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    locationInput.value = label;
                    locationInput.dataset.isValidLocation = 'true';
                    suggestionsList.classList.add('hidden');
                    suggestionsList.innerHTML = '';
                    if (locationError) locationError.classList.add('hidden');
                });
                suggestionsList.appendChild(li);
            });
            suggestionsList.classList.remove('hidden');
        }

        locationInput.addEventListener('input', function () {
            locationInput.dataset.isValidLocation = 'false';
            const query = this.value.trim();
            clearTimeout(locationDebounce);

            if (query.length < 2) {
                suggestionsList.classList.add('hidden');
                suggestionsList.innerHTML = '';
                return;
            }

            locationDebounce = setTimeout(function () {
                fetch(
                    NOMINATIM + '?q=' + encodeURIComponent(query) + '&format=json&addressdetails=1&limit=6&accept-language=es',
                    { headers: { 'Accept-Language': 'es' } }
                )
                    .then(function (res) { return res.json(); })
                    .then(function (data) { showLocationSuggestions(data || []); })
                    .catch(function () { suggestionsList.classList.add('hidden'); });
            }, 350);
        });

        locationInput.addEventListener('blur', function () {
            setTimeout(function () { suggestionsList.classList.add('hidden'); }, 150);
            if (locationInput.value.trim() && locationInput.dataset.isValidLocation !== 'true' && locationError) {
                locationError.classList.remove('hidden');
            }
        });

        locationInput.addEventListener('focus', function () {
            if (locationError) locationError.classList.add('hidden');
        });
    }

    // ── EDITOR QUILL — BIOGRAFÍA ───────────────────────────────
    if (typeof Quill === 'undefined') return;
    const qc = document.getElementById('quillEditor');
    if (!qc) return;

    const Font = Quill.import('formats/font');
    Font.whitelist = ['sans-serif', 'serif', 'monospace', 'arial', 'times', 'courier'];
    Quill.register(Font, true);

    const quill = new Quill('#quillEditor', {
        theme: 'snow',
        placeholder: 'Escribe una breve presentación sobre ti...',
        modules: {
            toolbar: [
                [{ font: ['sans-serif', 'serif', 'monospace', 'arial', 'times', 'courier'] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ script: 'super' }, { script: 'sub' }],
                [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
                [{ align: [] }],
                ['link'],
                ['clean']
            ]
        }
    });

    window.quillBio = quill;

    const inputBio = document.getElementById('bio');
    const contadorBio = document.getElementById('contadorBio');

    if (inputBio && inputBio.value.trim()) {
        quill.root.innerHTML = inputBio.value;
    }

    function getBioCharCount() {
        return bioTextoSinEspacios(quill.getText()).length;
    }

    function actualizarContadorBio() {
        const len = getBioCharCount();
        if (contadorBio) {
            contadorBio.textContent = len + '/500 caracteres';
            contadorBio.style.color = len > BIO_LIMITE ? '#ef4444' : '#94a3b8';
            contadorBio.style.fontWeight = len > BIO_LIMITE ? 'bold' : 'normal';
        }
        if (inputBio) {
            inputBio.value = quill.root.innerHTML;
        }
    }

    quill.on('text-change', function (delta, oldDelta, source) {
        if (source !== 'user') return;

        const len = getBioCharCount();
        if (len > BIO_LIMITE) {
            quill.history.undo();
            return;
        }
        actualizarContadorBio();
    });

    actualizarContadorBio();
});
