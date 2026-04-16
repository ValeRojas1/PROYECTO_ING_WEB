<?php
/**
 * Historial de Movimientos
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/HistorialDAO.php';

verificarSesion();

$historialDAO = new HistorialDAO($conn);
$exito = '';
$error = '';

// Procesar eliminación (Solo administradores)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['usuario_rol'] === 'admin') {
    if (isset($_POST['eliminar_historial'])) {
        if ($historialDAO->eliminarTodo()) {
            $exito = 'Todo el historial ha sido eliminado exitosamente.';
            registrarActividad('Historial eliminado', 'El administrador vació todo el historial');
        } else {
            $error = 'Ocurrió un error al intentar vaciar el historial.';
        }
    } elseif (isset($_POST['eliminar_multiples']) && !empty($_POST['ids_historial'])) {
        if ($historialDAO->eliminarMultiples($_POST['ids_historial'])) {
            $exito = 'Se eliminaron ' . count($_POST['ids_historial']) . ' registros seleccionados.';
            registrarActividad('Historial eliminado', 'El administrador eliminó un lote de registros del historial');
        } else {
             $error = 'Ocurrió un error al intentar eliminar los registros seleccionados.';
        }
    } elseif (isset($_POST['eliminar_unico']) && !empty($_POST['id_historial'])) {
        if ($historialDAO->eliminar($_POST['id_historial'])) {
            $exito = 'El registro ha sido eliminado exitosamente.';
            registrarActividad('Historial eliminado', 'El administrador eliminó un registro del historial');
        } else {
            $error = 'Ocurrió un error al intentar eliminar el registro.';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = 'Acceso denegado. Solo el administrador puede realizar esta acción.';
}

$historial = $historialDAO->obtenerTodo();

require_once dirname(__FILE__) . '/layout/header.php';
require_once dirname(__FILE__) . '/layout/sidebar.php';
?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Historial de Movimientos (Auditoría)</h1>
                    <div class="d-flex gap-2">
                        <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                        <form method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar TODO el historial permanentemente? Esta acción no se puede deshacer.');">
                            <input type="hidden" name="eliminar_historial" value="1">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i> Vaciar Historial
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <?php if ($exito): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $exito; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                    <form method="POST" id="formEliminarMultiplesHistorial" onsubmit="return confirm('¿Está seguro de eliminar los registros del historial seleccionados?');">
                        <input type="hidden" name="eliminar_multiples" value="1">
                        <div class="px-3 pt-3">
                            <button type="submit" class="btn btn-danger btn-sm" id="btnEliminarVariosHistorial" disabled>
                                <i class="fas fa-trash-alt"></i> Eliminar Seleccionados
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive <?php echo ($_SESSION['usuario_rol'] === 'admin') ? 'mt-2' : ''; ?>">
                        <table class="table table-sm mb-0 table-hover">
                            <thead class="table-light">
                                <tr>
                                    <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                                    <th style="width: 40px;">
                                        <input type="checkbox" class="form-check-input" id="checkAllHistorial">
                                    </th>
                                    <?php endif; ?>
                                    <th><?php echo __('hist_col_date'); ?></th>
                                    <th><?php echo __('hist_col_bien'); ?></th>
                                    <th>Código</th>
                                    <th><?php echo __('hist_col_from'); ?></th>
                                    <th><?php echo __('hist_col_to'); ?></th>
                                    <th>Acción</th>
                                    <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                                    <th><?php echo __('hist_col_options'); ?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($historial): ?>
                                    <?php foreach ($historial as $h): ?>
                                        <tr>
                                            <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                                            <td>
                                                <input type="checkbox" class="form-check-input check-item-historial" name="ids_historial[]" value="<?php echo $h['id']; ?>">
                                            </td>
                                            <?php endif; ?>
                                            <td>
                                                <small class="text-muted"><?php echo date(FORMATO_FECHA_MIN, strtotime($h['fecha'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($h['nombre'] ?? ''); ?></td>
                                            <td><code><?php echo htmlspecialchars($h['codigo_patrimonial'] ?? ''); ?></code></td>
                                            <td><?php echo htmlspecialchars($h['persona_anterior'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($h['persona_nueva'] ?? '-'); ?></td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($h['accion']); ?></span>
                                            </td>
                                            <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                                            <td>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este registro del historial?');">
                                                    <input type="hidden" name="eliminar_unico" value="1">
                                                    <input type="hidden" name="id_historial" value="<?php echo $h['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?php echo ($_SESSION['usuario_rol'] === 'admin') ? '8' : '6'; ?>" class="text-center py-4 text-muted">No hay movimientos registrados en el sistema</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                    </form>
                    <?php endif; ?>
                    
                    <?php if ($historial): ?>
                    <div class="card-footer text-muted border-0 bg-white">
                        <small><i class="fas fa-list"></i> Total de movimientos registrados: <strong><?php echo count($historial); ?></strong></small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAllHistorial');
        if (checkAll) {
            const checkItems = document.querySelectorAll('.check-item-historial');
            const btnEliminar = document.getElementById('btnEliminarVariosHistorial');
            
            function updateDeleteButton() {
                const anyChecked = Array.from(checkItems).some(cb => cb.checked);
                btnEliminar.disabled = !anyChecked;
            }

            checkAll.addEventListener('change', function() {
                checkItems.forEach(cb => {
                    cb.checked = checkAll.checked;
                });
                updateDeleteButton();
            });

            checkItems.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = Array.from(checkItems).every(item => item.checked);
                    checkAll.checked = allChecked;
                    updateDeleteButton();
                });
            });
        }
    });
    </script>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>


