<?php

namespace SlimSeed\Presentation\Controllers;

use SlimSeed\Application\UseCases\HealthCheckUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HealthController
{
    private HealthCheckUseCase $healthCheckUseCase;

    public function __construct(HealthCheckUseCase $healthCheckUseCase)
    {
        $this->healthCheckUseCase = $healthCheckUseCase;
    }

    public function check(Request $request, Response $response): Response
    {
        $healthResult = $this->healthCheckUseCase->execute();
        
        $response->getBody()->write(json_encode($healthResult->toArray()));
        return $response->withHeader('Content-Type', 'application/json');
    }
}