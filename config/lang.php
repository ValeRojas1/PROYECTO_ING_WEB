<?php
// Diccionario de Traducciones
global $diccionario;

$diccionario = [
    'es' => [
        // GENERAL
        'btn_save' => 'Guardar',
        'btn_cancel' => 'Cancelar',
        'btn_edit' => 'Editar',
        'btn_view' => 'Ver',
        'btn_delete' => 'Eliminar',
        'btn_back' => 'Volver',
        'btn_add_new' => 'Agregar Nuevo',
        // NAVBAR / HEADER 
        'nav_logout' => 'Salir',
        'nav_brand' => 'Control Patrimonial',
        // SIDEBAR
        'menu_dashboard' => 'Dashboard',
        'menu_bienes' => 'Bienes',
        'menu_personas' => 'Personas',
        'menu_desplazamientos' => 'Desplazamientos',
        'menu_reportes' => 'Reportes',
        'menu_historial' => 'Historial',
        'menu_users' => 'Usuarios',
        // DASHBOARD
        'dash_total_bienes' => 'Total Bienes',
        'dash_bienes_asignados' => 'Bienes Asignados',
        'dash_bienes_disponibles' => 'Bienes Disponibles',
        'dash_bienes_danados' => 'Dañados/Descartados',
        'dash_recent_activity' => 'Actividad Reciente',
        'dash_view_all' => 'Ver todo',
        // BIENES
        'bienes_title' => 'Gestión de Bienes',
        'bienes_btn_new' => 'Nuevo Bien',
        'bienes_btn_import' => 'Importar Excel',
        'bienes_btn_delete_selected' => 'Eliminar Seleccionados',
        'bienes_empty' => 'No hay bienes registrados',
        'bienes_col_code' => 'Código',
        'bienes_col_name' => 'Nombre',
        'bienes_col_desc' => 'Descripción',
        'bienes_col_status' => 'Estado',
        'bienes_col_assigned' => 'Persona Asignada',
        'bienes_col_date' => 'Fecha Registro',
        'bienes_col_actions' => 'Acciones',
        // PERSONAS
        'personas_title' => 'Gestión de Personas',
        'personas_btn_new' => 'Nueva Persona',
        'personas_col_name' => 'Nombre',
        'personas_col_area' => 'Área',
        'personas_col_status' => 'Estado',
        'personas_col_bienes' => 'Bienes Asignados',
        // DESPLAZAMIENTOS
        'desp_title' => 'Desplazamientos',
        'desp_btn_new' => 'Nuevo Desplazamiento',
        'desp_col_id' => 'ID',
        'desp_col_from' => 'Origen',
        'desp_col_to' => 'Destino',
        'desp_col_reason' => 'Motivo',
        'desp_col_date' => 'Fecha',
        'desp_col_items' => 'Cant. Bienes',
        // REPORTES
        'rep_title' => 'Reportes del Sistema',
        'rep_bienes_persona' => 'Reporte de Bienes por Persona',
        'rep_bienes_desc' => 'Genera un reporte con todos los bienes asignados a cada persona.',
        'rep_btn_pdf' => 'Generar PDF',
        'rep_filter_person' => 'Filtrar por Persona (opcional)',
        'rep_desp' => 'Reporte de Desplazamientos',
        'rep_desp_desc' => 'Genera un reporte con todos los desplazamientos realizados.',
        'rep_date_from' => 'Desde',
        'rep_date_to' => 'Hasta',
        // HISTORIAL
        'hist_title' => 'Historial de Movimientos',
        'hist_btn_clear' => 'Vaciar Historial',
        'hist_col_date' => 'Fecha',
        'hist_col_bien' => 'Bien',
        'hist_col_code' => 'Código',
        'hist_col_from' => 'De (Persona)',
        'hist_col_to' => 'Para (Persona)',
        'hist_col_action' => 'Acción',
        'hist_col_options' => 'Opciones',
        'hist_total' => 'Total de movimientos registrados:',
        // USUARIOS
        'users_title' => 'Gestión de Usuarios',
        'users_btn_new' => 'Nuevo Usuario',
        'users_col_name' => 'Nombre',
        'users_col_email' => 'Email',
        'users_col_role' => 'Rol',
        'users_col_date' => 'Fecha Creación'
    ],
    'en' => [
        // GENERAL
        'btn_save' => 'Save',
        'btn_cancel' => 'Cancel',
        'btn_edit' => 'Edit',
        'btn_view' => 'View',
        'btn_delete' => 'Delete',
        'btn_back' => 'Go Back',
        'btn_add_new' => 'Add New',
        // NAVBAR / HEADER 
        'nav_logout' => 'Logout',
        'nav_brand' => 'Asset Management',
        // SIDEBAR
        'menu_dashboard' => 'Dashboard',
        'menu_bienes' => 'Assets',
        'menu_personas' => 'Personnel',
        'menu_desplazamientos' => 'Movements',
        'menu_reportes' => 'Reports',
        'menu_historial' => 'Audit History',
        'menu_users' => 'Users',
        // DASHBOARD
        'dash_total_bienes' => 'Total Assets',
        'dash_bienes_asignados' => 'Assigned Assets',
        'dash_bienes_disponibles' => 'Available Assets',
        'dash_bienes_danados' => 'Damaged/Discarded',
        'dash_recent_activity' => 'Recent Activity',
        'dash_view_all' => 'View all',
        // BIENES
        'bienes_title' => 'Asset Management',
        'bienes_btn_new' => 'New Asset',
        'bienes_btn_import' => 'Import Excel',
        'bienes_btn_delete_selected' => 'Delete Selected',
        'bienes_empty' => 'No assets registered',
        'bienes_col_code' => 'Code',
        'bienes_col_name' => 'Name',
        'bienes_col_desc' => 'Description',
        'bienes_col_status' => 'Status',
        'bienes_col_assigned' => 'Assigned Person',
        'bienes_col_date' => 'Reg. Date',
        'bienes_col_actions' => 'Actions',
        // PERSONAS
        'personas_title' => 'Personnel Management',
        'personas_btn_new' => 'New Person',
        'personas_col_name' => 'Name',
        'personas_col_area' => 'Area/Dept.',
        'personas_col_status' => 'Status',
        'personas_col_bienes' => 'Assigned Assets',
        // DESPLAZAMIENTOS
        'desp_title' => 'Movements (Displacements)',
        'desp_btn_new' => 'New Movement',
        'desp_col_id' => 'ID',
        'desp_col_from' => 'Source',
        'desp_col_to' => 'Destination',
        'desp_col_reason' => 'Reason',
        'desp_col_date' => 'Date',
        'desp_col_items' => 'Asset Qty',
        // REPORTES
        'rep_title' => 'System Reports',
        'rep_bienes_persona' => 'Assets per Person Report',
        'rep_bienes_desc' => 'Generates a PDF report containing all assets assigned to each person.',
        'rep_btn_pdf' => 'Generate PDF',
        'rep_filter_person' => 'Filter by Person (optional)',
        'rep_desp' => 'Movements Report',
        'rep_desp_desc' => 'Generates a PDF report containing all system movements filtering by date.',
        'rep_date_from' => 'From Date',
        'rep_date_to' => 'To Date',
        // HISTORIAL
        'hist_title' => 'Audit History',
        'hist_btn_clear' => 'Clear History',
        'hist_col_date' => 'Date',
        'hist_col_bien' => 'Asset',
        'hist_col_code' => 'Code',
        'hist_col_from' => 'From (Person)',
        'hist_col_to' => 'To (Person)',
        'hist_col_action' => 'Action',
        'hist_col_options' => 'Options',
        'hist_total' => 'Total audit logs recorded:',
        // USUARIOS
        'users_title' => 'User Management',
        'users_btn_new' => 'New System User',
        'users_col_name' => 'Name',
        'users_col_email' => 'Email Address',
        'users_col_role' => 'Access Role',
        'users_col_date' => 'Creation Date'
    ]
];

/**
 * Traduce una etiqueta según el idioma seleccionado
 */
if (!function_exists('__')) {
    function __($clave) {
        global $diccionario;
        $idiomaActual = $_SESSION['lang'] ?? 'es';
        
        if (isset($diccionario[$idiomaActual][$clave])) {
            return $diccionario[$idiomaActual][$clave];
        }
        
        // Fallback a español si no existe
        if (isset($diccionario['es'][$clave])) {
            return $diccionario['es'][$clave];
        }
        
        // Si no existe, devolver la clave tal cual
        return $clave;
    }
}
?>
