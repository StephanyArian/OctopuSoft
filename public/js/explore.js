
// ── Dropdown de usuario ──
function toggleUserMenu() {
    const menu    = document.getElementById('userMenu');
    const chevron = document.getElementById('dropdownChevron');
    if (!menu || !chevron) return;

    const isOpen = menu.classList.contains('open');
    menu.classList.toggle('open');
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('userDropdown');
    const menu     = document.getElementById('userMenu');
    const chevron  = document.getElementById('dropdownChevron');

    if (dropdown && menu && chevron && !dropdown.contains(e.target)) {
        menu.classList.remove('open');
        chevron.style.transform = 'rotate(0deg)';
    }
});

// ── Navbar compacto al hacer scroll ──
window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 120) {
        navbar.classList.add('compact');
    } else {
        navbar.classList.remove('compact');
    }
});