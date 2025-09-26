# 🤝 Guía de Contribución

¡Gracias por tu interés en contribuir al Slim Seed Project! Esta guía te ayudará a contribuir de manera efectiva.

## 📋 Índice

- [Código de Conducta](#código-de-conducta)
- [Cómo Contribuir](#cómo-contribuir)
- [Proceso de Desarrollo](#proceso-de-desarrollo)
- [Estándares de Código](#estándares-de-código)
- [Testing](#testing)
- [Documentación](#documentación)
- [Pull Requests](#pull-requests)

## 🤝 Código de Conducta

### **Nuestros Compromisos**

- **Inclusivo**: Respetamos a todos los contribuidores
- **Colaborativo**: Trabajamos juntos hacia un objetivo común
- **Profesional**: Mantenemos un ambiente de trabajo positivo
- **Constructivo**: Proporcionamos feedback útil y constructivo

### **Comportamiento Esperado**

- Usar lenguaje inclusivo y respetuoso
- Respetar diferentes puntos de vista y experiencias
- Aceptar críticas constructivas con gracia
- Enfocarse en lo que es mejor para la comunidad
- Mostrar empatía hacia otros miembros de la comunidad

## 🚀 Cómo Contribuir

### **Tipos de Contribuciones**

#### **🐛 Reportar Bugs**
- Usa el template de bug report
- Incluye pasos para reproducir el problema
- Especifica versión del sistema y dependencias
- Adjunta logs y capturas de pantalla si es relevante

#### **✨ Sugerir Mejoras**
- Usa el template de feature request
- Describe el problema que resuelve
- Explica la solución propuesta
- Considera alternativas y limitaciones

#### **📝 Mejorar Documentación**
- Corrige errores tipográficos
- Mejora claridad de explicaciones
- Agrega ejemplos útiles
- Traduce documentación a otros idiomas

#### **💻 Contribuir Código**
- Implementa nuevas funcionalidades
- Corrige bugs existentes
- Optimiza rendimiento
- Mejora tests y cobertura

## 🔄 Proceso de Desarrollo

### **1. Fork y Clone**

```bash
# Fork el repositorio en GitHub
# Luego clona tu fork
git clone https://github.com/tu-usuario/slim-seed-project.git
cd slim-seed-project

# Agregar upstream
git remote add upstream https://github.com/original-repo/slim-seed-project.git
```

### **2. Crear Branch**

```bash
# Crear branch para tu feature
git checkout -b feature/nueva-funcionalidad

# O para bug fix
git checkout -b fix/corregir-bug
```

### **3. Configurar Entorno**

```bash
# Levantar contenedores
docker-compose up -d

# Instalar dependencias
docker-compose exec -T app bash -c "cd /var/www/html && composer install"

# Ejecutar migraciones
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/migrate.php"
```

### **4. Desarrollo**

```bash
# Hacer cambios en el código
# Ejecutar tests
docker-compose exec -T app bash -c "cd /var/www/html && vendor/bin/phpunit"

# Verificar sintaxis
docker-compose exec -T app bash -c "cd /var/www/html && php -l src/**/*.php"
```

### **5. Commit y Push**

```bash
# Agregar cambios
git add .

# Commit con mensaje descriptivo
git commit -m "feat: agregar nueva funcionalidad de usuarios"

# Push al fork
git push origin feature/nueva-funcionalidad
```

## 📝 Estándares de Código

### **Convenciones de Naming**

#### **Clases**
```php
// ✅ Correcto
class UserController
class EmailNotificationService
class DoctrineUserRepository

// ❌ Incorrecto
class userController
class email_notification_service
class doctrine_user_repository
```

#### **Métodos**
```php
// ✅ Correcto
public function createUser(): User
public function isActive(): bool
public function getEmail(): string

// ❌ Incorrecto
public function create_user(): User
public function IsActive(): bool
public function getemail(): string
```

#### **Variables**
```php
// ✅ Correcto
$userName = 'John Doe';
$isActive = true;
$userRepository = $this->container->get(UserRepositoryInterface::class);

// ❌ Incorrecto
$user_name = 'John Doe';
$IsActive = true;
$userRepo = $this->container->get(UserRepositoryInterface::class);
```

### **Estructura de Archivos**

```
src/
├── Domain/
│   ├── Entities/
│   │   ├── User.php
│   │   └── Product.php
│   ├── ValueObjects/
│   │   ├── Email.php
│   │   └── Money.php
│   ├── Services/
│   │   ├── UserServiceInterface.php
│   │   └── EmailServiceInterface.php
│   └── Repositories/
│       ├── UserRepositoryInterface.php
│       └── ProductRepositoryInterface.php
├── Application/
│   ├── UseCases/
│   │   ├── CreateUserUseCase.php
│   │   └── UpdateUserUseCase.php
│   └── DTOs/
│       ├── UserDTO.php
│       └── ProductDTO.php
└── Infrastructure/
    ├── Services/
    │   ├── SmtpEmailService.php
    │   └── SlackNotificationService.php
    └── Persistence/
        ├── DoctrineUserRepository.php
        └── DoctrineProductRepository.php
```

### **Documentación de Código**

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
     * @throws ValidationException Si los datos no son válidos
     */
    public function createUser(string $email, string $name, string $password): User
    {
        // Implementación
    }
}
```

### **Manejo de Errores**

```php
// ✅ Correcto
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

// ❌ Incorrecto
try {
    $user = $this->userRepository->findByEmail($email);
} catch (\Exception $e) {
    // Error genérico sin logging
    throw new \Exception('Error');
}
```

## 🧪 Testing

### **Escribir Tests**

```php
// tests/Unit/Application/UseCases/UserUseCaseTest.php
class UserUseCaseTest extends TestCase
{
    public function testCreateUserWithValidData()
    {
        // Arrange
        $mockRepository = $this->createMock(UserRepositoryInterface::class);
        $mockRepository->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);
        $mockRepository->expects($this->once())
            ->method('save');

        $userUseCase = new UserUseCase($mockRepository);

        // Act
        $user = $userUseCase->createUser('test@example.com', 'Test User', 'password123');

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('Test User', $user->getName());
    }

    public function testCreateUserWithExistingEmail()
    {
        // Arrange
        $existingUser = new User('test@example.com', 'Existing User', 'password123');
        $mockRepository = $this->createMock(UserRepositoryInterface::class);
        $mockRepository->expects($this->once())
            ->method('findByEmail')
            ->willReturn($existingUser);

        $userUseCase = new UserUseCase($mockRepository);

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User with this email already exists');
        
        $userUseCase->createUser('test@example.com', 'Test User', 'password123');
    }
}
```

### **Ejecutar Tests**

```bash
# Todos los tests
docker-compose exec -T app bash -c "cd /var/www/html && vendor/bin/phpunit"

# Tests específicos
docker-compose exec -T app bash -c "cd /var/www/html && vendor/bin/phpunit tests/Unit/"

# Con coverage
docker-compose exec -T app bash -c "cd /var/www/html && vendor/bin/phpunit --coverage-html coverage/"
```

### **Cobertura de Tests**

- **Mínimo**: 80% de cobertura de código
- **Objetivo**: 90% de cobertura de código
- **Crítico**: 100% para código de dominio

## 📚 Documentación

### **Actualizar README**

```markdown
# Agregar nueva funcionalidad
## 🆕 Nueva Funcionalidad

### Descripción
Breve descripción de la funcionalidad.

### Uso
```bash
# Ejemplo de uso
curl -X POST http://localhost:8081/api/nueva-funcionalidad
```

### API
- `POST /api/nueva-funcionalidad` - Crear nueva funcionalidad
```

### **Actualizar API.md**

```markdown
#### **Nueva Funcionalidad**
```http
POST /api/nueva-funcionalidad
Content-Type: application/json

{
  "param1": "value1",
  "param2": "value2"
}
```

**Respuesta (201):**
```json
{
  "success": true,
  "message": "Nueva funcionalidad creada",
  "data": {
    "id": 1,
    "param1": "value1",
    "param2": "value2"
  }
}
```
```

### **Actualizar CHANGELOG.md**

```markdown
## [1.1.0] - 2025-09-27

### ✨ Added
- Nueva funcionalidad de gestión de productos
- Endpoint para crear productos
- Validación de datos de entrada

### 🐛 Fixed
- Corregido bug en autenticación de usuarios
- Mejorado manejo de errores en API

### 🔧 Changed
- Actualizada documentación de API
- Mejorado rendimiento de consultas
```

## 🔀 Pull Requests

### **Crear Pull Request**

1. **Título descriptivo**:
   ```
   feat: agregar gestión de productos
   fix: corregir bug en autenticación
   docs: actualizar documentación de API
   ```

2. **Descripción detallada**:
   ```markdown
   ## Descripción
   Breve descripción de los cambios realizados.

   ## Tipo de Cambio
   - [ ] Bug fix
   - [ ] Nueva funcionalidad
   - [ ] Breaking change
   - [ ] Documentación

   ## Cambios Realizados
   - Agregado endpoint POST /api/products
   - Implementado ProductController
   - Agregado ProductUseCase
   - Actualizada documentación

   ## Testing
   - [ ] Tests unitarios agregados
   - [ ] Tests de integración agregados
   - [ ] Todos los tests pasan

   ## Checklist
   - [ ] Código sigue estándares del proyecto
   - [ ] Documentación actualizada
   - [ ] CHANGELOG.md actualizado
   - [ ] No hay conflictos de merge
   ```

### **Revisión de Código**

#### **Como Autor**:
- Responde a comentarios de revisión
- Haz cambios solicitados
- Mantén el PR actualizado
- Sé paciente con el proceso

#### **Como Revisor**:
- Revisa el código cuidadosamente
- Proporciona feedback constructivo
- Sugiere mejoras específicas
- Aproba cuando esté listo

### **Criterios de Aceptación**

- [ ] Código sigue estándares del proyecto
- [ ] Tests pasan y cobertura es adecuada
- [ ] Documentación actualizada
- [ ] No hay conflictos de merge
- [ ] Funcionalidad probada manualmente
- [ ] Performance aceptable
- [ ] Seguridad considerada

## 🏷️ Etiquetas de Issues

### **Tipos**
- `bug` - Algo no funciona
- `enhancement` - Nueva funcionalidad
- `documentation` - Mejoras en documentación
- `question` - Pregunta o duda
- `help wanted` - Se necesita ayuda

### **Prioridades**
- `priority: high` - Crítico
- `priority: medium` - Importante
- `priority: low` - Opcional

### **Estados**
- `status: needs-triage` - Necesita revisión
- `status: in-progress` - En desarrollo
- `status: needs-review` - Necesita revisión
- `status: needs-testing` - Necesita testing

## 📞 Comunicación

### **Canal de Comunicación**
- **Issues**: Para bugs y feature requests
- **Discussions**: Para preguntas y debates
- **Pull Requests**: Para revisión de código

### **Respuesta a Issues**
- **Tiempo objetivo**: 48 horas
- **Tiempo máximo**: 1 semana
- **Escalación**: Contactar maintainers

## 🎯 Roadmap

### **Versión 1.1.0**
- [ ] Autenticación JWT
- [ ] Validación de entrada
- [ ] Cache con Redis
- [ ] Rate Limiting

### **Versión 1.2.0**
- [ ] Event Sourcing
- [ ] CQRS Pattern
- [ ] Domain Events
- [ ] Microservices support

## 🙏 Reconocimientos

### **Contribuidores**
- [Lista de contribuidores](https://github.com/your-repo/graphs/contributors)

### **Agradecimientos**
- Comunidad de Slim Framework
- Comunidad de Doctrine ORM
- Todos los contribuidores

---

**¡Gracias por contribuir al Slim Seed Project!** 🚀

**Juntos construimos software mejor.** 💪
