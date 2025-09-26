#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use SlimSeed\Infrastructure\Config\DoctrineConfig;

// Cargar variables de entorno
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

echo "⚠️  Resetting database...\n\n";

try {
    // Configuración de Doctrine
    $doctrineConfig = new DoctrineConfig([
        'host' => $_ENV['DB_HOST'] ?? 'mysql',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'database' => $_ENV['DB_NAME'] ?? 'slim_seed',
        'username' => $_ENV['DB_USER'] ?? 'slim_user',
        'password' => $_ENV['DB_PASS'] ?? 'slim_pass',
        'debug' => $_ENV['APP_DEBUG'] ?? false,
    ]);

    $connection = $doctrineConfig->createConnection();

    // Eliminar todas las tablas
    $tables = ['health_status', 'users', 'doctrine_migration_versions'];
    
    foreach ($tables as $table) {
        try {
            $connection->executeStatement("DROP TABLE IF EXISTS `$table`");
            echo "🗑️  Dropped table: $table\n";
        } catch (Exception $e) {
            echo "⚠️  Could not drop table $table: " . $e->getMessage() . "\n";
        }
    }

    echo "\n✅ Database reset completed!\n";
    echo "💡 Run 'php scripts/migrate.php' to recreate the schema.\n";

} catch (Exception $e) {
    echo "❌ Error during reset: " . $e->getMessage() . "\n";
    exit(1);
}
