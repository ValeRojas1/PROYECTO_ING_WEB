# 🏛️ Sistema de Control Patrimonial

**Versión**: 1.0  
**Estado**: Completo y Funcional  
**Tipo**: Sistema Web de 3 Capas

Un sistema web integral para la gestión y control de bienes patrimoniales institucionales, con funcionalidades robustas de registro, asignación, desplazamiento, auditoría y generación de reportes.

## 🚀 Iniciar Rápidamente

## Integrantes
- Estrada Flores Axel
- Rojas Camayo Valentino

### 1. Instalación
```bash
# Copiar a XAMPP
cp -r PROYECTO_ING_WEB /xampp/htdocs/

# Ejecutar script SQL
mysql -u root < setup.sql
```

### 2. Acceder
- 🌐 URL: `http://localhost/PROYECTO_ING_WEB/frontend/login.php`
- 📧 Usuario: `admin@demo.com`
- 🔑 Contraseña: `123456`

## ✨ Características

- ✅ Autenticación y autorización multiusuario
- ✅ Registro individual e importación masiva de bienes
- ✅ Gestión de desplazamientos con historial automático
- ✅ Reportes en PDF (bienes, desplazamientos)
- ✅ Auditoría completa de movimientos
- ✅ Panel de control con estadísticas
- ✅ Interfaz responsiva con Bootstrap 5

## 🏗️ Tecnologías

| Componente | Tecnología |
|-----------|-----------|
| Backend | PHP 7.4+ |
| Frontend | HTML5, CSS3, JavaScript |
| Base de Datos | MySQL 5.7+ |
| Servidor | Apache (XAMPP) |
| Framework CSS | Bootstrap 5 |

## 📁 Estructura

```
PROYECTO_ING_WEB/
├── config/          # Configuración y conexión BD
├── backend/         # DAOs, Servicios, Utilidades
├── frontend/        # Vistas y controladores
├── assets/          # CSS, JavaScript
├── uploads/         # Archivos importados
└── reports/         # Reportes generados
```

## 📖 Documentación Completa

Ver: [`README_COMPLETO.md`](README_COMPLETO.md) para detalles completos de instalación, uso y troubleshooting.

## 🔑 Roles y Acceso

| Rol | Permisos |
|-----|----------|
| **Admin** | Acceso total |
| **Supervisor** | Ver reportes, crear desplazamientos |
| **Usuario** | Ver bienes, registrar básicos |

## 🎯 Caso de Uso

Historia de una institución que necesita controlar sus bienes patrimoniales (computadoras, muebles, equipos) y saber quién los tiene, historial de movimientos, y generar reportes de auditoría.

## 🔒 Seguridad

- ✓ Contraseñas con hash bcrypt
- ✓ Sesiones con timeout automático
- ✓ Validación de entrada sanitizada
- ✓ Control de acceso por roles
- ✓ Auditoría completa

---

**Para iniciar: Ejecutar `setup.sql` en phpMyAdmin** 🚀
