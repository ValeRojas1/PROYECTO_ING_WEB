<?php
/**
 * Servicio de Autenticación
 */

require_once dirname(__FILE__) . '/../backend/UsuarioDAO.php';

class AuthService {
    private $usuarioDAO;
    
    public function __construct($conexion) {
        $this->usuarioDAO = new UsuarioDAO($conexion);
    }
    
    /**
     * Autenticar usuario
     */
    public function autenticar($email, $password) {
        $usuario = $this->usuarioDAO->validar($email, $password);
        
        if ($usuario) {
            // Iniciar sesión
            iniciarSesion($usuario['id'], $usuario['nombre'], $usuario['rol']);
            return $usuario;
        }
        
        return false;
    }
    
    /**
     * Registrar nuevo usuario (solo admin)
     */
    public function registrar($nombre, $email, $password, $rol = 'usuario') {
        // Validar nombre
        if (empty($nombre) || strlen($nombre) < 2 || strlen($nombre) > 100) {
            return ['error' => 'El nombre debe tener entre 2 y 100 caracteres'];
        }
        
        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'El formato del email no es válido'];
        }
        
        // Validar contraseña
        if (strlen($password) < 6) {
            return ['error' => 'La contraseña debe tener al menos 6 caracteres'];
        }
        
        if (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return ['error' => 'La contraseña debe contener al menos una letra y un número'];
        }
        
        // Validar rol
        if (!in_array($rol, ['usuario', 'supervisor', 'admin'])) {
            return ['error' => 'Rol no válido'];
        }
        
        // Validar que email no exista
        if ($this->usuarioDAO->obtenerPorEmail($email)) {
            return ['error' => 'El email ya está registrado'];
        }
        
        $id = $this->usuarioDAO->crear($nombre, $email, $password, $rol);
        
        if ($id) {
            return ['success' => true, 'id' => $id];
        }
        
        return ['error' => 'Error al crear usuario'];
    }
    
    /**
     * Obtener usuario actual
     */
    public function obtenerActual() {
        $usuario_id = usuarioActual();
        if ($usuario_id) {
            return $this->usuarioDAO->obtenerPorId($usuario_id);
        }
        return null;
    }
}

?>
