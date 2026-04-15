<?php
/**
 * Importar Bienes desde Excel
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/BienService.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();

$bienService = new BienService($conn);
$error = '';
$exito = '';
$detalles = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    
    // Validar archivo
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $error = 'Error al subir el archivo';
    } elseif ($archivo['size'] > MAX_UPLOAD_SIZE) {
        $error = 'El archivo es demasiado grande (máximo 5MB)';
    } else {
        // Validar extensión
        $info = pathinfo($archivo['name']);
        $ext = strtolower($info['extension']);
        
        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            $error = '¡Error! Formato de archivo no admitido. Para evitar datos rotos, el sistema solo acepta estrictamente formato .csv';
        } else {
            // Crear directorio si no existe
            if (!is_dir(UPLOADS_PATH)) {
                mkdir(UPLOADS_PATH, 0775, true);
            }
            
            // Guardar archivo temporalmente
            $temp_file = UPLOADS_PATH . 'temp_' . time() . '.' . $ext;
            
            if (move_uploaded_file($archivo['tmp_name'], $temp_file)) {
                // Procesar archivo
                $resultado = $bienService->importarExcel($temp_file);
                
                if ($resultado['success'] ?? false) {
                    $exito = "Se importaron {$resultado['bienes_creados']} bienes correctamente.";
                    $detalles = $resultado;
                    registrarActividad('Importación Excel', "Bienes: {$resultado['bienes_creados']}");
                } else {
                    $error = $resultado['error'] ?? 'Error al procesar archivo';
                }
                
                // Eliminar archivo temporal
                unlink($temp_file);
            } else {
                $error = 'Error al guardar el archivo';
            }
        }
    }
}

require_once dirname(__FILE__) . '/layout/header.php';
require_once dirname(__FILE__) . '/layout/sidebar.php';
?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Importar Bienes desde Excel</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($exito): ?>
                            <div class="alert alert-success text-center py-4 shadow-sm" style="border-radius: 10px;">
                                <i class="fas fa-check-circle mb-2" style="font-size: 3.5rem; color: #48bb78;"></i>
                                <h4>¡Importación Exitosa!</h4>
                                <p class="mb-0 fs-5"><?php echo htmlspecialchars($exito); ?></p>
                                <div class="mt-4">
                                    <a href="bienes.php" class="btn btn-success px-4 py-2">
                                        <i class="fas fa-box me-1"></i> Ir al Catálogo de Bienes
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <h5 class="mb-3">¿Para qué sirve esta opción?</h5>
                        <p class="text-muted">
                            La importación te ahorrará mucho tiempo. En lugar de registrar tus equipos uno por uno, puedes subir un archivo con muchos registros y el sistema los procesará todos juntos. Una vez finalizada la carga, podrás gestionar todos estos artículos directamente desde el módulo <strong>Bienes</strong>.
                        </p>

                        <div class="alert alert-info">
                            <strong>Estructura requerida de las columnas de tu Excel (en este orden):</strong><br>
                            1. <code>Código</code> - Código patrimonial único<br>
                            2. <code>Nombre</code> - Nombre del bien<br>
                            3. <code>Descripción</code> - Descripción (opcional)<br>
                            4. <code>Persona</code> - Nombre de la persona para asignarlo (opcional)
                        </div>
                        <p class="mb-0"><small class="text-muted">La primera fila se tratará como encabezado y será ignorada.</small></p>
                        
                        <hr>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="archivo" class="form-label">Seleccionar archivo (.csv) *</label>
                                <input type="file" class="form-control" id="archivo" name="archivo" required 
                                       accept=".csv">
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Importar
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </form>
                        
                        <hr>
                        <p class="text-muted"><small>Descargue <a href="#" onclick="descargarPlantilla(); return false;">esta plantilla</a> como base para su archivo.</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function descargarPlantilla() {
        // Usa BOM (\uFEFF) para forzar UTF-8 en Excel y punto y coma (;) como delimitador
        const data = '\uFEFFCódigo;Nombre;Descripción;Persona\nPAT-2024-001;Computadora Dell;Intel i7 16GB;Juan Perez';
        const blob = new Blob([data], { type: 'text/csv;charset=utf-8' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Plantilla_Bienes.csv';
        a.click();
    }
    </script>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>
