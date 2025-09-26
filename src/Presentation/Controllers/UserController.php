<?php

namespace SlimSeed\Presentation\Controllers;

use SlimSeed\Application\UseCases\UserUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controlador para gestión de usuarios
 * Demuestra el uso de Doctrine ORM a través de casos de uso
 */
class UserController
{
    private UserUseCase $userUseCase;

    public function __construct(UserUseCase $userUseCase)
    {
        $this->userUseCase = $userUseCase;
    }

    /**
     * Crea un nuevo usuario
     */
    public function create(Request $request, Response $response): Response
    {
        $body = json_decode($request->getBody()->getContents(), true);
        
        try {
            $user = $this->userUseCase->createUser(
                $body['email'] ?? '',
                $body['name'] ?? '',
                $body['password'] ?? ''
            );

            $data = [
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user->toArray()
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        } catch (\InvalidArgumentException $e) {
            $data = [
                'success' => false,
                'message' => $e->getMessage()
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    /**
     * Obtiene un usuario por ID
     */
    public function getById(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $user = $this->userUseCase->getUserById($id);

        if (!$user) {
            $data = ['message' => 'User not found'];
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($user->toArray()));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Obtiene todos los usuarios activos
     */
    public function getActiveUsers(Request $request, Response $response): Response
    {
        $users = $this->userUseCase->getActiveUsers();
        
        $data = [
            'count' => count($users),
            'users' => array_map(fn($user) => $user->toArray(), $users)
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Actualiza el nombre de un usuario
     */
    public function updateName(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $body = json_decode($request->getBody()->getContents(), true);
        
        try {
            $user = $this->userUseCase->updateUserName($id, $body['name'] ?? '');

            $data = [
                'success' => true,
                'message' => 'User name updated successfully',
                'user' => $user->toArray()
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\InvalidArgumentException $e) {
            $data = [
                'success' => false,
                'message' => $e->getMessage()
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    /**
     * Autentica un usuario
     */
    public function authenticate(Request $request, Response $response): Response
    {
        $body = json_decode($request->getBody()->getContents(), true);
        
        $user = $this->userUseCase->authenticateUser(
            $body['email'] ?? '',
            $body['password'] ?? ''
        );

        if (!$user) {
            $data = [
                'success' => false,
                'message' => 'Invalid credentials'
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $data = [
            'success' => true,
            'message' => 'Authentication successful',
            'user' => $user->toArray()
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
