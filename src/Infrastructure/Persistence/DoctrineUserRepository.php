<?php

namespace SlimSeed\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use SlimSeed\Domain\Entities\User;
use SlimSeed\Domain\Repositories\UserRepositoryInterface;
use SlimSeed\Infrastructure\Persistence\Doctrine\UserEntity;
use SlimSeed\Infrastructure\Persistence\Mappers\UserMapper;

/**
 * Repositorio de usuarios con Doctrine ORM
 * Implementa el puerto UserRepositoryInterface usando Doctrine
 * Usa mappers para separar entidades de dominio de entidades de persistencia
 */
class DoctrineUserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(User $user): void
    {
        $doctrineUser = UserMapper::toDoctrineEntity($user);
        $this->entityManager->persist($doctrineUser);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?User
    {
        $doctrineUser = $this->entityManager->find(UserEntity::class, $id);
        return $doctrineUser ? UserMapper::toDomainEntity($doctrineUser) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $doctrineUser = $this->entityManager
            ->getRepository(UserEntity::class)
            ->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
            
        return $doctrineUser ? UserMapper::toDomainEntity($doctrineUser) : null;
    }

    public function findActiveUsers(): array
    {
        $doctrineUsers = $this->entityManager
            ->getRepository(UserEntity::class)
            ->createQueryBuilder('u')
            ->where('u.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
            
        return array_map(fn($doctrineUser) => UserMapper::toDomainEntity($doctrineUser), $doctrineUsers);
    }

    public function findAll(): array
    {
        $doctrineUsers = $this->entityManager
            ->getRepository(UserEntity::class)
            ->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
            
        return array_map(fn($doctrineUser) => UserMapper::toDomainEntity($doctrineUser), $doctrineUsers);
    }

    public function delete(User $user): void
    {
        $doctrineUser = UserMapper::toDoctrineEntity($user);
        $this->entityManager->remove($doctrineUser);
        $this->entityManager->flush();
    }
}
