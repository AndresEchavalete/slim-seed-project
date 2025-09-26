#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
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

echo "🚀 Running Doctrine Migrations...\n\n";

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

    $entityManager = $doctrineConfig->createEntityManager();

    // Crear esquema de base de datos
    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
    $classes = [
        $entityManager->getClassMetadata(\SlimSeed\Infrastructure\Persistence\Doctrine\HealthStatusEntity::class),
        $entityManager->getClassMetadata(\SlimSeed\Infrastructure\Persistence\Doctrine\UserEntity::class),
    ];

    echo "📋 Creating database schema...\n";
    $schemaTool->createSchema($classes);
    echo "✅ Database schema created successfully!\n\n";

    echo "📊 Schema includes:\n";
    echo "   - health_status table\n";
    echo "   - users table\n";
    echo "   - doctrine_migration_versions table\n\n";

    echo "🎉 Migration completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error during migration: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
