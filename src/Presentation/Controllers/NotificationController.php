<?php

namespace SlimSeed\Presentation\Controllers;

use SlimSeed\Application\UseCases\NotificationUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controlador para notificaciones
 * Demuestra el uso de servicios de notificación a través de casos de uso
 */
class NotificationController
{
    private NotificationUseCase $notificationUseCase;

    public function __construct(NotificationUseCase $notificationUseCase)
    {
        $this->notificationUseCase = $notificationUseCase;
    }

    /**
     * Envía una alerta manual
     */
    public function sendAlert(Request $request, Response $response): Response
    {
        $body = json_decode($request->getBody()->getContents(), true);
        
        if (!isset($body['message'])) {
            $data = ['error' => 'Message is required'];
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $message = $body['message'];
        $context = $body['context'] ?? [];

        $this->notificationUseCase->sendAlert($message, $context);

        $data = [
            'success' => true,
            'message' => 'Alert sent successfully',
            'sent_at' => date('Y-m-d H:i:s')
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Envía notificación de prueba
     */
    public function sendTest(Request $request, Response $response): Response
    {
        $this->notificationUseCase->sendAlert('Test notification from API', [
            'endpoint' => '/api/notifications/test',
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        $data = [
            'success' => true,
            'message' => 'Test notification sent',
            'sent_at' => date('Y-m-d H:i:s')
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
