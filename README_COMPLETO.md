{{ VSCODE_README }}

# Sistema de Control Patrimonial

Un sistema web de 3 capas para la gestión y control de bienes patrimoniales institucionales, con funcionalidades de registro, asignación, desplazamiento, auditoría y reportes.

## 📋 Características

### ✅ Funcionalidades Principales

- **Autenticación y Autorización**: Sistema seguro de login multiusuario
- **Registro de Bienes**: Registro individual o importación masiva desde archivos Excel
- **Gestión de Asignaciones**: Asignar bienes a personas
- **Desplazamientos**: Transferencias de bienes entre personas con historial
- **Reportes**: Generación de reportes en PDF sobre asignaciones y movimientos
- **Auditoría**: Historial completo de movimientos y cambios
- **Administración**: Gestión de usuarios, personas y configuración

### 🏗️ Arquitectura

```
Sistema de 3 Capas:
┌─────────────────────┐
│  PRESENTACIÓN (UI)  │  ← HTML, CSS, JavaScript, Bootstrap
│  /frontend/         │
├─────────────────────┤
│ LÓGICA DE NEGOCIO   │  ← PHP (Services, DAOs)
│  /backend/          │
├─────────────────────┤
│ DATOS (BD)          │  ← MySQL / XAMPP
│  control_patrimonial│
└─────────────────────┘
```

## 🛠️ Tecnologías

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla + Bootstrap 5)
- **Base de Datos**: MySQL 5.7+ (XAMPP)
- **Servidor**: Apache (XAMPP)
- **Almacenamiento de Archivos**: CSV, Excel (.xlsx)

## 📦 Instalación

### Requisitos Previos

- XAMPP instalado y ejecutándose
- MySQL corriendo en puerto 3306
- Apache corriendo en puerto 80

### Pasos de Instalación

1. **Copiar el proyecto a htdocs**:
   ```bash
   cp -r PROYECTO_ING_WEB /xampp/htdocs/
   ```

2. **Crear la base de datos**:
   - Abrir phpMyAdmin: `http://localhost/phpmyadmin`
   - Crear nueva base de datos: `control_patrimonial`
   - Importar el script SQL proporcionado o ejecutar:

   ```sql
   CREATE DATABASE control_patrimonial;
   USE control_patrimonial;

   CREATE TABLE usuarios (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(100) NOT NULL,
       email VARCHAR(100) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       rol VARCHAR(50) DEFAULT 'usuario',
       estado TINYINT DEFAULT 1,
       fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE personas (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(100) NOT NULL,
       area VARCHAR(100),
       estado TINYINT DEFAULT 1
   );

   CREATE TABLE bienes (
       id INT AUTO_INCREMENT PRIMARY KEY,
       codigo_patrimonial VARCHAR(50) UNIQUE NOT NULL,
       nombre VARCHAR(100) NOT NULL,
       descripcion TEXT,
       estado VARCHAR(50),
       persona_id INT,
       fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (persona_id) REFERENCES personas(id)
   );

   CREATE TABLE desplazamientos (
       id INT AUTO_INCREMENT PRIMARY KEY,
       numero_desplazamiento VARCHAR(50) UNIQUE NOT NULL,
       persona_origen_id INT,
       persona_destino_id INT,
       motivo TEXT,
       fecha DATE,
       FOREIGN KEY (persona_origen_id) REFERENCES personas(id),
       FOREIGN KEY (persona_destino_id) REFERENCES personas(id)
   );

   CREATE TABLE detalle_desplazamiento (
       id INT AUTO_INCREMENT PRIMARY KEY,
       desplazamiento_id INT,
       bien_id INT,
       FOREIGN KEY (desplazamiento_id) REFERENCES desplazamientos(id),
       FOREIGN KEY (bien_id) REFERENCES bienes(id)
   );

   CREATE TABLE historial (
       id INT AUTO_INCREMENT PRIMARY KEY,
       bien_id INT,
       persona_anterior_id INT,
       persona_nueva_id INT,
       fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       accion VARCHAR(100),
       FOREIGN KEY (bien_id) REFERENCES bienes(id),
       FOREIGN KEY (persona_anterior_id) REFERENCES personas(id),
       FOREIGN KEY (persona_nueva_id) REFERENCES personas(id)
   );

   -- Datos iniciales
   INSERT INTO personas (nombre, area) VALUES
   ('Juan Perez', 'Sistemas'),
   ('Maria Lopez', 'Administración');

   INSERT INTO usuarios (nombre, email, password) VALUES
   ('Admin', 'admin@demo.com', '$2y$10$YrXt.gXu.zN7ppRhClQzu.VLb.Fw3Qx6Qrp3zKJnJ7g9c1nPEKJue');
   ```

3. **Acceder a la aplicación**:
   - URL: `http://localhost/PROYECTO_ING_WEB/frontend/login.php`
   - Usuario demo: `admin@demo.com`
   - Contraseña: `123456`

## 📁 Estructura del Proyecto

```
PROYECTO_ING_WEB/
├── config/
│   ├── database.php          # Conexión a BD
│   ├── constants.php         # Constantes de la app
│   └── session.php           # Gestión de sesiones
├── backend/
│   ├── UsuarioDAO.php        # DAO usuarios
│   ├── PersonaDAO.php        # DAO personas
│   ├── BienDAO.php           # DAO bienes
│   ├── DesplazamientoDAO.php # DAO desplazamientos
│   ├── HistorialDAO.php      # DAO historial
│   ├── AuthService.php       # Servicio autenticación
│   ├── BienService.php       # Servicio bienes
│   ├── DesplazamientoService.php # Servicio desplazamientos
│   ├── ReporteService.php    # Servicio reportes
│   ├── GeneradorPDF.php      # Generador reportes PDF
│   ├── Utilidades.php        # Funciones utilitarias
│   └── api_bienes.php        # API REST (AJAX)
├── frontend/
│   ├── login.php             # Página de login
│   ├── dashboard.php         # Panel principal
│   ├── bienes.php            # Listado de bienes
│   ├── registrar-bien.php    # Registrar bien individual
│   ├── importar-excel.php    # Importar bienes masivamente
│   ├── nuevo-desplazamiento.php  # Crear desplazamiento
│   ├── desplazamientos.php   # Listado desplazamientos
│   ├── reportes.php          # Generador reportes
│   ├── historial.php         # Auditoría de movimientos
│   ├── usuarios.php          # Gestión usuarios (admin)
│   ├── personas.php          # Gestión personas (admin)
│   └── [más vistas...]
├── assets/
│   ├── css/
│   │   └── estilos.css       # Estilos generales
│   └── js/
│       └── [scripts JS]
├── uploads/                  # Archivos subidos
├── reports/                  # Reportes generados
├── vendor/                   # Librerías externas
└── README.md
```

## 🚀 Uso

### 1. Acceder al Sistema
```
URL: http://localhost/PROYECTO_ING_WEB/frontend/login.php
Usuario: admin@demo.com
Contraseña: 123456
```

### 2. Registrar Bienes
- **Individual**: Ir a "Registrar Bien"
- **Masivo**: Ir a "Importar Excel" (plantilla CSV)

### 3. Realizar Desplazamientos
- Ir a "Nuevo Desplazamiento"
- Seleccionar persona origen y destino
- Seleccionar bienes a trasladar
- Guardar

### 4. Generar Reportes
- Ir a "Reportes"
- Seleccionar tipo de reporte
- Descargar PDF

### 5. Consultar Historial
- Ir a "Historial" para ver todos los movimientos

## 🔐 Seguridad

- Contraseñas hasheadas con bcrypt
- Sesiones con timeout automático
- Control de acceso por roles (Admin, Supervisor, Usuario)
- Validación de datos de entrada
- CSRF protection (recomendado agregar tokens en producción)
- Auditoría completa de movimientos

## 👥 Roles y Permisos

| Rol | Permisos |
|-----|----------|
| **Admin** | Acceso total, crear usuarios, gestionar personas |
| **Supervisor** | Ver reportes, crear desplazamientos |
| **Usuario** | Ver bienes, crear registros básicos |

## 📊 Reportes Disponibles

1. **Reporte de Bienes por Persona**: Listado de todos los bienes asignados
2. **Reporte de Desplazamientos**: Movimientos entre personas con fechas
3. **Estadísticas**: Totales y distribución de bienes

## 🔄 Flujo de Trabajo Típico

```
1. Admin crea personas del sistema
2. Admin crea usuarios con acceso
3. Usuario registra/importa bienes
4. Usuario realiza desplazamientos
5. Sistema registra automáticamente en historial
6. Admin genera reportes para auditoría
```

## 🐛 Troubleshooting

### Error de conexión a BD
- Verificar que MySQL esté corriendo
- Revisar credenciales en `config/database.php`
- Asegurar que la BD existe

### Archivos no funcionan
- Verificar permisos de `/uploads/` y `/reports/`
- Asegurar que Apache puede escribir en esas carpetas

### Errores de sesión
- Limpiar cookies del navegador
- Verificar que session.save_path es escribible

## 📝 Mantenimiento

### Backup de BD
```sql
mysqldump -u root control_patrimonial > backup.sql
```

### Restaurar BD
```sql
mysql -u root control_patrimonial < backup.sql
```

## 🆘 Soporte

Para problemas o mejoras, contactar al administrador del sistema.

## 📄 Licencia

Sistema desarrollado para propósitos educativos e institucionales.

---

**Versión**: 1.0  
**Última actualización**: 2024  
**Desarrollador**: Sistema de Control Patrimonial
