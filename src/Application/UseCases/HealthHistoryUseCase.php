<?php

namespace SlimSeed\Application\UseCases;

use SlimSeed\Domain\Repositories\HealthStatusRepositoryInterface;
use SlimSeed\Domain\Entities\HealthStatus;
use SlimSeed\Application\DTOs\HealthCheckDTO;

/**
 * Caso de uso para obtener historial de estados de salud
 * Demuestra el uso de repositorios en la capa de aplicación
 */
class HealthHistoryUseCase
{
    private HealthStatusRepositoryInterface $healthRepository;

    public function __construct(HealthStatusRepositoryInterface $healthRepository)
    {
        $this->healthRepository = $healthRepository;
    }

    /**
     * Obtiene el último estado de salud
     * 
     * @return HealthCheckDTO|null
     */
    public function getLatestHealthStatus(): ?HealthCheckDTO
    {
        $healthStatus = $this->healthRepository->getLatest();
        if (!$healthStatus) {
            return null;
        }

        return new HealthCheckDTO(
            $healthStatus->getStatus() === 'healthy',
            'Latest health status',
            $healthStatus->getDetails()
        );
    }

    /**
     * Obtiene historial de salud por rango de fechas
     * 
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     * @return array
     */
    public function getHealthHistory(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $history = $this->healthRepository->getByDateRange($from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s'));
        
        return array_map(fn($status) => new HealthCheckDTO(
            $status->getStatus() === 'healthy',
            'Historical health status',
            $status->getDetails()
        ), $history);
    }
}
