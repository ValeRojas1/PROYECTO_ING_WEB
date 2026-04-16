<?php
/**
 * DAO para gestión de Bienes
 */

require_once dirname(__FILE__) . '/../config/database.php';

class BienDAO {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Crear bien
     */
    public function crear($codigo_patrimonial, $nombre, $descripcion, $estado, $persona_id = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO bienes (codigo_patrimonial, nombre, descripcion, estado, persona_id) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param("ssssi", $codigo_patrimonial, $nombre, $descripcion, $estado, $persona_id);
        
        $result = $stmt->execute();
        $id = $this->conn->insert_id;
        $stmt->close();
        
        if ($result) {
            return $id;
        }
        return false;
    }
    
    /**
     * Obtener bien por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare(
            "SELECT b.id, b.codigo_patrimonial, b.nombre, b.descripcion, b.estado, 
                    b.persona_id, p.nombre as persona_nombre, p.area, b.fecha_registro
             FROM bienes b 
             LEFT JOIN personas p ON b.persona_id = p.id 
             WHERE b.id = ?"
        );
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();
        return $row;
    }
    
    /**
     * Obtener bien por código patrimonial
     */
    public function obtenerPorCodigo($codigo) {
        $stmt = $this->conn->prepare(
            "SELECT id, codigo_patrimonial, nombre, descripcion, estado, persona_id 
             FROM bienes WHERE codigo_patrimonial = ?"
        );
        
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();
        return $row;
    }
    
    /**
     * Obtener todos los bienes
     */
    public function obtenerTodos($filtro = null, $valor = null) {
        $query = "SELECT b.id, b.codigo_patrimonial, b.nombre, b.descripcion, b.estado, 
                         b.persona_id, p.nombre as persona_nombre, p.area, b.fecha_registro
                  FROM bienes b 
                  LEFT JOIN personas p ON b.persona_id = p.id ";
        
        if ($filtro && $valor) {
            switch($filtro) {
                case 'estado':
                    $query .= "WHERE b.estado = ? ";
                    break;
                case 'persona':
                    $query .= "WHERE b.persona_id = ? ";
                    break;
            }
        }
        
        $query .= "ORDER BY b.nombre ASC";
        
        if ($filtro && $valor) {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $valor);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $rows = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        } else {
            $resultado = $this->conn->query($query);
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }
    }
    
    /**
     * Actualizar bien
     */
    public function actualizar($id, $nombre, $descripcion, $estado, $persona_id = null) {
        $stmt = $this->conn->prepare(
            "UPDATE bienes SET nombre = ?, descripcion = ?, estado = ?, persona_id = ? WHERE id = ?"
        );
        
        $stmt->bind_param("sssii", $nombre, $descripcion, $estado, $persona_id, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /**
     * Actualizar persona asignada
     */
    public function actualizarPersona($bien_id, $persona_id) {
        $estado = ESTADO_ASIGNADO;
        $stmt = $this->conn->prepare(
            "UPDATE bienes SET persona_id = ?, estado = ? WHERE id = ?"
        );
        
        $stmt->bind_param("isi", $persona_id, $estado, $bien_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /**
     * Obtener bienes por persona
     */
    public function obtenerPorPersona($persona_id) {
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
     * Contar bienes totales
     */
    public function contar() {
        $resultado = $this->conn->query("SELECT COUNT(*) as total FROM bienes");
        $row = $resultado->fetch_assoc();
        return $row['total'];
    }
    
    /**
     * Codificar bien ya existe
     */
    public function codigoExiste($codigo, $excluir_id = null) {
        if ($excluir_id) {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as total FROM bienes WHERE codigo_patrimonial = ? AND id != ?"
            );
            $stmt->bind_param("si", $codigo, $excluir_id);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as total FROM bienes WHERE codigo_patrimonial = ?"
            );
            $stmt->bind_param("s", $codigo);
        }
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();
        return $row['total'] > 0;
    }
    
    /**
     * Eliminar bien (individual)
     * Elimina previamente registros relacionados para evitar errores de llave foránea.
     */
    public function eliminar($id) {
        $id = intval($id);
        $this->conn->query("DELETE FROM historial WHERE bien_id = $id");
        $this->conn->query("DELETE FROM detalle_desplazamiento WHERE bien_id = $id");
        return $this->conn->query("DELETE FROM bienes WHERE id = $id");
    }
    
    /**
     * Eliminar múltiples bienes
     */
    public function eliminarMultiples($ids) {
        if (empty($ids)) return false;
        
        $ids_clean = array_map('intval', $ids);
        $ids_str = implode(',', $ids_clean);
        
        $this->conn->query("DELETE FROM historial WHERE bien_id IN ($ids_str)");
        $this->conn->query("DELETE FROM detalle_desplazamiento WHERE bien_id IN ($ids_str)");
        return $this->conn->query("DELETE FROM bienes WHERE id IN ($ids_str)");
    }
}

?>
