# 🚀 Ejemplo de Uso - SlimSeed Framework

Este ejemplo te muestra cómo crear un nuevo proyecto usando SlimSeed Framework como paquete de Composer.

## 📋 Requisitos Previos

- PHP 8.2+
- Composer
- Docker y Docker Compose
- Git

## 🎯 Crear Nuevo Proyecto

### Paso 1: Inicializar Proyecto

```bash
# Crear directorio del proyecto
mkdir mi-api-slimseed
cd mi-api-slimseed

# Inicializar Composer
composer init
# Responde las preguntas básicas del proyecto
```

### Paso 2: Instalar SlimSeed Framework

```bash
# Agregar SlimSeed Framework
composer require slimseed/framework

# El instalador se ejecuta automáticamente y configura todo
```

### Paso 3: Verificar Instalación

```bash
# Ver estructura creada
ls -la

# Deberías ver:
# .env, docker-compose.yml, public/, src/, migrations/, etc.
```

### Paso 4: Configurar Variables

```bash
# Editar .env con tus configuraciones
nano .env
```

```env
# Aplicación
APP_ENV=development
APP_DEBUG=true
APP_NAME="Mi API con SlimSeed"

# Base de datos
DB_HOST=mysql
DB_PORT=3306
DB_NAME=mi_api
DB_USER=api_user
DB_PASS=api_password

# Notificaciones
NOTIFICATION_TYPE=email
ADMIN_EMAIL=admin@mi-api.com
```

### Paso 5: Levantar Servicios

```bash
# Levantar contenedores
docker-compose up -d

# Verificar que estén corriendo
docker-compose ps
```

### Paso 6: Configurar Base de Datos

```bash
# Ejecutar migraciones
composer run migrate

# Verificar estado
composer run migrate:status
```

### Paso 7: Probar la API

```bash
# Probar endpoint principal
curl http://localhost:8081/

# Respuesta esperada:
# {
#   "message": "¡Bienvenido a SlimSeed Framework!",
#   "version": "1.0.0",
#   "architecture": "DDD + Hexagonal",
#   "framework": "Slim 4 + DI Container",
#   "timestamp": "2025-09-26 14:00:00"
# }

# Probar health check
curl http://localhost:8081/health

# Respuesta esperada:
# {
#   "healthy": true,
#   "message": "All systems operational",
#   "checks": {
#     "database": true,
#     "redis": true,
#     "memory": true
#   }
# }
```

## 🏗️ Agregar Funcionalidad Personalizada

### Ejemplo: Crear Entidad "Producto"

#### 1. Crear Entidad de Dominio

```php
// src/Domain/Entities/Product.php
<?php

namespace SlimSeed\Domain\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isActive = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters y Setters...
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
}
```

#### 2. Crear Repositorio

```php
// src/Domain/Repositories/ProductRepositoryInterface.php
<?php

namespace SlimSeed\Domain\Repositories;

use SlimSeed\Domain\Entities\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function findById(int $id): ?Product;
    public function findAll(): array;
    public function findByActive(bool $active = true): array;
}
```

#### 3. Implementar Repositorio

```php
// src/Infrastructure/Persistence/DoctrineProductRepository.php
<?php

namespace SlimSeed\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use SlimSeed\Domain\Entities\Product;
use SlimSeed\Domain\Repositories\ProductRepositoryInterface;

class DoctrineProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?Product
    {
        return $this->entityManager->find(Product::class, $id);
    }

    public function findAll(): array
    {
        return $this->entityManager
            ->getRepository(Product::class)
            ->findAll();
    }

    public function findByActive(bool $active = true): array
    {
        return $this->entityManager
            ->getRepository(Product::class)
            ->findBy(['isActive' => $active]);
    }
}
```

#### 4. Crear Caso de Uso

```php
// src/Application/UseCases/ProductUseCase.php
<?php

namespace SlimSeed\Application\UseCases;

use SlimSeed\Domain\Entities\Product;
use SlimSeed\Domain\Repositories\ProductRepositoryInterface;

class ProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function createProduct(string $name, float $price, ?string $description = null): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);

        $this->productRepository->save($product);

        return $product;
    }

    public function getProduct(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function getActiveProducts(): array
    {
        return $this->productRepository->findByActive(true);
    }
}
```

#### 5. Crear Controlador

```php
// src/Presentation/Controllers/ProductController.php
<?php

namespace SlimSeed\Presentation\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimSeed\Application\UseCases\ProductUseCase;

class ProductController
{
    public function __construct(
        private ProductUseCase $productUseCase
    ) {}

    public function create(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $product = $this->productUseCase->createProduct(
            $data['name'],
            $data['price'],
            $data['description'] ?? null
        );

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'isActive' => $product->isActive()
            ]
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getById(Request $request, Response $response, array $args): Response
    {
        $product = $this->productUseCase->getProduct((int)$args['id']);

        if (!$product) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'isActive' => $product->isActive()
            ]
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAll(Request $request, Response $response): Response
    {
        $products = $this->productUseCase->getAllProducts();

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => array_map(function($product) {
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'description' => $product->getDescription(),
                    'isActive' => $product->isActive()
                ];
            }, $products)
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
```

#### 6. Registrar en el Contenedor DI

```php
// src/Shared/Container/ContainerBuilder.php
// Agregar estas líneas:

use SlimSeed\Domain\Repositories\ProductRepositoryInterface;
use SlimSeed\Infrastructure\Persistence\DoctrineProductRepository;
use SlimSeed\Application\UseCases\ProductUseCase;
use SlimSeed\Presentation\Controllers\ProductController;

// En el array de definiciones:
ProductRepositoryInterface::class => \DI\create(DoctrineProductRepository::class),
ProductUseCase::class => \DI\create(),
ProductController::class => \DI\create(),
```

#### 7. Agregar Rutas

```php
// src/Presentation/Routes/ApiRoutes.php
// Agregar estas rutas:

use SlimSeed\Presentation\Controllers\ProductController;

// En el método register():
$app->group('/api/products', function (Group $group) {
    $group->post('', [ProductController::class, 'create']);
    $group->get('', [ProductController::class, 'getAll']);
    $group->get('/{id}', [ProductController::class, 'getById']);
});
```

#### 8. Crear Migración

```bash
# Generar migración
composer run migrate:generate

# Editar el archivo generado en migrations/
```

```php
// migrations/Version20240101000001.php
public function up(Schema $schema): void
{
    $table = $schema->createTable('products');
    $table->addColumn('id', 'integer', ['autoincrement' => true]);
    $table->addColumn('name', 'string', ['length' => 255]);
    $table->addColumn('description', 'text', ['notnull' => false]);
    $table->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2]);
    $table->addColumn('is_active', 'boolean', ['default' => true]);
    $table->addColumn('created_at', 'datetime');
    $table->setPrimaryKey(['id']);
}
```

#### 9. Ejecutar Migración

```bash
# Ejecutar migración
composer run migrate
```

#### 10. Probar la API

```bash
# Crear producto
curl -X POST http://localhost:8081/api/products \
  -H "Content-Type: application/json" \
  -d '{"name":"Laptop","price":999.99,"description":"Laptop gaming"}'

# Obtener todos los productos
curl http://localhost:8081/api/products

# Obtener producto por ID
curl http://localhost:8081/api/products/1
```

## 🎉 ¡Listo!

Ahora tienes un proyecto completo con SlimSeed Framework que incluye:

- ✅ Arquitectura Hexagonal
- ✅ Domain Driven Design
- ✅ Inyección de Dependencias
- ✅ Repository Pattern
- ✅ Doctrine ORM
- ✅ API REST
- ✅ Docker
- ✅ Migraciones
- ✅ Health Check

## 📚 Próximos Pasos

1. **Agregar Validación**: Usar Respect/Validation
2. **Implementar Autenticación**: JWT o Session
3. **Agregar Tests**: PHPUnit
4. **Configurar CI/CD**: GitHub Actions
5. **Documentar API**: OpenAPI/Swagger

---

**¡Disfruta desarrollando con SlimSeed Framework!** 🚀
