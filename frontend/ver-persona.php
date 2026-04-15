<?php
/**
 * Ver Persona (corregido)
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';

verificarSesion();

$persona_id = intval($_GET['id'] ?? 0);
$personaDAO = new PersonaDAO($conn);

$persona = $personaDAO->obtenerPorId($persona_id);
$bienes = $personaDAO->obtenerBienes($persona_id);

if (!$persona) {
    header('HTTP/1.1 404 Not Found');
    exit('Persona no encontrada');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Persona - Control Patrimonial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1"><i class="fas fa-chart-line"></i> Control Patrimonial</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="mb-0">Detalles de la Persona</h4>
                        <div>
                            <a href="editar-persona.php?id=<?php echo $persona['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="personas.php" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Nombre</h6>
                                <p class="fs-5"><?php echo htmlspecialchars($persona['nombre']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Área</h6>
                                <p><?php echo htmlspecialchars($persona['area'] ?? ''); ?></p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-muted">Estado</h6>
                            <p>
                                <span class="badge bg-<?php echo $persona['estado'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $persona['estado'] ? 'Activa' : 'Inactiva'; ?>
                                </span>
                            </p>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Bienes Asignados</h5>
                        <?php if ($bienes): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bienes as $bien): ?>
                                            <tr>
                                                <td><code><?php echo htmlspecialchars($bien['codigo_patrimonial']); ?></code></td>
                                                <td><?php echo htmlspecialchars($bien['nombre']); ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo htmlspecialchars($bien['estado']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="ver-bien.php?id=<?php echo $bien['id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No tiene bienes asignados</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
