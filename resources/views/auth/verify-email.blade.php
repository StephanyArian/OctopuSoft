<x-guest-layout>
    <style>
        .ve-bg { min-height:100vh; background:#d4eeea; display:flex; align-items:center; justify-content:center; padding:1.5rem; font-family:'Figtree',sans-serif; }
        .ve-card { background:linear-gradient(160deg,#3d0a28 0%,#4a1030 60%,#2d0a1e 100%); border-radius:1.5rem; padding:2.5rem 2rem 2rem; width:100%; max-width:400px; display:flex; flex-direction:column; align-items:center; gap:1rem; box-shadow:0 30px 70px rgba(45,10,30,0.45); position:relative; }
        .ve-back { position:absolute; top:1.2rem; left:1.4rem; color:#c8cdd8; font-size:0.85rem; font-weight:600; display:flex; align-items:center; gap:0.3rem; text-decoration:none; transition:color 0.2s; }
        .ve-back:hover { color:#fff; }
        .ve-steps { display:flex; align-items:center; gap:0.4rem; margin-top:0.5rem; }
        .ve-step-dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,0.25); }
        .ve-step-dot.done { background:#0abf9e; }
        .ve-step-dot.active { background:#0abf9e; opacity:0.5; }
        .ve-step-line { width:28px; height:2px; background:rgba(255,255,255,0.15); border-radius:2px; }
        .ve-step-line.done { background:#0abf9e; opacity:0.4; }
        .ve-icon { width:68px; height:68px; border-radius:50%; background:rgba(255,255,255,0.07); border:2px solid rgba(10,191,158,0.25); display:flex; align-items:center; justify-content:center; color:#0abf9e; margin:0.5rem 0; }
        .ve-title { color:#fff; font-size:1.8rem; font-weight:800; text-align:center; letter-spacing:-0.5px; margin:0; }
        .ve-text { color:#b0b8cc; font-size:0.88rem; text-align:center; line-height:1.6; max-width:300px; }
        .ve-alert { width:100%; background:rgba(10,191,158,0.12); border:1px solid rgba(10,191,158,0.3); color:#0abf9e; border-radius:0.75rem; padding:0.7rem 1rem; font-size:0.83rem; font-weight:500; display:flex; align-items:center; gap:0.5rem; }
        .ve-btn-primary { width:100%; padding:0.95rem; border-radius:0.85rem; border:none; cursor:pointer; font-family:'Figtree',sans-serif; font-size:1rem; font-weight:700; background:linear-gradient(135deg,#0abf9e,#07866e); color:#fff; box-shadow:0 4px 15px rgba(10,191,158,0.35); transition:transform 0.2s,box-shadow 0.2s; }
        .ve-btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 22px rgba(10,191,158,0.45); }
        .ve-divider { width:100%; display:flex; align-items:center; gap:0.75rem; }
        .ve-divider-line { flex:1; height:1px; background:rgba(255,255,255,0.1); }
        .ve-divider-dot { width:5px; height:5px; border-radius:50%; background:rgba(255,255,255,0.2); }
        .ve-btn-secondary { width:100%; padding:0.85rem; border-radius:0.85rem; border:1px solid rgba(255,255,255,0.12); cursor:pointer; font-family:'Figtree',sans-serif; font-size:0.9rem; font-weight:600; background:rgba(255,255,255,0.06); color:#b0b8cc; transition:background 0.2s,color 0.2s; }
        .ve-btn-secondary:hover { background:rgba(255,255,255,0.11); color:#fff; }
        .ve-hint { color:#7a8298; font-size:0.76rem; text-align:center; line-height:1.5; padding-top:0.25rem; }
        .ve-hint strong { color:#a0a8bc; }
        form { width:100%; }
        @media (max-width:480px) { .ve-card { padding:2rem 1.25rem 1.5rem; } .ve-title { font-size:1.5rem; } }
    </style>

    <div class="ve-bg">
        <div class="ve-card">

         <form method="POST" action="{{ route('register.cancel') }}">
    @csrf
    <button type="submit" class="ve-back" style="background:none;border:none;cursor:pointer;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
        Volver
    </button>
</form>
            <div class="ve-steps">
                <span class="ve-step-dot done"></span>
                <span class="ve-step-line done"></span>
                <span class="ve-step-dot active"></span>
                <span class="ve-step-line"></span>
                <span class="ve-step-dot"></span>
            </div>

            <div class="ve-icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="3"/>
                    <polyline points="2,4 12,13 22,4"/>
                </svg>
            </div>

            <h1 class="ve-title">Revisa tu correo</h1>

            <p class="ve-text">
                Te enviamos un enlace de verificación. Haz clic en él para <strong style="color:#fff;">activar tu cuenta</strong>.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="ve-alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    Nuevo enlace enviado exitosamente.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="ve-btn-primary">
                    Reenviar enlace de verificación
                </button>
            </form>

            <div class="ve-divider">
                <div class="ve-divider-line"></div>
                <div class="ve-divider-dot"></div>
                <div class="ve-divider-line"></div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ve-btn-secondary">
                    Cerrar sesión
                </button>
            </form>

            <p class="ve-hint">
                ¿No encuentras el correo? Revisa tu carpeta de <strong>spam</strong>.
            </p>

        </div>
    </div>
</x-guest-layout>