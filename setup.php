<?php

/**
 * Setup rápido de SlimSeed Framework
 * 
 * Uso: php setup.php
 */

echo "🚀 SlimSeed Framework - Setup Rápido\n";
echo "====================================\n\n";

// Verificar Composer
if (!file_exists('composer.json')) {
    echo "❌ Error: No se encontró composer.json\n";
    exit(1);
}

// Verificar paquete instalado
if (!file_exists('vendor/slimseed/framework')) {
    echo "❌ Error: SlimSeed Framework no está instalado\n";
    echo "   Ejecuta: composer require slimseed/framework:^0.2.2-beta\n";
    exit(1);
}

echo "✅ Instalando SlimSeed Framework...\n\n";

// Ejecutar instalador
require_once 'vendor/slimseed/framework/src/Installer/Installer.php';
SlimSeed\Installer\Installer::setupProject();

echo "\n🎉 ¡Setup completado!\n";
echo "📝 Ejecuta: docker-compose up -d\n";
echo "🌐 Visita: http://localhost:8081\n";
