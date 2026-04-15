<?php
/**
 * Gestión de Sesiones
 */

session_start();

// Alternar formato de fecha/idioma si se solicita
if (isset($_GET['toggle_date_lang'])) {
    $_SESSION['date_lang'] = (isset($_SESSION['date_lang']) && $_SESSION['date_lang'] === 'en') ? 'es' : 'en';
    
    // Redirigir limpiando la URL
    $referer = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
    $url_limpia = strtok($referer, '?');
    
    // Reconstruir los parámetros GET (omitiendo el toggle)
    $params = [];
    $query_str = parse_url($referer, PHP_URL_QUERY);
    if ($query_str) {
        parse_str($query_str, $queryParams);
        unset($queryParams['toggle_date_lang']);
        if (!empty($queryParams)) {
            $url_limpia .= '?' . http_build_query($queryParams);
        }
    }
    
    header('Location: ' . $url_limpia);
    exit();
}

if (!isset($_SESSION['date_lang'])) {
    $_SESSION['date_lang'] = 'es'; // Por defecto Español
}

// FORMATOS GLOBALES DEPENDIENDO DEL LENGUAJE SELECCIONADO
define('FORMATO_FECHA', $_SESSION['date_lang'] === 'en' ? 'm/d/Y' : 'd/m/Y');
define('FORMATO_FECHA_MIN', $_SESSION['date_lang'] === 'en' ? 'm/d/Y H:i' : 'd/m/Y H:i');
define('FORMATO_FECHA_HORA', $_SESSION['date_lang'] === 'en' ? 'm/d/Y H:i:s' : 'd/m/Y H:i:s');

// Verificar sesión activa
function verificarSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . 'frontend/login.php');
        exit();
    }
    
    // Verificar timeout
    if (isset($_SESSION['ultimo_acceso'])) {
        $tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];
        if ($tiempo_transcurrido > SESSION_TIMEOUT) {
            session_destroy();
            header('Location: ' . BASE_URL . 'frontend/login.php?error=Sesión expirada');
            exit();
        }
    }
    
    $_SESSION['ultimo_acceso'] = time();
}

// Iniciar sesión de usuario
function iniciarSesion($usuario_id, $usuario_nombre, $usuario_rol) {
    $_SESSION['usuario_id'] = $usuario_id;
    $_SESSION['usuario_nombre'] = $usuario_nombre;
    $_SESSION['usuario_rol'] = $usuario_rol;
    $_SESSION['ultimo_acceso'] = time();
    $_SESSION['login_time'] = time();
}

// Cerrar sesión
function cerrarSesion() {
    session_destroy();
}

// Obtener usuario actual
function usuarioActual() {
    return $_SESSION['usuario_id'] ?? null;
}

// Obtener rol del usuario
function rolUsuario() {
    return $_SESSION['usuario_rol'] ?? null;
}

// Verificar permiso
function verificarPermiso($rol_requerido) {
    if (rolUsuario() !== $rol_requerido && rolUsuario() !== ROL_ADMIN) {
        return false;
    }
    return true;
}

?>
