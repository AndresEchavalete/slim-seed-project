# 📦 Guía para Publicar SlimSeed Framework

Esta guía explica cómo publicar el paquete SlimSeed Framework en Packagist para que otros desarrolladores puedan instalarlo.

## 🚀 Pasos para Publicar

### 1. Preparar el Repositorio

```bash
# Asegúrate de que el código esté en GitHub
git add .
git commit -m "feat: convertir en paquete de Composer instalable"
git push origin main

# Crear tag de versión
git tag -a v1.0.0 -m "Primera versión estable"
git push origin v1.0.0
```

### 2. Configurar Packagist

1. **Crear cuenta en Packagist:**
   - Visita: https://packagist.org/register
   - Conecta tu cuenta de GitHub

2. **Agregar el paquete:**
   - Ve a: https://packagist.org/packages/submit
   - Ingresa la URL del repositorio: `https://github.com/tu-usuario/slim-seed-project`
   - Haz clic en "Check"

3. **Configurar webhook (opcional):**
   - Ve a la configuración del paquete
   - Agrega webhook de GitHub para actualizaciones automáticas

### 3. Configurar composer.json para Packagist

El archivo `composer.json` ya está configurado correctamente:

```json
{
    "name": "slimseed/framework",
    "description": "Framework Slim con DDD y arquitectura hexagonal - Instalable via Composer",
    "type": "library",
    "keywords": ["slim", "framework", "ddd", "hexagonal", "architecture", "doctrine", "php"],
    "license": "MIT",
    "authors": [
        {
            "name": "SlimSeed Team",
            "email": "team@slimseed.com"
        }
    ]
}
```

### 4. Crear Archivo de Licencia

```bash
# Crear archivo LICENSE
cat > LICENSE << 'EOF'
MIT License

Copyright (c) 2024 SlimSeed Framework

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
EOF
```

### 5. Crear CHANGELOG

```bash
# Crear archivo CHANGELOG.md
cat > CHANGELOG.md << 'EOF'
# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2024-01-01

### Added
- Arquitectura hexagonal completa
- Domain Driven Design (DDD)
- Inyección de dependencias con PHP-DI
- Repository Pattern
- Doctrine ORM integrado
- API REST con Slim Framework
- Health Check automático
- Sistema de notificaciones (Email/Slack)
- Migraciones automáticas
- Docker ready
- Instalador automático
- Plantillas de configuración
- Documentación completa

### Features
- Instalación via Composer
- Configuración automática
- Estructura de proyecto escalable
- Patrones de diseño profesionales
- Testing ready
- CI/CD ready
EOF
```

### 6. Verificar Instalación Local

```bash
# Crear proyecto de prueba
mkdir test-slimseed
cd test-slimseed
composer init

# Instalar desde repositorio local (para pruebas)
composer config repositories.slimseed vcs https://github.com/tu-usuario/slim-seed-project
composer require slimseed/framework:dev-main

# Verificar que funcione
ls -la
# Deberías ver: .env, docker-compose.yml, public/, src/, etc.
```

### 7. Publicar en Packagist

Una vez que el paquete esté en Packagist, los usuarios podrán instalarlo con:

```bash
composer require slimseed/framework
```

## 🔄 Actualizaciones Futuras

### Para publicar nuevas versiones:

```bash
# 1. Hacer cambios en el código
git add .
git commit -m "feat: nueva funcionalidad"

# 2. Crear nuevo tag
git tag -a v1.1.0 -m "Nueva versión con mejoras"
git push origin v1.1.0

# 3. Packagist detectará automáticamente el nuevo tag
# (si tienes webhook configurado)
```

### Para actualizar manualmente en Packagist:

1. Ve a tu paquete en Packagist
2. Haz clic en "Update" o "Check for updates"
3. El paquete se actualizará automáticamente

## 📋 Checklist Pre-Publicación

- [ ] Código revisado y sin errores
- [ ] Tests pasando (si los hay)
- [ ] Documentación actualizada
- [ ] README.md completo
- [ ] CHANGELOG.md creado
- [ ] LICENSE.md agregado
- [ ] composer.json configurado correctamente
- [ ] Repositorio en GitHub
- [ ] Tag de versión creado
- [ ] Prueba de instalación local exitosa

## 🎯 Promoción del Paquete

### Redes Sociales
- Twitter: Anunciar el lanzamiento
- LinkedIn: Compartir en grupos de PHP
- Reddit: r/PHP, r/webdev

### Comunidad PHP
- PHP.net
- Laravel News
- Symfony News
- Sitepoint
- Dev.to

### Documentación
- Crear ejemplos en GitHub
- Escribir tutoriales
- Crear videos de demostración

## 🚀 ¡Listo para Publicar!

Una vez completados todos los pasos, tu paquete estará disponible para que cualquier desarrollador PHP pueda instalarlo con:

```bash
composer require slimseed/framework
```

¡Felicitaciones por crear un framework reutilizable! 🎉
