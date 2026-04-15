<?php
/**
 * Configuración de la Base de Datos
 * Sistema de Control Patrimonial
 */

// Parámetros de conexión
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'control_patrimonial');
define('DB_CHARSET', 'utf8mb4');

// Crear conexión
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset(DB_CHARSET);

// Configurar modo error
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>
