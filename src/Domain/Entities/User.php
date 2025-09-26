<?php

namespace SlimSeed\Domain\Entities;

/**
 * Entidad de dominio User
 * Representa un usuario en el contexto de negocio
 * Sin dependencias de infraestructura
 */
class User
{
    private ?int $id;
    private string $email;
    private string $name;
    private string $passwordHash;
    private bool $isActive;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;

    public function __construct(
        string $email,
        string $name,
        string $password,
        ?int $id = null,
        bool $isActive = true,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->isActive = $isActive;
        $this->createdAt = $createdAt ?? new \DateTime();
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTime();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTime();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTime();
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'isActive' => $this->isActive,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Método estático para crear desde datos de persistencia
     * Útil para mappers y repositorios
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            name: $data['name'],
            password: '', // No se necesita la contraseña para reconstruir
            id: $data['id'] ?? null,
            isActive: $data['isActive'] ?? true,
            createdAt: isset($data['createdAt']) ? new \DateTime($data['createdAt']) : null,
            updatedAt: isset($data['updatedAt']) ? new \DateTime($data['updatedAt']) : null
        );
    }
}