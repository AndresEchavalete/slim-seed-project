<?php

namespace SlimSeed\Infrastructure\Config;

use PDO;

/**
 * Configuración de base de datos
 * Adaptador para la conexión a la base de datos
 */
class DatabaseConfig
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createConnection(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $this->config['host'],
            $this->config['port'],
            $this->config['database']
        );

        $pdo = new PDO($dsn, $this->config['username'], $this->config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $pdo;
    }

    public function createHealthStatusTable(): void
    {
        $pdo = $this->createConnection();
        
        $sql = "
            CREATE TABLE IF NOT EXISTS health_status (
                id INT AUTO_INCREMENT PRIMARY KEY,
                status VARCHAR(50) NOT NULL,
                timestamp DATETIME NOT NULL,
                details JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        $pdo->exec($sql);
    }
}
