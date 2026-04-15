<?php
/**
 * DAO para gestión de Desplazamientos
 */

require_once dirname(__FILE__) . '/../config/database.php';

class DesplazamientoDAO {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Crear desplazamiento
     */
    public function crear($numero_desplazamiento, $persona_origen_id, $persona_destino_id, $motivo, $fecha) {
        $stmt = $this->conn->prepare(
            "INSERT INTO desplazamientos (numero_desplazamiento, persona_origen_id, persona_destino_id, motivo, fecha) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param("siiss", $numero_desplazamiento, $persona_origen_id, $persona_destino_id, $motivo, $fecha);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    /**
     * Agregar bien a desplazamiento
     */
    public function agregarBien($desplazamiento_id, $bien_id) {
        $stmt = $this->conn->prepare(
            "INSERT INTO detalle_desplazamiento (desplazamiento_id, bien_id) VALUES (?, ?)"
        );
        
        $stmt->bind_param("ii", $desplazamiento_id, $bien_id);
        return $stmt->execute();
    }
    
    /**
     * Obtener desplazamiento por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare(
            "SELECT d.id, d.numero_desplazamiento, d.persona_origen_id, d.persona_destino_id, 
                    d.motivo, d.fecha, po.nombre as persona_origen, pd.nombre as persona_destino
             FROM desplazamientos d 
             LEFT JOIN personas po ON d.persona_origen_id = po.id 
             LEFT JOIN personas pd ON d.persona_destino_id = pd.id 
             WHERE d.id = ?"
        );
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
    
    /**
     * Obtener todos los desplazamientos
     */
    public function obtenerTodos() {
        $resultado = $this->conn->query(
            "SELECT d.id, d.numero_desplazamiento, d.persona_origen_id, d.persona_destino_id, 
                    d.motivo, d.fecha, po.nombre as persona_origen, pd.nombre as persona_destino,
                    COUNT(dd.bien_id) as cantidad_bienes
             FROM desplazamientos d 
             LEFT JOIN personas po ON d.persona_origen_id = po.id 
             LEFT JOIN personas pd ON d.persona_destino_id = pd.id 
             LEFT JOIN detalle_desplazamiento dd ON d.id = dd.desplazamiento_id
             GROUP BY d.id
             ORDER BY d.fecha DESC"
        );
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Obtener detalles de desplazamiento
     */
    public function obtenerDetalles($desplazamiento_id) {
        $stmt = $this->conn->prepare(
            "SELECT dd.id, dd.bien_id, b.codigo_patrimonial, b.nombre, b.descripcion, b.estado
             FROM detalle_desplazamiento dd 
             JOIN bienes b ON dd.bien_id = b.id 
             WHERE dd.desplazamiento_id = ?"
        );
        
        $stmt->bind_param("i", $desplazamiento_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Verificar número de desplazamiento único
     */
    public function numeroExiste($numero) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) as total FROM desplazamientos WHERE numero_desplazamiento = ?"
        );
        
        $stmt->bind_param("s", $numero);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return $resultado['total'] > 0;
    }
    
    /**
     * Generar número de desplazamiento único
     */
    public function generarNumero() {
        $resultado = $this->conn->query("SELECT MAX(CAST(numero_desplazamiento AS UNSIGNED)) as max_num FROM desplazamientos WHERE numero_desplazamiento REGEXP '^[0-9]+$'");
        $row = $resultado->fetch_assoc();
        $numero = ($row['max_num'] ?? 0) + 1;
        return str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}

?>
