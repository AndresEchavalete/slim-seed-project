<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use SlimSeed\Infrastructure\Config\DoctrineConfig;

// Cargar variables de entorno
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

echo "🔄 Reseteando base de datos...\n";

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

    // Eliminar esquema de base de datos
    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
    $classes = [
        $entityManager->getClassMetadata(\SlimSeed\Infrastructure\Persistence\Doctrine\HealthStatusEntity::class),
        $entityManager->getClassMetadata(\SlimSeed\Infrastructure\Persistence\Doctrine\UserEntity::class),
    ];

    $schemaTool->dropSchema($classes);
    echo "✅ Base de datos reseteada correctamente!\n";
    
    // Recrear esquema
    $schemaTool->createSchema($classes);
    echo "✅ Esquema recreado correctamente!\n";
    
} catch (Exception $e) {
    echo "❌ Error reseteando base de datos: " . $e->getMessage() . "\n";
    exit(1);
}