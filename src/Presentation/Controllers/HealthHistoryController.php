<?php

namespace SlimSeed\Presentation\Controllers;

use SlimSeed\Application\UseCases\HealthHistoryUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controlador para historial de estados de salud
 * Demuestra el uso de repositorios a través de casos de uso
 */
class HealthHistoryController
{
    private HealthHistoryUseCase $healthHistoryUseCase;

    public function __construct(HealthHistoryUseCase $healthHistoryUseCase)
    {
        $this->healthHistoryUseCase = $healthHistoryUseCase;
    }

    /**
     * Obtiene el último estado de salud
     */
    public function getLatest(Request $request, Response $response): Response
    {
        $healthStatus = $this->healthHistoryUseCase->getLatestHealthStatus();
        
        if (!$healthStatus) {
            $data = ['message' => 'No health status found'];
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($healthStatus->toArray()));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Obtiene historial de salud por rango de fechas
     */
    public function getHistory(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $from = $queryParams['from'] ?? date('Y-m-d 00:00:00', strtotime('-7 days'));
        $to = $queryParams['to'] ?? date('Y-m-d 23:59:59');

        $fromDateTime = new \DateTimeImmutable($from);
        $toDateTime = new \DateTimeImmutable($to);

        $history = $this->healthHistoryUseCase->getHealthHistory($fromDateTime, $toDateTime);
        
        $data = [
            'from' => $from,
            'to' => $to,
            'count' => count($history),
            'history' => array_map(fn($status) => $status->toArray(), $history)
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
