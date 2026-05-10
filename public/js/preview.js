

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