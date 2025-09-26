# Correcciones para el Instalador de Slim Seed Framework

## Problemas Identificados

### 1. Scripts de Composer Faltantes
El `composer.json` del framework necesita los scripts de post-install:

```json
{
    "scripts": {
        "post-install-cmd": [
            "SlimSeed\\Installer\\Installer::postInstall"
        ],
        "post-update-cmd": [
            "SlimSeed\\Installer\\Installer::postUpdate"
        ],
        "migrate": "php vendor/bin/doctrine-migrations migrate",
        "migrate:status": "php vendor/bin/doctrine-migrations status",
        "migrate:generate": "php vendor/bin/doctrine-migrations generate",
        "migrate:execute": "php vendor/bin/doctrine-migrations execute"
    }
}
```

### 2. Archivo .env.example Faltante
Crear archivo `.env.example` en la raíz del paquete:

```env
# Aplicación
APP_ENV=development
APP_DEBUG=true
APP_NAME="Mi Proyecto SlimSeed"

# Base de datos
DB_DRIVER=mysql
DB_HOST=mysql
DB_PORT=3306
DB_NAME=slim_seed
DB_USER=slim_user
DB_PASS=slim_pass

# Notificaciones
NOTIFICATION_TYPE=email
ADMIN_EMAIL=admin@example.com
SLACK_WEBHOOK=https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK

# Logging
LOG_LEVEL=debug
```

### 3. Configuración de Migraciones
Crear archivo `migrations-db.php`:

```php
<?php

use Doctrine\DBAL\DriverManager;

return DriverManager::getConnection([
    'dbname' => $_ENV['DB_NAME'] ?? 'slim_seed',
    'user' => $_ENV['DB_USER'] ?? 'slim_user',
    'password' => $_ENV['DB_PASS'] ?? 'slim_pass',
    'host' => $_ENV['DB_HOST'] ?? 'mysql',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'driver' => 'pdo_mysql',
]);
```

### 4. Mejorar el Instalador
El instalador actual tiene algunos problemas:

- No copia la estructura `src/` completa
- No crea el archivo de configuración de migraciones
- No verifica dependencias

### 5. Dockerfile Mejorado
El Dockerfile actual es básico, necesita:

- Configuración de PHP-FPM
- Nginx como servidor web
- Configuración de permisos correcta

## Archivos a Modificar

1. `composer.json` - Agregar scripts de post-install
2. `.env.example` - Crear archivo de ejemplo
3. `migrations-db.php` - Configuración de migraciones
4. `tools/installer/Installer.php` - Mejorar lógica
5. `Dockerfile` - Mejorar configuración
6. `docker-compose.yml` - Template mejorado

## Estructura de Archivos Necesaria

```
slimseed/framework/
├── .env.example
├── migrations-db.php
├── composer.json (con scripts)
├── tools/
│   └── installer/
│       └── Installer.php (mejorado)
├── templates/
│   ├── docker-compose.yml.template
│   ├── Dockerfile.template
│   └── nginx.conf.template
└── src/ (estructura completa)
```

## Comandos de Instalación

Después de las correcciones, la instalación sería:

```bash
composer require slimseed/framework:^0.2.1-beta
# El instalador se ejecuta automáticamente
docker-compose up -d
composer run migrate
```

## Testing

Para probar las correcciones:

1. Crear un proyecto nuevo
2. Ejecutar `composer require slimseed/framework:^0.2.1-beta`
3. Verificar que se crean todos los archivos automáticamente
4. Verificar que `docker-compose up -d` funciona
5. Verificar que la API responde correctamente
