<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificador de Instalación - Control Patrimonial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        .check-item {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .check-item:last-child {
            border-bottom: none;
        }
        .check-label {
            font-weight: 500;
        }
        .check-ok {
            color: #48bb78;
            font-size: 24px;
        }
        .check-error {
            color: #f56565;
            font-size: 24px;
        }
        .check-warning {
            color: #ed8936;
            font-size: 24px;
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        .header h1 {
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-ok {
            background: #c6f6d5;
            color: #22543d;
        }
        .status-error {
            background: #fed7d7;
            color: #742a2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Verificador de Instalación</h1>
            <p>Sistema de Control Patrimonial</p>
        </div>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4">Verificando requisitos...</h5>
                
                <?php
                    $verificaciones = [];
                    $todo_ok = true;
                    
                    // 1. PHP Version
                    $php_ok = version_compare(PHP_VERSION, '7.4.0', '>=');
                    $verificaciones[] = [
                        'nombre' => 'Versión PHP',
                        'requerido' => '7.4+',
                        'actual' => PHP_VERSION,
                        'ok' => $php_ok
                    ];
                    if (!$php_ok) $todo_ok = false;
                    
                    // 2. Extensión MySQLi
                    $mysqli_ok = extension_loaded('mysqli');
                    $verificaciones[] = [
                        'nombre' => 'Extensión MySQLi',
                        'requerido' => 'Habilitada',
                        'actual' => $mysqli_ok ? 'Habilitada' : 'No habilitada',
                        'ok' => $mysqli_ok
                    ];
                    if (!$mysqli_ok) $todo_ok = false;
                    
                    // 3. Extensión cURL
                    $curl_ok = extension_loaded('curl');
                    $verificaciones[] = [
                        'nombre' => 'Extensión cURL',
                        'requerido' => 'Habilitada',
                        'actual' => $curl_ok ? 'Habilitada' : 'No habilitada',
                        'ok' => $curl_ok
                    ];
                    
                    // 4. Permisos de carpetas
                    $permisos_ok = true;
                    $carpetas = ['uploads', 'reports', 'config'];
                    $carpetas_permisos = [];
                    
                    foreach ($carpetas as $carpeta) {
                        $existe = is_dir($carpeta);
                        $escribible = is_writable($carpeta);
                        $carpetas_permisos[] = [
                            'carpeta' => $carpeta,
                            'existe' => $existe,
                            'escribible' => $escribible
                        ];
                        if (!$existe || !$escribible) {
                            $permisos_ok = false;
                        }
                    }
                    
                    $verificaciones[] = [
                        'nombre' => 'Permisos de carpetas',
                        'requerido' => 'Escritura (755)',
                        'actual' => $permisos_ok ? 'OK' : 'Permisos insuficientes',
                        'ok' => $permisos_ok
                    ];
                    if (!$permisos_ok) $todo_ok = false;
                    
                    // 5. Conexión a BD
                    $bd_ok = false;
                    $bd_mensaje = '';
                    
                    try {
                        require_once 'config/database.php';
                        $bd_ok = !($conn->connect_error);
                        $bd_mensaje = $bd_ok ? 'Conexión exitosa' : $conn->connect_error;
                    } catch (Exception $e) {
                        $bd_mensaje = 'No se puede conectar';
                    }
                    
                    $verificaciones[] = [
                        'nombre' => 'Conexión MySQL',
                        'requerido' => 'control_patrimonial',
                        'actual' => $bd_ok ? 'Conectado' : 'Error',
                        'ok' => $bd_ok,
                        'detalle' => $bd_mensaje
                    ];
                    if (!$bd_ok) $todo_ok = false;
                    
                    // 6. Tablas de BD
                    $tablas_ok = false;
                    if ($bd_ok) {
                        $resultado = $conn->query("SHOW TABLES");
                        $num_tablas = $resultado->num_rows;
                        $tablas_ok = $num_tablas >= 6;
                        
                        $verificaciones[] = [
                            'nombre' => 'Tablas de Base de Datos',
                            'requerido' => '6 tablas',
                            'actual' => $num_tablas . ' tablas encontradas',
                            'ok' => $tablas_ok
                        ];
                        if (!$tablas_ok) $todo_ok = false;
                    }
                    
                    // Mostrar resultados
                    foreach ($verificaciones as $v):
                ?>
                    <div class="check-item">
                        <div class="check-label">
                            <strong><?php echo $v['nombre']; ?></strong>
                            <br>
                            <small class="text-muted">
                                Requerido: <?php echo $v['requerido']; ?> | 
                                Actual: <?php echo $v['actual']; ?>
                            </small>
                            <?php if (isset($v['detalle'])): ?>
                                <br>
                                <small class="text-danger"><?php echo $v['detalle']; ?></small>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if ($v['ok']): ?>
                                <span class="check-ok">✓</span>
                            <?php else: ?>
                                <span class="check-error">✗</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if ($bd_ok && isset($resultado) && $resultado->num_rows > 0): ?>
                    <div class="check-item">
                        <div class="check-label">
                            <strong>Tablas en la BD</strong>
                        </div>
                    </div>
                    <?php
                        $resultado = $conn->query("SHOW TABLES");
                        while ($fila = $resultado->fetch_row()):
                    ?>
                        <div class="check-item" style="padding-left: 30px; background: #f9f9f9;">
                            <div class="check-label">
                                📋 <?php echo htmlspecialchars($fila[0]); ?>
                            </div>
                            <span class="check-ok">✓</span>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Resultado final -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <h5 class="mb-3">Estado General</h5>
                <?php if ($todo_ok): ?>
                    <span class="status-badge status-ok">
                        ✓ SISTEMA LISTO
                    </span>
                    <p class="mt-3 text-success">
                        <strong>¡La instalación está completa!</strong><br>
                        Puedes acceder al sistema aquí:
                    </p>
                    <a href="frontend/login.php" class="btn btn-success btn-lg mt-3">
                        🚀 Ir al Login
                    </a>
                <?php else: ?>
                    <span class="status-badge status-error">
                        ✗ ERRORES ENCONTRADOS
                    </span>
                    <p class="mt-3 text-danger">
                        <strong>Por favor, soluciona los problemas arriba marcados:</strong><br>
                        1. Verifica que MySQL este corriendo<br>
                        2. Revisa los permisos de carpetas<br>
                        3. Ejecuta el script setup.sql<br>
                        4. Recarga esta página
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Información del sistema -->
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Información del Sistema</h6>
                <small class="text-muted">
                    PHP: <?php echo PHP_VERSION; ?><br>
                    SO: <?php echo php_uname(); ?><br>
                    Memoria disponible: <?php echo ini_get('memory_limit'); ?><br>
                    Max upload: <?php echo ini_get('upload_max_filesize'); ?><br>
                    Max post: <?php echo ini_get('post_max_size'); ?>
                </small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
