# 📘 Guía de Uso Rápido - Control Patrimonial

## 🎯 Objetivos

Este documento te ayudará a usar el Sistema de Control Patrimonial en 5 minutos.

---

## 🔐 Paso 1: Iniciar Sesión

1. Abre tu navegador
2. Ingresa a: `http://localhost/PROYECTO_ING_WEB/frontend/login.php`
3. Usa estas credenciales:
   - **Email**: `admin@demo.com`
   - **Contraseña**: `123456`
4. Haz clic en "Iniciar Sesión"

✅ Verás el Dashboard con estadísticas

---

## 📊 Paso 2: Entender el Dashboard

El panel principal mostrará:

```
📦 Total de Bienes       → Todos los registrados
✅ Asignados             → Bienes en uso
📭 Disponibles           → Listos para asignar
⚠️ Dañados/Descartados   → No disponibles
```

---

## 📦 Paso 3: Registrar Bienes

### Opción A: Individual

1. Haz clic en **"Registrar Bien"**
2. Completa:
   - **Código Patrimonial**: `PAT-2024-001` (único)
   - **Nombre**: `Computadora Dell`
   - **Descripción**: `Intel i7, 16GB RAM`
   - **Estado**: Seleccionar (Disponible, Asignado, etc.)
   - **Persona**: Si aplica
3. Haz clic en **"Guardar"**

### Opción B: Masiva (Recomendado)

1. Haz clic en **"Importar Excel"**
2. Descarga la plantilla
3. Llena el archivo CSV/Excel:
   ```
   Código,Nombre,Descripción,Persona
   PAT-2024-001,Computadora,Intel i7,Juan Perez
   PAT-2024-002,Monitor,24",,
   ```
4. Sube el archivo
5. El sistema importa automáticamente

---

## 👥 Paso 4: Crear Desplazamientos

Un desplazamiento es cuando un bien se mueve de una persona a otra.

1. Haz clic en **"Nuevo Desplazamiento"**
2. Completa:
   - **Número**: `DESP-001` (único)
   - **Persona Origen**: Juan Perez (quien tiene el bien)
   - **Bienes**: ✓ Computadora, ✓ Monitor (selecciona múltiples)
   - **Persona Destino**: Maria Lopez (quien lo recibirá)
   - **Motivo**: Cambio de área / Renuncia / Transferencia
   - **Fecha**: Hoy
3. Haz clic en **"Crear Desplazamiento"**

✅ El sistema:
- Actualiza automáticamente las asignaciones
- Registra en el historial
- Crea la auditoría

---

## 📊 Paso 5: Generar Reportes

1. Haz clic en **"Reportes"**
2. Verás dos opciones:

### Reporte de Bienes por Persona
```
Genera PDF con:
- Todas las personas
- Sus bienes asignados
- Total por persona
```

### Reporte de Desplazamientos
```
Genera PDF con:
- Todos los movimientos
- Fechas
- Bienes movidos
- Motivos
```

3. Haz clic en **"Generar PDF"**
4. Se abre en nueva pestaña (imprimible)

---

## 🔍 Paso 6: Consultar Historial

Accede a **"Historial"** para ver:

```
Fecha | Bien | De (Persona) | Para (Persona) | Acción
```

Esto es útil para:
- ✓ Auditoría de movimientos
- ✓ Reconstruir histórico
- ✓ Verificar cambios

---

## 🔍 Paso 7: Ver Detalles

### Ver un Bien
1. Ir a **"Bienes"**
2. Haz clic en el ícono **👁️ (ojo)**
3. Verás: Código, Historia de movimientos, Persona asignada

### Ver una Persona
1. Ir a **"Personas"**
2. Haz clic en ícono **👁️**
3. Verás: Bienes asignados a esa persona

---

## 👤 Funciones de Admin (Solo Administrador)

### Crear Nuevo Usuario
1. Ir a **"Usuarios"** → **"Nuevo Usuario"**
2. Llenar:
   - Nombre
   - Email
   - Contraseña
   - Rol (Usuario, Supervisor, Admin)
3. Guardar

### Crear Nueva Persona
1. Ir a **"Personas"** → **"Nueva Persona"**
2. Llenar:
   - Nombre
   - Área (Sistemas, Administración, etc.)
3. Guardar

---

## ⚙️ Menú Principal

```
Dashboard          → Panel de inicio
├─ Bienes
│  ├─ Registrar Bien        → Crear uno nuevo
│  ├─ Importar Excel        → Crear múltiples
│  └─ [Listado]             → Ver todos
│
├─ Desplazamientos
│  ├─ Nuevo Desplazamiento  → Mover bien
│  └─ [Listado]             → Ver historial
│
├─ Reportes                 → Generar PDF
├─ Historial                → Ver auditoría
│
└─ (ADMIN)
   ├─ Usuarios              → Gestionar acceso
   └─ Personas              → Gestionar personal
```

---

## 🆘 Errores Comunes

### ❌ "Código patrimonial ya existe"
**Problema**: Intentas crear un bien con código duplicado  
**Solución**: Usa un código diferente

### ❌ "Debe asignar personas"
**Problema**: Intentas crear desplazamiento sin seleccionar bienes  
**Solución**: Selecciona bienes del lado izquierdo

### ❌ "No se puede importar archivo"
**Problema**: Formato inválido o archivo muy grande  
**Solución**: Usa Excel/CSV con columnas: Código, Nombre, Descripción, Persona

### ❌ "Usuario/Contraseña incorrectos"
**Problema**: Credenciales erróneas  
**Solución**: Verifica email y contraseña (sensible a mayúsculas)

---

## 💡 Tips Útiles

✓ **Código patrimonial**: Usa formato consistente `PAT-YYYY-XXX`

✓ **Importar Excel**: Es más rápido para muchos bienes a la vez

✓ **Desplazamientos**: Siempre llena el motivo para auditoría

✓ **Estado**: Usa los estados correctos:
- `Disponible` = Listo para asignar
- `Asignado` = En uso
- `Dañado` = No funciona
- `Descartado` = Dado de baja

✓ **Reportes**: Puedes imprimirlos o guardar como PDF

---

## 📞 Soporte

Si algo no funciona:

1. **Refresca la página**: Ctrl + F5 o Cmd + Shift + R
2. **Limpia cookies**: Cierra la sesión y vuelve a iniciar
3. **Contacta admin**: Si el problema persiste

---

## 🎓 Flujos Típicos

### Flujo 1: Instituir nuevo personal
```
1. Admin crea persona:    Personas → Nueva
2. Admin crea usuario:    Usuarios → Nuevo
3. Asignar bienes:        Registrar Bien + Asignar
4. Desp. bienvenida listo ✓
```

### Flujo 2: Renuncia de personal
```
1. Admin desactiva usuario:   Usuarios → Editar
2. Crear desplazamiento:      Desplazamientos → Nuevo
   - Origen: Persona que se va
   - Destino: Supervisora
   - Motivo: Renuncia
3. Sistema registra en historial ✓
```

### Flujo 3: Cambio de área
```
1. Ir a:  Desplazamientos → Nuevo
2. Origen: Área antigua
3. Destino:  Área nueva
4. Seleccionar bienes
5. Motivo: "Cambio de área"
6. Guardar ✓
```

---

## 📋 Plantilla CSV para Importación

Descarga y modifica este formato:

```
Código,Nombre,Descripción,Persona
PAT-2024-001,Monitor 24",LG Full HD,
PAT-2024-002,Teclado,Mecánico RGB,Juan Perez
PAT-2024-003,Mouse,Logitech Inalámbrico,Juan Perez
PAT-2024-004,Escritorio,1.5m Modulable,Maria Lopez
```

---

## ✅ Checklist de Inicio

- [ ] XAMPP corriendo (Apache + MySQL)
- [ ] BD `control_patrimonial` creada
- [ ] Script `setup.sql` ejecutado
- [ ] Sistema accesible en `http://localhost/PROYECTO_ING_WEB/frontend/login.php`
- [ ] Puedo iniciar sesión con `admin@demo.com`
- [ ] Dashboard muestra estadísticas
- [ ] Puedo ver y crear bienes
- [ ] Puedo crear desplazamientos
- [ ] Puedo generar reportes

¡**Sistema listo para usar!** 🎉

---

**Última actualización**: Abril 2024  
**Versión**: 1.0
