
        function previewPhoto(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('photoPreview');
            const photoIcon = document.getElementById('photoIcon');
            const photoLabel = document.getElementById('photoLabel');
            const photoLink = document.getElementById('photoLink');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (photoLink) {
                        photoLink.dataset.src = e.target.result;
                        photoLink.style.display = 'block';
                    }
                    if (photoIcon) photoIcon.style.display = 'none';
                    if (photoLabel) photoLabel.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validación de NOMBRE COMPLETO (solo letras, espacios y acentos)
            const name = document.getElementById('name').value.trim();
            const nameError = document.getElementById('nameError');
            const nameRegex = /^[a-zA-ZáéíóúñÁÉÍÓÚÑ\s]+$/;
            
            if (name === '') {
                nameError.classList.remove('hidden');
                nameError.textContent = 'El nombre es obligatorio';
                isValid = false;
            } 
            else if (name.length > 30) {
                nameError.classList.remove('hidden');
                nameError.textContent = 'El nombre no puede exceder los 30 caracteres';
                isValid = false;
            }
            else if (!nameRegex.test(name)) {
                nameError.classList.remove('hidden');
                nameError.textContent = 'El nombre solo puede contener letras y espacios';
                isValid = false;
            }
            else {
                nameError.classList.add('hidden');
            }
            
            // Validación de TÍTULO PROFESIONAL (solo letras y espacios)
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
                titleError.textContent = 'El título profesional no puede exceder los 30 caracteres';
                isValid = false;
            }
            else if (title !== '' && !titleRegex.test(title)) {
                titleError.classList.remove('hidden');
                titleError.textContent = 'El título profesional solo puede contener letras y espacios';
                isValid = false;
            }
            else {
                titleError.classList.add('hidden');
            }
            
            // Validación de UBICACIÓN (letras, números, espacios, comas y guiones)
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
            }
            else if (location !== '' && !locationRegex.test(location)) {
                locationError.classList.remove('hidden');
                locationError.textContent = 'La ubicación solo puede contener letras, números, espacios, puntos, comas y guiones';
                isValid = false;
            }
            else {
                locationError.classList.add('hidden');
            }
            
            // Validación de BIOGRAFÍA
            const bio = document.getElementById('bio').value;
            const bioError = document.getElementById('bioError');
            if (bio.length > 500) {
                bioError.classList.remove('hidden');
                isValid = false;
            } else {
                bioError.classList.add('hidden');
            }
            
            // Validación de FOTO
            const photo = document.getElementById('photoInput').files[0];
            const photoError = document.getElementById('photoError');
            if (photo) {
                const validTypes = ['image/jpeg', 'image/png'];
                if (!validTypes.includes(photo.type)) {
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
            
            if (!isValid) {
                e.preventDefault();
            }
        });

       

        // Función para cargar perfil personal (vista por defecto)
        function cargarPerfilPersonal() {
            // El perfil personal ya está cargado por defecto
            // Esta función se puede usar para recargar si es necesario
            location.reload();
        }

        // Evento click en Proyectos
       // document.getElementById('proyectosLink')?.addEventListener('click', function(e) {
         //   e.preventDefault();
           // cargarProyectos();
            
            // Actualizar clase activa en el sidebar
            //document.querySelectorAll('.sidebar-item').forEach(item => {
              //  item.classList.remove('active');
            //});
            //this.classList.add('active');
        //});

        // Evento click en Personal (recargar página)
        document.getElementById('personalLink')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ url("/dashboard") }}';
        });
        // Funciones para el modal de eliminar foto
        function openDeleteModal() {
            const modal = document.getElementById('delete-modal');
            if (modal) {
                modal.classList.add('active');
            }
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            if (modal) {
                modal.classList.remove('active');
            }
        }
        
        // Función para abrir la foto en el lightbox
        function openPhotoPreview(src) {
            if (!src || src === '#') return;
            const lightbox = document.getElementById('photo-lightbox');
            const lightboxImg = document.getElementById('lightbox-img');
            if (lightbox && lightboxImg) {
                lightboxImg.src = src;
                lightbox.classList.add('active');
            }
        }
    