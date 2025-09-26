<?php

namespace SlimSeed\Shared\Container;

use DI\ContainerBuilder as DIContainerBuilder;
use PDO;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Doctrine\ORM\EntityManagerInterface;

// Domain Interfaces
use SlimSeed\Domain\Services\HealthCheckServiceInterface;
use SlimSeed\Domain\Services\NotificationServiceInterface;
use SlimSeed\Domain\Repositories\HealthStatusRepositoryInterface;
use SlimSeed\Domain\Repositories\UserRepositoryInterface;

// Infrastructure Adapters
use SlimSeed\Infrastructure\Services\HealthCheckService;
use SlimSeed\Infrastructure\External\EmailNotificationService;
use SlimSeed\Infrastructure\External\SlackNotificationService;
use SlimSeed\Infrastructure\Persistence\HealthStatusRepository;
use SlimSeed\Infrastructure\Persistence\DoctrineHealthStatusRepository;
use SlimSeed\Infrastructure\Persistence\DoctrineUserRepository;
use SlimSeed\Infrastructure\Config\DatabaseConfig;
use SlimSeed\Infrastructure\Config\DoctrineConfig;
use SlimSeed\Infrastructure\Config\NotificationConfig;

// Application Use Cases
use SlimSeed\Application\UseCases\HealthCheckUseCase;
use SlimSeed\Application\UseCases\HealthHistoryUseCase;
use SlimSeed\Application\UseCases\NotificationUseCase;
use SlimSeed\Application\UseCases\UserUseCase;

// Presentation Controllers
use SlimSeed\Presentation\Controllers\HealthController;
use SlimSeed\Presentation\Controllers\WelcomeController;
use SlimSeed\Presentation\Controllers\HealthHistoryController;
use SlimSeed\Presentation\Controllers\NotificationController;
use SlimSeed\Presentation\Controllers\UserController;

/**
 * Constructor del contenedor de inyección de dependencias
 * Configura todos los puertos y adaptadores de la arquitectura hexagonal
 */
class ContainerBuilder
{
    public static function build(): \DI\Container
    {
        $containerBuilder = new DIContainerBuilder();
        
        // Configuración de servicios
        $containerBuilder->addDefinitions([
            
            // === CONFIGURACIONES ===
            'database.config' => \DI\factory(function () {
                return new DatabaseConfig([
                    'host' => $_ENV['DB_HOST'] ?? 'mysql',
                    'port' => $_ENV['DB_PORT'] ?? '3306',
                    'database' => $_ENV['DB_NAME'] ?? 'slim_seed',
                    'username' => $_ENV['DB_USER'] ?? 'slim_user',
                    'password' => $_ENV['DB_PASS'] ?? 'slim_pass',
                ]);
            }),
            
            'notification.config' => \DI\factory(function () {
                return new NotificationConfig(
                    [
                        'admin_email' => $_ENV['ADMIN_EMAIL'] ?? 'admin@example.com',
                    ],
                    [
                        'webhook_url' => $_ENV['SLACK_WEBHOOK'] ?? '',
                    ]
                );
            }),
            
            // === INFRAESTRUCTURA ===
            'doctrine.config' => \DI\factory(function () {
                return new DoctrineConfig([
                    'host' => $_ENV['DB_HOST'] ?? 'mysql',
                    'port' => $_ENV['DB_PORT'] ?? '3306',
                    'database' => $_ENV['DB_NAME'] ?? 'slim_seed',
                    'username' => $_ENV['DB_USER'] ?? 'slim_user',
                    'password' => $_ENV['DB_PASS'] ?? 'slim_pass',
                    'debug' => $_ENV['APP_DEBUG'] ?? false,
                ]);
            }),

            EntityManagerInterface::class => \DI\factory(function (\DI\Container $c) {
                $doctrineConfig = $c->get('doctrine.config');
                return $doctrineConfig->createEntityManager();
            }),

            PDO::class => \DI\factory(function (\DI\Container $c) {
                $dbConfig = $c->get('database.config');
                $pdo = $dbConfig->createConnection();
                $dbConfig->createHealthStatusTable();
                return $pdo;
            }),
            
            LoggerInterface::class => \DI\factory(function () {
                $logger = new Logger('app');
                $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
                return $logger;
            }),
            
            // === PUERTOS E INTERFACES ===
            // Domain Services
            HealthCheckServiceInterface::class => \DI\create(HealthCheckService::class)
                ->constructor(\DI\get(HealthStatusRepositoryInterface::class)),
            
            NotificationServiceInterface::class => \DI\factory(function (\DI\Container $c) {
                $logger = $c->get(LoggerInterface::class);
                $config = $c->get('notification.config');
                
                // Se puede cambiar fácilmente entre Email y Slack
                $notificationType = $_ENV['NOTIFICATION_TYPE'] ?? 'email';
                
                if ($notificationType === 'slack') {
                    return new SlackNotificationService($logger, $config->getSlackConfig());
                }
                
                return new EmailNotificationService($logger, $config->getEmailConfig());
            }),
            
            // Domain Repositories
            HealthStatusRepositoryInterface::class => \DI\create(DoctrineHealthStatusRepository::class)
                ->constructor(\DI\get(EntityManagerInterface::class)),
            
            UserRepositoryInterface::class => \DI\create(DoctrineUserRepository::class)
                ->constructor(\DI\get(EntityManagerInterface::class)),
            
            // === CASOS DE USO ===
            HealthCheckUseCase::class => \DI\create()
                ->constructor(\DI\get(HealthCheckServiceInterface::class)),
            
            HealthHistoryUseCase::class => \DI\create()
                ->constructor(\DI\get(HealthStatusRepositoryInterface::class)),
            
            NotificationUseCase::class => \DI\create()
                ->constructor(\DI\get(NotificationServiceInterface::class)),
            
            UserUseCase::class => \DI\create()
                ->constructor(\DI\get(UserRepositoryInterface::class)),
            
            // === CONTROLADORES ===
            HealthController::class => \DI\create()
                ->constructor(\DI\get(HealthCheckUseCase::class)),
            
            HealthHistoryController::class => \DI\create()
                ->constructor(\DI\get(HealthHistoryUseCase::class)),
            
            NotificationController::class => \DI\create()
                ->constructor(\DI\get(NotificationUseCase::class)),
            
            UserController::class => \DI\create()
                ->constructor(\DI\get(UserUseCase::class)),
            
            WelcomeController::class => \DI\create(),
        ]);

        return $containerBuilder->build();
    }
}