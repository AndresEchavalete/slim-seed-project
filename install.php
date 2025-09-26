<?php

/**
 * Instalador de SlimSeed Framework
 * 
 * Uso: php install.php
 * 
 * Este script debe ejecutarse después de instalar el paquete
 */

echo "🚀 SlimSeed Framework - Instalador\n";
echo "==================================\n\n";

// Verificar que estamos en un proyecto con Composer
if (!file_exists('composer.json')) {
    echo "❌ Error: No se encontró composer.json en el directorio actual.\n";
    echo "   Asegúrate de ejecutar este script en la raíz de tu proyecto.\n";
    exit(1);
}

// Verificar que el paquete está instalado
if (!file_exists('vendor/slimseed/framework')) {
    echo "❌ Error: SlimSeed Framework no está instalado.\n";
    echo "   Ejecuta primero: composer require slimseed/framework:^0.2.2-beta\n";
    exit(1);
}

echo "✅ SlimSeed Framework detectado en vendor/\n\n";

// Incluir el instalador
require_once 'vendor/slimseed/framework/src/Installer/Installer.php';

try {
    // Ejecutar el instalador
    SlimSeed\Installer\Installer::setupProject();
    
    echo "\n🎉 ¡Instalación completada exitosamente!\n\n";
    echo "📝 Próximos pasos:\n";
    echo "   1. Configura las variables en .env\n";
    echo "   2. Ejecuta: docker-compose up -d\n";
    echo "   3. Ejecuta: composer run migrate\n";
    echo "   4. Visita: http://localhost:8081\n\n";
    echo "📚 Documentación: https://github.com/AndresEchavalete/slim-seed-project\n";
    
} catch (Exception $e) {
    echo "❌ Error durante la instalación: " . $e->getMessage() . "\n";
    exit(1);
}
