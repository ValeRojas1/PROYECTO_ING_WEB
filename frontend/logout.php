<?php
/**
 * Logout
 */

require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../config/constants.php';

cerrarSesion();
header('Location: ' . BASE_URL . 'frontend/login.php?mensaje=Sesión cerrada correctamente');
exit();
