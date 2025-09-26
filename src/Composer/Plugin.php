<?php

namespace SlimSeed\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io): void
    {
        // Plugin activado
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // Plugin desactivado
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // Plugin desinstalado
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => 'onPostPackageInstall',
            PackageEvents::POST_PACKAGE_UPDATE => 'onPostPackageUpdate',
        ];
    }

    public function onPostPackageInstall(PackageEvent $event): void
    {
        $package = $event->getOperation()->getPackage();
        
        if ($package->getName() === 'slimseed/framework') {
            $this->runInstaller($event->getIO());
        }
    }

    public function onPostPackageUpdate(PackageEvent $event): void
    {
        $package = $event->getOperation()->getTargetPackage();
        
        if ($package->getName() === 'slimseed/framework') {
            $this->runInstaller($event->getIO());
        }
    }

    private function runInstaller(IOInterface $io): void
    {
        $io->write('<info>🚀 Ejecutando instalador automático de SlimSeed Framework...</info>');
        
        try {
            // Incluir el instalador
            $installerPath = __DIR__ . '/../Installer/Installer.php';
            
            if (file_exists($installerPath)) {
                require_once $installerPath;
                \SlimSeed\Installer\Installer::setupProject();
                $io->write('<info>✅ SlimSeed Framework configurado automáticamente!</info>');
            } else {
                $io->write('<comment>⚠️ Instalador no encontrado. Ejecuta manualmente: php vendor/slimseed/framework/install.php</comment>');
            }
        } catch (\Exception $e) {
            $io->write('<error>❌ Error en instalación automática: ' . $e->getMessage() . '</error>');
            $io->write('<comment>💡 Ejecuta manualmente: php vendor/slimseed/framework/install.php</comment>');
        }
    }
}
