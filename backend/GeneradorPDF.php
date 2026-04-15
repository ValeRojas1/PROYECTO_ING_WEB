<?php
/**
 * Generador de Reportes en PDF
 * Usa FPDF o genera HTML que se imprime como PDF
 */

class GeneradorPDF {
    private $titulo;
    private $contenido;
    
    public function __construct($titulo = 'Reporte') {
        $this->titulo = $titulo;
        $this->contenido = '';
    }
    
    /**
     * Generar PDF simple (HTML que se imprime)
     */
    public function generarReporteBienesPorPersona($datos) {
        $html = $this->obtenerEncabezado('Reporte de Asignación de Bienes por Persona');
        
        $html .= '<table class="tabla-pdf">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Persona</th>';
        $html .= '<th>Área</th>';
        $html .= '<th>Total de Bienes</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        $total_general = 0;
        
        foreach ($datos['personas'] as $persona) {
            $html .= '<tr>';
            $html .= '<td><strong>' . htmlspecialchars($persona['nombre']) . '</strong></td>';
            $html .= '<td>' . htmlspecialchars($persona['area']) . '</td>';
            $html .= '<td>' . $persona['total_bienes'] . '</td>';
            $html .= '</tr>';
            
            // Detalles de bienes
            if (!empty($persona['bienes'])) {
                $html .= '<tr class="detalle">';
                $html .= '<td colspan="3">';
                $html .= '<ul>';
                
                foreach ($persona['bienes'] as $bien) {
                    $html .= '<li>' . 
                             htmlspecialchars($bien['codigo_patrimonial']) . ' - ' .
                             htmlspecialchars($bien['nombre']) . 
                             ' (' . htmlspecialchars($bien['estado']) . ')' .
                             '</li>';
                }
                
                $html .= '</ul>';
                $html .= '</td>';
                $html .= '</tr>';
            }
            
            $total_general += $persona['total_bienes'];
        }
        
        $html .= '<tr class="total">';
        $html .= '<td colspan="2"><strong>TOTAL GENERAL</strong></td>';
        $html .= '<td><strong>' . $total_general . '</strong></td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        
        $html .= $this->obtenerPie();
        
        return $html;
    }
    
    /**
     * Generar reporte de desplazamientos
     */
    public function generarReporteDesplazamientos($datos) {
        $html = $this->obtenerEncabezado('Reporte de Desplazamientos');
        
        if ($datos['fecha_inicio'] !== 'Toda la historia') {
            $html .= '<p><strong>Período:</strong> ' . $datos['fecha_inicio'] . ' al ' . $datos['fecha_fin'] . '</p>';
        }
        
        $html .= '<table class="tabla-pdf">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>N° Desplazamiento</th>';
        $html .= '<th>Origen</th>';
        $html .= '<th>Destino</th>';
        $html .= '<th>Motivo</th>';
        $html .= '<th>Fecha</th>';
        $html .= '<th>Cantidad Bienes</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($datos['desplazamientos'] as $despl) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($despl['numero_desplazamiento']) . '</td>';
            $html .= '<td>' . htmlspecialchars($despl['persona_origen']) . '</td>';
            $html .= '<td>' . htmlspecialchars($despl['persona_destino']) . '</td>';
            $html .= '<td>' . htmlspecialchars($despl['motivo']) . '</td>';
            $html .= '<td>' . formatearFecha($despl['fecha']) . '</td>';
            $html .= '<td>' . count($despl['detalles']) . '</td>';
            $html .= '</tr>';
            
            // Detalles de bienes
            if (!empty($despl['detalles'])) {
                $html .= '<tr class="detalle">';
                $html .= '<td colspan="6">';
                $html .= '<ul>';
                
                foreach ($despl['detalles'] as $bien) {
                    $html .= '<li>' . 
                             htmlspecialchars($bien['codigo_patrimonial']) . ' - ' .
                             htmlspecialchars($bien['nombre']) . 
                             '</li>';
                }
                
                $html .= '</ul>';
                $html .= '</td>';
                $html .= '</tr>';
            }
        }
        
        $html .= '<tr class="total">';
        $html .= '<td colspan="5"><strong>TOTAL DESPLAZAMIENTOS</strong></td>';
        $html .= '<td><strong>' . $datos['total'] . '</strong></td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        
        $html .= $this->obtenerPie();
        
        return $html;
    }
    
    /**
     * Obtener encabezado del PDF
     */
    private function obtenerEncabezado($titulo) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>' . htmlspecialchars($titulo) . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    color: #333;
                }
                .encabezado {
                    text-align: center;
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }
                .encabezado h1 {
                    margin: 0;
                    font-size: 18px;
                }
                .encabezado p {
                    margin: 5px 0;
                    font-size: 12px;
                    color: #666;
                }
                .tabla-pdf {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                .tabla-pdf thead {
                    background-color: #f0f0f0;
                    font-weight: bold;
                }
                .tabla-pdf th, .tabla-pdf td {
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: left;
                }
                .tabla-pdf tbody tr:nth-child(odd) {
                    background-color: #f9f9f9;
                }
                .tabla-pdf tr.detalle td {
                    background-color: #f0f0f0;
                    padding: 5px;
                }
                .tabla-pdf tr.detalle ul {
                    margin: 5px 0;
                    padding-left: 20px;
                }
                .tabla-pdf tr.detalle li {
                    margin: 3px 0;
                    font-size: 12px;
                }
                .tabla-pdf tr.total {
                    font-weight: bold;
                    background-color: #f0f0f0;
                }
                .pie {
                    margin-top: 30px;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                }
                p {
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class="encabezado">
                <h1>SISTEMA DE CONTROL PATRIMONIAL</h1>
                <h2>' . htmlspecialchars($titulo) . '</h2>
                <p>Fecha de Generación: ' . date('d/m/Y H:i:s') . '</p>
            </div>
        ';
    }
    
    /**
     * Obtener pie del PDF
     */
    private function obtenerPie() {
        return '
            <div class="pie">
                <p>Este documento fue generado automáticamente por el Sistema de Control Patrimonial</p>
                <p>© ' . date('Y') . ' - Todos los derechos reservados</p>
            </div>
        </body>
        </html>
        ';
    }
}

?>
