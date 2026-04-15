<?php
/**
 * Servicio de Gestión de Bienes
 */

require_once dirname(__FILE__) . '/../backend/BienDAO.php';
require_once dirname(__FILE__) . '/../backend/HistorialDAO.php';
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';

class BienService {
    private $bienDAO;
    private $historialDAO;
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
        $this->bienDAO = new BienDAO($conexion);
        $this->historialDAO = new HistorialDAO($conexion);
    }
    
    /**
     * Crear bien individual
     */
    public function crear($codigo, $nombre, $descripcion, $estado = ESTADO_DISPONIBLE, $persona_id = null) {
        // Validar datos obligatorios y formato
        if (empty($codigo) || empty($nombre)) {
            return ['error' => 'Código y nombre son obligatorios'];
        }
        
        if (strlen($codigo) < 3 || strlen($codigo) > 50) {
            return ['error' => 'El código debe tener entre 3 y 50 caracteres'];
        }
        
        if (!preg_match('/^[a-zA-Z0-9\-_.]+$/', $codigo)) {
            return ['error' => 'El código solo puede contener letras, números, guiones y puntos'];
        }
        
        if (strlen($nombre) < 2 || strlen($nombre) > 100) {
            return ['error' => 'El nombre debe tener entre 2 y 100 caracteres'];
        }
        
        if (strlen($descripcion) > 500) {
            return ['error' => 'La descripción no puede exceder 500 caracteres'];
        }
        
        // Validar código no exista
        if ($this->bienDAO->codigoExiste($codigo)) {
            return ['error' => 'El código patrimonial ya existe'];
        }
        
        // Validar coherencia estado-persona
        if ($estado === ESTADO_ASIGNADO && !$persona_id) {
            return ['error' => 'Debe asignar una persona para estado Asignado'];
        }
        
        $bien_id = $this->bienDAO->crear($codigo, $nombre, $descripcion, $estado, $persona_id);
        
        if ($bien_id) {
            // Registrar en historial
            if ($persona_id) {
                $this->historialDAO->registrar($bien_id, null, $persona_id, 'Creación y asignación');
            }
            return ['success' => true, 'id' => $bien_id];
        }
        
        return ['error' => 'Error al crear bien'];
    }
    
    /**
     * Actualizar bien
     */
    public function actualizar($id, $nombre, $descripcion, $estado, $persona_id = null) {
        $bien_actual = $this->bienDAO->obtenerPorId($id);
        
        if (!$bien_actual) {
            return ['error' => 'Bien no encontrado'];
        }
        
        // Validar nombre
        if (empty($nombre) || strlen($nombre) < 2 || strlen($nombre) > 100) {
            return ['error' => 'El nombre debe tener entre 2 y 100 caracteres'];
        }
        
        // Validar descripción
        if (strlen($descripcion) > 500) {
            return ['error' => 'La descripción no puede exceder 500 caracteres'];
        }
        
        // Si el estado cambia a ASIGNADO, debe tener persona
        if ($estado === ESTADO_ASIGNADO && !$persona_id) {
            return ['error' => 'Debe asignar una persona para estado Asignado'];
        }
        
        if ($this->bienDAO->actualizar($id, $nombre, $descripcion, $estado, $persona_id)) {
            // Registrar cambio en historial si cambió la persona
            if ($persona_id && $bien_actual['persona_id'] !== $persona_id) {
                $this->historialDAO->registrar(
                    $id, 
                    $bien_actual['persona_id'], 
                    $persona_id, 
                    'Actualización de asignación'
                );
            }
            return ['success' => true];
        }
        
        return ['error' => 'Error al actualizar bien'];
    }
    
    /**
     * Eliminar múltiples bienes (o individual)
     */
    public function eliminarBienes($ids) {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        
        $ids = array_filter($ids, 'is_numeric');
        if (empty($ids)) {
            return ['error' => 'No se proporcionaron IDs válidos'];
        }
        
        if ($this->bienDAO->eliminarMultiples($ids)) {
            return ['success' => true, 'cantidad' => count($ids)];
        }
        
        return ['error' => 'Error al eliminar bienes'];
    }
    
    /**
     * Importar bienes desde Excel
     */
    public function importarExcel($archivo) {
        // Verificar extensión
        $info = pathinfo($archivo);
        $ext = strtolower($info['extension']);
        
        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            return ['error' => 'Formato de archivo no válido'];
        }
        
        $bienes_creados = 0;
        $errores = [];
        
        // Leer archivo CSV
        if (($fichero = fopen($archivo, 'r')) !== false) {
            // Auto-detectar delimitador
            $primera_linea = fgets($fichero);
            if ($primera_linea === false) {
                fclose($fichero);
                return ['error' => 'El archivo está vacío'];
            }
            $delimitador = (strpos($primera_linea, ';') !== false) ? ';' : ',';
            rewind($fichero);

            // Saltar BOM (\xEF\xBB\xBF) si existe
            $bom = fread($fichero, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($fichero);
            }

            $contador = 0;
            while (($datos = fgetcsv($fichero, 1000, $delimitador)) !== false) {
                $contador++;
                
                // Saltar header
                if ($contador === 1) continue;
                
                // Validar estructura
                if (count($datos) < 3) {
                    $errores[] = "Fila {$contador}: Información incompleta";
                    continue;
                }
                
                $codigo = trim($datos[0]);
                $nombre = trim($datos[1]);
                $descripcion = trim($datos[2] ?? '');
                $persona_nombre = trim($datos[3] ?? '');
                
                // Validar código
                if (empty($codigo) || empty($nombre)) {
                    $errores[] = "Fila {$contador}: Código o nombre vacío";
                    continue;
                }
                
                if ($this->bienDAO->codigoExiste($codigo)) {
                    $errores[] = "Fila {$contador}: Código {$codigo} ya existe";
                    continue;
                }
                
                // Buscar persona si se proporciona
                $persona_id = null;
                if (!empty($persona_nombre)) {
                    $personaDAO = new PersonaDAO($this->conn);
                    $personas = $personaDAO->obtenerTodos();
                    foreach ($personas as $p) {
                        if (strtolower($p['nombre']) === strtolower($persona_nombre)) {
                            $persona_id = $p['id'];
                            break;
                        }
                    }
                }
                
                $estado = $persona_id ? ESTADO_ASIGNADO : ESTADO_DISPONIBLE;
                
                if ($this->bienDAO->crear($codigo, $nombre, $descripcion, $estado, $persona_id)) {
                    $bienes_creados++;
                } else {
                    $errores[] = "Fila {$contador}: Error al crear bien";
                }
            }
            fclose($fichero);
        }
        
        return [
            'success' => true,
            'bienes_creados' => $bienes_creados,
            'errores' => $errores
        ];
    }
    
    /**
     * Obtener estadísticas
     */
    public function obtenerEstadisticas() {
        $bienes = $this->bienDAO->obtenerTodos();
        
        $total = count($bienes);
        $disponibles = count(array_filter($bienes, fn($b) => $b['estado'] === ESTADO_DISPONIBLE));
        $asignados = count(array_filter($bienes, fn($b) => $b['estado'] === ESTADO_ASIGNADO));
        $dañados = count(array_filter($bienes, fn($b) => $b['estado'] === ESTADO_DAÑADO));
        $descartados = count(array_filter($bienes, fn($b) => $b['estado'] === ESTADO_DESCARTADO));
        
        return [
            'total' => $total,
            'disponibles' => $disponibles,
            'asignados' => $asignados,
            'dañados' => $dañados,
            'descartados' => $descartados
        ];
    }
}

?>
