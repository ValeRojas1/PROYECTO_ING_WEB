<?php
/**
 * Header genérico para la plataforma
 */
$pagina_actual = basename($_SERVER['PHP_SELF']);
$paginas_auto_ocultables = ['registrar-bien.php', 'importar-excel.php', 'desplazamientos.php', 'nuevo-desplazamiento.php', 'reportes.php', 'historial.php'];
$sidebar_autohide = in_array($pagina_actual, $paginas_auto_ocultables);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Patrimonial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #48bb78;
            --warning: #ed8936;
            --danger: #f56565;
            --info: #4299e1;
            --sidebar-width: 250px;
        }
        body {
            background-color: #f5f7fa;
        }
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* ===== SIDEBAR BASE ===== */
        .sidebar {
            background: white;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
            min-height: calc(100vh - 56px);
            width: var(--sidebar-width);
            overflow-x: hidden;
            white-space: nowrap;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            border-left-color: var(--primary);
            background-color: #f5f7fa;
            color: var(--primary);
        }
        .sidebar .nav-link.active {
            border-left-color: var(--primary);
            background-color: #f5f7fa;
            color: var(--primary);
            font-weight: 600;
        }
        
        /* ===== LAYOUT NORMAL (sidebar siempre visible) ===== */
        .main-container {
            display: flex;
            margin-top: 56px;
        }
        .main-container .sidebar {
            flex: 0 0 var(--sidebar-width);
        }
        .main-container .content {
            flex: 1;
            padding: 20px;
            overflow-x: hidden;
        }
        .content .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }
        
        /* ===== SIDEBAR AUTOCULTA (JS controlado) ===== */
        .sidebar-autohide .sidebar {
            position: fixed;
            top: 56px;
            left: calc(-1 * var(--sidebar-width));
            height: calc(100vh - 56px);
            transition: left 0.3s ease;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        }
        .sidebar-autohide .sidebar.visible {
            left: 0;
        }
        .sidebar-autohide .content {
            width: 100%;
        }
        
        /* Zona de activación invisible en borde izquierdo */
        .sidebar-trigger {
            position: fixed;
            left: 0;
            top: 56px;
            width: 18px;
            height: calc(100vh - 56px);
            z-index: 999;
            background: transparent;
        }
        /* Indicador visual sutil */
        .sidebar-trigger::after {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 50px;
            border-radius: 0 4px 4px 0;
            background: var(--primary);
            opacity: 0.35;
            transition: opacity 0.3s;
        }
        .sidebar-trigger:hover::after {
            opacity: 0.8;
        }
        
        /* Overlay oscuro detrás de sidebar abierta */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 56px;
            left: 0;
            width: 100%;
            height: calc(100vh - 56px);
            background: rgba(0,0,0,0.25);
            z-index: 998;
        }
        .sidebar-overlay.active {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Navbar Superior -->
    <nav class="navbar navbar-dark fixed-top">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-chart-line"></i> Control Patrimonial
            </span>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    👤 <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?>
                </span>
                <a href="?toggle_lang=1" class="btn btn-outline-light btn-sm me-3" title="Cambiar idioma del sistema">
                    <i class="fas fa-language"></i> <?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] === 'en') ? 'EN' : 'ES'; ?>
                </a>
                <a href="logout.php" class="btn btn-outline-light btn-sm"><?php echo __('nav_logout'); ?></a>
            </div>
        </div>
    </nav>
    
    <?php if ($sidebar_autohide): ?>
    <!-- Zona de activación y overlay para sidebar auto-ocultable -->
    <div class="sidebar-trigger" id="sidebarTrigger"></div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php endif; ?>
    
    <!-- Contenedor Principal -->
    <div class="main-container <?php echo $sidebar_autohide ? 'sidebar-autohide' : ''; ?>">
