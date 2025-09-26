<?php

namespace SlimSeed\Presentation\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Throwable;

class ErrorHandlerMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $exception) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'error' => true,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}