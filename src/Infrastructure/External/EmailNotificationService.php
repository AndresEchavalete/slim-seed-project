<?php

namespace SlimSeed\Infrastructure\External;

use SlimSeed\Domain\Services\NotificationServiceInterface;
use SlimSeed\Domain\ValueObjects\HealthCheckResult;
use Psr\Log\LoggerInterface;

/**
 * Adaptador de notificación por email
 * Implementa el puerto NotificationServiceInterface
 */
class EmailNotificationService implements NotificationServiceInterface
{
    private LoggerInterface $logger;
    private array $config;

    public function __construct(LoggerInterface $logger, array $config = [])
    {
        $this->logger = $logger;
        $this->config = $config;
    }

    public function sendHealthNotification(HealthCheckResult $healthResult): void
    {
        $subject = $healthResult->isHealthy() 
            ? 'Sistema Operativo' 
            : 'Alerta del Sistema';

        $message = sprintf(
            "Estado del sistema: %s\nMensaje: %s\nVerificaciones: %s",
            $healthResult->isHealthy() ? 'Saludable' : 'Con problemas',
            $healthResult->getMessage(),
            json_encode($healthResult->getChecks(), JSON_PRETTY_PRINT)
        );

        $this->logger->info('Enviando notificación de salud', [
            'healthy' => $healthResult->isHealthy(),
            'message' => $message
        ]);

        // Aquí se implementaría el envío real de email
        // mail($this->config['admin_email'], $subject, $message);
    }

    public function sendAlert(string $message, array $context = []): void
    {
        $this->logger->error('Alerta del sistema', [
            'message' => $message,
            'context' => $context
        ]);

        // Aquí se implementaría el envío real de alerta por email
        // mail($this->config['admin_email'], 'Alerta del Sistema', $message);
    }
}
