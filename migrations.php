<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\DependencyFactory;

// Cargar variables de entorno
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Configuración de conexión
$connectionParams = [
    'dbname' => $_ENV['DB_NAME'] ?? 'slim_seed',
    'user' => $_ENV['DB_USER'] ?? 'slim_user',
    'password' => $_ENV['DB_PASS'] ?? 'slim_pass',
    'host' => $_ENV['DB_HOST'] ?? 'mysql',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'driver' => 'pdo_mysql',
];

$connection = DriverManager::getConnection($connectionParams);

// Configuración de migraciones
$config = new PhpFile('migrations-db.php');
$dependencyFactory = DependencyFactory::fromConnection($config, new ExistingConnection($connection));

// Ejecutar comando
$command = $argv[1] ?? 'status';

switch ($command) {
    case 'status':
        $dependencyFactory->getMigrationStatusCalculator()->getExecutedMigrationsCount();
        break;
    case 'migrate':
        $dependencyFactory->getMigrationRepository()->ensureInitialized();
        $migrations = $dependencyFactory->getMigrationRepository()->getMigrations();
        foreach ($migrations as $migration) {
            $migration->execute($connection);
        }
        echo "✅ Migrations executed successfully!\n";
        break;
    case 'generate':
        echo "Use: php vendor/bin/doctrine-migrations generate\n";
        break;
    default:
        echo "Available commands: status, migrate, generate\n";
}