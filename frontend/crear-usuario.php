<?php
/**
 * Crear Usuario (Admin)
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/AuthService.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();
if ($_SESSION['usuario_rol'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso denegado');
}

$authService = new AuthService($conn);
$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';
    $rol = sanitizar($_POST['rol'] ?? 'usuario');
    
    // Validaciones backend reforzadas
    if (empty($nombre) || strlen($nombre) < 2 || strlen($nombre) > 100) {
        $error = 'El nombre debe tener entre 2 y 100 caracteres.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingrese un correo electrónico válido.';
    } elseif (empty($password) || strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = 'La contraseña debe contener al menos una letra y un número.';
    } elseif ($password !== $password_confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!in_array($rol, ['usuario', 'supervisor', 'admin'])) {
        $error = 'Rol no válido.';
    } else {
        $resultado = $authService->registrar($nombre, $email, $password, $rol);
        
        if (isset($resultado['success'])) {
            $exito = 'Usuario creado exitosamente';
            registrarActividad('Usuario creado', "Email: {$email}");
        } else {
            $error = $resultado['error'] ?? 'Error desconocido';
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
                        <h4 class="mb-0">Crear Nuevo Usuario</h4>
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
                        
                        <form method="POST" id="formCrearUsuario" novalidate>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required
                                       minlength="2" maxlength="100"
                                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       maxlength="100"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="password" name="password" required
                                       minlength="6" maxlength="100">
                                <small class="text-muted">Mín. 6 caracteres. Debe incluir al menos una letra y un número.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmar" class="form-label">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" required
                                       minlength="6" maxlength="100">
                            </div>
                            
                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol</label>
                                <select class="form-select" id="rol" name="rol">
                                    <option value="usuario">Usuario</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Crear
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
        const form = document.getElementById('formCrearUsuario');
        const passField = form.querySelector('[name="password"]');
        const passConfirmField = form.querySelector('[name="password_confirmar"]');
        
        const reglasNombre = [
            { validar: v => Validador.requerido(v), mensaje: 'El nombre es obligatorio.' },
            { validar: v => Validador.longitudMinima(v, 2), mensaje: 'El nombre debe tener al menos 2 caracteres.' },
            { validar: v => Validador.longitudMaxima(v, 100), mensaje: 'El nombre no puede exceder 100 caracteres.' }
        ];
        const reglasEmail = [
            { validar: v => Validador.requerido(v), mensaje: 'El email es obligatorio.' },
            { validar: v => Validador.emailValido(v), mensaje: 'Ingrese un correo electrónico válido.' }
        ];
        const reglasPassword = [
            { validar: v => Validador.requerido(v), mensaje: 'La contraseña es obligatoria.' },
            { validar: v => Validador.longitudMinima(v, 6), mensaje: 'La contraseña debe tener al menos 6 caracteres.' },
            { validar: v => Validador.contrasenaSegura(v), mensaje: 'Debe incluir al menos una letra y un número.' }
        ];
        const reglasConfirmar = [
            { validar: v => Validador.requerido(v), mensaje: 'Confirme la contraseña.' },
            { validar: v => v === passField.value, mensaje: 'Las contraseñas no coinciden.' }
        ];

        Validador.adjuntarValidacionCampo(form.querySelector('[name="nombre"]'), reglasNombre);
        Validador.adjuntarValidacionCampo(form.querySelector('[name="email"]'), reglasEmail);
        Validador.adjuntarValidacionCampo(passField, reglasPassword);
        Validador.adjuntarValidacionCampo(passConfirmField, reglasConfirmar);

        // Re-validar confirmación cuando cambia la contraseña principal
        passField.addEventListener('input', function() {
            if (passConfirmField.value) {
                if (passConfirmField.value === passField.value) {
                    Validador.marcarValido(passConfirmField);
                } else {
                    Validador.marcarInvalido(passConfirmField, 'Las contraseñas no coinciden.');
                }
            }
        });

        form.addEventListener('submit', function(e) {
            const reglas = {
                nombre: reglasNombre,
                email: reglasEmail,
                password: reglasPassword,
                password_confirmar: reglasConfirmar
            };
            if (!Validador.validarFormulario(form, reglas)) {
                e.preventDefault();
            }
        });
    });
    </script>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>
