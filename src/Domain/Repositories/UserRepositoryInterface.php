<?php

namespace SlimSeed\Domain\Repositories;

use SlimSeed\Domain\Entities\User;

/**
 * Puerto para persistencia de usuarios
 * Define el contrato que deben cumplir los adaptadores de persistencia
 */
interface UserRepositoryInterface
{
    /**
     * Guarda un usuario
     */
    public function save(User $user): void;

    /**
     * Busca un usuario por ID
     */
    public function findById(int $id): ?User;

    /**
     * Busca un usuario por email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Obtiene todos los usuarios activos
     */
    public function findActiveUsers(): array;

    /**
     * Obtiene todos los usuarios
     */
    public function findAll(): array;

    /**
     * Elimina un usuario
     */
    public function delete(User $user): void;
}
