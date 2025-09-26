# 🚀 SlimSeed Framework - Arquitectura Hexagonal + DDD + Doctrine ORM

**Framework PHP reutilizable** con **Slim Framework + DDD + Arquitectura Hexagonal + Doctrine ORM** listo para instalar en cualquier proyecto.

> **Instalable via Composer:** `composer require slimseed/framework` - Estructura profesional y escalable.

[![PHP Version](https://img.shields.io/badge/php-8.2+-blue.svg)](https://php.net)
[![Slim Framework](https://img.shields.io/badge/slim-4.15-green.svg)](https://slimframework.com)
[![Doctrine ORM](https://img.shields.io/badge/doctrine-3.5-orange.svg)](https://doctrine-project.org)
[![Docker](https://img.shields.io/badge/docker-ready-blue.svg)](https://docker.com)

## 📋 Tabla de Contenidos

- [🏗️ Arquitectura](#️-arquitectura)
- [🚀 Características](#-características)
- [🛠️ Instalación](#️-instalación)
- [🔧 Configuración](#-configuración)
- [📚 API Documentation](#-api-documentation)
- [🏛️ Arquitectura Hexagonal](#️-arquitectura-hexagonal)
- [🗄️ Base de Datos](#️-base-de-datos)
- [🧪 Testing](#-testing)
- [📖 Guías](#-guías)

## 🏗️ Arquitectura

### **Principios Implementados**
- ✅ **Arquitectura Hexagonal** (Puertos y Adaptadores)
- ✅ **Domain Driven Design (DDD)**
- ✅ **SOLID Principles**
- ✅ **Inyección de Dependencias**
- ✅ **Repository Pattern**
- ✅ **Unit of Work (Doctrine)**

### **Estructura de Capas**

```
src/
├── Domain/                    # 🎯 CAPA DE DOMINIO
│   ├── Entities/              # Entidades de negocio (Doctrine)
│   ├── ValueObjects/          # Objetos de valor
│   ├── Services/              # 🚪 PUERTOS (Interfaces)
│   └── Repositories/          # 🚪 PUERTOS (Interfaces)
│
├── Application/               # 🎯 CAPA DE APLICACIÓN
│   ├── UseCases/              # Casos de uso
│   ├── DTOs/                  # Data Transfer Objects
│   └── Services/              # Servicios de aplicación
│
├── Infrastructure/            # 🔌 ADAPTADORES
│   ├── Services/              # Implementaciones de servicios
│   ├── External/              # Servicios externos (Email, Slack)
│   ├── Persistence/           # Repositorios Doctrine
│   └── Config/                # Configuraciones
│
├── Presentation/              # 🎯 CAPA DE PRESENTACIÓN
│   ├── Controllers/           # Controladores HTTP
│   ├── Middleware/            # Middleware de Slim
│   └── Routes/                # Definición de rutas
│
└── Shared/                    # 🎯 CAPA COMPARTIDA
    ├── Container/             # Configuración DI
    └── Exceptions/            # Excepciones compartidas
```

## 🚀 Características

### **Framework & Tecnologías**
- **Slim Framework 4.15** - Micro-framework PHP
- **Doctrine ORM 3.5** - Mapeo objeto-relacional
- **PHP-DI 7.1** - Inyección de dependencias
- **Monolog 3.9** - Logging avanzado
- **Docker** - Contenedores para desarrollo

### **Funcionalidades Implementadas**
- ✅ **Health Check** - Monitoreo del sistema
- ✅ **Gestión de Usuarios** - CRUD completo
- ✅ **Notificaciones** - Email y Slack
- ✅ **Historial de Salud** - Persistencia de estados
- ✅ **Migraciones** - Automáticas con Doctrine
- ✅ **API REST** - Endpoints documentados

## 🛠️ Instalación

### **Como Paquete de Composer (Recomendado)**

```bash
# 1. Crear nuevo proyecto
mkdir mi-proyecto-slimseed
cd mi-proyecto-slimseed

# 2. Inicializar Composer
composer init

# 3. Instalar SlimSeed Framework
composer require slimseed/framework

# 4. ¡Listo! El framework se configura automáticamente
# 5. Levantar contenedores
docker-compose up -d

# 6. Ejecutar migraciones
composer run migrate

# 7. Visitar: http://localhost:8081
```

### **Desarrollo del Framework**

```bash
# 1. Clonar el repositorio
git clone <repository-url>
cd slim-seed-project

# 2. Instalar dependencias
composer install

# 3. Levantar contenedores
docker-compose up -d

# 4. Ejecutar migraciones
composer run migrate

# 5. ¡Listo! La API está en http://localhost:8081
```

### **Verificar Instalación**

```bash
# Probar endpoint de bienvenida
curl http://localhost:8081/

# Probar health check
curl http://localhost:8081/health
```

## 🔧 Configuración

### **Puertos del Sistema**
- **API**: http://localhost:8081
- **MySQL**: localhost:3307
- **Redis**: localhost:6380

### **Variables de Entorno**

```env
# Aplicación
APP_ENV=development
APP_DEBUG=true
APP_NAME="Slim Seed Project"

# Base de datos
DB_HOST=mysql
DB_PORT=3306
DB_NAME=slim_seed
DB_USER=slim_user
DB_PASS=slim_pass

# Notificaciones
NOTIFICATION_TYPE=email  # email | slack
ADMIN_EMAIL=admin@example.com
SLACK_WEBHOOK=https://hooks.slack.com/...

# Logging
LOG_LEVEL=debug
```

## 📚 API Documentation

### **Endpoints Principales**

#### **🏠 Bienvenida**
```http
GET /
```
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

#### **💚 Health Check**
```http
GET /health
```
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

### **👥 Gestión de Usuarios**

#### **Crear Usuario**
```http
POST /api/users
Content-Type: application/json

{
  "email": "test@example.com",
  "name": "Test User",
  "password": "password123"
}
```

#### **Obtener Usuarios Activos**
```http
GET /api/users/active
```

#### **Obtener Usuario por ID**
```http
GET /api/users/{id}
```

#### **Actualizar Nombre de Usuario**
```http
PUT /api/users/{id}/name
Content-Type: application/json

{
  "name": "Nuevo Nombre"
}
```

#### **Autenticar Usuario**
```http
POST /api/users/authenticate
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "password123"
}
```

### **📊 Historial de Salud**

#### **Último Estado**
```http
GET /api/health/latest
```

#### **Historial por Fechas**
```http
GET /api/health/history?from=2025-09-01&to=2025-09-30
```

### **🔔 Notificaciones**

#### **Enviar Alerta**
```http
POST /api/notifications/alert
Content-Type: application/json

{
  "message": "Sistema en mantenimiento",
  "context": {
    "duration": "2 hours"
  }
}
```

#### **Notificación de Prueba**
```http
POST /api/notifications/test
```

## 🏛️ Arquitectura Hexagonal

### **Puertos (Interfaces)**

```php
// Domain/Services/HealthCheckServiceInterface.php
interface HealthCheckServiceInterface
{
    public function checkHealth(): HealthCheckResult;
}

// Domain/Repositories/UserRepositoryInterface.php
interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
}
```

### **Adaptadores (Implementaciones)**

```php
// Infrastructure/Services/HealthCheckService.php
class HealthCheckService implements HealthCheckServiceInterface
{
    public function checkHealth(): HealthCheckResult
    {
        // Implementación específica
    }
}

// Infrastructure/Persistence/DoctrineUserRepository.php
class DoctrineUserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
```

### **Inyección de Dependencias**

```php
// Shared/Container/ContainerBuilder.php
HealthCheckServiceInterface::class => \DI\create(HealthCheckService::class)
UserRepositoryInterface::class => \DI\create(DoctrineUserRepository::class)
```

## 🗄️ Base de Datos

### **Entidades Doctrine**

#### **User Entity**
```php
#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isActive = true;
}
```

### **Migraciones**

```bash
# Crear esquema de base de datos
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/migrate.php"

# Resetear base de datos
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/reset-db.php"
```

### **Comandos de Desarrollo**

```bash
# Entrar al contenedor
docker-compose exec app bash

# Instalar dependencias
docker-compose exec -T app bash -c "cd /var/www/html && composer install"

# Ver logs
docker-compose logs -f app
```

## 🧪 Testing

### **Ejemplos de Pruebas**

```bash
# Crear usuario
curl -X POST http://localhost:8081/api/users \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","name":"Test User","password":"password123"}'

# Obtener usuarios activos
curl http://localhost:8081/api/users/active

# Probar autenticación
curl -X POST http://localhost:8081/api/users/authenticate \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

## 📦 Uso del Paquete

### **Instalación en Nuevo Proyecto**

```bash
# Crear proyecto
mkdir mi-api
cd mi-api
composer init

# Instalar SlimSeed Framework
composer require slimseed/framework

# Configurar (automático)
# Editar .env según necesidades
# docker-compose up -d
# composer run migrate
```

### **Comandos Disponibles**

```bash
# Migraciones
composer run migrate              # Ejecutar migraciones
composer run reset-db             # Resetear BD
composer run migrate:status       # Estado de migraciones

# Instalación
composer run slimseed:install     # Re-ejecutar instalador
```

### **Estructura Creada**

```
mi-proyecto/
├── .env                          # Variables de entorno
├── docker-compose.yml            # Configuración Docker
├── public/index.php              # Punto de entrada
├── src/                          # Código fuente del framework
├── migrations/                   # Migraciones de BD
└── vendor/slimseed/framework/    # Paquete instalado
```

## 📖 Guías

### **Instalación y Uso**
- [Guía de Instalación](docs/INSTALLATION.md)
- [Ejemplo de Uso](examples/quick-start.md)
- [API Documentation](docs/API.md)

### **Desarrollo**
- [Guía de Desarrollo](docs/development.md)
- [Arquitectura Hexagonal](docs/architecture.md)
- [Patrones DDD](docs/ddd-patterns.md)

### **Despliegue**
- [Docker Production](docs/docker-production.md)
- [Configuración de Servidor](docs/server-setup.md)

### **Contribución**
- [Guía de Contribución](docs/contributing.md)
- [Estándares de Código](docs/coding-standards.md)

## 🎯 Beneficios de esta Arquitectura

1. **Testabilidad** - Fácil mock de dependencias
2. **Mantenibilidad** - Separación clara de responsabilidades
3. **Flexibilidad** - Intercambio fácil de adaptadores
4. **Escalabilidad** - Estructura preparada para crecimiento
5. **Independencia** - El dominio no depende de infraestructura
6. **Productividad** - Desarrollo rápido con patrones establecidos

## 🚀 Próximos Pasos

- [ ] Autenticación JWT
- [ ] Validación de entrada con Respect/Validation
- [ ] Tests unitarios con PHPUnit
- [ ] Cache con Redis
- [ ] Documentación OpenAPI
- [ ] CI/CD con GitHub Actions

---

**¡Proyecto listo para desarrollo con Arquitectura Hexagonal completa!** 🎉

**Desarrollado con ❤️ usando Slim Framework + DDD + Doctrine ORM**