<?php
/**
 * GestiÃ³n de Usuarios (Admin)
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/UsuarioDAO.php';

verificarSesion();
if ($_SESSION['usuario_rol'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso denegado');
}

$usuarioDAO = new UsuarioDAO($conn);
$usuarios = $usuarioDAO->obtenerTodos();

require_once dirname(__FILE__) . '/layout/header.php';
require_once dirname(__FILE__) . '/layout/sidebar.php';
?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>GestiÃ³n de Usuarios</h1>
                    <div>
                        <a href="crear-usuario.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Usuario
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
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Fecha CreaciÃ³n</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $usuario['rol'] === 'admin' ? 'danger' : 'info'; ?>">
                                                <?php echo strtoupper($usuario['rol']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $usuario['estado'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $usuario['estado'] ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date(FORMATO_FECHA, strtotime($usuario['fecha_creacion'])); ?></td>
                                        <td>
                                            <a href="editar-usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
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

