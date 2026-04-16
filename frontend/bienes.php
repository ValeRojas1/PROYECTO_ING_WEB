<?php
/**
 * Listado de Bienes
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/BienDAO.php';
require_once dirname(__FILE__) . '/../backend/BienService.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();

$bienDAO = new BienDAO($conn);
$bienService = new BienService($conn);
$exito = '';
$error = '';

// Procesar eliminación (Solo administradores)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['usuario_rol'] === 'admin') {
    if (isset($_POST['eliminar_bienes']) && !empty($_POST['bienes_ids'])) {
        $resultado = $bienService->eliminarBienes($_POST['bienes_ids']);
        if (isset($resultado['success'])) {
            $exito = "Se eliminaron {$resultado['cantidad']} bienes exitosamente.";
            registrarActividad('Bienes eliminados', "El administrador eliminó {$resultado['cantidad']} bienes en lote");
        } else {
            $error = $resultado['error'] ?? 'Error al eliminar los bienes.';
        }
    } elseif (isset($_POST['eliminar_bien_unico']) && !empty($_POST['id_bien'])) {
        $resultado = $bienService->eliminarBienes([$_POST['id_bien']]);
        if (isset($resultado['success'])) {
            $exito = "El bien fue eliminado exitosamente.";
            registrarActividad('Bien eliminado', "El administrador eliminó el bien ID: " . $_POST['id_bien']);
        } else {
            $error = $resultado['error'] ?? 'Error al eliminar el bien.';
        }
    }
}

$filtro = $_GET['filtro'] ?? null;
$valor = $_GET['valor'] ?? null;

$bienes = $bienDAO->obtenerTodos($filtro, $valor);

require_once dirname(__FILE__) . '/layout/header.php';
require_once dirname(__FILE__) . '/layout/sidebar.php';
?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Bienes</h1>
                    <div>
                        <a href="registrar-bien.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Bien
                        </a>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <?php if ($exito): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($exito); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Filtros -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="d-flex gap-2">
                            <select name="filtro" class="form-select" style="max-width: 200px;">
                                <option value="">Sin filtro</option>
                                <option value="estado" <?php echo $filtro === 'estado' ? 'selected' : ''; ?>>Por Estado</option>
                                <option value="persona" <?php echo $filtro === 'persona' ? 'selected' : ''; ?>>Por Persona</option>
                            </select>
                            <input type="text" name="valor" class="form-control" placeholder="Filtrar..." value="<?php echo htmlspecialchars($valor ?? ''); ?>">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <a href="bienes.php" class="btn btn-secondary">Limpiar</a>
                        </form>
                    </div>
                </div>
                
                <!-- Tabla -->
                <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                <form method="POST" id="formEliminarMultiples" onsubmit="return confirm('¿Está seguro de eliminar los bienes seleccionados? Se borrarán también sus historiales y desplazamientos.');">
                    <input type="hidden" name="eliminar_bienes" value="1">
                    <div class="mb-2">
                        <button type="submit" class="btn btn-danger btn-sm" id="btnEliminarVarios" disabled>
                            <i class="fas fa-trash-alt"></i> Eliminar Seleccionados
                        </button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                                    <th style="width: 40px;">
                                        <input type="checkbox" class="form-check-input" id="checkAll">
                                    </th>
                                    <?php endif; ?>
                                    <th>Código</th>
                                    <th><?php echo __('bienes_col_name'); ?></th>
                                    <th>Descripción</th>
                                    <th><?php echo __('bienes_col_status'); ?></th>
                                    <th><?php echo __('bienes_col_assigned'); ?></th>
                                    <th><?php echo __('bienes_col_date'); ?></th>
                                    <th><?php echo __('bienes_col_actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($bienes): ?>
                                    <?php foreach ($bienes as $bien): ?>
                                        <tr>
                                            <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                                            <td>
                                                <input type="checkbox" class="form-check-input check-item" name="bienes_ids[]" value="<?php echo $bien['id']; ?>">
                                            </td>
                                            <?php endif; ?>
                                            <td><code><?php echo htmlspecialchars($bien['codigo_patrimonial']); ?></code></td>
                                            <td><?php echo htmlspecialchars($bien['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($bien['descripcion'] ?? '', 0, 50)); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo match($bien['estado']) {
                                                        'Disponible' => 'success',
                                                        'Asignado' => 'info',
                                                        'Dañado' => 'warning',
                                                        'Descartado' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                ?>">
                                                    <?php echo htmlspecialchars($bien['estado']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $bien['persona_nombre'] ?? '<em>Sin asignar</em>'; ?>
                                            </td>
                                            <td><?php echo date(FORMATO_FECHA, strtotime($bien['fecha_registro'])); ?></td>
                                            <td class="d-flex gap-1">
                                                <a href="editar-bien.php?id=<?php echo $bien['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="ver-bien.php?id=<?php echo $bien['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este bien?');">
                                                    <input type="hidden" name="eliminar_bien_unico" value="1">
                                                    <input type="hidden" name="id_bien" value="<?php echo $bien['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?php echo ($_SESSION['usuario_rol'] === 'admin') ? '8' : '7'; ?>" class="text-center py-3"><?php echo __('bienes_empty'); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            const checkItems = document.querySelectorAll('.check-item');
            const btnEliminar = document.getElementById('btnEliminarVarios');
            
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


