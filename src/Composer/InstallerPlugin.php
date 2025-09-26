<?php

namespace SlimSeed\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\Installer\InstallerEvent;
use Composer\Installer\InstallerEvents;

class InstallerPlugin implements PluginInterface, EventSubscriberInterface
{
    private $composer;
    private $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
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
            InstallerEvents::POST_DEPENDENCIES_SOLVING => 'onPostDependenciesSolving',
            PackageEvents::POST_PACKAGE_INSTALL => 'onPostPackageInstall',
            PackageEvents::POST_PACKAGE_UPDATE => 'onPostPackageUpdate',
        ];
    }

    public function onPostDependenciesSolving(InstallerEvent $event): void
    {
        // Se ejecuta antes de instalar dependencias
    }

    public function onPostPackageInstall(PackageEvent $event): void
    {
        $package = $event->getOperation()->getPackage();
        
        if ($package->getName() === 'slimseed/framework') {
            $this->runInstaller();
        }
    }

    public function onPostPackageUpdate(PackageEvent $event): void
    {
        $package = $event->getOperation()->getTargetPackage();
        
        if ($package->getName() === 'slimseed/framework') {
            $this->runInstaller();
        }
    }

    private function runInstaller(): void
    {
        $this->io->write('<info>🚀 SlimSeed Framework - Instalación automática</info>');
        
        try {
            // Verificar que estamos en un proyecto válido
            if (!file_exists('composer.json')) {
                $this->io->write('<comment>⚠️ No se encontró composer.json. Saltando instalación automática.</comment>');
                return;
            }

            // Verificar que el paquete está instalado
            if (!file_exists('vendor/slimseed/framework')) {
                $this->io->write('<comment>⚠️ SlimSeed Framework no está instalado. Saltando instalación automática.</comment>');
                return;
            }

            // Incluir el instalador
            $installerPath = 'vendor/slimseed/framework/src/Installer/Installer.php';
            
            if (file_exists($installerPath)) {
                require_once $installerPath;
                \SlimSeed\Installer\Installer::setupProject();
                
                $this->io->write('<info>✅ SlimSeed Framework configurado automáticamente!</info>');
                $this->io->write('<comment>📝 Próximos pasos:</comment>');
                $this->io->write('<comment>   1. Configura las variables en .env</comment>');
                $this->io->write('<comment>   2. Ejecuta: docker-compose up -d</comment>');
                $this->io->write('<comment>   3. Ejecuta: composer run migrate</comment>');
                $this->io->write('<comment>   4. Visita: http://localhost:8081</comment>');
            } else {
                $this->io->write('<comment>⚠️ Instalador no encontrado. Ejecuta manualmente: php vendor/slimseed/framework/install.php</comment>');
            }
        } catch (\Exception $e) {
            $this->io->write('<error>❌ Error en instalación automática: ' . $e->getMessage() . '</error>');
            $this->io->write('<comment>💡 Ejecuta manualmente: php vendor/slimseed/framework/install.php</comment>');
        }
    }
}
