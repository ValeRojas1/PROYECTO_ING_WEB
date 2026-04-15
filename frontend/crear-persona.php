<?php
/**
 * Crear Persona (Admin)
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();

$personaDAO = new PersonaDAO($conn);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $area = sanitizar($_POST['area'] ?? '');
    
    // Validaciones backend reforzadas
    if (empty($nombre)) {
        $error = 'El nombre es obligatorio.';
    } elseif (strlen($nombre) < 2 || strlen($nombre) > 100) {
        $error = 'El nombre debe tener entre 2 y 100 caracteres.';
    } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', $nombre)) {
        $error = 'El nombre solo puede contener letras y espacios.';
    } elseif (!empty($area) && strlen($area) > 100) {
        $error = 'El área no puede exceder 100 caracteres.';
    } else {
        $persona_id = $personaDAO->crear($nombre, $area);
        if ($persona_id) {
            registrarActividad('Persona creada', "Nombre: {$nombre}");
            header('Location: personas.php');
            exit();
        } else {
            $error = 'Error al crear persona';
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
                        <h4 class="mb-0">Crear Nueva Persona</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="formCrearPersona" novalidate>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required
                                       minlength="2" maxlength="100" placeholder="Ej: Juan Pérez"
                                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="area" class="form-label">Área</label>
                                <input type="text" class="form-control" id="area" name="area" 
                                       placeholder="Ej: Sistemas, Administración" maxlength="100"
                                       value="<?php echo htmlspecialchars($_POST['area'] ?? ''); ?>">
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Crear
                                </button>
                                <a href="personas.php" class="btn btn-secondary">
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
        const form = document.getElementById('formCrearPersona');
        
        const reglasNombre = [
            { validar: v => Validador.requerido(v), mensaje: 'El nombre es obligatorio.' },
            { validar: v => Validador.longitudMinima(v, 2), mensaje: 'El nombre debe tener al menos 2 caracteres.' },
            { validar: v => Validador.longitudMaxima(v, 100), mensaje: 'El nombre no puede exceder 100 caracteres.' },
            { validar: v => Validador.soloLetras(v), mensaje: 'El nombre solo puede contener letras y espacios.' }
        ];
        const reglasArea = [
            { validar: v => !v || Validador.longitudMaxima(v, 100), mensaje: 'El área no puede exceder 100 caracteres.' }
        ];

        Validador.adjuntarValidacionCampo(form.querySelector('[name="nombre"]'), reglasNombre);
        Validador.adjuntarValidacionCampo(form.querySelector('[name="area"]'), reglasArea);

        form.addEventListener('submit', function(e) {
            const reglas = {
                nombre: reglasNombre,
                area: reglasArea
            };
            if (!Validador.validarFormulario(form, reglas)) {
                e.preventDefault();
            }
        });
    });
    </script>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>
