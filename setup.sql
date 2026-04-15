-- =========================================
-- BASE DE DATOS: CONTROL PATRIMONIAL
-- Sistema de Gestión de Bienes
-- =========================================

CREATE DATABASE IF NOT EXISTS control_patrimonial;
USE control_patrimonial;

-- =========================================
-- TABLA: USUARIOS
-- =========================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(50) DEFAULT 'usuario',
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_estado (estado)
);

-- =========================================
-- TABLA: PERSONAS
-- =========================================
CREATE TABLE IF NOT EXISTS personas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    area VARCHAR(100),
    estado TINYINT DEFAULT 1,
    INDEX idx_nombre (nombre),
    INDEX idx_estado (estado)
);

-- =========================================
-- TABLA: BIENES
-- =========================================
CREATE TABLE IF NOT EXISTS bienes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_patrimonial VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado VARCHAR(50),
    persona_id INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (persona_id) REFERENCES personas(id),
    INDEX idx_codigo (codigo_patrimonial),
    INDEX idx_estado (estado),
    INDEX idx_persona (persona_id)
);

-- =========================================
-- TABLA: DESPLAZAMIENTOS
-- =========================================
CREATE TABLE IF NOT EXISTS desplazamientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_desplazamiento VARCHAR(50) UNIQUE NOT NULL,
    persona_origen_id INT,
    persona_destino_id INT,
    motivo TEXT,
    fecha DATE,
    FOREIGN KEY (persona_origen_id) REFERENCES personas(id),
    FOREIGN KEY (persona_destino_id) REFERENCES personas(id),
    INDEX idx_numero (numero_desplazamiento),
    INDEX idx_fecha (fecha)
);

-- =========================================
-- TABLA: DETALLE DESPLAZAMIENTO
-- =========================================
CREATE TABLE IF NOT EXISTS detalle_desplazamiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    desplazamiento_id INT,
    bien_id INT,
    FOREIGN KEY (desplazamiento_id) REFERENCES desplazamientos(id) ON DELETE CASCADE,
    FOREIGN KEY (bien_id) REFERENCES bienes(id),
    INDEX idx_desplazamiento (desplazamiento_id),
    INDEX idx_bien (bien_id)
);

-- =========================================
-- TABLA: HISTORIAL (AUDITORÍA)
-- =========================================
CREATE TABLE IF NOT EXISTS historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bien_id INT,
    persona_anterior_id INT,
    persona_nueva_id INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    accion VARCHAR(100),
    FOREIGN KEY (bien_id) REFERENCES bienes(id),
    FOREIGN KEY (persona_anterior_id) REFERENCES personas(id),
    FOREIGN KEY (persona_nueva_id) REFERENCES personas(id),
    INDEX idx_bien (bien_id),
    INDEX idx_fecha (fecha)
);

-- =========================================
-- DATOS INICIALES
-- =========================================

-- Insertar personas de prueba
INSERT INTO personas (nombre, area) VALUES
('Juan Perez', 'Sistemas'),
('Maria Lopez', 'Administración'),
('Carlos Rodríguez', 'Recursos Humanos'),
('Alejandra Silva', 'Finanzas');

-- Insertar usuario admin (contraseña: 123456)
-- Hash bcrypt de "123456": $2y$10$YrXt.gXu.zN7ppRhClQzu.VLb.Fw3Qx6Qrp3zKJnJ7g9c1nPEKJue
INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES
('Administrador', 'admin@demo.com', '$2y$10$YrXt.gXu.zN7ppRhClQzu.VLb.Fw3Qx6Qrp3zKJnJ7g9c1nPEKJue', 'admin', 1),
('Supervisor', 'supervisor@demo.com', '$2y$10$YrXt.gXu.zN7ppRhClQzu.VLb.Fw3Qx6Qrp3zKJnJ7g9c1nPEKJue', 'supervisor', 1),
('Usuario Regular', 'usuario@demo.com', '$2y$10$YrXt.gXu.zN7ppRhClQzu.VLb.Fw3Qx6Qrp3zKJnJ7g9c1nPEKJue', 'usuario', 1);

-- Insertar bienes de prueba
INSERT INTO bienes (codigo_patrimonial, nombre, descripcion, estado, persona_id) VALUES
('PAT-2024-001', 'Computadora Dell Inspiron', 'Intel i7, 16GB RAM, SSD 512GB', 'Asignado', 1),
('PAT-2024-002', 'Monitor LG 24"', 'Full HD, HDMI', 'Asignado', 1),
('PAT-2024-003', 'Teclado Mecánico', 'RGB, Cherry MX', 'Asignado', 1),
('PAT-2024-004', 'Impresora Canon', 'Color, multifunción', 'Asignado', 2),
('PAT-2024-005', 'Escritorio Modulable', 'Gris, 1.5m x 0.75m', 'Disponible', NULL),
('PAT-2024-006', 'Silla Ergonómica', 'Negra, reclinable', 'Disponible', NULL);

-- =========================================
-- VISTAS ÚTILES (Opcional)
-- =========================================

-- Vista: Bienes con información de asignado
CREATE OR REPLACE VIEW v_bienes_detalle AS
SELECT 
    b.id,
    b.codigo_patrimonial,
    b.nombre,
    b.descripcion,
    b.estado,
    b.fecha_registro,
    b.persona_id,
    p.nombre as persona_nombre,
    p.area
FROM bienes b
LEFT JOIN personas p ON b.persona_id = p.id;

-- Vista: Últimos movimientos
CREATE OR REPLACE VIEW v_movimientos_recientes AS
SELECT 
    h.id,
    h.bien_id,
    b.codigo_patrimonial,
    b.nombre,
    h.fecha,
    h.accion,
    pa.nombre as de_persona,
    pn.nombre as para_persona
FROM historial h
LEFT JOIN bienes b ON h.bien_id = b.id
LEFT JOIN personas pa ON h.persona_anterior_id = pa.id
LEFT JOIN personas pn ON h.persona_nueva_id = pn.id
ORDER BY h.fecha DESC
LIMIT 50;

-- =========================================
-- FIN DEL SCRIPT
-- =========================================
