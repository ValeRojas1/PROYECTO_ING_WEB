<?php
/**
 * DAO para gestión de Historial de Movimientos
 */

require_once dirname(__FILE__) . '/../config/database.php';

class HistorialDAO {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Registrar movimiento en historial
     */
    public function registrar($bien_id, $persona_anterior_id, $persona_nueva_id, $accion) {
        $stmt = $this->conn->prepare(
            "INSERT INTO historial (bien_id, persona_anterior_id, persona_nueva_id, accion) 
             VALUES (?, ?, ?, ?)"
        );
        
        $stmt->bind_param("iiis", $bien_id, $persona_anterior_id, $persona_nueva_id, $accion);
        return $stmt->execute();
    }
    
    /**
     * Obtener historial de un bien
     */
    public function obtenerPorBien($bien_id) {
        $stmt = $this->conn->prepare(
            "SELECT h.id, h.bien_id, h.persona_anterior_id, h.persona_nueva_id, 
                    h.fecha, h.accion, pa.nombre as persona_anterior, pn.nombre as persona_nueva,
                    b.codigo_patrimonial, b.nombre
             FROM historial h 
             LEFT JOIN personas pa ON h.persona_anterior_id = pa.id 
             LEFT JOIN personas pn ON h.persona_nueva_id = pn.id 
             LEFT JOIN bienes b ON h.bien_id = b.id 
             WHERE h.bien_id = ? 
             ORDER BY h.fecha DESC"
        );
        
        $stmt->bind_param("i", $bien_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Obtener historial de una persona
     */
    public function obtenerPorPersona($persona_id) {
        $stmt = $this->conn->prepare(
            "SELECT h.id, h.bien_id, h.persona_anterior_id, h.persona_nueva_id, 
                    h.fecha, h.accion, pa.nombre as persona_anterior, pn.nombre as persona_nueva,
                    b.codigo_patrimonial, b.nombre
             FROM historial h 
             LEFT JOIN personas pa ON h.persona_anterior_id = pa.id 
             LEFT JOIN personas pn ON h.persona_nueva_id = pn.id 
             LEFT JOIN bienes b ON h.bien_id = b.id 
             WHERE h.persona_anterior_id = ? OR h.persona_nueva_id = ? 
             ORDER BY h.fecha DESC"
        );
        
        $stmt->bind_param("ii", $persona_id, $persona_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Obtener historial completo (auditoría)
     */
    public function obtenerTodo() {
        $resultado = $this->conn->query(
            "SELECT h.id, h.bien_id, h.persona_anterior_id, h.persona_nueva_id, 
                    h.fecha, h.accion, pa.nombre as persona_anterior, pn.nombre as persona_nueva,
                    b.codigo_patrimonial, b.nombre
             FROM historial h 
             LEFT JOIN personas pa ON h.persona_anterior_id = pa.id 
             LEFT JOIN personas pn ON h.persona_nueva_id = pn.id 
             LEFT JOIN bienes b ON h.bien_id = b.id 
             ORDER BY h.fecha DESC"
        );
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Eliminar todo el historial
     */
    public function eliminarTodo() {
        return $this->conn->query("TRUNCATE TABLE historial");
    }
    
    /**
     * Eliminar un registro específico del historial
     */
    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM historial WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Eliminar múltiples registros del historial
     */
    public function eliminarMultiples($ids) {
        if (empty($ids)) return false;
        
        $ids_clean = array_map('intval', $ids);
        $ids_str = implode(',', $ids_clean);
        
        return $this->conn->query("DELETE FROM historial WHERE id IN ($ids_str)");
    }
}
?>
