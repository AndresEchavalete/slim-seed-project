<?php

namespace SlimSeed\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Entidad de Doctrine para persistencia de estados de salud
 * Separada de la entidad de dominio para mantener DDD
 */
#[ORM\Entity]
#[ORM\Table(name: 'health_status')]
class HealthStatusEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $timestamp;

    #[ORM\Column(type: Types::JSON)]
    private array $details = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): void
    {
        $this->details = $details;
    }
}
