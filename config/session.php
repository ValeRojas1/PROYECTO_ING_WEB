<?php
/**
 * Gestión de Sesiones
 */

session_start();

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
