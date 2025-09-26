<?php

namespace SlimSeed\Application\DTOs;

class HealthCheckDTO
{
    private bool $healthy;
    private string $message;
    private array $checks;
    private string $timestamp;

    public function __construct(bool $healthy, string $message, array $checks = [])
    {
        $this->healthy = $healthy;
        $this->message = $message;
        $this->checks = $checks;
        $this->timestamp = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'healthy' => $this->healthy,
            'message' => $this->message,
            'checks' => $this->checks,
            'timestamp' => $this->timestamp
        ];
    }
}