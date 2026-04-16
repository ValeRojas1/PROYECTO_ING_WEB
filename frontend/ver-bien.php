<?php
/**
 * Ver Bien
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/BienDAO.php';
require_once dirname(__FILE__) . '/../backend/HistorialDAO.php';

verificarSesion();

$bien_id = intval($_GET['id'] ?? 0);
$bienDAO = new BienDAO($conn);
$historialDAO = new HistorialDAO($conn);

$bien = $bienDAO->obtenerPorId($bien_id);
$historial = $historialDAO->obtenerPorBien($bien_id);

if (!$bien) {
    header('HTTP/1.1 404 Not Found');
    exit('Bien no encontrado');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Bien - Control Patrimonial</title>
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
                        <h4 class="mb-0">Detalles del Bien</h4>
                        <div>
                            <a href="editar-bien.php?id=<?php echo $bien['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="bienes.php" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Código Patrimonial</h6>
                                <p class="fs-5"><code><?php echo htmlspecialchars($bien['codigo_patrimonial']); ?></code></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Estado</h6>
                                <p><span class="badge bg-<?php 
                                    echo match($bien['estado']) {
                                        'Disponible' => 'success',
                                        'Asignado' => 'info',
                                        'Dañado' => 'warning',
                                        'Descartado' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>"><?php echo htmlspecialchars($bien['estado']); ?></span></p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6 class="text-muted">Nombre</h6>
                                <p class="fs-6"><?php echo htmlspecialchars($bien['nombre']); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6 class="text-muted">Descripción</h6>
                                <p><?php echo htmlspecialchars($bien['descripcion'] ?? 'Sin descripción'); ?></p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Persona Asignada</h6>
                                <p>
                                    <?php if ($bien['persona_nombre']): ?>
                                        <a href="ver-persona.php?id=<?php echo $bien['persona_id']; ?>">
                                            <?php echo htmlspecialchars($bien['persona_nombre']); ?>
                                        </a>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($bien['area']); ?></small>
                                    <?php else: ?>
                                        <em class="text-muted">Sin asignar</em>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Fecha de Registro</h6>
                                <p><?php echo date(FORMATO_FECHA_MIN, strtotime($bien['fecha_registro'])); ?></p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Historial de Movimientos</h5>
                        <?php if ($historial): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>De (Persona)</th>
                                            <th>Para (Persona)</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($historial as $h): ?>
                                            <tr>
                                                <td><small><?php echo date(FORMATO_FECHA_MIN, strtotime($h['fecha'])); ?></small></td>
                                                <td><?php echo htmlspecialchars($h['persona_anterior'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($h['persona_nueva'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($h['accion']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No hay historial de movimientos</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

