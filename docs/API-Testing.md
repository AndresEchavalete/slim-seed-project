# 🧪 API Testing Collections

Colecciones de requests HTTP para probar la API del Slim Seed Project con diferentes clientes.

## 📋 Colecciones Disponibles

### **1. Archivo .http (Universal)**
- **Archivo**: `Slim-Seed-API.http`
- **Compatibilidad**: Bruno, Insomnia, VS Code REST Client, IntelliJ HTTP Client
- **Características**: 
  - Variables reutilizables
  - Casos de prueba completos
  - Tests de validación y error
  - Tests de rendimiento y estrés

### **2. Bruno Collection**
- **Archivo**: `bruno-collection.json`
- **Compatibilidad**: Bruno IDE
- **Características**:
  - Interfaz gráfica intuitiva
  - Variables de entorno
  - Organización por carpetas
  - Tests automatizados

### **3. Insomnia Collection**
- **Archivo**: `insomnia-collection.json`
- **Compatibilidad**: Insomnia
- **Características**:
  - Interfaz moderna
  - Variables de entorno
  - Organización por carpetas
  - Historial de requests

## 🚀 Instalación y Uso

### **Bruno IDE**

1. **Instalar Bruno**:
   ```bash
   # Descargar desde: https://www.usebruno.com/
   # O instalar con npm
   npm install -g @usebruno/cli
   ```

2. **Importar Colección**:
   - Abrir Bruno IDE
   - Crear nueva colección
   - Importar archivo `bruno-collection.json`

3. **Configurar Variables**:
   - Ir a "Environments"
   - Configurar variables según tu entorno

### **Insomnia**

1. **Instalar Insomnia**:
   ```bash
   # Descargar desde: https://insomnia.rest/
   # O instalar con npm
   npm install -g insomnia
   ```

2. **Importar Colección**:
   - Abrir Insomnia
   - File → Import Data
   - Seleccionar archivo `insomnia-collection.json`

3. **Configurar Variables**:
   - Ir a "Manage Environments"
   - Configurar variables según tu entorno

### **VS Code REST Client**

1. **Instalar Extensión**:
   - Buscar "REST Client" en VS Code
   - Instalar extensión de Huachao Mao

2. **Usar Archivo .http**:
   - Abrir archivo `Slim-Seed-API.http`
   - Hacer clic en "Send Request" sobre cada request

### **IntelliJ HTTP Client**

1. **Crear Archivo .http**:
   - Crear nuevo archivo `.http`
   - Copiar contenido de `Slim-Seed-API.http`

2. **Ejecutar Requests**:
   - Hacer clic en el botón "Run" junto a cada request
   - O usar Ctrl+Enter

## ⚙️ Configuración de Variables

### **Variables de Entorno**

```json
{
  "baseUrl": "http://localhost:8081",
  "testEmail": "test@example.com",
  "testName": "Test User",
  "testPassword": "password123",
  "userId": "1"
}
```

### **Entornos Disponibles**

#### **Development**
- **URL**: `http://localhost:8081`
- **Base de datos**: MySQL local
- **Redis**: Local

#### **Production**
- **URL**: `https://api.slimseed.com`
- **Base de datos**: MySQL de producción
- **Redis**: Redis de producción

## 🧪 Casos de Prueba Incluidos

### **✅ Casos de Éxito**
- Bienvenida del sistema
- Health check
- Crear usuario
- Obtener usuarios activos
- Obtener usuario por ID
- Actualizar nombre de usuario
- Autenticar usuario
- Historial de salud
- Enviar notificaciones

### **❌ Casos de Error**
- Email inválido
- Usuario no encontrado
- Credenciales incorrectas
- Datos faltantes
- Email duplicado
- Formato de fecha inválido

### **🚀 Tests de Rendimiento**
- Múltiples requests simultáneos
- Creación masiva de usuarios
- Health checks repetitivos
- Tests de concurrencia

### **🔒 Tests de Seguridad**
- SQL injection
- XSS attacks
- Path traversal
- Large payloads
- Headers maliciosos

## 📊 Organización de Requests

### **🏠 Públicos**
- Bienvenida
- Health Check
- Status API

### **👥 Usuarios**
- Crear Usuario
- Obtener Usuarios Activos
- Obtener Usuario por ID
- Actualizar Nombre de Usuario
- Autenticar Usuario

### **📊 Salud**
- Último Estado de Salud
- Historial de Salud

### **🔔 Notificaciones**
- Enviar Alerta
- Notificación de Prueba

### **❌ Casos de Error**
- Validaciones
- Errores 404
- Errores 400
- Errores 401

### **🧪 Tests de Rendimiento**
- Stress tests
- Concurrencia
- Múltiples requests

## 🔧 Comandos Útiles

### **Verificar Servidor**
```bash
# Health check
curl http://localhost:8081/health

# Bienvenida
curl http://localhost:8081/
```

### **Ejecutar Migraciones**
```bash
# Ejecutar migraciones
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/migrate.php"

# Resetear base de datos
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/reset-db.php"
```

### **Ver Logs**
```bash
# Logs de la aplicación
docker-compose logs -f app

# Logs de MySQL
docker-compose logs -f mysql

# Logs de Redis
docker-compose logs -f redis
```

## 📝 Notas de Uso

### **Antes de Empezar**
1. Asegúrate de que el servidor esté ejecutándose
2. Ejecuta las migraciones de base de datos
3. Configura las variables de entorno correctamente

### **Orden de Ejecución**
1. **Públicos** - Verificar que el servidor funcione
2. **Usuarios** - Crear usuarios de prueba
3. **Salud** - Verificar monitoreo
4. **Notificaciones** - Probar sistema de alertas
5. **Casos de Error** - Validar manejo de errores
6. **Tests de Rendimiento** - Probar bajo carga

### **Variables Dinámicas**
- `@userId` se actualiza automáticamente al crear usuarios
- `@testEmail` se puede cambiar para diferentes pruebas
- `@baseUrl` se puede cambiar para diferentes entornos

### **Troubleshooting**
- Si un request falla, verifica que el servidor esté funcionando
- Revisa los logs para errores específicos
- Asegúrate de que las variables estén configuradas correctamente
- Verifica que la base de datos esté inicializada

## 🎯 Mejores Prácticas

### **Testing**
- Ejecuta los tests en orden lógico
- Verifica respuestas antes de continuar
- Usa variables para datos reutilizables
- Documenta casos de prueba específicos

### **Desarrollo**
- Usa diferentes entornos para desarrollo y producción
- Mantén las colecciones actualizadas
- Agrega nuevos casos de prueba cuando agregues funcionalidades
- Documenta cambios en la API

### **Colaboración**
- Comparte las colecciones con el equipo
- Mantén versiones de las colecciones
- Documenta casos de prueba específicos del proyecto
- Usa naming conventions consistentes

---

**¡Con estas colecciones puedes probar completamente la API del Slim Seed Project!** 🚀
