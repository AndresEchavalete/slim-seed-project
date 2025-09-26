<?php

/**
 * Instalador Simple para SlimSeed Framework
 * 
 * Uso: php tools/simple-installer.php
 */

echo "🚀 SlimSeed Framework - Instalador Simple\n";
echo "==========================================\n\n";

// Crear archivo .env si no existe
if (!file_exists('.env')) {
    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        echo "✅ Creado archivo .env desde .env.example\n";
    } else {
        echo "❌ No se encontró .env.example\n";
        exit(1);
    }
} else {
    echo "ℹ️  El archivo .env ya existe\n";
}

// Crear directorio public si no existe
if (!is_dir('public')) {
    mkdir('public', 0755, true);
    echo "✅ Creado directorio public/\n";
}

// Crear index.php si no existe
$indexPath = 'public/index.php';
if (!file_exists($indexPath)) {
    $indexContent = <<<'PHP'
<?php

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
PHP;
    
    file_put_contents($indexPath, $indexContent);
    echo "✅ Creado public/index.php\n";
} else {
    echo "ℹ️  El archivo public/index.php ya existe\n";
}

// Crear directorio data para SQLite si es necesario
if (!is_dir('data')) {
    mkdir('data', 0755, true);
    echo "✅ Creado directorio data/ para SQLite\n";
}

echo "\n🎉 ¡Instalación completada!\n\n";
echo "Próximos pasos:\n";
echo "1. Editar .env con tu configuración\n";
echo "2. Ejecutar: composer run migrate\n";
echo "3. Iniciar servidor: php -S localhost:8000 -t public\n";
echo "4. Visitar: http://localhost:8000\n\n";
echo "Para documentación completa: https://github.com/AndresEchavalete/slim-seed-project\n";
