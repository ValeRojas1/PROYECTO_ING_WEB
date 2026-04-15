<?php
/**
 * Registro Público de Usuarios
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/AuthService.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';
    $rol = sanitizar($_POST['rol'] ?? 'usuario');
    
    // El administrador solo puede ser creado desde adentro por otro admin
    if ($rol === 'admin') {
        $rol = 'usuario';
    }
    
    // Validaciones
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
    } elseif (!in_array($rol, ['usuario', 'supervisor'])) {
        $error = 'Rol no válido.';
    } else {
        $authService = new AuthService($conn);
        $resultado = $authService->registrar($nombre, $email, $password, $rol);
        
        if (isset($resultado['success'])) {
            $exito = 'Cuenta creada exitosamente. Puede iniciar sesión.';
        } else {
            $error = $resultado['error'] ?? 'Error al registrar usuario.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Control Patrimonial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        body {
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }
        .login-card {
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background: white;
            border: none;
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }
        .login-header i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .login-body {
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="fas fa-user-plus"></i>
        <h4 class="mb-0">Control Patrimonial</h4>
        <p class="mb-0 mt-2 text-white-50">Crear una cuenta</p>
    </div>
    <div class="login-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                 <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($exito)): ?>
            <div class="alert alert-success" role="alert">
                 <i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($exito); ?>
                 <br>
                 <a href="login.php" class="alert-link">Haz clic aquí para iniciar sesión</a>
            </div>
        <?php else: ?>
        
        <form method="POST" id="formRegistro" novalidate>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                           minlength="2" maxlength="100" placeholder="Ej: Juan Pérez"
                           value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required 
                           placeholder="usuario@ejemplo.com" maxlength="100"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="rol" class="form-label">Perfil de Usuario *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                    <select class="form-select" id="rol" name="rol" required>
                        <option value="usuario" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'usuario') ? 'selected' : ''; ?>>Usuario Normal</option>
                        <option value="supervisor" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'supervisor') ? 'selected' : ''; ?>>Supervisor / Encargado</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required
                           minlength="6" maxlength="100">
                </div>
                <small class="text-muted d-block mt-1">Mín. 6 caracteres (incluir mínimo 1 letra y 1 número).</small>
            </div>
            
            <div class="mb-4">
                <label for="password_confirmar" class="form-label">Confirmar Contraseña *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" required
                           minlength="6" maxlength="100">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                <i class="fas fa-check-circle me-2"></i> Crear Cuenta
            </button>
            <div class="text-center">
                <span class="text-muted">¿Ya tienes cuenta?</span> <a href="login.php" class="text-decoration-none">Inicia sesión</a>
            </div>
        </form>
        
        <script src="../assets/js/validaciones.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formRegistro');
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
                { validar: v => Validador.longitudMinima(v, 6), mensaje: 'Debe tener al menos 6 caracteres.' },
                { validar: v => Validador.contrasenaSegura(v), mensaje: 'Debe incluir al menos 1 letra y 1 número.' }
            ];
            const reglasConfirmar = [
                { validar: v => Validador.requerido(v), mensaje: 'Confirme la contraseña.' },
                { validar: v => v === passField.value, mensaje: 'Las contraseñas no coinciden.' }
            ];

            Validador.adjuntarValidacionCampo(form.querySelector('[name="nombre"]'), reglasNombre);
            Validador.adjuntarValidacionCampo(form.querySelector('[name="email"]'), reglasEmail);
            Validador.adjuntarValidacionCampo(passField, reglasPassword);
            Validador.adjuntarValidacionCampo(passConfirmField, reglasConfirmar);

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
        
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
