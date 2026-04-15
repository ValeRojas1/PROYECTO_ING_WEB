<?php
/**
 * Editar Usuario (Admin)
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/UsuarioDAO.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();
if ($_SESSION['usuario_rol'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso denegado');
}

$usuario_id = intval($_GET['id'] ?? 0);
$usuarioDAO = new UsuarioDAO($conn);

$usuario = $usuarioDAO->obtenerPorId($usuario_id);

if (!$usuario) {
    header('HTTP/1.1 404 Not Found');
    exit('Usuario no encontrado');
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $rol = sanitizar($_POST['rol'] ?? '');
    $estado = intval($_POST['estado'] ?? 0);
    
    // Validaciones backend reforzadas
    if (empty($nombre) || strlen($nombre) < 2 || strlen($nombre) > 100) {
        $error = 'El nombre debe tener entre 2 y 100 caracteres.';
    } elseif (!in_array($rol, ['usuario', 'supervisor', 'admin'])) {
        $error = 'Rol no válido.';
    } elseif (!in_array($estado, [0, 1])) {
        $error = 'Estado no válido.';
    } else {
        if ($usuarioDAO->actualizar($usuario_id, $nombre, $rol, $estado)) {
            $exito = 'Usuario actualizado correctamente';
            registrarActividad('Usuario actualizado', "Email: {$usuario['email']}");
            $usuario = $usuarioDAO->obtenerPorId($usuario_id);
        } else {
            $error = 'Error al actualizar usuario';
        }
    }
}

require_once dirname(__FILE__) . '/layout/header.php';
require_once dirname(__FILE__) . '/layout/sidebar.php';
?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Editar Usuario: <?php echo htmlspecialchars($usuario['email']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($exito): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($exito); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="formEditarUsuario" novalidate>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required 
                                       value="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                                       minlength="2" maxlength="100">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" disabled 
                                       value="<?php echo htmlspecialchars($usuario['email']); ?>">
                                <small class="text-muted">No se puede cambiar el email</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol</label>
                                <select class="form-select" id="rol" name="rol">
                                    <option value="usuario" <?php echo $usuario['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="supervisor" <?php echo $usuario['rol'] === 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                                    <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="1" <?php echo $usuario['estado'] ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo !$usuario['estado'] ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <a href="usuarios.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/validaciones.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formEditarUsuario');
        
        const reglasNombre = [
            { validar: v => Validador.requerido(v), mensaje: 'El nombre es obligatorio.' },
            { validar: v => Validador.longitudMinima(v, 2), mensaje: 'El nombre debe tener al menos 2 caracteres.' },
            { validar: v => Validador.longitudMaxima(v, 100), mensaje: 'El nombre no puede exceder 100 caracteres.' }
        ];

        Validador.adjuntarValidacionCampo(form.querySelector('[name="nombre"]'), reglasNombre);

        form.addEventListener('submit', function(e) {
            const reglas = { nombre: reglasNombre };
            if (!Validador.validarFormulario(form, reglas)) {
                e.preventDefault();
            }
        });
    });
    </script>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>
