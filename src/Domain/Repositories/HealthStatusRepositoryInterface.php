<?php

namespace SlimSeed\Domain\Repositories;

use SlimSeed\Domain\Entities\HealthStatus;

/**
 * Puerto para persistencia de estados de salud
 * Define el contrato que deben cumplir los adaptadores de persistencia
 */
interface HealthStatusRepositoryInterface
{
    /**
     * Guarda un estado de salud
     * 
     * @param HealthStatus $healthStatus
     * @return void
     */
    public function save(HealthStatus $healthStatus): void;

    /**
     * Obtiene el último estado de salud
     * 
     * @return HealthStatus|null
     */
    public function getLatest(): ?HealthStatus;

    /**
     * Obtiene estados de salud por rango de fechas
     * 
     * @param string $from
     * @param string $to
     * @return array
     */
    public function getByDateRange(string $from, string $to): array;
}
