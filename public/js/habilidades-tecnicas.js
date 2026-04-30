
function toggleForm(show) {
    document.getElementById('skill-form-wrap').style.display = show ? 'block' : 'none';
}

function cancelForm() {
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

// ── HU-24: Vincular proyectos a habilidades ──────────────────

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
        : pendingProjects.map(p => p.id);       // habilidad nueva (pendientes)

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

// ── Seleccionar proyecto del selector ─────────────────────────
// Si la habilidad ya existe → llama API; si es nueva → guarda pendiente
 
function selectProject(projectId, projectName) {
    if (currentContext === 'new-form' || !currentSkillId) {
        addPendingChip(projectId, projectName);
        closeProjectSelector();
        return;
    }
    attachProjectApi(currentSkillId, projectId, projectName, () => {
        // Agregar al historial siempre
        addChipToSkill(currentSkillId, projectId, projectName, true);
        // Si venimos del formulario de edición, agregar también ahí
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

// ── Vincular via API (habilidad existente) ────────────────────
 
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
 
// ── Chip en habilidad existente ───────────────────────────────
 
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
 
// ── Chips pendientes (habilidad nueva) ───────────────────────
 
function addPendingChip(projectId, projectName) {
    pendingProjects.push({ id: projectId, name: projectName });
 
    const container = document.getElementById('pending-projects-container');
    if (!container) return;
 
    // Input oculto para enviar con el formulario
    const input = document.createElement('input');
    input.type  = 'hidden';
    input.name  = 'project_ids[]';
    input.value = projectId;
    input.id    = 'pending-input-' + projectId;
    container.appendChild(input);
 
    // Chip visual
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
 
// ── Confirmar desvinculación (habilidad existente) ────────────
 
let detachPending = null; // { skillId, projectId, btn }
 
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

            // Eliminar chip de AMBOS contenedores
            ['skill-projects-', 'edit-projects-'].forEach(prefix => {
                const container = document.getElementById(prefix + skillId);
                if (!container) return;
                const chip = container.querySelector(`[data-project="${projectId}"]`);
                if (chip) chip.remove();
            });

            detachPending = null;
        }
    });
}
 
// ── Proyectos vinculados en la vista de edición ───────────────
// Cuando se abre el formulario de edición (?edit=ID),
// el botón + proyecto de ese formulario ya apunta al skillId correcto.
 
function openProjectSelectorForEdit(skillId) {
    currentSkillId = skillId;
    currentContext = 'edit-form';  // ← indica que venimos del formulario
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
// ── Helpers ───────────────────────────────────────────────────
 
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