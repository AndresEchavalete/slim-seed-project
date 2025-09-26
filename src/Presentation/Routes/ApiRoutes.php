<?php

namespace SlimSeed\Presentation\Routes;

use Slim\App;
use SlimSeed\Presentation\Controllers\HealthController;
use SlimSeed\Presentation\Controllers\WelcomeController;
use SlimSeed\Presentation\Controllers\HealthHistoryController;
use SlimSeed\Presentation\Controllers\NotificationController;
use SlimSeed\Presentation\Controllers\UserController;

/**
 * Configuración de rutas de la API
 * Demuestra el uso de controladores con inyección de dependencias
 */
class ApiRoutes
{
    public static function register(App $app): void
    {
        // === RUTAS PÚBLICAS ===
        // Welcome route
        $app->get('/', WelcomeController::class . ':welcome');
        
        // Health check routes
        $app->get('/health', HealthController::class . ':check');
        $app->get('/api/status', HealthController::class . ':check');
        
        // === RUTAS DE HISTORIAL ===
        // Último estado de salud
        $app->get('/api/health/latest', HealthHistoryController::class . ':getLatest');
        
        // Historial por rango de fechas
        $app->get('/api/health/history', HealthHistoryController::class . ':getHistory');
        
        // === RUTAS DE NOTIFICACIONES ===
        // Enviar alerta manual
        $app->post('/api/notifications/alert', NotificationController::class . ':sendAlert');
        
        // Enviar notificación de prueba
        $app->post('/api/notifications/test', NotificationController::class . ':sendTest');
        
        // === RUTAS DE USUARIOS ===
        // Obtener usuarios activos (debe ir antes de la ruta con parámetro)
        $app->get('/api/users/active', UserController::class . ':getActiveUsers');
        
        // Crear usuario
        $app->post('/api/users', UserController::class . ':create');
        
        // Obtener usuario por ID
        $app->get('/api/users/{id}', UserController::class . ':getById');
        
        // Actualizar nombre de usuario
        $app->put('/api/users/{id}/name', UserController::class . ':updateName');
        
        // Autenticar usuario
        $app->post('/api/users/authenticate', UserController::class . ':authenticate');
    }
}