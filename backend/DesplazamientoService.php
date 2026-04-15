<?php
/**
 * Servicio de Desplazamientos
 */

require_once dirname(__FILE__) . '/../backend/DesplazamientoDAO.php';
require_once dirname(__FILE__) . '/../backend/BienDAO.php';
require_once dirname(__FILE__) . '/../backend/HistorialDAO.php';

class DesplazamientoService {
    private $desplazamientoDAO;
    private $bienDAO;
    private $historialDAO;
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
        $this->desplazamientoDAO = new DesplazamientoDAO($conexion);
        $this->bienDAO = new BienDAO($conexion);
        $this->historialDAO = new HistorialDAO($conexion);
    }
    
    /**
     * Crear desplazamiento
     */
    public function crear($numero_desplazamiento, $persona_origen_id, $persona_destino_id, $motivo, $fecha, $bienes_ids) {
        // Validar datos
        if (empty($numero_desplazamiento) || !$persona_origen_id || !$persona_destino_id) {
            return ['error' => 'Datos incompletos'];
        }
        
        // Validar formato del número
        if (strlen($numero_desplazamiento) < 3 || strlen($numero_desplazamiento) > 50) {
            return ['error' => 'El número de desplazamiento debe tener entre 3 y 50 caracteres'];
        }
        
        if (!preg_match('/^[a-zA-Z0-9\-_.]+$/', $numero_desplazamiento)) {
            return ['error' => 'El número solo puede contener letras, números, guiones y puntos'];
        }
        
        // Validar que persona origen y destino sean diferentes
        if ($persona_origen_id === $persona_destino_id) {
            return ['error' => 'La persona origen y destino no pueden ser la misma'];
        }
        
        if (empty($bienes_ids)) {
            return ['error' => 'Debe seleccionar al menos un bien'];
        }
        
        // Validar que todos los bienes pertenecen a la persona origen
        foreach ($bienes_ids as $bien_id) {
            $bien = $this->bienDAO->obtenerPorId($bien_id);
            if (!$bien || $bien['persona_id'] != $persona_origen_id) {
                return ['error' => 'Algunos bienes no pertenecen a la persona origen'];
            }
        }
        
        // Validar número único
        if ($this->desplazamientoDAO->numeroExiste($numero_desplazamiento)) {
            return ['error' => 'El número de desplazamiento ya existe'];
        }
        
        // Iniciar transacción
        $this->conn->begin_transaction();
        
        try {
            // Crear desplazamiento
            $desplazamiento_id = $this->desplazamientoDAO->crear(
                $numero_desplazamiento,
                $persona_origen_id,
                $persona_destino_id,
                $motivo,
                $fecha
            );
            
            if (!$desplazamiento_id) {
                throw new Exception('Error al crear desplazamiento');
            }
            
            // Procesar cada bien
            foreach ($bienes_ids as $bien_id) {
                // Agregar a detalle
                if (!$this->desplazamientoDAO->agregarBien($desplazamiento_id, $bien_id)) {
                    throw new Exception('Error al agregar bien al desplazamiento');
                }
                
                // Actualizar persona del bien
                if (!$this->bienDAO->actualizarPersona($bien_id, $persona_destino_id)) {
                    throw new Exception('Error al actualizar bien');
                }
                
                // Registrar en historial
                $this->historialDAO->registrar(
                    $bien_id,
                    $persona_origen_id,
                    $persona_destino_id,
                    "Desplazamiento: {$numero_desplazamiento} - {$motivo}"
                );
            }
            
            $this->conn->commit();
            return ['success' => true, 'id' => $desplazamiento_id];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Obtener desplazamientos pendientes
     */
    public function obtenerPendientes($persona_id = null) {
        $desplazamientos = $this->desplazamientoDAO->obtenerTodos();
        
        // Filtrar por persona si se proporciona
        if ($persona_id) {
            $desplazamientos = array_filter(
                $desplazamientos,
                fn($d) => $d['persona_origen_id'] == $persona_id || $d['persona_destino_id'] == $persona_id
            );
        }
        
        return $desplazamientos;
    }
}

?>
