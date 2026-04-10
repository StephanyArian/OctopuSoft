<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cerrar Sesión - OctopuSoft</title>
  <link rel="stylesheet" href="../css/styles.css" />
  <style>
    .user-name  { font-size: 16px; font-weight: 600; text-align: center; margin: 0 0 0.2rem; }
    .user-email { font-size: 13px; color: var(--text-muted); text-align: center; margin: 0 0 1.6rem; }
    .info-text  { font-size: 13px; color: var(--text-muted); text-align: center; line-height: 1.6; margin-bottom: 1.6rem; }
    .card-subtitle { font-size: 13px; color: var(--text-muted); text-align: center; margin: -1rem 0 1.6rem; }
  </style>
</head>
<body>
  <div class="card">
    <h1 class="card-title">Cerrar sesión</h1>
    <p class="card-subtitle">¿Estás seguro que deseas salir?</p>
 
    <!-- Avatar con iniciales del usuario -->
    <div class="avatar" id="avatar">--</div>
    <p class="user-name" id="user-name">Cargando...</p>
    <p class="user-email" id="user-email"></p>
 
    <hr class="divider" />
 
    <p class="info-text">
      Al cerrar sesión se eliminarán los tokens de autenticación
      y serás redirigido a la página principal.
    </p>
 
    <button class="btn-primary" onclick="cerrarSesion()">Cerrar sesión</button>
    <button class="btn-secondary" onclick="cancelar()">Cancelar</button>
 
    <div class="toast" id="toast"></div>
  </div>
 
  <script src="../js/utils.js"></script>
  <script>
    // HU-RU5 Criterio 2: solo visible si está autenticado
    requiereAutenticacion();
 
    // Mostrar datos del usuario actual
    const user = Auth.obtener();
    if (user) {
      const iniciales = (user.nombre[0] + (user.apellido ? user.apellido[0] : '')).toUpperCase();
      document.getElementById('avatar').textContent = iniciales;
      document.getElementById('user-name').textContent = `${user.nombre} ${user.apellido || ''}`.trim();
      document.getElementById('user-email').textContent = user.correo;
    }
 
    function cerrarSesion() {
      // HU-RU5 Criterio 1: destruir sesión y tokens
      Auth.cerrarSesion();
 
      showToast('toast', 'Sesión cerrada. Redirigiendo a inicio...');
 
      // HU-RU5 Criterio 3: reemplazar historial para bloquear botón "Atrás"
      setTimeout(() => {
        history.replaceState(null, '', '../index.html');
        window.location.replace('../index.html');
      }, 1500);
    }
 
    function cancelar() {
      window.location.href = 'portafolio.html';
    }
  </script>
</body>
</html>
 