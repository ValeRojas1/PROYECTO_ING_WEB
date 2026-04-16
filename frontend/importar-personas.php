<?php
/**
 * Importar Personas desde CSV
 */

require_once dirname(__FILE__) . '/../config/database.php';
require_once dirname(__FILE__) . '/../config/constants.php';
require_once dirname(__FILE__) . '/../config/session.php';
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';
require_once dirname(__FILE__) . '/../backend/Utilidades.php';

verificarSesion();

// Solo admin y supervisor pueden importar personas
if ($_SESSION['usuario_rol'] === 'usuario') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso denegado.');
}

$personaDAO = new PersonaDAO($conn);
$error = '';
$exito = '';
$detalles = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $error = 'Error al subir el archivo';
    } elseif ($archivo['size'] > MAX_UPLOAD_SIZE) {
        $error = 'El archivo es demasiado grande (máximo 5MB)';
    } else {
        $info = pathinfo($archivo['name']);
        $ext = strtolower($info['extension']);
        
        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            $error = '¡Error! Formato no admitido. Solo se acepta formato .csv';
        } else {
            if (!is_dir(UPLOADS_PATH)) {
                mkdir(UPLOADS_PATH, 0775, true);
            }
            
            $temp_file = UPLOADS_PATH . 'temp_personas_' . time() . '.' . $ext;
            
            if (move_uploaded_file($archivo['tmp_name'], $temp_file)) {
                // Procesar archivo CSV
                $personas_creadas = 0;
                $errores = [];
                
                if (($fichero = fopen($temp_file, 'r')) !== false) {
                    // Auto-detectar delimitador
                    $primera_linea = fgets($fichero);
                    if ($primera_linea === false) {
                        fclose($fichero);
                        $error = 'El archivo está vacío';
                    } else {
                        $delimitador = (strpos($primera_linea, ';') !== false) ? ';' : ',';
                        rewind($fichero);
                        
                        // Saltar BOM si existe
                        $bom = fread($fichero, 3);
                        if ($bom !== "\xEF\xBB\xBF") {
                            rewind($fichero);
                        }
                        
                        $contador = 0;
                        while (($datos = fgetcsv($fichero, 1000, $delimitador)) !== false) {
                            $contador++;
                            
                            // Saltar header
                            if ($contador === 1) continue;
                            
                            // Validar estructura mínima (al menos 1 columna: nombre)
                            if (count($datos) < 1 || empty(trim($datos[0]))) {
                                $errores[] = "Fila {$contador}: Nombre vacío o fila incompleta";
                                continue;
                            }
                            
                            $nombre = trim($datos[0]);
                            $area = isset($datos[1]) ? trim($datos[1]) : '';
                            
                            // Validar nombre
                            if (strlen($nombre) < 2 || strlen($nombre) > 100) {
                                $errores[] = "Fila {$contador}: El nombre '{$nombre}' debe tener entre 2 y 100 caracteres";
                                continue;
                            }
                            
                            if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/', $nombre)) {
                                $errores[] = "Fila {$contador}: El nombre '{$nombre}' contiene caracteres no permitidos";
                                continue;
                            }
                            
                            // Validar área
                            if (strlen($area) > 100) {
                                $errores[] = "Fila {$contador}: El área excede 100 caracteres";
                                continue;
                            }
                            
                            // Crear persona
                            if ($personaDAO->crear($nombre, $area)) {
                                $personas_creadas++;
                            } else {
                                $errores[] = "Fila {$contador}: Error al crear la persona '{$nombre}'";
                            }
                        }
                        fclose($fichero);
                        
                        if ($personas_creadas > 0) {
                            $exito = "Se importaron {$personas_creadas} personas correctamente.";
                            $detalles = [
                                'personas_creadas' => $personas_creadas,
                                'errores' => $errores
                            ];
                            registrarActividad('Importación Personas', "Personas: {$personas_creadas}");
                        } else {
                            $error = 'No se pudo importar ninguna persona. Revise el formato del archivo.';
                            $detalles = ['errores' => $errores];
                        }
                    }
                }
                
                // Eliminar archivo temporal
                if (file_exists($temp_file)) {
                    unlink($temp_file);
                }
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
                        <h4 class="mb-0"><i class="fas fa-file-import me-2"></i>Importar Personas desde CSV</h4>
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
                                    <a href="personas.php" class="btn btn-success px-4 py-2">
                                        <i class="fas fa-users me-1"></i> Ir al Catálogo de Personas
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($detalles && !empty($detalles['errores'])): ?>
                            <div class="alert alert-warning mt-3">
                                <strong><i class="fas fa-exclamation-triangle"></i> Advertencias durante la importación:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($detalles['errores'] as $err): ?>
                                        <li><small><?php echo htmlspecialchars($err); ?></small></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <h5 class="mb-3">¿Para qué sirve esta opción?</h5>
                        <p class="text-muted">
                            La importación masiva te ahorrará mucho tiempo. En lugar de registrar personas una por una, 
                            puedes subir un archivo CSV con muchos registros y el sistema los procesará todos juntos. 
                            Una vez finalizada la carga, podrás gestionar todas estas personas directamente desde el módulo 
                            <strong>Personas</strong>.
                        </p>

                        <div class="alert alert-info">
                            <strong>Estructura requerida de las columnas de tu CSV (en este orden):</strong><br>
                            1. <code>Nombre</code> - Nombre completo de la persona (obligatorio)<br>
                            2. <code>Área</code> - Área o departamento (opcional)
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
                                <a href="personas.php" class="btn btn-secondary">
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
        const data = '\uFEFFNombre;Área\nJuan Pérez;Sistemas\nMaría López;Administración\nCarlos García;Contabilidad';
        const blob = new Blob([data], { type: 'text/csv;charset=utf-8' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Plantilla_Personas.csv';
        a.click();
    }
    </script>
    
<?php require_once dirname(__FILE__) . '/layout/footer.php'; ?>
