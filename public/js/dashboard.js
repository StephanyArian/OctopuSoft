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

// ── VALIDACIÓN DEL FORMULARIO ───────────────────────────────
document.getElementById('profileForm')?.addEventListener('submit', function (e) {
    let isValid = true;

    // Nombre
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

    // Ubicación (Autocomplete y Validación Real)
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

    // Biografía (500 caracteres - CORREGIDO)
    const bio = document.getElementById('bio').value;
    const bioError = document.getElementById('bioError');
    
    // Crear un elemento temporal para parsear el HTML y obtener el texto plano de forma segura
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = bio;
    let text = tempDiv.textContent || tempDiv.innerText || '';
    
    // Quitar el salto de línea final
    if (text.length > 0 && text.charCodeAt(text.length - 1) === 10) {
        text = text.slice(0, -1);
    }
    
    // Quitar todos los espacios para el conteo
    const cleanText = text.replace(/\s/g, '');
    
    if (cleanText.length > 500) {
        bioError.classList.remove('hidden');
        bioError.textContent = 'La biografía no puede exceder los 500 caracteres (sin contar espacios)';
        isValid = false;
    } else {
        bioError.classList.add('hidden');
    }

    // Foto
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
    // ── AUTOCOMPLETADO DE UBICACIÓN ──────────────────────────────
    const locationInput = document.getElementById('location');
    const suggestionsDiv = document.getElementById('locationSuggestions');
    const locationError = document.getElementById('locationError');
    
    if (locationInput && suggestionsDiv) {
        // Inicializar dataset
        locationInput.dataset.isValidLocation = locationInput.value.trim() !== '' ? 'true' : 'false';
        let debounceTimeout;

        locationInput.addEventListener('input', function() {
            locationInput.dataset.isValidLocation = 'false';
            const query = this.value.trim();
            
            clearTimeout(debounceTimeout);
            if (query.length < 3) {
                suggestionsDiv.innerHTML = '';
                suggestionsDiv.style.display = 'none';
                return;
            }
            
            debounceTimeout = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1&limit=5`, {
                    headers: {
                        'User-Agent': 'OctopuSoft-Portfolio-App'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    suggestionsDiv.innerHTML = '';
                    if (data && data.length > 0) {
                        suggestionsDiv.style.display = 'block';
                        data.forEach(item => {
                            const address = item.address;
                            const city = address.city || address.town || address.village || address.suburb || address.municipality || '';
                            const state = address.state || address.region || '';
                            const country = address.country || '';
                            
                            let displayText = '';
                            if (city) displayText += city;
                            if (state) displayText += (displayText ? ', ' : '') + state;
                            if (country) displayText += (displayText ? ', ' : '') + country;
                            
                            if (!displayText) {
                                displayText = item.display_name;
                            }
                            
                            // Limitar tamaño a un máximo de 30 caracteres
                            if (displayText.length > 30) {
                                displayText = '';
                                if (city) displayText += city;
                                if (country) displayText += (displayText ? ', ' : '') + country;
                                if (displayText.length > 30) {
                                    displayText = displayText.substring(0, 30);
                                }
                            }
                            
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.style.cssText = 'padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #334155; transition: background 0.2s; text-align: left;';
                            div.textContent = displayText;
                            
                            div.addEventListener('mouseover', function() {
                                this.style.backgroundColor = '#f1f5f9';
                            });
                            div.addEventListener('mouseout', function() {
                                this.style.backgroundColor = 'white';
                            });
                            
                            div.addEventListener('click', function() {
                                locationInput.value = displayText;
                                locationInput.dataset.isValidLocation = 'true';
                                suggestionsDiv.innerHTML = '';
                                suggestionsDiv.style.display = 'none';
                                if (locationError) locationError.classList.add('hidden');
                            });
                            suggestionsDiv.appendChild(div);
                        });
                    } else {
                        suggestionsDiv.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Error fetching locations:', err);
                });
            }, 400);
        });
        
        // Cerrar sugerencias al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!locationInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.innerHTML = '';
                suggestionsDiv.style.display = 'none';
            }
        });
    }

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

    const inputBio = document.getElementById('bio');
    const contadorBio = document.getElementById('contadorBio');
    let lastValidHtml = quill.root.innerHTML;

    function getCharacterCount() {
        // Obtener texto plano y eliminar el salto de línea final de Quill
        let text = quill.getText();
        // Quitar el carácter de nueva línea al final si existe
        if (text.length > 0 && text.charCodeAt(text.length - 1) === 10) {
            text = text.slice(0, -1);
        }
        // Quitar todos los espacios para el conteo
        return text.replace(/\s/g, '').length;
    }

    function actualizarContador() {
        const len = getCharacterCount();
        if (contadorBio) {
            contadorBio.textContent = len + '/500 caracteres';
            contadorBio.style.color = len > 500 ? '#ef4444' : '#94a3b8';
        }
    }

    quill.on('text-change', function () {
        const len = getCharacterCount();
        if (len > 500) {
            // Revertir al último HTML válido
            quill.root.innerHTML = lastValidHtml;
            actualizarContador();
        } else {
            lastValidHtml = quill.root.innerHTML;
            if (inputBio) inputBio.value = quill.root.innerHTML;
            actualizarContador();
        }
    });

    actualizarContador();
});