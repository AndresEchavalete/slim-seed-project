# 🛠️ Guía de Desarrollo

Guía completa para desarrolladores del Slim Seed Project.

## 📋 Índice

- [Configuración del Entorno](#configuración-del-entorno)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Flujo de Desarrollo](#flujo-de-desarrollo)
- [Convenciones de Código](#convenciones-de-código)
- [Testing](#testing)
- [Debugging](#debugging)
- [Mejores Prácticas](#mejores-prácticas)

## 🚀 Configuración del Entorno

### **Requisitos Previos**

- Docker & Docker Compose
- Git
- Editor de código (VS Code recomendado)

### **Configuración Inicial**

```bash
# 1. Clonar el repositorio
git clone <repository-url>
cd slim-seed-project

# 2. Levantar contenedores
docker-compose up -d

# 3. Instalar dependencias
docker-compose exec -T app bash -c "cd /var/www/html && composer install"

# 4. Ejecutar migraciones
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/migrate.php"

# 5. Verificar instalación
curl http://localhost:8081/health
```

### **Comandos Útiles**

```bash
# Entrar al contenedor
docker-compose exec app bash

# Ver logs en tiempo real
docker-compose logs -f app

# Reiniciar contenedores
docker-compose restart

# Reconstruir imagen
docker-compose up -d --build

# Limpiar contenedores
docker-compose down --volumes
```

## 📁 Estructura del Proyecto

```
slim-seed-project/
├── src/                          # Código fuente
│   ├── Domain/                   # Capa de Dominio
│   │   ├── Entities/             # Entidades de negocio
│   │   ├── ValueObjects/         # Objetos de valor
│   │   ├── Services/             # Puertos (Interfaces)
│   │   └── Repositories/         # Puertos (Interfaces)
│   ├── Application/              # Capa de Aplicación
│   │   ├── UseCases/             # Casos de uso
│   │   ├── DTOs/                 # Data Transfer Objects
│   │   └── Services/             # Servicios de aplicación
│   ├── Infrastructure/           # Capa de Infraestructura
│   │   ├── Services/             # Adaptadores de servicios
│   │   ├── External/             # Servicios externos
│   │   ├── Persistence/          # Adaptadores de persistencia
│   │   └── Config/               # Configuraciones
│   ├── Presentation/             # Capa de Presentación
│   │   ├── Controllers/          # Controladores HTTP
│   │   ├── Middleware/           # Middleware de Slim
│   │   └── Routes/               # Definición de rutas
│   └── Shared/                   # Capa Compartida
│       ├── Container/            # Configuración DI
│       └── Exceptions/           # Excepciones compartidas
├── public/                       # Punto de entrada web
├── tests/                        # Tests del proyecto
├── docs/                         # Documentación
├── scripts/                      # Scripts de utilidad
├── docker/                       # Configuración Docker
├── composer.json                 # Dependencias PHP
├── docker-compose.yml           # Orquestación de contenedores
└── Dockerfile                   # Imagen de la aplicación
```

## 🔄 Flujo de Desarrollo

### **1. Crear Nueva Funcionalidad**

#### **Paso 1: Definir el Puerto (Interface)**
```php
// src/Domain/Services/ProductServiceInterface.php
interface ProductServiceInterface
{
    public function createProduct(string $name, float $price): Product;
    public function getProductById(int $id): ?Product;
    public function getAllProducts(): array;
}
```

#### **Paso 2: Crear la Entidad de Dominio**
```php
// src/Domain/Entities/Product.php
#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $price;

    // ... getters, setters, etc.
}
```

#### **Paso 3: Crear el Repositorio**
```php
// src/Domain/Repositories/ProductRepositoryInterface.php
interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function findById(int $id): ?Product;
    public function findAll(): array;
}
```

#### **Paso 4: Implementar el Adaptador**
```php
// src/Infrastructure/Persistence/DoctrineProductRepository.php
class DoctrineProductRepository implements ProductRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    // ... otros métodos
}
```

#### **Paso 5: Crear el Caso de Uso**
```php
// src/Application/UseCases/ProductUseCase.php
class ProductUseCase
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProduct(string $name, float $price): Product
    {
        $product = new Product($name, $price);
        $this->productRepository->save($product);
        return $product;
    }

    // ... otros métodos
}
```

#### **Paso 6: Crear el Controlador**
```php
// src/Presentation/Controllers/ProductController.php
class ProductController
{
    private ProductUseCase $productUseCase;

    public function __construct(ProductUseCase $productUseCase)
    {
        $this->productUseCase = $productUseCase;
    }

    public function create(Request $request, Response $response): Response
    {
        $body = json_decode($request->getBody()->getContents(), true);
        
        try {
            $product = $this->productUseCase->createProduct(
                $body['name'] ?? '',
                $body['price'] ?? 0.0
            );

            $data = [
                'success' => true,
                'message' => 'Product created successfully',
                'product' => $product->toArray()
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
}
```

#### **Paso 7: Registrar en el Container**
```php
// src/Shared/Container/ContainerBuilder.php
$containerBuilder->addDefinitions([
    // ... otras definiciones
    
    ProductRepositoryInterface::class => \DI\create(DoctrineProductRepository::class)
        ->constructor(\DI\get(EntityManagerInterface::class)),
    
    ProductUseCase::class => \DI\create()
        ->constructor(\DI\get(ProductRepositoryInterface::class)),
    
    ProductController::class => \DI\create()
        ->constructor(\DI\get(ProductUseCase::class)),
]);
```

#### **Paso 8: Agregar Rutas**
```php
// src/Presentation/Routes/ApiRoutes.php
public static function register(App $app): void
{
    // ... otras rutas
    
    // Product routes
    $app->post('/api/products', ProductController::class . ':create');
    $app->get('/api/products', ProductController::class . ':getAll');
    $app->get('/api/products/{id}', ProductController::class . ':getById');
}
```

#### **Paso 9: Ejecutar Migraciones**
```bash
# Crear nueva migración
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/migrate.php"
```

### **2. Testing**

#### **Test Unitario de Use Case**
```php
// tests/Unit/Application/UseCases/ProductUseCaseTest.php
class ProductUseCaseTest extends TestCase
{
    public function testCreateProduct()
    {
        // Mock del repositorio
        $mockRepository = $this->createMock(ProductRepositoryInterface::class);
        $mockRepository->expects($this->once())
            ->method('save');

        // Crear Use Case
        $productUseCase = new ProductUseCase($mockRepository);

        // Ejecutar test
        $product = $productUseCase->createProduct('Test Product', 99.99);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals(99.99, $product->getPrice());
    }
}
```

#### **Test de Integración**
```php
// tests/Integration/ProductControllerTest.php
class ProductControllerTest extends TestCase
{
    public function testCreateProductEndpoint()
    {
        $client = new \GuzzleHttp\Client();
        
        $response = $client->post('http://localhost:8081/api/products', [
            'json' => [
                'name' => 'Test Product',
                'price' => 99.99
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Test Product', $data['product']['name']);
    }
}
```

## 📝 Convenciones de Código

### **Naming Conventions**

#### **Clases**
- **Entidades**: `User`, `Product`, `Order`
- **Value Objects**: `Email`, `Money`, `Address`
- **Interfaces**: `UserRepositoryInterface`, `EmailServiceInterface`
- **Implementaciones**: `DoctrineUserRepository`, `SmtpEmailService`
- **Use Cases**: `CreateUserUseCase`, `ProcessOrderUseCase`
- **Controllers**: `UserController`, `ProductController`

#### **Métodos**
- **Getters**: `getName()`, `getEmail()`, `isActive()`
- **Setters**: `setName()`, `setEmail()`, `activate()`
- **Acciones**: `create()`, `update()`, `delete()`, `find()`
- **Verificaciones**: `isValid()`, `canBeDeleted()`, `hasPermission()`

#### **Variables**
- **Propiedades**: `$name`, `$email`, `$isActive`
- **Parámetros**: `$userId`, `$productName`, `$isActive`
- **Locales**: `$userData`, `$productList`, `$isValid`

### **Estructura de Archivos**

```
src/Domain/Entities/
├── User.php
├── Product.php
└── Order.php

src/Domain/ValueObjects/
├── Email.php
├── Money.php
└── Address.php

src/Domain/Services/
├── UserServiceInterface.php
├── EmailServiceInterface.php
└── PaymentServiceInterface.php

src/Infrastructure/Services/
├── SmtpEmailService.php
├── SlackNotificationService.php
└── StripePaymentService.php
```

### **Documentación**

#### **DocBlocks**
```php
/**
 * Servicio para gestión de usuarios
 * 
 * @package Emit\SlimSeed\Application\Services
 */
class UserService
{
    /**
     * Crea un nuevo usuario en el sistema
     * 
     * @param string $email Email del usuario
     * @param string $name Nombre del usuario
     * @param string $password Contraseña en texto plano
     * @return User Usuario creado
     * @throws InvalidArgumentException Si el email ya existe
     */
    public function createUser(string $email, string $name, string $password): User
    {
        // Implementación
    }
}
```

## 🧪 Testing

### **Tipos de Tests**

#### **1. Unit Tests**
- Testean clases individuales
- Mock de todas las dependencias
- Ejecución rápida
- Ubicación: `tests/Unit/`

#### **2. Integration Tests**
- Testean integración entre componentes
- Usan base de datos real
- Ejecución más lenta
- Ubicación: `tests/Integration/`

#### **3. Feature Tests**
- Testean funcionalidades completas
- Simulan peticiones HTTP
- Ejecución más lenta
- Ubicación: `tests/Feature/`

### **Configuración de Tests**

```php
// tests/TestCase.php
abstract class TestCase extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configuración común para tests
        $this->container = ContainerBuilder::build();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Limpieza después de cada test
    }
}
```

### **Comandos de Testing**

```bash
# Ejecutar todos los tests
docker-compose exec -T app bash -c "cd /var/www/html && vendor/bin/phpunit"

# Ejecutar tests específicos
docker-compose exec -T app bash -c "cd /var/www/html && vendor/bin/phpunit tests/Unit/"

# Ejecutar con coverage
docker-compose exec -T app bash -c "cd /var/www/html && vendor/bin/phpunit --coverage-html coverage/"
```

## 🐛 Debugging

### **Logging**

```php
// En cualquier parte del código
$logger = $this->container->get(LoggerInterface::class);
$logger->info('User created', ['user_id' => $user->getId()]);
$logger->error('Database connection failed', ['error' => $e->getMessage()]);
```

### **Debug en Desarrollo**

```bash
# Ver logs en tiempo real
docker-compose logs -f app

# Entrar al contenedor para debugging
docker-compose exec app bash

# Usar Xdebug (configurar en Dockerfile)
# Agregar breakpoints en el código
```

### **Herramientas de Debug**

```php
// Dump de variables
dump($variable);

// Dump y die
dd($variable);

// Log de arrays
error_log(print_r($array, true));
```

## 🎯 Mejores Prácticas

### **1. Principios SOLID**

#### **Single Responsibility**
```php
// ❌ Malo
class UserController
{
    public function createUser($data)
    {
        // Validar datos
        // Crear usuario
        // Enviar email
        // Log de auditoría
    }
}

// ✅ Bueno
class UserController
{
    public function createUser($data)
    {
        $user = $this->userUseCase->createUser($data);
        return $user;
    }
}
```

#### **Open/Closed**
```php
// ✅ Extensible sin modificar
interface PaymentServiceInterface
{
    public function processPayment(float $amount): bool;
}

class StripePaymentService implements PaymentServiceInterface { }
class PayPalPaymentService implements PaymentServiceInterface { }
```

### **2. Manejo de Errores**

```php
// ✅ Manejo específico de errores
try {
    $user = $this->userRepository->findByEmail($email);
    if (!$user) {
        throw new UserNotFoundException('User not found');
    }
} catch (UserNotFoundException $e) {
    $this->logger->warning('User not found', ['email' => $email]);
    throw $e;
} catch (\Exception $e) {
    $this->logger->error('Unexpected error', ['error' => $e->getMessage()]);
    throw new \RuntimeException('An unexpected error occurred');
}
```

### **3. Validación de Datos**

```php
// ✅ Validación en el dominio
class User
{
    public function __construct(string $email, string $name)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }
        
        $this->email = $email;
        $this->name = $name;
    }
}
```

### **4. Performance**

```php
// ✅ Consultas optimizadas
public function findActiveUsersWithOrders(): array
{
    return $this->entityManager
        ->createQuery('
            SELECT u, o 
            FROM User u 
            LEFT JOIN u.orders o 
            WHERE u.isActive = true
        ')
        ->getResult();
}
```

### **5. Seguridad**

```php
// ✅ Sanitización de entrada
public function createUser(array $data): User
{
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
    
    return new User($email, $name);
}
```

## 🚀 Próximos Pasos

1. **Implementar CQRS** - Separar comandos y consultas
2. **Agregar Event Sourcing** - Para auditoría completa
3. **Implementar Caching** - Con Redis
4. **Agregar Rate Limiting** - Para protección de API
5. **Implementar JWT** - Para autenticación
6. **Agregar OpenAPI** - Para documentación automática

---

**¡Sigue estas guías para mantener un código limpio, mantenible y escalable!**
