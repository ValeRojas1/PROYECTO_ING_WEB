<?php
/**
 * Constantes de la Aplicación
 */

// URLs base
define('BASE_URL', 'http://localhost/PROYECTO_ING_WEB/');
define('FRONTEND_URL', BASE_URL . 'frontend/');

// Rutas de archivos
define('PROJECT_ROOT', dirname(dirname(__FILE__)));
define('UPLOADS_PATH', PROJECT_ROOT . '/uploads/');
define('REPORTS_PATH', PROJECT_ROOT . '/reports/');

// Estados de bienes
define('ESTADO_DISPONIBLE', 'Disponible');
define('ESTADO_ASIGNADO', 'Asignado');
define('ESTADO_DAÑADO', 'Dañado');
define('ESTADO_DESCARTADO', 'Descartado');

// Roles de usuario
define('ROL_ADMIN', 'admin');
define('ROL_USUARIO', 'usuario');
define('ROL_SUPERVISOR', 'supervisor');

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configuración de carga de archivos
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['csv']);

?>
