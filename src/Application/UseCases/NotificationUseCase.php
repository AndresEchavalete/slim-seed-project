<?php

namespace SlimSeed\Application\UseCases;

use SlimSeed\Domain\Services\NotificationServiceInterface;
use SlimSeed\Domain\ValueObjects\HealthCheckResult;

/**
 * Caso de uso para manejo de notificaciones
 * Demuestra el uso de servicios de notificación en la capa de aplicación
 */
class NotificationUseCase
{
    private NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Envía notificación de estado de salud
     * 
     * @param HealthCheckResult $healthResult
     * @return void
     */
    public function sendHealthNotification(HealthCheckResult $healthResult): void
    {
        $this->notificationService->sendHealthNotification($healthResult);
    }

    /**
     * Envía alerta del sistema
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    public function sendAlert(string $message, array $context = []): void
    {
        $this->notificationService->sendAlert($message, $context);
    }
}
