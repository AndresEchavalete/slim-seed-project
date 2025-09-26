<?php

namespace SlimSeed\Infrastructure\Services;

use SlimSeed\Domain\Services\HealthCheckServiceInterface;
use SlimSeed\Domain\ValueObjects\HealthCheckResult;
use SlimSeed\Domain\Repositories\HealthStatusRepositoryInterface;
use SlimSeed\Domain\Entities\HealthStatus;

/**
 * Adaptador de verificación de salud del sistema
 * Implementa el puerto HealthCheckServiceInterface
 */
class HealthCheckService implements HealthCheckServiceInterface
{
    private HealthStatusRepositoryInterface $healthRepository;

    public function __construct(HealthStatusRepositoryInterface $healthRepository)
    {
        $this->healthRepository = $healthRepository;
    }

    public function checkHealth(): HealthCheckResult
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'memory' => $this->checkMemory()
        ];

        $isHealthy = !in_array(false, $checks);
        $message = $isHealthy ? 'All systems operational' : 'Some systems are down';

        $result = new HealthCheckResult($isHealthy, $message, $checks);
        
        // Guardar el estado en el repositorio
        $healthStatus = new HealthStatus(
            $isHealthy ? 'healthy' : 'unhealthy',
            $checks
        );
        $this->healthRepository->save($healthStatus);

        return $result;
    }

    private function checkDatabase(): bool
    {
        // Implementar verificación de base de datos
        return true;
    }

    private function checkRedis(): bool
    {
        // Implementar verificación de Redis
        return true;
    }

    private function checkMemory(): bool
    {
        // Implementar verificación de memoria
        return true;
    }
}