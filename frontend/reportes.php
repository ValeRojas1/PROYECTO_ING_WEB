<?php
/**
 * Reportes PDF
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/ReporteService.php';
require_once dirname(__FILE__) . '/../backend/GeneradorPDF.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();

$reporteService = new ReporteService($conn);
$generador = new GeneradorPDF();

// Procesar descarga de PDF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_reporte = $_POST['tipo_reporte'] ?? null;
    
    switch ($tipo_reporte) {
        case 'bienes_por_persona':
            $persona_id = !empty($_POST['persona_id']) ? intval($_POST['persona_id']) : null;
            $datos = $reporteService->generarReporteBienesPorPersona($persona_id);
            $html = $generador->generarReporteBienesPorPersona($datos);
            
            // Generar PDF
            $filename = 'Reporte_Bienes_' . date('YmdHis') . '.html';
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $html;
            exit;
            break;
            
        case 'desplazamientos':
            $fecha_inicio = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
            $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
            
            // ValidaciÃ³n de coherencia de rango de fechas
            if ($fecha_inicio && $fecha_fin && $fecha_inicio > $fecha_fin) {
                // No generar reporte, mostrar error vÃ­a GET
                header('Location: reportes.php?error=' . urlencode('La fecha "Desde" no puede ser posterior a la fecha "Hasta".'));
                exit;
            }
            
            $datos = $reporteService->generarReporteDesplazamientos($fecha_inicio, $fecha_fin);
            $html = $generador->generarReporteDesplazamientos($datos);
            
            $filename = 'Reporte_Desplazamientos_' . date('YmdHis') . '.html';
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $html;
            exit;
            break;
    }
}

// Obtener personas para filtros
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';
$personaDAO = new PersonaDAO($conn);
$personas = $personaDAO->obtenerTodos();
$stats = $reporteService->generarReporteEstadisticas();

// Capturar error de la URL si existe
$error_reporte = '';
if (isset($_GET['error'])) {
    $error_reporte = htmlspecialchars($_GET['error']);
}

require_once dirname(__FILE__) . '/layout/header.php';
require_once dirname(__FILE__) . '/layout/sidebar.php';
?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Reportes del Sistema</h1>
                
                <?php if ($error_reporte): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_reporte; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- EstadÃ­sticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total de Bienes</h5>
                                <h2><?php echo $stats['total']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Asignados</h5>
                                <h2><?php echo $stats['por_estado']['Asignado'] ?? 0; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Disponibles</h5>
                                <h2><?php echo $stats['por_estado']['Disponible'] ?? 0; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">DaÃ±ados/Descartados</h5>
                                <h2><?php echo ($stats['por_estado']['DaÃ±ado'] ?? 0) + ($stats['por_estado']['Descartado'] ?? 0); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reportes -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><?php echo __('rep_bienes_persona'); ?></h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted"><?php echo __('rep_bienes_desc'); ?></p>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="persona_id" class="form-label"><?php echo __('rep_filter_person'); ?></label>
                                        <select class="form-select" id="persona_id" name="persona_id">
                                            <option value="">-- Todas las Personas --</option>
                                            <?php foreach ($personas as $p): ?>
                                                <option value="<?php echo $p['id']; ?>">
                                                    <?php echo htmlspecialchars($p['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <input type="hidden" name="tipo_reporte" value="bienes_por_persona">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-file-pdf"></i> Generar PDF
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><?php echo __('rep_desp'); ?></h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted"><?php echo __('rep_desp_desc'); ?></p>
                                <form method="POST" id="formReporteDesplazamientos" novalidate>
                                    <div class="mb-3">
                                        <label for="fecha_inicio" class="form-label"><?php echo __('rep_date_from'); ?></label>
                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                    </div>
                                    <div class="mb-3">
                                        <label for="fecha_fin" class="form-label"><?php echo __('rep_date_to'); ?></label>
                                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                    </div>
                                    <input type="hidden" name="tipo_reporte" value="desplazamientos">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-file-pdf"></i> Generar PDF
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- DistribuciÃ³n por Persona -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">DistribuciÃ³n de Bienes por Persona</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Persona</th>
                                            <th class="text-end">Cantidad de Bienes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats['por_persona'] as $persona => $cantidad): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($persona); ?></td>
                                                <td class="text-end">
                                                    <span class="badge bg-primary"><?php echo $cantidad; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/validaciones.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const formDesp = document.getElementById('formReporteDesplazamientos');
        const fechaInicio = formDesp.querySelector('[name="fecha_inicio"]');
        const fechaFin = formDesp.querySelector('[name="fecha_fin"]');
        
        function validarRangoFechas() {
            if (fechaInicio.value && fechaFin.value) {
                if (!Validador.rangoFechasValido(fechaInicio.value, fechaFin.value)) {
                    Validador.marcarInvalido(fechaFin, 'La fecha "Hasta" no puede ser anterior a "Desde".');
                    return false;
                } else {
                    Validador.marcarValido(fechaFin);
                }
            } else {
                Validador.limpiar(fechaFin);
            }
            return true;
        }
        
        fechaInicio.addEventListener('change', validarRangoFechas);
        fechaFin.addEventListener('change', validarRangoFechas);
        
        formDesp.addEventListener('submit', function(e) {
            if (!validarRangoFechas()) {
                e.preventDefault();
            }
        });
    });
    </script>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>

