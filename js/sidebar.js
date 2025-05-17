document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContainer = document.querySelector('.main-container');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('collapsed');
            mainContainer.classList.toggle('expanded');
            
            // Ajustar la posición del botón
            if (sidebar.classList.contains('collapsed')) {
                sidebarToggle.style.left = '10px';
            } else {
                sidebarToggle.style.left = '260px';
            }
        });
    }
});

// Detectar cambios en el tamaño de la ventana
window.addEventListener('resize', function() {
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
        if (mainContainer) {
            mainContainer.classList.add('expanded');
        }
    } else {
        sidebar.classList.remove('collapsed');
        if (mainContainer) {
            mainContainer.classList.remove('expanded');
        }
    }
});