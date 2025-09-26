<?php

namespace SlimSeed\Infrastructure\Persistence\Mappers;

use SlimSeed\Domain\Entities\HealthStatus;
use SlimSeed\Infrastructure\Persistence\Doctrine\HealthStatusEntity;

/**
 * Mapper entre entidad de dominio HealthStatus y entidad de Doctrine HealthStatusEntity
 * Facilita la migración futura a Laravel
 */
class HealthStatusMapper
{
    /**
     * Convierte entidad de dominio a entidad de Doctrine
     */
    public static function toDoctrineEntity(HealthStatus $domainHealthStatus): HealthStatusEntity
    {
        $doctrineHealthStatus = new HealthStatusEntity();
        
        // El ID se asigna automáticamente por Doctrine si es null
        // No se puede asignar manualmente en Doctrine
        
        $doctrineHealthStatus->setStatus($domainHealthStatus->getStatus());
        $doctrineHealthStatus->setTimestamp($domainHealthStatus->getTimestamp());
        $doctrineHealthStatus->setDetails($domainHealthStatus->getDetails());
        
        return $doctrineHealthStatus;
    }

    /**
     * Convierte entidad de Doctrine a entidad de dominio
     */
    public static function toDomainEntity(HealthStatusEntity $doctrineHealthStatus): HealthStatus
    {
        return new HealthStatus(
            status: $doctrineHealthStatus->getStatus(),
            details: $doctrineHealthStatus->getDetails(),
            id: $doctrineHealthStatus->getId(),
            timestamp: $doctrineHealthStatus->getTimestamp()
        );
    }

    /**
     * Convierte array de datos a entidad de dominio
     * Útil para consultas directas
     */
    public static function arrayToDomainEntity(array $data): HealthStatus
    {
        return HealthStatus::fromArray($data);
    }

    /**
     * Convierte entidad de dominio a array
     * Útil para consultas directas
     */
    public static function domainEntityToArray(HealthStatus $domainHealthStatus): array
    {
        return $domainHealthStatus->toArray();
    }
}
