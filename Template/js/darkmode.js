document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('toggle-darkmode');
    const icon = document.getElementById('darkmode-icon');
    const logo = document.getElementById('logo-navbar');

    // Función para aplicar o quitar dark mode
    function setDarkMode(isDark) {
        document.body.classList.toggle('bg-dark', isDark);
        document.body.classList.toggle('text-white', isDark);

        // Cambia la navbar
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            navbar.classList.toggle('navbar-light', !isDark);
            navbar.classList.toggle('bg-light', !isDark);
            navbar.classList.toggle('navbar-dark', isDark);
            navbar.classList.toggle('bg-dark', isDark);
        }

        // Cambia las cards
        document.querySelectorAll('.card').forEach(function(card) {
            card.classList.toggle('bg-dark', isDark);
            card.classList.toggle('text-white', isDark);
            card.classList.toggle('border-light', isDark);
        });

        // Cambia los modales
        document.querySelectorAll('.modal-content').forEach(function(modal) {
            modal.classList.toggle('bg-dark', isDark);
            modal.classList.toggle('text-white', isDark);
            modal.classList.toggle('border-light', isDark);
        });
        
        // Cambia los campos de formulario (input, select, textarea)
        document.querySelectorAll('input, select, textarea').forEach(function(el) {
            el.classList.toggle('bg-dark', isDark);
            el.classList.toggle('text-white', isDark);
            el.classList.toggle('border-light', isDark);
        });

        // Cambia la tabla y sus celdas
        document.querySelectorAll('table').forEach(function(table) {
            table.classList.toggle('table-dark', isDark);
        });

        // Cambia el dropdown de usuario
        document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
            menu.classList.toggle('darkmode', isDark);
        });

        // Cambia el icono
        if (icon) {
            if (isDark) {
                icon.classList.remove('bi-moon');
                icon.classList.add('bi-sun');
            } else {
                icon.classList.remove('bi-sun');
                icon.classList.add('bi-moon');
            }
        }
        // Cambia el logo
        if (logo) {
            logo.classList.toggle('logo-darkmode', isDark);
        }
    }

    // Al cargar la página, aplica el modo guardado
    const darkPref = localStorage.getItem('darkmode');
    if (darkPref === 'true') {
        setDarkMode(true);
    }

    if (!toggle) return;
    toggle.addEventListener('click', function() {
        const isDark = !document.body.classList.contains('bg-dark');
        setDarkMode(isDark);
        localStorage.setItem('darkmode', isDark);
    });
});