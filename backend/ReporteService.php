<?php
/**
 * Servicio de Reportes (PDF)
 */

require_once dirname(__FILE__) . '/../backend/BienDAO.php';
require_once dirname(__FILE__) . '/../backend/DesplazamientoDAO.php';
require_once dirname(__FILE__) . '/../backend/PersonaDAO.php';

class ReporteService {
    private $bienDAO;
    private $desplazamientoDAO;
    private $personaDAO;
    
    public function __construct($conexion) {
        $this->bienDAO = new BienDAO($conexion);
        $this->desplazamientoDAO = new DesplazamientoDAO($conexion);
        $this->personaDAO = new PersonaDAO($conexion);
    }
    
    /**
     * Generar reporte de bienes por persona
     */
    public function generarReporteBienesPorPersona($persona_id = null) {
        $personas = [];
        
        if ($persona_id) {
            $personas[] = $this->personaDAO->obtenerPorId($persona_id);
        } else {
            $personas = $this->personaDAO->obtenerTodos();
        }
        
        $reporte = [
            'titulo' => 'Reporte de Asignación de Bienes por Persona',
            'fecha' => date('d/m/Y H:i:s'),
            'personas' => []
        ];
        
        foreach ($personas as $persona) {
            $bienes = $this->bienDAO->obtenerPorPersona($persona['id']);
            
            $data_persona = [
                'id' => $persona['id'],
                'nombre' => $persona['nombre'],
                'area' => $persona['area'],
                'total_bienes' => count($bienes),
                'bienes' => $bienes
            ];
            
            $reporte['personas'][] = $data_persona;
        }
        
        return $reporte;
    }
    
    /**
     * Generar reporte de desplazamientos
     */
    public function generarReporteDesplazamientos($fecha_inicio = null, $fecha_fin = null) {
        $desplazamientos = $this->desplazamientoDAO->obtenerTodos();
        
        // Filtrar por fecha si se proporciona
        if ($fecha_inicio && $fecha_fin) {
            $desplazamientos = array_filter(
                $desplazamientos,
                fn($d) => $d['fecha'] >= $fecha_inicio && $d['fecha'] <= $fecha_fin
            );
        }
        
        // Agregar detalles
        foreach ($desplazamientos as &$d) {
            $d['detalles'] = $this->desplazamientoDAO->obtenerDetalles($d['id']);
        }
        
        return [
            'titulo' => 'Reporte de Desplazamientos',
            'fecha' => date('d/m/Y H:i:s'),
            'fecha_inicio' => $fecha_inicio ?? 'Toda la historia',
            'fecha_fin' => $fecha_fin ?? date('Y-m-d'),
            'desplazamientos' => $desplazamientos,
            'total' => count($desplazamientos)
        ];
    }
    
    /**
     * Generar estadísticas
     */
    public function generarReporteEstadisticas() {
        $bienes = $this->bienDAO->obtenerTodos();
        
        $stats = [
            'total' => count($bienes),
            'por_estado' => [],
            'por_persona' => [],
            'fecha_reporte' => date('d/m/Y H:i:s')
        ];
        
        // Contar por estado
        $estados = [ESTADO_DISPONIBLE, ESTADO_ASIGNADO, ESTADO_DAÑADO, ESTADO_DESCARTADO];
        foreach ($estados as $estado) {
            $count = count(array_filter($bienes, fn($b) => $b['estado'] === $estado));
            if ($count > 0) {
                $stats['por_estado'][$estado] = $count;
            }
        }
        
        // Contar por persona
        foreach ($bienes as $bien) {
            if ($bien['persona_id']) {
                $persona = $bien['persona_nombre'];
                if (!isset($stats['por_persona'][$persona])) {
                    $stats['por_persona'][$persona] = 0;
                }
                $stats['por_persona'][$persona]++;
            }
        }
        
        return $stats;
    }
}

?>
