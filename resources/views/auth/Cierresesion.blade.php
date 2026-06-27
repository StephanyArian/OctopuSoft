{{--
    resources/views/auth/Cierresesion.blade.php
--}}

<div id="logout-overlay" class="logout-wrapper">
    <div class="logout-card">

        <div class="logout-header">
            <div class="icon-wrap">
                <svg class="lock-svg" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="6" y="17" width="26" height="18" rx="5" fill="none" stroke="#07866e" stroke-width="2"/>
                    <path d="M12 17V12.5C12 8.91 14.91 6 18.5 6h1C23.09 6 26 8.91 26 12.5V17"
                          stroke="#07866e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="19" cy="26" r="2.4" fill="#07866e"/>
                    <line x1="19" y1="28.4" x2="19" y2="31" stroke="#07866e" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h1>¿Seguro que quieres salir?</h1>
        </div>

        <p class="subtitle">
            Tu sesión se cerrará y volverás a la pantalla de inicio.
        </p>

        <div class="logout-actions">
            <button type="button" class="cancel-btn" onclick="closeLogoutModal()">
                ¡No, quedarme!
            </button>

            <form method="POST" action="{{ route('logout') }}" id="logout-form" style="margin:0;">
                @csrf
                <button type="button" class="confirm-btn" onclick="cerrarSesion()">
                    Sí, salir
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.logout-wrapper {
    display: none !important;
    position: fixed !important;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.6) !important;
    z-index: 999999 !important;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.logout-wrapper.active {
    display: flex !important;
}

.logout-wrapper .logout-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 28px 30px;
    max-width: 380px;
    width: 100%;
    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.25);
    text-align: left;
    position: relative;
    animation: logoutCardPop 0.18s ease;
}

@keyframes logoutCardPop {
    from { transform: scale(0.96); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}

.logout-wrapper .logout-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.logout-wrapper .icon-wrap {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    border-radius: 50%;
    background: rgba(7, 134, 110, 0.12);
    border: 1px solid rgba(7, 134, 110, 0.30);
    display: flex;
    align-items: center;
    justify-content: center;
}

.logout-wrapper .lock-svg {
    width: 18px;
    height: 18px;
}

.logout-wrapper h1 {
    font-family: Arial, sans-serif;
    font-size: 19px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.logout-wrapper .subtitle {
    font-size: 14px;
    color: #5a5a5a;
    line-height: 1.5;
    margin: 0 0 24px 0;
    padding-left: 50px;
}

.logout-wrapper .logout-actions {
    display: flex;
    gap: 12px;
}

.logout-wrapper .logout-actions form {
    flex: 1;
}

.logout-wrapper .cancel-btn,
.logout-wrapper .confirm-btn {
    flex: 1;
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.15s ease;
}

.logout-wrapper .cancel-btn {
    background: #e9e9ec;
    color: #2d2d2d;
}

.logout-wrapper .cancel-btn:hover {
    background: #dcdce0;
}

.logout-wrapper .confirm-btn {
    background: linear-gradient(135deg, #0abf9e, #07866e);
    color: #ffffff;
}

.logout-wrapper .confirm-btn:hover {
    opacity: 0.9;
}
</style>

<script>
function openLogoutModal() {
    const overlay = document.getElementById('logout-overlay');
    if (overlay) {
        overlay.classList.add('active');
    } else {
        console.error('No se encontró #logout-overlay. Falta incluir el partial auth.Cierresesion en esta página.');
    }
}

function closeLogoutModal() {
    const overlay = document.getElementById('logout-overlay');
    if (overlay) overlay.classList.remove('active');
}

function cerrarSesion() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('portfolioData');

    sessionStorage.removeItem('token');
    sessionStorage.removeItem('user');
    sessionStorage.removeItem('portfolioData');

    document.cookie.split(";").forEach(function (c) {
        document.cookie = c
            .replace(/^ +/, "")
            .replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
    });

    document.getElementById('logout-form').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('logout-overlay');
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) closeLogoutModal();
        });
    }
});
</script>