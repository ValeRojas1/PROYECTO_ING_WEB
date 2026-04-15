<?php
/**
 * Dashboard - PÃ¡gina Principal
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/BienService.php';

verificarSesion();

$bienService = new BienService($conn);
$estadisticas = $bienService->obtenerEstadisticas();

require_once dirname(__FILE__) . '/layout/header.php';
require_once dirname(__FILE__) . '/layout/sidebar.php';
?>
            <h1 class="mb-4"><?php echo __('menu_dashboard'); ?></h1>
            
            <!-- EstadÃ­sticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card card-stat" style="border-top: 3px solid var(--info);">
                        <div class="card-body">
                            <div class="stat-icon text-info">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div class="stat-number"><?php echo $estadisticas['total']; ?></div>
                            <div class="stat-label">Total de Bienes</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card card-stat" style="border-top: 3px solid var(--success);">
                        <div class="card-body">
                            <div class="stat-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-number"><?php echo $estadisticas['asignados']; ?></div>
                            <div class="stat-label"><?php echo __('dash_bienes_asignados'); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card card-stat" style="border-top: 3px solid var(--warning);">
                        <div class="card-body">
                            <div class="stat-icon text-warning">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="stat-number"><?php echo $estadisticas['disponibles']; ?></div>
                            <div class="stat-label"><?php echo __('dash_bienes_disponibles'); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card card-stat" style="border-top: 3px solid var(--danger);">
                        <div class="card-body">
                            <div class="stat-icon text-danger">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="stat-number"><?php echo $estadisticas['daÃ±ados'] + $estadisticas['descartados']; ?></div>
                            <div class="stat-label">DaÃ±ados/Descartados</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Acciones RÃ¡pidas -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Acciones RÃ¡pidas</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="registrar-bien.php" class="btn btn-primary btn-action">
                                    <i class="fas fa-plus"></i> Registrar Nuevo Bien
                                </a>
                                <a href="importar-excel.php" class="btn btn-info btn-action">
                                    <i class="fas fa-file-import"></i> Importar desde Excel
                                </a>
                                <a href="nuevo-desplazamiento.php" class="btn btn-warning btn-action">
                                    <i class="fas fa-exchange-alt"></i> Hacer Desplazamiento
                                </a>
                                <a href="reportes.php" class="btn btn-success btn-action">
                                    <i class="fas fa-file-pdf"></i> Generar Reportes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ãšltima actividad -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">InformaciÃ³n del Sistema</h5>
                            <p><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
                            <p><strong>Rol:</strong> <?php echo strtoupper($_SESSION['usuario_rol']); ?></p>
                            <p><strong>Login:</strong> <?php echo date(FORMATO_FECHA_HORA, $_SESSION['login_time']); ?></p>
                            <p class="text-muted mb-0"><small>Sistema de Control Patrimonial v1.0</small></p>
                        </div>
                    </div>
                </div>
            </div>
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>


