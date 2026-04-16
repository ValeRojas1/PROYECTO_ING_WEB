<?php
/**
 * DAO para gestión de Personas
 */

require_once dirname(__FILE__) . '/../config/database.php';

class PersonaDAO {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Crear persona
     */
    public function crear($nombre, $area) {
        $stmt = $this->conn->prepare(
            "INSERT INTO personas (nombre, area, estado) VALUES (?, ?, 1)"
        );
        
        $stmt->bind_param("ss", $nombre, $area);
        
        $result = $stmt->execute();
        $id = $this->conn->insert_id;
        $stmt->close();
        
        if ($result) {
            return $id;
        }
        return false;
    }
    
    /**
     * Obtener persona por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare(
            "SELECT id, nombre, area, estado FROM personas WHERE id = ?"
        );
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();
        return $row;
    }
    
    /**
     * Obtener todas las personas
     */
    public function obtenerTodos() {
        $resultado = $this->conn->query(
            "SELECT id, nombre, area, estado FROM personas WHERE estado = 1 ORDER BY nombre ASC"
        );
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Actualizar persona
     */
    public function actualizar($id, $nombre, $area, $estado) {
        $stmt = $this->conn->prepare(
            "UPDATE personas SET nombre = ?, area = ?, estado = ? WHERE id = ?"
        );
        
        $stmt->bind_param("ssii", $nombre, $area, $estado, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /**
     * Obtener bienes de una persona
     */
    public function obtenerBienes($persona_id) {
        $stmt = $this->conn->prepare(
            "SELECT id, codigo_patrimonial, nombre, descripcion, estado 
             FROM bienes WHERE persona_id = ? ORDER BY nombre ASC"
        );
        
        $stmt->bind_param("i", $persona_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $rows = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
    
    /**
     * Contar bienes de una persona
     */
    public function contarBienes($persona_id) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) as total FROM bienes WHERE persona_id = ? AND estado = ?"
        );
        
        $estado = ESTADO_ASIGNADO;
        $stmt->bind_param("is", $persona_id, $estado);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }
}

?>
