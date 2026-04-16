<?php
/**
 * Gestión de Personas (Admin)
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';

verificarSesion();

$personaDAO = new PersonaDAO($conn);
$todas_personas = $conn->query("SELECT * FROM personas ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

require_once dirname(__FILE__) . '/layout/header.php';
require_once dirname(__FILE__) . '/layout/sidebar.php';
?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Gestión de Personas</h1>
                    <div>
                        <?php if ($_SESSION['usuario_rol'] !== 'usuario'): ?>
                        <a href="crear-persona.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Persona
                        </a>
                        <?php endif; ?>
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
                                    <th><?php echo __('personas_col_name'); ?></th>
                                    <th>Área</th>
                                    <th><?php echo __('personas_col_status'); ?></th>
                                    <th><?php echo __('personas_col_bienes'); ?></th>
                                    <th><?php echo __('bienes_col_actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($todas_personas as $persona): ?>
                                    <?php $cantidad_bienes = $personaDAO->contarBienes($persona['id']); ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($persona['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($persona['area'] ?? ''); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $persona['estado'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $persona['estado'] ? 'Activa' : 'Inactiva'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $cantidad_bienes; ?></td>
                                        <td>
                                            <?php if ($_SESSION['usuario_rol'] !== 'usuario'): ?>
                                            <a href="editar-persona.php?id=<?php echo $persona['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                            <a href="ver-persona.php?id=<?php echo $persona['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>

