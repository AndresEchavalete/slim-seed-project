<?php

namespace SlimSeed\Presentation\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class WelcomeController
{
    public function welcome(Request $request, Response $response): Response
    {
        $data = [
            'message' => '¡Bienvenido a Slim Seed Project!',
            'version' => '1.0.0',
            'architecture' => 'DDD + Hexagonal',
            'framework' => 'Slim 4 + DI Container',
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}