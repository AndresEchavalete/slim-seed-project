<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use SlimSeed\Shared\Container\ContainerBuilder;
use SlimSeed\Presentation\Routes\ApiRoutes;
use SlimSeed\Presentation\Middleware\CorsMiddleware;
use SlimSeed\Presentation\Middleware\ErrorHandlerMiddleware;

// Crear aplicación Slim con DI Container
$container = ContainerBuilder::build();
$app = AppFactory::createFromContainer($container);

// Agregar middleware
$app->add(new CorsMiddleware());
$app->add(new ErrorHandlerMiddleware());

// Registrar rutas
ApiRoutes::register($app);

// Ejecutar aplicación
$app->run();