document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (toggle && mobileMenu) {
        toggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Opcjonalne: zmień header dynamicznie przy zmianie rozmiaru
    const desktopHeader = document.getElementById('header-desktop');
    const mobileHeader = document.getElementById('header-mobile');

    function updateHeaderVisibility() {
        const width = window.innerWidth;
        if (width >= 768) {
            desktopHeader.classList.remove('hidden');
            mobileHeader.classList.add('hidden');
        } else {
            desktopHeader.classList.add('hidden');
            mobileHeader.classList.remove('hidden');
        }
    }

    window.addEventListener('resize', updateHeaderVisibility);
    updateHeaderVisibility(); // Uruchom od razu przy starcie
});

