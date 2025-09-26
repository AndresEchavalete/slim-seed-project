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
        $connectionParams = [
            'driver' => 'pdo_mysql',
            'host' => $this->config['host'],
            'port' => $this->config['port'],
            'dbname' => $this->config['database'],
            'user' => $this->config['username'],
            'password' => $this->config['password'],
            'charset' => 'utf8mb4',
        ];

        $connection = DriverManager::getConnection($connectionParams);
        
        return new EntityManager($connection, $doctrineConfig);
    }

    public function createConnection(): Connection
    {
        $connectionParams = [
            'driver' => 'pdo_mysql',
            'host' => $this->config['host'],
            'port' => $this->config['port'],
            'dbname' => $this->config['database'],
            'user' => $this->config['username'],
            'password' => $this->config['password'],
            'charset' => 'utf8mb4',
        ];

        return DriverManager::getConnection($connectionParams);
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
