<?php

namespace SlimSeed\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use SlimSeed\Domain\Entities\HealthStatus;
use SlimSeed\Domain\Repositories\HealthStatusRepositoryInterface;
use SlimSeed\Infrastructure\Persistence\Doctrine\HealthStatusEntity;
use SlimSeed\Infrastructure\Persistence\Mappers\HealthStatusMapper;

/**
 * Repositorio de estados de salud con Doctrine ORM
 * Implementa el puerto HealthStatusRepositoryInterface usando Doctrine
 * Usa mappers para separar entidades de dominio de entidades de persistencia
 */
class DoctrineHealthStatusRepository implements HealthStatusRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(HealthStatus $healthStatus): void
    {
        $doctrineStatus = HealthStatusMapper::toDoctrineEntity($healthStatus);
        $this->entityManager->persist($doctrineStatus);
        $this->entityManager->flush();
    }

    public function getLatest(): ?HealthStatus
    {
        $doctrineStatus = $this->entityManager
            ->getRepository(HealthStatusEntity::class)
            ->createQueryBuilder('h')
            ->orderBy('h.timestamp', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
            
        return $doctrineStatus ? HealthStatusMapper::toDomainEntity($doctrineStatus) : null;
    }

    public function getByDateRange(string $from, string $to): array
    {
        $doctrineStatuses = $this->entityManager
            ->getRepository(HealthStatusEntity::class)
            ->createQueryBuilder('h')
            ->where('h.timestamp BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('h.timestamp', 'DESC')
            ->getQuery()
            ->getResult();
            
        return array_map(fn($doctrineStatus) => HealthStatusMapper::toDomainEntity($doctrineStatus), $doctrineStatuses);
    }

    public function findById(int $id): ?HealthStatus
    {
        $doctrineStatus = $this->entityManager->find(HealthStatusEntity::class, $id);
        return $doctrineStatus ? HealthStatusMapper::toDomainEntity($doctrineStatus) : null;
    }

    public function findAll(): array
    {
        $doctrineStatuses = $this->entityManager
            ->getRepository(HealthStatusEntity::class)
            ->findAll();
            
        return array_map(fn($doctrineStatus) => HealthStatusMapper::toDomainEntity($doctrineStatus), $doctrineStatuses);
    }

    public function delete(HealthStatus $healthStatus): void
    {
        $doctrineStatus = HealthStatusMapper::toDoctrineEntity($healthStatus);
        $this->entityManager->remove($doctrineStatus);
        $this->entityManager->flush();
    }
}
