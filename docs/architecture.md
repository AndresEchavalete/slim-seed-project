# 🏛️ Arquitectura Hexagonal

Documentación detallada de la arquitectura hexagonal implementada en el Slim Seed Project.

## 📋 Índice

- [Conceptos Fundamentales](#conceptos-fundamentales)
- [Estructura de Capas](#estructura-de-capas)
- [Puertos y Adaptadores](#puertos-y-adaptadores)
- [Inyección de Dependencias](#inyección-de-dependencias)
- [Patrones Implementados](#patrones-implementados)
- [Ejemplos Prácticos](#ejemplos-prácticos)

## 🎯 Conceptos Fundamentales

### **¿Qué es la Arquitectura Hexagonal?**

La Arquitectura Hexagonal (también conocida como "Ports and Adapters") es un patrón arquitectónico que:

- **Aísla la lógica de negocio** de la infraestructura externa
- **Facilita el testing** mediante la inyección de dependencias
- **Permite intercambiar adaptadores** sin afectar el dominio
- **Mantiene el dominio independiente** de frameworks y tecnologías

### **Principios Clave**

1. **Inversión de Dependencias** - El dominio no depende de la infraestructura
2. **Separación de Responsabilidades** - Cada capa tiene una responsabilidad específica
3. **Testabilidad** - Fácil mock de dependencias externas
4. **Flexibilidad** - Intercambio fácil de implementaciones

## 🏗️ Estructura de Capas

```
┌─────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                      │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │ Controllers │  │ Middleware  │  │   Routes    │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                   APPLICATION LAYER                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │  Use Cases  │  │    DTOs     │  │  Services   │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                     DOMAIN LAYER                           │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │  Entities   │  │Value Objects│  │  Services   │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │Repositories │  │  Factories  │  │  Events     │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                 INFRASTRUCTURE LAYER                       │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │  Services   │  │  External   │  │Persistence  │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │    Config   │  │   Logging   │  │   Cache     │        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
└─────────────────────────────────────────────────────────────┘
```

## 🔌 Puertos y Adaptadores

### **Puertos (Interfaces)**

Los puertos definen **QUÉ** debe hacer el sistema, no **CÓMO**.

#### **Puerto de Servicio**
```php
// Domain/Services/HealthCheckServiceInterface.php
interface HealthCheckServiceInterface
{
    public function checkHealth(): HealthCheckResult;
}
```

#### **Puerto de Repositorio**
```php
// Domain/Repositories/UserRepositoryInterface.php
interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findActiveUsers(): array;
}
```

#### **Puerto de Notificación**
```php
// Domain/Services/NotificationServiceInterface.php
interface NotificationServiceInterface
{
    public function sendHealthNotification(HealthCheckResult $healthResult): void;
    public function sendAlert(string $message, array $context = []): void;
}
```

### **Adaptadores (Implementaciones)**

Los adaptadores implementan **CÓMO** se realizan las operaciones.

#### **Adaptador de Servicio**
```php
// Infrastructure/Services/HealthCheckService.php
class HealthCheckService implements HealthCheckServiceInterface
{
    private HealthStatusRepositoryInterface $healthRepository;

    public function __construct(HealthStatusRepositoryInterface $healthRepository)
    {
        $this->healthRepository = $healthRepository;
    }

    public function checkHealth(): HealthCheckResult
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'memory' => $this->checkMemory()
        ];

        $isHealthy = !in_array(false, $checks);
        $message = $isHealthy ? 'All systems operational' : 'Some systems are down';

        $result = new HealthCheckResult($isHealthy, $message, $checks);
        
        // Guardar el estado en el repositorio
        $healthStatus = new HealthStatus(
            $isHealthy ? 'healthy' : 'unhealthy',
            $checks
        );
        $this->healthRepository->save($healthStatus);

        return $result;
    }
}
```

#### **Adaptador de Repositorio**
```php
// Infrastructure/Persistence/DoctrineUserRepository.php
class DoctrineUserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
```

## 💉 Inyección de Dependencias

### **Configuración del Container**

```php
// Shared/Container/ContainerBuilder.php
class ContainerBuilder
{
    public static function build(): \DI\Container
    {
        $containerBuilder = new DIContainerBuilder();
        
        $containerBuilder->addDefinitions([
            // === PUERTOS E INTERFACES ===
            HealthCheckServiceInterface::class => \DI\create(HealthCheckService::class)
                ->constructor(\DI\get(HealthStatusRepositoryInterface::class)),
            
            UserRepositoryInterface::class => \DI\create(DoctrineUserRepository::class)
                ->constructor(\DI\get(EntityManagerInterface::class)),
            
            NotificationServiceInterface::class => \DI\factory(function (\DI\Container $c) {
                $logger = $c->get(LoggerInterface::class);
                $config = $c->get('notification.config');
                
                $notificationType = $_ENV['NOTIFICATION_TYPE'] ?? 'email';
                
                if ($notificationType === 'slack') {
                    return new SlackNotificationService($logger, $config->getSlackConfig());
                }
                
                return new EmailNotificationService($logger, $config->getEmailConfig());
            }),
            
            // === CASOS DE USO ===
            HealthCheckUseCase::class => \DI\create()
                ->constructor(\DI\get(HealthCheckServiceInterface::class)),
            
            UserUseCase::class => \DI\create()
                ->constructor(\DI\get(UserRepositoryInterface::class)),
        ]);

        return $containerBuilder->build();
    }
}
```

## 🎨 Patrones Implementados

### **1. Repository Pattern**

```php
// Puerto
interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(int $id): ?User;
}

// Adaptador
class DoctrineUserRepository implements UserRepositoryInterface
{
    // Implementación con Doctrine ORM
}
```

### **2. Use Case Pattern**

```php
// Application/UseCases/UserUseCase.php
class UserUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(string $email, string $name, string $password): User
    {
        // Lógica de negocio
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser) {
            throw new \InvalidArgumentException('User with this email already exists');
        }

        $user = new User($email, $name, $password);
        $this->userRepository->save($user);

        return $user;
    }
}
```

### **3. DTO Pattern**

```php
// Application/DTOs/HealthCheckDTO.php
class HealthCheckDTO
{
    private bool $healthy;
    private string $message;
    private array $checks;
    private string $timestamp;

    public function __construct(bool $healthy, string $message, array $checks = [])
    {
        $this->healthy = $healthy;
        $this->message = $message;
        $this->checks = $checks;
        $this->timestamp = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'healthy' => $this->healthy,
            'message' => $this->message,
            'checks' => $this->checks,
            'timestamp' => $this->timestamp
        ];
    }
}
```

## 🔄 Intercambio de Adaptadores

### **Cambio de Notificaciones**

```env
# Para usar Email
NOTIFICATION_TYPE=email
ADMIN_EMAIL=admin@example.com

# Para usar Slack
NOTIFICATION_TYPE=slack
SLACK_WEBHOOK=https://hooks.slack.com/services/...
```

### **Cambio de Persistencia**

```php
// Cambiar de MySQL a PostgreSQL
// Solo cambiar la configuración de Doctrine
$connectionParams = [
    'driver' => 'pdo_pgsql',  // En lugar de 'pdo_mysql'
    'host' => $this->config['host'],
    'port' => $this->config['port'],
    'dbname' => $this->config['database'],
    'user' => $this->config['username'],
    'password' => $this->config['password'],
];
```

## 🧪 Testing

### **Mock de Dependencias**

```php
// Test de Use Case
class UserUseCaseTest extends TestCase
{
    public function testCreateUser()
    {
        // Mock del repositorio
        $mockRepository = $this->createMock(UserRepositoryInterface::class);
        $mockRepository->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);
        $mockRepository->expects($this->once())
            ->method('save');

        // Crear Use Case con mock
        $userUseCase = new UserUseCase($mockRepository);

        // Ejecutar test
        $user = $userUseCase->createUser('test@example.com', 'Test User', 'password123');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->getEmail());
    }
}
```

## 🎯 Beneficios de esta Arquitectura

### **1. Testabilidad**
- Fácil mock de dependencias
- Tests unitarios independientes
- Tests de integración controlados

### **2. Mantenibilidad**
- Separación clara de responsabilidades
- Código organizado por capas
- Fácil localización de funcionalidades

### **3. Flexibilidad**
- Intercambio fácil de adaptadores
- Cambio de tecnologías sin afectar el dominio
- Configuración por variables de entorno

### **4. Escalabilidad**
- Estructura preparada para crecimiento
- Fácil agregar nuevas funcionalidades
- Patrones establecidos para nuevos desarrollos

### **5. Independencia**
- El dominio no depende de frameworks
- Fácil migración a otros frameworks
- Lógica de negocio reutilizable

## 🚀 Próximos Pasos

1. **Event Sourcing** - Para auditoría completa
2. **CQRS** - Separación de comandos y consultas
3. **Domain Events** - Comunicación entre agregados
4. **Saga Pattern** - Para transacciones distribuidas
5. **API Gateway** - Para microservicios

---

**Esta arquitectura proporciona una base sólida y escalable para el desarrollo de aplicaciones empresariales modernas.**
