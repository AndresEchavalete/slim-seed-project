<?php

namespace SlimSeed\Infrastructure\Persistence\Mappers;

use SlimSeed\Domain\Entities\User;
use SlimSeed\Infrastructure\Persistence\Doctrine\UserEntity;

/**
 * Mapper entre entidad de dominio User y entidad de Doctrine UserEntity
 * Facilita la migración futura a Laravel
 */
class UserMapper
{
    /**
     * Convierte entidad de dominio a entidad de Doctrine
     */
    public static function toDoctrineEntity(User $domainUser): UserEntity
    {
        $doctrineUser = new UserEntity();
        
        // El ID se asigna automáticamente por Doctrine si es null
        // No se puede asignar manualmente en Doctrine
        
        $doctrineUser->setEmail($domainUser->getEmail());
        $doctrineUser->setName($domainUser->getName());
        $doctrineUser->setPassword($domainUser->getPasswordHash());
        $doctrineUser->setIsActive($domainUser->isActive());
        $doctrineUser->setCreatedAt($domainUser->getCreatedAt());
        $doctrineUser->setUpdatedAt($domainUser->getUpdatedAt());
        
        return $doctrineUser;
    }

    /**
     * Convierte entidad de Doctrine a entidad de dominio
     */
    public static function toDomainEntity(UserEntity $doctrineUser): User
    {
        return new User(
            email: $doctrineUser->getEmail(),
            name: $doctrineUser->getName(),
            password: '', // No se necesita la contraseña para reconstruir
            id: $doctrineUser->getId(),
            isActive: $doctrineUser->isActive(),
            createdAt: $doctrineUser->getCreatedAt(),
            updatedAt: $doctrineUser->getUpdatedAt()
        );
    }

    /**
     * Convierte array de datos a entidad de dominio
     * Útil para consultas directas
     */
    public static function arrayToDomainEntity(array $data): User
    {
        return User::fromArray($data);
    }

    /**
     * Convierte entidad de dominio a array
     * Útil para consultas directas
     */
    public static function domainEntityToArray(User $domainUser): array
    {
        return $domainUser->toArray();
    }
}
