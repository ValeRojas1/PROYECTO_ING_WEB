        </div> <!-- CIERRE DE .content -->
    </div> <!-- CIERRE DE .main-container -->
    
    <!-- Scripts Compartidos -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // ===== Sidebar Auto-Hide con JavaScript =====
    (function() {
        const trigger  = document.getElementById('sidebarTrigger');
        const overlay  = document.getElementById('sidebarOverlay');
        const sidebar  = document.querySelector('.sidebar-autohide .sidebar');
        
        // Solo ejecutar si estamos en una página con sidebar auto-ocultable
        if (!trigger || !overlay || !sidebar) return;

        function abrirSidebar() {
            sidebar.classList.add('visible');
            overlay.classList.add('active');
            trigger.style.display = 'none';
        }

        function cerrarSidebar() {
            sidebar.classList.remove('visible');
            overlay.classList.remove('active');
            trigger.style.display = '';
        }

        // Abrir cuando el cursor toca la zona izquierda
        trigger.addEventListener('mouseenter', abrirSidebar);

        // Cerrar con click en el overlay
        overlay.addEventListener('click', cerrarSidebar);

        // Cerrar cuando el cursor sale de sidebar hacia la derecha
        sidebar.addEventListener('mouseleave', function(e) {
            // Solo cerrar si el cursor sale por la derecha (hacia el contenido)
            if (e.clientX > sidebar.getBoundingClientRect().right - 5) {
                cerrarSidebar();
            }
        });

        // Cerrar al hacer click en un enlace de la sidebar
        sidebar.querySelectorAll('.nav-link').forEach(function(link) {
            link.addEventListener('click', cerrarSidebar);
        });
    })();
    </script>
</body>
</html>
