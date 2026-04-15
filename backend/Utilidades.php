<?php
/**
 * Funciones de Utilidad
 */

/**
 * Validar email
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitizar entrada
 */
function sanitizar($data) {
    if (is_array($data)) {
        return array_map('sanitizar', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Responder JSON
 */
function respuestaJSON($data, $codigo = 200) {
    header('Content-Type: application/json');
    http_response_code($codigo);
    echo json_encode($data);
    exit();
}

/**
 * Obtener IP del cliente
 */
function obtenerIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * Registrar actividad
 */
function registrarActividad($actividad, $detalles = '') {
    // Aquí se puede guardar a BD o archivo de log
    $log = date('Y-m-d H:i:s') . " | IP: " . obtenerIP() . " | {$actividad} | {$detalles}\n";
    // file_put_contents(PROJECT_ROOT . '/logs/actividad.log', $log, FILE_APPEND);
}

/**
 * Formatear fecha
 */
function formatearFecha($fecha, $formato = 'd/m/Y') {
    $timestamp = strtotime($fecha);
    return date($formato, $timestamp);
}

/**
 * Generar número aleatorio
 */
function generarCodigo($prefijo = '', $longitud = 8) {
    $codigo = $prefijo . strtoupper(substr(uniqid(''), -$longitud));
    return $codigo;
}

/**
 * Verificar si es AJAX
 */
function esAJAX() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Convertir array a CSV
 */
function arrayACSV($datos, $separador = ',', $encerrador = '"') {
    $salida = '';
    
    foreach ($datos as $fila) {
        $datos_fila = [];
        foreach ($fila as $valor) {
            $valor = str_replace($encerrador, $encerrador . $encerrador, $valor);
            $datos_fila[] = $encerrador . $valor . $encerrador;
        }
        $salida .= implode($separador, $datos_fila) . "\n";
    }
    
    return $salida;
}

/**
 * Descargar archivo de forma segura
 */
function descargarArchivo($ruta_archivo, $nombre_descarga) {
    if (file_exists($ruta_archivo)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $nombre_descarga . '"');
        header('Content-Length: ' . filesize($ruta_archivo));
        readfile($ruta_archivo);
        exit();
    }
}

// ============================================
// FUNCIONES DE VALIDACIÓN CENTRALIZADAS
// ============================================

/**
 * Validar longitud de cadena
 */
function validarLongitud($valor, $min = 0, $max = PHP_INT_MAX) {
    $len = strlen(trim($valor));
    return $len >= $min && $len <= $max;
}

/**
 * Validar formato de código patrimonial (letras, números, guiones, puntos)
 */
function validarCodigoPatrimonial($valor) {
    return preg_match('/^[a-zA-Z0-9\-_.]+$/', trim($valor));
}

/**
 * Validar que solo contenga letras y espacios
 */
function validarSoloLetras($valor) {
    return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', trim($valor));
}

/**
 * Validar contraseña segura (al menos 1 letra y 1 número)
 */
function validarContrasena($valor) {
    return preg_match('/[a-zA-Z]/', $valor) && preg_match('/[0-9]/', $valor);
}

/**
 * Validar que dos valores no sean iguales
 */
function validarValoresDiferentes($val1, $val2) {
    return $val1 != $val2;
}

?>
