<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Generador de Portafolios Digitales | Crea tu portafolio profesional</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --burg-deep: #2d0a1e;
            --burg-mid: #4a1030;
            --burg-soft: #6b1f45;
            --teal: #0abf9e;
            --teal-light: #1de8c0;
            --teal-dim: #07866e;
            --white: #ffffff;
            --off: #f5f6f8;
            --gray-100: #edf0f4;
            --gray-300: #c8cdd8;
            --gray-500: #7a8298;
            --gray-700: #3d4459;
            --dark: #111827;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, var(--burg-deep) 0%, var(--burg-mid) 100%);
            color: var(--dark);
        }

        /* Navegación */
        .navbar {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Botones */
        .btn-primary {
            background: var(--teal);
            color: var(--white);
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: var(--teal-dim);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(10, 191, 158, 0.3);
        }

        .btn-outline {
            background: transparent;
            color: var(--teal);
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            border: 2px solid var(--teal);
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .btn-outline:hover {
            background: var(--teal);
            color: var(--white);
            transform: translateY(-2px);
        }

        /* Secciones */
        .section {
            padding: 80px 0;
        }

        .section-white {
            background: var(--white);
        }

        .section-gray {
            background: var(--off);
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 16px;
            background: linear-gradient(135deg, var(--burg-soft) 0%, var(--burg-deep) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            text-align: center;
            color: var(--gray-500);
            margin-bottom: 48px;
            font-size: 1.125rem;
        }

        /* Hero */
        .hero {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
            position: relative;
            overflow: hidden;
            background-image: url('/imagenes/escritorio.png');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        /* Overlay oscuro para que el texto se lea mejor */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 32px;
            opacity: 0.95;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-buttons .btn-outline {
            border-color: var(--white);
            color: var(--white);
        }

        .hero-buttons .btn-outline:hover {
            background: var(--white);
            color: var(--teal);
            border-color: var(--white);
        }

        /* Tarjetas de beneficios */
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .benefit-card {
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-100);
        }

        .benefit-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 30px -10px rgba(10, 191, 158, 0.2);
            border-color: var(--teal);
        }

        .benefit-icon {
            width: 70px;
            height: 70px;
            background: rgba(10, 191, 158, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }

        .benefit-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--dark);
        }

        .benefit-card p {
            color: var(--gray-500);
            line-height: 1.5;
        }

        /* Pasos */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .step {
            text-align: center;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: var(--teal);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 auto 20px;
        }

        .step h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .step p {
            color: var(--gray-500);
        }

        /* CTA */
        .cta-section {
            background: linear-gradient(135deg, var(--burg-soft) 0%, var(--burg-deep) 100%);
            color: var(--white);
            text-align: center;
            padding: 80px 20px;
            border-radius: 30px;
            margin: 40px 0;
        }

        .cta-section .btn-primary {
            background: var(--white);
            color: var(--burg-soft);
        }

        .cta-section .btn-primary:hover {
            background: var(--teal);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        footer {
            background: var(--burg-deep);
            color: var(--gray-300);
            padding: 50px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-logo h3 {
            color: var(--white);
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .footer-links h4 {
            color: var(--white);
            margin-bottom: 15px;
        }

        .footer-links a {
            display: block;
            color: var(--gray-300);
            text-decoration: none;
            margin-bottom: 10px;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--teal-light);
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            color: var(--gray-300);
            font-size: 1.5rem;
            transition: color 0.3s;
            text-decoration: none;
        }

        .social-links a:hover {
            color: var(--teal-light);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid var(--burg-mid);
            font-size: 0.875rem;
        }

        /* Navbar links */
        .nav-link {
            text-decoration: none;
            color: var(--gray-700);
            font-weight: 500;
            transition: color 0.3s;
            padding: 8px 0;
        }

        .nav-link:hover {
            color: var(--teal);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 1.75rem;
            }
            
            .navbar .container {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; flex-wrap: wrap;">
            <div class="logo">
                <h2 style="background: linear-gradient(135deg, var(--burg-soft) 0%, var(--burg-deep) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">PORTAFOLIO</h2>
            </div>
            
            <div class="nav-links" style="display: flex; gap: 32px; align-items: center; flex-wrap: wrap;">
                <a href="#inicio" class="nav-link">Inicio</a>
                <a href="#que-es" class="nav-link">¿Qué es el sistema?</a>
                <a href="#beneficios" class="nav-link">Beneficios</a>
                <a href="#como-funciona" class="nav-link">Cómo funciona</a>
                
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary" style="padding: 8px 20px;">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link" style="font-weight: 600;">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="btn-primary" style="padding: 8px 20px;">Registrarse</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="inicio" class="hero">
        <div class="container hero-content">
            <h1>Crea tu Portafolio Digital y<br>Destaca tu Talento</h1>
            <p>Nuestra plataforma te permite construir un portafolio profesional de forma fácil, rápida y moderna para mostrar tus proyectos, habilidades y experiencia al mundo.</p>
            <div class="hero-buttons">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">Registrarse</a>
                @endif
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-outline" style="border-color: white; color: white;">Iniciar sesión</a>
                @endif
            </div>
        </div>
    </section>

    <!-- ¿QUÉ ES EL SISTEMA? -->
    <section id="que-es" class="section section-white">
        <div class="container">
            <h2 class="section-title">¿Qué es este sistema?</h2>
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <p style="font-size: 1.125rem; color: var(--gray-700); margin-bottom: 24px; line-height: 1.6;">
                    Es una plataforma que te ayuda a crear y gestionar tu portfolio digital en pocos minutos, sin necesidad de conocimientos técnicos.
                </p>
                <p style="color: var(--gray-500); font-size: 1rem;">
                    Ideal para profesionales, estudiantes, desarrolladores, diseñadores y cualquier persona que quiera mostrar su trabajo de manera profesional.
                </p>
            </div>
        </div>
    </section>

    <!-- BENEFICIOS -->
    <section id="beneficios" class="section section-gray">
        <div class="container">
            <h2 class="section-title">Beneficios</h2>
            <p class="section-subtitle">Todo lo que necesitas para destacar en el mundo digital</p>
            
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">🎨</div>
                    <h3>Diseños Profesionales</h3>
                    <p>Plantillas modernas y personalizables para todos los estilos.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">🚀</div>
                    <h3>Fácil de Usar</h3>
                    <p>Crea tu portafolio en minutos, sin complicaciones técnicas.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">📱</div>
                    <h3>Acceso desde cualquier dispositivo</h3>
                    <p>Tu portafolio se ve bien en todos lados.</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">🔗</div>
                    <h3>Comparte tu Trabajo</h3>
                    <p>Comparte tu portafolio con un enlace único y profesional.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CÓMO FUNCIONA -->
    <section id="como-funciona" class="section section-white">
        <div class="container">
            <h2 class="section-title">¿Cómo funciona?</h2>
            <p class="section-subtitle">En 4 sencillos pasos ya tienes tu portafolio en línea</p>
            
            <div class="steps-grid">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Regístrate</h3>
                    <p>Crea tu cuenta en pocos pasos.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Personaliza</h3>
                    <p>Completa tu perfil y agrega tus proyectos.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Publica</h3>
                    <p>Genera tu portafolio y hazlo visible al mundo.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Comparte</h3>
                    <p>Comparte tu enlace y destaca tu talento.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <div class="container">
        <div class="cta-section">
            <h2>¿Listo para crear tu portafolio?</h2>
            <p style="margin-bottom: 10px;">Únete a miles de profesionales que ya destacan su talento</p>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-primary">Regístrate Gratis</a>
            @endif
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>PORTAFOLIO</h3>
                    <p>Crea. Comparte. Impacta.</p>
                </div>
                <div class="footer-links">
                    <h4>Enlaces</h4>
                    <a href="#inicio">Inicio</a>
                    <a href="#que-es">¿Qué es el sistema?</a>
                    <a href="#beneficios">Beneficios</a>
                    <a href="#como-funciona">Cómo funciona</a>
                </div>
                <div class="footer-links">
                    <h4>Legal</h4>
                    <a href="#">Contacto</a>
                    <a href="#">Términos</a>
                    <a href="#">Privacidad</a>
                </div>
                <div class="footer-links">
                    <h4>Redes Sociales</h4>
                    <div class="social-links">
                        <a href="#">📘</a>
                        <a href="#">🐦</a>
                        <a href="#">📷</a>
                        <a href="#">💼</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 Generador de Portafolios Digitales. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>