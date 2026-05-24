// ── Dropdown de usuario ──
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    const chevron = document.getElementById('dropdownChevron');

    if (!menu || !chevron) return;

    const isOpen = menu.classList.contains('open');

    menu.classList.toggle('open');
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchRepo');
    const btnClear = document.getElementById('btnClearFilters');

    const portfoliosGrid = document.getElementById('portfoliosGrid');
    const totalResults = document.getElementById('totalResults');
    const totalResultsHero = document.getElementById('totalResultsHero');

    const dropdowns = document.querySelectorAll('.custom-dropdown');

    const categoryRadios = document.querySelectorAll('input[name="category"]');
    const skillCheckboxes = document.querySelectorAll('input[name="skills[]"]');
    const projectRadios = document.querySelectorAll('input[name="min_projects"]');
    const languageRadios = document.querySelectorAll('input[name="language"]');
    const sortRadios = document.querySelectorAll('input[name="sort"]');

    const categoryText = document.getElementById('categoryDropdownText');
    const skillsText = document.getElementById('skillsDropdownText');
    const projectsText = document.getElementById('projectsDropdownText');
    const languageText = document.getElementById('languageDropdownText');
    const sortText = document.getElementById('sortDropdownText');
    const clearSkillsOption = document.getElementById('clearSkillsOption');

    let debounceTimer;

    function getCheckedValue(radios, defaultValue = '') {
        const checked = Array.from(radios).find(radio => radio.checked);
        return checked ? checked.value : defaultValue;
    }

    function getCheckedLabel(radios, fallback) {
        const checked = Array.from(radios).find(radio => radio.checked);

        if (!checked) return fallback;

        const label = checked.closest('label');
        const text = label ? label.querySelector('span') : null;

        return text ? text.textContent.trim() : fallback;
    }

    function getSelectedSkills() {
        return Array.from(skillCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
    }

    function updateDropdownTexts() {
        if (categoryText) {
            categoryText.textContent = getCheckedLabel(categoryRadios, 'Categorías');
        }

        if (projectsText) {
            projectsText.textContent = getCheckedLabel(projectRadios, 'Proyectos');
        }

        if (languageText) {
            languageText.textContent = getCheckedLabel(languageRadios, 'Idiomas');
        }

        if (sortText) {
            sortText.textContent = getCheckedLabel(sortRadios, 'Más recientes');
        }

        if (skillsText) {
            const selectedSkills = getSelectedSkills();

            if (selectedSkills.length === 0) {
                skillsText.textContent = 'Tecnologías';

                if (clearSkillsOption) {
                    clearSkillsOption.classList.add('active');
                }
            } else if (selectedSkills.length === 1) {
                skillsText.textContent = selectedSkills[0];

                if (clearSkillsOption) {
                    clearSkillsOption.classList.remove('active');
                }
            } else {
                skillsText.textContent = `${selectedSkills.length} tecnologías`;

                if (clearSkillsOption) {
                    clearSkillsOption.classList.remove('active');
                }
            }
        }
    }

    function buildFilterUrl() {
        const query = searchInput
            ? searchInput.value
                .slice(0, 50)
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
            : '';

        const category = getCheckedValue(categoryRadios);
        const minProjects = getCheckedValue(projectRadios);
        const language = getCheckedValue(languageRadios);
        const sort = getCheckedValue(sortRadios, 'desc');
        const selectedSkills = getSelectedSkills();

        const params = new URLSearchParams();

        if (query) params.append('search', query);
        if (category) params.append('category', category);

        selectedSkills.forEach(skill => {
            if (skill) {
                params.append('skills[]', skill);
            }
        });

        if (minProjects) params.append('min_projects', minProjects);
        if (language) params.append('language', language);
        if (sort) params.append('sort', sort);

        return `${window.location.pathname}?${params.toString()}`;
    }

    function fetchFilteredPortfolios() {
        const newUrl = buildFilterUrl();

        window.history.replaceState({}, '', newUrl);

        if (portfoliosGrid) {
            portfoliosGrid.style.opacity = '0.6';
        }

        fetch(newUrl, {
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    }
})
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }

                return response.json();
            })
            .then(data => {
                if (portfoliosGrid) {
                    portfoliosGrid.style.opacity = '1';
                }

                if (portfoliosGrid && data.html !== undefined) {
                    portfoliosGrid.innerHTML = data.html;
                }

                if (totalResults) {
                    totalResults.textContent = data.count;
                }

                if (totalResultsHero) {
                    totalResultsHero.textContent = data.count;
                }
            })
            .catch(error => {
                console.error('Hubo un problema con la petición AJAX:', error);

                if (portfoliosGrid) {
                    portfoliosGrid.style.opacity = '1';
                }
            });
    }

    function closeAllDropdowns(except = null) {
        dropdowns.forEach(dropdown => {
            if (dropdown !== except) {
                dropdown.classList.remove('open');
            }
        });
    }

    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('.custom-dropdown-btn');

        if (!button) return;

        button.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();

            const isOpen = dropdown.classList.contains('open');

            closeAllDropdowns(dropdown);

            if (isOpen) {
                dropdown.classList.remove('open');
            } else {
                dropdown.classList.add('open');
            }
        });
    });

    document.addEventListener('click', e => {
        const clickedInsideDropdown = e.target.closest('.custom-dropdown');

        if (!clickedInsideDropdown) {
            closeAllDropdowns();
        }
    });

    categoryRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            updateDropdownTexts();
            closeAllDropdowns();
            fetchFilteredPortfolios();
        });
    });

    projectRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            updateDropdownTexts();
            closeAllDropdowns();
            fetchFilteredPortfolios();
        });
    });

    languageRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            updateDropdownTexts();
            closeAllDropdowns();
            fetchFilteredPortfolios();
        });
    });

    sortRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            updateDropdownTexts();
            closeAllDropdowns();
            fetchFilteredPortfolios();
        });
    });

    skillCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateDropdownTexts();
            fetchFilteredPortfolios();
        });
    });

    if (clearSkillsOption) {
        clearSkillsOption.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();

            skillCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            updateDropdownTexts();
            fetchFilteredPortfolios();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(() => {
                fetchFilteredPortfolios();
            }, 350);
        });
    }

    if (btnClear) {
        btnClear.addEventListener('click', e => {
            e.preventDefault();

            if (searchInput) searchInput.value = '';

            categoryRadios.forEach(radio => {
                radio.checked = radio.value === '';
            });

            projectRadios.forEach(radio => {
                radio.checked = radio.value === '';
            });

            languageRadios.forEach(radio => {
                radio.checked = radio.value === '';
            });

            sortRadios.forEach(radio => {
                radio.checked = radio.value === 'desc';
            });

            skillCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            updateDropdownTexts();
            closeAllDropdowns();
            fetchFilteredPortfolios();
        });
    }

    updateDropdownTexts();
});

// Cerrar dropdown de usuario al hacer click afuera
document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('userDropdown');
    const menu = document.getElementById('userMenu');
    const chevron = document.getElementById('dropdownChevron');

    if (dropdown && menu && chevron && !dropdown.contains(e.target)) {
        menu.classList.remove('open');
        chevron.style.transform = 'rotate(0deg)';
    }
});

// Navbar compacta al hacer scroll
window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');

    if (!navbar) return;

    if (window.scrollY > 120) {
        navbar.classList.add('compact');
    } else {
        navbar.classList.remove('compact');
    }
});