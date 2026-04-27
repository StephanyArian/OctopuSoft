
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
