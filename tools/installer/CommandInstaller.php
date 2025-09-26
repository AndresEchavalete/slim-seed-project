<?php

namespace SlimSeed\Installer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandInstaller extends Command
{
    protected static $defaultName = 'slimseed:install';
    protected static $defaultDescription = 'Instala y configura SlimSeed Framework en un nuevo proyecto';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('🚀 Instalador de SlimSeed Framework');
        $io->text('Configuraremos tu nuevo proyecto con arquitectura hexagonal + DDD');

        // Obtener información del proyecto
        $projectInfo = $this->gatherProjectInfo($io);
        
        // Crear estructura del proyecto
        $this->createProjectStructure($projectInfo, $io);
        
        // Configurar archivos
        $this->configureFiles($projectInfo, $io);
        
        // Configurar base de datos
        $this->setupDatabase($projectInfo, $io);
        
        // Mostrar resumen
        $this->showSummary($projectInfo, $io);

        return Command::SUCCESS;
    }

    private function gatherProjectInfo(SymfonyStyle $io): array
    {
        $helper = $this->getHelper('question');
        
        $projectName = $helper->ask($input, $output, new Question('Nombre del proyecto: '));
        
        // Preguntar tipo de base de datos
        $dbType = $io->choice('Tipo de base de datos', ['mysql', 'postgresql', 'sqlite'], 'mysql');
        
        $dbName = $helper->ask($input, $output, new Question('Nombre de la base de datos [slim_seed]: ', 'slim_seed'));
        
        $dbUser = 'slim_user';
        $dbPass = 'slim_pass';
        $dbHost = 'mysql';
        $dbPort = '3306';
        
        if ($dbType !== 'sqlite') {
            $dbUser = $helper->ask($input, $output, new Question('Usuario de BD [slim_user]: ', 'slim_user'));
            $dbPass = $helper->ask($input, $output, new Question('Contraseña de BD [slim_pass]: ', 'slim_pass'));
            
            if ($dbType === 'postgresql') {
                $dbHost = 'postgres';
                $dbPort = '5432';
            }
        }
        
        $adminEmail = $helper->ask($input, $output, new Question('Email del administrador: '));
        $notificationType = $io->choice('Tipo de notificaciones', ['email', 'slack'], 'email');
        
        // Preguntar si quiere usar Docker
        $useDocker = $io->confirm('¿Usar Docker para desarrollo? (recomendado)', true);
        
        // Si no usa Docker, preguntar configuración local
        if (!$useDocker) {
            $dbHost = $helper->ask($input, $output, new Question('Host de la base de datos [localhost]: ', 'localhost'));
            $dbPort = $helper->ask($input, $output, new Question("Puerto de la base de datos [{$dbPort}]: ", $dbPort));
        }

        return [
            'project_name' => $projectName,
            'db_type' => $dbType,
            'db_name' => $dbName,
            'db_user' => $dbUser,
            'db_pass' => $dbPass,
            'db_host' => $dbHost,
            'db_port' => $dbPort,
            'admin_email' => $adminEmail,
            'notification_type' => $notificationType,
            'use_docker' => $useDocker
        ];
    }

    private function createProjectStructure(array $projectInfo, SymfonyStyle $io): void
    {
        $io->section('📁 Creando estructura del proyecto...');
        
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
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                $io->text("  ✓ Creado: {$dir}/");
            }
        }
    }

    private function configureFiles(array $projectInfo, SymfonyStyle $io): void
    {
        $io->section('⚙️ Configurando archivos...');
        
        // Crear .env
        $this->createEnvFile($projectInfo);
        $io->text('  ✓ Creado: .env');
        
        // Crear docker-compose.yml solo si se usa Docker
        if ($projectInfo['use_docker']) {
            $this->createDockerCompose($projectInfo);
            $io->text('  ✓ Creado: docker-compose.yml');
        } else {
            $io->text('  ⏭️  Saltado: docker-compose.yml (Docker no seleccionado)');
        }
        
        // Crear index.php
        $this->createIndexFile();
        $io->text('  ✓ Creado: public/index.php');
        
        // Crear README.md
        $this->createReadme($projectInfo);
        $io->text('  ✓ Creado: README.md');
        
        // Crear archivos de configuración local si no usa Docker
        if (!$projectInfo['use_docker']) {
            $this->createLocalConfigFiles($projectInfo);
            $io->text('  ✓ Creados: archivos de configuración local');
        }
    }

    private function createEnvFile(array $projectInfo): void
    {
        $envContent = <<<ENV
# Aplicación
APP_ENV=development
APP_DEBUG=true
APP_NAME="{$projectInfo['project_name']}"

# Base de datos
DB_DRIVER={$projectInfo['db_type']}
DB_HOST={$projectInfo['db_host']}
DB_PORT={$projectInfo['db_port']}
DB_NAME={$projectInfo['db_name']}
DB_USER={$projectInfo['db_user']}
DB_PASS={$projectInfo['db_pass']}

# Notificaciones
NOTIFICATION_TYPE={$projectInfo['notification_type']}
ADMIN_EMAIL={$projectInfo['admin_email']}
SLACK_WEBHOOK=https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK

# Logging
LOG_LEVEL=debug
ENV;

        file_put_contents('.env', $envContent);
    }

    private function createDockerCompose(array $projectInfo): void
    {
        $projectName = strtolower(str_replace(' ', '_', $projectInfo['project_name']));
        $dbType = $projectInfo['db_type'];
        
        // Seleccionar plantilla según el tipo de BD
        $templateFile = match($dbType) {
            'postgresql' => 'templates/docker-compose.postgresql.yml',
            'sqlite' => 'templates/docker-compose.sqlite.yml',
            default => 'templates/docker-compose.mysql.yml'
        };
        
        if (file_exists($templateFile)) {
            $dockerContent = file_get_contents($templateFile);
            $dockerContent = str_replace('{{PROJECT_NAME}}', $projectName, $dockerContent);
            $dockerContent = str_replace('{{DB_NAME}}', $projectInfo['db_name'], $dockerContent);
            $dockerContent = str_replace('{{DB_USER}}', $projectInfo['db_user'], $dockerContent);
            $dockerContent = str_replace('{{DB_PASS}}', $projectInfo['db_pass'], $dockerContent);
        } else {
            // Fallback a MySQL si no existe la plantilla
            $dockerContent = $this->getDefaultDockerCompose($projectName, $projectInfo);
        }

        file_put_contents('docker-compose.yml', $dockerContent);
    }
    
    private function getDefaultDockerCompose(string $projectName, array $projectInfo): string
    {
        return <<<YAML
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: docker/Dockerfile
    container_name: {$projectName}_app
    ports:
      - "8081:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - mysql
      - redis
    environment:
      - DB_DRIVER={$projectInfo['db_type']}
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_NAME={$projectInfo['db_name']}
      - DB_USER={$projectInfo['db_user']}
      - DB_PASS={$projectInfo['db_pass']}

  mysql:
    image: mysql:8.0
    container_name: {$projectName}_mysql
    ports:
      - "3307:3306"
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: {$projectInfo['db_name']}
      MYSQL_USER: {$projectInfo['db_user']}
      MYSQL_PASSWORD: {$projectInfo['db_pass']}
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    container_name: {$projectName}_redis
    ports:
      - "6380:6379"

volumes:
  mysql_data:
YAML;
    }

    private function createIndexFile(): void
    {
        $indexContent = <<<PHP
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use SlimSeed\Shared\Container\ContainerBuilder;
use SlimSeed\Presentation\Routes\ApiRoutes;
use SlimSeed\Presentation\Middleware\CorsMiddleware;
use SlimSeed\Presentation\Middleware\ErrorHandlerMiddleware;

// Crear aplicación Slim con DI Container
\$container = ContainerBuilder::build();
\$app = AppFactory::createFromContainer(\$container);

// Agregar middleware
\$app->add(new CorsMiddleware());
\$app->add(new ErrorHandlerMiddleware());

// Registrar rutas
ApiRoutes::register(\$app);

// Ejecutar aplicación
\$app->run();
PHP;

        file_put_contents('public/index.php', $indexContent);
    }

    private function createReadme(array $projectInfo): void
    {
        $readmeContent = <<<MD
# {$projectInfo['project_name']}

Proyecto creado con **SlimSeed Framework** - Arquitectura Hexagonal + DDD + Doctrine ORM

## 🚀 Inicio Rápido

### 1. Levantar Contenedores
\`\`\`bash
docker-compose up -d
\`\`\`

### 2. Ejecutar Migraciones
\`\`\`bash
composer run migrate
\`\`\`

### 3. ¡Listo!
Visita: http://localhost:8081

## 🏗️ Estructura del Proyecto

\`\`\`
src/
├── Domain/                    # Capa de Dominio
├── Application/               # Capa de Aplicación
├── Infrastructure/            # Adaptadores
├── Presentation/              # Capa de Presentación
└── Shared/                    # Capa Compartida
\`\`\`

## 🛠️ Comandos Útiles

\`\`\`bash
# Migraciones
composer run migrate
composer run reset-db

# Desarrollo
docker-compose exec app bash
docker-compose logs -f app
\`\`\`

---

**Desarrollado con ❤️ usando SlimSeed Framework**
MD;

        file_put_contents('README.md', $readmeContent);
    }

    private function setupDatabase(array $projectInfo, SymfonyStyle $io): void
    {
        $io->section('🗄️ Configurando base de datos...');
        
        // Crear archivo de migración inicial
        $migrationContent = <<<PHP
<?php

declare(strict_types=1);

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migración inicial para {$projectInfo['project_name']}';
    }

    public function up(Schema \$schema): void
    {
        // Crear tabla de usuarios
        \$table = \$schema->createTable('users');
        \$table->addColumn('id', 'integer', ['autoincrement' => true]);
        \$table->addColumn('email', 'string', ['length' => 100]);
        \$table->addColumn('name', 'string', ['length' => 255]);
        \$table->addColumn('is_active', 'boolean', ['default' => true]);
        \$table->addColumn('created_at', 'datetime');
        \$table->addColumn('updated_at', 'datetime');
        \$table->setPrimaryKey(['id']);
        \$table->addUniqueIndex(['email']);

        // Crear tabla de historial de salud
        \$healthTable = \$schema->createTable('health_status');
        \$healthTable->addColumn('id', 'integer', ['autoincrement' => true]);
        \$healthTable->addColumn('status', 'string', ['length' => 50]);
        \$healthTable->addColumn('message', 'text');
        \$healthTable->addColumn('checks', 'json');
        \$healthTable->addColumn('created_at', 'datetime');
        \$healthTable->setPrimaryKey(['id']);
    }

    public function down(Schema \$schema): void
    {
        \$schema->dropTable('health_status');
        \$schema->dropTable('users');
    }
}
PHP;

        $migrationFile = 'migrations/Version' . date('YmdHis') . '.php';
        file_put_contents($migrationFile, $migrationContent);
        $io->text("  ✓ Creado: {$migrationFile}");
    }

    private function showSummary(array $projectInfo, SymfonyStyle $io): void
    {
        $io->success('🎉 ¡Instalación completada!');
        
        $io->section('📋 Resumen del Proyecto');
        $io->table(
            ['Configuración', 'Valor'],
            [
                ['Nombre del Proyecto', $projectInfo['project_name']],
                ['Tipo de BD', $projectInfo['db_type']],
                ['Base de Datos', $projectInfo['db_name']],
                ['Usuario BD', $projectInfo['db_user']],
                ['Host BD', $projectInfo['db_host']],
                ['Puerto BD', $projectInfo['db_port']],
                ['Email Admin', $projectInfo['admin_email']],
                ['Notificaciones', $projectInfo['notification_type']],
            ]
        );

        $io->section('🚀 Próximos Pasos');
        
        if ($projectInfo['use_docker']) {
            $io->listing([
                'Levantar contenedores: docker-compose up -d',
                'Ejecutar migraciones: composer run migrate',
                'Visitar la aplicación: http://localhost:8081',
                'Probar health check: http://localhost:8081/health',
                'Revisar documentación: docs/API.md'
            ]);
        } else {
            $steps = [
                'Configurar base de datos local',
                'Ejecutar migraciones: composer run migrate',
                'Iniciar servidor: php -S localhost:8000 -t public',
                'Visitar la aplicación: http://localhost:8000',
                'Probar health check: http://localhost:8000/health',
                'Revisar documentación: docs/API.md'
            ];
            
            if ($projectInfo['db_type'] === 'sqlite') {
                $steps[0] = 'Base de datos SQLite lista (archivo en data/)';
            }
            
            $io->listing($steps);
        }

        if ($projectInfo['use_docker']) {
            $io->note('💡 Tip: Usa "docker-compose exec app bash" para acceder al contenedor');
        } else {
            $io->note('💡 Tip: Usa "php -S localhost:8000 -t public" para servidor de desarrollo');
        }
    }

    /**
     * Crea archivos de configuración para instalación local
     */
    private function createLocalConfigFiles(array $projectInfo): void
    {
        // Crear .htaccess
        if (file_exists('templates/.htaccess')) {
            copy('templates/.htaccess', '.htaccess');
        }

        // Crear nginx.conf
        if (file_exists('templates/nginx.conf')) {
            copy('templates/nginx.conf', 'nginx.conf');
        }

        // Crear directorio data para SQLite
        if ($projectInfo['db_type'] === 'sqlite') {
            if (!is_dir('data')) {
                mkdir('data', 0755, true);
            }
        }
    }
}
