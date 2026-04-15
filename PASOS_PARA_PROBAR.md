# 🚀 PASOS ESPECÍFICOS PARA INSTALAR Y PROBAR EL SISTEMA

## ⚡ REQUISITOS PREVIOS

Antes de empezar, asegurate de tener:

```
✓ XAMPP instalado (Apache + MySQL)
✓ PHP 7.4+ habilitado
✓ MySQL 5.7+ corriendo
✓ navegador web moderno
```

---

## 📋 INSTALACIÓN PASO A PASO

### PASO 1: Verificar que XAMPP esté corriendo

```bash
# En tu servidor local:
- Apache debe estar iniciado (puerto 80)
- MySQL debe estar iniciado (puerto 3306)

Verifica en:
http://localhost/
# Deberías ver la página de XAMPP
```

**Si no está corriendo:**
- **Windows**: Abre XAMPP Control Panel → Click "Start" en Apache y MySQL
- **Mac/Linux**: `sudo /Applications/XAMPP/xamppfiles/bin/xampp start`

---

### PASO 2: Descargar el proyecto

#### Opción A: Clonar desde GitHub

```bash
cd /xampp/htdocs
git clone https://github.com/ValeRojas1/PROYECTO_ING_WEB.git
cd PROYECTO_ING_WEB
```

#### Opción B: Copiar manualmente

1. Descarga el ZIP del repositorio GitHub
2. Extrae en `/xampp/htdocs/`
3. Renombra la carpeta a: `PROYECTO_ING_WEB`

---

### PASO 3: Crear la Base de Datos (MUY IMPORTANTE)

Este es el paso crítico. Aquí hay 3 formas:

#### 🔵 FORMA 1: Usar phpMyAdmin (Más Fácil)

```
1. Abre en browser: http://localhost/phpmyadmin
2. Haz clic en "SQL" en la parte superior
3. Copia TODO el contenido de: /PROYECTO_ING_WEB/setup.sql
4. Pégalo en el área de SQL
5. Haz clic en el botón rojo "Ejecutar" (Go)
6. Espera a que termine
7. Deberías ver: "Se ejecutó exitosamente" ✓
```

#### 🟢 FORMA 2: Terminal (Línea de comando)

```bash
# En Windows (si tienes MySQL en PATH)
mysql -u root < setup.sql

# Luego verifica la creación:
mysql -u root -e "SHOW DATABASES;" | grep control_patrimonial
```

#### 🟡 FORMA 3: Verificador Automático

```
1. Abre: http://localhost/PROYECTO_ING_WEB/verificar.php
2. Presiona botón "Crear Base de Datos"
3. Espera confirmación
```

**Verifica que se creó:**
1. Abre phpMyAdmin
2. En la izquierda debería aparecer: `control_patrimonial`
3. Haz clic en ella
4. Deberías ver 8 tablas (usuarios, personas, bienes, etc.)

---

### PASO 4: Validar la Instalación

Este paso es OBLIGATORIO:

```
1. Abre en browser: http://localhost/PROYECTO_ING_WEB/verificar.php
2. Deberías ver una página con checkmarks verdes ✓

Requisitos que debe verificar:
✓ PHP versión
✓ Extensión MySQLi
✓ Conexión a base de datos
✓ Carpetas con permisos
✓ Tablas de base de datos

Si ves ✗ en alguno:
- Para PHP: No hay solución, necesitas PHP 7.4+
- Para MySQLi: Edita php.ini y descomenta: extension=mysqli
- Para BD: Repite PASO 3
- Para carpetas: En Windows, clic derecho → Propiedades → Permisos
```

**Si TODO está verde ✓:**
```
Muestra un botón "SISTEMA LISTO - Ir a Login"
Haz clic en él → Te llevará a la siguiente pantalla
```

---

### PASO 5: Acceder al Sistema

```
URL: http://localhost/PROYECTO_ING_WEB/
o
URL: http://localhost/PROYECTO_ING_WEB/frontend/login.php
```

**Verás:** Formulario de login con fondo morado

---

## 🔐 PRUEBAS FUNCIONALES

### TEST 1: Login con credenciales demo

**Pantalla: Login**

Ingresa:
```
Email:      admin@demo.com
Contraseña: 123456
```

Haz clic en: **"Iniciar Sesión"**

**Esperado:** Se abre Dashboard con estadísticas
```
✓ Total de Bienes: 6
✓ Asignados: 3
✓ Disponibles: 2
✓ Dañados/Descartados: 1
```

---

### TEST 2: Ver Dashboard

**Pantalla: Dashboard**

En el sidebar izquierdo verás:
```
☰ MENÚ PRINCIPAL
  🏠 Dashboard
  📦 Bienes
  📤 Importar Excel
  📍 Desplazamientos
  📊 Reportes
  📋 Historial
  ⚙️ Administración
  🚪 Cerrar Sesión
```

**En el contenido central:**
```
4 Tarjetas con estadísticas
- Total de Bienes
- Asignados
- Disponibles
- Dañados/Descartados

2 Botones de acción rápida
- Registrar Bien
- Nuevo Desplazamiento
```

**Prueba:** Haz clic en cada tarjeta
- Debería aplicar filtros o llevar a la sección correspondiente

---

### TEST 3: Ver lista de Bienes

**Menú:** Haz clic en "📦 Bienes"

**Pantalla: Lista de Bienes**

Verás tabla con:
```
| Código        | Nombre      | Estado      | Persona   | Acciones |
|---------------|-------------|-------------|-----------|----------|
| PAT-2024-001  | Computadora | Asignado    | Juan P.   | [✎ 👁]  |
| PAT-2024-002  | Monitor     | Disponible  | -         | [✎ 👁]  |
```

**Prueba 1:** Haz clic en icono **👁** (Ver)
```
Debería mostrar:
- Detalles del bien
- Historial de cambios
- Quién lo tiene ahora
```

**Prueba 2:** Haz clic en icono **✎** (Editar)
```
Debería abrir formulario editable de ese bien
Puedes cambiar nombre, estado, persona
```

---

### TEST 4: Registrar un Bien Nuevo

**Menú:** Haz clic en "Registrar Bien" o "📦 Bienes" → botón verde "Nuevo"

**Formulario:**
```
Código Patrimonial: PAT-2024-007
Nombre:             Impresora Xerox
Descripción:        Blanco y negro, 50ppm
Estado:             Disponible
Persona:            (dejar vacío)
```

**Haz clic:** "Guardar"

**Esperado:**
```
✓ Mensaje: "Bien registrado correctamente"
✓ Te redirige a lista de bienes
✓ Verás PAT-2024-007 al final de la lista
```

---

### TEST 5: Importar desde Excel

**Menú:** "📤 Importar Excel"

**Pantalla: Importador**

1. Haz clic en **Descargar Plantilla**
   - Se descarga: `plantilla.csv`

2. Abre con Excel/LibreOffice

3. Completa 2-3 filas:
```
Código,Nombre,Descripción,Persona
PAT-2024-100,Teclado,Logitech K850,Juan Perez
PAT-2024-101,Mouse,Razer Pro,Maria Lopez
```

4. Guarda como CSV (Excel → Guardar como → CSV)

5. Vuelve a "📤 Importar Excel"

6. Haz clic en **"Elegir Archivo"** → Selecciona tu CSV

7. Haz clic en **"Importar"**

**Esperado:**
```
✓ Mensaje: "2 bienes importados correctamente"
✓ En Bienes ves PAT-2024-100 y PAT-2024-101
```

---

### TEST 6: Crear Desplazamiento

**Menú:** "📍 Desplazamientos" → Botón "Nuevo"

**Pantalla: Nuevo Desplazamiento**

Completa:
```
Número Desplazamiento: DESP-001
Persona Origen:        Juan Perez     ← Selecciona
```

**Automáticamente:**
```
Aparecerá checkbox con sus bienes:
☑ PAT-2024-001 (Computadora)
☐ PAT-2024-003 (Impresora)
```

**Continúa rellenando:**
```
Persona Destino:  Maria Lopez
Motivo:           Cambio de área
Fecha:            [hoy]

☑ Selecciona 1-2 bienes
```

**Haz clic:** "Crear Desplazamiento"

**Esperado:**
```
✓ Mensaje: "Desplazamiento creado DESP-001"
✓ Te redirige a lista de desplazamientos
✓ Ves DESP-001 en la tabla
✓ Estados de bienes: Se actualiza automáticamente
```

---

### TEST 7: Ver Historial

**Menú:** "📋 Historial"

**Pantalla: Historial completo**

Verás tabla con TODOS los movimientos:
```
| Fecha      | Bien          | Código      | De         | Para       | Acción |
|------------|---------------|-------------|------------|-----------|--------|
| 2024-04-14 | Computadora   | PAT-2024-001| Juan Perez | Maria Lopez| Mover  |
```

**Prueba:** Verifica que aparezca el desplazamiento que creaste

---

### TEST 8: Generar Reportes

**Menú:** "📊 Reportes"

**Pantalla: Reportes**

#### Reporte 1: Bienes por Persona
```
1. Haz clic en: "Descargar PDF: Bienes por Persona"
2. Se abre en nueva pestaña (PDF)
3. Debería mostrar:
   - Título: "Reporte de Bienes por Persona"
   - Tabla con: Persona | Cantidad | Detalles
   - Botones: [Imprimir] [Descargar]

Ctrl+P para imprimir o guardar como PDF
```

#### Reporte 2: Desplazamientos
```
1. Selecciona fechas (opcional)
2. Haz clic en: "Descargar PDF: Desplazamientos"
3. Se abre PDF con tabla de movimientos
```

---

### TEST 9: Admin - Usuarios

**Menú:** "⚙️ Administración" → "👥 Usuarios"

**Pantalla: Lista de Usuarios**

Verás 3 usuarios de demo:
```
| Email              | Nombre     | Rol       | Estado | Acciones |
|------------------|------------|-----------|--------|----------|
| admin@demo.com     | Administrador| Admin    | Activo | [✎ 👁]  |
| supervisor@demo.com| Supervisor | Supervisor| Activo | [✎ 👁]  |
| usuario@demo.com   | Usuario    | Usuario   | Activo | [✎ 👁]  |
```

**Prueba:** Haz clic en **✎** junto a usuario@demo.com
```
Debería abrir formulario donde puedes:
☐ Cambiar nombre
☐ Cambiar rol
☐ Cambiar estado (Activo/Inactivo)
☐ Guardar cambios
```

---

### TEST 10: Admin - Personas

**Menú:** "⚙️ Administración" → "👤 Personas"

**Pantalla: Lista de Personas**

Verás 4 personas de demo:
```
| Nombre          | Área      | Estado | Bienes | Acciones |
|----------------|-----------|--------|--------|----------|
| Juan Perez      | Sistemas  | Activo | 2      | [✎ 👁]  |
| Maria Lopez     | Admin     | Activo | 1      | [✎ 👁]  |
| Carlos Rodriguez| HR        | Activo | 1      | [✎ 👁]  |
| Alejandra Silva | Finanzas  | Activo | 2      | [✎ 👁]  |
```

**Prueba:** Haz clic en **👁** junto a Juan Perez
```
Debería mostrar:
- Datos personales
- Tabla de bienes asignados
- Historial de movimientos
```

---

### TEST 11: Logout (Cerrar Sesión)

**Menú:** Haz clic en "🚪 Cerrar Sesión" (abajo del menú)

**Esperado:**
```
✓ Se limpia la sesión
✓ Te devuelve a login.php
✓ No tienes acceso a dashboard
```

**Intenta entrar a dashboard directamente:**
```
URL: http://localhost/PROYECTO_ING_WEB/frontend/dashboard.php
Esperado: Te redirige a login
```

---

### TEST 12: Cambiar de usuario (Roles diferentes)

**Vuelve a login:**

Prueba con diferentes usuarios:

#### Usuario: supervisor@demo.com

```
Email:      supervisor@demo.com
Contraseña: 123456
```

**Verás:**
- Mismo menú
- Mismas funcionalidades

#### Usuario: usuario@demo.com

```
Email:      usuario@demo.com
Contraseña: 123456
```

**Verás:**
- Menú sin opción "Administración"
- Solo puede ver bienes, desplazamientos, reportes, historial
- No puede crear usuarios ni personas

---

## ✅ CHECKLIST DE VALIDACIÓN

```
INSTALACIÓN:
☐ XAMPP corriendo (Apache + MySQL)
☐ Proyecto en /xampp/htdocs/PROYECTO_ING_WEB/
☐ Base de datos creada (verificar.php muestra ✓)
☐ Carpetas con permisos de escritura

ACCESO:
☐ Página de login abre correctamente
☐ Credenciales de demo funcionan
☐ Dashboard muestra con datos

FUNCIONALIDADES:
☐ Listar bienes funciona
☐ Registrar bien nuevo funciona
☐ Editar bien funciona
☐ Importar Excel funciona
☐ Crear desplazamiento funciona
☐ Ver historial funciona
☐ Generar PDF de reportes funciona
☐ Cambiar entre usuarios funciona
☐ Admin panel de usuarios funciona
☐ Admin panel de personas funciona
☐ Logout funciona

SEGURIDAD:
☐ No se puede acceder a dashboard sin login
☐ Sesión expira después de 1 hora
☐ Usuario normal no ve "Administración"
☐ Contraseñas no son visibles en BD (bcrypt)
```

---

## 🆘 TROUBLESHOOTING

### ❌ Error: "No se puede conectar a la base de datos"

**Solución:**
```
1. Verifica MySQL está corriendo
2. Abre phpMyAdmin: http://localhost/phpmyadmin
3. Si pide contraseña, usa: root (sin contraseña)
4. Verifica tabla "control_patrimonial" exista
5. Si no existe, ejecuta setup.sql nuevamente
```

---

### ❌ Error: "Tabla no existe"

**Solución:**
```
1. Ve a phpMyAdmin
2. Selecciona "control_patrimonial"
3. Deberías ver 8 tablas:
   - usuarios
   - personas
   - bienes
   - desplazamientos
   - detalle_desplazamiento
   - historial
   - v_bienes_detalle (vista)
   - v_movimientos_recientes (vista)

4. Si faltan, ejecuta setup.sql completo nuevamente
```

---

### ❌ Error: "Acceso denegado" en login

**Solución:**
```
Credenciales correctas:
✓ admin@demo.com / 123456
✓ supervisor@demo.com / 123456
✓ usuario@demo.com / 123456

Si no funcionan:
1. Verifica en phpMyAdmin:
   - Base de datos: control_patrimonial
   - Tabla: usuarios
   - Deberías ver 3 registros

2. Si la tabla está vacía, ejecuta setup.sql

3. Si los emails no coinciden, verifica en tabla usuarios
```

---

### ❌ Error: "No puedo subir archivos"

**Solución:**
```
1. Verifica carpeta /uploads/ existe
   Si no: Crea carpeta manualmente

2. Verifica permisos de escritura:
   Windows: Clic derecho → Propiedades → Seguridad
   Linux/Mac: chmod 755 uploads/

3. Reinicia Apache (XAMPP Control Panel)
```

---

### ❌ Error: "PDF no se genera"

**Solución:**
```
1. El sistema usa HTML→PDF del navegador
2. Para descargar PDF:
   - Ctrl+P (Windows/Linux) o Cmd+P (Mac)
   - Selecciona: "Guardar como PDF"
   - Elige carpeta

3. O haz clic en botón "Descargar PDF" si existe
```

---

### ❌ Error: "Página en blanco"

**Solución:**
```
1. Abre consola: F12 → Pestaña "Console"
2. Verifica si hay errores en rojo
3. Si dice "mysqli not loaded":
   - Edita php.ini
   - Descomen ta: extension=mysqli
   - Reinicia Apache

4. Si dice "Archivo no encontrado":
   - Verifica rutas en config/database.php
```

---

## 📞 ¿Necesitas ayuda?

Si algo no funciona:

1. **Verifica verificar.php** - Te dice qué está mal
2. **Lee README_COMPLETO.md** - Documentación completa
3. **Revisa phpMyAdmin** - Verifica que BD exista
4. **Revisa browser console** - F12 → Console

---

## 🎉 ¡TODO LISTO!

Una vez completes estos tests, tu sistema está 100% funcional.

**Ahora puedes:**
```
✅ Usar en desarrollo local
✅ Subir a servidor de pruebas
✅ Personalizar datos
✅ Usar en producción
```

¡Felicidades! 🚀
