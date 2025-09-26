<?php

namespace SlimSeed\Installer;

use Composer\Script\Event;

class AutoInstaller
{
    /**
     * Se ejecuta automáticamente después de la instalación
     */
    public static function postInstall(Event $event): void
    {
        $io = $event->getIO();
        $io->write('<info>🚀 SlimSeed Framework - Instalación automática</info>');
        
        try {
            // Verificar que estamos en un proyecto válido
            if (!file_exists('composer.json')) {
                $io->write('<comment>⚠️ No se encontró composer.json. Saltando instalación automática.</comment>');
                return;
            }

            // Verificar que el paquete está instalado
            if (!file_exists('vendor/slimseed/framework')) {
                $io->write('<comment>⚠️ SlimSeed Framework no está instalado. Saltando instalación automática.</comment>');
                return;
            }

            // Ejecutar el instalador
            Installer::setupProject($event);
            
            $io->write('<info>✅ SlimSeed Framework configurado automáticamente!</info>');
            $io->write('<comment>📝 Próximos pasos:</comment>');
            $io->write('<comment>   1. Configura las variables en .env</comment>');
            $io->write('<comment>   2. Ejecuta: docker-compose up -d</comment>');
            $io->write('<comment>   3. Ejecuta: composer run migrate</comment>');
            $io->write('<comment>   4. Visita: http://localhost:8081</comment>');
            
        } catch (\Exception $e) {
            $io->write('<error>❌ Error en instalación automática: ' . $e->getMessage() . '</error>');
            $io->write('<comment>💡 Ejecuta manualmente: php vendor/slimseed/framework/install.php</comment>');
        }
    }

    /**
     * Se ejecuta automáticamente después de la actualización
     */
    public static function postUpdate(Event $event): void
    {
        self::postInstall($event);
    }
}
