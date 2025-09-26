<?php

use Doctrine\DBAL\DriverManager;

return DriverManager::getConnection([
    'dbname' => $_ENV['DB_NAME'] ?? 'slim_seed',
    'user' => $_ENV['DB_USER'] ?? 'slim_user',
    'password' => $_ENV['DB_PASS'] ?? 'slim_pass',
    'host' => $_ENV['DB_HOST'] ?? 'mysql',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'driver' => 'pdo_mysql',
]);
