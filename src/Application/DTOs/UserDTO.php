<?php

namespace SlimSeed\Application\DTOs;

/**
 * DTO para transferencia de datos de usuario
 * Compatible con Laravel y otros frameworks
 */
class UserDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $email,
        public readonly string $name,
        public readonly bool $isActive,
        public readonly \DateTime $createdAt,
        public readonly ?\DateTime $updatedAt = null
    ) {}

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

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            email: $data['email'],
            name: $data['name'],
            isActive: $data['isActive'] ?? true,
            createdAt: new \DateTime($data['createdAt']),
            updatedAt: isset($data['updatedAt']) ? new \DateTime($data['updatedAt']) : null
        );
    }
}
