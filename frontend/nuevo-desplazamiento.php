<?php
/**
 * Nuevo Desplazamiento
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/DesplazamientoService.php';
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';
require_once dirname(__FILE__) . '/../backend/BienDAO.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();

$desplazamientoService = new DesplazamientoService($conn);
$personaDAO = new PersonaDAO($conn);
$bienDAO = new BienDAO($conn);

$personas = $personaDAO->obtenerTodos();
$error = '';
$exito = '';

$persona_origen_id = $_GET['persona_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = sanitizar($_POST['numero'] ?? '');
    $persona_origen_id = intval($_POST['persona_origen_id'] ?? 0);
    $persona_destino_id = intval($_POST['persona_destino_id'] ?? 0);
    $motivo = sanitizar($_POST['motivo'] ?? '');
    $fecha = sanitizar($_POST['fecha'] ?? date('Y-m-d'));
    $bienes_ids = $_POST['bienes_ids'] ?? [];
    
    if (!is_array($bienes_ids)) {
        $bienes_ids = [];
    }
    
    // Validaciones backend reforzadas
    if (empty($numero) || strlen($numero) < 3 || strlen($numero) > 50) {
        $error = 'El número de desplazamiento debe tener entre 3 y 50 caracteres.';
    } elseif (!preg_match('/^[a-zA-Z0-9\-_.]+$/', $numero)) {
        $error = 'El número solo puede contener letras, números, guiones y puntos.';
    } elseif (!$persona_origen_id) {
        $error = 'Debe seleccionar una persona origen.';
    } elseif (!$persona_destino_id) {
        $error = 'Debe seleccionar una persona destino.';
    } elseif ($persona_origen_id === $persona_destino_id) {
        $error = 'La persona origen y destino no pueden ser la misma.';
    } elseif (empty($motivo)) {
        $error = 'Debe seleccionar un motivo.';
    } elseif (empty($fecha)) {
        $error = 'La fecha es obligatoria.';
    } elseif (empty($bienes_ids)) {
        $error = 'Debe seleccionar al menos un bien para desplazar.';
    } else {
        $resultado = $desplazamientoService->crear(
            $numero,
            $persona_origen_id,
            $persona_destino_id,
            $motivo,
            $fecha,
            array_map('intval', $bienes_ids)
        );
        
        if (isset($resultado['success'])) {
            $exito = 'Desplazamiento registrado exitosamente';
            registrarActividad('Desplazamiento creado', "Número: {$numero}");
            header('Location: desplazamientos.php?exito=' . urlencode($exito));
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
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Nuevo Desplazamiento de Bienes</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="formDesplazamiento" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="numero" class="form-label">Número de Desplazamiento *</label>
                                        <input type="text" class="form-control" id="numero" name="numero" required 
                                               placeholder="Ej: DESP-001" minlength="3" maxlength="50"
                                               value="<?php echo htmlspecialchars($_POST['numero'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha" class="form-label">Fecha *</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" required 
                                               value="<?php echo htmlspecialchars($_POST['fecha'] ?? date('Y-m-d')); ?>"
                                               max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="persona_origen_id" class="form-label">Persona Origen *</label>
                                        <select class="form-select" id="persona_origen_id" name="persona_origen_id" required onchange="actualizarBienes()">
                                            <option value="">-- Seleccione Persona --</option>
                                            <?php foreach ($personas as $p): ?>
                                                <option value="<?php echo $p['id']; ?>" 
                                                    <?php echo $persona_origen_id == $p['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($p['nombre'] . ' - ' . $p['area']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="persona_destino_id" class="form-label">Persona Destino *</label>
                                        <select class="form-select" id="persona_destino_id" name="persona_destino_id" required>
                                            <option value="">-- Seleccione Persona --</option>
                                            <?php foreach ($personas as $p): ?>
                                                <option value="<?php echo $p['id']; ?>">
                                                    <?php echo htmlspecialchars($p['nombre'] . ' - ' . $p['area']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="motivo" class="form-label">Motivo del Desplazamiento *</label>
                                <select class="form-select" id="motivo" name="motivo" required>
                                    <option value="">-- Seleccione Motivo --</option>
                                    <option value="Cambio de área">Cambio de área</option>
                                    <option value="Renuncia">Renuncia</option>
                                    <option value="Promoción">Promoción</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Bienes a Desplazar *</label>
                                <div id="bienesContainer" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                    <p class="text-muted">Seleccione una persona origen para ver sus bienes</p>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <strong>Bienes seleccionados:</strong> <span id="cantidadBienes">0</span>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check"></i> Crear Desplazamiento
                                </button>
                                <a href="desplazamientos.php" class="btn btn-secondary">
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
    const personaOrigenSelect = document.getElementById('persona_origen_id');
    const personaDestinoSelect = document.getElementById('persona_destino_id');
    const bienesContainer = document.getElementById('bienesContainer');
    
    function actualizarBienes() {
        const personaId = personaOrigenSelect.value;
        
        if (!personaId) {
            bienesContainer.innerHTML = '<p class="text-muted">Seleccione una persona origen para ver sus bienes</p>';
            return;
        }
        
        // Hacer petición AJAX para obtener bienes
        fetch(`../backend/api_bienes.php?persona_id=${personaId}`)
            .then(r => r.json())
            .then(data => {
                if (data.bienes && data.bienes.length > 0) {
                    let html = '';
                    data.bienes.forEach(bien => {
                        html += `
                            <div class="form-check">
                                <input class="form-check-input bien-checkbox" type="checkbox" 
                                       value="${bien.id}" id="bien_${bien.id}" 
                                       name="bienes_ids[]" onchange="actualizarCantidad()">
                                <label class="form-check-label" for="bien_${bien.id}">
                                    ${bien.codigo_patrimonial} - ${bien.nombre}
                                </label>
                            </div>
                        `;
                    });
                    bienesContainer.innerHTML = html;
                } else {
                    bienesContainer.innerHTML = '<p class="text-muted text-warning">Esta persona no tiene bienes asignados</p>';
                }
            })
            .catch(e => {
                bienesContainer.innerHTML = '<p class="text-danger">Error al cargar bienes</p>';
            });
            
        // Validar coherencia persona origen ≠ destino
        validarPersonasDiferentes();
    }
    
    function actualizarCantidad() {
        const cantidad = document.querySelectorAll('.bien-checkbox:checked').length;
        document.getElementById('cantidadBienes').textContent = cantidad;
    }

    // ====== VALIDACIONES ======
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formDesplazamiento');
        
        const reglasNumero = [
            { validar: v => Validador.requerido(v), mensaje: 'El número de desplazamiento es obligatorio.' },
            { validar: v => Validador.longitudMinima(v, 3), mensaje: 'El número debe tener al menos 3 caracteres.' },
            { validar: v => Validador.longitudMaxima(v, 50), mensaje: 'El número no puede exceder 50 caracteres.' },
            { validar: v => Validador.codigoValido(v), mensaje: 'Solo letras, números, guiones y puntos.' }
        ];
        const reglasFecha = [
            { validar: v => Validador.requerido(v), mensaje: 'La fecha es obligatoria.' },
            { validar: v => Validador.fechaNoFutura(v), mensaje: 'La fecha no puede ser futura.' }
        ];
        const reglasOrigen = [
            { validar: v => Validador.requerido(v), mensaje: 'Seleccione una persona origen.' }
        ];
        const reglasDestino = [
            { validar: v => Validador.requerido(v), mensaje: 'Seleccione una persona destino.' }
        ];
        const reglasMotivo = [
            { validar: v => Validador.requerido(v), mensaje: 'Seleccione un motivo.' }
        ];

        Validador.adjuntarValidacionCampo(form.querySelector('[name="numero"]'), reglasNumero);
        Validador.adjuntarValidacionCampo(form.querySelector('[name="fecha"]'), reglasFecha);
        Validador.adjuntarValidacionCampo(form.querySelector('[name="motivo"]'), reglasMotivo);

        // Validar persona origen ≠ destino
        function validarPersonasDiferentes() {
            const origen = personaOrigenSelect.value;
            const destino = personaDestinoSelect.value;
            
            if (origen && destino && origen === destino) {
                Validador.marcarInvalido(personaDestinoSelect, 'La persona destino no puede ser igual a la persona origen.');
                return false;
            } else if (destino) {
                Validador.marcarValido(personaDestinoSelect);
            } else {
                Validador.limpiar(personaDestinoSelect);
            }
            return true;
        }
        
        // Exponer para uso global
        window.validarPersonasDiferentes = validarPersonasDiferentes;

        personaOrigenSelect.addEventListener('change', function() {
            // Validar campo requerido
            if (this.value) {
                Validador.marcarValido(this);
            } else {
                Validador.marcarInvalido(this, 'Seleccione una persona origen.');
            }
            validarPersonasDiferentes();
        });

        personaDestinoSelect.addEventListener('change', function() {
            validarPersonasDiferentes();
            if (!this.value) {
                Validador.marcarInvalido(this, 'Seleccione una persona destino.');
            }
        });

        form.addEventListener('submit', function(e) {
            let valido = true;

            // Validar campos con reglas
            const reglas = {
                numero: reglasNumero,
                fecha: reglasFecha,
                persona_origen_id: reglasOrigen,
                persona_destino_id: reglasDestino,
                motivo: reglasMotivo
            };
            if (!Validador.validarFormulario(form, reglas)) valido = false;
            
            // Validar personas diferentes
            if (!validarPersonasDiferentes()) valido = false;
            
            // Validar al menos un bien seleccionado
            const bienesSeleccionados = document.querySelectorAll('.bien-checkbox:checked').length;
            if (bienesSeleccionados === 0) {
                bienesContainer.style.borderColor = '#dc3545';
                bienesContainer.style.boxShadow = '0 0 0 0.2rem rgba(220,53,69,.25)';
                
                // Agregar mensaje de error si no existe
                let errorBienes = bienesContainer.parentElement.querySelector('.invalid-feedback');
                if (!errorBienes) {
                    errorBienes = document.createElement('div');
                    errorBienes.className = 'invalid-feedback';
                    errorBienes.style.display = 'block';
                    bienesContainer.parentElement.appendChild(errorBienes);
                }
                errorBienes.textContent = 'Debe seleccionar al menos un bien para desplazar.';
                errorBienes.style.display = 'block';
                valido = false;
            } else {
                bienesContainer.style.borderColor = '';
                bienesContainer.style.boxShadow = '';
                const errorBienes = bienesContainer.parentElement.querySelector('.invalid-feedback');
                if (errorBienes) errorBienes.style.display = 'none';
            }
            
            if (!valido) e.preventDefault();
        });
    });
    </script>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>
