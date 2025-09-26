<?php

namespace SlimSeed\Domain\Services;

use SlimSeed\Domain\ValueObjects\HealthCheckResult;

/**
 * Puerto para verificación de salud del sistema
 * Define el contrato que deben cumplir los adaptadores de infraestructura
 */
interface HealthCheckServiceInterface
{
    /**
     * Verifica el estado de salud del sistema
     * 
     * @return HealthCheckResult
     */
    public function checkHealth(): HealthCheckResult;
}
