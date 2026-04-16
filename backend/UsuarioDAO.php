<?php
/**
 * DAO para gestión de Usuarios
 */

require_once dirname(__FILE__) . '/../config/database.php';

class UsuarioDAO {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    /**
     * Obtener usuario por email
     */
    public function obtenerPorEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, nombre, email, password, rol, estado FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();
        return $row;
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT id, nombre, email, rol, estado FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();
        return $row;
    }
    
    /**
     * Crear nuevo usuario
     */
    public function crear($nombre, $email, $password, $rol = 'usuario') {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $this->conn->prepare(
            "INSERT INTO usuarios (nombre, email, password, rol, estado) 
             VALUES (?, ?, ?, ?, 1)"
        );
        
        $stmt->bind_param("ssss", $nombre, $email, $password_hash, $rol);
        
        $result = $stmt->execute();
        $id = $this->conn->insert_id;
        $stmt->close();
        
        if ($result) {
            return $id;
        }
        return false;
    }
    
    /**
     * Actualizar usuario
     */
    public function actualizar($id, $nombre, $rol, $estado) {
        $stmt = $this->conn->prepare(
            "UPDATE usuarios SET nombre = ?, rol = ?, estado = ? WHERE id = ?"
        );
        
        $stmt->bind_param("ssii", $nombre, $rol, $estado, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /**
     * Cambiar contraseña
     */
    public function cambiarPassword($id, $password_nuevo) {
        $password_hash = password_hash($password_nuevo, PASSWORD_BCRYPT);
        
        $stmt = $this->conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password_hash, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos() {
        $resultado = $this->conn->query(
            "SELECT id, nombre, email, rol, estado, fecha_creacion FROM usuarios ORDER BY nombre ASC"
        );
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Validar credenciales
     */
    public function validar($email, $password) {
        $usuario = $this->obtenerPorEmail($email);
        
        if (!$usuario) {
            return false;
        }
        
        if ($usuario['estado'] != 1) {
            return false;
        }
        
        if (password_verify($password, $usuario['password'])) {
            unset($usuario['password']);
            return $usuario;
        }
        
        return false;
    }
}

?>
