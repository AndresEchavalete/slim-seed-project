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
        $dsn = $this->getDsn();
        $options = $this->getPdoOptions();

        $pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
        return $pdo;
    }

    /**
     * Genera el DSN según el tipo de base de datos
     */
    private function getDsn(): string
    {
        $dbType = $this->config['driver'] ?? $this->detectDatabaseType();
        
        switch ($dbType) {
            case 'pgsql':
            case 'postgresql':
                return sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s',
                    $this->config['host'],
                    $this->config['port'],
                    $this->config['database']
                );
            
            case 'sqlite':
                $path = $this->config['database'];
                if (!str_ends_with($path, '.db')) {
                    $path .= '.db';
                }
                return "sqlite:$path";
            
            case 'mysql':
            default:
                return sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    $this->config['host'],
                    $this->config['port'],
                    $this->config['database']
                );
        }
    }

    /**
     * Obtiene las opciones de PDO según el tipo de base de datos
     */
    private function getPdoOptions(): array
    {
        $dbType = $this->config['driver'] ?? $this->detectDatabaseType();
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if ($dbType === 'mysql') {
            $options[PDO::ATTR_EMULATE_PREPARES] = false;
        }

        return $options;
    }

    /**
     * Detecta el tipo de base de datos
     */
    private function detectDatabaseType(): string
    {
        $port = $this->config['port'] ?? '3306';
        if ($port === '5432') {
            return 'pgsql';
        }
        if ($port === '3306') {
            return 'mysql';
        }

        $host = $this->config['host'] ?? 'mysql';
        if (strpos($host, 'postgres') !== false) {
            return 'pgsql';
        }

        return 'mysql';
    }

    public function createHealthStatusTable(): void
    {
        $pdo = $this->createConnection();
        $sql = $this->getHealthStatusTableSql();
        $pdo->exec($sql);
    }

    /**
     * Genera el SQL para crear la tabla health_status según el tipo de BD
     */
    private function getHealthStatusTableSql(): string
    {
        $dbType = $this->config['driver'] ?? $this->detectDatabaseType();
        
        switch ($dbType) {
            case 'pgsql':
            case 'postgresql':
                return "
                    CREATE TABLE IF NOT EXISTS health_status (
                        id SERIAL PRIMARY KEY,
                        status VARCHAR(50) NOT NULL,
                        timestamp TIMESTAMP NOT NULL,
                        details JSONB,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ";
            
            case 'sqlite':
                return "
                    CREATE TABLE IF NOT EXISTS health_status (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        status VARCHAR(50) NOT NULL,
                        timestamp DATETIME NOT NULL,
                        details TEXT,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    )
                ";
            
            case 'mysql':
            default:
                return "
                    CREATE TABLE IF NOT EXISTS health_status (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        status VARCHAR(50) NOT NULL,
                        timestamp DATETIME NOT NULL,
                        details JSON,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ";
        }
    }
}
