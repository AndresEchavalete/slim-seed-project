<?php

namespace SlimSeed\Application\UseCases;

use SlimSeed\Domain\Entities\User;
use SlimSeed\Domain\Repositories\UserRepositoryInterface;
use SlimSeed\Application\DTOs\UserDTO;

/**
 * Caso de uso para gestión de usuarios
 * Demuestra el uso de repositorios con Doctrine en la capa de aplicación
 */
class UserUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Crea un nuevo usuario
     */
    public function createUser(string $email, string $name, string $password): UserDTO
    {
        // Verificar si el usuario ya existe
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser) {
            throw new \InvalidArgumentException('User with this email already exists');
        }

        $user = new User($email, $name, $password);
        $this->userRepository->save($user);

        return new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName(),
            isActive: $user->isActive(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }

    /**
     * Obtiene un usuario por ID
     */
    public function getUserById(int $id): ?UserDTO
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            return null;
        }

        return new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName(),
            isActive: $user->isActive(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }

    /**
     * Obtiene un usuario por email
     */
    public function getUserByEmail(string $email): ?UserDTO
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return null;
        }

        return new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName(),
            isActive: $user->isActive(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }

    /**
     * Obtiene todos los usuarios activos
     */
    public function getActiveUsers(): array
    {
        $users = $this->userRepository->findActiveUsers();
        return array_map(fn($user) => new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName(),
            isActive: $user->isActive(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        ), $users);
    }

    /**
     * Actualiza el nombre de un usuario
     */
    public function updateUserName(int $id, string $name): UserDTO
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        $user->updateName($name);
        $this->userRepository->save($user);

        return new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName(),
            isActive: $user->isActive(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }

    /**
     * Desactiva un usuario
     */
    public function deactivateUser(int $id): UserDTO
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        $user->deactivate();
        $this->userRepository->save($user);

        return new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName(),
            isActive: $user->isActive(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }

    /**
     * Autentica un usuario
     */
    public function authenticateUser(string $email, string $password): ?UserDTO
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !$user->isActive()) {
            return null;
        }

        if (!$user->verifyPassword($password)) {
            return null;
        }

        return new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName(),
            isActive: $user->isActive(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }
}
