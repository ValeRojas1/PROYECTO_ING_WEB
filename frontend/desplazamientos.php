<?php
/**
 * Listado de Desplazamientos
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/DesplazamientoDAO.php';

verificarSesion();

$desplazamientoDAO = new DesplazamientoDAO($conn);
$desplazamientos = $desplazamientoDAO->obtenerTodos();

require_once dirname(__FILE__) . '/layout/header.php';
require_once dirname(__FILE__) . '/layout/sidebar.php';
?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Desplazamientos de Bienes</h1>
                    <div>
                        <a href="nuevo-desplazamiento.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Desplazamiento
                        </a>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>NÂ° Desplazamiento</th>
                                    <th>Origen</th>
                                    <th>Destino</th>
                                    <th>Motivo</th>
                                    <th>Fecha</th>
                                    <th>Bienes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($desplazamientos): ?>
                                    <?php foreach ($desplazamientos as $d): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars($d['numero_desplazamiento']); ?></code></td>
                                            <td><?php echo htmlspecialchars($d['persona_origen']); ?></td>
                                            <td><?php echo htmlspecialchars($d['persona_destino']); ?></td>
                                            <td><?php echo htmlspecialchars($d['motivo']); ?></td>
                                            <td><?php echo date(FORMATO_FECHA, strtotime($d['fecha'])); ?></td>
                                            <td><span class="badge bg-info"><?php echo $d['cantidad_bienes']; ?></span></td>
                                            <td>
                                                <a href="ver-desplazamiento.php?id=<?php echo $d['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-3">No hay desplazamientos registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>

