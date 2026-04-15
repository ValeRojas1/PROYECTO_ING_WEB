<?php
/**
 * Editar Persona (Admin)
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();

$persona_id = intval($_GET['id'] ?? 0);
$personaDAO = new PersonaDAO($conn);

$persona = $personaDAO->obtenerPorId($persona_id);

if (!$persona) {
    header('HTTP/1.1 404 Not Found');
    exit('Persona no encontrada');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $area = sanitizar($_POST['area'] ?? '');
    $estado = intval($_POST['estado'] ?? 0);
    
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
        if ($personaDAO->actualizar($persona_id, $nombre, $area, $estado)) {
            registrarActividad('Persona actualizada', "Nombre: {$nombre}");
            header('Location: ver-persona.php?id=' . $persona_id);
            exit();
        } else {
            $error = 'Error al actualizar persona';
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
                        <h4 class="mb-0">Editar Persona: <?php echo htmlspecialchars($persona['nombre']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="formEditarPersona" novalidate>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required 
                                       value="<?php echo htmlspecialchars($persona['nombre']); ?>"
                                       minlength="2" maxlength="100">
                            </div>
                            
                            <div class="mb-3">
                                <label for="area" class="form-label">Área</label>
                                <input type="text" class="form-control" id="area" name="area" 
                                       value="<?php echo htmlspecialchars($persona['area'] ?? ''); ?>"
                                       maxlength="100">
                            </div>
                            
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="1" <?php echo $persona['estado'] ? 'selected' : ''; ?>>Activa</option>
                                    <option value="0" <?php echo !$persona['estado'] ? 'selected' : ''; ?>>Inactiva</option>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <a href="ver-persona.php?id=<?php echo $persona_id; ?>" class="btn btn-secondary">
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
        const form = document.getElementById('formEditarPersona');
        
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
