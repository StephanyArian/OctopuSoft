let estadoInicialProyectos = [];

function toggleForm(show) {
    document.getElementById('skill-form-wrap').style.display = show ? 'block' : 'none';
}

function cancelForm() {

    const editSkillId = new URLSearchParams(window.location.search).get('edit');
    
    if (editSkillId) {
        const container = document.getElementById('edit-projects-' + editSkillId);
        if (container) {
            const estadoActual = Array.from(
                container.querySelectorAll('.skill-project-chip[data-project]')
            ).map(chip => parseInt(chip.dataset.project));

            const estadoInicial = estadoInicialProyectos.map(p => p.id);
            const vinculadosDemas = estadoActual.filter(id => !estadoInicial.includes(id));
            const desvinculadosDemas = estadoInicial.filter(id => !estadoActual.includes(id));
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            // Revertir vínculos agregados
            vinculadosDemas.forEach(projectId => {
                fetch(`/skills/${editSkillId}/projects/${projectId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf }
                });
            });

            // Revertir desvinculaciones
            desvinculadosDemas.forEach(project => {
                fetch(`/skills/${editSkillId}/projects`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ project_id: project })
                });
            });
        }
    }

    window.location.href = window.location.pathname;
}

function openDeleteModal(id, name) {
    document.getElementById('modal-skill-name').textContent = name;
    document.getElementById('delete-form').action = '/skills/' + id;
    document.getElementById('delete-modal').classList.add('active');
}
function closeDeleteModal() {
    document.getElementById('delete-modal').classList.remove('active');
}
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

//  HU-24: Vincular proyectos a habilidades 

let currentSkillId = null;
let currentContext  = null;
let pendingProjects = []; 

function openProjectSelector(skillId) {
    currentSkillId = skillId;
    currentContext = skillId ? 'historial' : 'new-form';
    document.getElementById('project-search').value = '';
    renderProjectList('');
    document.getElementById('project-selector-modal').classList.add('active');
}

function closeProjectSelector() {
    document.getElementById('project-selector-modal').classList.remove('active');
    currentSkillId = null;
}

document.getElementById('project-selector-modal').addEventListener('click', function (e) {
    if (e.target === this) closeProjectSelector();
});

function filterProjects() {
    renderProjectList(document.getElementById('project-search').value);
}

function renderProjectList(query) {
    const list = document.getElementById('project-list');
    const q = query.toLowerCase();

    // Obtener ids ya vinculados a esta skill
    const linkedIds = currentSkillId
        ? getLinkedIds(currentSkillId)          // habilidad existente
        : pendingProjects.map(p => p.id);       // habilidad nueva 

    const filtered = (window.userProjects || []).filter(p =>
        p.name.toLowerCase().includes(q) && !linkedIds.includes(p.id)
    );

    if (filtered.length === 0) {
        list.innerHTML = '<p style="color:#999;font-size:13px;padding:8px 0;">No hay proyectos disponibles para vincular.</p>';
        return;
    }

    list.innerHTML = filtered.map(p => {
        const techs = (p.technologies || []).join(' · ');
        return `
        <div class="project-option" onclick="selectProject(${p.id}, '${escapeHtml(p.name)}')">
            <span class="project-option-dot"></span>
            <div>
                <div class="project-option-name">${escapeHtml(p.name)}</div>
                ${techs ? `<div class="project-option-techs">${escapeHtml(techs)}</div>` : ''}
            </div>
        </div>`;
    }).join('');
}
 
function selectProject(projectId, projectName) {
    if (currentContext === 'new-form' || !currentSkillId) {
        addPendingChip(projectId, projectName);
        closeProjectSelector();
        return;
    }
    attachProjectApi(currentSkillId, projectId, projectName, () => {       
        if (currentContext !== 'edit-form') {
            addChipToSkill(currentSkillId, projectId, projectName, true);
        }
        
        if (currentContext === 'edit-form') {
            const editContainer = document.getElementById('edit-projects-' + currentSkillId);
            if (editContainer && !editContainer.querySelector(`[data-project="${projectId}"]`)) {
                const addBtn = editContainer.querySelector('.chip-add');
                const chip   = document.createElement('span');
                chip.className       = 'skill-project-chip';
                chip.dataset.skill   = currentSkillId;
                chip.dataset.project = projectId;
                chip.innerHTML = `${escapeHtml(projectName)}<button type="button" class="chip-remove" onclick="confirmDetach(${currentSkillId},${projectId},this)" title="Quitar proyecto">×</button>`;
                editContainer.insertBefore(chip, addBtn);
            }
        }
        closeProjectSelector();
    });
}
 
function attachProjectApi(skillId, projectId, projectName, onSuccess) {
    fetch(`/skills/${skillId}/projects`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ project_id: projectId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) onSuccess();
    });
}
 
 
function addChipToSkill(skillId, projectId, projectName, withRemove) {
    const container = document.getElementById('skill-projects-' + skillId);
    if (!container) return;
    const addBtn = container.querySelector('.chip-add');
    const chip   = document.createElement('span');
    chip.className          = 'skill-project-chip';
    chip.dataset.skill      = skillId;
    chip.dataset.project    = projectId;
    chip.innerHTML = withRemove
        ? `${escapeHtml(projectName)}<button type="button" class="chip-remove" onclick="confirmDetach(${skillId},${projectId},this)" title="Quitar proyecto">×</button>`
        : escapeHtml(projectName);
    container.insertBefore(chip, addBtn);
}
 
 
function addPendingChip(projectId, projectName) {
    pendingProjects.push({ id: projectId, name: projectName });
    const container = document.getElementById('pending-projects-container');
    if (!container) return;
 
    const input = document.createElement('input');
    input.type  = 'hidden';
    input.name  = 'project_ids[]';
    input.value = projectId;
    input.id    = 'pending-input-' + projectId;
    container.appendChild(input);
 
    
    const chip = document.createElement('span');
    chip.className       = 'skill-project-chip';
    chip.id              = 'pending-chip-' + projectId;
    chip.innerHTML = `${escapeHtml(projectName)}<button type="button" class="chip-remove" onclick="removePendingChip(${projectId})" title="Quitar">×</button>`;
    container.appendChild(chip);
}
 
function removePendingChip(projectId) {
    pendingProjects = pendingProjects.filter(p => p.id !== projectId);
    const chip  = document.getElementById('pending-chip-' + projectId);
    const input = document.getElementById('pending-input-' + projectId);
    if (chip)  chip.remove();
    if (input) input.remove();
}
 
 
let detachPending = null; 
 
function confirmDetach(skillId, projectId, btn) {
    if (detachPending && detachPending.projectId === projectId && detachPending.skillId === skillId) {
        cancelDetach();
        return;
    }
    if (detachPending) cancelDetach();

    const chip = btn.closest('.skill-project-chip');
    const projectName = chip.childNodes[0].textContent.trim();
    const anchorContainer = chip.parentElement;

    detachPending = { skillId, projectId, btn, chip, anchorContainer };
    chip.classList.add('chip-confirming');

    const banner = document.createElement('div');
    banner.className = 'detach-confirm-banner';
    banner.id        = 'detach-banner-' + skillId + '-' + projectId;
    banner.innerHTML = `
        <span>¿Desvincular <strong>${escapeHtml(projectName)}</strong> de esta habilidad?</span>
        <div style="display:flex;gap:6px;flex-shrink:0;">
            <button type="button" class="btn-detach-confirm" onclick="executeDetach()">Sí, desvincular</button>
            <button type="button" class="btn-detach-cancel"  onclick="cancelDetach()">Cancelar</button>
        </div>`;

    anchorContainer.insertAdjacentElement('afterend', banner);
}
 
function cancelDetach() {
    if (!detachPending) return;
    const chip = detachPending.btn.closest('.skill-project-chip');
    chip.classList.remove('chip-confirming');
    const banner = document.getElementById('detach-banner-' + detachPending.skillId + '-' + detachPending.projectId);
    if (banner) banner.remove();
    detachPending = null;
}
 
function executeDetach() {
    if (!detachPending) return;
    const { skillId, projectId, btn } = detachPending;
 
    fetch(`/skills/${skillId}/projects/${projectId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const banner = document.getElementById('detach-banner-' + skillId + '-' + projectId);
            if (banner) banner.remove();

            
            const enModoEdicion = document.getElementById('edit-projects-' + skillId) !== null;

            if (enModoEdicion) {
            // Solo actualiza el formulario de edición
                const editContainer = document.getElementById('edit-projects-' + skillId);
                if (editContainer) {
                    const chip = editContainer.querySelector(`[data-project="${projectId}"]`);
                    if (chip) chip.remove();
                }
            } else {
            // Solo actualiza el historial
                const histContainer = document.getElementById('skill-projects-' + skillId);
                if (histContainer) {
                    const chip = histContainer.querySelector(`[data-project="${projectId}"]`);
                    if (chip) chip.remove();
                }
            }

            detachPending = null;
        }
    });
}
 
function openProjectSelectorForEdit(skillId) {
    currentSkillId = skillId;
    currentContext = 'edit-form';  
    document.getElementById('project-search').value = '';
    renderProjectList('');
    document.getElementById('project-selector-modal').classList.add('active');
}
function openProjectSelectorNew() {
    currentSkillId = null;
    currentContext = 'new-form';
    document.getElementById('project-search').value = '';
    renderProjectList('');
    document.getElementById('project-selector-modal').classList.add('active');
} 
// Helpers 
 
function getLinkedIds(skillId) {
    const ids = new Set();
    ['skill-projects-', 'edit-projects-'].forEach(prefix => {
        const container = document.getElementById(prefix + skillId);
        if (!container) return;
        container.querySelectorAll('.skill-project-chip').forEach(el => {
            const id = parseInt(el.dataset.project);
            if (id) ids.add(id);
        });
    });
    return Array.from(ids);
}
 
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}


//  Historial con categorías y ver más

const CATEGORY_META = {
    frontend: { label: 'Frontend',      color: '#E6F1FB', textColor: '#185FA5' },
    backend:  { label: 'Backend',       color: '#EAF3DE', textColor: '#3B6D11' },
    '':       { label: 'Sin categoría', color: '#F1EFE8', textColor: '#6B7280' },
};

const VISIBLE_COUNT = 3;
const expandedCats  = {};

function sortAndGroup(sortMode) {
    const container = document.getElementById('skills-list-container');
    if (!container) return;

    const cards = Array.from(container.querySelectorAll('.skill-card'));
    if (!cards.length) return;

    // Ordenar
    cards.sort((a, b) => {
        if (sortMode === 'alpha')
            return a.dataset.name.localeCompare(b.dataset.name);
        if (sortMode === 'level')
            return parseInt(b.dataset.level) - parseInt(a.dataset.level);
       return parseInt(b.dataset.order) - parseInt(a.dataset.order);
    });

    // Limpiar separadores y botones ver-más anteriores
    container.querySelectorAll('.category-divider, .ver-mas-skill-btn').forEach(el => el.remove());

    // Ocultar todas las cards
    cards.forEach(c => c.style.display = 'none');

    // Agrupar
    const catOrder = ['frontend', 'backend', ''];
    const grouped  = {};
    catOrder.forEach(k => grouped[k] = []);
    cards.forEach(c => grouped[c.dataset.category || ''].push(c));

    let globalIdx = 1;

    catOrder.forEach(cat => {
        const list = grouped[cat];
        if (!list.length) return;

        const meta     = CATEGORY_META[cat];
        const isExpand = expandedCats[cat] || false;
        const visible  = isExpand ? list : list.slice(0, VISIBLE_COUNT);
        const hiddenN  = list.length - VISIBLE_COUNT;

        // Separador
        const divider = document.createElement('div');
        divider.className    = 'category-divider';
        divider.style.cssText = 'display:flex;align-items:center;gap:8px;margin:18px 0 8px;';
        divider.innerHTML = `
            <span style="font-size:10px;font-weight:500;letter-spacing:.06em;text-transform:uppercase;
                         padding:2px 10px;border-radius:99px;white-space:nowrap;
                         background:${meta.color};color:${meta.textColor};">
                ${meta.label}
            </span>
            <div style="flex:1;height:.5px;background:var(--color-border-tertiary);"></div>`;
        container.appendChild(divider);

        // Cards visibles
        visible.forEach(card => {
            const indexEl = card.querySelector('.skill-card-index');
            if (indexEl) indexEl.textContent = String(globalIdx++).padStart(2, '0');
            card.style.display = '';
            container.appendChild(card);
        });

        // Botón ver más / ocultar
        if (hiddenN > 0 && !isExpand) {
            const btn = document.createElement('button');
            btn.className   = 'ver-mas-skill-btn';
            btn.textContent = `+ ${hiddenN} más en ${meta.label}`;
            btn.onclick     = () => toggleCat(cat);
            container.appendChild(btn);
        } else if (isExpand && list.length > VISIBLE_COUNT) {
            list.slice(VISIBLE_COUNT).forEach(card => {
                const indexEl = card.querySelector('.skill-card-index');
                if (indexEl) indexEl.textContent = String(globalIdx++).padStart(2, '0');
                card.style.display = '';
                container.appendChild(card);
            });
            const btn = document.createElement('button');
            btn.className   = 'ver-mas-skill-btn';
            btn.textContent = `▲ Ocultar ${meta.label}`;
            btn.onclick     = () => toggleCat(cat);
            container.appendChild(btn);
        }
    });
}

function toggleCat(cat) {
    expandedCats[cat] = !expandedCats[cat];
    const sort = document.getElementById('skills-sort');
    sortAndGroup(sort ? sort.value : 'order');
}

// ── DOMContentLoaded (único) ─────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
    const editSkillId = new URLSearchParams(window.location.search).get('edit');
    if (editSkillId) {
        const container = document.getElementById('edit-projects-' + editSkillId);
        if (container) {
            estadoInicialProyectos = Array.from(
                container.querySelectorAll('.skill-project-chip[data-project]')
            ).map(chip => ({
                id:   parseInt(chip.dataset.project),
                name: chip.childNodes[0].textContent.trim()
            }));
        }
    }

    sortAndGroup('order');
});
