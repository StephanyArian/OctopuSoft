// ============================================
// DATOS SIMULADOS DEL USUARIO (SOLO FRONTEND)
// ============================================

const datosUsuario = {
    // Datos personales
    nombre: "SARAHI MOLLO",
    apellido: "",
    titulo: "ingeniera en informática",
    ciudad: "New Jersey",
    pais: "",
    biografia: "tengo muchos talentos y soy competitiva",
    
    // Habilidades blandas (puedes tener 1, 5, 10 o las que quieras)
    habilidadesBlandas: [
        "PACIENCIA",
        "puntualidad",
        "investigacion",
        "trabajo en equipo",
        "comunicacion efectiva",
        "liderazgo",
        "resolucion de problemas",
        "adaptabilidad",
        "creatividad",
        "empatia"
    ],
    
    // Habilidades técnicas
    habilidadesTecnicas: [
        { nombre: "FIGMA", nivel: "Avanzado" },
        { nombre: "Laravel", nivel: "Avanzado" },
        { nombre: "React", nivel: "Intermedio" },
        { nombre: "MySQL", nivel: "Intermedio" },
        { nombre: "JavaScript", nivel: "Avanzado" },
        { nombre: "CSS", nivel: "Avanzado" }
    ],
    
    // Experiencias laborales
    experiencias: [
        {
            empresa: "cochasoft",
            cargo: "desarrollador frontend",
            ubicacion: "New Jersey",
            fecha_inicio: "2022-11-18",
            fecha_fin: "2025-11-16",
            trabajo_actual: false,
            descripcion: "desarrollador frontend en proyectos de multicine y mas"
        },
        {
            empresa: "Tech Solutions",
            cargo: "UI/UX Designer",
            ubicacion: "Santa Cruz, Bolivia",
            fecha_inicio: "2020-01-10",
            fecha_fin: "2022-10-30",
            trabajo_actual: false,
            descripcion: "Diseño de interfaces y experiencia de usuario para aplicaciones web"
        }
    ],
    
    // Información académica
    academicas: [
        {
            institucion: "universidad UPDS",
            titulo: "LICENCIATURA EN INGENIERIA EN INFORMÁTICA",
            fecha_inicio: "2022-01-01",
            fecha_fin: "2026-09-30",
            estudio_actual: false,
            descripcion: "ESTUDIANTE A TIEMPO COMPLETO"
        },
        {
            institucion: "Instituto Tecnológico",
            titulo: "Técnico en Desarrollo Web",
            fecha_inicio: "2019-02-01",
            fecha_fin: "2021-12-31",
            estudio_actual: false,
            descripcion: "Desarrollo full stack"
        }
    ],
    
    // Proyectos
    proyectos: [
        {
            nombre: "MULTICINE ORURO",
            descripcion: "CINE FUNCIONAL EN LA CIUDAD DE ORURO",
            fecha_inicio: "2025-02-12",
            fecha_fin: "2025-12-11",
            estado: "Completado",
            rol: "Frontend Developer",
            cliente: "ORUCINE"
        },
        {
            nombre: "E-commerce App",
            descripcion: "Plataforma de ventas en línea",
            fecha_inicio: "2024-01-15",
            fecha_fin: "2024-06-30",
            estado: "Completado",
            rol: "Full Stack Developer",
            cliente: "DigitalStore"
        },
        {
            nombre: "Sistema de Inventarios",
            descripcion: "Gestión de inventario para bodegas",
            fecha_inicio: "2024-08-01",
            fecha_fin: null,
            estado: "En progreso",
            rol: "Backend Developer",
            cliente: "Logistics SA"
        }
    ],
    
    // Redes y contacto
    redes: {
        linkedin: "LINKKI/343JNJI",
        github: "HTTPS;NJSNXISX",
        whatsapp: "717503389",
        correo: "mamanimollosarahiesther@mail.com",
        otros: "TITOK|"
    }
};

// ============================================
// FUNCIONES PARA RENDERIZAR EL PERFIL
// ============================================

function formatearFecha(fecha) {
    if (!fecha) return '';
    const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    const date = new Date(fecha);
    return `${date.getDate()} ${meses[date.getMonth()]} ${date.getFullYear()}`;
}

function renderizarExperiencias() {
    const container = document.getElementById('experiencias-container');
    if (!container) return;
    
    if (datosUsuario.experiencias.length === 0) {
        container.innerHTML = '<div class="empty-message">No hay experiencias laborales registradas</div>';
        return;
    }
    
    let html = '';
    for (const exp of datosUsuario.experiencias) {
        html += `
            <div class="card">
                <h3>${exp.empresa}</h3>
                <div class="subtitle">${exp.cargo} ${exp.ubicacion ? '| ' + exp.ubicacion : ''}</div>
                <div class="date">
                    ${formatearFecha(exp.fecha_inicio)} 
                    ${exp.fecha_fin ? '— ' + formatearFecha(exp.fecha_fin) : (exp.trabajo_actual ? '— Actualidad' : '')}
                </div>
                <div class="description">${exp.descripcion}</div>
            </div>
        `;
    }
    container.innerHTML = html;
}

function renderizarAcademicas() {
    const container = document.getElementById('academica-container');
    if (!container) return;
    
    if (datosUsuario.academicas.length === 0) {
        container.innerHTML = '<div class="empty-message">No hay información académica registrada</div>';
        return;
    }
    
    let html = '';
    for (const aca of datosUsuario.academicas) {
        html += `
            <div class="card">
                <h3>${aca.institucion}</h3>
                <div class="subtitle">${aca.titulo}</div>
                <div class="date">
                    ${formatearFecha(aca.fecha_inicio)}
                    ${aca.fecha_fin ? '— ' + formatearFecha(aca.fecha_fin) : (aca.estudio_actual ? '— Actualidad' : '')}
                </div>
                <div class="description">${aca.descripcion}</div>
            </div>
        `;
    }
    container.innerHTML = html;
}

function renderizarHabilidadesTecnicas() {
    const container = document.getElementById('habilidades-tecnicas-container');
    if (!container) return;
    
    if (datosUsuario.habilidadesTecnicas.length === 0) {
        container.innerHTML = '<div class="empty-message">No hay habilidades técnicas registradas</div>';
        return;
    }
    
    let html = '';
    for (const skill of datosUsuario.habilidadesTecnicas) {
        let nivelClass = '';
        if (skill.nivel === 'Avanzado') nivelClass = 'advanced';
        else if (skill.nivel === 'Intermedio') nivelClass = 'intermediate';
        else nivelClass = 'basic';
        
        html += `<span class="skill-tag ${nivelClass}">${skill.nombre} - ${skill.nivel}</span>`;
    }
    container.innerHTML = html;
}

function renderizarHabilidadesBlandas() {
    const container = document.getElementById('habilidades-blandas-container');
    if (!container) return;
    
    if (datosUsuario.habilidadesBlandas.length === 0) {
        container.innerHTML = '<div class="empty-message">No hay habilidades blandas registradas</div>';
        return;
    }
    
    let html = '';
    for (const skill of datosUsuario.habilidadesBlandas) {
        html += `<span class="soft-skill-tag">${skill}</span>`;
    }
    container.innerHTML = html;
}

function renderizarProyectos() {
    const container = document.getElementById('proyectos-container');
    if (!container) return;
    
    if (datosUsuario.proyectos.length === 0) {
        container.innerHTML = '<div class="empty-message">No hay proyectos registrados</div>';
        return;
    }
    
    let html = '';
    for (const proyecto of datosUsuario.proyectos) {
        html += `
            <div class="card">
                <h3>${proyecto.nombre}</h3>
                <div class="description">${proyecto.descripcion}</div>
                <div class="date">
                    ${formatearFecha(proyecto.fecha_inicio)}
                    ${proyecto.fecha_fin ? '— ' + formatearFecha(proyecto.fecha_fin) : ''}
                    | ${proyecto.estado || 'En progreso'}
                </div>
                <div class="description">Rol: ${proyecto.rol || ''} ${proyecto.cliente ? '| Cliente: ' + proyecto.cliente : ''}</div>
            </div>
        `;
    }
    container.innerHTML = html;
}



function renderizarPerfil() {
    const container = document.getElementById('previewContainer');
    
    const html = `
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h1>${datosUsuario.nombre} ${datosUsuario.apellido}</h1>
            <div class="title">${datosUsuario.titulo}</div>
            <div class="location"><i class="fas fa-map-marker-alt"></i> ${datosUsuario.ciudad}${datosUsuario.pais ? ', ' + datosUsuario.pais : ''}</div>
            <div class="bio">${datosUsuario.biografia}</div>
        </div>

        <div style="display: flex; justify-content: flex-start; margin: 30px 0; padding: 0 40px;">
            <a href="{{ url('/dashboard') }}" class="btn btn-editar">
                <i class="fas fa-edit"></i> Continuar editando
            </a>
        </div>

        <div class="section">
            <h2><i class="fas fa-briefcase"></i> Experiencia laboral</h2>
            <div id="experiencias-container" class="cards-grid"></div>
        </div>

        <div class="section">
            <h2><i class="fas fa-graduation-cap"></i> Información académica</h2>
            <div id="academica-container" class="cards-grid"></div>
        </div>

        <div class="section">
            <h2><i class="fas fa-code"></i> Habilidades técnicas</h2>
            <div class="skills-container" id="habilidades-tecnicas-container"></div>
        </div>

        <div class="section">
            <h2><i class="fas fa-heart"></i> Habilidades blandas</h2>
            <div class="skills-container" id="habilidades-blandas-container"></div>
        </div>

        <div class="section">
            <h2><i class="fas fa-project-diagram"></i> Proyectos</h2>
            <div id="proyectos-container" class="cards-grid"></div>
        </div>



        <div class="buttons-container">
            <a href="{{ url('/dashboard') }}" class="btn btn-editar">
                <i class="fas fa-edit"></i> Continuar editando
            </a>
            <button type="button" class="btn btn-publicar" onclick="publicarPerfil()">
                <i class="fas fa-globe"></i> Publicar perfil
            </button>
        </div>
    `;
    
    container.innerHTML = html;
    
    renderizarExperiencias();
    renderizarAcademicas();
    renderizarHabilidadesTecnicas();
    renderizarHabilidadesBlandas();
    renderizarProyectos();

}

function publicarPerfil() {
    if (confirm('¿Estás segura de que quieres publicar tu perfil? Una vez publicado, será visible para todos.')) {
        alert('✅ Perfil publicado correctamente.\n\n(Modo demo - Solo frontend)');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    renderizarPerfil();
});