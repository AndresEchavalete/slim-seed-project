<?php

namespace SlimSeed\Domain\Entities;

/**
 * Entidad de dominio HealthStatus
 * Representa el estado de salud del sistema
 * Sin dependencias de infraestructura
 */
class HealthStatus
{
    private ?int $id;
    private string $status;
    private \DateTime $timestamp;
    private array $details;

    public function __construct(
        string $status,
        array $details = [],
        ?int $id = null,
        ?\DateTime $timestamp = null
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->timestamp = $timestamp ?? new \DateTime();
        $this->details = $details;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s'),
            'details' => $this->details
        ];
    }

    /**
     * Método estático para crear desde datos de persistencia
     * Útil para mappers y repositorios
     */
    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            details: $data['details'] ?? [],
            id: $data['id'] ?? null,
            timestamp: isset($data['timestamp']) ? new \DateTime($data['timestamp']) : null
        );
    }
}