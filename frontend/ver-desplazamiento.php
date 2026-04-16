<?php
/**
 * Ver Desplazamiento
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/DesplazamientoDAO.php';

verificarSesion();

$despl_id = intval($_GET['id'] ?? 0);
$desplazamientoDAO = new DesplazamientoDAO($conn);

$desplazamiento = $desplazamientoDAO->obtenerPorId($despl_id);
$detalles = $desplazamientoDAO->obtenerDetalles($despl_id);

if (!$desplazamiento) {
    header('HTTP/1.1 404 Not Found');
    exit('Desplazamiento no encontrado');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Desplazamiento - Control Patrimonial</title>
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
                        <h4 class="mb-0">Detalles del Desplazamiento</h4>
                        <a href="desplazamientos.php" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Número de Desplazamiento</h6>
                                <p class="fs-5"><code><?php echo htmlspecialchars($desplazamiento['numero_desplazamiento']); ?></code></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Fecha</h6>
                                <p><?php echo date(FORMATO_FECHA, strtotime($desplazamiento['fecha'])); ?></p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Persona Origen</h6>
                                <p><?php echo htmlspecialchars($desplazamiento['persona_origen']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Persona Destino</h6>
                                <p><?php echo htmlspecialchars($desplazamiento['persona_destino']); ?></p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-muted">Motivo</h6>
                            <p><?php echo htmlspecialchars($desplazamiento['motivo']); ?></p>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Bienes Desplazados</h5>
                        <?php if ($detalles): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detalles as $bien): ?>
                                            <tr>
                                                <td><code><?php echo htmlspecialchars($bien['codigo_patrimonial']); ?></code></td>
                                                <td><?php echo htmlspecialchars($bien['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($bien['descripcion']); ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo htmlspecialchars($bien['estado']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No hay bienes en este desplazamiento</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

