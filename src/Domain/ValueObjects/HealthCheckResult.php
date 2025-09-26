<?php

namespace SlimSeed\Domain\ValueObjects;

class HealthCheckResult
{
    private bool $isHealthy;
    private string $message;
    private array $checks;

    public function __construct(bool $isHealthy, string $message, array $checks = [])
    {
        $this->isHealthy = $isHealthy;
        $this->message = $message;
        $this->checks = $checks;
    }

    public function isHealthy(): bool
    {
        return $this->isHealthy;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getChecks(): array
    {
        return $this->checks;
    }

    public function toArray(): array
    {
        return [
            'healthy' => $this->isHealthy,
            'message' => $this->message,
            'checks' => $this->checks
        ];
    }
}