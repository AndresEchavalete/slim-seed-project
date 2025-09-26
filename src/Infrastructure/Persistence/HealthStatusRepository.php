<?php

namespace SlimSeed\Infrastructure\Persistence;

use SlimSeed\Domain\Entities\HealthStatus;
use SlimSeed\Domain\Repositories\HealthStatusRepositoryInterface;
use PDO;

/**
 * Adaptador de persistencia para estados de salud
 * Implementa el puerto HealthStatusRepositoryInterface
 */
class HealthStatusRepository implements HealthStatusRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(HealthStatus $healthStatus): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO health_status (status, timestamp, details) VALUES (?, ?, ?)"
        );
        
        $stmt->execute([
            $healthStatus->getStatus(),
            $healthStatus->getTimestamp(),
            json_encode($healthStatus->getDetails())
        ]);
    }

    public function getLatest(): ?HealthStatus
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM health_status ORDER BY timestamp DESC LIMIT 1"
        );
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }

        return new HealthStatus(
            $row['status'],
            json_decode($row['details'], true) ?: []
        );
    }

    public function getByDateRange(string $from, string $to): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM health_status WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp DESC"
        );
        $stmt->execute([$from, $to]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new HealthStatus(
                $row['status'],
                json_decode($row['details'], true) ?: []
            );
        }

        return $results;
    }
}
