# SlimSeed Framework

Framework PHP con arquitectura hexagonal, DDD y Doctrine ORM para desarrollo rápido de APIs.

## Instalación

```bash
composer require slimseed/framework
```

## Uso Básico

### 1. Configurar Variables de Entorno

```bash
cp .env.example .env
# Editar .env con tu configuración
```

### 2. Crear Archivo de Entrada

```php
<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use SlimSeed\Shared\Container\ContainerBuilder;
use SlimSeed\Presentation\Routes\ApiRoutes;
use SlimSeed\Presentation\Middleware\CorsMiddleware;
use SlimSeed\Presentation\Middleware\ErrorHandlerMiddleware;

$container = ContainerBuilder::build();
$app = AppFactory::createFromContainer($container);

$app->add(new CorsMiddleware());
$app->add(new ErrorHandlerMiddleware());

ApiRoutes::register($app);

$app->run();
```

### 3. Configurar Base de Datos

```env
# .env
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=mi_proyecto
DB_USER=root
DB_PASS=password
```

### 4. Ejecutar Migraciones

```bash
composer run migrate
```

### 5. Iniciar Servidor

```bash
php -S localhost:8000 -t public
```

## Características

- ✅ Arquitectura Hexagonal
- ✅ Domain Driven Design (DDD)
- ✅ Doctrine ORM
- ✅ Inyección de Dependencias
- ✅ Soporte Multi-BD (MySQL, PostgreSQL, SQLite)
- ✅ API REST
- ✅ Health Check
- ✅ Sistema de Notificaciones

## Documentación Completa

Para documentación detallada, ejemplos y guías de instalación avanzada, visita: [Documentación Completa](https://github.com/AndresEchavalete/slim-seed-project)

## Licencia

MIT
