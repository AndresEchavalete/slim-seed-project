<?php

namespace SlimSeed\Infrastructure\Config;

/**
 * Configuración de notificaciones
 * Adaptador para configurar servicios de notificación
 */
class NotificationConfig
{
    private array $emailConfig;
    private array $slackConfig;

    public function __construct(array $emailConfig = [], array $slackConfig = [])
    {
        $this->emailConfig = $emailConfig;
        $this->slackConfig = $slackConfig;
    }

    public function getEmailConfig(): array
    {
        return array_merge([
            'admin_email' => 'admin@example.com',
            'smtp_host' => 'localhost',
            'smtp_port' => 587,
            'smtp_username' => '',
            'smtp_password' => '',
        ], $this->emailConfig);
    }

    public function getSlackConfig(): array
    {
        return array_merge([
            'webhook_url' => '',
            'channel' => '#alerts',
            'username' => 'Health Monitor',
        ], $this->slackConfig);
    }
}
