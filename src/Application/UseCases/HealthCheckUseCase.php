<?php

namespace SlimSeed\Application\UseCases;

use SlimSeed\Domain\ValueObjects\HealthCheckResult;
use SlimSeed\Domain\Services\HealthCheckServiceInterface;
use SlimSeed\Application\DTOs\HealthCheckDTO;

class HealthCheckUseCase
{
    private HealthCheckServiceInterface $healthCheckService;

    public function __construct(HealthCheckServiceInterface $healthCheckService)
    {
        $this->healthCheckService = $healthCheckService;
    }

    public function execute(): HealthCheckDTO
    {
        $healthResult = $this->healthCheckService->checkHealth();
        
        return new HealthCheckDTO(
            $healthResult->isHealthy(),
            $healthResult->getMessage(),
            $healthResult->getChecks()
        );
    }
}