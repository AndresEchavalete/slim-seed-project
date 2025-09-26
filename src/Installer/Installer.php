<?php

namespace SlimSeed\Installer;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Installer
{
    /**
     * Ejecuta después de la instalación del paquete
     */
    public static function postInstall(Event $event): void
    {
        self::setupProject($event);
    }

    /**
     * Ejecuta después de actualizar el paquete
     */
    public static function postUpdate(Event $event): void
    {
        self::setupProject($event);
    }

    /**
     * Configura el proyecto después de la instalación
     */
    private static function setupProject(Event $event): void
    {
        $io = $event->getIO();
        $projectRoot = getcwd();
        
        $io->write('<info>🚀 Configurando SlimSeed Framework...</info>');

        // Crear directorios necesarios
        self::createDirectories($projectRoot);
        
        // Copiar archivos de configuración
        self::copyConfigFiles($projectRoot, $io);
        
        // Crear archivo .env si no existe
        self::createEnvFile($projectRoot, $io);
        
        // Crear archivo index.php principal
        self::createIndexFile($projectRoot, $io);
        
        // Crear docker-compose.yml
        self::createDockerCompose($projectRoot, $io);
        
        $io->write('<info>✅ SlimSeed Framework configurado correctamente!</info>');
        $io->write('<comment>📝 Próximos pasos:</comment>');
        $io->write('<comment>   1. Configura las variables en .env</comment>');
        $io->write('<comment>   2. Ejecuta: docker-compose up -d</comment>');
        $io->write('<comment>   3. Ejecuta: composer run migrate</comment>');
        $io->write('<comment>   4. Visita: http://localhost:8081</comment>');
    }

    /**
     * Crea los directorios necesarios
     */
    private static function createDirectories(string $projectRoot): void
    {
        $directories = [
            'config',
            'public',
            'src',
            'migrations',
            'scripts',
            'tests',
            'docs',
            'docker'
        ];

        foreach ($directories as $dir) {
            $path = $projectRoot . '/' . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * Copia archivos de configuración
     */
    private static function copyConfigFiles(string $projectRoot, $io): void
    {
        $packageRoot = __DIR__ . '/../../';
        
        $files = [
            'doctrine-migrations.php' => 'doctrine-migrations.php',
            'migrations.php' => 'migrations.php',
            'docker/Dockerfile' => 'docker/Dockerfile',
            'scripts/migrate.php' => 'scripts/migrate.php',
            'scripts/reset-db.php' => 'scripts/reset-db.php',
        ];

        foreach ($files as $source => $destination) {
            $sourcePath = $packageRoot . $source;
            $destPath = $projectRoot . '/' . $destination;
            
            if (file_exists($sourcePath) && !file_exists($destPath)) {
                copy($sourcePath, $destPath);
                $io->write("  ✓ Copiado: {$destination}");
            }
        }
    }

    /**
     * Crea archivo .env
     */
    private static function createEnvFile(string $projectRoot, $io): void
    {
        $envPath = $projectRoot . '/.env';
        
        if (!file_exists($envPath)) {
            $envContent = self::getEnvTemplate();
            file_put_contents($envPath, $envContent);
            $io->write("  ✓ Creado: .env");
        }
    }

    /**
     * Crea archivo index.php principal
     */
    private static function createIndexFile(string $projectRoot, $io): void
    {
        $indexPath = $projectRoot . '/public/index.php';
        
        if (!file_exists($indexPath)) {
            $indexContent = self::getIndexTemplate();
            file_put_contents($indexPath, $indexContent);
            $io->write("  ✓ Creado: public/index.php");
        }
    }

    /**
     * Crea docker-compose.yml
     */
    private static function createDockerCompose(string $projectRoot, $io): void
    {
        $dockerPath = $projectRoot . '/docker-compose.yml';
        
        if (!file_exists($dockerPath)) {
            $dockerContent = self::getDockerComposeTemplate();
            file_put_contents($dockerPath, $dockerContent);
            $io->write("  ✓ Creado: docker-compose.yml");
        }
    }

    /**
     * Template para .env
     */
    private static function getEnvTemplate(): string
    {
        return <<<'ENV'
# Aplicación
APP_ENV=development
APP_DEBUG=true
APP_NAME="Mi Proyecto SlimSeed"

# Base de datos
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
ENV;
    }

    /**
     * Template para index.php
     */
    private static function getIndexTemplate(): string
    {
        return <<<'PHP'
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use SlimSeed\Shared\Container\ContainerBuilder;
use SlimSeed\Presentation\Routes\ApiRoutes;
use SlimSeed\Presentation\Middleware\CorsMiddleware;
use SlimSeed\Presentation\Middleware\ErrorHandlerMiddleware;

// Crear aplicación Slim con DI Container
$container = ContainerBuilder::build();
$app = AppFactory::createFromContainer($container);

// Agregar middleware
$app->add(new CorsMiddleware());
$app->add(new ErrorHandlerMiddleware());

// Registrar rutas
ApiRoutes::register($app);

// Ejecutar aplicación
$app->run();
PHP;
    }

    /**
     * Template para docker-compose.yml
     */
    private static function getDockerComposeTemplate(): string
    {
        return <<<'YAML'
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: docker/Dockerfile
    container_name: slimseed_app
    ports:
      - "8081:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - mysql
      - redis
    environment:
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_NAME=slim_seed
      - DB_USER=slim_user
      - DB_PASS=slim_pass

  mysql:
    image: mysql:8.0
    container_name: slimseed_mysql
    ports:
      - "3307:3306"
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: slim_seed
      MYSQL_USER: slim_user
      MYSQL_PASSWORD: slim_pass
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    container_name: slimseed_redis
    ports:
      - "6380:6379"

volumes:
  mysql_data:
YAML;
    }
}
