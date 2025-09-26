<?php

namespace SlimSeed\Infrastructure\Config;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\Connection;

/**
 * Configuración de Doctrine ORM
 * Adaptador para la configuración de Doctrine con Arquitectura Hexagonal
 */
class DoctrineConfig
{
    private array $config;
    private string $entityPath;

    public function __construct(array $config, string $entityPath = null)
    {
        $this->config = $config;
        $this->entityPath = $entityPath ?? __DIR__ . '/../Persistence/Doctrine';
    }

    public function createEntityManager(): EntityManager
    {
        // Configuración de Doctrine
        $doctrineConfig = ORMSetup::createAttributeMetadataConfiguration(
            [$this->entityPath],
            $this->config['debug'] ?? false,
            $this->config['proxy_dir'] ?? null,
            $this->config['cache'] ?? null
        );

        // Configuración de la conexión
        $connectionParams = $this->getConnectionParams();

        $connection = DriverManager::getConnection($connectionParams);
        
        return new EntityManager($connection, $doctrineConfig);
    }

    public function createConnection(): Connection
    {
        $connectionParams = $this->getConnectionParams();
        return DriverManager::getConnection($connectionParams);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Obtiene los parámetros de conexión según el tipo de base de datos
     */
    private function getConnectionParams(): array
    {
        $dbType = $this->config['driver'] ?? $this->detectDatabaseType();
        
        switch ($dbType) {
            case 'pgsql':
            case 'postgresql':
                return $this->getPostgreSQLParams();
            
            case 'sqlite':
                return $this->getSQLiteParams();
            
            case 'mysql':
            default:
                return $this->getMySQLParams();
        }
    }

    /**
     * Detecta el tipo de base de datos basado en las variables de entorno
     */
    private function detectDatabaseType(): string
    {
        $dbUrl = $_ENV['DATABASE_URL'] ?? '';
        
        if (!empty($dbUrl)) {
            if (strpos($dbUrl, 'postgresql://') === 0 || strpos($dbUrl, 'postgres://') === 0) {
                return 'pgsql';
            }
            if (strpos($dbUrl, 'sqlite://') === 0) {
                return 'sqlite';
            }
            if (strpos($dbUrl, 'mysql://') === 0) {
                return 'mysql';
            }
        }

        // Detectar por puerto
        $port = $this->config['port'] ?? '3306';
        if ($port === '5432') {
            return 'pgsql';
        }
        if ($port === '3306') {
            return 'mysql';
        }

        // Detectar por host
        $host = $this->config['host'] ?? 'mysql';
        if (strpos($host, 'postgres') !== false) {
            return 'pgsql';
        }

        return 'mysql'; // Default
    }

    /**
     * Parámetros de conexión para MySQL
     */
    private function getMySQLParams(): array
    {
        return [
            'driver' => 'pdo_mysql',
            'host' => $this->config['host'],
            'port' => $this->config['port'],
            'dbname' => $this->config['database'],
            'user' => $this->config['username'],
            'password' => $this->config['password'],
            'charset' => 'utf8mb4',
            'serverVersion' => '8.0',
        ];
    }

    /**
     * Parámetros de conexión para PostgreSQL
     */
    private function getPostgreSQLParams(): array
    {
        return [
            'driver' => 'pdo_pgsql',
            'host' => $this->config['host'],
            'port' => $this->config['port'],
            'dbname' => $this->config['database'],
            'user' => $this->config['username'],
            'password' => $this->config['password'],
            'charset' => 'utf8',
            'serverVersion' => '13.0',
        ];
    }

    /**
     * Parámetros de conexión para SQLite
     */
    private function getSQLiteParams(): array
    {
        $path = $this->config['database'];
        if (!str_ends_with($path, '.db')) {
            $path .= '.db';
        }

        return [
            'driver' => 'pdo_sqlite',
            'path' => $path,
        ];
    }
}
