<?php

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/BienService.php';
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();

$bienService = new BienService($conn);
$personaDAO = new PersonaDAO($conn);
$personas = $personaDAO->obtenerTodos();

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = sanitizar($_POST['codigo'] ?? '');
    $nombre = sanitizar($_POST['nombre'] ?? '');
    $descripcion = sanitizar($_POST['descripcion'] ?? '');
    $estado = sanitizar($_POST['estado'] ?? ESTADO_DISPONIBLE);
    $persona_id = intval($_POST['persona_id'] ?? 0) ?: null;
    
    // Validaciones backend reforzadas
    if (empty($codigo) || strlen($codigo) < 3 || strlen($codigo) > 50) {
        $error = 'El código patrimonial debe tener entre 3 y 50 caracteres.';
    } elseif (!preg_match('/^[a-zA-Z0-9\-_.]+$/', $codigo)) {
        $error = 'El código solo puede contener letras, números, guiones y puntos.';
    } elseif (empty($nombre) || strlen($nombre) < 2 || strlen($nombre) > 100) {
        $error = 'El nombre del bien debe tener entre 2 y 100 caracteres.';
    } elseif (strlen($descripcion) > 500) {
        $error = 'La descripción no puede exceder 500 caracteres.';
    } elseif ($estado === ESTADO_ASIGNADO && !$persona_id) {
        $error = 'Debe seleccionar una persona para el estado "Asignado".';
    } else {
        $resultado = $bienService->crear($codigo, $nombre, $descripcion, $estado, $persona_id);
        
        if (isset($resultado['success'])) {
            $exito = 'Bien registrado exitosamente';
            registrarActividad('Bien creado', "Código: {$codigo}");
            header('Location: bienes.php?exito=' . urlencode($exito));
            exit();
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
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Registrar Nuevo Bien</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="formRegistrarBien" novalidate>
                            <div class="mb-3">
                                <label for="codigo" class="form-label">Código Patrimonial *</label>
                                <input type="text" class="form-control" id="codigo" name="codigo" required 
                                       placeholder="Ej: PAT-2024-001" minlength="3" maxlength="50"
                                       value="<?php echo htmlspecialchars($_POST['codigo'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Bien *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required 
                                       placeholder="Ej: Computadora Dell" minlength="2" maxlength="100"
                                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                          placeholder="Detalles adicionales del bien..." maxlength="500"><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="<?php echo ESTADO_DISPONIBLE; ?>"><?php echo ESTADO_DISPONIBLE; ?></option>
                                    <option value="<?php echo ESTADO_ASIGNADO; ?>"><?php echo ESTADO_ASIGNADO; ?></option>
                                    <option value="<?php echo ESTADO_DAÑADO; ?>"><?php echo ESTADO_DAÑADO; ?></option>
                                    <option value="<?php echo ESTADO_DESCARTADO; ?>"><?php echo ESTADO_DESCARTADO; ?></option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="persona_id" class="form-label">Asignar a Persona</label>
                                <select class="form-select" id="persona_id" name="persona_id">
                                    <option value="">-- Sin asignar --</option>
                                    <?php foreach ($personas as $persona): ?>
                                        <option value="<?php echo $persona['id']; ?>">
                                            <?php echo htmlspecialchars($persona['nombre'] . ' - ' . $persona['area']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <a href="bienes.php" class="btn btn-secondary">
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
        const form = document.getElementById('formRegistrarBien');
        
        const reglasCodigo = [
            { validar: v => Validador.requerido(v), mensaje: 'El código patrimonial es obligatorio.' },
            { validar: v => Validador.longitudMinima(v, 3), mensaje: 'El código debe tener al menos 3 caracteres.' },
            { validar: v => Validador.longitudMaxima(v, 50), mensaje: 'El código no puede exceder 50 caracteres.' },
            { validar: v => Validador.codigoValido(v), mensaje: 'El código solo puede contener letras, números, guiones y puntos.' }
        ];
        const reglasNombre = [
            { validar: v => Validador.requerido(v), mensaje: 'El nombre del bien es obligatorio.' },
            { validar: v => Validador.longitudMinima(v, 2), mensaje: 'El nombre debe tener al menos 2 caracteres.' },
            { validar: v => Validador.longitudMaxima(v, 100), mensaje: 'El nombre no puede exceder 100 caracteres.' }
        ];
        const reglasDescripcion = [
            { validar: v => !v || Validador.longitudMaxima(v, 500), mensaje: 'La descripción no puede exceder 500 caracteres.' }
        ];

        Validador.adjuntarValidacionCampo(form.querySelector('[name="codigo"]'), reglasCodigo);
        Validador.adjuntarValidacionCampo(form.querySelector('[name="nombre"]'), reglasNombre);
        Validador.adjuntarValidacionCampo(form.querySelector('[name="descripcion"]'), reglasDescripcion);
        Validador.agregarContador(form.querySelector('[name="descripcion"]'), 500);

        // Coherencia estado ↔ persona
        const estadoSelect = form.querySelector('[name="estado"]');
        const personaSelect = form.querySelector('[name="persona_id"]');

        function validarCoherenciaEstado() {
            if (estadoSelect.value === '<?php echo ESTADO_ASIGNADO; ?>' && !personaSelect.value) {
                Validador.marcarInvalido(personaSelect, 'Debe seleccionar una persona cuando el estado es "Asignado".');
                return false;
            } else {
                Validador.limpiar(personaSelect);
                return true;
            }
        }

        estadoSelect.addEventListener('change', validarCoherenciaEstado);
        personaSelect.addEventListener('change', validarCoherenciaEstado);

        form.addEventListener('submit', function(e) {
            const reglas = {
                codigo: reglasCodigo,
                nombre: reglasNombre,
                descripcion: reglasDescripcion
            };
            let valido = Validador.validarFormulario(form, reglas);
            if (!validarCoherenciaEstado()) valido = false;
            if (!valido) e.preventDefault();
        });
    });
    </script>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>

