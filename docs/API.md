# 📚 API Documentation

Documentación completa de la API REST del Slim Seed Project.

## 🔗 Base URL

```
http://localhost:8081
```

## 📋 Endpoints

### **🏠 Bienvenida**

#### `GET /`
Mensaje de bienvenida con información del proyecto.

**Respuesta:**
```json
{
  "message": "¡Bienvenido a Slim Seed Project!",
  "version": "1.0.0",
  "architecture": "DDD + Hexagonal",
  "framework": "Slim 4 + DI Container",
  "timestamp": "2025-09-26 13:39:39"
}
```

---

### **💚 Health Check**

#### `GET /health`
Verifica el estado de salud del sistema.

**Respuesta:**
```json
{
  "healthy": true,
  "message": "All systems operational",
  "checks": {
    "database": true,
    "redis": true,
    "memory": true
  }
}
```

#### `GET /api/status`
Alias para el health check.

---

### **👥 Gestión de Usuarios**

#### `POST /api/users`
Crea un nuevo usuario.

**Request Body:**
```json
{
  "email": "test@example.com",
  "name": "Test User",
  "password": "password123"
}
```

**Respuesta (201):**
```json
{
  "success": true,
  "message": "User created successfully",
  "user": {
    "id": 1,
    "email": "test@example.com",
    "name": "Test User",
    "isActive": true,
    "createdAt": "2025-09-26 13:39:54",
    "updatedAt": null
  }
}
```

**Errores:**
- `400` - Email ya existe o datos inválidos

---

#### `GET /api/users/active`
Obtiene todos los usuarios activos.

**Respuesta:**
```json
{
  "count": 1,
  "users": [
    {
      "id": 1,
      "email": "test@example.com",
      "name": "Test User",
      "isActive": true,
      "createdAt": "2025-09-26 13:39:54",
      "updatedAt": null
    }
  ]
}
```

---

#### `GET /api/users/{id}`
Obtiene un usuario por su ID.

**Parámetros:**
- `id` (integer) - ID del usuario

**Respuesta (200):**
```json
{
  "id": 1,
  "email": "test@example.com",
  "name": "Test User",
  "isActive": true,
  "createdAt": "2025-09-26 13:39:54",
  "updatedAt": null
}
```

**Errores:**
- `404` - Usuario no encontrado

---

#### `PUT /api/users/{id}/name`
Actualiza el nombre de un usuario.

**Parámetros:**
- `id` (integer) - ID del usuario

**Request Body:**
```json
{
  "name": "Nuevo Nombre"
}
```

**Respuesta (200):**
```json
{
  "success": true,
  "message": "User name updated successfully",
  "user": {
    "id": 1,
    "email": "test@example.com",
    "name": "Nuevo Nombre",
    "isActive": true,
    "createdAt": "2025-09-26 13:39:54",
    "updatedAt": "2025-09-26 13:45:12"
  }
}
```

**Errores:**
- `400` - Usuario no encontrado o datos inválidos

---

#### `POST /api/users/authenticate`
Autentica un usuario.

**Request Body:**
```json
{
  "email": "test@example.com",
  "password": "password123"
}
```

**Respuesta (200):**
```json
{
  "success": true,
  "message": "Authentication successful",
  "user": {
    "id": 1,
    "email": "test@example.com",
    "name": "Test User",
    "isActive": true,
    "createdAt": "2025-09-26 13:39:54",
    "updatedAt": null
  }
}
```

**Errores:**
- `401` - Credenciales inválidas

---

### **📊 Historial de Salud**

#### `GET /api/health/latest`
Obtiene el último estado de salud registrado.

**Respuesta (200):**
```json
{
  "id": 1,
  "status": "healthy",
  "timestamp": "2025-09-26 13:39:39",
  "details": {
    "database": true,
    "redis": true,
    "memory": true
  }
}
```

**Errores:**
- `404` - No hay estados de salud registrados

---

#### `GET /api/health/history`
Obtiene el historial de estados de salud por rango de fechas.

**Query Parameters:**
- `from` (string, opcional) - Fecha de inicio (YYYY-MM-DD HH:MM:SS)
- `to` (string, opcional) - Fecha de fin (YYYY-MM-DD HH:MM:SS)

**Ejemplo:**
```
GET /api/health/history?from=2025-09-01&to=2025-09-30
```

**Respuesta:**
```json
{
  "from": "2025-09-01 00:00:00",
  "to": "2025-09-30 23:59:59",
  "count": 2,
  "history": [
    {
      "id": 2,
      "status": "healthy",
      "timestamp": "2025-09-26 14:30:15",
      "details": {
        "database": true,
        "redis": true,
        "memory": true
      }
    },
    {
      "id": 1,
      "status": "healthy",
      "timestamp": "2025-09-26 13:39:39",
      "details": {
        "database": true,
        "redis": true,
        "memory": true
      }
    }
  ]
}
```

---

### **🔔 Notificaciones**

#### `POST /api/notifications/alert`
Envía una alerta manual.

**Request Body:**
```json
{
  "message": "Sistema en mantenimiento",
  "context": {
    "duration": "2 hours",
    "severity": "high"
  }
}
```

**Respuesta (200):**
```json
{
  "success": true,
  "message": "Alert sent successfully",
  "sent_at": "2025-09-26 13:45:12"
}
```

---

#### `POST /api/notifications/test`
Envía una notificación de prueba.

**Respuesta (200):**
```json
{
  "success": true,
  "message": "Test notification sent",
  "sent_at": "2025-09-26 13:45:12"
}
```

---

## 🔧 Códigos de Estado HTTP

| Código | Descripción |
|--------|-------------|
| `200` | OK - Solicitud exitosa |
| `201` | Created - Recurso creado exitosamente |
| `400` | Bad Request - Datos inválidos |
| `401` | Unauthorized - No autorizado |
| `404` | Not Found - Recurso no encontrado |
| `500` | Internal Server Error - Error del servidor |

## 📝 Ejemplos de Uso

### **Crear y Autenticar Usuario**

```bash
# 1. Crear usuario
curl -X POST http://localhost:8081/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "name": "John Doe",
    "password": "securepassword123"
  }'

# 2. Autenticar usuario
curl -X POST http://localhost:8081/api/users/authenticate \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "securepassword123"
  }'

# 3. Obtener usuarios activos
curl http://localhost:8081/api/users/active
```

### **Monitoreo de Salud**

```bash
# Verificar estado actual
curl http://localhost:8081/health

# Obtener último estado registrado
curl http://localhost:8081/api/health/latest

# Obtener historial de la última semana
curl "http://localhost:8081/api/health/history?from=2025-09-19&to=2025-09-26"
```

### **Notificaciones**

```bash
# Enviar alerta
curl -X POST http://localhost:8081/api/notifications/alert \
  -H "Content-Type: application/json" \
  -d '{
    "message": "Mantenimiento programado",
    "context": {
      "duration": "30 minutes",
      "affected_services": ["API", "Database"]
    }
  }'

# Enviar notificación de prueba
curl -X POST http://localhost:8081/api/notifications/test
```

## 🛡️ Seguridad

### **Validación de Entrada**
- Todos los endpoints validan los datos de entrada
- Los emails deben tener formato válido
- Las contraseñas se hashean automáticamente
- Los IDs deben ser enteros positivos

### **Manejo de Errores**
- Errores consistentes en formato JSON
- Mensajes descriptivos para debugging
- Códigos de estado HTTP apropiados
- Logging de errores para monitoreo

## 📊 Rate Limiting

Actualmente no implementado, pero se puede agregar fácilmente con middleware de Slim.

## 🔄 Versionado

La API actual es la versión 1.0.0. Para futuras versiones se puede usar:
- `/api/v1/` para la versión actual
- `/api/v2/` para futuras versiones

## 📞 Soporte

Para soporte técnico o reportar bugs, contacta al equipo de desarrollo.
