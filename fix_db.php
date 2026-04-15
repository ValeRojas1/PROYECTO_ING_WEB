<?php
require_once dirname(__FILE__) . '/config/database.php';

// Generar un hash correcto para '123456'
$hash = password_hash('123456', PASSWORD_BCRYPT);
echo "Nuevo hash generado: " . $hash . "\n";

// Actualizar en la base de datos
$conn->query("UPDATE usuarios SET password = '$hash'");
echo "Filas actualizadas: " . $conn->affected_rows . "\n";

?>
