<?php
/**
 * API: Obtener bienes de una persona (AJAX)
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../backend/BienDAO.php';

header('Content-Type: application/json');

if (isset($_GET['persona_id'])) {
    $persona_id = intval($_GET['persona_id']);
    $bienDAO = new BienDAO($conn);
    $bienes = $bienDAO->obtenerPorPersona($persona_id);
    
    echo json_encode([
        'success' => true,
        'bienes' => $bienes
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Parámetro faltante'
    ]);
}

?>
