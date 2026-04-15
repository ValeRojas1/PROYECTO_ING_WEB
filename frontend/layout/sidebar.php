<?php
/**
 * Sidebar genérico para la plataforma compartida
 */
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="nav flex-column nav-pills">
                <a href="dashboard.php" class="nav-link <?php echo $pagina_actual == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> <span class="nav-text"><?php echo __('menu_dashboard'); ?></span>
                </a>
                <a href="bienes.php" class="nav-link <?php echo $pagina_actual == 'bienes.php' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> <span class="nav-text"><?php echo __('menu_bienes'); ?></span>
                </a>
                <a href="personas.php" class="nav-link <?php echo $pagina_actual == 'personas.php' ? 'active' : ''; ?>">
                    <i class="fas fa-people-carry"></i> <span class="nav-text"><?php echo __('menu_personas'); ?></span>
                </a>
                <a href="desplazamientos.php" class="nav-link <?php echo $pagina_actual == 'desplazamientos.php' ? 'active' : ''; ?>">
                    <i class="fas fa-exchange-alt"></i> <span class="nav-text"><?php echo __('menu_desplazamientos'); ?></span>
                </a>
                <a href="reportes.php" class="nav-link <?php echo $pagina_actual == 'reportes.php' ? 'active' : ''; ?>">
                    <i class="fas fa-file-pdf"></i> <span class="nav-text"><?php echo __('menu_reportes'); ?></span>
                </a>
                <a href="historial.php" class="nav-link <?php echo $pagina_actual == 'historial.php' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i> <span class="nav-text"><?php echo __('menu_historial'); ?></span>
                </a>
                
                <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): ?>
                <hr>
                <a href="usuarios.php" class="nav-link <?php echo $pagina_actual == 'usuarios.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> <span class="nav-text"><?php echo __('menu_users'); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </nav>
        
        <!-- Content Body (Inicia el contenedor donde irá el HTML propio de cada archivo) -->
        <div class="content">
