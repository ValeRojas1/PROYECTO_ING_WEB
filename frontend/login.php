<?php
/**
 * Login - Inicio de Sesión
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/AuthService.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, ingrese email y contraseña.';
    } else {
        $authService = new AuthService($conn);
        $usuario = $authService->autenticar($email, $password);
        
        if ($usuario) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Credenciales incorrectas. Verifique su email y contraseña.';
        }
    }
}

// Capturar error de la URL si existe (ej: sesión expirada)
if (isset($_GET['error']) && empty($error)) {
    $error = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Control Patrimonial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        body {
            background-color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background: white;
            border: none;
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .login-body {
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="fas fa-boxes"></i>
        <h4 class="mb-0">Control Patrimonial</h4>
        <p class="mb-0 mt-2 text-white-50">Acceso al Sistema</p>
    </div>
    <div class="login-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                 <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php" id="formLogin" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="admin@demo.com"
                           maxlength="100">
                </div>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required
                           minlength="6" maxlength="100">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
            </button>
            <div class="text-center">
                <span class="text-muted">¿No tienes cuenta?</span> <a href="registro.php" class="text-decoration-none">Regístrate aquí</a>
            </div>
        </form>
        <script src="../assets/js/validaciones.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formLogin');
            const emailField = form.querySelector('[name="email"]');
            const passField = form.querySelector('[name="password"]');

            const reglasEmail = [
                { validar: v => Validador.requerido(v), mensaje: 'El correo electrónico es obligatorio.' },
                { validar: v => Validador.emailValido(v), mensaje: 'Ingrese un correo electrónico válido (ej: usuario@dominio.com).' }
            ];
            const reglasPass = [
                { validar: v => Validador.requerido(v), mensaje: 'La contraseña es obligatoria.' },
                { validar: v => Validador.longitudMinima(v, 6), mensaje: 'La contraseña debe tener al menos 6 caracteres.' }
            ];

            Validador.adjuntarValidacionCampo(emailField, reglasEmail);
            Validador.adjuntarValidacionCampo(passField, reglasPass);

            form.addEventListener('submit', function(e) {
                const reglas = { email: reglasEmail, password: reglasPass };
                if (!Validador.validarFormulario(form, reglas)) {
                    e.preventDefault();
                }
            });
        });
        </script>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>