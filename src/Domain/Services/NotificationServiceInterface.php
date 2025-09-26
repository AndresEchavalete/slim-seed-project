<?php

namespace SlimSeed\Domain\Services;

use SlimSeed\Domain\ValueObjects\HealthCheckResult;

/**
 * Puerto para notificaciones del sistema
 * Define el contrato que deben cumplir los adaptadores de notificación
 */
interface NotificationServiceInterface
{
    /**
     * Envía notificación de estado de salud
     * 
     * @param HealthCheckResult $healthResult
     * @return void
     */
    public function sendHealthNotification(HealthCheckResult $healthResult): void;

    /**
     * Envía notificación de alerta
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    public function sendAlert(string $message, array $context = []): void;
}
