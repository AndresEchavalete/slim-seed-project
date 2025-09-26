# 📦 Instalación de SlimSeed Framework

Esta guía te explica cómo instalar y usar SlimSeed Framework como paquete de Composer en tus proyectos.

## 🚀 Instalación Rápida

### Opción 1: Instalación Automática (Recomendada)

```bash
# 1. Crear nuevo proyecto
mkdir mi-proyecto-slimseed
cd mi-proyecto-slimseed

# 2. Inicializar Composer
composer init

# 3. Instalar SlimSeed Framework
composer require slimseed/framework

# 4. ¡Listo! El framework se configura automáticamente
```

### Opción 2: Instalación Manual

```bash
# 1. Instalar el paquete
composer require slimseed/framework

# 2. Ejecutar instalador
php vendor/bin/slimseed:install

# 3. Seguir las instrucciones del instalador
```

## 🔧 Configuración Post-Instalación

### 1. Configurar Variables de Entorno

El archivo `.env` se crea automáticamente. Edítalo según tus necesidades:

```env
# Aplicación
APP_ENV=development
APP_DEBUG=true
APP_NAME="Mi Proyecto"

# Base de datos
DB_HOST=mysql
DB_PORT=3306
DB_NAME=mi_proyecto
DB_USER=mi_usuario
DB_PASS=mi_contraseña

# Notificaciones
NOTIFICATION_TYPE=email
ADMIN_EMAIL=admin@mi-proyecto.com
SLACK_WEBHOOK=https://hooks.slack.com/services/...
```

### 2. Levantar Contenedores

```bash
# Levantar servicios
docker-compose up -d

# Verificar que todo esté funcionando
docker-compose ps
```

### 3. Configurar Base de Datos

```bash
# Ejecutar migraciones
composer run migrate

# Verificar estado de migraciones
composer run migrate:status
```

### 4. Probar la Instalación

```bash
# Probar endpoint principal
curl http://localhost:8081/

# Probar health check
curl http://localhost:8081/health

# Probar API de usuarios
curl http://localhost:8081/api/users/active
```

## 🏗️ Estructura del Proyecto Creado

Después de la instalación, tendrás esta estructura:

```
mi-proyecto/
├── .env                          # Variables de entorno
├── .env.example                  # Plantilla de variables
├── composer.json                 # Dependencias
├── composer.lock                 # Lock de dependencias
├── docker-compose.yml            # Configuración Docker
├── README.md                     # Documentación del proyecto
├── public/
│   └── index.php                 # Punto de entrada
├── src/                          # Código fuente (desde el paquete)
├── config/                       # Configuraciones
├── migrations/                   # Migraciones de BD
├── scripts/                      # Scripts de utilidad
├── tests/                        # Tests
└── vendor/                       # Dependencias de Composer
```

## 🛠️ Comandos Disponibles

### Comandos de Composer

```bash
# Migraciones
composer run migrate              # Ejecutar migraciones
composer run reset-db             # Resetear base de datos
composer run migrate:status       # Estado de migraciones
composer run migrate:generate     # Generar nueva migración

# Instalación
composer run slimseed:install     # Re-ejecutar instalador
```

### Comandos de Docker

```bash
# Gestión de contenedores
docker-compose up -d              # Levantar en background
docker-compose down               # Detener contenedores
docker-compose restart            # Reiniciar contenedores

# Desarrollo
docker-compose exec app bash      # Acceder al contenedor
docker-compose logs -f app        # Ver logs en tiempo real
```

## 🎯 Personalización

### Agregar Nuevas Entidades

1. Crear entidad en `src/Domain/Entities/`
2. Crear repositorio en `src/Domain/Repositories/`
3. Implementar repositorio en `src/Infrastructure/Persistence/`
4. Crear caso de uso en `src/Application/UseCases/`
5. Crear controlador en `src/Presentation/Controllers/`
6. Registrar rutas en `src/Presentation/Routes/`

### Agregar Nuevos Servicios

1. Crear interfaz en `src/Domain/Services/`
2. Implementar servicio en `src/Infrastructure/Services/`
3. Registrar en el contenedor DI

### Configurar Notificaciones

```php
// En .env
NOTIFICATION_TYPE=slack
SLACK_WEBHOOK=https://hooks.slack.com/services/...

// O para email
NOTIFICATION_TYPE=email
ADMIN_EMAIL=admin@example.com
```

## 🔍 Solución de Problemas

### Error de Conexión a Base de Datos

```bash
# Verificar que MySQL esté corriendo
docker-compose ps mysql

# Ver logs de MySQL
docker-compose logs mysql

# Reiniciar MySQL
docker-compose restart mysql
```

### Error de Permisos

```bash
# Dar permisos correctos
chmod -R 755 public/
chmod -R 755 migrations/
```

### Puerto ya en Uso

```bash
# Cambiar puerto en docker-compose.yml
ports:
  - "8082:80"  # Cambiar 8081 por 8082
```

## 📚 Recursos Adicionales

- [Documentación de la API](API.md)
- [Guía de Arquitectura](architecture.md)
- [Patrones DDD](ddd-patterns.md)
- [Guía de Desarrollo](development.md)

## 🤝 Contribuir

Si encuentras algún problema o tienes sugerencias:

1. Abre un issue en el repositorio
2. Fork el proyecto
3. Crea una rama para tu feature
4. Envía un pull request

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver [LICENSE](LICENSE) para más detalles.

---

**¡Disfruta desarrollando con SlimSeed Framework!** 🚀
