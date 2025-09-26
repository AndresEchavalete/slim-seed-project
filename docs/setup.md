# 🛠️ Guía de Instalación y Configuración

Guía paso a paso para instalar y configurar el Slim Seed Project.

## 📋 Índice

- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación Rápida](#instalación-rápida)
- [Instalación Manual](#instalación-manual)
- [Configuración](#configuración)
- [Verificación](#verificación)
- [Solución de Problemas](#solución-de-problemas)

## 💻 Requisitos del Sistema

### **Mínimos**
- **Docker**: 20.10+
- **Docker Compose**: 2.0+
- **Git**: 2.30+
- **RAM**: 4GB
- **Disco**: 2GB libres

### **Recomendados**
- **Docker**: 24.0+
- **Docker Compose**: 2.20+
- **Git**: 2.40+
- **RAM**: 8GB
- **Disco**: 5GB libres

### **Sistemas Operativos Soportados**
- ✅ Linux (Ubuntu 20.04+, CentOS 8+)
- ✅ macOS (10.15+)
- ✅ Windows 10/11 (con WSL2)

## 🚀 Instalación Rápida

### **Paso 1: Clonar el Repositorio**

```bash
git clone <repository-url>
cd slim-seed-project
```

### **Paso 2: Levantar Contenedores**

```bash
docker-compose up -d
```

### **Paso 3: Instalar Dependencias**

```bash
docker-compose exec -T app bash -c "cd /var/www/html && composer install"
```

### **Paso 4: Ejecutar Migraciones**

```bash
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/migrate.php"
```

### **Paso 5: Verificar Instalación**

```bash
curl http://localhost:8081/health
```

**¡Listo!** La aplicación estará disponible en `http://localhost:8081`

## 🔧 Instalación Manual

### **Paso 1: Preparar el Entorno**

#### **Instalar Docker**

**Ubuntu/Debian:**
```bash
# Actualizar paquetes
sudo apt update

# Instalar dependencias
sudo apt install apt-transport-https ca-certificates curl gnupg lsb-release

# Agregar clave GPG de Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Agregar repositorio
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Instalar Docker
sudo apt update
sudo apt install docker-ce docker-ce-cli containerd.io

# Agregar usuario al grupo docker
sudo usermod -aG docker $USER
```

**macOS:**
```bash
# Instalar con Homebrew
brew install --cask docker

# O descargar Docker Desktop desde:
# https://www.docker.com/products/docker-desktop
```

**Windows:**
1. Descargar Docker Desktop desde: https://www.docker.com/products/docker-desktop
2. Instalar y reiniciar
3. Habilitar WSL2 si es necesario

#### **Instalar Docker Compose**

```bash
# Descargar última versión
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

# Dar permisos de ejecución
sudo chmod +x /usr/local/bin/docker-compose

# Verificar instalación
docker-compose --version
```

### **Paso 2: Clonar y Configurar**

```bash
# Clonar repositorio
git clone <repository-url>
cd slim-seed-project

# Crear archivo de configuración
cp .env.example .env

# Editar configuración (opcional)
nano .env
```

### **Paso 3: Construir y Ejecutar**

```bash
# Construir imágenes
docker-compose build

# Levantar contenedores
docker-compose up -d

# Verificar estado
docker-compose ps
```

### **Paso 4: Configurar Base de Datos**

```bash
# Esperar a que MySQL esté listo
sleep 30

# Ejecutar migraciones
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/migrate.php"
```

## ⚙️ Configuración

### **Variables de Entorno**

Edita el archivo `.env`:

```env
# ===========================================
# CONFIGURACIÓN DE LA APLICACIÓN
# ===========================================
APP_ENV=development
APP_DEBUG=true
APP_NAME="Slim Seed Project"
APP_VERSION=1.0.0

# ===========================================
# CONFIGURACIÓN DE BASE DE DATOS
# ===========================================
DB_HOST=mysql
DB_PORT=3306
DB_NAME=slim_seed
DB_USER=slim_user
DB_PASS=slim_pass

# ===========================================
# CONFIGURACIÓN DE REDIS
# ===========================================
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=

# ===========================================
# CONFIGURACIÓN DE NOTIFICACIONES
# ===========================================
NOTIFICATION_TYPE=email  # email | slack
ADMIN_EMAIL=admin@example.com
SLACK_WEBHOOK=https://hooks.slack.com/services/...

# ===========================================
# CONFIGURACIÓN DE LOGGING
# ===========================================
LOG_LEVEL=debug
LOG_FILE=logs/app.log
```

### **Puertos del Sistema**

| Servicio | Puerto Host | Puerto Contenedor | URL |
|----------|-------------|-------------------|-----|
| **API** | 8081 | 80 | http://localhost:8081 |
| **MySQL** | 3307 | 3306 | localhost:3307 |
| **Redis** | 6380 | 6379 | localhost:6380 |

### **Configuración de Producción**

Para producción, modifica `docker-compose.yml`:

```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "80:80"  # Puerto estándar HTTP
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    restart: unless-stopped
    depends_on:
      - redis
      - mysql

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: ${DB_NAME}
      MYSQL_USER: ${DB_USER}
      MYSQL_PASSWORD: ${DB_PASS}
    ports:
      - "3306:3306"  # Puerto estándar MySQL
    volumes:
      - mysql_data:/var/lib/mysql
    restart: unless-stopped

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"  # Puerto estándar Redis
    restart: unless-stopped
```

## ✅ Verificación

### **Verificar Contenedores**

```bash
# Estado de contenedores
docker-compose ps

# Debería mostrar algo como:
# NAME                        IMAGE                    COMMAND                  SERVICE   CREATED        STATUS                    PORTS
# slim-seed-project_app_1     slim-seed-project_app    "docker-php-entrypoint"  app       2 minutes ago   Up 2 minutes (healthy)   0.0.0.0:8081->80/tcp
# slim-seed-project_mysql_1   mysql:8.0               "docker-entrypoint.s…"   mysql     2 minutes ago   Up 2 minutes (healthy)   0.0.0.0:3307->3306/tcp
# slim-seed-project_redis_1   redis:7-alpine          "docker-entrypoint.s…"   redis     2 minutes ago   Up 2 minutes (healthy)   0.0.0.0:6380->6379/tcp
```

### **Verificar Aplicación**

```bash
# Health check
curl http://localhost:8081/health

# Respuesta esperada:
# {"healthy":true,"message":"All systems operational","checks":{"database":true,"redis":true,"memory":true}}

# Bienvenida
curl http://localhost:8081/

# Respuesta esperada:
# {"message":"¡Bienvenido a Slim Seed Project!","version":"1.0.0","architecture":"DDD + Hexagonal","framework":"Slim 4 + DI Container","timestamp":"2025-09-26 13:39:39"}
```

### **Verificar Base de Datos**

```bash
# Conectar a MySQL
docker-compose exec mysql mysql -u slim_user -p slim_seed

# Verificar tablas
SHOW TABLES;

# Debería mostrar:
# +----------------------------+
# | Tables_in_slim_seed        |
# +----------------------------+
# | doctrine_migration_versions|
# | health_status              |
# | users                      |
# +----------------------------+
```

### **Verificar Redis**

```bash
# Conectar a Redis
docker-compose exec redis redis-cli

# Probar comando
PING

# Respuesta esperada:
# PONG
```

## 🔧 Comandos Útiles

### **Gestión de Contenedores**

```bash
# Levantar contenedores
docker-compose up -d

# Parar contenedores
docker-compose down

# Reiniciar contenedores
docker-compose restart

# Ver logs
docker-compose logs -f app

# Entrar al contenedor
docker-compose exec app bash
```

### **Gestión de Base de Datos**

```bash
# Ejecutar migraciones
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/migrate.php"

# Resetear base de datos
docker-compose exec -T app bash -c "cd /var/www/html && php scripts/reset-db.php"

# Backup de base de datos
docker-compose exec mysql mysqldump -u slim_user -p slim_seed > backup.sql

# Restaurar backup
docker-compose exec -T mysql mysql -u slim_user -p slim_seed < backup.sql
```

### **Desarrollo**

```bash
# Instalar dependencias
docker-compose exec -T app bash -c "cd /var/www/html && composer install"

# Actualizar dependencias
docker-compose exec -T app bash -c "cd /var/www/html && composer update"

# Ejecutar tests
docker-compose exec -T app bash -c "cd /var/www/html && vendor/bin/phpunit"

# Verificar sintaxis
docker-compose exec -T app bash -c "cd /var/www/html && php -l src/**/*.php"
```

## 🐛 Solución de Problemas

### **Problema: Puerto ya en uso**

```bash
# Error: Port 8081 is already allocated
# Solución: Cambiar puerto en docker-compose.yml

# O matar proceso que usa el puerto
sudo lsof -ti:8081 | xargs kill -9
```

### **Problema: Contenedor no inicia**

```bash
# Ver logs detallados
docker-compose logs app

# Reconstruir imagen
docker-compose build --no-cache app

# Limpiar todo y empezar de nuevo
docker-compose down --volumes
docker system prune -f
docker-compose up -d --build
```

### **Problema: Base de datos no conecta**

```bash
# Verificar que MySQL esté listo
docker-compose exec mysql mysqladmin ping -h localhost -u slim_user -p

# Verificar variables de entorno
docker-compose exec app env | grep DB_

# Reiniciar solo MySQL
docker-compose restart mysql
```

### **Problema: Permisos de archivos**

```bash
# Dar permisos correctos
sudo chown -R $USER:$USER .
chmod -R 755 .

# En el contenedor
docker-compose exec app chown -R www-data:www-data /var/www/html
```

### **Problema: Memoria insuficiente**

```bash
# Aumentar memoria para Docker
# En Docker Desktop: Settings > Resources > Memory

# O reducir uso de memoria
# En docker-compose.yml, agregar:
services:
  app:
    deploy:
      resources:
        limits:
          memory: 512M
```

## 📞 Soporte

### **Logs Importantes**

```bash
# Logs de aplicación
docker-compose logs app

# Logs de base de datos
docker-compose logs mysql

# Logs de Redis
docker-compose logs redis

# Logs de todos los servicios
docker-compose logs
```

### **Información del Sistema**

```bash
# Versión de Docker
docker --version
docker-compose --version

# Información del sistema
docker system info

# Uso de recursos
docker stats
```

### **Contacto**

Si encuentras problemas no cubiertos en esta guía:

1. Revisa los [Issues del repositorio](https://github.com/your-repo/issues)
2. Consulta la [documentación de Docker](https://docs.docker.com/)
3. Contacta al equipo de desarrollo

---

**¡Con esta guía deberías tener el proyecto funcionando sin problemas!** 🚀
