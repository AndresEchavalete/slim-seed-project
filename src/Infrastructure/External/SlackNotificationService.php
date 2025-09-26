<?php

namespace SlimSeed\Infrastructure\External;

use SlimSeed\Domain\Services\NotificationServiceInterface;
use SlimSeed\Domain\ValueObjects\HealthCheckResult;
use Psr\Log\LoggerInterface;

/**
 * Adaptador de notificación por Slack
 * Implementa el puerto NotificationServiceInterface
 */
class SlackNotificationService implements NotificationServiceInterface
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
        $emoji = $healthResult->isHealthy() ? ':white_check_mark:' : ':warning:';
        $color = $healthResult->isHealthy() ? 'good' : 'danger';

        $message = [
            'text' => "{$emoji} Estado del Sistema",
            'attachments' => [
                [
                    'color' => $color,
                    'fields' => [
                        [
                            'title' => 'Estado',
                            'value' => $healthResult->isHealthy() ? 'Saludable' : 'Con problemas',
                            'short' => true
                        ],
                        [
                            'title' => 'Mensaje',
                            'value' => $healthResult->getMessage(),
                            'short' => false
                        ],
                        [
                            'title' => 'Verificaciones',
                            'value' => json_encode($healthResult->getChecks(), JSON_PRETTY_PRINT),
                            'short' => false
                        ]
                    ]
                ]
            ]
        ];

        $this->logger->info('Enviando notificación de salud a Slack', [
            'healthy' => $healthResult->isHealthy(),
            'webhook' => $this->config['webhook_url'] ?? 'not_configured'
        ]);

        // Aquí se implementaría el envío real a Slack
        // $this->sendToSlack($message);
    }

    public function sendAlert(string $message, array $context = []): void
    {
        $slackMessage = [
            'text' => ":warning: Alerta del Sistema",
            'attachments' => [
                [
                    'color' => 'danger',
                    'fields' => [
                        [
                            'title' => 'Mensaje',
                            'value' => $message,
                            'short' => false
                        ],
                        [
                            'title' => 'Contexto',
                            'value' => json_encode($context, JSON_PRETTY_PRINT),
                            'short' => false
                        ]
                    ]
                ]
            ]
        ];

        $this->logger->error('Enviando alerta a Slack', [
            'message' => $message,
            'context' => $context
        ]);

        // Aquí se implementaría el envío real a Slack
        // $this->sendToSlack($slackMessage);
    }

    private function sendToSlack(array $message): void
    {
        // Implementación real del envío a Slack usando cURL
        // $ch = curl_init($this->config['webhook_url']);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        // curl_exec($ch);
        // curl_close($ch);
    }
}
